<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahapLomba extends Model
{
    protected $table = 'tahap_lomba';

    protected $fillable = [
        'event_id',
        'nomor_tahap',
        'nama_tahap',
        'jenis_berkas',
        'batas_mulai',
        'batas_akhir',
        'kuota_lolos',
    ];

    protected function casts(): array
    {
        return [
            'nomor_tahap' => 'integer',
            'batas_mulai' => 'datetime',
            'batas_akhir' => 'datetime',
            'kuota_lolos' => 'integer',
        ];
    }

    public function eventLomba(): BelongsTo
    {
        return $this->belongsTo(EventLomba::class, 'event_id', 'event_id');
    }

    public function pengumpulan(): HasMany
    {
        return $this->hasMany(Pengumpulan::class, 'tahap_lomba_id', 'id');
    }

    public function hasilTahap(): HasMany
    {
        return $this->hasMany(HasilTahap::class, 'tahap_lomba_id', 'id');
    }

    public function pengumuman(): HasMany
    {
        return $this->hasMany(Pengumuman::class, 'tahap_lomba_id', 'id');
    }
}
