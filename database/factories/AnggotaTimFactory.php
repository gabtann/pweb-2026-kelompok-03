<?php

namespace Database\Factories;

use App\Models\AnggotaTim;
use App\Models\Tim;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Selalu isi tim_id, event_id, dan user_id secara eksplisit saat seeding:
 * event_id harus sama dengan event_id milik tim (composite FK), dan
 * user hanya boleh 1 tim per event.
 *
 * @extends Factory<AnggotaTim>
 */
class AnggotaTimFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tim_id' => Tim::factory(),
            'event_id' => fn (array $attributes) => Tim::findOrFail($attributes['tim_id'])->event_id,
            'user_id' => User::factory(),
            'peran' => 'anggota',
            'joined_at' => now(),
        ];
    }

    public function ketua(): static
    {
        return $this->state(fn (array $attributes) => ['peran' => 'ketua']);
    }
}
