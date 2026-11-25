<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tim extends Model
{
    protected $table = 'tim';

    protected $fillable = [
        'event_id',
        'nama_tim',
        'kode_tim',
        'asal_perguruan_tinggi',
        'nim_ketua',
        'file_ktm',
        'file_pakta',
        'file_bukti_bayar',
        'status_verifikasi',
        'catatan_verifikasi',
        'verifikator_id',
        'diverifikasi_at',
    ];

    protected function casts(): array
    {
        return [
            'diverifikasi_at' => 'datetime',
        ];
    }

    public function eventLomba(): BelongsTo
    {
        return $this->belongsTo(EventLomba::class, 'event_id', 'event_id');
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verifikator_id', 'id');
    }

    public function anggotaTim(): HasMany
    {
        return $this->hasMany(AnggotaTim::class, 'tim_id', 'id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'anggota_tim', 'tim_id', 'user_id')
            ->withPivot('peran', 'joined_at')
            ->withTimestamps();
    }

    public function pengumpulan(): HasMany
    {
        return $this->hasMany(Pengumpulan::class, 'tim_id', 'id');
    }

    public function hasilTahap(): HasMany
    {
        return $this->hasMany(HasilTahap::class, 'tim_id', 'id');
    }

    public function sertifikat(): HasMany
    {
        return $this->hasMany(Sertifikat::class, 'tim_id', 'id');
    }
}
