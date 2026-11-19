<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventLomba;
use App\Models\EventSeminar;
use App\Models\TahapLomba;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $panitia = User::where('role', 'panitia')->get();

        // 3 seminar. Tiap event hanya punya SATU baris turunan (seminar).
        for ($i = 0; $i < 3; $i++) {
            $event = Event::factory()->seminar()->publish()->create([
                'pembuat_id' => $panitia->random()->id,
            ]);

            EventSeminar::factory()->create(['event_id' => $event->id]);
        }

        // 3 lomba, masing-masing 3 tahap.
        $tahap = [
            ['Penyisihan', 'Proposal', 20],
            ['Semifinal', 'Prototype', 10],
            ['Final', 'Presentasi', null],
        ];

        for ($i = 0; $i < 3; $i++) {
            $event = Event::factory()->lomba()->publish()->create([
                'pembuat_id' => $panitia->random()->id,
            ]);

            EventLomba::factory()->create(['event_id' => $event->id]);

            foreach ($tahap as $urutan => [$nama, $berkas, $kuota]) {
                TahapLomba::factory()->create([
                    'event_id' => $event->id,
                    'nomor_tahap' => $urutan + 1,
                    'nama_tahap' => $nama,
                    'jenis_berkas' => $berkas,
                    'batas_mulai' => now()->addWeeks(1 + $urutan * 2),
                    'batas_akhir' => now()->addWeeks(2 + $urutan * 2),
                    'kuota_lolos' => $kuota,
                ]);
            }
        }
    }
}
