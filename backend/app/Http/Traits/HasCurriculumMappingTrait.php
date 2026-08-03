<?php

namespace App\Http\Traits;

use App\Models\School;
use App\Models\Subject;
use App\Models\SubjectGrade;
use App\Models\Curriculum;
use App\Models\CurriculumSubject;

trait HasCurriculumMappingTrait
{
    /**
     * Auto-populate school subjects from the active curriculum assigned to the school.
     */
    public function syncSchoolSubjectsFromCurriculum(School $school): void
    {
        if (!$school->curriculum_id) {
            return;
        }

        $curriculum = Curriculum::with('curriculumSubjects')->find($school->curriculum_id);
        if (!$curriculum) {
            return;
        }

        foreach ($curriculum->curriculumSubjects as $currSubject) {
            $subject = Subject::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'name'      => $currSubject->name,
                ],
                [
                    'code'        => $currSubject->code,
                    'description' => "Mata Pelajaran Standar Kurikulum (" . $currSubject->level . ")",
                    'is_active'   => true,
                ]
            );

            // Default grade mappings based on curriculum subject configuration or level fallback
            if (!empty($currSubject->default_grades) && is_array($currSubject->default_grades)) {
                $grades = $currSubject->default_grades;
            } elseif ($school->level === 'SD') {
                $grades = [1, 2, 3, 4, 5, 6];
            } elseif ($school->level === 'SMP') {
                $grades = [7, 8, 9];
            } else {
                if ($currSubject->level === 'SD') {
                    $grades = [1, 2, 3, 4, 5, 6];
                } elseif ($currSubject->level === 'SMP') {
                    $grades = [7, 8, 9];
                } else {
                    $grades = [];
                }
            }

            foreach ($grades as $grade) {
                SubjectGrade::firstOrCreate([
                    'subject_id' => $subject->id,
                    'grade'      => $grade,
                ]);
            }
        }
    }

    /**
     * Sync grade levels for a specific subject.
     */
    public function syncSubjectGrades(Subject $subject, array $grades): void
    {
        SubjectGrade::where('subject_id', $subject->id)->delete();

        foreach ($grades as $grade) {
            SubjectGrade::create([
                'subject_id' => $subject->id,
                'grade'      => (int) $grade,
            ]);
        }
    }
}
