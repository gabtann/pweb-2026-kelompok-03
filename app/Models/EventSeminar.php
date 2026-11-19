<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventSeminar extends Model
{
    use HasFactory;

    protected $table = 'event_seminar';

    protected $primaryKey = 'event_id';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'event_id',
        'penyelenggara',
        'waktu_acara',
        'lokasi',
        'contact_person',
        'kuota_peserta',
    ];

    protected function casts(): array
    {
        return [
            'waktu_acara' => 'datetime',
            'kuota_peserta' => 'integer',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id', 'id');
    }

    public function pendaftaranSeminar(): HasMany
    {
        return $this->hasMany(PendaftaranSeminar::class, 'event_id', 'event_id');
    }
}
