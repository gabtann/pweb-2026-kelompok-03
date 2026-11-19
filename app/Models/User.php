<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['nama_lengkap', 'email', 'no_telepon', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function eventDibuat(): HasMany
    {
        return $this->hasMany(Event::class, 'pembuat_id', 'id');
    }

    public function anggotaTim(): HasMany
    {
        return $this->hasMany(AnggotaTim::class, 'user_id', 'id');
    }

    public function tim(): BelongsToMany
    {
        return $this->belongsToMany(Tim::class, 'anggota_tim', 'user_id', 'tim_id')
            ->withPivot('event_id', 'peran', 'joined_at')
            ->withTimestamps();
    }

    public function timDiverifikasi(): HasMany
    {
        return $this->hasMany(Tim::class, 'verifikator_id', 'id');
    }

    public function pendaftaranSeminar(): HasMany
    {
        return $this->hasMany(PendaftaranSeminar::class, 'user_id', 'id');
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class, 'user_id', 'id');
    }

    public function sertifikat(): HasMany
    {
        return $this->hasMany(Sertifikat::class, 'user_id', 'id');
    }

    public function sertifikatDiunggah(): HasMany
    {
        return $this->hasMany(Sertifikat::class, 'pengunggah_id', 'id');
    }

    public function pengumpulan(): HasMany
    {
        return $this->hasMany(Pengumpulan::class, 'pengumpul_id', 'id');
    }

    public function pengumpulanDinilai(): HasMany
    {
        return $this->hasMany(Pengumpulan::class, 'penilai_id', 'id');
    }

    public function pengumumanDibuat(): HasMany
    {
        return $this->hasMany(Pengumuman::class, 'pembuat_id', 'id');
    }

    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class, 'user_id', 'id');
    }

    public function landingPageDiubah(): HasMany
    {
        return $this->hasMany(LandingPage::class, 'pengubah_id', 'id');
    }

    public function isPanitia(): bool
    {
        return $this->role === 'panitia';
    }

    public function isPeserta(): bool
    {
        return $this->role === 'peserta';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
