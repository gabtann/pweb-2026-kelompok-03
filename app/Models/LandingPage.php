<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandingPage extends Model
{
    protected $table = 'landing_page';

    protected $fillable = [
        'jenis',
        'judul',
        'isi',
        'urutan',
        'pengubah_id',
    ];

    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
        ];
    }

    public function pengubah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengubah_id', 'id');
    }
}
