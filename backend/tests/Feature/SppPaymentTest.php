<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\StudentPayment;
use App\Models\StudentPaymentItem;
use App\Models\StudentBill;
use App\Models\ParentProfile;
use App\Models\StudentProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SppPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $tataUsaha;
    protected User $student;
    protected StudentPayment $payment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->tataUsaha = User::where('email', 'tatausaha@mail.com')->first();
        $this->student = User::where('email', 'siswa@mail.com')->first();

        // Create a test payment to ensure we have a valid reference
        $this->payment = StudentPayment::create([
            'student_id' => $this->student->id,
            'payment_method' => 'cash',
            'amount' => 1500000,
            'reference_number' => 'INV-TEST-99999',
            'status' => 'success',
            'payment_date' => now(),
            'verified_by' => $this->tataUsaha->id,
            'notes' => 'Test payment'
        ]);
    }

    public function test_tata_usaha_can_list_payments(): void
    {
        $response = $this->actingAs($this->tataUsaha, 'sanctum')
            ->getJson('/api/finance/spp/payments');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => [
                        'id',
                        'student_id',
                        'payment_method',
                        'amount',
                        'reference_number',
                        'status',
                        'payment_date',
                        'student' => [
                            'name',
                            'student_profile' => [
                                'classroom'
                            ]
                        ]
                    ]
                ]
            ]);
    }

    public function test_tata_usaha_can_get_payment_details(): void
    {
        $response = $this->actingAs($this->tataUsaha, 'sanctum')
            ->getJson("/api/finance/spp/payments/{$this->payment->id}");

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.reference_number', 'INV-TEST-99999')
            ->assertJsonPath('data.student.name', $this->student->name);
    }

    public function test_siswa_can_view_own_payment_details(): void
    {
        $response = $this->actingAs($this->student, 'sanctum')
            ->getJson("/api/finance/spp/payments/{$this->payment->id}");

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success');
    }

    public function test_siswa_cannot_view_others_payment_details(): void
    {
        // Create another student
        $otherStudent = User::where('email', 'siswa2@mail.com')->first();
        
        $response = $this->actingAs($otherStudent, 'sanctum')
            ->getJson("/api/finance/spp/payments/{$this->payment->id}");

        $response->assertStatus(403)
            ->assertJsonPath('status', 'error');
    }
}
