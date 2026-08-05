<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Curriculum;
use App\Models\CurriculumSubject;

class CurriculumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Kurikulum Merdeka Utama (Payung Yayasan)
        Curriculum::updateOrCreate(
            ['code' => 'KUR-MERDEKA'],
            [
                'name'        => 'Kurikulum Merdeka',
                'level'       => 'ALL',
                'description' => 'Kurikulum Nasional berbasis Fleksibilitas & Profil Pelajar Pancasila.',
                'is_active'   => true,
            ]
        );

        // 2. Kurikulum 2013 Utama (Payung Yayasan)
        Curriculum::updateOrCreate(
            ['code' => 'K13-REVISI'],
            [
                'name'        => 'Kurikulum 2013 (K13)',
                'level'       => 'ALL',
                'description' => 'Kurikulum 2013 Revisi berbasis Kompetensi Inti & Dasar (KI/KD).',
                'is_active'   => true,
            ]
        );

        // 3. Kurikulum Merdeka SD (Spesifik Sekolah SD)
        $kurmerSD = Curriculum::updateOrCreate(
            ['code' => 'KUR-MERDEKA-SD'],
            [
                'name'        => 'Kurikulum Merdeka SD',
                'level'       => 'SD',
                'description' => 'Kurikulum Merdeka khusus Jenjang Sekolah Dasar (Fase A, B, C).',
                'is_active'   => true,
            ]
        );

        $sdSubjects = [
            ['code' => 'PAI', 'name' => 'Pendidikan Agama dan Budi Pekerti', 'phase' => 'Fase A-C', 'grades' => [1, 2, 3, 4, 5, 6], 'sort' => 1],
            ['code' => 'PPKN', 'name' => 'Pendidikan Pancasila', 'phase' => 'Fase A-C', 'grades' => [1, 2, 3, 4, 5, 6], 'sort' => 2],
            ['code' => 'BINDO', 'name' => 'Bahasa Indonesia', 'phase' => 'Fase A-C', 'grades' => [1, 2, 3, 4, 5, 6], 'sort' => 3],
            ['code' => 'MTK', 'name' => 'Matematika', 'phase' => 'Fase A-C', 'grades' => [1, 2, 3, 4, 5, 6], 'sort' => 4],
            ['code' => 'IPAS', 'name' => 'Ilmu Pengetahuan Alam dan Sosial (IPAS)', 'phase' => 'Fase B-C', 'grades' => [3, 4, 5, 6], 'sort' => 5],
            ['code' => 'PJOK', 'name' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan', 'phase' => 'Fase A-C', 'grades' => [1, 2, 3, 4, 5, 6], 'sort' => 6],
            ['code' => 'SENI', 'name' => 'Seni dan Budaya', 'phase' => 'Fase A-C', 'grades' => [1, 2, 3, 4, 5, 6], 'sort' => 7],
            ['code' => 'BING', 'name' => 'Bahasa Inggris', 'phase' => 'Fase B-C', 'grades' => [3, 4, 5, 6], 'sort' => 8],
            ['code' => 'MULOK', 'name' => 'Muatan Lokal', 'phase' => 'Fase A-C', 'grades' => [1, 2, 3, 4, 5, 6], 'sort' => 9],
        ];

        foreach ($sdSubjects as $subj) {
            CurriculumSubject::updateOrCreate(
                [
                    'curriculum_id' => $kurmerSD->id,
                    'code'          => $subj['code'],
                ],
                [
                    'name'           => $subj['name'],
                    'level'          => 'SD',
                    'phase'          => $subj['phase'],
                    'default_grades' => $subj['grades'],
                    'is_mandatory'   => true,
                    'sort_order'     => $subj['sort'],
                ]
            );
        }

        // 4. Kurikulum Merdeka SMP (Spesifik Sekolah SMP)
        $kurmerSMP = Curriculum::updateOrCreate(
            ['code' => 'KUR-MERDEKA-SMP'],
            [
                'name'        => 'Kurikulum Merdeka SMP',
                'level'       => 'SMP',
                'description' => 'Kurikulum Merdeka khusus Jenjang Sekolah Menengah Pertama (Fase D).',
                'is_active'   => true,
            ]
        );

        $smpSubjects = [
            ['code' => 'PAI', 'name' => 'Pendidikan Agama dan Budi Pekerti', 'phase' => 'Fase D', 'grades' => [7, 8, 9], 'sort' => 1],
            ['code' => 'PPKN', 'name' => 'Pendidikan Pancasila', 'phase' => 'Fase D', 'grades' => [7, 8, 9], 'sort' => 2],
            ['code' => 'BINDO', 'name' => 'Bahasa Indonesia', 'phase' => 'Fase D', 'grades' => [7, 8, 9], 'sort' => 3],
            ['code' => 'MTK', 'name' => 'Matematika', 'phase' => 'Fase D', 'grades' => [7, 8, 9], 'sort' => 4],
            ['code' => 'IPA', 'name' => 'Ilmu Pengetahuan Alam (IPA)', 'phase' => 'Fase D', 'grades' => [7, 8, 9], 'sort' => 5],
            ['code' => 'IPS', 'name' => 'Ilmu Pengetahuan Sosial (IPS)', 'phase' => 'Fase D', 'grades' => [7, 8, 9], 'sort' => 6],
            ['code' => 'BING', 'name' => 'Bahasa Inggris', 'phase' => 'Fase D', 'grades' => [7, 8, 9], 'sort' => 7],
            ['code' => 'PJOK', 'name' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan', 'phase' => 'Fase D', 'grades' => [7, 8, 9], 'sort' => 8],
            ['code' => 'INF', 'name' => 'Informatika', 'phase' => 'Fase D', 'grades' => [7, 8, 9], 'sort' => 9],
            ['code' => 'SENI', 'name' => 'Seni dan Prakarya', 'phase' => 'Fase D', 'grades' => [7, 8, 9], 'sort' => 10],
            ['code' => 'MULOK', 'name' => 'Muatan Lokal', 'phase' => 'Fase D', 'grades' => [7, 8, 9], 'sort' => 11],
        ];

        foreach ($smpSubjects as $subj) {
            CurriculumSubject::updateOrCreate(
                [
                    'curriculum_id' => $kurmerSMP->id,
                    'code'          => $subj['code'],
                ],
                [
                    'name'           => $subj['name'],
                    'level'          => 'SMP',
                    'phase'          => $subj['phase'],
                    'default_grades' => $subj['grades'],
                    'is_mandatory'   => true,
                    'sort_order'     => $subj['sort'],
                ]
            );
        }
    }
}
