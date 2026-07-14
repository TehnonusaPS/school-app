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

        // 1. Create Spp Tariffs
        $tariffs = [];
        $tariffs[] = SppTariff::create([
            'school_id' => $school->id,
            'name' => 'SPP Bulanan',
            'amount' => 1200000,
            'type' => 'mandatory',
        ]);

        $tariffs[] = SppTariff::create([
            'school_id' => $school->id,
            'name' => 'Biaya Praktikum & Laboratorium',
            'amount' => 150000,
            'type' => 'mandatory',
        ]);

        $tariffs[] = SppTariff::create([
            'school_id' => $school->id,
            'name' => 'Kunjungan Lapangan (Field Trip) - Museum',
            'amount' => 100000,
            'type' => 'addon',
        ]);

        // 2. Find students of this school
        $students = User::where('school_id', $school->id)
            ->whereHas('role', function ($q) {
                $q->where('name', 'siswa');
            })->get();

        foreach ($students as $idx => $student) {
            // Generate bills for each student
            
            // Bill 1: SPP Bulanan - Oktober 2023 (unpaid for some, paid for others)
            $bill1Status = ($idx === 0 || $idx === 3) ? 'unpaid' : 'paid';
            $bill1PaidAmount = ($bill1Status === 'paid') ? 1200000 : 0;
            $bill1 = StudentBill::create([
                'student_id' => $student->id,
                'spp_tariff_id' => $tariffs[0]->id,
                'title' => 'SPP Bulanan - Oktober 2023',
                'amount' => 1200000,
                'paid_amount' => $bill1PaidAmount,
                'due_date' => '2023-10-10',
                'status' => $bill1Status,
            ]);

            // Bill 2: Biaya Praktikum (partial for some)
            $bill2Status = ($idx === 0) ? 'partial' : 'paid';
            $bill2PaidAmount = ($bill2Status === 'partial') ? 50000 : 150000;
            $bill2 = StudentBill::create([
                'student_id' => $student->id,
                'spp_tariff_id' => $tariffs[1]->id,
                'title' => 'Lab Fees (IPA & Komputer)',
                'amount' => 150000,
                'paid_amount' => $bill2PaidAmount,
                'due_date' => '2023-09-25',
                'status' => $bill2Status,
            ]);

            // Bill 3: Field Trip (paid for some, unpaid for others)
            $bill3Status = ($idx === 0) ? 'paid' : 'unpaid';
            $bill3PaidAmount = ($bill3Status === 'paid') ? 100000 : 0;
            $bill3 = StudentBill::create([
                'student_id' => $student->id,
                'spp_tariff_id' => $tariffs[2]->id,
                'title' => 'Field Trip ke Bandung',
                'amount' => 100000,
                'paid_amount' => $bill3PaidAmount,
                'due_date' => '2023-09-15',
                'status' => $bill3Status,
            ]);

            // 3. Create payments/transactions
            if ($bill1Status === 'paid') {
                $pay = StudentPayment::create([
                    'student_id' => $student->id,
                    'payment_method' => 'bca',
                    'amount' => 1200000,
                    'reference_number' => 'INV-20231010-' . strtoupper(Str::random(6)),
                    'status' => 'success',
                    'payment_date' => Carbon::parse('2023-10-09 14:30:00'),
                    'verified_by' => $adminSekolah ? $adminSekolah->id : null,
                ]);
                StudentPaymentItem::create([
                    'student_payment_id' => $pay->id,
                    'student_bill_id' => $bill1->id,
                    'amount_paid' => 1200000,
                ]);
            }

            if ($bill2Status === 'paid') {
                $pay = StudentPayment::create([
                    'student_id' => $student->id,
                    'payment_method' => 'mandiri',
                    'amount' => 150000,
                    'reference_number' => 'INV-20230925-' . strtoupper(Str::random(6)),
                    'status' => 'success',
                    'payment_date' => Carbon::parse('2023-09-24 09:15:00'),
                    'verified_by' => $adminSekolah ? $adminSekolah->id : null,
                ]);
                StudentPaymentItem::create([
                    'student_payment_id' => $pay->id,
                    'student_bill_id' => $bill2->id,
                    'amount_paid' => 150000,
                ]);
            } elseif ($bill2Status === 'partial') {
                // partial payment
                $pay = StudentPayment::create([
                    'student_id' => $student->id,
                    'payment_method' => 'dana',
                    'amount' => 50000,
                    'reference_number' => 'INV-20230924-' . strtoupper(Str::random(6)),
                    'status' => 'success',
                    'payment_date' => Carbon::parse('2023-09-24 10:20:00'),
                    'verified_by' => $adminSekolah ? $adminSekolah->id : null,
                ]);
                StudentPaymentItem::create([
                    'student_payment_id' => $pay->id,
                    'student_bill_id' => $bill2->id,
                    'amount_paid' => 50000,
                ]);
            }

            if ($bill3Status === 'paid') {
                $pay = StudentPayment::create([
                    'student_id' => $student->id,
                    'payment_method' => 'gopay',
                    'amount' => 100000,
                    'reference_number' => 'INV-20230915-' . strtoupper(Str::random(6)),
                    'status' => 'success',
                    'payment_date' => Carbon::parse('2023-09-14 11:45:00'),
                    'verified_by' => $adminSekolah ? $adminSekolah->id : null,
                ]);
                StudentPaymentItem::create([
                    'student_payment_id' => $pay->id,
                    'student_bill_id' => $bill3->id,
                    'amount_paid' => 100000,
                ]);
            }

            // Create some pending transactions to populate verification queues
            if ($idx === 0) {
                // Aditya Saputra (siswa) submits a pending payment for verification
                $pay = StudentPayment::create([
                    'student_id' => $student->id,
                    'payment_method' => 'bca',
                    'amount' => 750000,
                    'reference_number' => 'INV-20231012-' . strtoupper(Str::random(6)),
                    'status' => 'pending',
                    'payment_date' => Carbon::now()->subMinutes(30),
                ]);
                StudentPaymentItem::create([
                    'student_payment_id' => $pay->id,
                    'student_bill_id' => $bill1->id,
                    'amount_paid' => 750000,
                ]);
            }

            if ($idx === 3) {
                // Farah Lestari (siswa4) submits a pending payment
                $pay = StudentPayment::create([
                    'student_id' => $student->id,
                    'payment_method' => 'mandiri',
                    'amount' => 750000,
                    'reference_number' => 'INV-20231013-' . strtoupper(Str::random(6)),
                    'status' => 'pending',
                    'payment_date' => Carbon::now()->subMinutes(10),
                ]);
                StudentPaymentItem::create([
                    'student_payment_id' => $pay->id,
                    'student_bill_id' => $bill1->id,
                    'amount_paid' => 750000,
                ]);
            }
        }
    }
}
