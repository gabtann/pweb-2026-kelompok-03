<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengumpulan extends Model
{
    use HasFactory;

    protected $table = 'pengumpulan';

    // PERINGATAN: jangan isi dari $request->all().
    // skor, catatan_penilaian, penilai_id, dinilai_at diisi panitia; peringkat dan
    // status_kelulusan HANYA diisi PengumumanService::publikasikan().
    protected $fillable = [
        'tim_id',
        'tahap_lomba_id',
        'file_submission',
        'submitted_at',
        'skor',
        'catatan_penilaian',
        'penilai_id',
        'dinilai_at',
        'peringkat',
        'status_kelulusan',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'dinilai_at' => 'datetime',
            'skor' => 'decimal:2',
            'peringkat' => 'integer',
        ];
    }

    public function tim(): BelongsTo
    {
        return $this->belongsTo(Tim::class, 'tim_id', 'id');
    }

    public function tahapLomba(): BelongsTo
    {
        return $this->belongsTo(TahapLomba::class, 'tahap_lomba_id', 'id');
    }

    public function penilai(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penilai_id', 'id');
    }

    public function isLolos(): bool
    {
        return $this->status_kelulusan === 'lolos';
    }

    public function sudahDinilai(): bool
    {
        return $this->skor !== null;
    }
}
