<?php

namespace App\Http\Requests\Panitia;

class UpdateEventRequest extends StoreEventRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            // Saat update, gambar opsional (kosong = pertahankan gambar lama).
            'gambar_pendukung' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
    }
}
