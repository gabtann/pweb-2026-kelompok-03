<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'event';

    protected $fillable = [
        'pembuat_id',
        'nama_event',
        'tema',
        'deskripsi',
        'gambar_pendukung',
        'link_guidebook',
        'syarat_ketentuan',
        'registrasi_mulai',
        'registrasi_selesai',
        'waktu_mulai',
        'waktu_selesai',
        'biaya_pendaftaran',
        'min_anggota',
        'max_anggota',
    ];

    protected function casts(): array
    {
        return [
            'registrasi_mulai' => 'datetime',
            'registrasi_selesai' => 'datetime',
            'waktu_mulai' => 'datetime',
            'waktu_selesai' => 'datetime',
            'biaya_pendaftaran' => 'decimal:2',
            'min_anggota' => 'integer',
            'max_anggota' => 'integer',
        ];
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembuat_id', 'id');
    }

    public function tahapLomba(): HasMany
    {
        return $this->hasMany(TahapLomba::class, 'event_id', 'id')->orderBy('nomor_tahap');
    }

    public function tim(): HasMany
    {
        return $this->hasMany(Tim::class, 'event_id', 'id');
    }

    /** Tahap dengan nomor_tahap tertinggi. */
    public function tahapTerakhir(): HasOne
    {
        return $this->hasOne(TahapLomba::class, 'event_id', 'id')->ofMany('nomor_tahap', 'max');
    }

    /**
     * Top 3 = peringkat 1-3 di tahap terakhir, hanya jika pengumumannya sudah dipublikasikan.
     *
     * @return Collection<int, Pengumpulan>
     */
    public function juara(): Collection
    {
        $tahap = $this->tahapTerakhir;

        if (! $tahap || ! $tahap->pengumuman?->isDipublikasikan()) {
            return new Collection;
        }

        return Pengumpulan::with('tim')
            ->where('tahap_lomba_id', $tahap->id)
            ->whereBetween('peringkat', [1, 3])
            ->orderBy('peringkat')
            ->get();
    }
}
