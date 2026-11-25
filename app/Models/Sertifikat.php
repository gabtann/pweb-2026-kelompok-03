<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sertifikat extends Model
{
    protected $table = 'sertifikat';

    protected $fillable = [
        'event_id',
        'user_id',
        'tim_id',
        'link_gdrive',
        'pengunggah_id',
        'diunggah_at',
    ];

    protected function casts(): array
    {
        return [
            'diunggah_at' => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function tim(): BelongsTo
    {
        return $this->belongsTo(Tim::class, 'tim_id', 'id');
    }

    public function pengunggah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengunggah_id', 'id');
    }
}
