<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Factory ini hanya membuat baris `event`. Baris turunannya (event_seminar /
 * event_lomba) dibuat lewat EventSeminarFactory / EventLombaFactory, dan
 * state seminar()/lomba() di sini menjaga jenis_event tetap cocok.
 *
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    public function definition(): array
    {
        $mulai = fake()->dateTimeBetween('-1 week', '+1 week');

        return [
            'pembuat_id' => User::factory()->panitia(),
            'jenis_event' => fake()->randomElement(['lomba', 'seminar']),
            'nama_event' => Str::title(fake()->words(3, true)),
            'tema' => Str::title(fake()->words(4, true)),
            'deskripsi' => fake()->paragraph(),
            'gambar_pendukung' => 'event/'.fake()->lexify('????????').'.jpg',
            'registrasi_mulai' => $mulai,
            'registrasi_selesai' => Carbon::instance($mulai)->addWeeks(3),
            'status_publikasi' => 'draft',
        ];
    }

    public function seminar(): static
    {
        return $this->state(fn (array $attributes) => ['jenis_event' => 'seminar']);
    }

    public function lomba(): static
    {
        return $this->state(fn (array $attributes) => ['jenis_event' => 'lomba']);
    }

    public function publish(): static
    {
        return $this->state(fn (array $attributes) => ['status_publikasi' => 'publish']);
    }
}
