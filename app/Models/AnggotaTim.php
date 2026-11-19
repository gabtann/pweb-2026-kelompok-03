<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnggotaTim extends Model
{
    use HasFactory;

    protected $table = 'anggota_tim';

    // ketua_tim adalah kolom generated (read-only), sengaja tidak di fillable.
    protected $fillable = [
        'tim_id',
        'event_id',
        'user_id',
        'peran',
        'joined_at',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
        ];
    }

    public function tim(): BelongsTo
    {
        return $this->belongsTo(Tim::class, 'tim_id', 'id');
    }

    public function eventLomba(): BelongsTo
    {
        return $this->belongsTo(EventLomba::class, 'event_id', 'event_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
