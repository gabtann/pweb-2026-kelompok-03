<?php

namespace Database\Factories;

use App\Models\AnggotaTim;
use App\Models\Tim;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AnggotaTim>
 */
class AnggotaTimFactory extends Factory
{
    public function definition(): array
    {
        $faker = fake('id_ID');

        return [
            'tim_id' => Tim::factory(),
            // event_id selalu ikut event milik tim (composite FK).
            'event_id' => fn (array $attributes) => Tim::findOrFail($attributes['tim_id'])->event_id,
            'nama_lengkap' => $faker->name(),
            'nim' => $faker->unique()->numerify('H071#########'),
        ];
    }
}
