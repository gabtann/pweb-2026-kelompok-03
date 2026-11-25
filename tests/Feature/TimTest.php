<?php

use App\Models\AnggotaTim;
use App\Models\Event;
use App\Models\Pengumpulan;
use App\Models\TahapLomba;
use App\Models\Tim;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('mengisi kode_tim otomatis dan unik', function () {
    $event = Event::factory()->create();
    $tim = Tim::factory()->count(20)->create(['event_id' => $event->id]);

    $kode = $tim->pluck('kode_tim');

    expect($kode->unique())->toHaveCount(20);
    expect($kode->every(fn ($k) => strlen($k) === 6 && $k === strtoupper($k)))->toBeTrue();
});

it('verifikasi() mengisi verifikator_id dan diverifikasi_at', function () {
    $tim = Tim::factory()->create();
    $admin = User::factory()->panitia()->create();

    $tim->verifikasi($admin, 'disetujui', 'Lengkap');

    $tim->refresh();
    expect($tim->status_verifikasi)->toBe('disetujui');
    expect($tim->catatan_verifikasi)->toBe('Lengkap');
    expect($tim->verifikator_id)->toBe($admin->id);
    expect($tim->diverifikasi_at)->not->toBeNull();
});

it('verifikasi() gagal untuk peserta atau status tidak valid', function () {
    $tim = Tim::factory()->create();

    expect(fn () => $tim->verifikasi(User::factory()->create(), 'disetujui'))
        ->toThrow(InvalidArgumentException::class);

    expect(fn () => $tim->verifikasi(User::factory()->panitia()->create(), 'menunggu'))
        ->toThrow(InvalidArgumentException::class);

    expect($tim->fresh()->status_verifikasi)->toBe('menunggu');
});

it('bolehMengumpulkan() mengikuti status verifikasi dan kelulusan tahap sebelumnya', function () {
    $event = Event::factory()->create();
    $tahap1 = TahapLomba::factory()->pdf()->create(['event_id' => $event->id]);
    $tahap2 = TahapLomba::factory()->pptx()->create(['event_id' => $event->id]);

    $menunggu = Tim::factory()->create(['event_id' => $event->id]);
    expect($menunggu->bolehMengumpulkan($tahap1))->toBeFalse();

    $tim = Tim::factory()->disetujui()->create(['event_id' => $event->id]);
    expect($tim->bolehMengumpulkan($tahap1))->toBeTrue();
    expect($tim->bolehMengumpulkan($tahap2))->toBeFalse();

    Pengumpulan::factory()->create([
        'tim_id' => $tim->id,
        'tahap_lomba_id' => $tahap1->id,
        'status_kelulusan' => 'tidak_lolos',
    ]);
    expect($tim->bolehMengumpulkan($tahap2))->toBeFalse();

    Pengumpulan::where('tim_id', $tim->id)->update(['status_kelulusan' => 'lolos']);
    expect($tim->bolehMengumpulkan($tahap2))->toBeTrue();
});

it('bolehMengumpulkan() menolak tahap dari event lain', function () {
    $tim = Tim::factory()->disetujui()->create();
    $tahapLain = TahapLomba::factory()->pdf()->create();

    expect($tim->bolehMengumpulkan($tahapLain))->toBeFalse();
});

it('menolak NIM anggota yang sama di lomba yang sama', function () {
    $event = Event::factory()->create();
    $timA = Tim::factory()->create(['event_id' => $event->id]);
    $timB = Tim::factory()->create(['event_id' => $event->id]);

    AnggotaTim::factory()->create(['tim_id' => $timA->id, 'nim' => 'H071231001']);

    expect(fn () => AnggotaTim::factory()->create(['tim_id' => $timB->id, 'nim' => 'H071231001']))
        ->toThrow(QueryException::class);
});

it('mengizinkan NIM yang sama di lomba berbeda', function () {
    $timA = Tim::factory()->create();
    $timB = Tim::factory()->create();

    AnggotaTim::factory()->create(['tim_id' => $timA->id, 'nim' => 'H071231001']);
    AnggotaTim::factory()->create(['tim_id' => $timB->id, 'nim' => 'H071231001']);

    expect(AnggotaTim::where('nim', 'H071231001')->count())->toBe(2);
});

it('jumlahAnggotaTotal() menghitung ketua', function () {
    $tim = Tim::factory()->create();
    AnggotaTim::factory()->count(2)->create(['tim_id' => $tim->id]);

    expect($tim->jumlahAnggotaTotal())->toBe(3);
});
