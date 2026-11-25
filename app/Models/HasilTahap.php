<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HasilTahap extends Model
{
    protected $table = 'hasil_tahap';

    protected $fillable = [
        'tahap_lomba_id',
        'tim_id',
        'skor',
        'status_kelulusan',
        'peringkat',
    ];

    protected function casts(): array
    {
        return [
            'skor' => 'decimal:2',
            'peringkat' => 'integer',
        ];
    }

    public function isLolos(): bool
    {
        return $this->status_kelulusan === 'lolos';
    }

    public function isJuara(): bool
    {
        return $this->peringkat !== null && $this->peringkat <= 3;
    }

    public function tahapLomba(): BelongsTo
    {
        return $this->belongsTo(TahapLomba::class, 'tahap_lomba_id', 'id');
    }

    public function tim(): BelongsTo
    {
        return $this->belongsTo(Tim::class, 'tim_id', 'id');
    }
}
