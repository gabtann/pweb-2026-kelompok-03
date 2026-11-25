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
});

function adminTahap(): User
{
    return User::factory()->panitia()->create();
}

function dataTahapValid(array $override = []): array
{
    $f = 'Y-m-d\TH:i';

    return array_merge([
        'nomor_tahap' => 1,
        'nama_tahap' => 'Proposal',
        'jenis_berkas' => 'pdf',
        'batas_mulai' => now()->addDay()->format($f),
        'batas_akhir' => now()->addDays(7)->format($f),
        'kuota_lolos' => 10,
    ], $override);
}

// ---------- Akses ----------

it('peserta mendapat 403 di semua route tahap', function () {
    $event = Event::factory()->create();
    $tahap = TahapLomba::factory()->pdf()->create(['event_id' => $event->id]);
    $peserta = User::factory()->create();
    $dasar = "/panitia/event/{$event->id}/tahap";

    foreach ([
        ['get', "{$dasar}/create"],
        ['post', $dasar],
        ['get', "{$dasar}/{$tahap->id}/edit"],
        ['put', "{$dasar}/{$tahap->id}"],
        ['delete', "{$dasar}/{$tahap->id}"],
    ] as [$method, $url]) {
        $this->actingAs($peserta)->{$method}($url)->assertForbidden();
    }

    expect(TahapLomba::count())->toBe(1);
});

it('guest di-redirect dari route tahap', function () {
    $event = Event::factory()->create();

    $this->get("/panitia/event/{$event->id}/tahap/create")->assertRedirect('/');
    $this->post("/panitia/event/{$event->id}/tahap", dataTahapValid())->assertRedirect('/');
});

// ---------- Create / Store ----------

it('form tambah tahap menyarankan nomor tahap berikutnya', function () {
    $event = Event::factory()->create();
    $admin = adminTahap();

    $this->actingAs($admin)->get("/panitia/event/{$event->id}/tahap/create")
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Panitia/Tahap/Create')
            ->where('event.id', $event->id)
            ->where('nomorTahapBerikutnya', 1)
            ->has('jenisBerkas', 2));

    TahapLomba::factory()->pdf()->create(['event_id' => $event->id]);
    TahapLomba::factory()->pptx()->create(['event_id' => $event->id]);

    $this->actingAs($admin)->get("/panitia/event/{$event->id}/tahap/create")
        ->assertInertia(fn (Assert $page) => $page->where('nomorTahapBerikutnya', 3));
});

it('store menyimpan tahap dan kembali ke detail event', function () {
    $event = Event::factory()->create();

    $this->actingAs(adminTahap())
        ->post("/panitia/event/{$event->id}/tahap", dataTahapValid(['nama_tahap' => 'Babak Proposal']))
        ->assertRedirect("/panitia/event/{$event->id}")
        ->assertSessionHas('success');

    $tahap = $event->tahapLomba()->firstOrFail();
    expect($tahap->nama_tahap)->toBe('Babak Proposal');
    expect($tahap->jenis_berkas)->toBe('pdf');
    expect($tahap->kuota_lolos)->toBe(10);
});

// ---------- Validasi ----------

it('menolak nomor_tahap duplikat di event yang sama tapi boleh di event lain', function () {
    $event = Event::factory()->create();
    $lain = Event::factory()->create();
    TahapLomba::factory()->pdf()->create(['event_id' => $event->id]);
    $admin = adminTahap();

    $this->actingAs($admin)
        ->post("/panitia/event/{$event->id}/tahap", dataTahapValid(['nomor_tahap' => 1]))
        ->assertSessionHasErrors('nomor_tahap');

    expect($event->tahapLomba()->count())->toBe(1);

    $this->actingAs($admin)
        ->post("/panitia/event/{$lain->id}/tahap", dataTahapValid(['nomor_tahap' => 1]))
        ->assertSessionHasNoErrors();

    expect($lain->tahapLomba()->count())->toBe(1);
});

it('menolak batas_akhir sebelum batas_mulai, jenis_berkas tidak valid, dan kuota 0', function () {
    $event = Event::factory()->create();
    $admin = adminTahap();
    $url = "/panitia/event/{$event->id}/tahap";

    $this->actingAs($admin)
        ->post($url, dataTahapValid([
            'batas_mulai' => now()->addDays(5)->format('Y-m-d\TH:i'),
            'batas_akhir' => now()->addDays(2)->format('Y-m-d\TH:i'),
        ]))
        ->assertSessionHasErrors('batas_akhir');

    $this->actingAs($admin)->post($url, dataTahapValid(['jenis_berkas' => 'docx']))
        ->assertSessionHasErrors('jenis_berkas');

    $this->actingAs($admin)->post($url, dataTahapValid(['kuota_lolos' => 0]))
        ->assertSessionHasErrors('kuota_lolos');

    expect(TahapLomba::count())->toBe(0);
});

// ---------- Edit / Update ----------

it('edit menampilkan data tahap', function () {
    $event = Event::factory()->create();
    $tahap = TahapLomba::factory()->pptx()->create(['event_id' => $event->id]);

    $this->actingAs(adminTahap())->get("/panitia/event/{$event->id}/tahap/{$tahap->id}/edit")
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Panitia/Tahap/Edit')
            ->where('event.id', $event->id)
            ->where('tahap.id', $tahap->id)
            ->where('tahap.nama_tahap', 'Babak Presentasi')
            ->where('tahap.batas_mulai', $tahap->batas_mulai->format('Y-m-d\TH:i')));
});

it('update mengubah tahap dan nomor_tahap milik sendiri tidak dianggap duplikat', function () {
    $event = Event::factory()->create();
    $tahap = TahapLomba::factory()->pdf()->create(['event_id' => $event->id]);

    $this->actingAs(adminTahap())
        ->put("/panitia/event/{$event->id}/tahap/{$tahap->id}", dataTahapValid([
            'nomor_tahap' => 1,
            'nama_tahap' => 'Diubah',
            'kuota_lolos' => 5,
        ]))
        ->assertRedirect("/panitia/event/{$event->id}")
        ->assertSessionHasNoErrors();

    $tahap->refresh();
    expect($tahap->nama_tahap)->toBe('Diubah');
    expect($tahap->kuota_lolos)->toBe(5);
});

it('update menolak nomor_tahap yang dipakai tahap lain di event yang sama', function () {
    $event = Event::factory()->create();
    TahapLomba::factory()->pdf()->create(['event_id' => $event->id]);
    $kedua = TahapLomba::factory()->pptx()->create(['event_id' => $event->id]);

    $this->actingAs(adminTahap())
        ->put("/panitia/event/{$event->id}/tahap/{$kedua->id}", dataTahapValid(['nomor_tahap' => 1]))
        ->assertSessionHasErrors('nomor_tahap');

    expect($kedua->fresh()->nomor_tahap)->toBe(2);
});

// ---------- Destroy ----------

it('destroy menghapus tahap tanpa pengumpulan', function () {
    $event = Event::factory()->create();
    $tahap = TahapLomba::factory()->pdf()->create(['event_id' => $event->id]);

    $this->actingAs(adminTahap())->delete("/panitia/event/{$event->id}/tahap/{$tahap->id}")
        ->assertRedirect("/panitia/event/{$event->id}")
        ->assertSessionHas('success');

    expect(TahapLomba::find($tahap->id))->toBeNull();
});

it('destroy ikut menghapus pengumuman draft milik tahap', function () {
    $event = Event::factory()->create();
    $tahap = TahapLomba::factory()->pdf()->create(['event_id' => $event->id]);
    Pengumuman::factory()->create(['tahap_lomba_id' => $tahap->id]);

    $this->actingAs(adminTahap())->delete("/panitia/event/{$event->id}/tahap/{$tahap->id}")
        ->assertSessionHas('success');

    expect(TahapLomba::find($tahap->id))->toBeNull();
    expect(Pengumuman::count())->toBe(0);
});

it('tahap yang sudah punya pengumpulan tidak bisa dihapus', function () {
    $event = Event::factory()->create();
    $tahap = TahapLomba::factory()->pdf()->create(['event_id' => $event->id]);
    $tim = Tim::factory()->disetujui()->create(['event_id' => $event->id]);
    Pengumpulan::factory()->create(['tim_id' => $tim->id, 'tahap_lomba_id' => $tahap->id]);

    $this->actingAs(adminTahap())->delete("/panitia/event/{$event->id}/tahap/{$tahap->id}")
        ->assertSessionHas('error');

    expect(TahapLomba::find($tahap->id))->not->toBeNull();
});

it('tahap yang pengumumannya sudah dipublikasikan tidak bisa dihapus', function () {
    $event = Event::factory()->create();
    $tahap = TahapLomba::factory()->pdf()->create(['event_id' => $event->id]);
    Pengumuman::factory()->lengkap()->create(['tahap_lomba_id' => $tahap->id, 'dipublikasikan_at' => now()]);

    $this->actingAs(adminTahap())->delete("/panitia/event/{$event->id}/tahap/{$tahap->id}")
        ->assertSessionHas('error');

    expect(TahapLomba::find($tahap->id))->not->toBeNull();
});

// ---------- Scoped binding ----------

it('tahap milik event lain di URL menghasilkan 404', function () {
    $event = Event::factory()->create();
    $lain = Event::factory()->create();
    $tahapLain = TahapLomba::factory()->pdf()->create(['event_id' => $lain->id]);
    $admin = adminTahap();
    $url = "/panitia/event/{$event->id}/tahap/{$tahapLain->id}";

    $this->actingAs($admin)->get("{$url}/edit")->assertNotFound();
    $this->actingAs($admin)->put($url, dataTahapValid(['nomor_tahap' => 9]))->assertNotFound();
    $this->actingAs($admin)->delete($url)->assertNotFound();

    expect($tahapLain->fresh())->not->toBeNull();
    expect($tahapLain->fresh()->nomor_tahap)->toBe(1);
});
