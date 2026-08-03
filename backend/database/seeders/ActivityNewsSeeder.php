<?php

namespace Database\Seeders;

use App\Models\ActivityNews;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivityNewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find a school admin or principal
        $author = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['admin_sekolah', 'kepala_sekolah']);
        })->first();

        if (!$author) {
            return;
        }

        $mockNews = [
            [
                'title' => 'Ujian Tengah Semester, Semester Ganjil 2025/2026',
                'category' => 'AKADEMIK',
                'publish_date' => '2025-03-10',
                'content' => 'Ujian Tengah Semester Untuk Seluruh Kelas Akan dilaksanakan pada tanggal 17 - 24 Maret 2025. Harap persiapkan diri dengan baik dan perhatikan jadwal ujian yang akan dibagikan oleh wali kelas masing-masing.',
                'image' => null,
            ],
            [
                'title' => 'Ujian Akhir Semester, Semester Ganjil 2025/2026',
                'category' => 'AKADEMIK',
                'publish_date' => '2025-07-10',
                'content' => 'Ujian Akhir Semester Untuk Seluruh Kelas Akan dilaksanakan pada tanggal 17 - 24 Juli 2025. Harap persiapkan diri dengan baik. Kegiatan belajar mengajar akan diakhiri lebih awal pada masa ujian.',
                'image' => null,
            ],
            [
                'title' => 'Permintaan Infaq Untuk Qurban',
                'category' => 'KEUANGAN',
                'publish_date' => '2025-05-18',
                'content' => 'Menjelang Hari Raya Idul Adha, sekolah membuka kesempatan bagi para siswa dan orang tua untuk menyisihkan sebagian rezekinya untuk infaq qurban. Infaq dapat disalurkan melalui wali kelas atau transfer ke rekening sekolah.',
                'image' => null,
            ],
            [
                'title' => 'Kegiatan Outbound Kelas 6',
                'category' => 'UMUM',
                'publish_date' => '2026-05-17',
                'content' => 'Dalam rangka penyegaran pasca ujian kelas 6, sekolah akan mengadakan kegiatan outbound ke luar daerah. Kegiatan akan dilaksanakan dengan bimbingan wali kelas masing-masing.',
                'image' => null,
            ],
            [
                'title' => 'Pengumuman Libur Nasional',
                'category' => 'UMUM',
                'publish_date' => '2026-06-01',
                'content' => 'Sehubungan dengan Hari Lahir Pancasila, sekolah akan diliburkan pada tanggal 1 Juni 2026. Kegiatan belajar mengajar akan kembali aktif pada tanggal 2 Juni 2026.',
                'image' => null,
            ],
        ];

        foreach ($mockNews as $news) {
            ActivityNews::create([
                'foundation_id' => $author->foundation_id ?? 1,
                'school_id'     => $author->school_id ?? 1,
                'created_by'    => $author->id,
                'title'         => $news['title'],
                'content'       => $news['content'],
                'category'      => $news['category'],
                'image'         => $news['image'],
                'publish_date'  => $news['publish_date'],
            ]);
        }
    }
}
