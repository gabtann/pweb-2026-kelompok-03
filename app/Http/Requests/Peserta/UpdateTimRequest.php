<?php

namespace App\Http\Requests\Peserta;

use App\Models\Event;
use App\Models\Tim;

/**
 * Aturan sama dengan pendaftaran, bedanya berkas opsional (kosong = pakai berkas lama)
 * dan NIM milik tim ini sendiri tidak dihitung sebagai duplikat.
 */
class UpdateTimRequest extends StoreTimRequest
{
    public function authorize(): bool
    {
        return parent::authorize()
            && (int) $this->tim()->ketua_id === (int) $this->user()->id;
    }

    protected function event(): Event
    {
        return $this->tim()->event;
    }

    protected function tim(): ?Tim
    {
        return $this->route('tim');
    }

    protected function berkasWajib(): bool
    {
        return false;
    }
}