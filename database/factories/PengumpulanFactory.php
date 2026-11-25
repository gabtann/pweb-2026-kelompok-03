<?php

namespace Database\Factories;

use App\Models\Pengumpulan;
use App\Models\TahapLomba;
use App\Models\Tim;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pengumpulan>
 */
class PengumpulanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tim_id' => Tim::factory()->disetujui(),
            // Tahap dibuat di event yang sama dengan tim.
            'tahap_lomba_id' => fn (array $attributes) => TahapLomba::factory()->create([
                'event_id' => Tim::findOrFail($attributes['tim_id'])->event_id,
            ]),
            'file_submission' => 'dummy/submission.pdf',
            'submitted_at' => now(),
        ];
    }

    public function dinilai(): static
    {
        return $this->state(fn (array $attributes) => [
            'skor' => fake('id_ID')->randomFloat(2, 60, 100),
            'catatan_penilaian' => fake('id_ID')->sentence(),
            'penilai_id' => User::factory()->panitia(),
            'dinilai_at' => now(),
        ]);
    }
}
