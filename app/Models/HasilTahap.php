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
        'lolos',
        'peringkat',
        'gelar_juara',
    ];

    protected function casts(): array
    {
        return [
            'lolos' => 'boolean',
            'peringkat' => 'integer',
        ];
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
