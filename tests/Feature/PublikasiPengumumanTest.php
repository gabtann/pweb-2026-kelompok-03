<?php

use App\Models\Event;
use App\Models\Pengumpulan;
use App\Models\Pengumuman;
use App\Models\TahapLomba;
use App\Models\Tim;
use App\Models\User;
use App\Services\PengumumanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

/**
 * Siapkan 1 lomba, tahap 1 (kuota_lolos 10), dan pengumuman untuk tahap itu.
 *
 * @return array{0: TahapLomba, 1: Pengumuman}
 */
function siapkanTahapPublikasi(bool $lengkap = true): array
{
    $event = Event::factory()->create();
    $tahap = TahapLomba::factory()->pdf()->create(['event_id' => $event->id, 'kuota_lolos' => 10]);

    $pengumuman = $lengkap
        ? Pengumuman::factory()->lengkap()->create(['tahap_lomba_id' => $tahap->id])
        : Pengumuman::factory()->create(['tahap_lomba_id' => $tahap->id]);

    return [$tahap, $pengumuman];
}

function buatPengumpulan(TahapLomba $tahap, ?float $skor, $submittedAt = null): Pengumpulan
{
    $tim = Tim::factory()->disetujui()->create(['event_id' => $tahap->event_id]);

    return Pengumpulan::factory()->create([
        'tim_id' => $tim->id,
        'tahap_lomba_id' => $tahap->id,
        'skor' => $skor,
        'submitted_at' => $submittedAt ?? now(),
    ]);
}

it('menghitung peringkat dan status lolos otomatis saat publish', function () {
    [$tahap, $pengumuman] = siapkanTahapPublikasi();

    $skor = collect(range(1, 12))->map(fn ($i) => 50 + $i * 3)->shuffle();
    $skor->each(fn ($s) => buatPengumpulan($tahap, $s));

    app(PengumumanService::class)->publikasikan($pengumuman, User::factory()->panitia()->create());

    $hasil = Pengumpulan::where('tahap_lomba_id', $tahap->id)->orderBy('peringkat')->get();

    expect($hasil->pluck('peringkat')->all())->toBe(range(1, 12));
    expect($hasil->pluck('skor')->map(fn ($s) => (float) $s)->all())
        ->toBe($skor->sortDesc()->values()->map(fn ($s) => (float) $s)->all());
    expect($hasil->where('status_kelulusan', 'lolos'))->toHaveCount(10);
    expect($hasil->where('status_kelulusan', 'tidak_lolos'))->toHaveCount(2);
    expect($hasil->take(10)->every(fn ($p) => $p->isLolos()))->toBeTrue();
    expect($pengumuman->fresh()->dipublikasikan_at)->not->toBeNull();
});

it('memenangkan submitted_at paling awal saat skor seri', function () {
    [$tahap, $pengumuman] = siapkanTahapPublikasi();

    $lambat = buatPengumpulan($tahap, 80, now()->subHour());
    $awal = buatPengumpulan($tahap, 80, now()->subDay());
    $atas = buatPengumpulan($tahap, 95);

    app(PengumumanService::class)->publikasikan($pengumuman, User::factory()->panitia()->create());

    expect($atas->fresh()->peringkat)->toBe(1);
    expect($awal->fresh()->peringkat)->toBe(2);
    expect($lambat->fresh()->peringkat)->toBe(3);
});

it('gagal jika link penilaian kosong', function () {
    [$tahap, $pengumuman] = siapkanTahapPublikasi();
    buatPengumpulan($tahap, 80);
    $pengumuman->update(['link_penilaian' => null]);

    expect(fn () => app(PengumumanService::class)->publikasikan($pengumuman, User::factory()->panitia()->create()))
        ->toThrow(DomainException::class);

    expect($pengumuman->fresh()->dipublikasikan_at)->toBeNull();
});

it('gagal jika link sertifikat kosong', function () {
    [$tahap, $pengumuman] = siapkanTahapPublikasi();
    buatPengumpulan($tahap, 80);
    $pengumuman->update(['link_sertifikat' => null]);

    expect(fn () => app(PengumumanService::class)->publikasikan($pengumuman, User::factory()->panitia()->create()))
        ->toThrow(DomainException::class);
});

it('gagal jika ada skor yang masih null', function () {
    [$tahap, $pengumuman] = siapkanTahapPublikasi();
    buatPengumpulan($tahap, 80);
    $belumDinilai = buatPengumpulan($tahap, null);

    expect(fn () => app(PengumumanService::class)->publikasikan($pengumuman, User::factory()->panitia()->create()))
        ->toThrow(DomainException::class);

    expect($belumDinilai->fresh()->peringkat)->toBeNull();
    expect($pengumuman->fresh()->dipublikasikan_at)->toBeNull();
});

it('gagal jika sudah dipublikasikan', function () {
    [$tahap, $pengumuman] = siapkanTahapPublikasi();
    buatPengumpulan($tahap, 80);
    $admin = User::factory()->panitia()->create();

    $service = app(PengumumanService::class);
    $service->publikasikan($pengumuman, $admin);

    expect(fn () => $service->publikasikan($pengumuman, $admin))->toThrow(DomainException::class);
});

it('gagal jika user bukan panitia', function () {
    [$tahap, $pengumuman] = siapkanTahapPublikasi();
    buatPengumpulan($tahap, 80);

    expect(fn () => app(PengumumanService::class)->publikasikan($pengumuman, User::factory()->create()))
        ->toThrow(DomainException::class);

    expect($pengumuman->fresh()->dipublikasikan_at)->toBeNull();
});
