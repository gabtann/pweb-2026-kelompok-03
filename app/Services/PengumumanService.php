<?php

namespace App\Services;

use App\Models\Pengumpulan;
use App\Models\Pengumuman;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

class PengumumanService
{
    /**
     * Publikasikan pengumuman: hitung peringkat + status kelulusan otomatis.
     *
     * @throws DomainException
     */
    public function publikasikan(Pengumuman $pengumuman, User $admin): void
    {
        if (! $admin->isPanitia()) {
            throw new DomainException('Hanya panitia yang boleh mempublikasikan pengumuman.');
        }

        DB::transaction(function () use ($pengumuman) {
            // Kunci baris supaya publish ganda bersamaan tidak lolos dua kali.
            $locked = Pengumuman::whereKey($pengumuman->id)->lockForUpdate()->firstOrFail();

            if ($locked->isDipublikasikan()) {
                throw new DomainException('Pengumuman sudah dipublikasikan.');
            }

            if (blank($locked->link_penilaian) || blank($locked->link_sertifikat)) {
                throw new DomainException('Link penilaian dan link sertifikat harus diisi sebelum dipublikasikan.');
            }

            $tahap = $locked->tahapLomba;

            if ($tahap->pengumpulan()->whereNull('skor')->exists()) {
                throw new DomainException('Masih ada pengumpulan yang belum dinilai.');
            }

            $urut = $tahap->pengumpulan()
                ->orderByDesc('skor')
                ->orderBy('submitted_at')
                ->orderBy('id')
                ->get();

            // Reset dulu agar tidak bentrok dengan unique (tahap_lomba_id, peringkat).
            Pengumpulan::where('tahap_lomba_id', $tahap->id)
                ->update(['peringkat' => null, 'status_kelulusan' => null]);

            foreach ($urut as $i => $pengumpulan) {
                $peringkat = $i + 1;

                $pengumpulan->update([
                    'peringkat' => $peringkat,
                    'status_kelulusan' => $peringkat <= $tahap->kuota_lolos ? 'lolos' : 'tidak_lolos',
                ]);
            }

            $locked->update(['dipublikasikan_at' => now()]);
        });

        $pengumuman->refresh();
    }
}
