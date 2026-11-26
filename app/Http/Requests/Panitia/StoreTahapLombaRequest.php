<?php

namespace App\Http\Requests\Panitia;

use App\Models\Event;
use App\Models\TahapLomba;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTahapLombaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isPanitia() === true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        /** @var Event $event */
        $event = $this->route('event');

        /** @var TahapLomba|null $tahap */
        $tahap = $this->route('tahap');

        return [
            'nomor_tahap' => [
                'required', 'integer', 'min:1', 'max:255',
                Rule::unique('tahap_lomba', 'nomor_tahap')
                    ->where('event_id', $event->id)
                    ->ignore($tahap?->id),
            ],
            'nama_tahap' => ['required', 'string', 'max:50'],
            'jenis_berkas' => ['required', Rule::in(['pdf', 'pptx'])],
            'batas_mulai' => ['required', 'date'],
            'batas_akhir' => ['required', 'date', 'after:batas_mulai'],
            'kuota_lolos' => ['required', 'integer', 'min:1'],
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
            'in' => ':attribute tidak valid.',
            'max.string' => ':attribute maksimal :max karakter.',
            'max.numeric' => ':attribute maksimal :max.',
            'min.numeric' => ':attribute minimal :min.',
            'unique' => ':attribute sudah dipakai di event ini.',
            'batas_akhir.after' => 'Batas akhir harus setelah batas mulai.',
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'nomor_tahap' => 'Nomor tahap',
            'nama_tahap' => 'Nama tahap',
            'jenis_berkas' => 'Jenis berkas',
            'batas_mulai' => 'Batas mulai',
            'batas_akhir' => 'Batas akhir',
            'kuota_lolos' => 'Kuota lolos',
        ];
    }
}
