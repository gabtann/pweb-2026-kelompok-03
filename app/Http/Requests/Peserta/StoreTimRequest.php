<?php

namespace App\Http\Requests\Peserta;

use App\Models\Event;
use App\Models\Tim;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTimRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Lapis kedua; route sudah dijaga middleware role:peserta.
        return $this->user()?->isPeserta() === true;
    }

    /** NIM selalu disimpan huruf besar supaya pembandingan konsisten. */
    protected function prepareForValidation(): void
    {
        $nimKetua = $this->input('nim_ketua');
        $anggota = $this->input('anggota');

        $this->merge([
            'nim_ketua' => is_string($nimKetua) ? strtoupper(trim($nimKetua)) : $nimKetua,
            'anggota' => is_array($anggota)
                ? array_map(
                    fn ($a) => is_array($a) && isset($a['nim']) && is_string($a['nim'])
                        ? array_merge($a, ['nim' => strtoupper(trim($a['nim']))])
                        : $a,
                    $anggota,
                )
                : $anggota,
        ]);
    }

    /**
     * ketua_id, event_id, dan seluruh field verifikasi sengaja tidak ada:
     * semuanya ditentukan server, bukan input peserta.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $event = $this->event();
        $tim = $this->tim();

        $nimBebasDiTim = fn () => Rule::unique('tim', 'nim_ketua')->where(
            fn ($q) => $q->where('event_id', $event->id)->when($tim, fn ($q) => $q->where('id', '!=', $tim->id))
        );
        $nimBebasDiAnggota = fn () => Rule::unique('anggota_tim', 'nim')->where(
            fn ($q) => $q->where('event_id', $event->id)->when($tim, fn ($q) => $q->where('tim_id', '!=', $tim->id))
        );

        $berkas = ['file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'];
        $wajib = $this->berkasWajib() ? 'required' : 'nullable';
        $buktiWajib = $this->berkasWajib() && (float) ($event->biaya_pendaftaran ?? 0) > 0 ? 'required' : 'nullable';

        // min/max_anggota di event sudah termasuk ketua.
        $min = max($event->min_anggota - 1, 0);
        $max = max($event->max_anggota - 1, 0);

        return [
            'nama_tim' => ['required', 'string', 'max:100'],
            'asal_perguruan_tinggi' => ['required', 'string', 'max:150'],
            'nim_ketua' => ['required', 'string', 'max:20', 'alpha_num', $nimBebasDiTim(), $nimBebasDiAnggota()],
            'file_ktm' => [$wajib, ...$berkas],
            'file_pakta' => [$wajib, ...$berkas],
            'file_bukti_bayar' => [$buktiWajib, ...$berkas],
            'anggota' => [$min > 0 ? 'required' : 'nullable', 'array', "min:{$min}", "max:{$max}"],
            'anggota.*.nama_lengkap' => ['required', 'string', 'max:100'],
            'anggota.*.nim' => [
                'required', 'string', 'max:20', 'alpha_num', 'distinct', 'different:nim_ketua',
                $nimBebasDiTim(), $nimBebasDiAnggota(),
            ],
        ];
    }

    protected function event(): Event
    {
        return $this->route('event');
    }

    protected function tim(): ?Tim
    {
        return null;
    }

    /** Saat pendaftaran semua berkas wajib; saat ubah data, berkas boleh dikosongkan. */
    protected function berkasWajib(): bool
    {
        return true;
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'max.string' => ':attribute maksimal :max karakter.',
            'alpha_num' => ':attribute hanya boleh berisi huruf dan angka.',
            'file' => ':attribute harus berupa berkas.',
            'mimes' => ':attribute harus berformat :values.',
            'max.file' => ':attribute maksimal :max KB.',
            'array' => ':attribute tidak valid.',
            'distinct' => 'NIM anggota tidak boleh sama dengan NIM lain di pendaftaran ini.',
            'different' => 'NIM anggota tidak boleh sama dengan NIM ketua.',
            'nim_ketua.unique' => 'NIM ini sudah terdaftar pada event ini.',
            'anggota.*.nim.unique' => 'NIM ini sudah terdaftar pada event ini.',
            'anggota.required' => 'Anggota tim wajib diisi.',
            'anggota.min' => 'Jumlah anggota di luar ketua minimal :min orang.',
            'anggota.max' => 'Jumlah anggota di luar ketua maksimal :max orang.',
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'nama_tim' => 'Nama tim',
            'asal_perguruan_tinggi' => 'Asal perguruan tinggi',
            'nim_ketua' => 'NIM ketua',
            'file_ktm' => 'KTM',
            'file_pakta' => 'Pakta integritas',
            'file_bukti_bayar' => 'Bukti pembayaran',
            'anggota' => 'Anggota',
            'anggota.*.nama_lengkap' => 'Nama anggota',
            'anggota.*.nim' => 'NIM anggota',
        ];
    }
}