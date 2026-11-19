<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendaftaranSeminar extends Model
{
    protected $table = 'pendaftaran_seminar';

    protected $fillable = [
        'event_id',
        'user_id',
        'status',
        'konfirmasi_dikirim_at',
    ];

    protected function casts(): array
    {
        return [
            'konfirmasi_dikirim_at' => 'datetime',
        ];
    }

    public function eventSeminar(): BelongsTo
    {
        return $this->belongsTo(EventSeminar::class, 'event_id', 'event_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
