<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

function dataRegister(array $override = []): array
{
    return array_merge([
        'nama_lengkap'          => 'Budi Santoso',
        'email'                 => 'budi@example.com',
        'no_telepon'            => '081234567890',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
    ], $override);
}

it('mendaftarkan ketua tim sebagai peserta dan redirect ke dashboard', function () {
    $this->post('/register', dataRegister())->assertRedirect('/dashboard');

    $this->assertAuthenticated();
    expect(User::where('email', 'budi@example.com')->first()->role)->toBe('peserta');
});

it('tidak bisa mendaftar sebagai panitia lewat input role', function () {
    $this->post('/register', dataRegister(['role' => 'panitia']));

    expect(User::where('email', 'budi@example.com')->first()->role)->toBe('peserta');
});

it('menolak email yang sudah terdaftar', function () {
    User::factory()->create(['email' => 'budi@example.com']);

    $this->post('/register', dataRegister())->assertSessionHasErrors('email');
});

it('menolak konfirmasi password yang tidak cocok', function () {
    $this->post('/register', dataRegister(['password_confirmation' => 'beda']))
        ->assertSessionHasErrors('password');
});

it('login dengan kredensial benar lalu redirect ke dashboard', function () {
    User::factory()->create(['email' => 'a@example.com', 'password' => 'password123']);

    $this->post('/login', ['email' => 'a@example.com', 'password' => 'password123'])
        ->assertRedirect('/dashboard');

    $this->assertAuthenticated();
});

it('menolak login dengan password salah', function () {
    User::factory()->create(['email' => 'a@example.com', 'password' => 'password123']);

    $this->post('/login', ['email' => 'a@example.com', 'password' => 'salah'])
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('logout mengarahkan ke route login', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/logout')->assertRedirect(route('login'));

    $this->assertGuest();
});