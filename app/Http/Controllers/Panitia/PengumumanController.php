<?php

namespace App\Http\Controllers\Panitia;

use App\Http\Controllers\Controller;
use App\Http\Requests\Panitia\StorePengumumanRequest;
use App\Http\Requests\Panitia\UpdatePengumumanRequest;
use App\Models\Event;
use App\Models\Pengumuman;
use App\Models\TahapLomba;
use App\Services\PengumumanService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PengumumanController extends Controller
{
    private const STATUS = ['draft', 'diumumkan'];

    public function index(Request $request): Response
    {
        $status = $request->query('status');
        $status = is_string($status) && in_array($status, self::STATUS, true) ? $status : null;

        $eventId = $request->query('event_id');
        $eventId = is_numeric($eventId) ? (int) $eventId : null;

        $pengumuman = Pengumuman::query()
            ->with(['tahapLomba' => fn ($q) => $q->withCount('pengumpulan')->with('event')])
            ->when($status === 'diumumkan', fn ($q) => $q->whereNotNull('dipublikasikan_at'))
            ->when($status === 'draft', fn ($q) => $q->whereNull('dipublikasikan_at'))
            ->when($eventId, fn ($q) => $q->whereHas('tahapLomba', fn ($t) => $t->where('event_id', $eventId)))
            ->orderByDesc('pengumuman.id')
            ->paginate(10)
            ->withQueryString()
            ->through(function (Pengumuman $p) {
                $tahap = $p->tahapLomba;

                return [
                    'id' => $p->id,
                    'tahap_lomba_id' => $tahap->id,
                    'nama_event' => $tahap->event?->nama_event,
                    'label_jenis_lomba' => $tahap->event?->label_jenis_lomba,
                    'nomor_tahap' => $tahap->nomor_tahap,
                    'nama_tahap' => $tahap->nama_tahap,
                    'kuota_lolos' => $tahap->kuota_lolos,
                    'jumlah_pengumpulan' => $tahap->pengumpulan_count,
                    'link_penilaian' => $p->link_penilaian,
                    'link_sertifikat' => $p->link_sertifikat,
                    'status_penilaian' => $p->status_penilaian,
                    'status_sertifikat' => $p->status_sertifikat,
                    'status_pengumuman' => $p->status_pengumuman,
                    'dipublikasikan_at' => $p->dipublikasikan_at?->toIso8601String(),
                    'dapat_diubah' => ! $p->isDipublikasikan(),
                    'dapat_dihapus' => ! $p->isDipublikasikan(),
                    'dapat_dipublikasikan' => ! $p->isDipublikasikan()
                        && filled($p->link_penilaian)
                        && filled($p->link_sertifikat),
                ];
            });

        return Inertia::render('Panitia/Pengumuman/Index', [
            'pengumuman' => $pengumuman,
            'filters' => ['status' => $status, 'event_id' => $eventId],
            'events' => Event::query()->orderBy('nama_event')->get(['id', 'nama_event'])
                ->map(fn (Event $e) => ['value' => $e->id, 'label' => $e->nama_event])
                ->values()
                ->all(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Panitia/Pengumuman/Create', [
            'tahapTersedia' => $this->tahapTersedia(),
        ]);
    }

    public function store(StorePengumumanRequest $request): RedirectResponse
    {
        // pembuat_id dari relasi pengumumanDibuat(); dipublikasikan_at tidak pernah dari input.
        $request->user()->pengumumanDibuat()->create($request->validated());

        return redirect()->route('panitia.pengumuman.index')->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    public function edit(Pengumuman $pengumuman): Response|RedirectResponse
    {
        if ($pengumuman->isDipublikasikan()) {
            return redirect()->route('panitia.pengumuman.index')
                ->with('error', 'Pengumuman yang sudah dipublikasikan tidak bisa diubah.');
        }

        $tahap = $pengumuman->tahapLomba()->with('event')->first();

        return Inertia::render('Panitia/Pengumuman/Edit', [
            'pengumuman' => [
                'id' => $pengumuman->id,
                'tahap_lomba_id' => $tahap->id,
                'nama_event' => $tahap->event?->nama_event,
                'nomor_tahap' => $tahap->nomor_tahap,
                'nama_tahap' => $tahap->nama_tahap,
                'link_penilaian' => $pengumuman->link_penilaian,
                'link_sertifikat' => $pengumuman->link_sertifikat,
            ],
        ]);
    }

    public function update(UpdatePengumumanRequest $request, Pengumuman $pengumuman): RedirectResponse
    {
        if ($pengumuman->isDipublikasikan()) {
            return back()->with('error', 'Pengumuman yang sudah dipublikasikan tidak bisa diubah.');
        }

        $pengumuman->update($request->validated()); // hanya link; tahap dan status tidak bisa diubah.

        return redirect()->route('panitia.pengumuman.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Pengumuman $pengumuman): RedirectResponse
    {
        if ($pengumuman->isDipublikasikan()) {
            return back()->with('error', 'Pengumuman yang sudah dipublikasikan tidak bisa dihapus.');
        }

        $pengumuman->delete();

        return redirect()->route('panitia.pengumuman.index')->with('success', 'Pengumuman berhasil dihapus.');
    }

    /** Hitung peringkat dan status lolos lalu tandai diumumkan; seluruh aturan ada di PengumumanService. */
    public function publikasi(Request $request, Pengumuman $pengumuman, PengumumanService $service): RedirectResponse
    {
        try {
            $service->publikasikan($pengumuman, $request->user());
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('panitia.pengumuman.index')->with('success', 'Pengumuman berhasil dipublikasikan.');
    }

    /** @return list<array<string, mixed>> */
    private function tahapTersedia(): array
    {
        return TahapLomba::query()
            ->whereDoesntHave('pengumuman')
            ->whereHas('event')
            ->with('event:id,nama_event,jenis_lomba')
            ->orderBy('event_id')
            ->orderBy('nomor_tahap')
            ->get()
            ->map(fn (TahapLomba $t) => [
                'id' => $t->id,
                'nama_event' => $t->event->nama_event,
                'nomor_tahap' => $t->nomor_tahap,
                'nama_tahap' => $t->nama_tahap,
                'kuota_lolos' => $t->kuota_lolos,
            ])
            ->values()
            ->all();
    }
}