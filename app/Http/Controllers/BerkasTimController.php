<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Peserta\TimController;
use App\Models\Tim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Berkas tim (KTM, pakta integritas, bukti bayar) ada di disk privat.
 * Hanya ketua tim yang bersangkutan dan panitia yang boleh membukanya.
 */
class BerkasTimController extends Controller
{
    private const JENIS = [
        'ktm' => 'file_ktm',
        'pakta' => 'file_pakta',
        'bukti_bayar' => 'file_bukti_bayar',
    ];

    public function show(Request $request, Tim $tim, string $jenis): StreamedResponse
    {
        $kolom = self::JENIS[$jenis] ?? null;
        abort_if($kolom === null, 404);

        $user = $request->user();
        abort_unless($user->isPanitia() || (int) $tim->ketua_id === (int) $user->id, 403);

        $path = $tim->{$kolom};
        $disk = Storage::disk(TimController::DISK);
        abort_if(! $path || ! $disk->exists($path), 404);

        return $disk->response($path);
    }
}