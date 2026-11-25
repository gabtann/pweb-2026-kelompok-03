<?php

use App\Models\AnggotaTim;
use App\Models\Event;
use App\Models\Pengumpulan;
use App\Models\Pengumuman;
use App\Models\TahapLomba;
use App\Models\Tim;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(fn () => $this->seed());

it('membuat 3 panitia, 15 peserta, dan admin Iterate', function () {
    expect(User::where('role', 'panitia')->count())->toBe(3);
    expect(User::where('role', 'peserta')->count())->toBe(15);

    $admin = User::where('email', 'admin@iterate.test')->first();
    expect($admin)->not->toBeNull();
    expect($admin->isPanitia())->toBeTrue();
    expect($admin->nama_lengkap)->toBe('Admin Iterate');
});

it('membuat 3 event dan 6 tahap', function () {
    expect(Event::count())->toBe(3);
    expect(TahapLomba::count())->toBe(6);
    expect(Event::pluck('jenis_lomba')->sort()->values()->all())
        ->toBe(['ict', 'software_development', 'uiux']);
});

it('membuat jumlah dan komposisi status tim sesuai rencana', function () {
    $komposisi = fn (string $jenis) => Tim::filterJenis($jenis)->get()->countBy('status_verifikasi')->all();

    expect($komposisi('uiux'))->toEqual(['disetujui' => 12]);
    expect($komposisi('ict'))->toEqual(['menunggu' => 3, 'disetujui' => 2, 'ditolak' => 1]);
    expect($komposisi('software_development'))->toEqual(['menunggu' => 2, 'disetujui' => 2, 'ditolak' => 1]);

    expect(Tim::where('status_verifikasi', 'ditolak')->whereNull('catatan_verifikasi')->count())->toBe(0);
    expect(Tim::where('status_verifikasi', 'disetujui')->whereNull('verifikator_id')->count())->toBe(0);
});

it('memberi tiap tim 1-2 anggota dan NIM anggota tidak sama dengan NIM ketua', function () {
    $semua = Tim::withCount('anggota')->get();

    expect($semua)->toHaveCount(23);
    expect($semua->every(fn ($t) => $t->anggota_count >= 1 && $t->anggota_count <= 2))->toBeTrue();

    $irisan = array_intersect(AnggotaTim::pluck('nim')->all(), Tim::pluck('nim_ketua')->all());
    expect($irisan)->toBeEmpty();
});

it('mengisi bukti bayar hanya untuk lomba berbayar', function () {
    $gratis = Tim::filterJenis('ict')->get();
    $berbayar = Tim::filterJenis('uiux')->get()->merge(Tim::filterJenis('software_development')->get());

    expect($gratis->every(fn ($t) => $t->file_bukti_bayar === null))->toBeTrue();
    expect($berbayar->every(fn ($t) => $t->file_bukti_bayar !== null))->toBeTrue();
});

it('mempublikasikan pengumuman UI/UX tahap 1 dengan 10 lolos dan 2 tidak lolos', function () {
    $tahap = TahapLomba::where('nomor_tahap', 1)
        ->whereHas('event', fn ($q) => $q->where('jenis_lomba', 'uiux'))
        ->firstOrFail();

    $pengumuman = $tahap->pengumuman;

    expect($pengumuman->isDipublikasikan())->toBeTrue();
    expect($pengumuman->status_penilaian)->toBe('sudah');
    expect($pengumuman->status_sertifikat)->toBe('sudah');

    $pengumpulan = Pengumpulan::where('tahap_lomba_id', $tahap->id)->get();
    expect($pengumpulan)->toHaveCount(12);
    expect($pengumpulan->where('status_kelulusan', 'lolos'))->toHaveCount(10);
    expect($pengumpulan->where('status_kelulusan', 'tidak_lolos'))->toHaveCount(2);

    expect($tahap->peringkatLolos())->toHaveCount(10);
});

it('membiarkan semua pengumuman lain tetap draft', function () {
    expect(Pengumuman::count())->toBe(6);
    expect(Pengumuman::whereNotNull('dipublikasikan_at')->count())->toBe(1);

    $draft = Pengumuman::whereNull('dipublikasikan_at')->get();
    expect($draft)->toHaveCount(5);
    expect($draft->every(fn ($p) => $p->link_penilaian === null && $p->link_sertifikat === null))->toBeTrue();
});

it('menyisakan pengumpulan ICT tahap 1 tanpa skor', function () {
    $ict = Event::where('jenis_lomba', 'ict')->firstOrFail();
    $tahap = $ict->tahapLomba()->where('nomor_tahap', 1)->firstOrFail();

    $pengumpulan = Pengumpulan::where('tahap_lomba_id', $tahap->id)->get();

    expect($pengumpulan)->toHaveCount(2);
    expect($pengumpulan->every(fn ($p) => $p->skor === null && $p->peringkat === null))->toBeTrue();
});
