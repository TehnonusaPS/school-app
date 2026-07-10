<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\School;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\SubjectMaterial;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\User;
use Illuminate\Database\Seeder;

class SiswaAkademikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $school = School::where('npsn', '20100001')->first();
        if (!$school) {
            $this->command->error('School not found.');
            return;
        }

        $academicYearEven = AcademicYear::where('school_id', $school->id)
            ->where('is_active', true)
            ->where('semester', 'even')
            ->first();

        $academicYearOdd = AcademicYear::where('school_id', $school->id)
            ->where('semester', 'odd')
            ->first();

        $subjectBin = Subject::where('school_id', $school->id)->where('code', 'BIN')->first();
        $subjectMtk = Subject::where('school_id', $school->id)->where('code', 'MTK')->first();

        $guruUser = User::where('email', 'guru@mail.com')->first();

        $classroomEven = Classroom::where('school_id', $school->id)
            ->where('academic_year_id', $academicYearEven->id)
            ->where('name', '2-D')
            ->first();

        $classroomOdd = Classroom::where('school_id', $school->id)
            ->where('academic_year_id', $academicYearOdd->id)
            ->where('name', '2-D')
            ->first();

        $studentBudi = StudentProfile::where('nisn', '0012345679')->first();

        if (!$studentBudi) {
            $this->command->error('Student Budi Santoso not found.');
            return;
        }

        // ─────────────────────────────────────────────
        // 1. Seed Bahasa Indonesia Materials & Assessments (Semester Even / Genap)
        // ─────────────────────────────────────────────
        if ($classroomEven && $subjectBin && $guruUser) {
            // Material
            $binMatEven = SubjectMaterial::create([
                'school_id' => $school->id,
                'subject_id' => $subjectBin->id,
                'classroom_id' => $classroomEven->id,
                'academic_year_id' => $academicYearEven->id,
                'uploaded_by' => $guruUser->id,
                'title' => 'Bab 1 : Membaca dan Menulis Puisi',
                'file_path' => 'materials/' . $school->id . '/' . $subjectBin->id . '/membaca_puisi.pdf',
                'file_name' => 'Membaca Puisi.pdf',
                'file_type' => 'pdf',
                'file_size' => (int) (1024 * 1024 * 1.5),
                'uploaded_by_name' => $guruUser->name,
                'is_active' => true,
            ]);

            // Assessments
            $tugasBinEven = Assessment::create([
                'school_id' => $school->id,
                'subject_id' => $subjectBin->id,
                'classroom_id' => $classroomEven->id,
                'academic_year_id' => $academicYearEven->id,
                'created_by' => $guruUser->id,
                'material_id' => $binMatEven->id,
                'category' => 'tugas',
                'type' => 'tugas_sekolah',
                'title' => 'Tugas I : Membuat Puisi Bertema Alam',
                'uploaded_by_name' => $guruUser->name,
                'is_active' => true,
            ]);

            AssessmentScore::create([
                'assessment_id' => $tugasBinEven->id,
                'student_id' => $studentBudi->id,
                'score' => 88.50,
            ]);

            $ujianBinEven = Assessment::create([
                'school_id' => $school->id,
                'subject_id' => $subjectBin->id,
                'classroom_id' => $classroomEven->id,
                'academic_year_id' => $academicYearEven->id,
                'created_by' => $guruUser->id,
                'material_id' => $binMatEven->id,
                'category' => 'ujian',
                'type' => 'ujian_harian',
                'title' => 'Ujian Harian I : Struktur Puisi',
                'uploaded_by_name' => $guruUser->name,
                'is_active' => true,
            ]);

            AssessmentScore::create([
                'assessment_id' => $ujianBinEven->id,
                'student_id' => $studentBudi->id,
                'score' => 80.00,
            ]);

            // UTS
            $utsBinEven = Assessment::create([
                'school_id' => $school->id,
                'subject_id' => $subjectBin->id,
                'classroom_id' => $classroomEven->id,
                'academic_year_id' => $academicYearEven->id,
                'created_by' => $guruUser->id,
                'material_id' => null,
                'category' => 'ujian',
                'type' => 'uts',
                'title' => 'UTS Bahasa Indonesia',
                'uploaded_by_name' => $guruUser->name,
                'is_active' => true,
            ]);

            AssessmentScore::create([
                'assessment_id' => $utsBinEven->id,
                'student_id' => $studentBudi->id,
                'score' => 85.00,
            ]);

            // UAS
            $uasBinEven = Assessment::create([
                'school_id' => $school->id,
                'subject_id' => $subjectBin->id,
                'classroom_id' => $classroomEven->id,
                'academic_year_id' => $academicYearEven->id,
                'created_by' => $guruUser->id,
                'material_id' => null,
                'category' => 'ujian',
                'type' => 'uas',
                'title' => 'UAS Bahasa Indonesia',
                'uploaded_by_name' => $guruUser->name,
                'is_active' => true,
            ]);

            AssessmentScore::create([
                'assessment_id' => $uasBinEven->id,
                'student_id' => $studentBudi->id,
                'score' => 90.00,
            ]);
        }

        // ─────────────────────────────────────────────
        // 2. Seed Bahasa Indonesia Materials & Assessments (Semester Odd / Ganjil)
        // ─────────────────────────────────────────────
        if ($classroomOdd && $subjectBin && $guruUser) {
            // Material
            $binMatOdd = SubjectMaterial::create([
                'school_id' => $school->id,
                'subject_id' => $subjectBin->id,
                'classroom_id' => $classroomOdd->id,
                'academic_year_id' => $academicYearOdd->id,
                'uploaded_by' => $guruUser->id,
                'title' => 'Bab 1 : Pengenalan Teks Deskripsi',
                'file_path' => 'materials/' . $school->id . '/' . $subjectBin->id . '/teks_deskripsi.pdf',
                'file_name' => 'Teks Deskripsi.pdf',
                'file_type' => 'pdf',
                'file_size' => (int) (1024 * 1024 * 1.2),
                'uploaded_by_name' => $guruUser->name,
                'is_active' => true,
            ]);

            // Assessments
            $tugasBinOdd = Assessment::create([
                'school_id' => $school->id,
                'subject_id' => $subjectBin->id,
                'classroom_id' => $classroomOdd->id,
                'academic_year_id' => $academicYearOdd->id,
                'created_by' => $guruUser->id,
                'material_id' => $binMatOdd->id,
                'category' => 'tugas',
                'type' => 'tugas_sekolah',
                'title' => 'Tugas I : Mendeskripsikan Hewan Peliharaan',
                'uploaded_by_name' => $guruUser->name,
                'is_active' => true,
            ]);

            AssessmentScore::create([
                'assessment_id' => $tugasBinOdd->id,
                'student_id' => $studentBudi->id,
                'score' => 84.00,
            ]);

            $ujianBinOdd = Assessment::create([
                'school_id' => $school->id,
                'subject_id' => $subjectBin->id,
                'classroom_id' => $classroomOdd->id,
                'academic_year_id' => $academicYearOdd->id,
                'created_by' => $guruUser->id,
                'material_id' => $binMatOdd->id,
                'category' => 'ujian',
                'type' => 'ujian_harian',
                'title' => 'Ujian Harian I : Ciri-ciri Teks Deskripsi',
                'uploaded_by_name' => $guruUser->name,
                'is_active' => true,
            ]);

            AssessmentScore::create([
                'assessment_id' => $ujianBinOdd->id,
                'student_id' => $studentBudi->id,
                'score' => 82.50,
            ]);

            // UTS
            $utsBinOdd = Assessment::create([
                'school_id' => $school->id,
                'subject_id' => $subjectBin->id,
                'classroom_id' => $classroomOdd->id,
                'academic_year_id' => $academicYearOdd->id,
                'created_by' => $guruUser->id,
                'material_id' => null,
                'category' => 'ujian',
                'type' => 'uts',
                'title' => 'UTS Bahasa Indonesia',
                'uploaded_by_name' => $guruUser->name,
                'is_active' => true,
            ]);

            AssessmentScore::create([
                'assessment_id' => $utsBinOdd->id,
                'student_id' => $studentBudi->id,
                'score' => 80.00,
            ]);

            // UAS
            $uasBinOdd = Assessment::create([
                'school_id' => $school->id,
                'subject_id' => $subjectBin->id,
                'classroom_id' => $classroomOdd->id,
                'academic_year_id' => $academicYearOdd->id,
                'created_by' => $guruUser->id,
                'material_id' => null,
                'category' => 'ujian',
                'type' => 'uas',
                'title' => 'UAS Bahasa Indonesia',
                'uploaded_by_name' => $guruUser->name,
                'is_active' => true,
            ]);

            AssessmentScore::create([
                'assessment_id' => $uasBinOdd->id,
                'student_id' => $studentBudi->id,
                'score' => 86.00,
            ]);
        }

        // ─────────────────────────────────────────────
        // 3. Seed UTS & UAS Matematika (Semester Even / Genap)
        // ─────────────────────────────────────────────
        if ($classroomEven && $subjectMtk && $guruUser) {
            // UTS
            $utsMtkEven = Assessment::create([
                'school_id' => $school->id,
                'subject_id' => $subjectMtk->id,
                'classroom_id' => $classroomEven->id,
                'academic_year_id' => $academicYearEven->id,
                'created_by' => $guruUser->id,
                'material_id' => null,
                'category' => 'ujian',
                'type' => 'uts',
                'title' => 'UTS Matematika',
                'uploaded_by_name' => $guruUser->name,
                'is_active' => true,
            ]);

            AssessmentScore::create([
                'assessment_id' => $utsMtkEven->id,
                'student_id' => $studentBudi->id,
                'score' => 92.00,
            ]);

            // UAS
            $uasMtkEven = Assessment::create([
                'school_id' => $school->id,
                'subject_id' => $subjectMtk->id,
                'classroom_id' => $classroomEven->id,
                'academic_year_id' => $academicYearEven->id,
                'created_by' => $guruUser->id,
                'material_id' => null,
                'category' => 'ujian',
                'type' => 'uas',
                'title' => 'UAS Matematika',
                'uploaded_by_name' => $guruUser->name,
                'is_active' => true,
            ]);

            AssessmentScore::create([
                'assessment_id' => $uasMtkEven->id,
                'student_id' => $studentBudi->id,
                'score' => 89.50,
            ]);
        }
    }
}
