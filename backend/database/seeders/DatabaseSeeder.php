<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Foundation;
use App\Models\ParentProfile;
use App\Models\Role;
use App\Models\School;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ─────────────────────────────────────────────
        //  1. Seed Roles
        // ─────────────────────────────────────────────
        $this->call(RoleSeeder::class);

        $roles = Role::all()->keyBy('name');

        // ─────────────────────────────────────────────
        //  2. Foundation (Yayasan)
        // ─────────────────────────────────────────────
        $foundation = Foundation::create([
            'code'             => 'Y0001',
            'name'             => 'Yayasan Nusantara Pintar',
            'established_date' => '2010-01-15',
            'status'           => 'active',
            'address'          => 'Jl. Pendidikan No. 123, Jakarta Timur',
            'email'            => 'info@nusantarapintar.id',
            'phone'            => '021-12345678',
            'website'          => 'https://nusantarapintar.id',
            'deed_number'      => 'AHU-12345.AH.01.04',
            'deed_date'        => '2010-01-15',
            'decree_number'    => 'SK-001/YNP/2010',
            'decree_date'      => '2010-02-01',
        ]);

        // ─────────────────────────────────────────────
        //  3. School (Sekolah)
        // ─────────────────────────────────────────────
        $school = School::create([
            'foundation_id'        => $foundation->id,
            'name'                 => 'SD Nusantara Pintar Bekasi',
            'npsn'                 => '20100001',
            'level'                => 'SD',
            'established_date'     => '2012-07-01',
            'status'               => 'active',
            'address'              => 'Jl. Raya Bekasi No. 45, Bekasi',
            'email'                => 'info@sdnpbekasi.sch.id',
            'phone'                => '021-87654321',
            'website'              => 'https://sdnpbekasi.sch.id',
            'decree_number'        => 'SK-002/SDNP/2012',
            'decree_date'          => '2012-06-15',
            'permit_number'        => 'IZIN-001/2012',
            'permit_date'          => '2012-06-01',
            'accreditation'        => 'A',
            'accreditation_date'   => '2023-05-10',
            'accreditation_number' => 'AKR-2023-0001',
        ]);

        // ─────────────────────────────────────────────
        //  4. Academic Year (Tahun Ajaran)
        // ─────────────────────────────────────────────
        $academicYear = AcademicYear::create([
            'school_id'  => $school->id,
            'name'       => '2024/2025',
            'semester'   => 'even',
            'start_date' => '2025-01-06',
            'end_date'   => '2025-06-30',
            'is_active'  => true,
        ]);

        // ─────────────────────────────────────────────
        //  5. Subjects (Mata Pelajaran)
        // ─────────────────────────────────────────────
        $subjectsData = [
            ['code' => 'MTK', 'name' => 'Matematika'],
            ['code' => 'BIN', 'name' => 'Bahasa Indonesia'],
            ['code' => 'IPA', 'name' => 'Ilmu Pengetahuan Alam'],
            ['code' => 'IPS', 'name' => 'Ilmu Pengetahuan Sosial'],
            ['code' => 'BIG', 'name' => 'Bahasa Inggris'],
            ['code' => 'PAI', 'name' => 'Pendidikan Agama Islam'],
            ['code' => 'PJK', 'name' => 'Pendidikan Jasmani'],
            ['code' => 'SBD', 'name' => 'Seni Budaya'],
        ];

        foreach ($subjectsData as $subject) {
            Subject::create([
                'school_id'   => $school->id,
                'code'        => $subject['code'],
                'name'        => $subject['name'],
                'is_active'   => true,
            ]);
        }

        // ─────────────────────────────────────────────
        //  6. Users — One per role
        // ─────────────────────────────────────────────
        $defaultPassword = Hash::make('123456');

        // 6.1 Superadmin
        User::create([
            'name'       => 'Super Admin',
            'email'      => 'superadmin@mail.com',
            'password'   => $defaultPassword,
            'role_id'    => $roles['superadmin']->id,
            'is_active'  => true,
        ]);

        // 6.2 Admin Yayasan
        User::create([
            'name'          => 'Admin Yayasan',
            'email'         => 'adminyayasan@mail.com',
            'password'      => $defaultPassword,
            'role_id'       => $roles['admin_yayasan']->id,
            'foundation_id' => $foundation->id,
            'is_active'     => true,
        ]);

        // 6.3 Kepala Sekolah
        $kepalaSekolah = User::create([
            'name'      => 'Kepala Sekolah',
            'email'     => 'kepalasekolah@mail.com',
            'password'  => $defaultPassword,
            'role_id'   => $roles['kepala_sekolah']->id,
            'school_id' => $school->id,
            'is_active' => true,
        ]);

        TeacherProfile::create([
            'user_id'           => $kepalaSekolah->id,
            'nip_nuptk'         => '1980010120050101',
            'birth_place'       => 'Jakarta',
            'birth_date'        => '1980-01-01',
            'gender'            => 'male',
            'religion'          => 'Islam',
            'last_education'    => 'S2',
            'position'          => 'Kepala Sekolah',
            'employment_status' => 'Tetap',
            'join_date'         => '2005-01-01',
        ]);

        // 6.4 Admin Sekolah
        $adminSekolah = User::create([
            'name'      => 'Admin Sekolah',
            'email'     => 'adminsekolah@mail.com',
            'password'  => $defaultPassword,
            'role_id'   => $roles['admin_sekolah']->id,
            'school_id' => $school->id,
            'is_active' => true,
        ]);

        TeacherProfile::create([
            'user_id'           => $adminSekolah->id,
            'birth_place'       => 'Bekasi',
            'birth_date'        => '1985-05-10',
            'gender'            => 'female',
            'religion'          => 'Islam',
            'last_education'    => 'S1',
            'position'          => 'Admin Sekolah',
            'employment_status' => 'Tetap',
            'join_date'         => '2015-07-01',
        ]);

        // 6.5 Tata Usaha
        $tataUsaha = User::create([
            'name'      => 'Tata Usaha',
            'email'     => 'tatausaha@mail.com',
            'password'  => $defaultPassword,
            'role_id'   => $roles['tata_usaha']->id,
            'school_id' => $school->id,
            'is_active' => true,
        ]);

        TeacherProfile::create([
            'user_id'           => $tataUsaha->id,
            'birth_place'       => 'Depok',
            'birth_date'        => '1988-03-20',
            'gender'            => 'female',
            'religion'          => 'Islam',
            'last_education'    => 'D3',
            'position'          => 'Tata Usaha',
            'employment_status' => 'Kontrak',
            'join_date'         => '2018-01-15',
        ]);

        // 6.6 Guru
        $guru = User::create([
            'name'      => 'Guru Pengajar',
            'email'     => 'guru@mail.com',
            'password'  => $defaultPassword,
            'role_id'   => $roles['guru']->id,
            'school_id' => $school->id,
            'is_active' => true,
        ]);

        TeacherProfile::create([
            'user_id'           => $guru->id,
            'nip_nuptk'         => '1990020220120201',
            'birth_place'       => 'Bandung',
            'birth_date'        => '1990-02-02',
            'gender'            => 'male',
            'religion'          => 'Islam',
            'last_education'    => 'S1',
            'position'          => 'Guru',
            'employment_status' => 'Tetap',
            'join_date'         => '2012-02-01',
        ]);

        // 6.7 Wali Kelas
        $waliKelas = User::create([
            'name'      => 'Wali Kelas',
            'email'     => 'walikelas@mail.com',
            'password'  => $defaultPassword,
            'role_id'   => $roles['wali_kelas']->id,
            'school_id' => $school->id,
            'is_active' => true,
        ]);

        TeacherProfile::create([
            'user_id'           => $waliKelas->id,
            'nip_nuptk'         => '1992030320140301',
            'birth_place'       => 'Surabaya',
            'birth_date'        => '1992-03-03',
            'gender'            => 'female',
            'religion'          => 'Kristen',
            'last_education'    => 'S1',
            'position'          => 'Guru',
            'employment_status' => 'Tetap',
            'join_date'         => '2014-03-01',
        ]);

        // ─────────────────────────────────────────────
        //  7. Classroom (Kelas) — assigned to wali kelas
        // ─────────────────────────────────────────────
        $classroom = Classroom::create([
            'school_id'           => $school->id,
            'academic_year_id'    => $academicYear->id,
            'name'                => '2-D',
            'grade'               => 2,
            'homeroom_teacher_id' => $waliKelas->id,
            'capacity'            => 30,
        ]);

        // ─────────────────────────────────────────────
        //  8. Siswa
        // ─────────────────────────────────────────────
        $siswa = User::create([
            'name'      => 'Ahmad Wibowo',
            'email'     => 'siswa@mail.com',
            'password'  => $defaultPassword,
            'role_id'   => $roles['siswa']->id,
            'school_id' => $school->id,
            'is_active' => true,
        ]);

        $studentProfile = StudentProfile::create([
            'user_id'         => $siswa->id,
            'classroom_id'    => $classroom->id,
            'nisn'            => '0012345678',
            'birth_place'     => 'Jakarta',
            'birth_date'      => '2016-08-15',
            'gender'          => 'male',
            'address'         => 'Jl. Mawar No. 10, Bekasi',
            'enrollment_date' => '2022-07-01',
            'status'          => 'active',
        ]);

        // ─────────────────────────────────────────────
        //  9. Orang Tua / Wali
        // ─────────────────────────────────────────────
        $orangTua = User::create([
            'name'      => 'Orang Tua / Wali',
            'email'     => 'orangtua@mail.com',
            'password'  => $defaultPassword,
            'role_id'   => $roles['orang_tua']->id,
            'phone'     => '081234567890',
            'is_active' => true,
        ]);

        $parentProfile = ParentProfile::create([
            'user_id'        => $orangTua->id,
            'nik'            => '3201010101800001',
            'gender'         => 'male',
            'birth_place'    => 'Jakarta',
            'birth_date'     => '1980-05-20',
            'religion'       => 'Islam',
            'last_education' => 'S1',
            'marital_status' => 'Menikah',
            'relationship'   => 'father',
            'occupation'     => 'Wiraswasta',
            'address'        => 'Jl. Mawar No. 10, Bekasi',
        ]);

        // Link parent to student
        $parentProfile->children()->attach($studentProfile->id);
    }
}
