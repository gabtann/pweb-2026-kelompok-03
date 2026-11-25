<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder demo/dev. Hanya jalan di local dan testing.
 *
 * Sengaja TIDAK memakai WithoutModelEvents: Tim::booted() membuat kode_tim
 * lewat event "creating", dan akan terlewat jika event model dimatikan.
 * Tidak idempotent: pakai bersama `migrate:fresh`.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        DB::transaction(function () {
            $this->call([
                PenggunaSeeder::class,
                LombaSeeder::class,
                TimSeeder::class,
                PengumpulanSeeder::class,
                PengumumanSeeder::class,
            ]);
        });
    }
}
