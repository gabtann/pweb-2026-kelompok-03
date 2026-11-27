<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Http\Requests\Peserta\StoreTimRequest;
use App\Http\Requests\Peserta\UpdateTimRequest;
use App\Models\AnggotaTim;
use App\Models\Event;
use App\Models\Tim;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class TimController extends Controller
{
    /** Berkas pribadi disimpan di disk privat (bukan public) dan diakses lewat route berkas.tim. */
    public const DISK = 'local';

    private const BERKAS = ['file_ktm', 'file_pakta', 'file_bukti_bayar'];

    /** Halaman "Event diikuti": tim milik peserta yang login. */
    public function index(Request $request): Response
    {
        $tim = Tim::query()
            ->where('ketua_id', $request->user()->id)
            ->with(['event', 'anggota'])
            ->latest('id')
            ->paginate(10)
            ->through(fn (Tim $t) => [
                'id' => $t->id,
                'kode_tim' => $t->kode_tim,
                'nama_tim' => $t->nama_tim,
                'asal_perguruan_tinggi' => $t->asal_perguruan_tinggi,
                'nim_ketua' => $t->nim_ketua,
                'status_verifikasi' => $t->status_verifikasi,
                'catatan_verifikasi' => $t->catatan_verifikasi,
                'jumlah_anggota_total' => $t->anggota->count() + 1,
                'anggota' => $this->daftarAnggota($t),
                'berkas' => $this->tautanBerkas($t),
                'dapat_diubah' => $this->dapatDiubah($t),
                'event' => [
                    'id' => $t->event->id,
                    'nama_event' => $t->event->nama_event,
                    'jenis_lomba' => $t->event->jenis_lomba,
                    'label_jenis_lomba' => $t->event->label_jenis_lomba,
                    'waktu_mulai' => $t->event->waktu_mulai->toIso8601String(),
                    'waktu_selesai' => $t->event->waktu_selesai->toIso8601String(),
                ],
            ]);

        return Inertia::render('Peserta/Tim/Index', ['tim' => $tim]);
    }

    public function create(Request $request, Event $event): Response|RedirectResponse
    {
        if ($tolak = $this->tolakDaftar($request->user(), $event)) {
            return $tolak;
        }

        return Inertia::render('Peserta/Tim/Create', [
            'event' => $this->ringkasEvent($event),
        ]);
    }

    public function store(StoreTimRequest $request, Event $event): RedirectResponse
    {
        if ($tolak = $this->tolakDaftar($request->user(), $event)) {
            return $tolak;
        }

        $data = $request->validated();
        $anggota = array_values($data['anggota'] ?? []);
        $tersimpan = [];

        try {
            DB::transaction(function () use ($request, $event, $data, $anggota, &$tersimpan) {
                $this->kunciEvent($event);
                $this->pastikanTidakBentrok(
                    $event,
                    $data['nim_ketua'],
                    array_column($anggota, 'nim'),
                    $request->user(),
                );

                $berkas = [];
                foreach (self::BERKAS as $kolom) {
                    if ($request->hasFile($kolom)) {
                        $berkas[$kolom] = $request->file($kolom)->store("tim/{$event->id}", self::DISK);
                        $tersimpan[] = $berkas[$kolom];
                    }
                }

                // Atribut ditulis eksplisit: status_verifikasi dan kawan-kawannya TIDAK boleh dari request.
                $tim = Tim::create([
                    'event_id' => $event->id,
                    'ketua_id' => $request->user()->id,
                    'nama_tim' => $data['nama_tim'],
                    'asal_perguruan_tinggi' => $data['asal_perguruan_tinggi'],
                    'nim_ketua' => $data['nim_ketua'],
                ] + $berkas);

                $this->simpanAnggota($tim, $anggota);
            });
        } catch (Throwable $e) {
            Storage::disk(self::DISK)->delete($tersimpan);

            throw $e;
        }

        return redirect()->route('peserta.tim.index')
            ->with('success', 'Pendaftaran berhasil. Menunggu verifikasi panitia.');
    }

    public function edit(Request $request, Tim $tim): Response|RedirectResponse
    {
        $this->pastikanMilikSendiri($request, $tim);

        if (! $this->dapatDiubah($tim)) {
            return redirect()->route('peserta.tim.index')
                ->with('error', 'Tim tidak bisa diubah karena sudah diverifikasi atau pendaftaran sudah ditutup.');
        }

        $tim->load('anggota');

        return Inertia::render('Peserta/Tim/Edit', [
            'tim' => [
                'id' => $tim->id,
                'kode_tim' => $tim->kode_tim,
                'nama_tim' => $tim->nama_tim,
                'asal_perguruan_tinggi' => $tim->asal_perguruan_tinggi,
                'nim_ketua' => $tim->nim_ketua,
                'anggota' => $this->daftarAnggota($tim),
                'berkas' => $this->tautanBerkas($tim),
            ],
            'event' => $this->ringkasEvent($tim->event),
        ]);
    }

    public function update(UpdateTimRequest $request, Tim $tim): RedirectResponse
    {
        if (! $this->dapatDiubah($tim)) {
            return back()->with('error', 'Tim tidak bisa diubah karena sudah diverifikasi atau pendaftaran sudah ditutup.');
        }

        $data = $request->validated();
        $anggota = array_values($data['anggota'] ?? []);
        $event = $tim->event;
        $baru = [];
        $lama = [];

        try {
            DB::transaction(function () use ($request, $tim, $event, $data, $anggota, &$baru, &$lama) {
                $this->kunciEvent($event);
                $this->pastikanTidakBentrok($event, $data['nim_ketua'], array_column($anggota, 'nim'), null, $tim);

                foreach (self::BERKAS as $kolom) {
                    if ($request->hasFile($kolom)) {
                        $path = $request->file($kolom)->store("tim/{$event->id}", self::DISK);
                        $baru[] = $path;

                        if ($tim->{$kolom}) {
                            $lama[] = $tim->{$kolom};
                        }

                        $tim->{$kolom} = $path;
                    }
                }

                $tim->nama_tim = $data['nama_tim'];
                $tim->asal_perguruan_tinggi = $data['asal_perguruan_tinggi'];
                $tim->nim_ketua = $data['nim_ketua'];
                $tim->save();

                $tim->anggota()->delete();
                $this->simpanAnggota($tim, $anggota);
            });
        } catch (Throwable $e) {
            Storage::disk(self::DISK)->delete($baru);

            throw $e;
        }

        // Berkas lama dihapus setelah transaksi berhasil.
        Storage::disk(self::DISK)->delete($lama);

        return redirect()->route('peserta.tim.index')->with('success', 'Data tim berhasil diperbarui.');
    }

    public function destroy(Request $request, Tim $tim): RedirectResponse
    {
        $this->pastikanMilikSendiri($request, $tim);

        if (! $this->dapatDiubah($tim)) {
            return back()->with('error', 'Pendaftaran tidak bisa dibatalkan karena sudah diverifikasi atau sudah ditutup.');
        }

        $berkas = array_filter([$tim->file_ktm, $tim->file_pakta, $tim->file_bukti_bayar]);

        $tim->delete(); // anggota ikut terhapus (cascade di database)

        Storage::disk(self::DISK)->delete($berkas);

        return redirect()->route('peserta.tim.index')->with('success', 'Pendaftaran dibatalkan.');
    }

    private function tolakDaftar(User $user, Event $event): ?RedirectResponse
    {
        $sekarang = now();

        if ($sekarang->lt($event->registrasi_mulai) || $sekarang->gt($event->registrasi_selesai)) {
            return redirect()->route('peserta.event.index')
                ->with('error', 'Pendaftaran event ini belum dibuka atau sudah ditutup.');
        }

        if ($event->tim()->where('ketua_id', $user->id)->exists()) {
            return redirect()->route('peserta.tim.index')
                ->with('error', 'Kamu sudah mendaftar pada event ini.');
        }

        return null;
    }

    /** Pendaftaran bersamaan untuk event yang sama diserialisasi lewat kunci baris event. */
    private function kunciEvent(Event $event): void
    {
        Event::query()->whereKey($event->id)->lockForUpdate()->first();
    }

    /**
     * NIM ketua (tabel tim) dan NIM anggota (tabel anggota_tim) tidak bisa dijaga satu
     * constraint database, jadi dicek ulang di dalam transaksi setelah event dikunci.
     *
     * @param  list<string>  $nimAnggota
     */
    private function pastikanTidakBentrok(Event $event, string $nimKetua, array $nimAnggota, ?User $ketua = null, ?Tim $kecuali = null): void
    {
        if ($ketua && Tim::query()->where('event_id', $event->id)->where('ketua_id', $ketua->id)->exists()) {
            throw ValidationException::withMessages(['event' => 'Kamu sudah mendaftar pada event ini.']);
        }

        $semua = array_merge([$nimKetua], $nimAnggota);

        $bentrokTim = Tim::query()
            ->where('event_id', $event->id)
            ->whereIn('nim_ketua', $semua)
            ->when($kecuali, fn ($q) => $q->whereKeyNot($kecuali->id))
            ->exists();

        $bentrokAnggota = AnggotaTim::query()
            ->where('event_id', $event->id)
            ->whereIn('nim', $semua)
            ->when($kecuali, fn ($q) => $q->where('tim_id', '!=', $kecuali->id))
            ->exists();

        if ($bentrokTim || $bentrokAnggota) {
            throw ValidationException::withMessages([
                'nim_ketua' => 'Ada NIM yang sudah terdaftar pada event ini.',
            ]);
        }
    }

    /** @param  list<array{nama_lengkap: string, nim: string}>  $anggota */
    private function simpanAnggota(Tim $tim, array $anggota): void
    {
        if ($anggota === []) {
            return;
        }

        $tim->anggota()->createMany(array_map(fn (array $a) => [
            'event_id' => $tim->event_id, // dibutuhkan composite FK (tim_id, event_id)
            'nama_lengkap' => $a['nama_lengkap'],
            'nim' => $a['nim'],
        ], $anggota));
    }

    private function pastikanMilikSendiri(Request $request, Tim $tim): void
    {
        abort_unless((int) $tim->ketua_id === (int) $request->user()->id, 403);
    }

    /** Boleh diubah atau dibatalkan selama belum diverifikasi dan registrasi masih dibuka. */
    private function dapatDiubah(Tim $tim): bool
    {
        return $tim->status_verifikasi === 'menunggu'
            && $tim->event->registrasi_selesai->isFuture();
    }

    /** @return list<array{nama_lengkap: string, nim: string}> */
    private function daftarAnggota(Tim $tim): array
    {
        return $tim->anggota
            ->map(fn (AnggotaTim $a) => ['nama_lengkap' => $a->nama_lengkap, 'nim' => $a->nim])
            ->values()
            ->all();
    }

    /** @return array{ktm: string, pakta: string, bukti_bayar: ?string} */
    private function tautanBerkas(Tim $tim): array
    {
        return [
            'ktm' => route('berkas.tim', ['tim' => $tim->id, 'jenis' => 'ktm']),
            'pakta' => route('berkas.tim', ['tim' => $tim->id, 'jenis' => 'pakta']),
            'bukti_bayar' => $tim->file_bukti_bayar
                ? route('berkas.tim', ['tim' => $tim->id, 'jenis' => 'bukti_bayar'])
                : null,
        ];
    }

    /** @return array<string, mixed> */
    private function ringkasEvent(Event $event): array
    {
        return [
            'id' => $event->id,
            'nama_event' => $event->nama_event,
            'jenis_lomba' => $event->jenis_lomba,
            'label_jenis_lomba' => $event->label_jenis_lomba,
            'registrasi_mulai' => $event->registrasi_mulai->toIso8601String(),
            'registrasi_selesai' => $event->registrasi_selesai->toIso8601String(),
            'biaya_pendaftaran' => $event->biaya_pendaftaran !== null ? (int) $event->biaya_pendaftaran : null,
            'perlu_bukti_bayar' => (float) ($event->biaya_pendaftaran ?? 0) > 0,
            'min_anggota' => $event->min_anggota,
            'max_anggota' => $event->max_anggota,
            // min/max_anggota sudah termasuk ketua; dua angka ini adalah jumlah anggota di luar ketua.
            'min_anggota_tambahan' => max($event->min_anggota - 1, 0),
            'max_anggota_tambahan' => max($event->max_anggota - 1, 0),
        ];
    }
}