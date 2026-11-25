<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Pengumpulan;
use App\Models\User;
use Illuminate\Database\Seeder;

class PengumpulanSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@iterate.test')->firstOrFail();
        $faker = fake('id_ID');

        // UI/UX tahap 1: 12 tim sudah dikumpul dan dinilai (peringkat dihitung saat publish).
        $uiux = Event::where('jenis_lomba', 'uiux')->firstOrFail();
        $tahapUiux = $uiux->tahapLomba()->where('nomor_tahap', 1)->firstOrFail();
        $timUiux = $uiux->tim()->where('status_verifikasi', 'disetujui')->orderBy('id')->get();
        $skor = $this->skorUnik($timUiux->count());

        foreach ($timUiux as $i => $tim) {
            Pengumpulan::create([
                'tim_id' => $tim->id,
                'tahap_lomba_id' => $tahapUiux->id,
                'file_submission' => "dummy/submission/uiux-tahap-1/{$tim->kode_tim}.pdf",
                // sebelum batas_akhir tahap
                'submitted_at' => $tahapUiux->batas_mulai->copy()
                    ->addDays(random_int(1, 13))->addHours(random_int(0, 23)),
                'skor' => $skor[$i],
                'catatan_penilaian' => $faker->sentence(),
                'penilai_id' => $admin->id,
                'dinilai_at' => $tahapUiux->batas_akhir->copy()->addDays(2),
            ]);
        }

        // ICT tahap 1: 2 tim disetujui sudah mengumpulkan, belum dinilai.
        $ict = Event::where('jenis_lomba', 'ict')->firstOrFail();
        $tahapIct = $ict->tahapLomba()->where('nomor_tahap', 1)->firstOrFail();
        $timIct = $ict->tim()->where('status_verifikasi', 'disetujui')->orderBy('id')->get();

        foreach ($timIct as $i => $tim) {
            Pengumpulan::create([
                'tim_id' => $tim->id,
                'tahap_lomba_id' => $tahapIct->id,
                'file_submission' => "dummy/submission/ict-tahap-1/{$tim->kode_tim}.pdf",
                'submitted_at' => $tahapIct->batas_mulai->copy()->addDays($i + 2),
            ]);
        }
    }

    /** @return list<string> $n skor unik 60.00-100.00, urutan acak. */
    private function skorUnik(int $n): array
    {
        $unik = [];

        while (count($unik) < $n) {
            $unik[number_format(random_int(6000, 10000) / 100, 2, '.', '')] = true;
        }

        $skor = array_map('strval', array_keys($unik));
        shuffle($skor);

        return $skor;
    }
}
