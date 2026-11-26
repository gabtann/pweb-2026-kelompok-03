<?php

namespace App\Http\Controllers\Panitia;

use App\Http\Controllers\Controller;
use App\Http\Requests\Panitia\StoreEventRequest;
use App\Http\Requests\Panitia\UpdateEventRequest;
use App\Models\Event;
use App\Models\TahapLomba;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class EventController extends Controller
{
    private const DISK = 'public';

    private const FORMAT_INPUT = 'Y-m-d\TH:i'; // format <input type="datetime-local">

    public function index(Request $request): Response
    {
        $jenis = $request->query('jenis');
        $jenis = is_string($jenis) && array_key_exists($jenis, Event::JENIS_LOMBA) ? $jenis : null;

        $events = Event::query()
            ->jenis($jenis)
            ->withCount('tim')
            ->urutJenis()
            ->orderBy('event.id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Event $event) => [
                'id' => $event->id,
                'nama_event' => $event->nama_event,
                'jenis_lomba' => $event->jenis_lomba,
                'label_jenis_lomba' => $event->label_jenis_lomba,
                'registrasi_mulai' => $event->registrasi_mulai->toIso8601String(),
                'registrasi_selesai' => $event->registrasi_selesai->toIso8601String(),
                'biaya_pendaftaran' => $this->biaya($event),
                'jumlah_tim' => $event->tim_count,
            ]);

        return Inertia::render('Panitia/Event/Index', [
            'events' => $events,
            'filters' => ['jenis' => $jenis],
            'jenisLomba' => $this->opsiJenisLomba(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Panitia/Event/Create', [
            'jenisLomba' => $this->opsiJenisLomba(),
        ]);
    }

    public function store(StoreEventRequest $request): RedirectResponse
    {
        // pembuat_id TIDAK dari input: relasi eventDibuat() mengisinya dari user login.
        $data = Arr::except($request->validated(), ['gambar_pendukung']);
        $data['gambar_pendukung'] = $request->file('gambar_pendukung')->store('event', self::DISK);

        try {
            $request->user()->eventDibuat()->create($data);
        } catch (Throwable $e) {
            Storage::disk(self::DISK)->delete($data['gambar_pendukung']);

            throw $e;
        }

        return redirect()->route('panitia.event.index')->with('success', 'Event berhasil ditambahkan.');
    }

    public function show(Event $event): Response
    {
        $tahap = $event->tahapLomba()
            ->withCount('pengumpulan')
            ->with('pengumuman')
            ->get()
            ->map(fn (TahapLomba $t) => [
                'id' => $t->id,
                'nomor_tahap' => $t->nomor_tahap,
                'nama_tahap' => $t->nama_tahap,
                'jenis_berkas' => $t->jenis_berkas,
                'batas_mulai' => $t->batas_mulai->toIso8601String(),
                'batas_akhir' => $t->batas_akhir->toIso8601String(),
                'kuota_lolos' => $t->kuota_lolos,
                'jumlah_pengumpulan' => $t->pengumpulan_count,
                'sudah_diumumkan' => $t->pengumuman?->isDipublikasikan() ?? false,
            ])
            ->values();

        return Inertia::render('Panitia/Event/Show', [
            'event' => [
                'id' => $event->id,
                'nama_event' => $event->nama_event,
                'jenis_lomba' => $event->jenis_lomba,
                'label_jenis_lomba' => $event->label_jenis_lomba,
                'tema' => $event->tema,
                'deskripsi' => $event->deskripsi,
                'gambar_url' => $this->gambarUrl($event),
                'link_guidebook' => $event->link_guidebook,
                'syarat_ketentuan' => $event->syarat_ketentuan,
                'registrasi_mulai' => $event->registrasi_mulai->toIso8601String(),
                'registrasi_selesai' => $event->registrasi_selesai->toIso8601String(),
                'waktu_mulai' => $event->waktu_mulai->toIso8601String(),
                'waktu_selesai' => $event->waktu_selesai->toIso8601String(),
                'biaya_pendaftaran' => $this->biaya($event),
                'min_anggota' => $event->min_anggota,
                'max_anggota' => $event->max_anggota,
                'jumlah_tim' => $event->tim()->count(),
            ],
            'tahap' => $tahap,
        ]);
    }

    public function edit(Event $event): Response
    {
        return Inertia::render('Panitia/Event/Edit', [
            'event' => [
                'id' => $event->id,
                'jenis_lomba' => $event->jenis_lomba,
                'nama_event' => $event->nama_event,
                'tema' => $event->tema,
                'deskripsi' => $event->deskripsi,
                'gambar_url' => $this->gambarUrl($event),
                'link_guidebook' => $event->link_guidebook,
                'syarat_ketentuan' => $event->syarat_ketentuan,
                'registrasi_mulai' => $event->registrasi_mulai->format(self::FORMAT_INPUT),
                'registrasi_selesai' => $event->registrasi_selesai->format(self::FORMAT_INPUT),
                'waktu_mulai' => $event->waktu_mulai->format(self::FORMAT_INPUT),
                'waktu_selesai' => $event->waktu_selesai->format(self::FORMAT_INPUT),
                'biaya_pendaftaran' => $this->biaya($event),
                'min_anggota' => $event->min_anggota,
                'max_anggota' => $event->max_anggota,
            ],
            'jenisLomba' => $this->opsiJenisLomba(),
        ]);
    }

    public function update(UpdateEventRequest $request, Event $event): RedirectResponse
    {
        $data = Arr::except($request->validated(), ['gambar_pendukung']);
        $gambarLama = $event->gambar_pendukung;
        $gambarBaru = null;

        if ($request->hasFile('gambar_pendukung')) {
            $gambarBaru = $request->file('gambar_pendukung')->store('event', self::DISK);
            $data['gambar_pendukung'] = $gambarBaru;
        }

        try {
            $event->update($data);
        } catch (Throwable $e) {
            if ($gambarBaru) {
                Storage::disk(self::DISK)->delete($gambarBaru);
            }

            throw $e;
        }

        // File lama dihapus setelah update berhasil.
        if ($gambarBaru && $gambarLama) {
            Storage::disk(self::DISK)->delete($gambarLama);
        }

        return redirect()->route('panitia.event.index')->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        if ($event->tim()->exists()) {
            return back()->with('error', 'Event tidak bisa dihapus karena sudah memiliki tim terdaftar.');
        }

        $event->delete(); // soft delete

        return redirect()->route('panitia.event.index')->with('success', 'Event berhasil dihapus.');
    }

    /** @return list<array{value: string, label: string}> */
    private function opsiJenisLomba(): array
    {
        return collect(Event::JENIS_LOMBA)
            ->map(fn (string $label, string $value) => ['value' => $value, 'label' => $label])
            ->values()
            ->all();
    }

    private function biaya(Event $event): ?int
    {
        return $event->biaya_pendaftaran !== null ? (int) $event->biaya_pendaftaran : null;
    }

    private function gambarUrl(Event $event): ?string
    {
        return $event->gambar_pendukung
            ? Storage::disk(self::DISK)->url($event->gambar_pendukung)
            : null;
    }
}
