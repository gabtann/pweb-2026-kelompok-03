<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use InvalidArgumentException;

class Tim extends Model
{
    use HasFactory;

    protected $table = 'tim';

    // PERINGATAN: jangan isi dari $request->all().
    // status_verifikasi, verifikator_id, diverifikasi_at hanya boleh diisi lewat verifikasi().
    protected $fillable = [
        'event_id',
        'ketua_id',
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

    protected static function booted(): void
    {
        static::creating(function (Tim $tim) {
            if (empty($tim->kode_tim)) {
                do {
                    $kode = Str::upper(Str::random(6));
                } while (static::where('kode_tim', $kode)->exists());

                $tim->kode_tim = $kode;
            }
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id', 'id');
    }

    public function ketua(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ketua_id', 'id');
    }

    public function anggota(): HasMany
    {
        return $this->hasMany(AnggotaTim::class, 'tim_id', 'id');
    }

    public function pengumpulan(): HasMany
    {
        return $this->hasMany(Pengumpulan::class, 'tim_id', 'id');
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verifikator_id', 'id');
    }

    /** Anggota + ketua. */
    public function jumlahAnggotaTotal(): int
    {
        return $this->anggota()->count() + 1;
    }

    public function verifikasi(User $admin, string $status, ?string $catatan = null): void
    {
        if (! $admin->isPanitia()) {
            throw new InvalidArgumentException('Hanya panitia yang boleh memverifikasi tim.');
        }

        if (! in_array($status, ['disetujui', 'ditolak'], true)) {
            throw new InvalidArgumentException('Status verifikasi harus disetujui atau ditolak.');
        }

        $this->status_verifikasi = $status;
        $this->catatan_verifikasi = $catatan;
        $this->verifikator_id = $admin->id;
        $this->diverifikasi_at = now();
        $this->save();
    }

    public function isDisetujui(): bool
    {
        return $this->status_verifikasi === 'disetujui';
    }

    public function lolosTahap(TahapLomba $tahap): bool
    {
        return $this->pengumpulan()
            ->where('tahap_lomba_id', $tahap->id)
            ->where('status_kelulusan', 'lolos')
            ->exists();
    }

    public function bolehMengumpulkan(TahapLomba $tahap): bool
    {
        if (! $this->isDisetujui() || $tahap->event_id !== $this->event_id) {
            return false;
        }

        if ($tahap->nomor_tahap === 1) {
            return true;
        }

        $sebelumnya = $tahap->tahapSebelumnya();

        return $sebelumnya !== null && $this->lolosTahap($sebelumnya);
    }

    public function scopeFilterJenis(Builder $query, ?string $jenis): Builder
    {
        return $query->when($jenis !== null, fn (Builder $q) => $q->whereHas('event', fn (Builder $e) => $e->where('jenis_lomba', $jenis)));
    }

    public function scopeFilterEvent(Builder $query, ?int $eventId): Builder
    {
        return $query->when($eventId, fn (Builder $q) => $q->where('event_id', $eventId));
    }
}
