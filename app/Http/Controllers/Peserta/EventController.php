<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Tim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class EventController extends Controller
{
    public function index(Request $request): Response
    {
        $jenis = $request->query('jenis');
        $jenis = is_string($jenis) && array_key_exists($jenis, Event::JENIS_LOMBA) ? $jenis : null;

        $diikuti = Tim::query()
            ->where('ketua_id', $request->user()->id)
            ->pluck('event_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $sekarang = now();

        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk('public');

        $events = Event::query()
            ->jenis($jenis)
            ->urutJenis()
            ->orderBy('event.id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Event $event) => [
                'id' => $event->id,
                'nama_event' => $event->nama_event,
                'jenis_lomba' => $event->jenis_lomba,
                'label_jenis_lomba' => $event->label_jenis_lomba,
                'tema' => $event->tema,
                'gambar_url' => $event->gambar_pendukung
                    ? $storage->url($event->gambar_pendukung)
                    : null,
                'registrasi_mulai' => $event->registrasi_mulai->toIso8601String(),
                'registrasi_selesai' => $event->registrasi_selesai->toIso8601String(),
                'waktu_mulai' => $event->waktu_mulai->toIso8601String(),
                'waktu_selesai' => $event->waktu_selesai->toIso8601String(),
                'biaya_pendaftaran' => $event->biaya_pendaftaran !== null ? (int) $event->biaya_pendaftaran : null,
                'min_anggota' => $event->min_anggota,
                'max_anggota' => $event->max_anggota,
                'registrasi_dibuka' => $sekarang->between($event->registrasi_mulai, $event->registrasi_selesai),
                'sudah_terdaftar' => in_array($event->id, $diikuti, true),
            ]);

        return Inertia::render('Peserta/Event/Index', [
            'events' => $events,
            'filters' => ['jenis' => $jenis],
            'jenisLomba' => collect(Event::JENIS_LOMBA)
                ->map(fn (string $label, string $value) => ['value' => $value, 'label' => $label])
                ->values()
                ->all(),
        ]);
    }
}