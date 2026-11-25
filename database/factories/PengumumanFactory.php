<?php

namespace Database\Factories;

use App\Models\Pengumuman;
use App\Models\TahapLomba;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pengumuman>
 */
class PengumumanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tahap_lomba_id' => TahapLomba::factory(),
            'pembuat_id' => User::factory()->panitia(),
            'link_penilaian' => null,
            'link_sertifikat' => null,
            'dipublikasikan_at' => null,
        ];
    }

    /** Link penilaian + sertifikat terisi (siap dipublikasikan). */
    public function lengkap(): static
    {
        return $this->state(fn (array $attributes) => [
            'link_penilaian' => 'https://docs.google.com/spreadsheets/d/contoh',
            'link_sertifikat' => 'https://drive.google.com/drive/folders/contoh',
        ]);
    }
}
