<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumuman';

    // PERINGATAN: jangan isi dari $request->all().
    // dipublikasikan_at hanya boleh diisi lewat PengumumanService::publikasikan().
    protected $fillable = [
        'tahap_lomba_id',
        'pembuat_id',
        'link_penilaian',
        'link_sertifikat',
        'dipublikasikan_at',
    ];

    protected function casts(): array
    {
        return [
            'dipublikasikan_at' => 'datetime',
        ];
    }

    public function tahapLomba(): BelongsTo
    {
        return $this->belongsTo(TahapLomba::class, 'tahap_lomba_id', 'id');
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembuat_id', 'id');
    }

    public function isDipublikasikan(): bool
    {
        return $this->dipublikasikan_at !== null;
    }

    protected function statusPenilaian(): Attribute
    {
        return Attribute::get(fn () => filled($this->link_penilaian) ? 'sudah' : 'belum');
    }

    protected function statusSertifikat(): Attribute
    {
        return Attribute::get(fn () => filled($this->link_sertifikat) ? 'sudah' : 'belum');
    }

    protected function statusPengumuman(): Attribute
    {
        return Attribute::get(fn () => $this->isDipublikasikan() ? 'diumumkan' : 'belum');
    }
}
