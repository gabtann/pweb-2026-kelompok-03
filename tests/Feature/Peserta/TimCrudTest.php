<?php

use App\Models\Event;
use App\Models\Tim;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    Storage::fake('local');
    // Halaman Vue Peserta/* dibuat Hanz. Hapus baris ini setelah file-nya ada.
    config(['inertia.testing.ensure_pages_exist' => false]);
});

function pesertaTim(): User
{
    return User::factory()->create();
}

/** Event dengan registrasi sedang dibuka; total anggota (termasuk ketua) 2 sampai 3. */
function eventBuka(array $override = []): Event
{
    return Event::factory()->create(array_merge([
        'registrasi_mulai' => now()->subDay(),
        'registrasi_selesai' => now()->addDays(5),
        'waktu_mulai' => now()->addDays(10),
        'waktu_selesai' => now()->addDays(20),
        'biaya_pendaftaran' => null,
        'min_anggota' => 2,
        'max_anggota' => 3,
    ], $override));
}

function pdfPalsu(string $nama): UploadedFile
{
    return UploadedFile::fake()->create($nama, 100, 'application/pdf');
}

function dataDaftar(array $override = []): array
{
    return array_merge([
        'nama_tim' => 'Tim Garuda',
        'asal_perguruan_tinggi' => 'Universitas Hasanuddin',
        'nim_ketua' => 'H071241001',
        'file_ktm' => pdfPalsu('ktm.pdf'),
        'file_pakta' => pdfPalsu('pakta.pdf'),
        'anggota' => [
            ['nama_lengkap' => 'Ani', 'nim' => 'H071241002'],
        ],
    ], $override);
}

function urlDaftar(Event $event): string
{
    return "/peserta/event/{$event->id}/daftar";
}

/** Tim yang berkasnya benar-benar ada di disk privat (untuk tes ubah, batal, dan unduh). */
function timDenganBerkas(User $ketua, Event $event, array $override = []): Tim
{
    Storage::disk('local')->put("tim/{$event->id}/ktm.pdf", 'ktm');
    Storage::disk('local')->put("tim/{$event->id}/pakta.pdf", 'pakta');

    return Tim::factory()->create(array_merge([
        'event_id' => $event->id,
        'ketua_id' => $ketua->id,
        'file_ktm' => "tim/{$event->id}/ktm.pdf",
        'file_pakta' => "tim/{$event->id}/pakta.pdf",
    ], $override));
}

// ---------- Akses ----------

it('panitia mendapat 403 dan guest di-redirect dari route peserta', function () {
    $event = eventBuka();
    $tim = timDenganBerkas(pesertaTim(), $event);

    foreach ([
        ['get', '/peserta/event'],
        ['get', urlDaftar($event)],
        ['post', urlDaftar($event)],
        ['get', '/peserta/tim'],
        ['get', "/peserta/tim/{$tim->id}/edit"],
        ['put', "/peserta/tim/{$tim->id}"],
        ['delete', "/peserta/tim/{$tim->id}"],
    ] as [$method, $url]) {
        $this->actingAs(User::factory()->panitia()->create())->{$method}($url)->assertForbidden();
        auth()->logout();
        $this->{$method}($url)->assertRedirect();
    }

    expect(Tim::count())->toBe(1);
});

// ---------- Daftar event ----------

it('daftar event menandai registrasi dibuka dan sudah terdaftar', function () {
    $buka = eventBuka(['jenis_lomba' => 'uiux', 'nama_event' => 'Zxq Buka']);
    eventBuka([
        'jenis_lomba' => 'uiux',
        'nama_event' => 'Zxq Tutup',
        'registrasi_mulai' => now()->subDays(5),
        'registrasi_selesai' => now()->subDay(),
    ]);
    $user = pesertaTim();
    timDenganBerkas($user, $buka);

    $this->actingAs($user)->get('/peserta/event')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Peserta/Event/Index')
            ->has('events.data', 2)
            ->where('events.data.0.nama_event', 'Zxq Buka')
            ->where('events.data.0.registrasi_dibuka', true)
            ->where('events.data.0.sudah_terdaftar', true)
            ->where('events.data.1.registrasi_dibuka', false)
            ->where('events.data.1.sudah_terdaftar', false)
        );
});

// ---------- Form daftar ----------

it('form daftar menampilkan ringkasan event dan batas anggota', function () {
    $event = eventBuka(['biaya_pendaftaran' => 50000]);

    $this->actingAs(pesertaTim())->get(urlDaftar($event))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Peserta/Tim/Create')
            ->where('event.id', $event->id)
            ->where('event.perlu_bukti_bayar', true)
            ->where('event.min_anggota_tambahan', 1)
            ->where('event.max_anggota_tambahan', 2)
        );
});

it('form daftar ditolak jika registrasi ditutup atau peserta sudah mendaftar', function () {
    $tutup = eventBuka(['registrasi_mulai' => now()->addDay(), 'registrasi_selesai' => now()->addDays(5)]);
    $user = pesertaTim();

    $this->actingAs($user)->get(urlDaftar($tutup))
        ->assertRedirect(route('peserta.event.index'))
        ->assertSessionHas('error');

    $buka = eventBuka();
    timDenganBerkas($user, $buka);

    $this->actingAs($user)->get(urlDaftar($buka))
        ->assertRedirect(route('peserta.tim.index'))
        ->assertSessionHas('error');
});

// ---------- Store ----------

it('store membuat tim, anggota, dan menyimpan berkas di disk privat', function () {
    $event = eventBuka();
    $user = pesertaTim();

    $this->actingAs($user)
        ->post(urlDaftar($event), dataDaftar([
            'anggota' => [['nama_lengkap' => 'Ani', 'nim' => 'h071241002']], // huruf kecil
        ]))
        ->assertRedirect(route('peserta.tim.index'))
        ->assertSessionHas('success');

    $tim = Tim::with('anggota')->first();

    expect($tim->event_id)->toBe($event->id)
        ->and($tim->ketua_id)->toBe($user->id)
        ->and($tim->status_verifikasi)->toBe('menunggu')
        ->and($tim->kode_tim)->not->toBeEmpty()
        ->and($tim->nim_ketua)->toBe('H071241001')
        ->and($tim->file_bukti_bayar)->toBeNull()
        ->and($tim->anggota)->toHaveCount(1)
        ->and($tim->anggota->first()->nim)->toBe('H071241002')
        ->and($tim->anggota->first()->event_id)->toBe($event->id);

    Storage::disk('local')->assertExists($tim->file_ktm);
    Storage::disk('local')->assertExists($tim->file_pakta);
});

it('store mengabaikan ketua_id dan field verifikasi dari request', function () {
    $event = eventBuka();
    $user = pesertaTim();
    $orangLain = pesertaTim();

    $this->actingAs($user)->post(urlDaftar($event), dataDaftar([
        'ketua_id' => $orangLain->id,
        'event_id' => 999,
        'status_verifikasi' => 'disetujui',
        'catatan_verifikasi' => 'Disusupkan lewat request',
        'verifikator_id' => $orangLain->id,
    ]));

    $tim = Tim::first();

    expect($tim->ketua_id)->toBe($user->id)
        ->and($tim->event_id)->toBe($event->id)
        ->and($tim->status_verifikasi)->toBe('menunggu')
        ->and($tim->catatan_verifikasi)->toBeNull()
        ->and($tim->verifikator_id)->toBeNull();
});

it('store mewajibkan bukti bayar hanya untuk event berbayar', function () {
    $berbayar = eventBuka(['biaya_pendaftaran' => 50000]);
    $user = pesertaTim();

    $this->actingAs($user)->post(urlDaftar($berbayar), dataDaftar())
        ->assertSessionHasErrors('file_bukti_bayar');
    expect(Tim::count())->toBe(0);

    $this->actingAs($user)->post(urlDaftar($berbayar), dataDaftar(['file_bukti_bayar' => pdfPalsu('bukti.pdf')]))
        ->assertSessionHasNoErrors();

    $tim = Tim::first();
    Storage::disk('local')->assertExists($tim->file_bukti_bayar);
});

it('store menolak berkas dengan tipe atau ukuran tidak valid', function () {
    $event = eventBuka();
    $user = pesertaTim();

    $this->actingAs($user)->post(urlDaftar($event), dataDaftar([
        'file_ktm' => UploadedFile::fake()->create('ktm.exe', 100, 'application/x-msdownload'),
        'file_pakta' => UploadedFile::fake()->create('pakta.pdf', 5000, 'application/pdf'),
    ]))->assertSessionHasErrors(['file_ktm', 'file_pakta']);

    expect(Tim::count())->toBe(0)
        ->and(Storage::disk('local')->allFiles())->toBeEmpty();
});

it('store menolak jumlah anggota di luar batas event', function () {
    $event = eventBuka(); // total 2 sampai 3, jadi anggota di luar ketua 1 sampai 2
    $user = pesertaTim();

    $this->actingAs($user)->post(urlDaftar($event), dataDaftar(['anggota' => []]))
        ->assertSessionHasErrors('anggota');

    $this->actingAs($user)->post(urlDaftar($event), dataDaftar(['anggota' => [
        ['nama_lengkap' => 'A', 'nim' => 'H071240101'],
        ['nama_lengkap' => 'B', 'nim' => 'H071240102'],
        ['nama_lengkap' => 'C', 'nim' => 'H071240103'],
    ]]))->assertSessionHasErrors('anggota');

    expect(Tim::count())->toBe(0);
});

it('store menolak NIM ganda dalam satu pendaftaran', function () {
    $event = eventBuka();
    $user = pesertaTim();

    // NIM anggota sama dengan NIM ketua
    $this->actingAs($user)->post(urlDaftar($event), dataDaftar(['anggota' => [
        ['nama_lengkap' => 'A', 'nim' => 'H071241001'],
    ]]))->assertSessionHasErrors('anggota.0.nim');

    // dua anggota dengan NIM sama
    $this->actingAs($user)->post(urlDaftar($event), dataDaftar(['anggota' => [
        ['nama_lengkap' => 'A', 'nim' => 'H071240201'],
        ['nama_lengkap' => 'B', 'nim' => 'H071240201'],
    ]]))->assertSessionHasErrors('anggota.1.nim');

    expect(Tim::count())->toBe(0);
});

it('store menolak NIM yang dipakai tim lain di event yang sama tapi mengizinkannya di event lain', function () {
    $event = eventBuka();
    $lain = timDenganBerkas(pesertaTim(), $event, ['nim_ketua' => 'H071240001']);
    $lain->anggota()->create(['event_id' => $event->id, 'nama_lengkap' => 'X', 'nim' => 'H071240002']);
    $user = pesertaTim();

    // NIM ketua sama dengan NIM ketua tim lain, lalu dengan NIM anggota tim lain
    $this->actingAs($user)->post(urlDaftar($event), dataDaftar(['nim_ketua' => 'H071240001']))
        ->assertSessionHasErrors('nim_ketua');
    $this->actingAs($user)->post(urlDaftar($event), dataDaftar(['nim_ketua' => 'H071240002']))
        ->assertSessionHasErrors('nim_ketua');

    // NIM anggota sama dengan NIM ketua tim lain, lalu dengan NIM anggota tim lain
    $this->actingAs($user)->post(urlDaftar($event), dataDaftar(['anggota' => [
        ['nama_lengkap' => 'A', 'nim' => 'H071240001'],
    ]]))->assertSessionHasErrors('anggota.0.nim');
    $this->actingAs($user)->post(urlDaftar($event), dataDaftar(['anggota' => [
        ['nama_lengkap' => 'A', 'nim' => 'H071240002'],
    ]]))->assertSessionHasErrors('anggota.0.nim');

    expect(Tim::count())->toBe(1);

    // event lain boleh memakai NIM yang sama
    $eventLain = eventBuka();
    $this->actingAs($user)->post(urlDaftar($eventLain), dataDaftar(['nim_ketua' => 'H071240001']))
        ->assertSessionHasNoErrors();

    expect(Tim::count())->toBe(2);
});

it('store ditolak jika registrasi ditutup atau peserta sudah mendaftar', function () {
    $user = pesertaTim();
    $tutup = eventBuka(['registrasi_mulai' => now()->subDays(5), 'registrasi_selesai' => now()->subDay()]);

    $this->actingAs($user)->post(urlDaftar($tutup), dataDaftar())->assertSessionHas('error');
    expect(Tim::count())->toBe(0);

    $buka = eventBuka();
    timDenganBerkas($user, $buka);

    $this->actingAs($user)->post(urlDaftar($buka), dataDaftar(['nim_ketua' => 'H071249999']))
        ->assertSessionHas('error');
    expect(Tim::count())->toBe(1);
});

// ---------- Event diikuti ----------

it('event diikuti hanya menampilkan tim milik peserta yang login', function () {
    $event = eventBuka();
    $user = pesertaTim();
    $milikku = timDenganBerkas($user, $event, ['nama_tim' => 'Tim Milikku']);
    $milikku->anggota()->create(['event_id' => $event->id, 'nama_lengkap' => 'Ani', 'nim' => 'H071240301']);
    timDenganBerkas(pesertaTim(), $event, ['nama_tim' => 'Tim Orang Lain']);

    $this->actingAs($user)->get('/peserta/tim')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Peserta/Tim/Index')
            ->has('tim.data', 1)
            ->where('tim.data.0.nama_tim', 'Tim Milikku')
            ->where('tim.data.0.status_verifikasi', 'menunggu')
            ->where('tim.data.0.jumlah_anggota_total', 2)
            ->where('tim.data.0.dapat_diubah', true)
            ->where('tim.data.0.berkas.bukti_bayar', null)
        );
});

// ---------- Edit / Update ----------

it('update mengubah data tim, mengganti anggota, dan mengganti berkas lama', function () {
    $event = eventBuka();
    $user = pesertaTim();
    $tim = timDenganBerkas($user, $event);
    $tim->anggota()->create(['event_id' => $event->id, 'nama_lengkap' => 'Lama', 'nim' => 'H071240010']);
    $ktmLama = $tim->file_ktm;

    $this->actingAs($user)->put("/peserta/tim/{$tim->id}", [
        'nama_tim' => 'Nama Baru',
        'asal_perguruan_tinggi' => 'Universitas Baru',
        'nim_ketua' => $tim->nim_ketua,
        'file_ktm' => pdfPalsu('ktm-baru.pdf'),
        'anggota' => [['nama_lengkap' => 'Budi', 'nim' => 'H071240020']],
    ])->assertRedirect(route('peserta.tim.index'))->assertSessionHas('success');

    $tim->refresh();

    expect($tim->nama_tim)->toBe('Nama Baru')
        ->and($tim->asal_perguruan_tinggi)->toBe('Universitas Baru')
        ->and($tim->anggota->pluck('nim')->all())->toBe(['H071240020'])
        ->and($tim->file_ktm)->not->toBe($ktmLama);

    Storage::disk('local')->assertMissing($ktmLama);
    Storage::disk('local')->assertExists($tim->file_ktm);
    Storage::disk('local')->assertExists($tim->file_pakta); // tidak diganti, tetap ada
});

it('update menolak NIM yang dipakai tim lain', function () {
    $event = eventBuka();
    $user = pesertaTim();
    $tim = timDenganBerkas($user, $event);
    timDenganBerkas(pesertaTim(), $event, ['nim_ketua' => 'H071240555']);

    $this->actingAs($user)->put("/peserta/tim/{$tim->id}", [
        'nama_tim' => $tim->nama_tim,
        'asal_perguruan_tinggi' => $tim->asal_perguruan_tinggi,
        'nim_ketua' => 'H071240555',
        'anggota' => [['nama_lengkap' => 'Budi', 'nim' => 'H071240021']],
    ])->assertSessionHasErrors('nim_ketua');
});

it('update dan batal ditolak jika tim sudah diverifikasi', function () {
    $event = eventBuka();
    $user = pesertaTim();
    $tim = timDenganBerkas($user, $event, ['status_verifikasi' => 'disetujui', 'nama_tim' => 'Nama Lama']);

    $this->actingAs($user)->put("/peserta/tim/{$tim->id}", [
        'nama_tim' => 'Nama Baru',
        'asal_perguruan_tinggi' => $tim->asal_perguruan_tinggi,
        'nim_ketua' => $tim->nim_ketua,
        'anggota' => [['nama_lengkap' => 'Budi', 'nim' => 'H071240022']],
    ])->assertSessionHas('error');

    $this->actingAs($user)->delete("/peserta/tim/{$tim->id}")->assertSessionHas('error');

    expect($tim->fresh()->nama_tim)->toBe('Nama Lama');
    $this->assertDatabaseHas('tim', ['id' => $tim->id]);
});

it('peserta tidak bisa membuka, mengubah, atau membatalkan tim milik orang lain', function () {
    $event = eventBuka();
    $tim = timDenganBerkas(pesertaTim(), $event);
    $penyusup = pesertaTim();

    $this->actingAs($penyusup)->get("/peserta/tim/{$tim->id}/edit")->assertForbidden();
    $this->actingAs($penyusup)->put("/peserta/tim/{$tim->id}", [])->assertForbidden();
    $this->actingAs($penyusup)->delete("/peserta/tim/{$tim->id}")->assertForbidden();

    $this->assertDatabaseHas('tim', ['id' => $tim->id]);
});

// ---------- Destroy ----------

it('batal mendaftar menghapus tim, anggota, dan berkas', function () {
    $event = eventBuka();
    $user = pesertaTim();
    $tim = timDenganBerkas($user, $event);
    $tim->anggota()->create(['event_id' => $event->id, 'nama_lengkap' => 'Ani', 'nim' => 'H071240030']);
    $ktm = $tim->file_ktm;

    $this->actingAs($user)->delete("/peserta/tim/{$tim->id}")
        ->assertRedirect(route('peserta.tim.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('tim', ['id' => $tim->id]);
    $this->assertDatabaseMissing('anggota_tim', ['tim_id' => $tim->id]);
    Storage::disk('local')->assertMissing($ktm);
});

// ---------- Berkas ----------

it('berkas hanya bisa dibuka pemilik tim dan panitia', function () {
    $event = eventBuka();
    $user = pesertaTim();
    $tim = timDenganBerkas($user, $event);

    $this->actingAs($user)->get("/berkas/tim/{$tim->id}/ktm")->assertOk();
    $this->actingAs(User::factory()->panitia()->create())->get("/berkas/tim/{$tim->id}/pakta")->assertOk();
    $this->actingAs(pesertaTim())->get("/berkas/tim/{$tim->id}/ktm")->assertForbidden();

    $this->actingAs($user)->get("/berkas/tim/{$tim->id}/ngawur")->assertNotFound();
    $this->actingAs($user)->get("/berkas/tim/{$tim->id}/bukti_bayar")->assertNotFound(); // belum diunggah
});

it('guest tidak bisa membuka berkas', function () {
    $tim = timDenganBerkas(pesertaTim(), eventBuka());

    $this->get("/berkas/tim/{$tim->id}/ktm")->assertRedirect();
});