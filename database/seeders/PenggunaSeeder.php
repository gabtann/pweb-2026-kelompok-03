<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class PenggunaSeeder extends Seeder
{
    public const JUMLAH_PESERTA = 15;

    /**
     * NIM ketua untuk peserta ke-$urut (1-based, urut id). Tabel users tidak punya
     * kolom NIM, jadi dipakai konsisten di semua tim milik peserta tersebut.
     */
    public static function nimKetua(int $urut): string
    {
        return sprintf('D1212410%02d', $urut);
    }

    public function run(): void
    {
        // role di luar fillable, jadi diisi lewat factory (state panitia()), bukan mass assignment.
        User::factory()->panitia()->create([
            'nama_lengkap' => 'Admin Iterate',
            'email' => 'admin@iterate.test',
        ]);

        User::factory()->panitia()->count(2)->create();

        foreach (range(1, self::JUMLAH_PESERTA) as $urut) {
            User::factory()->create([
                'email' => "peserta{$urut}@iterate.test",
            ]);
        }
    }
}
