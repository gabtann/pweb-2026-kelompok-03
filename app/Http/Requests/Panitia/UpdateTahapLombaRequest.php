<?php

namespace App\Http\Requests\Panitia;

/**
 * Aturan sama dengan Store; unique nomor_tahap mengabaikan tahap yang sedang diubah
 * (diambil dari route parameter {tahap} di StoreTahapLombaRequest::rules()).
 */
class UpdateTahapLombaRequest extends StoreTahapLombaRequest {}
