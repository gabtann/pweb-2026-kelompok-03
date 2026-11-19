<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengumpulan extends Model
{
    protected $table = 'pengumpulan';

    protected $fillable = [
        'tim_id',
        'tahap_lomba_id',
        'pengumpul_id',
        'file_submission',
        'submitted_at',
        'status_validasi',
        'skor',
        'catatan_penilaian',
        'penilai_id',
        'dinilai_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'dinilai_at' => 'datetime',
            'skor' => 'decimal:2',
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

    public function pengumpul(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengumpul_id', 'id');
    }

    public function penilai(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penilai_id', 'id');
    }
}
