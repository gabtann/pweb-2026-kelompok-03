<?php

use App\Models\Event;
use App\Models\Pengumpulan;
use App\Models\Pengumuman;
use App\Models\TahapLomba;
use App\Models\Tim;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    // Halaman Vue Panitia/Pengumuman/* dan Peserta/Pengumuman/* dibuat Aji dan Hanz.
    // Hapus baris ini setelah file-nya ada.
    config(['inertia.testing.ensure_pages_exist' => false]);
});

function adminPengumuman(): User
{
    return User::factory()->panitia()->create();
}

function tahapUntukPengumuman(array $override = []): TahapLomba
{
    $event = Event::factory()->create();

    return TahapLomba::factory()->pdf()->create(array_merge([
        'event_id' => $event->id,
        'kuota_lolos' => 2,
    ], $override));
}

function pengumpulanBernilai(TahapLomba $tahap, float $skor): Pengumpulan
{
    $tim = Tim::factory()->disetujui()->create(['event_id' => $tahap->event_id]);

    return Pengumpulan::factory()->create([
        'tim_id' => $tim->id,
        'tahap_lomba_id' => $tahap->id,
        'skor' => $skor,
        'submitted_at' => now(),
    ]);
}

// ---------- Akses ----------

it('peserta mendapat 403 dan guest di-redirect dari semua route pengumuman panitia', function () {
    $pengumuman = Pengumuman::factory()->create(['tahap_lomba_id' => tahapUntukPengumuman()->id]);
    $peserta = User::factory()->create();

    $rute = [
        ['get', '/panitia/pengumuman'],
        ['get', '/panitia/pengumuman/create'],
        ['post', '/panitia/pengumuman'],
        ['get', "/panitia/pengumuman/{$pengumuman->id}/edit"],
        ['put', "/panitia/pengumuman/{$pengumuman->id}"],
        ['delete', "/panitia/pengumuman/{$pengumuman->id}"],
        ['post', "/panitia/pengumuman/{$pengumuman->id}/publikasi"],
    ];

    // Guest dulu (belum login), baru peserta: actingAs bertahan sampai test selesai.
    foreach ($rute as [$method, $url]) {
        $this->{$method}($url)->assertRedirect();
    }

    foreach ($rute as [$method, $url]) {
        $this->actingAs($peserta)->{$method}($url)->assertForbidden();
    }

    expect(Pengumuman::count())->toBe(1)
        ->and($pengumuman->fresh()->dipublikasikan_at)->toBeNull();
});

// ---------- Index ----------

it('index menampilkan status penilaian, sertifikat, dan pengumuman', function () {
    Pengumuman::factory()->create([
        'tahap_lomba_id' => tahapUntukPengumuman()->id,
        'link_penilaian' => null,
        'link_sertifikat' => null,
    ]);
    $lengkap = Pengumuman::factory()->lengkap()->create(['tahap_lomba_id' => tahapUntukPengumuman()->id]);
    $umum = Pengumuman::factory()->lengkap()->create([
        'tahap_lomba_id' => tahapUntukPengumuman()->id,
        'dipublikasikan_at' => now(),
    ]);

    $this->actingAs(adminPengumuman())->get('/panitia/pengumuman')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Panitia/Pengumuman/Index')
            ->has('pengumuman.data', 3)
            ->where('pengumuman.data.0.id', $umum->id)
            ->where('pengumuman.data.0.status_pengumuman', 'diumumkan')
            ->where('pengumuman.data.0.dapat_diubah', false)
            ->where('pengumuman.data.0.dapat_dipublikasikan', false)
            ->where('pengumuman.data.1.id', $lengkap->id)
            ->where('pengumuman.data.1.dapat_dipublikasikan', true)
            ->where('pengumuman.data.2.status_penilaian', 'belum')
            ->where('pengumuman.data.2.status_sertifikat', 'belum')
            ->where('pengumuman.data.2.dapat_dipublikasikan', false)
        );
});

it('filter status dan event pada index bekerja', function () {
    $tahapA = tahapUntukPengumuman();
    Pengumuman::factory()->lengkap()->create(['tahap_lomba_id' => $tahapA->id, 'dipublikasikan_at' => now()]);
    Pengumuman::factory()->lengkap()->create(['tahap_lomba_id' => tahapUntukPengumuman()->id]);
    $admin = adminPengumuman();

    $this->actingAs($admin)->get('/panitia/pengumuman?status=diumumkan')
        ->assertInertia(fn (Assert $page) => $page->has('pengumuman.data', 1)->where('filters.status', 'diumumkan'));

    $this->actingAs($admin)->get('/panitia/pengumuman?status=draft')
        ->assertInertia(fn (Assert $page) => $page->has('pengumuman.data', 1));

    $this->actingAs($admin)->get('/panitia/pengumuman?status=ngawur')
        ->assertInertia(fn (Assert $page) => $page->has('pengumuman.data', 2)->where('filters.status', null));

    $this->actingAs($admin)->get("/panitia/pengumuman?event_id={$tahapA->event_id}")
        ->assertInertia(fn (Assert $page) => $page->has('pengumuman.data', 1));
});

// ---------- Create / Store ----------

it('form tambah hanya menawarkan tahap yang belum punya pengumuman', function () {
    $sudah = tahapUntukPengumuman();
    Pengumuman::factory()->create(['tahap_lomba_id' => $sudah->id]);
    $belum = tahapUntukPengumuman();

    $this->actingAs(adminPengumuman())->get('/panitia/pengumuman/create')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Panitia/Pengumuman/Create')
            ->has('tahapTersedia', 1)
            ->where('tahapTersedia.0.id', $belum->id)
        );
});

it('store membuat pengumuman draft dengan pembuat dari user login', function () {
    $tahap = tahapUntukPengumuman();
    $admin = adminPengumuman();

    $this->actingAs($admin)->post('/panitia/pengumuman', [
        'tahap_lomba_id' => $tahap->id,
        'link_penilaian' => 'https://example.com/penilaian',
        'link_sertifikat' => 'https://example.com/sertifikat',
        'pembuat_id' => 999,
        'dipublikasikan_at' => now()->toDateTimeString(),
    ])->assertRedirect(route('panitia.pengumuman.index'))->assertSessionHas('success');

    $pengumuman = Pengumuman::first();

    expect($pengumuman->tahap_lomba_id)->toBe($tahap->id)
        ->and($pengumuman->pembuat_id)->toBe($admin->id)
        ->and($pengumuman->link_penilaian)->toBe('https://example.com/penilaian')
        ->and($pengumuman->dipublikasikan_at)->toBeNull();
});

it('store menolak tahap yang sudah punya pengumuman, tahap tidak ada, dan link bukan http', function () {
    $sudah = tahapUntukPengumuman();
    Pengumuman::factory()->create(['tahap_lomba_id' => $sudah->id]);
    $baru = tahapUntukPengumuman();
    $admin = adminPengumuman();

    $this->actingAs($admin)->post('/panitia/pengumuman', ['tahap_lomba_id' => $sudah->id])
        ->assertSessionHasErrors('tahap_lomba_id');

    $this->actingAs($admin)->post('/panitia/pengumuman', ['tahap_lomba_id' => 999999])
        ->assertSessionHasErrors('tahap_lomba_id');

    $this->actingAs($admin)->post('/panitia/pengumuman', [
        'tahap_lomba_id' => $baru->id,
        'link_penilaian' => 'javascript:alert(1)',
    ])->assertSessionHasErrors('link_penilaian');

    expect(Pengumuman::count())->toBe(1);
});

// ---------- Edit / Update ----------

it('update mengubah link tapi mengabaikan tahap dan status publikasi dari request', function () {
    $tahap = tahapUntukPengumuman();
    $pengumuman = Pengumuman::factory()->create([
        'tahap_lomba_id' => $tahap->id,
        'link_penilaian' => null,
        'link_sertifikat' => null,
    ]);

    $this->actingAs(adminPengumuman())->get("/panitia/pengumuman/{$pengumuman->id}/edit")
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Panitia/Pengumuman/Edit')
            ->where('pengumuman.id', $pengumuman->id)
        );

    $this->actingAs(adminPengumuman())->put("/panitia/pengumuman/{$pengumuman->id}", [
        'link_penilaian' => 'https://example.com/n',
        'link_sertifikat' => 'https://example.com/s',
        'tahap_lomba_id' => 999999,
        'dipublikasikan_at' => now()->toDateTimeString(),
    ])->assertRedirect(route('panitia.pengumuman.index'))->assertSessionHas('success');

    $pengumuman->refresh();

    expect($pengumuman->link_penilaian)->toBe('https://example.com/n')
        ->and($pengumuman->link_sertifikat)->toBe('https://example.com/s')
        ->and($pengumuman->tahap_lomba_id)->toBe($tahap->id)
        ->and($pengumuman->dipublikasikan_at)->toBeNull();
});

it('pengumuman yang sudah dipublikasikan tidak bisa diubah atau dihapus', function () {
    $pengumuman = Pengumuman::factory()->lengkap()->create([
        'tahap_lomba_id' => tahapUntukPengumuman()->id,
        'link_penilaian' => 'https://example.com/lama',
        'dipublikasikan_at' => now(),
    ]);
    $admin = adminPengumuman();

    $this->actingAs($admin)->get("/panitia/pengumuman/{$pengumuman->id}/edit")
        ->assertRedirect(route('panitia.pengumuman.index'));

    $this->actingAs($admin)->put("/panitia/pengumuman/{$pengumuman->id}", [
        'link_penilaian' => 'https://example.com/baru',
        'link_sertifikat' => 'https://example.com/s',
    ])->assertSessionHas('error');

    $this->actingAs($admin)->delete("/panitia/pengumuman/{$pengumuman->id}")
        ->assertSessionHas('error');

    expect($pengumuman->fresh()->link_penilaian)->toBe('https://example.com/lama');
    $this->assertDatabaseHas('pengumuman', ['id' => $pengumuman->id]);
});

// ---------- Destroy ----------

it('destroy menghapus pengumuman draft', function () {
    $pengumuman = Pengumuman::factory()->create(['tahap_lomba_id' => tahapUntukPengumuman()->id]);

    $this->actingAs(adminPengumuman())->delete("/panitia/pengumuman/{$pengumuman->id}")
        ->assertRedirect(route('panitia.pengumuman.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('pengumuman', ['id' => $pengumuman->id]);
});

// ---------- Publikasi ----------

it('publikasi menghitung peringkat dan menandai pengumuman diumumkan', function () {
    $tahap = tahapUntukPengumuman(['kuota_lolos' => 2]);
    $pengumuman = Pengumuman::factory()->lengkap()->create(['tahap_lomba_id' => $tahap->id]);

    foreach ([90, 80, 70] as $skor) {
        pengumpulanBernilai($tahap, $skor);
    }

    $this->actingAs(adminPengumuman())->post("/panitia/pengumuman/{$pengumuman->id}/publikasi")
        ->assertRedirect(route('panitia.pengumuman.index'))
        ->assertSessionHas('success');

    expect($pengumuman->fresh()->dipublikasikan_at)->not->toBeNull()
        ->and(Pengumpulan::where('tahap_lomba_id', $tahap->id)->where('status_kelulusan', 'lolos')->count())->toBe(2);
});

it('publikasi gagal dengan pesan error jika syarat belum terpenuhi', function () {
    $tahap = tahapUntukPengumuman();
    $pengumuman = Pengumuman::factory()->create([
        'tahap_lomba_id' => $tahap->id,
        'link_penilaian' => null,
        'link_sertifikat' => null,
    ]);
    pengumpulanBernilai($tahap, 80);

    $this->actingAs(adminPengumuman())->post("/panitia/pengumuman/{$pengumuman->id}/publikasi")
        ->assertSessionHas('error');

    expect($pengumuman->fresh()->dipublikasikan_at)->toBeNull();
});

// ---------- Halaman pengumuman peserta ----------

it('peserta hanya melihat pengumuman terpublikasi dari event yang diikuti', function () {
    $user = User::factory()->create();
    $tahap = tahapUntukPengumuman();

    $timSaya = Tim::factory()->disetujui()->create(['event_id' => $tahap->event_id, 'ketua_id' => $user->id]);
    Pengumpulan::factory()->create([
        'tim_id' => $timSaya->id,
        'tahap_lomba_id' => $tahap->id,
        'skor' => 90,
        'submitted_at' => now(),
        'peringkat' => 1,
        'status_kelulusan' => 'lolos',
    ]);
    Pengumuman::factory()->lengkap()->create(['tahap_lomba_id' => $tahap->id, 'dipublikasikan_at' => now()]);

    // Draft di event yang sama dan pengumuman terpublikasi di event yang tidak diikuti tidak boleh muncul.
    $tahapDraft = tahapUntukPengumuman(['event_id' => $tahap->event_id, 'nomor_tahap' => 2]);
    Pengumuman::factory()->lengkap()->create(['tahap_lomba_id' => $tahapDraft->id]);
    Pengumuman::factory()->lengkap()->create([
        'tahap_lomba_id' => tahapUntukPengumuman()->id,
        'dipublikasikan_at' => now(),
    ]);

    $this->actingAs($user)->get('/peserta/pengumuman')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Peserta/Pengumuman/Index')
            ->has('pengumuman.data', 1)
            ->where('pengumuman.data.0.hasil_saya.status_kelulusan', 'lolos')
            ->where('pengumuman.data.0.hasil_saya.peringkat', 1)
            ->has('pengumuman.data.0.lolos', 1)
            ->where('pengumuman.data.0.lolos.0.nama_tim', $timSaya->nama_tim)
        );
});

it('panitia mendapat 403 dan guest di-redirect dari halaman pengumuman peserta', function () {
    $this->get('/peserta/pengumuman')->assertRedirect();

    $this->actingAs(adminPengumuman())->get('/peserta/pengumuman')->assertForbidden();
});