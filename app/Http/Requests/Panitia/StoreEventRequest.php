<?php

namespace App\Http\Requests\Panitia;

use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Lapis kedua; route sudah dijaga middleware role:panitia.
        return $this->user()?->isPanitia() === true;
    }

    /**
     * pembuat_id sengaja tidak ada: selalu dari user yang login.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'jenis_lomba' => ['required', Rule::in(array_keys(Event::JENIS_LOMBA))],
            'nama_event' => ['required', 'string', 'max:150'], // kolom DB string(150)
            'tema' => ['required', 'string', 'max:150'],
            'deskripsi' => ['required', 'string'],
            'gambar_pendukung' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'link_guidebook' => ['required', 'url:http,https', 'max:255'], // tolak javascript:, data:, dll.
            'syarat_ketentuan' => ['required', 'string'],
            'registrasi_mulai' => ['required', 'date'],
            'registrasi_selesai' => ['required', 'date', 'after:registrasi_mulai'],
            'waktu_mulai' => ['required', 'date', 'after_or_equal:registrasi_selesai'],
            'waktu_selesai' => ['required', 'date', 'after:waktu_mulai'],
            'biaya_pendaftaran' => ['nullable', 'integer', 'min:0', 'max:99999999'], // kosong = gratis
            'min_anggota' => ['required', 'integer', 'min:1', 'max:255'],
            'max_anggota' => ['required', 'integer', 'gte:min_anggota', 'max:255'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'integer' => ':attribute harus berupa bilangan bulat.',
            'date' => ':attribute bukan tanggal yang valid.',
            'url' => ':attribute harus berupa URL yang valid.',
            'in' => ':attribute tidak valid.',
            'max.string' => ':attribute maksimal :max karakter.',
            'max.numeric' => ':attribute maksimal :max.',
            'max.file' => ':attribute maksimal :max KB.',
            'min.numeric' => ':attribute minimal :min.',
            'mimes' => ':attribute harus berformat :values.',
            'file' => ':attribute harus berupa berkas.',
            'after' => ':attribute harus setelah :date.',
            'after_or_equal' => ':attribute tidak boleh sebelum :date.',
            'gte.numeric' => ':attribute tidak boleh lebih kecil dari :value.',
            'registrasi_selesai.after' => 'Registrasi selesai harus setelah registrasi mulai.',
            'waktu_mulai.after_or_equal' => 'Waktu mulai lomba tidak boleh sebelum registrasi selesai.',
            'waktu_selesai.after' => 'Waktu selesai harus setelah waktu mulai.',
            'max_anggota.gte' => 'Maksimal anggota tidak boleh lebih kecil dari minimal anggota.',
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'jenis_lomba' => 'Jenis lomba',
            'nama_event' => 'Nama event',
            'tema' => 'Tema',
            'deskripsi' => 'Deskripsi',
            'gambar_pendukung' => 'Gambar pendukung',
            'link_guidebook' => 'Link guidebook',
            'syarat_ketentuan' => 'Syarat dan ketentuan',
            'registrasi_mulai' => 'Registrasi mulai',
            'registrasi_selesai' => 'Registrasi selesai',
            'waktu_mulai' => 'Waktu mulai',
            'waktu_selesai' => 'Waktu selesai',
            'biaya_pendaftaran' => 'Biaya pendaftaran',
            'min_anggota' => 'Minimal anggota',
            'max_anggota' => 'Maksimal anggota',
        ];
    }
}
