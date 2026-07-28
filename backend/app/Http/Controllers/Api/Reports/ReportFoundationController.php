<?php

namespace App\Http\Controllers\Api\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ReportFoundationController extends Controller
{
    public function consolidation(): JsonResponse
    {
        return response()->json([
            ['id' => 1, 'nama' => 'SDN Tunas Bangsa', 'jenjang' => 'SD', 'totalSiswa' => 412, 'guru' => 24, 'rataNilai' => 82.3, 'kehadiran' => 94, 'pemasukan' => 185000000, 'pengeluaran' => 162000000],
            ['id' => 2, 'nama' => 'SMPN Harapan Ilmu', 'jenjang' => 'SMP', 'totalSiswa' => 356, 'guru' => 31, 'rataNilai' => 79.8, 'kehadiran' => 91, 'pemasukan' => 220000000, 'pengeluaran' => 198000000],
            ['id' => 3, 'nama' => 'SMAN Bina Prestasi', 'jenjang' => 'SMA', 'totalSiswa' => 487, 'guru' => 42, 'rataNilai' => 81.5, 'kehadiran' => 93, 'pemasukan' => 345000000, 'pengeluaran' => 298000000],
            ['id' => 4, 'nama' => 'SMK Teknologi Maju', 'jenjang' => 'SMK', 'totalSiswa' => 298, 'guru' => 27, 'rataNilai' => 77.9, 'kehadiran' => 89, 'pemasukan' => 210000000, 'pengeluaran' => 195000000],
        ]);
    }

    public function academic(): JsonResponse
    {
        return response()->json([
            'akademik' => [
                ['id' => 1, 'sekolah' => 'SDN Tunas Bangsa', 'jenjang' => 'SD', 'siswa' => 412, 'rataNilai' => 82.3, 'kelulusan' => 96, 'ekskul' => 8, 'prestasi' => 12],
                ['id' => 2, 'sekolah' => 'SMPN Harapan Ilmu', 'jenjang' => 'SMP', 'siswa' => 356, 'rataNilai' => 79.8, 'kelulusan' => 94, 'ekskul' => 11, 'prestasi' => 8],
                ['id' => 3, 'sekolah' => 'SMAN Bina Prestasi', 'jenjang' => 'SMA', 'siswa' => 487, 'rataNilai' => 81.5, 'kelulusan' => 97, 'ekskul' => 15, 'prestasi' => 21],
                ['id' => 4, 'sekolah' => 'SMK Teknologi Maju', 'jenjang' => 'SMK', 'siswa' => 298, 'rataNilai' => 77.9, 'kelulusan' => 92, 'ekskul' => 7, 'prestasi' => 6],
            ],
            'prestasi' => [
                ['id' => 1, 'nama' => 'Olimpiade Matematika Nasional', 'sekolah' => 'SMAN Bina Prestasi', 'tingkat' => 'Nasional', 'hasil' => 'Juara 1', 'tgl' => 'Mar 2026'],
                ['id' => 2, 'nama' => 'Lomba Karya Ilmiah Remaja', 'sekolah' => 'SMAN Bina Prestasi', 'tingkat' => 'Provinsi', 'hasil' => 'Juara 2', 'tgl' => 'Feb 2026'],
                ['id' => 3, 'nama' => 'Futsal Pelajar Kota', 'sekolah' => 'SMPN Harapan Ilmu', 'tingkat' => 'Kota', 'hasil' => 'Juara 1', 'tgl' => 'Apr 2026'],
                ['id' => 4, 'nama' => 'Lomba Debat Bahasa Inggris', 'sekolah' => 'SMAN Bina Prestasi', 'tingkat' => 'Provinsi', 'hasil' => 'Juara 3', 'tgl' => 'Jan 2026'],
                ['id' => 5, 'nama' => 'Festival Seni Pelajar', 'sekolah' => 'SDN Tunas Bangsa', 'tingkat' => 'Kota', 'hasil' => 'Juara 2', 'tgl' => 'Mei 2026'],
            ],
        ]);
    }

    public function infrastructure(): JsonResponse
    {
        return response()->json([
            'sarana' => [
                ['id' => 1, 'sekolah' => 'SDN Tunas Bangsa', 'jenjang' => 'SD', 'ruangKelas' => 14, 'perpustakaan' => 1, 'lab' => 1, 'toilet' => 8, 'kondisiBaik' => 85, 'kondisiRusak' => 15],
                ['id' => 2, 'sekolah' => 'SMPN Harapan Ilmu', 'jenjang' => 'SMP', 'ruangKelas' => 18, 'perpustakaan' => 1, 'lab' => 3, 'toilet' => 10, 'kondisiBaik' => 78, 'kondisiRusak' => 22],
                ['id' => 3, 'sekolah' => 'SMAN Bina Prestasi', 'jenjang' => 'SMA', 'ruangKelas' => 24, 'perpustakaan' => 2, 'lab' => 5, 'toilet' => 14, 'kondisiBaik' => 92, 'kondisiRusak' => 8],
                ['id' => 4, 'sekolah' => 'SMK Teknologi Maju', 'jenjang' => 'SMK', 'ruangKelas' => 16, 'perpustakaan' => 1, 'lab' => 8, 'toilet' => 10, 'kondisiBaik' => 71, 'kondisiRusak' => 29],
            ],
            'inventaris' => [
                ['id' => 1, 'nama' => 'Komputer / Laptop', 'sekolah' => 'SMAN Bina Prestasi', 'jumlah' => 45, 'kondisi' => 'Baik', 'tahun' => '2024'],
                ['id' => 2, 'nama' => 'Proyektor', 'sekolah' => 'SMPN Harapan Ilmu', 'jumlah' => 18, 'kondisi' => 'Baik', 'tahun' => '2023'],
                ['id' => 3, 'nama' => 'Meja Siswa', 'sekolah' => 'SDN Tunas Bangsa', 'jumlah' => 412, 'kondisi' => 'Perlu Perbaikan', 'tahun' => '2019'],
                ['id' => 4, 'nama' => 'Peralatan Lab Kimia', 'sekolah' => 'SMAN Bina Prestasi', 'jumlah' => 5, 'kondisi' => 'Baik', 'tahun' => '2025'],
                ['id' => 5, 'nama' => 'Mesin CNC', 'sekolah' => 'SMK Teknologi Maju', 'jumlah' => 3, 'kondisi' => 'Perlu Perbaikan', 'tahun' => '2020'],
                ['id' => 6, 'nama' => 'Kursi Kantor', 'sekolah' => 'SMPN Harapan Ilmu', 'jumlah' => 50, 'kondisi' => 'Baik', 'tahun' => '2022'],
            ],
        ]);
    }

    public function finance(): JsonResponse
    {
        return response()->json([
            ['id' => 1, 'sekolah' => 'SDN Tunas Bangsa', 'jenjang' => 'SD', 'spp' => 85000000, 'bos' => 45000000, 'donasi' => 10000000, 'gaji' => 62000000, 'operasional' => 15000000, 'pemeliharaan' => 8000000],
            ['id' => 2, 'sekolah' => 'SMPN Harapan Ilmu', 'jenjang' => 'SMP', 'spp' => 112000000, 'bos' => 62000000, 'donasi' => 8000000, 'gaji' => 98000000, 'operasional' => 22000000, 'pemeliharaan' => 12000000],
            ['id' => 3, 'sekolah' => 'SMAN Bina Prestasi', 'jenjang' => 'SMA', 'spp' => 156000000, 'bos' => 82000000, 'donasi' => 15000000, 'gaji' => 148000000, 'operasional' => 35000000, 'pemeliharaan' => 18000000],
            ['id' => 4, 'sekolah' => 'SMK Teknologi Maju', 'jenjang' => 'SMK', 'spp' => 98000000, 'bos' => 54000000, 'donasi' => 6000000, 'gaji' => 85000000, 'operasional' => 28000000, 'pemeliharaan' => 14000000],
        ]);
    }

    public function hr(): JsonResponse
    {
        return response()->json([
            'guru' => [
                ['id' => 1, 'sekolah' => 'SDN Tunas Bangsa', 'jenjang' => 'SD', 'totalGuru' => 18, 'totalStaf' => 6, 's1' => 14, 's2' => 4, 'tetap' => 15, 'honorer' => 9, 'sertifikasi' => 11],
                ['id' => 2, 'sekolah' => 'SMPN Harapan Ilmu', 'jenjang' => 'SMP', 'totalGuru' => 22, 'totalStaf' => 9, 's1' => 16, 's2' => 6, 'tetap' => 20, 'honorer' => 11, 'sertifikasi' => 16],
                ['id' => 3, 'sekolah' => 'SMAN Bina Prestasi', 'jenjang' => 'SMA', 'totalGuru' => 30, 'totalStaf' => 12, 's1' => 20, 's2' => 10, 'tetap' => 28, 'honorer' => 14, 'sertifikasi' => 22],
                ['id' => 4, 'sekolah' => 'SMK Teknologi Maju', 'jenjang' => 'SMK', 'totalGuru' => 19, 'totalStaf' => 8, 's1' => 15, 's2' => 4, 'tetap' => 17, 'honorer' => 10, 'sertifikasi' => 13],
            ],
            'absensi' => [
                ['id' => 1, 'nama' => 'Pak Budi, S.Pd', 'sekolah' => 'SDN Tunas Bangsa', 'mapel' => 'Matematika', 'hadir' => 22, 'terlambat' => 1, 'izin' => 0, 'alpa' => 0, 'persen' => 100],
                ['id' => 2, 'nama' => 'Bu Rina, M.Pd', 'sekolah' => 'SDN Tunas Bangsa', 'mapel' => 'IPA', 'hadir' => 20, 'terlambat' => 2, 'izin' => 1, 'alpa' => 0, 'persen' => 91],
                ['id' => 3, 'nama' => 'Bu Sari Dewi, S.Pd', 'sekolah' => 'SMPN Harapan Ilmu', 'mapel' => 'Bahasa Indonesia', 'hadir' => 21, 'terlambat' => 0, 'izin' => 1, 'alpa' => 0, 'persen' => 95],
                ['id' => 4, 'nama' => 'Pak Rahmat, M.Pd', 'sekolah' => 'SMPN Harapan Ilmu', 'mapel' => 'Fisika', 'hadir' => 18, 'terlambat' => 3, 'izin' => 2, 'alpa' => 0, 'persen' => 82],
                ['id' => 5, 'nama' => 'Pak Hasan, M.Pd', 'sekolah' => 'SMAN Bina Prestasi', 'mapel' => 'Kimia', 'hadir' => 22, 'terlambat' => 0, 'izin' => 0, 'alpa' => 0, 'persen' => 100],
            ],
        ]);
    }

    public function students(): JsonResponse
    {
        return response()->json([
            ['id' => 1, 'sekolah' => 'SDN Tunas Bangsa', 'jenjang' => 'SD', 'total' => 412, 'laki' => 210, 'perempuan' => 202, 'baru' => 95, 'keluar' => 12, 'naik' => 88, 'tinggal' => 7],
            ['id' => 2, 'sekolah' => 'SMPN Harapan Ilmu', 'jenjang' => 'SMP', 'total' => 356, 'laki' => 182, 'perempuan' => 174, 'baru' => 88, 'keluar' => 8, 'naik' => 80, 'tinggal' => 8],
            ['id' => 3, 'sekolah' => 'SMAN Bina Prestasi', 'jenjang' => 'SMA', 'total' => 487, 'laki' => 245, 'perempuan' => 242, 'baru' => 110, 'keluar' => 15, 'naik' => 100, 'tinggal' => 10],
            ['id' => 4, 'sekolah' => 'SMK Teknologi Maju', 'jenjang' => 'SMK', 'total' => 298, 'laki' => 195, 'perempuan' => 103, 'baru' => 72, 'keluar' => 10, 'naik' => 65, 'tinggal' => 7],
        ]);
    }
}