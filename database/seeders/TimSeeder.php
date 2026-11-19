<?php

namespace Database\Seeders;

use App\Models\AnggotaTim;
use App\Models\EventLomba;
use App\Models\Tim;
use App\Models\User;
use Illuminate\Database\Seeder;

class TimSeeder extends Seeder
{
    public function run(): void
    {
        $panitia = User::where('role', 'panitia')->get();
        $peserta = User::where('role', 'peserta')->get();

        foreach (EventLomba::all() as $lomba) {
            // Pool dikocok per lomba; tiap peserta diambil (splice) sehingga
            // maksimal 1 tim per event (unique event_id + user_id).
            $pool = $peserta->shuffle()->values();

            foreach (['disetujui', 'menunggu', 'belum'] as $status) {
                $ukuran = random_int($lomba->min_anggota, $lomba->max_anggota);

                if ($pool->count() < $ukuran) {
                    break;
                }

                $tim = match ($status) {
                    'disetujui' => Tim::factory()->disetujui($panitia->random()->id),
                    'menunggu' => Tim::factory()->menunggu(),
                    default => Tim::factory(),
                };
                $tim = $tim->create(['event_id' => $lomba->event_id]);

                // Anggota pertama = ketua (tepat 1 per tim), sisanya anggota.
                foreach ($pool->splice(0, $ukuran)->values() as $urutan => $user) {
                    $factory = AnggotaTim::factory();
                    if ($urutan === 0) {
                        $factory = $factory->ketua();
                    }

                    $factory->create([
                        'tim_id' => $tim->id,
                        'event_id' => $tim->event_id,
                        'user_id' => $user->id,
                    ]);
                }
            }
        }
    }
}
