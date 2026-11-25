<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TahapLomba extends Model
{
    use HasFactory;

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

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id', 'id');
    }

    public function pengumpulan(): HasMany
    {
        return $this->hasMany(Pengumpulan::class, 'tahap_lomba_id', 'id');
    }

    public function pengumuman(): HasOne
    {
        return $this->hasOne(Pengumuman::class, 'tahap_lomba_id', 'id');
    }

    /**
     * Pengumpulan yang lolos di tahap ini, urut peringkat ASC.
     * Kosong jika pengumuman tahap ini belum dipublikasikan.
     *
     * @return Collection<int, Pengumpulan>
     */
    public function peringkatLolos(): Collection
    {
        // Query langsung (bukan relasi ter-cache) supaya tidak basi setelah publish.
        if (! $this->pengumuman()->whereNotNull('dipublikasikan_at')->exists()) {
            return new Collection;
        }

        return $this->pengumpulan()
            ->where('status_kelulusan', 'lolos')
            ->with('tim:id,nama_tim,asal_perguruan_tinggi')
            ->orderBy('peringkat')
            ->get();
    }

    /** Tahap dengan nomor tepat sebelumnya di event yang sama; null jika ini tahap pertama. */
    public function tahapSebelumnya(): ?TahapLomba
    {
        return static::where('event_id', $this->event_id)
            ->where('nomor_tahap', $this->nomor_tahap - 1)
            ->first();
    }

    public function isTahapTerakhir(): bool
    {
        return ! static::where('event_id', $this->event_id)
            ->where('nomor_tahap', '>', $this->nomor_tahap)
            ->exists();
    }
}
