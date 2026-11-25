<?php

namespace Database\Seeders;

use App\Models\AnggotaTim;
use App\Models\Event;
use App\Models\Tim;
use App\Models\User;
use Illuminate\Database\Seeder;

class TimSeeder extends Seeder
{
    /** Satu PT per peserta (konsisten di semua tim milik peserta itu). */
    private const PERGURUAN_TINGGI = [
        'Universitas Hasanuddin',
        'Universitas Negeri Makassar',
        'Universitas Muslim Indonesia',
        'Universitas Islam Negeri Alauddin Makassar',
        'Politeknik Negeri Ujung Pandang',
    ];

    public function run(): void
    {
        $admin = User::where('email', 'admin@iterate.test')->firstOrFail();
        $peserta = User::where('role', 'peserta')->orderBy('id')->get()->values();
        $events = Event::all()->keyBy('jenis_lomba');
        $faker = fake('id_ID');

        $namaTim = $this->poolNamaTim();
        $nimAnggota = 0; // global, jadi unik di semua event dan tidak bisa bentrok dengan NIM ketua

        $disetujui = ['disetujui', 'Berkas lengkap dan valid.'];
        $menunggu = ['menunggu', null];

        // jenis => indeks peserta (0-based) sebagai ketua + status verifikasi tiap tim
        $rencana = [
            'uiux' => [
                'ketua' => range(0, 11),
                'status' => array_fill(0, 12, $disetujui),
            ],
            'ict' => [
                'ketua' => [3, 4, 5, 12, 13, 14],
                'status' => [
                    $disetujui, $disetujui,
                    $menunggu, $menunggu, $menunggu,
                    ['ditolak', 'File KTM tidak terbaca, mohon unggah ulang.'],
                ],
            ],
            'software_development' => [
                'ketua' => [6, 7, 8, 13, 14],
                'status' => [
                    $menunggu, $menunggu,
                    $disetujui, $disetujui,
                    ['ditolak', 'Bukti pembayaran tidak sesuai nominal.'],
                ],
            ],
        ];

        foreach ($rencana as $jenis => $r) {
            $event = $events[$jenis];

            foreach ($r['ketua'] as $i => $indeksPeserta) {
                $ketua = $peserta[$indeksPeserta];
                $urut = $i + 1; // nomor tim di lomba ini
                $dasar = "dummy/{$jenis}/tim-{$urut}";

                // kode_tim sengaja tidak diisi: dibuat oleh hook creating di Tim::booted().
                $tim = Tim::create([
                    'event_id' => $event->id,
                    'ketua_id' => $ketua->id,
                    'nama_tim' => array_shift($namaTim),
                    'asal_perguruan_tinggi' => self::PERGURUAN_TINGGI[$indeksPeserta % count(self::PERGURUAN_TINGGI)],
                    'nim_ketua' => PenggunaSeeder::nimKetua($indeksPeserta + 1),
                    'file_ktm' => "{$dasar}-ktm.pdf",
                    'file_pakta' => "{$dasar}-pakta.pdf",
                    'file_bukti_bayar' => $event->biaya_pendaftaran !== null
                        ? "{$dasar}-bukti-bayar.pdf"
                        : null,
                ]);

                [$status, $catatan] = $r['status'][$i];
                if ($status !== 'menunggu') {
                    $tim->verifikasi($admin, $status, $catatan);
                }

                // 1-2 anggota selain ketua (total 2-3 dengan ketua).
                foreach (range(1, 1 + ($i % 2)) as $_) {
                    $nimAnggota++;

                    AnggotaTim::create([
                        'tim_id' => $tim->id,
                        'event_id' => $tim->event_id,
                        'nama_lengkap' => $faker->name(),
                        'nim' => sprintf('D1212420%02d', $nimAnggota),
                    ]);
                }
            }
        }
    }

    /** @return list<string> 48 nama tim unik, acak urutannya. */
    private function poolNamaTim(): array
    {
        $awal = ['Celebes', 'Bira', 'Phinisi', 'Losari', 'Toraja', 'Bugis', 'Makassar', 'Lontara'];
        $akhir = ['Tech', 'Inovasi', 'Labs', 'Dev', 'Creative', 'Code'];

        $pool = [];
        foreach ($awal as $a) {
            foreach ($akhir as $b) {
                $pool[] = "Tim {$a} {$b}";
            }
        }

        shuffle($pool);

        return $pool;
    }
}
