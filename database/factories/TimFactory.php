<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Tim;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tim>
 */
class TimFactory extends Factory
{
    public function definition(): array
    {
        $faker = fake('id_ID');

        // kode_tim sengaja tidak diisi: dibuat otomatis oleh Tim::booted().
        return [
            'event_id' => Event::factory(),
            'ketua_id' => User::factory(),
            'nama_tim' => 'Tim '.$faker->unique()->word(),
            'asal_perguruan_tinggi' => $faker->randomElement([
                'Universitas Hasanuddin',
                'Universitas Negeri Makassar',
                'Universitas Indonesia',
                'Institut Teknologi Bandung',
                'Universitas Gadjah Mada',
            ]),
            'nim_ketua' => $faker->unique()->numerify('H071#########'),
            'file_ktm' => 'dummy/ktm.pdf',
            'file_pakta' => 'dummy/pakta.pdf',
            'file_bukti_bayar' => null,
            'status_verifikasi' => 'menunggu',
        ];
    }

    public function disetujui(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_verifikasi' => 'disetujui',
            'catatan_verifikasi' => 'Berkas lengkap.',
            'verifikator_id' => User::factory()->panitia(),
            'diverifikasi_at' => now(),
        ]);
    }

    public function ditolak(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_verifikasi' => 'ditolak',
            'catatan_verifikasi' => 'Berkas tidak sesuai.',
            'verifikator_id' => User::factory()->panitia(),
            'diverifikasi_at' => now(),
        ]);
    }
}
