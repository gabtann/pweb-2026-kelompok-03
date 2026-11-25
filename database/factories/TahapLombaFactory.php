<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\TahapLomba;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TahapLomba>
 */
class TahapLombaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'nomor_tahap' => 1,
            'nama_tahap' => 'Babak Proposal',
            'jenis_berkas' => 'pdf',
            'batas_mulai' => now()->subDay(),
            'batas_akhir' => now()->addDays(7),
            'kuota_lolos' => 10,
        ];
    }

    /** Tahap 1: upload proposal PDF. */
    public function pdf(): static
    {
        return $this->state(fn (array $attributes) => [
            'nomor_tahap' => 1,
            'nama_tahap' => 'Babak Proposal',
            'jenis_berkas' => 'pdf',
        ]);
    }

    /** Tahap 2: upload presentasi PPTX. */
    public function pptx(): static
    {
        return $this->state(fn (array $attributes) => [
            'nomor_tahap' => 2,
            'nama_tahap' => 'Babak Presentasi',
            'jenis_berkas' => 'pptx',
        ]);
    }
}
