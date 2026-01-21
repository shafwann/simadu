<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\Logbook;

class LogbookSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('id_ID');

        // 1. KAMUS KEGIATAN BERDASARKAN INSTANSI
        $activities = [
            'BAPPERIDA' => [
                'Membantu rekap data excel pengajuan anggaran tahunan.',
                'Menyusun draf laporan bulanan bidang perencanaan.',
                'Input data usulan masyarakat ke sistem SIPD.',
                'Membantu menyiapkan ruang rapat untuk Musrenbang.',
                'Melakukan verifikasi proposal penelitian masuk.',
            ],
            'DISKOMINFO' => [
                'Melakukan maintenance jaringan internet di ruang server.',
                'Membantu desain grafis untuk banner kegiatan Pemkab.',
                'Input konten berita kegiatan bupati ke website resmi.',
                'Pengecekan hardware komputer di bidang sekretariat.',
                'Belajar konfigurasi Mikrotik dan manajemen bandwidth.',
                'Membantu troubleshooting printer macet di ruang TU.',
            ],
            'DISDIKBUD' => [
                'Rekap data Dapodik sekolah dasar se-kabupaten.',
                'Membantu administrasi sertifikasi guru.',
                'Menyiapkan berkas untuk lomba siswa berprestasi.',
                'Arsip surat masuk dari sekolah-sekolah.',
                'Membantu pendataan aset sekolah negeri.',
            ],
            'DINKES' => [
                'Input data imunisasi balita ke aplikasi pelaporan.',
                'Rekapitulasi data penyakit menular dari Puskesmas.',
                'Membantu administrasi surat rekomendasi izin praktek.',
                'Menyiapkan materi penyuluhan kesehatan lingkungan.',
                'Arsip data stok obat gudang farmasi.',
            ],
            'DPUPR' => [
                'Membantu survey lapangan pengukuran jalan rusak.',
                'Belajar menggambar denah bangunan menggunakan AutoCAD.',
                'Rekap data permohonan IMB masuk.',
                'Dokumentasi proyek perbaikan drainase kota.',
                'Membantu pengawasan teknis tata ruang.',
            ],
            'DISPARPORA' => [
                'Mendata kunjungan wisatawan ke objek wisata daerah.',
                'Membantu persiapan event pameran ekonomi kreatif.',
                'Update data database atlet berprestasi.',
                'Dokumentasi kegiatan seleksi Paskibraka.',
                'Membuat konten promosi wisata di media sosial dinas.',
            ],
            // Default General
            'UMUM' => [
                'Mengikuti apel pagi dan koordinasi rutin.',
                'Melakukan scanning dan digitalisasi arsip surat.',
                'Mengantar surat disposisi antar bidang.',
                'Membantu fotokopi dan penjilidan dokumen laporan.',
                'Membersihkan dan merapikan area kerja.',
            ]
        ];

        // 2. AMBIL DATA ABSENSI (Hadir/Telat) + RELASI USER
        $attendances = Attendance::with('user')->whereIn('status', ['Hadir', 'Telat'])->get();

        foreach ($attendances as $absen) {
            // Pastikan user masih ada (mencegah error jika user dihapus tapi absen tersisa)
            if (!$absen->user) continue;

            // Ambil Kategori Instansi Mahasiswa
            $instansi = $absen->user->kategori_instansi ?? 'UMUM';

            // Pilih daftar kegiatan sesuai instansi (Fallback ke BAPPERIDA/UMUM jika tidak ada)
            $listKegiatan = $activities[$instansi] ?? $activities['BAPPERIDA'];

            // Pilih 1 kegiatan random dari daftar tersebut
            $kegiatanTerpilih = $faker->randomElement($listKegiatan);

            Logbook::create([
                'user_id' => $absen->user_id,
                'tanggal' => $absen->tanggal, // Sesuai tanggal absen
                'kegiatan' => $kegiatanTerpilih,

                // Status Validasi Random (70% Valid, 30% Belum)
                'is_validated' => $faker->boolean(70),
            ]);
        }
    }
}
