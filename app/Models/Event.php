<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    /** Urutan array ini = urutan tampil. */
    public const JENIS_LOMBA = [
        'uiux' => 'UI/UX',
        'ict' => 'ICT',
        'software_development' => 'Software Development',
    ];

    protected $table = 'event';

    protected $fillable = [
        'pembuat_id',
        'jenis_lomba',
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

    protected function labelJenisLomba(): Attribute
    {
        return Attribute::get(fn () => self::JENIS_LOMBA[$this->jenis_lomba] ?? null);
    }

    public function scopeJenis(Builder $query, ?string $jenis): Builder
    {
        return $query->when($jenis !== null, fn (Builder $q) => $q->where($q->getModel()->getTable().'.jenis_lomba', $jenis));
    }

    /** Urut sesuai urutan JENIS_LOMBA, lalu nama_event. Pakai CASE WHEN (portabel MySQL dan sqlite). */
    public function scopeUrutJenis(Builder $query): Builder
    {
        $kunci = array_keys(self::JENIS_LOMBA);
        $kolom = $query->getModel()->getTable().'.jenis_lomba';

        $case = 'CASE '.$kolom.str_repeat(' WHEN ? THEN ?', count($kunci)).' ELSE '.count($kunci).' END';
        $bindings = [];
        foreach ($kunci as $i => $k) {
            $bindings[] = $k;
            $bindings[] = $i;
        }

        return $query->orderByRaw($case, $bindings)->orderBy($query->getModel()->getTable().'.nama_event');
    }

    /**
     * Juara per jenis lomba, dari event paling baru (waktu_mulai terbaru) tiap jenis.
     * Semua jenis selalu ada sebagai kunci; Collection kosong = belum ada pengumuman.
     *
     * @return array<string, Collection<int, Pengumpulan>>
     */
    public static function juaraPerJenis(): array
    {
        $hasil = [];

        foreach (array_keys(self::JENIS_LOMBA) as $jenis) {
            $event = static::jenis($jenis)->orderByDesc('waktu_mulai')->first();

            $hasil[$jenis] = $event ? $event->juara() : new Collection;
        }

        return $hasil;
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
