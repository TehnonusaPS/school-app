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
use App\Models\Extracurricular;
use App\Models\TeacherSubjectAssignment;
use App\Models\SubjectMaterial;
use App\Models\Assessment;
use App\Models\AssessmentScore;
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

        $academicYearOdd = AcademicYear::create([
            'school_id'  => $school->id,
            'name'       => '2024/2025',
            'semester'   => 'odd',
            'start_date' => '2024-07-15',
            'end_date'   => '2024-12-20',
            'is_active'  => false,
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

        User::create([
            'name'       => 'Super Admin 2',
            'email'      => 'superadmin2@mail.com',
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

        User::create([
            'name'          => 'Admin Yayasan 2',
            'email'         => 'adminyayasan2@mail.com',
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

        $kepalaSekolah2 = User::create([
            'name'      => 'Kepala Sekolah 2',
            'email'     => 'kepalasekolah2@mail.com',
            'password'  => $defaultPassword,
            'role_id'   => $roles['kepala_sekolah']->id,
            'school_id' => $school->id,
            'is_active' => true,
        ]);

        TeacherProfile::create([
            'user_id'           => $kepalaSekolah2->id,
            'nip_nuptk'         => '1981010120060102',
            'birth_place'       => 'Bandung',
            'birth_date'        => '1981-01-01',
            'gender'            => 'female',
            'religion'          => 'Islam',
            'last_education'    => 'S2',
            'position'          => 'Kepala Sekolah',
            'employment_status' => 'Tetap',
            'join_date'         => '2006-01-01',
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

        $adminSekolah2 = User::create([
            'name'      => 'Admin Sekolah 2',
            'email'     => 'adminsekolah2@mail.com',
            'password'  => $defaultPassword,
            'role_id'   => $roles['admin_sekolah']->id,
            'school_id' => $school->id,
            'is_active' => true,
        ]);

        TeacherProfile::create([
            'user_id'           => $adminSekolah2->id,
            'birth_place'       => 'Tangerang',
            'birth_date'        => '1987-06-12',
            'gender'            => 'male',
            'religion'          => 'Islam',
            'last_education'    => 'S1',
            'position'          => 'Admin Sekolah',
            'employment_status' => 'Tetap',
            'join_date'         => '2016-07-01',
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

        $tataUsaha2 = User::create([
            'name'      => 'Tata Usaha 2',
            'email'     => 'tatausaha2@mail.com',
            'password'  => $defaultPassword,
            'role_id'   => $roles['tata_usaha']->id,
            'school_id' => $school->id,
            'is_active' => true,
        ]);

        TeacherProfile::create([
            'user_id'           => $tataUsaha2->id,
            'birth_place'       => 'Bogor',
            'birth_date'        => '1990-04-15',
            'gender'            => 'male',
            'religion'          => 'Islam',
            'last_education'    => 'D3',
            'position'          => 'Tata Usaha',
            'employment_status' => 'Kontrak',
            'join_date'         => '2019-02-01',
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

        $guru2 = User::create([
            'name'      => 'Guru Pengajar 2',
            'email'     => 'guru2@mail.com',
            'password'  => $defaultPassword,
            'role_id'   => $roles['guru']->id,
            'school_id' => $school->id,
            'is_active' => true,
        ]);

        TeacherProfile::create([
            'user_id'           => $guru2->id,
            'nip_nuptk'         => '1991030320130302',
            'birth_place'       => 'Yogyakarta',
            'birth_date'        => '1991-03-03',
            'gender'            => 'female',
            'religion'          => 'Islam',
            'last_education'    => 'S1',
            'position'          => 'Guru',
            'employment_status' => 'Tetap',
            'join_date'         => '2013-03-01',
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

        $waliKelas2 = User::create([
            'name'      => 'Wali Kelas 2',
            'email'     => 'walikelas2@mail.com',
            'password'  => $defaultPassword,
            'role_id'   => $roles['wali_kelas']->id,
            'school_id' => $school->id,
            'is_active' => true,
        ]);

        TeacherProfile::create([
            'user_id'           => $waliKelas2->id,
            'nip_nuptk'         => '1993040420150402',
            'birth_place'       => 'Malang',
            'birth_date'        => '1993-04-04',
            'gender'            => 'male',
            'religion'          => 'Islam',
            'last_education'    => 'S1',
            'position'          => 'Guru',
            'employment_status' => 'Tetap',
            'join_date'         => '2015-04-01',
        ]);

        // ─────────────────────────────────────────────
        //  7. Classroom (Kelas) — assigned to wali kelas
        // ─────────────────────────────────────────────
        $classroom = Classroom::create([
            'school_id'           => $school->id,
            'academic_year_id'    => $academicYear->id,
            'name'                => '2-D',
            'grade'               => 2,
            'major'               => 'MIPA',
            'room'                => 'R. 101',
            'status'              => 'active',
            'homeroom_teacher_id' => $waliKelas->id,
            'capacity'            => 30,
        ]);

        $classroom2 = Classroom::create([
            'school_id'           => $school->id,
            'academic_year_id'    => $academicYear->id,
            'name'                => '2-E',
            'grade'               => 2,
            'major'               => 'IPS',
            'room'                => 'R. 102',
            'status'              => 'active',
            'homeroom_teacher_id' => $waliKelas2->id,
            'capacity'            => 30,
        ]);

        $classroomOdd = Classroom::create([
            'school_id'           => $school->id,
            'academic_year_id'    => $academicYearOdd->id,
            'name'                => '2-D',
            'grade'               => 2,
            'major'               => 'MIPA',
            'room'                => 'R. 101',
            'status'              => 'active',
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

        $siswa2 = User::create([
            'name'      => 'Budi Santoso',
            'email'     => 'siswa2@mail.com',
            'password'  => $defaultPassword,
            'role_id'   => $roles['siswa']->id,
            'school_id' => $school->id,
            'is_active' => true,
        ]);

        $studentProfile2 = StudentProfile::create([
            'user_id'         => $siswa2->id,
            'classroom_id'    => $classroom->id,
            'nisn'            => '0012345679',
            'birth_place'     => 'Bandung',
            'birth_date'      => '2016-09-12',
            'gender'          => 'male',
            'address'         => 'Jl. Anggrek No. 5, Bekasi',
            'enrollment_date' => '2022-07-01',
            'status'          => 'active',
        ]);

        $siswa3 = User::create([
            'name'      => 'Citra Lestari',
            'email'     => 'siswa3@mail.com',
            'password'  => $defaultPassword,
            'role_id'   => $roles['siswa']->id,
            'school_id' => $school->id,
            'is_active' => true,
        ]);

        $studentProfile3 = StudentProfile::create([
            'user_id'         => $siswa3->id,
            'classroom_id'    => $classroom2->id,
            'nisn'            => '0012345680',
            'birth_place'     => 'Surabaya',
            'birth_date'      => '2016-05-23',
            'gender'          => 'female',
            'address'         => 'Jl. Melati No. 12, Bekasi',
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

        $parentProfile->children()->attach($studentProfile->id);

        $orangTua2 = User::create([
            'name'      => 'Orang Tua / Wali 2',
            'email'     => 'orangtua2@mail.com',
            'password'  => $defaultPassword,
            'role_id'   => $roles['orang_tua']->id,
            'phone'     => '081234567891',
            'is_active' => true,
        ]);

        $parentProfile2 = ParentProfile::create([
            'user_id'        => $orangTua2->id,
            'nik'            => '3201010101800002',
            'gender'         => 'female',
            'birth_place'    => 'Bandung',
            'birth_date'     => '1982-06-15',
            'religion'       => 'Islam',
            'last_education' => 'S1',
            'marital_status' => 'Menikah',
            'relationship'   => 'mother',
            'occupation'     => 'Ibu Rumah Tangga',
            'address'        => 'Jl. Anggrek No. 5, Bekasi',
        ]);

        $parentProfile2->children()->attach($studentProfile2->id);

        $orangTua3 = User::create([
            'name'      => 'Orang Tua / Wali 3',
            'email'     => 'orangtua3@mail.com',
            'password'  => $defaultPassword,
            'role_id'   => $roles['orang_tua']->id,
            'phone'     => '081234567892',
            'is_active' => true,
        ]);

        $parentProfile3 = ParentProfile::create([
            'user_id'        => $orangTua3->id,
            'nik'            => '3201010101800003',
            'gender'         => 'male',
            'birth_place'    => 'Surabaya',
            'birth_date'     => '1979-11-02',
            'religion'       => 'Kristen',
            'last_education' => 'S1',
            'marital_status' => 'Menikah',
            'relationship'   => 'father',
            'occupation'     => 'PNS',
            'address'        => 'Jl. Melati No. 12, Bekasi',
        ]);

        $parentProfile3->children()->attach($studentProfile3->id);

        // ─────────────────────────────────────────────
        //  10. Subscriptions & Payments
        // ─────────────────────────────────────────────
        $this->call(SubscriptionSeeder::class);

        // ─────────────────────────────────────────────
        //  11. Extracurriculars
        // ─────────────────────────────────────────────
        $extracurriculars = [
            ['name' => 'Pramuka', 'description' => 'Kegiatan kepramukaan untuk mengembangkan karakter, kemandirian, dan jiwa kepemimpinan siswa.', 'is_active' => true],
            ['name' => 'Paduan Suara', 'description' => 'Ekstrakulikuler paduan suara untuk mengembangkan bakat seni musik dan vokal siswa.', 'is_active' => true],
            ['name' => 'Futsal', 'description' => 'Kegiatan olahraga futsal untuk melatih kerjasama tim dan kebugaran fisik siswa.', 'is_active' => true],
            ['name' => 'Seni Tari', 'description' => 'Ekstrakulikuler tari tradisional dan modern untuk mengembangkan apresiasi seni budaya Indonesia.', 'is_active' => false],
            ['name' => 'Robotika', 'description' => 'Kegiatan belajar pemrograman dan perakitan robot sederhana untuk melatih berpikir logis dan kreatif.', 'is_active' => true],
            ['name' => 'Jurnalistik', 'description' => 'Ekstrakulikuler penulisan berita dan fotografi untuk mengembangkan kemampuan komunikasi dan literasi siswa.', 'is_active' => false],
        ];

        foreach ($extracurriculars as $ekskul) {
            Extracurricular::create([
                'school_id'   => $school->id,
                'name'        => $ekskul['name'],
                'description' => $ekskul['description'],
                'is_active'   => $ekskul['is_active'],
            ]);
        }

        // Query the seeded entities for akademik assignments & samples
        $subjectMtk = Subject::where('code', 'MTK')->first();
        $subjectBin = Subject::where('code', 'BIN')->first();
        $subjectIpa = Subject::where('code', 'IPA')->first();

        $guruUser = User::where('email', 'guru@mail.com')->first();
        $guru2User = User::where('email', 'guru2@mail.com')->first();

        $classroomObj = Classroom::where('name', '2-D')->first();
        $classroom2Obj = Classroom::where('name', '2-E')->first();

        // 1. Teacher subject assignments
        // Even Semester (classroom)
        if ($guruUser && $subjectMtk && $classroom) {
            TeacherSubjectAssignment::create([
                'school_id' => $school->id,
                'teacher_id' => $guruUser->id,
                'subject_id' => $subjectMtk->id,
                'classroom_id' => $classroom->id,
                'academic_year_id' => $academicYear->id,
                'is_active' => true,
            ]);
        }

        if ($guruUser && $subjectBin && $classroom) {
            TeacherSubjectAssignment::create([
                'school_id' => $school->id,
                'teacher_id' => $guruUser->id,
                'subject_id' => $subjectBin->id,
                'classroom_id' => $classroom->id,
                'academic_year_id' => $academicYear->id,
                'is_active' => true,
            ]);
        }

        if ($guruUser && $subjectMtk && $classroom2) {
            TeacherSubjectAssignment::create([
                'school_id' => $school->id,
                'teacher_id' => $guruUser->id,
                'subject_id' => $subjectMtk->id,
                'classroom_id' => $classroom2->id,
                'academic_year_id' => $academicYear->id,
                'is_active' => true,
            ]);
        }

        if ($guru2User && $subjectIpa && $classroom2) {
            TeacherSubjectAssignment::create([
                'school_id' => $school->id,
                'teacher_id' => $guru2User->id,
                'subject_id' => $subjectIpa->id,
                'classroom_id' => $classroom2->id,
                'academic_year_id' => $academicYear->id,
                'is_active' => true,
            ]);
        }

        // Odd Semester (classroomOdd)
        if ($guruUser && $subjectMtk && $classroomOdd) {
            TeacherSubjectAssignment::create([
                'school_id' => $school->id,
                'teacher_id' => $guruUser->id,
                'subject_id' => $subjectMtk->id,
                'classroom_id' => $classroomOdd->id,
                'academic_year_id' => $academicYearOdd->id,
                'is_active' => true,
            ]);
        }

        if ($guruUser && $subjectBin && $classroomOdd) {
            TeacherSubjectAssignment::create([
                'school_id' => $school->id,
                'teacher_id' => $guruUser->id,
                'subject_id' => $subjectBin->id,
                'classroom_id' => $classroomOdd->id,
                'academic_year_id' => $academicYearOdd->id,
                'is_active' => true,
            ]);
        }

        // 2. Subject Materials (Sample Metadata)
        // Even Semester (classroom)
        $mtkMat1Even = null;
        $mtkMat2Even = null;
        if ($guruUser && $subjectMtk && $classroom) {
            $mtkMat1Even = SubjectMaterial::create([
                'school_id' => $school->id,
                'subject_id' => $subjectMtk->id,
                'classroom_id' => $classroom->id,
                'academic_year_id' => $academicYear->id,
                'uploaded_by' => $guruUser->id,
                'title' => 'Bab 1 : Pengenalan Aljabar Dasar',
                'file_path' => 'materials/' . $school->id . '/' . $subjectMtk->id . '/aljabar_dasar.pdf',
                'file_name' => 'Aljabar Dasar.pdf',
                'file_type' => 'pdf',
                'file_size' => 1024 * 1024 * 2, // 2MB
                'uploaded_by_name' => $guruUser->name,
                'is_active' => true,
            ]);

            $mtkMat2Even = SubjectMaterial::create([
                'school_id' => $school->id,
                'subject_id' => $subjectMtk->id,
                'classroom_id' => $classroom->id,
                'academic_year_id' => $academicYear->id,
                'uploaded_by' => $guruUser->id,
                'title' => 'Bab 2 : Himpunan & Diagram Venn',
                'file_path' => 'materials/' . $school->id . '/' . $subjectMtk->id . '/himpunan.pptx',
                'file_name' => 'Himpunan.pptx',
                'file_type' => 'pptx',
                'file_size' => 1024 * 1024 * 5, // 5MB
                'uploaded_by_name' => $guruUser->name,
                'is_active' => true,
            ]);
        }

        // Odd Semester (classroomOdd)
        $mtkMat1Odd = null;
        $mtkMat2Odd = null;
        if ($guruUser && $subjectMtk && $classroomOdd) {
            $mtkMat1Odd = SubjectMaterial::create([
                'school_id' => $school->id,
                'subject_id' => $subjectMtk->id,
                'classroom_id' => $classroomOdd->id,
                'academic_year_id' => $academicYearOdd->id,
                'uploaded_by' => $guruUser->id,
                'title' => 'Bab 1 : Review Matematika Dasar',
                'file_path' => 'materials/' . $school->id . '/' . $subjectMtk->id . '/review_dasar.pdf',
                'file_name' => 'Review Dasar.pdf',
                'file_type' => 'pdf',
                'file_size' => 1024 * 1024 * 1, // 1MB
                'uploaded_by_name' => $guruUser->name,
                'is_active' => true,
            ]);

            $mtkMat2Odd = SubjectMaterial::create([
                'school_id' => $school->id,
                'subject_id' => $subjectMtk->id,
                'classroom_id' => $classroomOdd->id,
                'academic_year_id' => $academicYearOdd->id,
                'uploaded_by' => $guruUser->id,
                'title' => 'Bab 2 : Geometri Sederhana',
                'file_path' => 'materials/' . $school->id . '/' . $subjectMtk->id . '/geometri.pptx',
                'file_name' => 'Geometri.pptx',
                'file_type' => 'pptx',
                'file_size' => 1024 * 1024 * 3, // 3MB
                'uploaded_by_name' => $guruUser->name,
                'is_active' => true,
            ]);
        }

        // 3. Assessments (Sample Tugas & Ujian)
        // Even Semester (classroom)
        $studentsInClass = StudentProfile::where('classroom_id', $classroom->id)->get();

        if ($guruUser && $subjectMtk && $classroom) {
            $tugas = Assessment::create([
                'school_id' => $school->id,
                'subject_id' => $subjectMtk->id,
                'classroom_id' => $classroom->id,
                'academic_year_id' => $academicYear->id,
                'created_by' => $guruUser->id,
                'material_id' => $mtkMat1Even ? $mtkMat1Even->id : null,
                'category' => 'tugas',
                'type' => 'tugas_sekolah',
                'title' => 'Tugas I : Latihan Aljabar Dasar',
                'uploaded_by_name' => $guruUser->name,
                'is_active' => true,
            ]);

            foreach ($studentsInClass as $st) {
                AssessmentScore::create([
                    'assessment_id' => $tugas->id,
                    'student_id' => $st->id,
                    'score' => $st->nisn === '0012345678' ? 85.00 : 90.00,
                ]);
            }

            $ujian = Assessment::create([
                'school_id' => $school->id,
                'subject_id' => $subjectMtk->id,
                'classroom_id' => $classroom->id,
                'academic_year_id' => $academicYear->id,
                'created_by' => $guruUser->id,
                'material_id' => $mtkMat1Even ? $mtkMat1Even->id : null,
                'category' => 'ujian',
                'type' => 'ujian_harian',
                'title' => 'Ujian Harian I : Aljabar',
                'uploaded_by_name' => $guruUser->name,
                'is_active' => true,
            ]);

            foreach ($studentsInClass as $st) {
                AssessmentScore::create([
                    'assessment_id' => $ujian->id,
                    'student_id' => $st->id,
                    'score' => $st->nisn === '0012345678' ? 78.50 : 88.00,
                ]);
            }
        }

        // Odd Semester (classroomOdd)
        if ($guruUser && $subjectMtk && $classroomOdd) {
            // Assessment 1: Tugas Sekolah tied to Bab 1
            $tugasOdd1 = Assessment::create([
                'school_id' => $school->id,
                'subject_id' => $subjectMtk->id,
                'classroom_id' => $classroomOdd->id,
                'academic_year_id' => $academicYearOdd->id,
                'created_by' => $guruUser->id,
                'material_id' => $mtkMat1Odd ? $mtkMat1Odd->id : null,
                'category' => 'tugas',
                'type' => 'tugas_sekolah',
                'title' => 'Tugas I : Latihan Penjumlahan Dasar',
                'uploaded_by_name' => $guruUser->name,
                'is_active' => true,
            ]);

            foreach ($studentsInClass as $st) {
                AssessmentScore::create([
                    'assessment_id' => $tugasOdd1->id,
                    'student_id' => $st->id,
                    'score' => $st->nisn === '0012345678' ? 80.00 : 85.00,
                ]);
            }

            // Assessment 2: Tugas Sekolah tied to Bab 2
            $tugasOdd2 = Assessment::create([
                'school_id' => $school->id,
                'subject_id' => $subjectMtk->id,
                'classroom_id' => $classroomOdd->id,
                'academic_year_id' => $academicYearOdd->id,
                'created_by' => $guruUser->id,
                'material_id' => $mtkMat2Odd ? $mtkMat2Odd->id : null,
                'category' => 'tugas',
                'type' => 'tugas_sekolah',
                'title' => 'Tugas II : Geometri Segitiga',
                'uploaded_by_name' => $guruUser->name,
                'is_active' => true,
            ]);

            foreach ($studentsInClass as $st) {
                AssessmentScore::create([
                    'assessment_id' => $tugasOdd2->id,
                    'student_id' => $st->id,
                    'score' => $st->nisn === '0012345678' ? 85.00 : 92.00,
                ]);
            }

            // Assessment 3: Ujian Harian tied to Bab 1
            $ujianOdd1 = Assessment::create([
                'school_id' => $school->id,
                'subject_id' => $subjectMtk->id,
                'classroom_id' => $classroomOdd->id,
                'academic_year_id' => $academicYearOdd->id,
                'created_by' => $guruUser->id,
                'material_id' => $mtkMat1Odd ? $mtkMat1Odd->id : null,
                'category' => 'ujian',
                'type' => 'ujian_harian',
                'title' => 'Ujian Harian I : Aritmatika',
                'uploaded_by_name' => $guruUser->name,
                'is_active' => true,
            ]);

            foreach ($studentsInClass as $st) {
                AssessmentScore::create([
                    'assessment_id' => $ujianOdd1->id,
                    'student_id' => $st->id,
                    'score' => $st->nisn === '0012345678' ? 75.00 : 80.00,
                ]);
            }

            // Assessment 4: UTS (No material)
            $utsOdd = Assessment::create([
                'school_id' => $school->id,
                'subject_id' => $subjectMtk->id,
                'classroom_id' => $classroomOdd->id,
                'academic_year_id' => $academicYearOdd->id,
                'created_by' => $guruUser->id,
                'material_id' => null,
                'category' => 'ujian',
                'type' => 'uts',
                'title' => 'UTS',
                'uploaded_by_name' => $guruUser->name,
                'is_active' => true,
            ]);

            foreach ($studentsInClass as $st) {
                AssessmentScore::create([
                    'assessment_id' => $utsOdd->id,
                    'student_id' => $st->id,
                    'score' => $st->nisn === '0012345678' ? 90.00 : 94.00,
                ]);
            }

            // Assessment 5: UAS (No material)
            $uasOdd = Assessment::create([
                'school_id' => $school->id,
                'subject_id' => $subjectMtk->id,
                'classroom_id' => $classroomOdd->id,
                'academic_year_id' => $academicYearOdd->id,
                'created_by' => $guruUser->id,
                'material_id' => null,
                'category' => 'ujian',
                'type' => 'uas',
                'title' => 'UAS',
                'uploaded_by_name' => $guruUser->name,
                'is_active' => true,
            ]);

            foreach ($studentsInClass as $st) {
                AssessmentScore::create([
                    'assessment_id' => $uasOdd->id,
                    'student_id' => $st->id,
                    'score' => $st->nisn === '0012345678' ? 88.00 : 91.00,
                ]);
            }
        }
    }
}
