<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventLomba extends Model
{
    protected $table = 'event_lomba';

    protected $primaryKey = 'event_id';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'event_id',
        'link_guidebook',
        'syarat_ketentuan',
        'waktu_mulai',
        'waktu_selesai',
        'biaya_pendaftaran',
        'min_anggota',
        'max_anggota',
    ];

    protected function casts(): array
    {
        return [
            'waktu_mulai' => 'datetime',
            'waktu_selesai' => 'datetime',
            'biaya_pendaftaran' => 'decimal:2',
            'min_anggota' => 'integer',
            'max_anggota' => 'integer',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id', 'id');
    }

    public function tahapLomba(): HasMany
    {
        return $this->hasMany(TahapLomba::class, 'event_id', 'event_id');
    }

    public function tim(): HasMany
    {
        return $this->hasMany(Tim::class, 'event_id', 'event_id');
    }

    public function anggotaTim(): HasMany
    {
        return $this->hasMany(AnggotaTim::class, 'event_id', 'event_id');
    }
}
