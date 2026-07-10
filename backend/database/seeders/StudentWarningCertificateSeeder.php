<?php

namespace Database\Seeders;

use App\Models\StudentWarningCertificate;
use App\Models\StudentProfile;
use Illuminate\Database\Seeder;

class StudentWarningCertificateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get active students from StudentProfile
        $students = StudentProfile::with(['user', 'classroom'])->take(6)->get();

        if ($students->isEmpty()) {
            return;
        }

        $mockWarnings = [
            [
                'jenis_surat' => 'Surat Pelanggaran',
                'tanggal'     => now()->subDays(1)->format('Y-m-d'),
                'perihal_pelanggaran' => 'Terlambat lebih dari 3 kali berturut-turut pada minggu ini tanpa alasan yang jelas.',
                'jumlah_tunggakan' => null,
            ],
            [
                'jenis_surat' => 'Surat Pelanggaran',
                'tanggal'     => now()->subDays(3)->format('Y-m-d'),
                'perihal_pelanggaran' => 'Membawa barang yang dilarang di lingkungan sekolah sesuai peraturan tata tertib sekolah.',
                'jumlah_tunggakan' => null,
            ],
            [
                'jenis_surat' => 'Surat Tunggakan',
                'tanggal'     => null,
                'perihal_pelanggaran' => null,
                'jumlah_tunggakan' => '1.500.000',
            ],
            [
                'jenis_surat' => 'Surat Tunggakan',
                'tanggal'     => null,
                'perihal_pelanggaran' => null,
                'jumlah_tunggakan' => '800.000',
            ],
        ];

        foreach ($mockWarnings as $index => $warning) {
            $student = $students[$index % $students->count()];

            StudentWarningCertificate::create([
                'foundation_id'       => $student->user->foundation_id ?? 1,
                'school_id'           => $student->user->school_id ?? 1,
                'student_id'          => $student->user_id,
                'tanggal_dibuat'      => now()->subDays($index * 3)->format('Y-m-d'),
                'jenis_surat'         => $warning['jenis_surat'],
                'nama'                => $student->user->name,
                'nisn'                => $student->nisn,
                'kelas'               => $student->classroom->name ?? 'VI A',
                'tanggal'             => $warning['tanggal'],
                'perihal_pelanggaran' => $warning['perihal_pelanggaran'],
                'jumlah_tunggakan'    => $warning['jumlah_tunggakan'],
                'status'              => 'Selesai',
            ]);
        }
    }
}
