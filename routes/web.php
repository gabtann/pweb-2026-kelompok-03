<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    /** @var \App\Models\User $user */
    $user = Auth::user();

    return $user->isPanitia()
        ? redirect()->route('panitia.dashboard')
        : redirect()->route('peserta.dashboard');
})->middleware('auth')->name('dashboard');

Route::prefix('panitia')->name('panitia.')->middleware(['auth', 'role:panitia'])->group(function () {
    Route::view('/dashboard', 'panitia.dashboard')->name('dashboard');
});

Route::prefix('peserta')->name('peserta.')->middleware(['auth', 'role:peserta'])->group(function () {
    Route::view('/dashboard', 'peserta.dashboard')->name('dashboard');
});
