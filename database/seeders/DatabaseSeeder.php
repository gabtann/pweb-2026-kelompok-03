<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Data demo memakai akun dengan password tetap, jadi jangan pernah
        // berjalan di production/staging.
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        // Urutan sesuai dependensi: user -> event (+tahap) -> tim (+anggota).
        $this->call([
            UserSeeder::class,
            EventSeeder::class,
            TimSeeder::class,
        ]);
    }
}
