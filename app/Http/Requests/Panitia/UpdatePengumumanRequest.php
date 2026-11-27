<?php

namespace App\Http\Requests\Panitia;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePengumumanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isPanitia() === true;
    }

    /**
     * Hanya link yang bisa diubah: tahap lomba tetap, dan status publikasi hanya lewat PengumumanService.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'link_penilaian' => ['nullable', 'url:http,https', 'max:255'],
            'link_sertifikat' => ['nullable', 'url:http,https', 'max:255'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'url' => ':attribute harus berupa URL http atau https yang valid.',
            'max.string' => ':attribute maksimal :max karakter.',
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'link_penilaian' => 'Link penilaian',
            'link_sertifikat' => 'Link sertifikat',
        ];
    }
}