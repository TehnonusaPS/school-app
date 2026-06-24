<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Foundation;
use App\Models\FoundationPayment;
use App\Models\FoundationSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FinanceController extends Controller
{
    /**
     * Get dashboard statistics and overview.
     */
    public function indexDashboard()
    {
        $totalRevenue = FoundationPayment::where('status', 'paid')->sum('amount');
        $activeSubsCount = FoundationSubscription::where('status', 'active')->count();
        $pendingPaymentsCount = FoundationPayment::where('status', 'pending')->count();

        // Calculate Monthly Recurring Revenue (MRR) based on active subscriptions
        $mrr = DB::table('foundation_subscriptions')
            ->join('subscription_plans', 'foundation_subscriptions.subscription_plan_id', '=', 'subscription_plans.id')
            ->where('foundation_subscriptions.status', 'active')
            ->select(DB::raw("
                SUM(
                    CASE 
                        WHEN subscription_plans.billing_cycle = 'monthly' THEN subscription_plans.price
                        WHEN subscription_plans.billing_cycle = 'yearly' THEN subscription_plans.price / 12
                        ELSE 0
                    END
                ) as mrr
            "))
            ->first()->mrr ?? 0;

        // Recent transactions
        $recentPayments = FoundationPayment::with(['foundation', 'plan'])
            ->latest()
            ->limit(5)
            ->get();

        // Recent active/trial subscriptions
        $recentSubscriptions = FoundationSubscription::with(['foundation', 'plan'])
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => [
                'stats' => [
                    'total_revenue'              => (float) $totalRevenue,
                    'active_subscriptions_count' => $activeSubsCount,
                    'pending_verifications_count'=> $pendingPaymentsCount,
                    'monthly_recurring_revenue'  => (float) $mrr,
                ],
                'recent_payments'      => $recentPayments,
                'recent_subscriptions' => $recentSubscriptions,
            ],
        ]);
    }

    /**
     * List all foundations.
     */
    public function getFoundations()
    {
        $foundations = Foundation::select('id', 'name', 'code')->get();

        return response()->json([
            'status' => 'success',
            'data'   => $foundations,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Subscription Plans CRUD
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * List all subscription plans.
     */
    public function plansIndex()
    {
        $plans = SubscriptionPlan::all();

        return response()->json([
            'status' => 'success',
            'data'   => $plans,
        ]);
    }

    /**
     * Create a new subscription plan.
     */
    public function plansStore(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'code'          => 'required|string|unique:subscription_plans,code|max:255',
            'price'         => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly,lifetime',
            'features'      => 'nullable|array',
            'features.*'    => 'string',
            'max_schools'   => 'required|integer|min:1',
            'max_students'  => 'required|integer|min:1',
            'is_active'     => 'boolean',
        ]);

        $plan = SubscriptionPlan::create($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Subscription plan created successfully.',
            'data'    => $plan,
        ], 201);
    }

    /**
     * Show details of a subscription plan.
     */
    public function plansShow(SubscriptionPlan $plan)
    {
        return response()->json([
            'status' => 'success',
            'data'   => $plan,
        ]);
    }

    /**
     * Update an existing subscription plan.
     */
    public function plansUpdate(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'name'          => 'string|max:255',
            'code'          => 'string|max:255|unique:subscription_plans,code,' . $plan->id,
            'price'         => 'numeric|min:0',
            'billing_cycle' => 'in:monthly,yearly,lifetime',
            'features'      => 'nullable|array',
            'features.*'    => 'string',
            'max_schools'   => 'integer|min:1',
            'max_students'  => 'integer|min:1',
            'is_active'     => 'boolean',
        ]);

        $plan->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Subscription plan updated successfully.',
            'data'    => $plan,
        ]);
    }

    /**
     * Delete a subscription plan.
     */
    public function plansDestroy(SubscriptionPlan $plan)
    {
        // Check if the plan is currently used by any active subscription
        $activeUsage = FoundationSubscription::where('subscription_plan_id', $plan->id)
            ->where('status', 'active')
            ->exists();

        if ($activeUsage) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot delete plan. It has active subscriptions associated with it.',
            ], 400);
        }

        $plan->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Subscription plan deleted successfully.',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Subscriptions Listing
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * List subscriptions with filtering.
     */
    public function getSubscriptions(Request $request)
    {
        $query = FoundationSubscription::with(['foundation', 'plan']);

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('foundation', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $subscriptions = $query->latest()->paginate(15);

        return response()->json([
            'status' => 'success',
            'data'   => $subscriptions,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Payments & Invoices Management
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * List payment invoices with filtering.
     */
    public function getPayments(Request $request)
    {
        $query = FoundationPayment::with(['foundation', 'plan']);

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('foundation', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhere('invoice_number', 'like', "%{$search}%");
        }

        $payments = $query->latest()->paginate(15);

        return response()->json([
            'status' => 'success',
            'data'   => $payments,
        ]);
    }

    /**
     * Manually create a pending invoice/billing statement for a foundation.
     */
    public function createInvoice(Request $request)
    {
        $validated = $request->validate([
            'foundation_id'        => 'required|exists:foundations,id',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'notes'                => 'nullable|string',
        ]);

        $plan = SubscriptionPlan::findOrFail($validated['subscription_plan_id']);

        // Generate invoice number e.g. INV-20260623-XXXX
        $invoiceNumber = 'INV-' . Carbon::now()->format('Ymd') . '-' . strtoupper(Str::random(6));

        $payment = FoundationPayment::create([
            'foundation_id'        => $validated['foundation_id'],
            'subscription_plan_id' => $validated['subscription_plan_id'],
            'invoice_number'       => $invoiceNumber,
            'amount'               => $plan->price,
            'status'               => 'pending',
            'payment_method'       => 'bank_transfer',
            'notes'                => $validated['notes'] ?? 'Manual billing invoice created by Administrator.',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Invoice created successfully.',
            'data'    => $payment->load(['foundation:id,name', 'plan:id,name']),
        ], 201);
    }

    /**
     * Verify/Approve or Reject a pending payment receipt.
     */
    public function verifyPayment(Request $request, $id)
    {
        $payment = FoundationPayment::findOrFail($id);

        if ($payment->status !== 'pending') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Payment has already been processed and is status: ' . $payment->status,
            ], 400);
        }

        $validated = $request->validate([
            'status' => 'required|in:paid,failed',
            'notes'  => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            if ($validated['status'] === 'paid') {
                $payment->update([
                    'status'       => 'paid',
                    'payment_date' => Carbon::now()->toDateTimeString(),
                    'notes'        => $validated['notes'] ?? $payment->notes,
                ]);

                // Update or create Subscription
                $plan = SubscriptionPlan::findOrFail($payment->subscription_plan_id);
                $durationInMonths = 1; // Default monthly

                if ($plan->billing_cycle === 'yearly') {
                    $durationInMonths = 12;
                } elseif ($plan->billing_cycle === 'lifetime') {
                    $durationInMonths = 1200; // 100 years
                }

                // Check for existing subscription for this foundation
                $activeSubscription = FoundationSubscription::where('foundation_id', $payment->foundation_id)
                    ->whereIn('status', ['active', 'trial'])
                    ->where('ends_at', '>=', Carbon::today()->toDateString())
                    ->first();

                if ($activeSubscription) {
                    // Extend ends_at of existing active subscription if it's the same plan
                    if ($activeSubscription->subscription_plan_id === $payment->subscription_plan_id) {
                        $activeSubscription->update([
                            'ends_at' => Carbon::parse($activeSubscription->ends_at)->addMonths($durationInMonths)->toDateString(),
                        ]);
                    } else {
                        // Plan changed: set old one to expired, start a new one
                        $activeSubscription->update(['status' => 'expired']);

                        FoundationSubscription::create([
                            'foundation_id'        => $payment->foundation_id,
                            'subscription_plan_id' => $payment->subscription_plan_id,
                            'status'               => 'active',
                            'starts_at'            => Carbon::today()->toDateString(),
                            'ends_at'              => Carbon::today()->addMonths($durationInMonths)->toDateString(),
                        ]);
                    }
                } else {
                    // Create new active subscription
                    FoundationSubscription::create([
                        'foundation_id'        => $payment->foundation_id,
                        'subscription_plan_id' => $payment->subscription_plan_id,
                        'status'               => 'active',
                        'starts_at'            => Carbon::today()->toDateString(),
                        'ends_at'              => Carbon::today()->addMonths($durationInMonths)->toDateString(),
                    ]);
                }
            } else {
                // Reject payment (status: failed)
                $payment->update([
                    'status' => 'failed',
                    'notes'  => $validated['notes'] ?? 'Payment receipt verification failed.',
                ]);
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Payment status updated and subscription processed successfully.',
                'data'    => $payment->load(['foundation:id,name', 'plan:id,name']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred during verification: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a subscription.
     */
    public function subscriptionsDestroy(FoundationSubscription $subscription)
    {
        $subscription->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Subscription deleted successfully.',
        ]);
    }
}
