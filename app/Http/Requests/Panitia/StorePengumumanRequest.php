<?php

namespace App\Http\Requests\Panitia;

use Illuminate\Foundation\Http\FormRequest;

class StorePengumumanRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Lapis kedua; route sudah dijaga middleware role:panitia.
        return $this->user()?->isPanitia() === true;
    }

    /**
     * pembuat_id dan dipublikasikan_at sengaja tidak ada:
     * pembuat dari user login, status publikasi hanya lewat PengumumanService.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'tahap_lomba_id' => ['required', 'integer', 'exists:tahap_lomba,id', 'unique:pengumuman,tahap_lomba_id'],
            'link_penilaian' => ['nullable', 'url:http,https', 'max:255'],
            'link_sertifikat' => ['nullable', 'url:http,https', 'max:255'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'integer' => ':attribute tidak valid.',
            'exists' => ':attribute tidak ditemukan.',
            'url' => ':attribute harus berupa URL http atau https yang valid.',
            'max.string' => ':attribute maksimal :max karakter.',
            'tahap_lomba_id.unique' => 'Tahap ini sudah memiliki pengumuman.',
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'tahap_lomba_id' => 'Tahap lomba',
            'link_penilaian' => 'Link penilaian',
            'link_sertifikat' => 'Link sertifikat',
        ];
    }
}