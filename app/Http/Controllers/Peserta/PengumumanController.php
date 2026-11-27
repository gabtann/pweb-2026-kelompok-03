<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Pengumpulan;
use App\Models\Pengumuman;
use App\Models\Tim;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Peserta hanya melihat pengumuman yang SUDAH dipublikasikan, dan hanya
 * untuk event yang diikuti timnya.
 */
class PengumumanController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $eventIds = Tim::query()->where('ketua_id', $user->id)->pluck('event_id');

        $pengumuman = Pengumuman::query()
            ->whereNotNull('dipublikasikan_at')
            ->whereHas('tahapLomba', fn ($q) => $q->whereIn('event_id', $eventIds))
            ->with('tahapLomba.event')
            ->orderByDesc('dipublikasikan_at')
            ->orderByDesc('pengumuman.id')
            ->paginate(10);

        // Hasil tim milik peserta ini di tiap tahap pada halaman ini.
        $milikku = Pengumpulan::query()
            ->whereIn('tahap_lomba_id', $pengumuman->getCollection()->pluck('tahap_lomba_id'))
            ->whereHas('tim', fn ($q) => $q->where('ketua_id', $user->id))
            ->get()
            ->keyBy('tahap_lomba_id');

        $pengumuman = $pengumuman->through(function (Pengumuman $p) use ($milikku) {
            $tahap = $p->tahapLomba;
            $saya = $milikku->get($tahap->id);

            return [
                'id' => $p->id,
                'nama_event' => $tahap->event?->nama_event,
                'label_jenis_lomba' => $tahap->event?->label_jenis_lomba,
                'nomor_tahap' => $tahap->nomor_tahap,
                'nama_tahap' => $tahap->nama_tahap,
                'kuota_lolos' => $tahap->kuota_lolos,
                'dipublikasikan_at' => $p->dipublikasikan_at->toIso8601String(),
                'link_penilaian' => $p->link_penilaian,
                'link_sertifikat' => $p->link_sertifikat,
                'hasil_saya' => $saya ? [
                    'status_kelulusan' => $saya->status_kelulusan,
                    'peringkat' => $saya->peringkat,
                ] : null,
                'lolos' => $tahap->peringkatLolos()
                    ->map(fn (Pengumpulan $l) => [
                        'peringkat' => $l->peringkat,
                        'nama_tim' => $l->tim->nama_tim,
                        'asal_perguruan_tinggi' => $l->tim->asal_perguruan_tinggi,
                    ])
                    ->values()
                    ->all(),
            ];
        });

        return Inertia::render('Peserta/Pengumuman/Index', ['pengumuman' => $pengumuman]);
    }
}