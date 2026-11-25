<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Anggota SELAIN ketua. Ketua ada di Tim (ketua_id, nim_ketua).
 */
class AnggotaTim extends Model
{
    use HasFactory;

    protected $table = 'anggota_tim';

    protected $fillable = [
        'tim_id',
        'event_id',
        'nama_lengkap',
        'nim',
    ];

    public function tim(): BelongsTo
    {
        return $this->belongsTo(Tim::class, 'tim_id', 'id');
    }
}
