<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\School;
use App\Models\SppTariff;
use App\Models\StudentBill;
use App\Models\StudentPayment;
use App\Models\StudentPaymentItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SppSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::first();
        if (!$school) {
            return;
        }

        $classrooms = Classroom::where('school_id', $school->id)->get();
        $adminSekolah = User::where('email', 'adminsekolah@mail.com')->first();

        $mipaClassroom = Classroom::where('school_id', $school->id)->where('name', 'like', '%MIPA%')->first();
        $ipsClassroom = Classroom::where('school_id', $school->id)->where('name', 'like', '%IPS%')->first();

        // 1. Create Spp Tariffs
        $tariffs = [];
        $tariffs[] = SppTariff::create([
            'school_id' => $school->id,
            'name' => 'SPP Bulanan',
            'amount' => 1200000,
            'type' => 'mandatory',
            'classroom_id' => null,
        ]);

        $tariffs[] = SppTariff::create([
            'school_id' => $school->id,
            'name' => 'Biaya Praktikum & Laboratorium',
            'amount' => 150000,
            'type' => 'mandatory',
            'classroom_id' => $mipaClassroom ? $mipaClassroom->id : null,
        ]);

        $tariffs[] = SppTariff::create([
            'school_id' => $school->id,
            'name' => 'Kunjungan Lapangan (Field Trip) - Museum',
            'amount' => 100000,
            'type' => 'addon',
            'classroom_id' => $ipsClassroom ? $ipsClassroom->id : null,
        ]);

        // 2. Find students of this school
        $students = User::where('school_id', $school->id)
            ->whereHas('role', function ($q) {
                $q->where('name', 'siswa');
            })->get();

        foreach ($students as $idx => $student) {
            // Get student's classroom
            $profile = $student->studentProfile;
            $classroomId = $profile ? $profile->classroom_id : null;

            // Filter tariffs that apply to this student
            $applicableTariffs = collect($tariffs)->filter(function ($t) use ($classroomId) {
                return is_null($t->classroom_id) || $t->classroom_id == $classroomId;
            });

            $createdBills = collect();

            foreach ($applicableTariffs as $tariff) {
                // Determine title, amount, due date dynamically
                $title = $tariff->name;
                $dueDate = '2023-10-20';

                if ($tariff->name === 'SPP Bulanan') {
                    $title = 'SPP Bulanan - Oktober 2023';
                    $dueDate = '2023-10-10';
                } elseif (str_contains($tariff->name, 'Praktikum') || str_contains($tariff->name, 'Lab')) {
                    $title = $tariff->name;
                    $dueDate = '2023-09-25';
                } elseif (str_contains($tariff->name, 'Trip') || str_contains($tariff->name, 'Kunjungan')) {
                    $title = $tariff->name;
                    $dueDate = '2023-09-15';
                }

                $createdBills->push(StudentBill::create([
                    'student_id' => $student->id,
                    'spp_tariff_id' => $tariff->id,
                    'title' => $title,
                    'amount' => $tariff->amount,
                    'paid_amount' => 0,
                    'due_date' => $dueDate,
                    'status' => 'unpaid',
                ]));
            }

            // Create pending payments for SPP to populate verification queues
            $sppBill = $createdBills->first(function ($b) {
                return str_contains($b->title, 'SPP');
            });

            if ($sppBill) {
                $pay = StudentPayment::create([
                    'student_id' => $student->id,
                    'payment_method' => 'bca',
                    'amount' => $sppBill->amount,
                    'reference_number' => 'INV-20231012-' . strtoupper(Str::random(6)),
                    'status' => 'pending',
                    'payment_date' => Carbon::now()->subMinutes(30 + $idx * 5),
                ]);
                StudentPaymentItem::create([
                    'student_payment_id' => $pay->id,
                    'student_bill_id' => $sppBill->id,
                    'amount_paid' => $sppBill->amount,
                ]);
            }
        }
    }
}
