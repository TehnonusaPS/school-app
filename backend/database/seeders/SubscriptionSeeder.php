<?php

namespace Database\Seeders;

use App\Models\Foundation;
use App\Models\FoundationPayment;
use App\Models\FoundationSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Default Plans
        $planBasic = SubscriptionPlan::create([
            'name'          => 'Paket Basic',
            'code'          => 'plan_basic',
            'price'         => 500000.00,
            'billing_cycle' => 'monthly',
            'features'      => [
                'School Management (1 School)',
                'Max 150 Students',
                'Standard Academic Features',
                'Email Support',
            ],
            'max_schools'   => 1,
            'max_students'  => 150,
            'is_active'     => true,
        ]);

        $planPremium = SubscriptionPlan::create([
            'name'          => 'Paket Premium',
            'code'          => 'plan_premium',
            'price'         => 1500000.00,
            'billing_cycle' => 'monthly',
            'features'      => [
                'Multi-School Management (Up to 3 Schools)',
                'Max 600 Students',
                'Advanced Academic & Report Cards',
                'Email & WhatsApp Support',
                'Financial & Tuition Fee Modules',
            ],
            'max_schools'   => 3,
            'max_students'  => 600,
            'is_active'     => true,
        ]);

        $planEnterprise = SubscriptionPlan::create([
            'name'          => 'Paket Enterprise',
            'code'          => 'plan_enterprise',
            'price'         => 5000000.00,
            'billing_cycle' => 'monthly',
            'features'      => [
                'Unlimited School Management (Up to 10 Schools)',
                'Max 2500 Students',
                'Full Module Suite Access',
                '24/7 Priority Support',
                'Custom Domain Integration Option',
            ],
            'max_schools'   => 10,
            'max_students'  => 2500,
            'is_active'     => true,
        ]);

        // 2. Find Dummy Foundation
        $foundation = Foundation::first();

        if ($foundation) {
            // Seed Active Subscription
            FoundationSubscription::create([
                'foundation_id'        => $foundation->id,
                'subscription_plan_id' => $planPremium->id,
                'status'               => 'active',
                'starts_at'            => Carbon::now()->startOfMonth()->toDateString(),
                'ends_at'              => Carbon::now()->startOfMonth()->addMonth()->toDateString(),
            ]);

            // Seed Payments
            // Past paid payment (June invoice)
            FoundationPayment::create([
                'foundation_id'        => $foundation->id,
                'subscription_plan_id' => $planPremium->id,
                'invoice_number'       => 'INV-' . Carbon::now()->subMonth()->format('Ymd') . '-0001',
                'amount'               => $planPremium->price,
                'status'               => 'paid',
                'payment_method'       => 'bank_transfer',
                'payment_date'         => Carbon::now()->subMonth()->startOfMonth()->addDays(2)->toDateTimeString(),
                'payment_receipt'      => 'receipts/paid_stub_june.png',
                'notes'                => 'Pembayaran subscription bulan Juni.',
            ]);

            // Future/Pending renewal payment
            FoundationPayment::create([
                'foundation_id'        => $foundation->id,
                'subscription_plan_id' => $planPremium->id,
                'invoice_number'       => 'INV-' . Carbon::now()->format('Ymd') . '-0002',
                'amount'               => $planPremium->price,
                'status'               => 'pending',
                'payment_method'       => 'bank_transfer',
                'payment_date'         => null,
                'payment_receipt'      => 'receipts/receipt_july_pending.png',
                'notes'                => 'Pengajuan perpanjangan lisensi Yayasan untuk bulan depan.',
            ]);
        }
    }
}
