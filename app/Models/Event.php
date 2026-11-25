<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $table = 'event';

    protected $fillable = [
        'pembuat_id',
        'nama_event',
        'tema',
        'deskripsi',
        'gambar_pendukung',
        'registrasi_mulai',
        'registrasi_selesai',
    ];

    protected function casts(): array
    {
        return [
            'registrasi_mulai' => 'datetime',
            'registrasi_selesai' => 'datetime',
        ];
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembuat_id', 'id');
    }

    public function eventLomba(): HasOne
    {
        return $this->hasOne(EventLomba::class, 'event_id', 'id');
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class, 'event_id', 'id');
    }

    public function sertifikat(): HasMany
    {
        return $this->hasMany(Sertifikat::class, 'event_id', 'id');
    }

    public function pengumuman(): HasMany
    {
        return $this->hasMany(Pengumuman::class, 'event_id', 'id');
    }
}
