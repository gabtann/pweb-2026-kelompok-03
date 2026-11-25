<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// 'role' sengaja TIDAK di fillable (cegah privilege escalation lewat mass assignment).
#[Fillable(['nama_lengkap', 'email', 'no_telepon', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function isPanitia(): bool
    {
        return $this->role === 'panitia';
    }

    public function isPeserta(): bool
    {
        return $this->role === 'peserta';
    }

    public function eventDibuat(): HasMany
    {
        return $this->hasMany(Event::class, 'pembuat_id', 'id');
    }

    public function timDiketuai(): HasMany
    {
        return $this->hasMany(Tim::class, 'ketua_id', 'id');
    }

    public function timDiverifikasi(): HasMany
    {
        return $this->hasMany(Tim::class, 'verifikator_id', 'id');
    }

    public function pengumpulanDinilai(): HasMany
    {
        return $this->hasMany(Pengumpulan::class, 'penilai_id', 'id');
    }

    public function pengumumanDibuat(): HasMany
    {
        return $this->hasMany(Pengumuman::class, 'pembuat_id', 'id');
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
