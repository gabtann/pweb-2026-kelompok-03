<?php

use App\Models\Event;
use App\Models\TahapLomba;
use App\Models\Tim;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    Storage::fake('public');
});

function adminEvent(): User
{
    return User::factory()->panitia()->create();
}

function dataEventValid(array $override = []): array
{
    $f = 'Y-m-d\TH:i';

    return array_merge([
        'jenis_lomba' => 'uiux',
        'nama_event' => 'Lomba UI/UX Nasional',
        'tema' => 'Desain untuk Layanan Publik',
        'deskripsi' => 'Deskripsi lomba.',
        'gambar_pendukung' => UploadedFile::fake()->image('poster.jpg'),
        'link_guidebook' => 'https://example.com/guidebook.pdf',
        'syarat_ketentuan' => 'Mahasiswa aktif.',
        'registrasi_mulai' => now()->addDays(1)->format($f),
        'registrasi_selesai' => now()->addDays(5)->format($f),
        'waktu_mulai' => now()->addDays(10)->format($f),
        'waktu_selesai' => now()->addDays(20)->format($f),
        'biaya_pendaftaran' => '50000',
        'min_anggota' => 2,
        'max_anggota' => 3,
    ], $override);
}

// ---------- Akses ----------

it('peserta mendapat 403 di semua route event', function () {
    $event = Event::factory()->create();
    $peserta = User::factory()->create();

    foreach ([
        ['get', '/panitia/event'],
        ['get', '/panitia/event/create'],
        ['post', '/panitia/event'],
        ['get', "/panitia/event/{$event->id}"],
        ['get', "/panitia/event/{$event->id}/edit"],
        ['put', "/panitia/event/{$event->id}"],
        ['delete', "/panitia/event/{$event->id}"],
    ] as [$method, $url]) {
        $this->actingAs($peserta)->{$method}($url)->assertForbidden();
    }

    expect(Event::count())->toBe(1);
});

it('guest di-redirect dari semua route event', function () {
    $event = Event::factory()->create();

    foreach ([
        ['get', '/panitia/event'],
        ['post', '/panitia/event'],
        ['put', "/panitia/event/{$event->id}"],
        ['delete', "/panitia/event/{$event->id}"],
    ] as [$method, $url]) {
        $this->{$method}($url)->assertRedirect('/');
    }
});

// ---------- Index ----------

it('panitia melihat daftar event terurut jenis beserta jumlah tim', function () {
    $uiux = Event::factory()->uiux()->create(['nama_event' => 'A UIUX']);
    Event::factory()->ict()->create(['nama_event' => 'B ICT']);
    Event::factory()->softwareDevelopment()->create(['nama_event' => 'C SD']);
    Tim::factory()->count(2)->create(['event_id' => $uiux->id]);

    $this->actingAs(adminEvent())->get('/panitia/event')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Panitia/Event/Index')
            ->has('events.data', 3)
            ->where('events.data.0.jenis_lomba', 'uiux')
            ->where('events.data.0.label_jenis_lomba', 'UI/UX')
            ->where('events.data.0.jumlah_tim', 2)
            ->where('events.data.1.jenis_lomba', 'ict')
            ->where('events.data.2.jenis_lomba', 'software_development')
            ->has('jenisLomba', 3)
            ->where('filters.jenis', null));
});

it('filter jenis pada index bekerja dan jenis tidak valid diabaikan', function () {
    Event::factory()->uiux()->count(2)->create();
    Event::factory()->ict()->create();
    $admin = adminEvent();

    $this->actingAs($admin)->get('/panitia/event?jenis=ict')
        ->assertInertia(fn (Assert $page) => $page
            ->has('events.data', 1)
            ->where('events.data.0.jenis_lomba', 'ict')
            ->where('filters.jenis', 'ict'));

    $this->actingAs($admin)->get('/panitia/event?jenis=bukan-jenis')
        ->assertInertia(fn (Assert $page) => $page
            ->has('events.data', 3)
            ->where('filters.jenis', null));
});

it('index memakai paginasi 10 per halaman', function () {
    Event::factory()->count(12)->create();

    $this->actingAs(adminEvent())->get('/panitia/event')
        ->assertInertia(fn (Assert $page) => $page
            ->has('events.data', 10)
            ->where('events.total', 12)
            ->where('events.last_page', 2));
});

// ---------- Create / Store ----------

it('panitia membuka form tambah event', function () {
    $this->actingAs(adminEvent())->get('/panitia/event/create')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Panitia/Event/Create')
            ->has('jenisLomba', 3));
});

it('store menyimpan event, pembuat dari user login, dan gambar tersimpan', function () {
    $admin = adminEvent();
    $lain = User::factory()->panitia()->create();

    $this->actingAs($admin)
        ->post('/panitia/event', dataEventValid(['pembuat_id' => $lain->id]))
        ->assertRedirect('/panitia/event')
        ->assertSessionHas('success');

    $event = Event::firstOrFail();

    expect($event->pembuat_id)->toBe($admin->id);
    expect($event->jenis_lomba)->toBe('uiux');
    expect((int) $event->biaya_pendaftaran)->toBe(50000);
    expect($event->gambar_pendukung)->toStartWith('event/');
    Storage::disk('public')->assertExists($event->gambar_pendukung);
});

it('biaya kosong berarti gratis (null)', function () {
    $this->actingAs(adminEvent())
        ->post('/panitia/event', dataEventValid(['biaya_pendaftaran' => '']))
        ->assertSessionHasNoErrors();

    expect(Event::firstOrFail()->biaya_pendaftaran)->toBeNull();
});

// ---------- Validasi ----------

it('menolak tanggal registrasi terbalik', function () {
    $f = 'Y-m-d\TH:i';

    $this->actingAs(adminEvent())
        ->post('/panitia/event', dataEventValid([
            'registrasi_mulai' => now()->addDays(5)->format($f),
            'registrasi_selesai' => now()->addDays(2)->format($f),
        ]))
        ->assertSessionHasErrors('registrasi_selesai');

    expect(Event::count())->toBe(0);
});

it('menolak waktu mulai lomba sebelum registrasi selesai', function () {
    $this->actingAs(adminEvent())
        ->post('/panitia/event', dataEventValid(['waktu_mulai' => now()->addDays(3)->format('Y-m-d\TH:i')]))
        ->assertSessionHasErrors('waktu_mulai');
});

it('menolak max_anggota lebih kecil dari min_anggota', function () {
    $this->actingAs(adminEvent())
        ->post('/panitia/event', dataEventValid(['min_anggota' => 3, 'max_anggota' => 2]))
        ->assertSessionHasErrors('max_anggota');

    expect(Event::count())->toBe(0);
});

it('menolak jenis_lomba yang tidak valid', function () {
    $this->actingAs(adminEvent())
        ->post('/panitia/event', dataEventValid(['jenis_lomba' => 'seminar']))
        ->assertSessionHasErrors('jenis_lomba');

    expect(Event::count())->toBe(0);
});

it('menolak link_guidebook dengan skema selain http/https', function () {
    $admin = adminEvent();

    foreach (['javascript://%0aalert(1)', 'ftp://example.com/guidebook.pdf', 'data://text/html,x'] as $link) {
        $this->actingAs($admin)
            ->post('/panitia/event', dataEventValid(['link_guidebook' => $link]))
            ->assertSessionHasErrors('link_guidebook');
    }

    expect(Event::count())->toBe(0);
});

it('menolak biaya negatif, gambar bukan gambar, dan store tanpa gambar', function () {
    $admin = adminEvent();

    $this->actingAs($admin)
        ->post('/panitia/event', dataEventValid(['biaya_pendaftaran' => -1]))
        ->assertSessionHasErrors('biaya_pendaftaran');

    $this->actingAs($admin)
        ->post('/panitia/event', dataEventValid(['gambar_pendukung' => UploadedFile::fake()->create('x.pdf', 10, 'application/pdf')]))
        ->assertSessionHasErrors('gambar_pendukung');

    $this->actingAs($admin)
        ->post('/panitia/event', Arr::except(dataEventValid(), ['gambar_pendukung']))
        ->assertSessionHasErrors('gambar_pendukung');

    expect(Event::count())->toBe(0);
});

// ---------- Show / Edit ----------

it('show menampilkan detail event dan daftar tahap', function () {
    $event = Event::factory()->uiux()->create(['gambar_pendukung' => 'event/poster.jpg']);
    TahapLomba::factory()->pdf()->create(['event_id' => $event->id]);

    $this->actingAs(adminEvent())->get("/panitia/event/{$event->id}")
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Panitia/Event/Show')
            ->where('event.id', $event->id)
            ->where('event.label_jenis_lomba', 'UI/UX')
            ->where('event.gambar_url', fn ($url) => str_ends_with($url, 'event/poster.jpg'))
            ->has('tahap', 1)
            ->where('tahap.0.nomor_tahap', 1)
            ->where('tahap.0.sudah_diumumkan', false));
});

it('edit menampilkan data event dengan tanggal siap untuk input datetime-local', function () {
    $event = Event::factory()->ict()->create();

    $this->actingAs(adminEvent())->get("/panitia/event/{$event->id}/edit")
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Panitia/Event/Edit')
            ->where('event.nama_event', $event->nama_event)
            ->where('event.registrasi_mulai', $event->registrasi_mulai->format('Y-m-d\TH:i'))
            ->has('jenisLomba', 3));
});

// ---------- Update ----------

it('update tanpa gambar baru mempertahankan gambar lama dan pembuat', function () {
    $event = Event::factory()->uiux()->create(['gambar_pendukung' => 'event/lama.jpg']);
    Storage::disk('public')->put('event/lama.jpg', 'isi');
    $pembuatAsli = $event->pembuat_id;
    $lain = User::factory()->panitia()->create();

    $this->actingAs(adminEvent())
        ->put("/panitia/event/{$event->id}", Arr::except(dataEventValid([
            'nama_event' => 'Nama Baru',
            'jenis_lomba' => 'ict',
            'pembuat_id' => $lain->id,
        ]), ['gambar_pendukung']))
        ->assertRedirect('/panitia/event')
        ->assertSessionHas('success');

    $event->refresh();
    expect($event->nama_event)->toBe('Nama Baru');
    expect($event->jenis_lomba)->toBe('ict');
    expect($event->pembuat_id)->toBe($pembuatAsli);
    expect($event->gambar_pendukung)->toBe('event/lama.jpg');
    Storage::disk('public')->assertExists('event/lama.jpg');
});

it('update dengan gambar baru menyimpan yang baru dan menghapus yang lama', function () {
    $event = Event::factory()->uiux()->create(['gambar_pendukung' => 'event/lama.jpg']);
    Storage::disk('public')->put('event/lama.jpg', 'isi');

    $this->actingAs(adminEvent())
        ->put("/panitia/event/{$event->id}", dataEventValid(['gambar_pendukung' => UploadedFile::fake()->image('baru.png')]))
        ->assertSessionHasNoErrors();

    $event->refresh();
    expect($event->gambar_pendukung)->not->toBe('event/lama.jpg');
    Storage::disk('public')->assertExists($event->gambar_pendukung);
    Storage::disk('public')->assertMissing('event/lama.jpg');
});

it('update gagal validasi tidak mengubah data', function () {
    $event = Event::factory()->uiux()->create(['nama_event' => 'Asli']);

    $this->actingAs(adminEvent())
        ->put("/panitia/event/{$event->id}", Arr::except(dataEventValid(['nama_event' => 'Diubah', 'min_anggota' => 3, 'max_anggota' => 1]), ['gambar_pendukung']))
        ->assertSessionHasErrors('max_anggota');

    expect($event->fresh()->nama_event)->toBe('Asli');
});

// ---------- Destroy ----------

it('destroy melakukan soft delete', function () {
    $event = Event::factory()->create();

    $this->actingAs(adminEvent())->delete("/panitia/event/{$event->id}")
        ->assertRedirect('/panitia/event')
        ->assertSessionHas('success');

    $this->assertSoftDeleted('event', ['id' => $event->id]);
});

it('event yang sudah punya tim tidak bisa dihapus', function () {
    $event = Event::factory()->create();
    Tim::factory()->create(['event_id' => $event->id]);

    $this->actingAs(adminEvent())->delete("/panitia/event/{$event->id}")
        ->assertRedirect()
        ->assertSessionHas('error');

    expect(Event::find($event->id))->not->toBeNull();
});
