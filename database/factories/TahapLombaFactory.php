<?php

namespace Database\Factories;

use App\Models\EventLomba;
use App\Models\TahapLomba;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * nomor_tahap harus diisi eksplisit per event (unique event_id + nomor_tahap).
 *
 * @extends Factory<TahapLomba>
 */
class TahapLombaFactory extends Factory
{
    public function definition(): array
    {
        $mulai = fake()->dateTimeBetween('+1 week', '+1 month');

        return [
            'event_id' => EventLomba::factory(),
            'nomor_tahap' => 1,
            'nama_tahap' => 'Penyisihan',
            'jenis_berkas' => 'Proposal',
            'batas_mulai' => $mulai,
            'batas_akhir' => Carbon::instance($mulai)->addWeeks(2),
            'kuota_lolos' => null,
        ];
    }
}
