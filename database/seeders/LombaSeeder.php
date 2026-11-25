<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;

class LombaSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@iterate.test')->firstOrFail();

        // UI/UX: registrasi lewat, tahap 1 selesai, tahap 2 berjalan. Berbayar.
        $this->buatEvent($admin, [
            'jenis_lomba' => 'uiux',
            'nama_event' => 'Iterate UI/UX Design Competition 2026',
            'tema' => 'Desain Antarmuka untuk Layanan Publik Digital',
            'registrasi_mulai' => now()->subDays(60),
            'registrasi_selesai' => now()->subDays(35),
            'waktu_mulai' => now()->subDays(30),
            'waktu_selesai' => now()->addDays(20),
            'biaya_pendaftaran' => 50000,
        ], [
            [now()->subDays(30), now()->subDays(15)],
            [now()->subDays(5), now()->addDays(10)],
        ]);

        // ICT: registrasi tutup, tahap 1 berjalan. Gratis.
        $this->buatEvent($admin, [
            'jenis_lomba' => 'ict',
            'nama_event' => 'Iterate ICT Innovation Challenge 2026',
            'tema' => 'Inovasi Teknologi Informasi untuk Smart City',
            'registrasi_mulai' => now()->subDays(40),
            'registrasi_selesai' => now()->subDays(10),
            'waktu_mulai' => now()->subDays(7),
            'waktu_selesai' => now()->addDays(40),
            'biaya_pendaftaran' => null,
        ], [
            [now()->subDays(7), now()->addDays(7)],
            [now()->addDays(14), now()->addDays(28)],
        ]);

        // Software Development: registrasi buka, tahap belum mulai. Berbayar.
        $this->buatEvent($admin, [
            'jenis_lomba' => 'software_development',
            'nama_event' => 'Iterate Software Development Contest 2026',
            'tema' => 'Membangun Aplikasi Web untuk UMKM Lokal',
            'registrasi_mulai' => now()->subDays(5),
            'registrasi_selesai' => now()->addDays(25),
            'waktu_mulai' => now()->addDays(30),
            'waktu_selesai' => now()->addDays(70),
            'biaya_pendaftaran' => 75000,
        ], [
            [now()->addDays(30), now()->addDays(45)],
            [now()->addDays(50), now()->addDays(65)],
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array{0: array{0: \Carbon\Carbon, 1: \Carbon\Carbon}, 1: array{0: \Carbon\Carbon, 1: \Carbon\Carbon}}  $jadwalTahap
     */
    private function buatEvent(User $admin, array $data, array $jadwalTahap): void
    {
        $jenis = $data['jenis_lomba'];

        $event = Event::create($data + [
            'pembuat_id' => $admin->id,
            'deskripsi' => 'Kompetisi tingkat mahasiswa Iterate 2026 untuk mengasah kemampuan di bidang '
                .Event::JENIS_LOMBA[$jenis].'.',
            'gambar_pendukung' => "dummy/event/{$jenis}.jpg",
            'link_guidebook' => "https://example.com/guidebook/{$jenis}.pdf",
            'syarat_ketentuan' => 'Peserta adalah mahasiswa aktif dari perguruan tinggi yang sama dalam satu tim. '
                .'Setiap tim terdiri dari 2-3 orang termasuk ketua.',
            'min_anggota' => 2,
            'max_anggota' => 3,
        ]);

        $event->tahapLomba()->create([
            'nomor_tahap' => 1,
            'nama_tahap' => 'Proposal',
            'jenis_berkas' => 'pdf',
            'batas_mulai' => $jadwalTahap[0][0],
            'batas_akhir' => $jadwalTahap[0][1],
            'kuota_lolos' => 10,
        ]);

        $event->tahapLomba()->create([
            'nomor_tahap' => 2,
            'nama_tahap' => 'Presentasi',
            'jenis_berkas' => 'pptx',
            'batas_mulai' => $jadwalTahap[1][0],
            'batas_akhir' => $jadwalTahap[1][1],
            'kuota_lolos' => 10,
        ]);
    }
}
