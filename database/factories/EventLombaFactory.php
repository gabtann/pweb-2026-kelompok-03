<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventLomba;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<EventLomba>
 */
class EventLombaFactory extends Factory
{
    public function definition(): array
    {
        $mulai = fake()->dateTimeBetween('+1 month', '+2 months');

        return [
            'event_id' => Event::factory()->lomba(),
            'link_guidebook' => 'https://drive.google.com/'.fake()->lexify('????????'),
            'syarat_ketentuan' => fake()->paragraphs(2, true),
            'waktu_mulai' => $mulai,
            'waktu_selesai' => Carbon::instance($mulai)->addMonth(),
            'biaya_pendaftaran' => fake()->randomElement([null, 25000, 50000, 75000]),
            'min_anggota' => 2,
            'max_anggota' => 3,
        ];
    }
}
