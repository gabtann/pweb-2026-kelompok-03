<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    public function definition(): array
    {
        $faker = fake('id_ID');

        return [
            'pembuat_id' => User::factory()->panitia(),
            'nama_event' => 'Lomba '.$faker->words(3, true),
            'tema' => $faker->sentence(4),
            'deskripsi' => $faker->paragraph(),
            'gambar_pendukung' => 'dummy/event.jpg',
            'link_guidebook' => $faker->url(),
            'syarat_ketentuan' => $faker->paragraph(),
            'registrasi_mulai' => now()->subDays(10),
            'registrasi_selesai' => now()->addDays(10),
            'waktu_mulai' => now()->addDays(20),
            'waktu_selesai' => now()->addDays(30),
            'biaya_pendaftaran' => null,
            'min_anggota' => 2,
            'max_anggota' => 3,
        ];
    }

    public function berbayar(float $biaya = 50000): static
    {
        return $this->state(fn (array $attributes) => [
            'biaya_pendaftaran' => $biaya,
        ]);
    }
}
