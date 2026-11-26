<?php

namespace App\Http\Requests\Panitia;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateAkunRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isPanitia() === true;
    }

    /**
     * role tidak bisa diubah lewat form ini. Password opsional (kosong = tidak diganti).
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->route('akun')),
            ],
            'no_telepon' => ['required', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'email' => ':attribute harus berupa email yang valid.',
            'max.string' => ':attribute maksimal :max karakter.',
            'email.unique' => 'Email sudah dipakai akun lain.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal :min karakter.',
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'nama_lengkap' => 'Nama lengkap',
            'email' => 'Email',
            'no_telepon' => 'No. telepon',
            'password' => 'Password',
        ];
    }
}
