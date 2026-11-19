<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventSeminar;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventSeminar>
 */
class EventSeminarFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_id' => Event::factory()->seminar(),
            'penyelenggara' => fake()->company(),
            'waktu_acara' => fake()->dateTimeBetween('+1 month', '+3 months'),
            'lokasi' => 'Aula '.fake()->company(),
            'contact_person' => fake()->name().' ('.fake()->numerify('08##########').')',
            'kuota_peserta' => fake()->randomElement([null, 100, 200, 300]),
        ];
    }
}
