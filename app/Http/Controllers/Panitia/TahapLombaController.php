<?php

namespace App\Http\Controllers\Panitia;

use App\Http\Controllers\Controller;
use App\Http\Requests\Panitia\StoreTahapLombaRequest;
use App\Http\Requests\Panitia\UpdateTahapLombaRequest;
use App\Models\Event;
use App\Models\TahapLomba;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Nested + scoped di event: tahap milik event lain di URL otomatis 404
 * (lihat ->scoped() di routes dan Event::childRouteBindingRelationshipName()).
 */
class TahapLombaController extends Controller
{
    private const FORMAT_INPUT = 'Y-m-d\TH:i'; // format <input type="datetime-local">

    public function create(Event $event): Response
    {
        return Inertia::render('Panitia/Tahap/Create', [
            'event' => $this->ringkasEvent($event),
            'nomorTahapBerikutnya' => ((int) TahapLomba::where('event_id', $event->id)->max('nomor_tahap')) + 1,
            'jenisBerkas' => $this->opsiJenisBerkas(),
        ]);
    }

    public function store(StoreTahapLombaRequest $request, Event $event): RedirectResponse
    {
        $event->tahapLomba()->create($request->validated());

        return redirect()->route('panitia.event.show', $event)->with('success', 'Tahap berhasil ditambahkan.');
    }

    public function edit(Event $event, TahapLomba $tahap): Response
    {
        return Inertia::render('Panitia/Tahap/Edit', [
            'event' => $this->ringkasEvent($event),
            'tahap' => [
                'id' => $tahap->id,
                'nomor_tahap' => $tahap->nomor_tahap,
                'nama_tahap' => $tahap->nama_tahap,
                'jenis_berkas' => $tahap->jenis_berkas,
                'batas_mulai' => $tahap->batas_mulai->format(self::FORMAT_INPUT),
                'batas_akhir' => $tahap->batas_akhir->format(self::FORMAT_INPUT),
                'kuota_lolos' => $tahap->kuota_lolos,
            ],
            'jenisBerkas' => $this->opsiJenisBerkas(),
        ]);
    }

    public function update(UpdateTahapLombaRequest $request, Event $event, TahapLomba $tahap): RedirectResponse
    {
        $tahap->update($request->validated());

        return redirect()->route('panitia.event.show', $event)->with('success', 'Tahap berhasil diperbarui.');
    }

    public function destroy(Event $event, TahapLomba $tahap): RedirectResponse
    {
        if ($tahap->pengumpulan()->exists()) {
            return back()->with('error', 'Tahap tidak bisa dihapus karena sudah ada pengumpulan.');
        }

        if ($tahap->pengumuman()->whereNotNull('dipublikasikan_at')->exists()) {
            return back()->with('error', 'Tahap tidak bisa dihapus karena pengumumannya sudah dipublikasikan.');
        }

        $tahap->delete();

        return redirect()->route('panitia.event.show', $event)->with('success', 'Tahap berhasil dihapus.');
    }

    /** @return array{id: int, nama_event: string} */
    private function ringkasEvent(Event $event): array
    {
        return ['id' => $event->id, 'nama_event' => $event->nama_event];
    }

    /** @return list<array{value: string, label: string}> */
    private function opsiJenisBerkas(): array
    {
        return [
            ['value' => 'pdf', 'label' => 'PDF (proposal)'],
            ['value' => 'pptx', 'label' => 'PPTX (presentasi)'],
        ];
    }
}
