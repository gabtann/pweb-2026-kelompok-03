<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('mengarahkan guest ke luar dari halaman terproteksi (bukan 500)', function (string $url) {
    $this->get($url)->assertRedirect('/');
})->with(['/dashboard', '/panitia/dashboard', '/peserta/dashboard']);

it('mengarahkan /dashboard sesuai role', function () {
    $this->actingAs(User::factory()->panitia()->create())
        ->get('/dashboard')->assertRedirect(route('panitia.dashboard'));

    $this->actingAs(User::factory()->create())
        ->get('/dashboard')->assertRedirect(route('peserta.dashboard'));
});

it('panitia bisa akses dashboard panitia, peserta kena 403', function () {
    $this->actingAs(User::factory()->panitia()->create())
        ->get('/panitia/dashboard')->assertOk();

    $this->actingAs(User::factory()->create())
        ->get('/panitia/dashboard')->assertForbidden();
});

it('peserta bisa akses dashboard peserta, panitia kena 403', function () {
    $this->actingAs(User::factory()->create())
        ->get('/peserta/dashboard')->assertOk();

    $this->actingAs(User::factory()->panitia()->create())
        ->get('/peserta/dashboard')->assertForbidden();
});

it('middleware menerima banyak role', function () {
    Route::middleware(['web', 'auth', 'role:panitia,peserta'])
        ->get('/_uji-multi-role', fn () => 'ok');

    $this->actingAs(User::factory()->panitia()->create())
        ->get('/_uji-multi-role')->assertOk();

    $this->actingAs(User::factory()->create())
        ->get('/_uji-multi-role')->assertOk();
});
