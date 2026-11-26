<?php

use App\Http\Controllers\Panitia\AkunController;
use App\Http\Controllers\Panitia\EventController;
use App\Http\Controllers\Panitia\TahapLombaController;
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

    Route::resource('event', EventController::class);
    Route::resource('akun', AkunController::class)->except(['show']);
    Route::resource('event.tahap', TahapLombaController::class)->except(['index', 'show'])->scoped();
});

Route::prefix('peserta')->name('peserta.')->middleware(['auth', 'role:peserta'])->group(function () {
    Route::view('/dashboard', 'peserta.dashboard')->name('dashboard');
});
