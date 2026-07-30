<?php

namespace App\Http\Controllers\Api\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ReportSchoolController extends Controller
{
    public function attendance(): JsonResponse
    {
        return response()->json([
            ['id' => 101, 'tanggal' => '20 Mei 2026', 'nama' => 'Ahmad Fadil', 'kelas' => 'XI IPA 1', 'jamMasuk' => '07:12:45', 'jamKeluar' => '10:15:20', 'status' => 'hadir'],
            ['id' => 102, 'tanggal' => '20 Mei 2026', 'nama' => 'Bunga Citra', 'kelas' => 'XI IPA 1', 'jamMasuk' => '07:25:10', 'jamKeluar' => '-', 'status' => 'terlambat'],
            ['id' => 103, 'tanggal' => '19 Mei 2026', 'nama' => 'Bunga Citra', 'kelas' => 'XI IPA 1', 'jamMasuk' => '-', 'jamKeluar' => '-', 'status' => 'sakit'],
            ['id' => 104, 'tanggal' => '18 Mei 2026', 'nama' => 'Ahmad Fadil', 'kelas' => 'XI IPA 1', 'jamMasuk' => '-', 'jamKeluar' => '-', 'status' => 'izin'],
            ['id' => 105, 'tanggal' => '18 Mei 2026', 'nama' => 'Elsa Novita', 'kelas' => 'XI IPA 2', 'jamMasuk' => '07:05:22', 'jamKeluar' => '14:14:10', 'status' => 'hadir'],
            ['id' => 106, 'tanggal' => '17 Mei 2026', 'nama' => 'Farhan Ramdan', 'kelas' => 'XI IPA 2', 'jamMasuk' => '-', 'jamKeluar' => '-', 'status' => 'alpa'],
        ]);
    }

    public function academic(): JsonResponse
    {
        return response()->json([
            'kelas' => [
                ['id' => 1, 'kelas' => 'X IPA 1', 'waliKelas' => 'Pak Budi, S.Pd', 'totalSiswa' => 32, 'lulus' => 30, 'tidakLulus' => 2, 'rataNilai' => 81.2, 'kehadiran' => 93],
                ['id' => 2, 'kelas' => 'X IPA 2', 'waliKelas' => 'Bu Rina, M.Pd', 'totalSiswa' => 30, 'lulus' => 28, 'tidakLulus' => 2, 'rataNilai' => 79.5, 'kehadiran' => 91],
                ['id' => 3, 'kelas' => 'XI IPA 1', 'waliKelas' => 'Bu Sari Dewi, S.Pd', 'totalSiswa' => 35, 'lulus' => 33, 'tidakLulus' => 2, 'rataNilai' => 83.7, 'kehadiran' => 95],
                ['id' => 4, 'kelas' => 'XI IPA 2', 'waliKelas' => 'Pak Rahmat, M.Pd', 'totalSiswa' => 34, 'lulus' => 31, 'tidakLulus' => 3, 'rataNilai' => 77.4, 'kehadiran' => 88],
                ['id' => 5, 'kelas' => 'XI IPS 1', 'waliKelas' => 'Bu Laila, S.Pd', 'totalSiswa' => 33, 'lulus' => 32, 'tidakLulus' => 1, 'rataNilai' => 80.1, 'kehadiran' => 90],
                ['id' => 6, 'kelas' => 'XII IPA 1', 'waliKelas' => 'Pak Hasan, M.Pd', 'totalSiswa' => 31, 'lulus' => 31, 'tidakLulus' => 0, 'rataNilai' => 85.6, 'kehadiran' => 97],
                ['id' => 7, 'kelas' => 'XII IPS 1', 'waliKelas' => 'Bu Dewi, S.Pd', 'totalSiswa' => 29, 'lulus' => 27, 'tidakLulus' => 2, 'rataNilai' => 78.9, 'kehadiran' => 89],
            ],
            'mapel' => [
                ['mapel' => 'Matematika', 'avg' => 79.2, 'tertinggi' => 98, 'terendah' => 55, 'tuntas' => 78],
                ['mapel' => 'Fisika', 'avg' => 77.8, 'tertinggi' => 95, 'terendah' => 52, 'tuntas' => 72],
                ['mapel' => 'Kimia', 'avg' => 80.1, 'tertinggi' => 97, 'terendah' => 58, 'tuntas' => 81],
                ['mapel' => 'Biologi', 'avg' => 82.4, 'tertinggi' => 99, 'terendah' => 61, 'tuntas' => 85],
                ['mapel' => 'Bahasa Indonesia', 'avg' => 84.6, 'tertinggi' => 98, 'terendah' => 64, 'tuntas' => 92],
                ['mapel' => 'Bahasa Inggris', 'avg' => 76.3, 'tertinggi' => 94, 'terendah' => 50, 'tuntas' => 69],
                ['mapel' => 'Sejarah', 'avg' => 81.5, 'tertinggi' => 96, 'terendah' => 60, 'tuntas' => 83],
                ['mapel' => 'Ekonomi', 'avg' => 79.9, 'tertinggi' => 95, 'terendah' => 57, 'tuntas' => 78],
            ],
        ]);
    }

    public function finance(): JsonResponse
    {
        return response()->json([
            'pemasukan' => [
                ['id' => 1, 'tanggal' => '2 Jan 2026', 'keterangan' => 'SPP Januari - XI IPA 1', 'kategori' => 'SPP', 'jumlah' => 12500000, 'status' => 'lunas'],
                ['id' => 2, 'tanggal' => '3 Jan 2026', 'keterangan' => 'SPP Januari - XI IPA 2', 'kategori' => 'SPP', 'jumlah' => 11800000, 'status' => 'lunas'],
                ['id' => 3, 'tanggal' => '5 Jan 2026', 'keterangan' => 'Dana BOS Triwulan I', 'kategori' => 'BOS', 'jumlah' => 45000000, 'status' => 'lunas'],
                ['id' => 4, 'tanggal' => '10 Jan 2026', 'keterangan' => 'SPP Januari - XII IPA 1', 'kategori' => 'SPP', 'jumlah' => 13200000, 'status' => 'sebagian'],
                ['id' => 5, 'tanggal' => '15 Jan 2026', 'keterangan' => 'Donasi Orang Tua Siswa', 'kategori' => 'Donasi', 'jumlah' => 5000000, 'status' => 'lunas'],
            ],
            'pengeluaran' => [
                ['id' => 1, 'tanggal' => '3 Jan 2026', 'keterangan' => 'Gaji Guru & Staf Januari', 'kategori' => 'Gaji', 'jumlah' => 38000000, 'status' => 'dibayar'],
                ['id' => 2, 'tanggal' => '5 Jan 2026', 'keterangan' => 'Listrik & Air Januari', 'kategori' => 'Operasional', 'jumlah' => 3200000, 'status' => 'dibayar'],
                ['id' => 3, 'tanggal' => '8 Jan 2026', 'keterangan' => 'Pembelian ATK & Bahan Ajar', 'kategori' => 'Operasional', 'jumlah' => 1850000, 'status' => 'dibayar'],
                ['id' => 4, 'tanggal' => '12 Jan 2026', 'keterangan' => 'Perbaikan Fasilitas Perpustakaan', 'kategori' => 'Pemeliharaan', 'jumlah' => 4500000, 'status' => 'dibayar'],
                ['id' => 5, 'tanggal' => '20 Jan 2026', 'keterangan' => 'Kegiatan Ekstrakurikuler', 'kategori' => 'Kegiatan', 'jumlah' => 2200000, 'status' => 'dibayar'],
            ],
        ]);
    }

    public function grades(): JsonResponse
    {
        return response()->json([
            ['id' => 1, 'nisn' => '0051234567', 'nama' => 'Ahmad Fadil', 'kelas' => 'XI IPA 1', 'waliKelas' => 'Bu Sari Dewi, S.Pd', 'tp' => '2025/2026', 'semester' => '1', 'nilai' => ['Matematika' => 88, 'Fisika' => 82, 'Kimia' => 90, 'Biologi' => 85, 'Bahasa Indonesia' => 88, 'Bahasa Inggris' => 79, 'Sejarah' => 85, 'PJOK' => 90], 'kehadiran' => ['hadir' => 95, 'terlambat' => 4, 'izin' => 2, 'sakit' => 1, 'alpa' => 0], 'catatan' => 'Ahmad menunjukkan kemajuan yang baik di bidang sains. Perlu meningkatkan kemampuan bahasa Inggris.'],
            ['id' => 2, 'nisn' => '0069876543', 'nama' => 'Bunga Citra', 'kelas' => 'XI IPA 1', 'waliKelas' => 'Bu Sari Dewi, S.Pd', 'tp' => '2025/2026', 'semester' => '1', 'nilai' => ['Matematika' => 92, 'Fisika' => 88, 'Kimia' => 85, 'Biologi' => 91, 'Bahasa Indonesia' => 90, 'Bahasa Inggris' => 87, 'Sejarah' => 88, 'PJOK' => 85], 'kehadiran' => ['hadir' => 100, 'terlambat' => 1, 'izin' => 1, 'sakit' => 0, 'alpa' => 0], 'catatan' => 'Siswa berprestasi dengan nilai konsisten di semua mata pelajaran. Direkomendasikan untuk program pengembangan bakat.'],
            ['id' => 3, 'nisn' => '0054321987', 'nama' => 'Cakra Khan', 'kelas' => 'XI IPA 1', 'waliKelas' => 'Bu Sari Dewi, S.Pd', 'tp' => '2025/2026', 'semester' => '1', 'nilai' => ['Matematika' => 72, 'Fisika' => 68, 'Kimia' => 74, 'Biologi' => 70, 'Bahasa Indonesia' => 75, 'Bahasa Inggris' => 65, 'Sejarah' => 73, 'PJOK' => 80], 'kehadiran' => ['hadir' => 85, 'terlambat' => 10, 'izin' => 5, 'sakit' => 2, 'alpa' => 3], 'catatan' => 'Cakra perlu lebih disiplin dalam kehadiran dan fokus pada mata pelajaran eksakta.'],
            ['id' => 4, 'nisn' => '0061122334', 'nama' => 'Dian Sastro', 'kelas' => 'XI IPA 1', 'waliKelas' => 'Bu Sari Dewi, S.Pd', 'tp' => '2025/2026', 'semester' => '1', 'nilai' => ['Matematika' => 95, 'Fisika' => 93, 'Kimia' => 88, 'Biologi' => 92, 'Bahasa Indonesia' => 89, 'Bahasa Inggris' => 91, 'Sejarah' => 90, 'PJOK' => 88], 'kehadiran' => ['hadir' => 98, 'terlambat' => 2, 'izin' => 0, 'sakit' => 0, 'alpa' => 0], 'catatan' => 'Sangat baik dalam partisipasi kelas dan tugas mandiri. Pertahankan prestasinya.'],
        ]);
    }

    public function studentDevelopment(): JsonResponse
    {
        return response()->json([
            'siswaList' => [
                ['id' => '1', 'nama' => 'Ahmad Fadil', 'nisn' => '0051234567', 'kelas' => 'XI IPA 1'],
                ['id' => '2', 'nama' => 'Bunga Citra', 'nisn' => '0069876543', 'kelas' => 'XI IPA 1'],
                ['id' => '3', 'nama' => 'Elsa Novita', 'nisn' => '0055566778', 'kelas' => 'XI IPA 2'],
            ],
            'data' => [
                '1' => ['akademik' => [['bulan' => 'Jul', 'nilai' => 82], ['bulan' => 'Agu', 'nilai' => 85], ['bulan' => 'Sep', 'nilai' => 84], ['bulan' => 'Okt', 'nilai' => 88], ['bulan' => 'Nov', 'nilai' => 89], ['bulan' => 'Des', 'nilai' => 90]], 'kehadiran' => ['hadir' => 95, 'izin' => 3, 'sakit' => 2, 'alpa' => 0], 'sikap' => ['spiritual' => 'Sangat Baik', 'sosial' => 'Baik', 'catatan' => 'Anak yang rajin dan aktif bertanya di kelas.'], 'prestasi' => ['Juara 3 Cerdas Cermat Sekolah']],
                '2' => ['akademik' => [['bulan' => 'Jul', 'nilai' => 88], ['bulan' => 'Agu', 'nilai' => 89], ['bulan' => 'Sep', 'nilai' => 92], ['bulan' => 'Okt', 'nilai' => 91], ['bulan' => 'Nov', 'nilai' => 94], ['bulan' => 'Des', 'nilai' => 95]], 'kehadiran' => ['hadir' => 100, 'izin' => 0, 'sakit' => 0, 'alpa' => 0], 'sikap' => ['spiritual' => 'Sangat Baik', 'sosial' => 'Sangat Baik', 'catatan' => 'Selalu disiplin dan menjadi teladan bagi temannya.'], 'prestasi' => ['Juara 1 Olimpiade Matematika Kota', 'Ketua OSIS']],
                '3' => ['akademik' => [['bulan' => 'Jul', 'nilai' => 78], ['bulan' => 'Agu', 'nilai' => 75], ['bulan' => 'Sep', 'nilai' => 76], ['bulan' => 'Okt', 'nilai' => 79], ['bulan' => 'Nov', 'nilai' => 82], ['bulan' => 'Des', 'nilai' => 85]], 'kehadiran' => ['hadir' => 90, 'izin' => 5, 'sakit' => 3, 'alpa' => 2], 'sikap' => ['spiritual' => 'Baik', 'sosial' => 'Cukup', 'catatan' => 'Perlu peningkatan fokus belajar, namun menunjukkan progres positif bulan terakhir.'], 'prestasi' => []],
            ],
        ]);
    }

    public function accountability(): JsonResponse
    {
        return response()->json([
            ['id' => 1, 'kegiatan' => 'Penerimaan Peserta Didik Baru (PPDB) 2026', 'unit' => 'SMAN Bina Prestasi', 'pic' => 'Bu Dewi Rahayu, M.Pd', 'anggaran' => 15000000, 'realisasi' => 14200000, 'tglLapor' => '15 Feb 2026', 'status' => 'Disetujui'],
            ['id' => 2, 'kegiatan' => 'Pelatihan Guru Kurikulum Merdeka', 'unit' => 'SMAN Bina Prestasi', 'pic' => 'Pak Hasan, M.Pd', 'anggaran' => 8500000, 'realisasi' => 8500000, 'tglLapor' => '20 Mar 2026', 'status' => 'Disetujui'],
            ['id' => 3, 'kegiatan' => 'Lomba Olimpiade Matematika Nasional', 'unit' => 'SMAN Bina Prestasi', 'pic' => 'Pak Budi, S.Pd', 'anggaran' => 12000000, 'realisasi' => 11800000, 'tglLapor' => '10 Apr 2026', 'status' => 'Menunggu Review'],
            ['id' => 4, 'kegiatan' => 'Renovasi Laboratorium Komputer', 'unit' => 'SMAN Bina Prestasi', 'pic' => 'Pak Hendra Wijaya', 'anggaran' => 45000000, 'realisasi' => 0, 'tglLapor' => '-', 'status' => 'Belum Lapor'],
            ['id' => 5, 'kegiatan' => 'Study Tour Kelas XII', 'unit' => 'SMAN Bina Prestasi', 'pic' => 'Bu Laila, S.Pd', 'anggaran' => 25000000, 'realisasi' => 27500000, 'tglLapor' => '5 Mei 2026', 'status' => 'Revisi'],
            ['id' => 6, 'kegiatan' => 'Pengadaan Buku Perpustakaan', 'unit' => 'SMAN Bina Prestasi', 'pic' => 'Bu Farida Hanum, S.Pd', 'anggaran' => 6000000, 'realisasi' => 5850000, 'tglLapor' => '18 Mei 2026', 'status' => 'Disetujui'],
        ]);
    }

    public function staff(): JsonResponse
    {
        return response()->json([
            ['id' => 1, 'nik' => 'GTK001', 'nama' => 'Pak Ahmad Siregar, S.Pd', 'jabatan' => 'Guru Matematika', 'status' => 'Tetap', 'hadir' => 22, 'terlambat' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0],
            ['id' => 2, 'nik' => 'GTK002', 'nama' => 'Bu Dewi Rahayu, M.Pd', 'jabatan' => 'Guru B. Indonesia', 'status' => 'Tetap', 'hadir' => 21, 'terlambat' => 1, 'izin' => 1, 'sakit' => 0, 'alpa' => 0],
            ['id' => 3, 'nik' => 'GTK003', 'nama' => 'Pak Rizky Pratama, S.Pd', 'jabatan' => 'Guru Fisika', 'status' => 'Honorer', 'hadir' => 18, 'terlambat' => 2, 'izin' => 0, 'sakit' => 2, 'alpa' => 1],
            ['id' => 4, 'nik' => 'GTK004', 'nama' => 'Bu Siti Nurhaliza, S.Pd', 'jabatan' => 'Guru Biologi', 'status' => 'Tetap', 'hadir' => 22, 'terlambat' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0],
            ['id' => 5, 'nik' => 'GTK005', 'nama' => 'Pak Hendra Wijaya', 'jabatan' => 'Tenaga Administrasi', 'status' => 'Tetap', 'hadir' => 20, 'terlambat' => 3, 'izin' => 0, 'sakit' => 0, 'alpa' => 2],
            ['id' => 6, 'nik' => 'GTK006', 'nama' => 'Bu Laila Sari, S.E', 'jabatan' => 'Bendahara', 'status' => 'Tetap', 'hadir' => 22, 'terlambat' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0],
            ['id' => 7, 'nik' => 'GTK007', 'nama' => 'Pak Doni Setiawan', 'jabatan' => 'Guru Olahraga', 'status' => 'Honorer', 'hadir' => 16, 'terlambat' => 4, 'izin' => 2, 'sakit' => 0, 'alpa' => 3],
            ['id' => 8, 'nik' => 'GTK008', 'nama' => 'Bu Farida Hanum, S.Pd', 'jabatan' => 'Guru B. Inggris', 'status' => 'Tetap', 'hadir' => 22, 'terlambat' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0],
        ]);
    }
}