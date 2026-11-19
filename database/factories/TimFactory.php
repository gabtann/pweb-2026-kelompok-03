<?php

namespace Database\Factories;

use App\Models\EventLomba;
use App\Models\Tim;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tim>
 */
class TimFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_id' => EventLomba::factory(),
            'nama_tim' => 'Tim '.Str::title(fake()->unique()->word()),
            'kode_tim' => strtoupper(fake()->unique()->bothify('??####')),
            'file_dokumen_tim' => null,
            'file_bukti_bayar' => null,
            'status_verifikasi' => 'belum',
            'catatan_verifikasi' => null,
            'verifikator_id' => null,
            'diverifikasi_at' => null,
        ];
    }

    public function menunggu(): static
    {
        return $this->state(fn (array $attributes) => [
            'file_dokumen_tim' => 'tim/'.fake()->lexify('????????').'.pdf',
            'file_bukti_bayar' => 'bukti/'.fake()->lexify('????????').'.jpg',
            'status_verifikasi' => 'menunggu',
        ]);
    }

    public function disetujui(?int $verifikatorId = null): static
    {
        return $this->menunggu()->state(fn (array $attributes) => [
            'status_verifikasi' => 'disetujui',
            'verifikator_id' => $verifikatorId ?? User::factory()->panitia(),
            'diverifikasi_at' => now(),
        ]);
    }
}
