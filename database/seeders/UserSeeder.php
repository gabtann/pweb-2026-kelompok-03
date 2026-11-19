<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Akun demo (password tetap dari UserFactory). Hanya dipanggil di
     * local/testing lewat DatabaseSeeder.
     */
    public function run(): void
    {
        // Admin tetap; role diisi lewat state panitia() (role tidak fillable).
        if (! User::where('email', 'admin@iterate.test')->exists()) {
            User::factory()->panitia()->create([
                'nama_lengkap' => 'Admin Iterate',
                'email' => 'admin@iterate.test',
            ]);
        }

        User::factory()->panitia()->count(2)->create();
        User::factory()->count(10)->create(); // peserta
    }
}
