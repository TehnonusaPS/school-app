<?php

namespace Tests\Feature;

use App\Models\AcademicCalendarEvent;
use App\Models\AcademicYear;
use App\Models\ExamSession;
use App\Models\Foundation;
use App\Models\Role;
use App\Models\School;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamScheduleTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminSekolah;
    protected User $siswaUser;
    protected School $school;
    protected AcademicYear $academicYear;
    protected AcademicCalendarEvent $event;
    protected Subject $subjectMath;

    protected function setUp(): void
    {
        parent::setUp();

        $adminSekolahRole = Role::firstOrCreate(
            ['name' => 'admin_sekolah'],
            ['label' => 'Admin Sekolah', 'description' => 'Administrator Sekolah']
        );

        $siswaRole = Role::firstOrCreate(
            ['name' => 'siswa'],
            ['label' => 'Siswa', 'description' => 'Peserta Didik']
        );

        $foundation = Foundation::create([
            'code' => 'YTEST',
            'name' => 'Yayasan Test Exam',
        ]);

        $this->school = School::create([
            'foundation_id' => $foundation->id,
            'npsn'          => '99887766',
            'name'          => 'SMA Test Exam',
            'level'         => 'SMA',
        ]);

        $this->adminSekolah = User::create([
            'name'          => 'Admin Sekolah Exam',
            'email'         => 'admin.exam@test.com',
            'password'      => bcrypt('password'),
            'role_id'       => $adminSekolahRole->id,
            'school_id'     => $this->school->id,
            'foundation_id' => $foundation->id,
        ]);

        $this->siswaUser = User::create([
            'name'          => 'Siswa Exam',
            'email'         => 'siswa.exam@test.com',
            'password'      => bcrypt('password'),
            'role_id'       => $siswaRole->id,
            'school_id'     => $this->school->id,
            'foundation_id' => $foundation->id,
        ]);

        $this->academicYear = AcademicYear::create([
            'school_id'  => $this->school->id,
            'name'       => '2026/2027',
            'semester'   => 'Ganjil',
            'start_date' => '2026-07-01',
            'end_date'   => '2026-12-31',
            'is_active'  => true,
        ]);

        $this->event = AcademicCalendarEvent::create([
            'school_id'        => $this->school->id,
            'academic_year_id' => $this->academicYear->id,
            'title'            => 'PTS Semester Ganjil 2026',
            'start_date'       => '2026-10-12',
            'end_date'         => '2026-10-17',
            'type'             => 'ujian',
            'description'      => 'Ujian Tengah Semester',
            'created_by'       => $this->adminSekolah->id,
        ]);

        $this->subjectMath = Subject::create([
            'school_id' => $this->school->id,
            'code'      => 'MTK-10',
            'name'      => 'Matematika',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_bulk_store_and_fetch_exam_schedules(): void
    {
        $payload = [
            'academic_calendar_event_id' => $this->event->id,
            'sessions' => [
                [
                    'exam_date'      => '2026-10-12',
                    'session_number' => 1,
                    'start_time'     => '07:30',
                    'end_time'       => '09:00',
                    'notes'          => 'Sesi 1 Pagi',
                    'subjects'       => [
                        [
                            'subject_id' => $this->subjectMath->id,
                            'grade'      => 10,
                        ]
                    ]
                ],
                [
                    'exam_date'      => '2026-10-12',
                    'session_number' => 2,
                    'start_time'     => '09:30',
                    'end_time'       => '11:00',
                    'notes'          => 'Sesi 2 Siang',
                    'subjects'       => []
                ]
            ]
        ];

        // Bulk store
        $response = $this->actingAs($this->adminSekolah)
            ->postJson('/api/academic-calendar/exam-schedules/bulk', $payload);

        $response->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('exam_sessions', [
            'academic_calendar_event_id' => $this->event->id,
            'exam_date'                  => '2026-10-12 00:00:00',
            'session_number'             => 1,
        ]);

        $this->assertDatabaseHas('exam_session_subjects', [
            'subject_id' => $this->subjectMath->id,
            'grade'      => 10,
        ]);

        // Fetch index
        $indexRes = $this->actingAs($this->adminSekolah)
            ->getJson('/api/academic-calendar/exam-schedules?academic_calendar_event_id=' . $this->event->id);

        $indexRes->assertStatus(200)
            ->assertJsonPath('event.title', 'PTS Semester Ganjil 2026');
    }

    public function test_user_can_fetch_my_schedule(): void
    {
        // First store a session
        $session = ExamSession::create([
            'school_id'                  => $this->school->id,
            'academic_calendar_event_id' => $this->event->id,
            'exam_date'                  => '2026-10-12',
            'session_number'             => 1,
            'start_time'                 => '07:30:00',
            'end_time'                   => '09:00:00',
            'created_by'                 => $this->adminSekolah->id,
        ]);

        $session->sessionSubjects()->create([
            'subject_id' => $this->subjectMath->id,
            'grade'      => 10,
        ]);

        $response = $this->actingAs($this->siswaUser)
            ->getJson('/api/exam-schedules/my-schedule?grade=10');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.subjects.0.subject_name', 'Matematika');
    }

    public function test_admin_can_delete_exam_session(): void
    {
        $session = ExamSession::create([
            'school_id'                  => $this->school->id,
            'academic_calendar_event_id' => $this->event->id,
            'exam_date'                  => '2026-10-12',
            'session_number'             => 1,
            'start_time'                 => '07:30:00',
            'end_time'                   => '09:00:00',
            'created_by'                 => $this->adminSekolah->id,
        ]);

        $response = $this->actingAs($this->adminSekolah)
            ->deleteJson('/api/academic-calendar/exam-schedules/sessions/' . $session->id);

        $response->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $this->assertDatabaseMissing('exam_sessions', ['id' => $session->id]);
    }
}
