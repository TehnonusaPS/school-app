<?php

namespace Tests\Feature;

use App\Models\FoundationPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminFinanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed roles and default plans/data
        $this->seed();
    }

    public function test_superadmin_can_access_finance_dashboard(): void
    {
        $superAdmin = User::whereHas('role', function ($q) {
            $q->where('name', 'superadmin');
        })->first();

        $response = $this->actingAs($superAdmin, 'sanctum')
            ->getJson('/api/superadmin/finance/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'stats' => [
                        'total_revenue',
                        'active_subscriptions_count',
                        'pending_verifications_count',
                        'monthly_recurring_revenue',
                    ],
                    'recent_payments',
                    'recent_subscriptions',
                ],
            ]);
    }

    public function test_non_superadmin_cannot_access_finance_dashboard(): void
    {
        $siswa = User::whereHas('role', function ($q) {
            $q->where('name', 'siswa');
        })->first();

        $response = $this->actingAs($siswa, 'sanctum')
            ->getJson('/api/superadmin/finance/dashboard');

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Forbidden: You do not have the required access role.',
            ]);
    }

    public function test_superadmin_can_crud_subscription_plans(): void
    {
        $superAdmin = User::whereHas('role', function ($q) {
            $q->where('name', 'superadmin');
        })->first();

        // 1. Index
        $response = $this->actingAs($superAdmin, 'sanctum')
            ->getJson('/api/superadmin/finance/plans');
        $response->assertStatus(200);

        // 2. Store
        $newPlanData = [
            'name'          => 'Test Plan',
            'code'          => 'test_plan',
            'price'         => 999000,
            'billing_cycle' => 'monthly',
            'features'      => ['Feature A', 'Feature B'],
            'max_schools'   => 5,
            'max_students'  => 500,
            'is_active'     => true,
        ];
        $response = $this->actingAs($superAdmin, 'sanctum')
            ->postJson('/api/superadmin/finance/plans', $newPlanData);
        $response->assertStatus(201);
        
        $planId = $response->json('data.id');

        // 3. Show
        $response = $this->actingAs($superAdmin, 'sanctum')
            ->getJson("/api/superadmin/finance/plans/{$planId}");
        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Test Plan');

        // 4. Update
        $response = $this->actingAs($superAdmin, 'sanctum')
            ->putJson("/api/superadmin/finance/plans/{$planId}", [
                'name' => 'Updated Test Plan',
            ]);
        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Test Plan');

        // 5. Destroy
        $response = $this->actingAs($superAdmin, 'sanctum')
            ->deleteJson("/api/superadmin/finance/plans/{$planId}");
        $response->assertStatus(200);
    }

    public function test_superadmin_can_verify_pending_payments(): void
    {
        $superAdmin = User::whereHas('role', function ($q) {
            $q->where('name', 'superadmin');
        })->first();

        // Find a pending payment
        $pendingPayment = FoundationPayment::where('status', 'pending')->first();
        $this->assertNotNull($pendingPayment);

        $response = $this->actingAs($superAdmin, 'sanctum')
            ->postJson("/api/superadmin/finance/payments/{$pendingPayment->id}/verify", [
                'status' => 'paid',
                'notes'  => 'Verified by test',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'paid');
    }
}
