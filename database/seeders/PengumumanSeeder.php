<?php

namespace Database\Seeders;

use App\Models\Pengumuman;
use App\Models\TahapLomba;
use App\Models\User;
use App\Services\PengumumanService;
use Illuminate\Database\Seeder;

class PengumumanSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@iterate.test')->firstOrFail();

        // UI/UX tahap 1: link lengkap, lalu publikasikan lewat service
        // (peringkat dan status_kelulusan dihitung otomatis, tidak diisi manual).
        $tahapUiux = TahapLomba::where('nomor_tahap', 1)
            ->whereHas('event', fn ($q) => $q->where('jenis_lomba', 'uiux'))
            ->firstOrFail();

        $pengumuman = Pengumuman::create([
            'tahap_lomba_id' => $tahapUiux->id,
            'pembuat_id' => $admin->id,
            'link_penilaian' => 'https://docs.google.com/spreadsheets/d/iterate-uiux-tahap-1',
            'link_sertifikat' => 'https://drive.google.com/drive/folders/iterate-uiux-tahap-1',
        ]);

        app(PengumumanService::class)->publikasikan($pengumuman, $admin);

        // Draft (link null, belum dipublikasikan) untuk semua tahap lain.
        TahapLomba::doesntHave('pengumuman')->get()->each(function (TahapLomba $tahap) use ($admin) {
            Pengumuman::create([
                'tahap_lomba_id' => $tahap->id,
                'pembuat_id' => $admin->id,
            ]);
        });
    }
}
