<?php

use App\Http\Controllers\Panitia\AkunController;
use App\Http\Controllers\Panitia\EventController;
use App\Http\Controllers\Panitia\TahapLombaController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BerkasTimController;
use App\Http\Controllers\Peserta\EventController as PesertaEventController;
use App\Http\Controllers\Peserta\TimController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/dashboard', function () {
    /** @var \App\Models\User $user */
    $user = Auth::user();

    return $user->isPanitia()
        ? redirect()->route('panitia.dashboard')
        : redirect()->route('peserta.dashboard');
})->middleware('auth')->name('dashboard');

Route::get('/berkas/tim/{tim}/{jenis}', [BerkasTimController::class, 'show'])
    ->middleware('auth')
    ->name('berkas.tim');

Route::prefix('panitia')->name('panitia.')->middleware(['auth', 'role:panitia'])->group(function () {
    Route::view('/dashboard', 'panitia.dashboard')->name('dashboard');

    Route::resource('event', EventController::class);
    Route::resource('akun', AkunController::class)->except(['show']);
    Route::resource('event.tahap', TahapLombaController::class)->except(['index', 'show'])->scoped();
});

Route::prefix('peserta')->name('peserta.')->middleware(['auth', 'role:peserta'])->group(function () {
    Route::view('/dashboard', 'peserta.dashboard')->name('dashboard');
    Route::get('/event', [PesertaEventController::class, 'index'])->name('event.index');
    Route::get('/event/{event}/daftar', [TimController::class, 'create'])->name('tim.create');
    Route::post('/event/{event}/daftar', [TimController::class, 'store'])->name('tim.store');
    Route::resource('tim', TimController::class)->only(['index', 'edit', 'update', 'destroy']);
});
