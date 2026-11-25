<?php

use App\Models\Pengumuman;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('status accessor "belum" saat semua kolom kosong', function () {
    $pengumuman = Pengumuman::factory()->create();

    expect($pengumuman->status_penilaian)->toBe('belum');
    expect($pengumuman->status_sertifikat)->toBe('belum');
    expect($pengumuman->status_pengumuman)->toBe('belum');
    expect($pengumuman->isDipublikasikan())->toBeFalse();
});

it('status accessor mengikuti isi kolom', function () {
    $pengumuman = Pengumuman::factory()->create([
        'link_penilaian' => 'https://docs.google.com/spreadsheets/d/x',
    ]);

    expect($pengumuman->status_penilaian)->toBe('sudah');
    expect($pengumuman->status_sertifikat)->toBe('belum');

    $pengumuman->update(['link_sertifikat' => 'https://drive.google.com/x', 'dipublikasikan_at' => now()]);

    expect($pengumuman->status_sertifikat)->toBe('sudah');
    expect($pengumuman->status_pengumuman)->toBe('diumumkan');
    expect($pengumuman->isDipublikasikan())->toBeTrue();
});
