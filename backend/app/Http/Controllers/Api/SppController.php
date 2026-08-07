<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SppTariff;
use App\Models\StudentBill;
use App\Models\StudentPayment;
use App\Models\StudentPaymentItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SppController extends Controller
{
    /**
     * Get dashboard statistics based on the authenticated user's role.
     */
    public function getDashboard(Request $request): JsonResponse
    {
        $user = $request->user();
        $roleName = $user->role->name;

        if (in_array($roleName, ['siswa'])) {
            return $this->getStudentDashboard($user);
        }

        if (in_array($roleName, ['orang_tua'])) {
            return $this->getParentDashboard($user);
        }

        if (in_array($roleName, ['admin_sekolah', 'tata_usaha'])) {
            return $this->getAdminDashboard($user);
        }

        if (in_array($roleName, ['kepala_sekolah'])) {
            return $this->getKepsekDashboard($user);
        }

        if (in_array($roleName, ['superadmin', 'admin_yayasan'])) {
            return $this->getSuperAdminOrYayasanDashboard($user);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Role not authorized for SPP Dashboard.'
        ], 403);
    }

    /**
     * Get Student-specific dashboard data.
     */
    private function getStudentDashboard(User $student): JsonResponse
    {
        $bills = StudentBill::where('student_id', $student->id)
            ->orderBy('due_date', 'asc')
            ->get();

        $outstandingBalance = $bills->where('status', '!=', 'paid')->sum(function ($bill) {
            return $bill->amount - $bill->paid_amount;
        });

        $nextDueBill = $bills->where('status', '!=', 'paid')->first();
        $nextPaymentDue = $nextDueBill ? Carbon::parse($nextDueBill->due_date)->translatedFormat('d F Y') : '-';

        $payments = StudentPayment::where('student_id', $student->id)
            ->with(['verifier'])
            ->orderBy('payment_date', 'desc')
            ->take(10)
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'reference_number' => $payment->reference_number,
                    'payment_method' => $payment->payment_method,
                    'amount' => $payment->amount,
                    'status' => strtoupper($payment->status),
                    'payment_date' => $payment->payment_date->toIso8601String(),
                    'notes' => $payment->notes,
                    'receipt_url' => '#' // Placeholder for receipt download
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'role' => 'siswa',
                'outstanding_balance' => (float)$outstandingBalance,
                'next_payment_due' => $nextPaymentDue,
                'current_bills' => $bills,
                'payment_history' => $payments
            ]
        ]);
    }

    /**
     * Get Parent-specific dashboard data.
     */
    private function getParentDashboard(User $parentUser): JsonResponse
    {
        $parentProfile = $parentUser->parentProfile;
        $childProfile = $parentProfile && $parentProfile->children->isNotEmpty() 
            ? $parentProfile->children->first() 
            : null;

        if (!$childProfile) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'role' => 'orang_tua',
                    'student_name' => '-',
                    'outstanding_balance' => 0,
                    'next_payment_due' => '-',
                    'current_bills' => [],
                    'payment_history' => []
                ]
            ]);
        }

        $student = $childProfile->user;

        $bills = StudentBill::where('student_id', $student->id)
            ->orderBy('due_date', 'asc')
            ->get();

        $outstandingBalance = $bills->where('status', '!=', 'paid')->sum(function ($bill) {
            return $bill->amount - $bill->paid_amount;
        });

        $nextDueBill = $bills->where('status', '!=', 'paid')->first();
        $nextPaymentDue = $nextDueBill ? Carbon::parse($nextDueBill->due_date)->translatedFormat('d F Y') : '-';

        $payments = StudentPayment::where('student_id', $student->id)
            ->orderBy('payment_date', 'desc')
            ->take(10)
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'reference_number' => $payment->reference_number,
                    'payment_method' => $payment->payment_method,
                    'amount' => $payment->amount,
                    'status' => strtoupper($payment->status),
                    'payment_date' => $payment->payment_date->toIso8601String(),
                    'notes' => $payment->notes,
                    'receipt_url' => '#'
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'role' => 'orang_tua',
                'student_name' => $student->name,
                'outstanding_balance' => (float)$outstandingBalance,
                'next_payment_due' => $nextPaymentDue,
                'current_bills' => $bills,
                'payment_history' => $payments
            ]
        ]);
    }

    /**
     * Get Admin Sekolah / Tata Usaha dashboard data.
     */
    private function getAdminDashboard(User $user): JsonResponse
    {
        $schoolId = $user->school_id;

        // Retrieve student user IDs belonging to this school
        $studentUserIds = User::where('school_id', $schoolId)
            ->whereHas('role', function($q) {
                $q->where('name', 'siswa');
            })->pluck('id');

        // Total SPP Terkumpul (Bulan ini)
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $totalSppBulanIni = StudentPayment::whereIn('student_id', $studentUserIds)
            ->where('status', 'success')
            ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        // Pending verifications
        $pendingCount = StudentPayment::whereIn('student_id', $studentUserIds)
            ->where('status', 'pending')
            ->count();

        // Antrian Pembayaran (Pending or recent success payments)
        $antrianPembayaran = StudentPayment::whereIn('student_id', $studentUserIds)
            ->with(['student.studentProfile.classroom'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('payment_date', 'desc')
            ->take(15)
            ->get()
            ->map(function ($payment) {
                $classroomName = $payment->student->studentProfile && $payment->student->studentProfile->classroom 
                    ? $payment->student->studentProfile->classroom->name 
                    : '-';
                
                // Get month descriptor if payment is linked to a bill
                $billTitle = $payment->bills->isNotEmpty() ? $payment->bills->first()->title : 'Oktober 2023';

                return [
                    'id' => $payment->id,
                    'nama_siswa' => $payment->student->name,
                    'initials' => collect(explode(' ', $payment->student->name))->map(fn($n) => substr($n, 0, 1))->take(2)->implode(''),
                    'kelas' => $classroomName,
                    'bulan' => str_replace('SPP Bulanan - ', '', $billTitle),
                    'jumlah' => $payment->amount,
                    'status' => strtoupper($payment->status),
                    'payment_method' => $payment->payment_method,
                    'payment_date' => $payment->payment_date->toIso8601String()
                ];
            });

        // Recent Audit logs / Success transactions
        $auditLogs = StudentPayment::whereIn('student_id', $studentUserIds)
            ->where('status', 'success')
            ->with(['student', 'verifier'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($p) {
                $verifierName = $p->verifier ? $p->verifier->name : 'Sistem (Audit)';
                return [
                    'id' => $p->id,
                    'title' => "Verifikasi Pembayaran Siswa: {$p->student->name}",
                    'description' => "Oleh: {$verifierName} • " . $p->updated_at->translatedFormat('d M Y, H:i'),
                    'amount' => $p->amount,
                    'status' => 'Verified'
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'role' => $user->role->name,
                'kas_kecil' => 4500000,
                'total_spp_bulan_ini' => (float)$totalSppBulanIni,
                'pending_verifikasi_count' => $pendingCount,
                'antrian_pembayaran' => $antrianPembayaran,
                'log_kas_kecil' => $auditLogs
            ]
        ]);
    }

    /**
     * Get Kepala Sekolah dashboard.
     */
    private function getKepsekDashboard(User $user): JsonResponse
    {
        $schoolId = $user->school_id;
        $studentUserIds = User::where('school_id', $schoolId)
            ->whereHas('role', function($q) {
                $q->where('name', 'siswa');
            })->pluck('id');

        $totalIncome = StudentPayment::whereIn('student_id', $studentUserIds)
            ->where('status', 'success')
            ->sum('amount');

        return response()->json([
            'status' => 'success',
            'data' => [
                'role' => 'kepala_sekolah',
                'total_pendapatan' => (float)$totalIncome,
                'beban_operasional' => 180000000,
                'sisa_anggaran' => 650000000,
                'menunggu_persetujuan' => 12
            ]
        ]);
    }

    /**
     * Superadmin or Yayasan dashboard.
     */
    private function getSuperAdminOrYayasanDashboard(User $user): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'role' => $user->role->name,
                'total_yayasan' => 3,
                'total_sekolah' => 15,
                'total_uang_masuk' => 50000000,
                'total_tagihan' => 340000000
            ]
        ]);
    }

    /**
     * Get active bills list.
     */
    public function getBills(Request $request): JsonResponse
    {
        $user = $request->user();

        if (in_array($user->role->name, ['siswa'])) {
            $bills = StudentBill::where('student_id', $user->id)->orderBy('due_date', 'asc')->get();
        } elseif (in_array($user->role->name, ['orang_tua'])) {
            $parentProfile = $user->parentProfile;
            $child = $parentProfile ? $parentProfile->children->first() : null;
            $bills = $child ? StudentBill::where('student_id', $child->user_id)->orderBy('due_date', 'asc')->get() : collect();
        } else {
            // Admin can view specific student bills
            $request->validate([
                'student_id' => 'required|exists:users,id'
            ]);
            $bills = StudentBill::where('student_id', $request->student_id)->orderBy('due_date', 'asc')->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $bills
        ]);
    }

    /**
     * Create a new payment (Online Midtrans or Manual).
     */
    public function createPayment(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'student_id' => 'nullable|exists:users,id',
            'payment_method' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'bill_ids' => 'required|array|min:1',
            'bill_ids.*' => 'exists:student_bills,id',
            'notes' => 'nullable|string'
        ]);

        $studentId = $request->student_id ?? $user->id;

        // Ensure user is authorized to perform payment for this student
        if ($user->role->name === 'siswa' && (int)$user->id !== (int)$studentId) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();

            $paymentMethod = strtolower($request->payment_method);
            $isManualAdmin = $user->hasAnyRole(['admin_sekolah', 'tata_usaha']);
            
            // Generate references
            $refNumber = 'SPP-' . Carbon::now()->format('Ymd') . '-' . strtoupper(Str::random(6));

            // Determine status
            // Admin cash processing is immediately 'success'. Online / E-wallet / VA is 'pending' till callback/verification.
            $status = ($paymentMethod === 'cash' || $isManualAdmin) ? 'success' : 'pending';

            $payment = StudentPayment::create([
                'student_id' => $studentId,
                'payment_method' => $paymentMethod,
                'amount' => $request->amount,
                'reference_number' => $refNumber,
                'status' => $status,
                'payment_date' => Carbon::now(),
                'verified_by' => $isManualAdmin ? $user->id : null,
                'notes' => $request->notes
            ]);

            // Track how much money is allocated to each bill
            $remainingPaymentAmount = $request->amount;
            $itemDetails = [];

            foreach ($request->bill_ids as $billId) {
                $bill = StudentBill::lockForUpdate()->find($billId);
                $unpaidAmount = $bill->amount - $bill->paid_amount;
                
                if ($unpaidAmount <= 0) {
                    continue;
                }

                $allocAmount = min($remainingPaymentAmount, $unpaidAmount);
                if ($allocAmount <= 0) {
                    break;
                }

                StudentPaymentItem::create([
                    'student_payment_id' => $payment->id,
                    'student_bill_id' => $bill->id,
                    'amount_paid' => $allocAmount
                ]);

                $itemDetails[] = [
                    'id' => (string)$bill->id,
                    'price' => (int)$allocAmount,
                    'quantity' => 1,
                    'name' => mb_strimwidth($bill->title, 0, 48, '...')
                ];

                if ($status === 'success') {
                    $bill->paid_amount += $allocAmount;
                    
                    if ($bill->paid_amount >= $bill->amount) {
                        $bill->status = 'paid';
                        
                        // Automatically approve/success any other pending payments for this bill
                        $pendingPaymentIds = StudentPaymentItem::where('student_bill_id', $bill->id)
                            ->pluck('student_payment_id');
                            
                        StudentPayment::whereIn('id', $pendingPaymentIds)
                            ->where('status', 'pending')
                            ->update([
                                'status' => 'success',
                                'verified_by' => $user->id,
                                'notes' => 'Auto-approved on manual payment entry.'
                            ]);
                    } elseif ($bill->paid_amount > 0) {
                        $bill->status = 'partial';
                    }
                    $bill->save();
                }

                $remainingPaymentAmount -= $allocAmount;
            }

            // Midtrans Snap Token Generation for Online Payments
            if ($paymentMethod === 'midtrans') {
                $serverKey = config('midtrans.server_key');
                if (empty($serverKey)) {
                    throw new \Exception('MIDTRANS_SERVER_KEY belum dikonfigurasi di file backend/.env. Silakan isi Server Key Midtrans Sandbox Anda.');
                }

                \Midtrans\Config::$serverKey = $serverKey;
                \Midtrans\Config::$isProduction = (bool) config('midtrans.is_production', false);
                \Midtrans\Config::$isSanitized = (bool) config('midtrans.is_sanitized', true);
                \Midtrans\Config::$is3ds = (bool) config('midtrans.is_3ds', true);

                $params = [
                    'transaction_details' => [
                        'order_id' => $refNumber,
                        'gross_amount' => (int)$request->amount,
                    ],
                    'customer_details' => [
                        'first_name' => $user->name,
                        'email' => $user->email ?? ($user->nisn . '@school.app'),
                    ],
                    'item_details' => $itemDetails,
                    'enabled_payments' => [
                        'credit_card', 'bca_va', 'bni_va', 'bri_va', 'other_va',
                        'gopay', 'shopeepay', 'qris', 'indomaret', 'alfamart'
                    ],
                ];

                try {
                    $snapToken = \Midtrans\Snap::getSnapToken($params);
                    $payment->snap_token = $snapToken;
                    $payment->snap_redirect_url = (\Midtrans\Config::$isProduction ? 'https://app.midtrans.com/snap/v2/vtweb/' : 'https://app.sandbox.midtrans.com/snap/v2/vtweb/') . $snapToken;
                    $payment->save();
                } catch (\Exception $e) {
                    Log::error('Midtrans Snap Token Exception: ' . $e->getMessage());
                    throw new \Exception('Gagal dari Midtrans: ' . $e->getMessage());
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Payment created successfully.',
                'data' => $payment
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify a pending payment queue (Admin Sekolah / Tata Usaha).
     */
    public function verifyPayment(Request $request, string $id): JsonResponse
    {
        $user = $request->user();

        if (!in_array($user->role->name, ['admin_sekolah', 'tata_usaha'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized role'
            ], 403);
        }

        $request->validate([
            'status' => 'required|in:success,failed',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $payment = StudentPayment::lockForUpdate()->findOrFail($id);

            if ($payment->status !== 'pending') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payment is already processed.'
                ], 400);
            }

            $payment->status = $request->status;
            $payment->verified_by = $user->id;
            if ($request->has('notes')) {
                $payment->notes = $request->notes;
            }
            $payment->save();

            // If success, apply the paid amount to each linked bill
            if ($request->status === 'success') {
                $paymentItems = StudentPaymentItem::where('student_payment_id', $payment->id)->get();
                foreach ($paymentItems as $item) {
                    $bill = StudentBill::lockForUpdate()->find($item->student_bill_id);
                    $bill->paid_amount += $item->amount_paid;

                    if ($bill->paid_amount >= $bill->amount) {
                        $bill->status = 'paid';
                    } elseif ($bill->paid_amount > 0) {
                        $bill->status = 'partial';
                    }
                    $bill->save();
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Payment status updated to ' . $request->status
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to verify payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * List all SPP Tariffs settings.
     */
    public function getTariffs(Request $request): JsonResponse
    {
        $user = $request->user();
        $schoolId = $user->school_id;

        $tariffs = SppTariff::where('school_id', $schoolId)
            ->with(['classroom'])
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $tariffs
        ]);
    }

    /**
     * Create a new SPP Tariff.
     */
    public function storeTariff(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $request->validate([
            'name' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'type' => 'required|in:mandatory,addon'
        ]);

        $tariff = SppTariff::create([
            'school_id' => $user->school_id,
            'classroom_id' => $request->classroom_id,
            'name' => $request->name,
            'amount' => $request->amount,
            'type' => $request->type
        ]);

        // Generate student bills automatically for matching students
        $studentQuery = User::where('school_id', $user->school_id)
            ->whereHas('role', function ($q) {
                $q->where('name', 'siswa');
            });

        if ($tariff->classroom_id) {
            $studentQuery->whereHas('studentProfile', function ($q) use ($tariff) {
                $q->where('classroom_id', $tariff->classroom_id);
            });
        }

        $students = $studentQuery->get();

        foreach ($students as $student) {
            StudentBill::create([
                'student_id' => $student->id,
                'spp_tariff_id' => $tariff->id,
                'title' => $tariff->name,
                'amount' => $tariff->amount,
                'paid_amount' => 0,
                'due_date' => Carbon::now()->addDays(15)->format('Y-m-d'),
                'status' => 'unpaid',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $tariff
        ], 201);
    }

    /**
     * Update an SPP Tariff.
     */
    public function updateTariff(Request $request, string $id): JsonResponse
    {
        $tariff = SppTariff::findOrFail($id);

        $request->validate([
            'name' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'type' => 'required|in:mandatory,addon'
        ]);

        $tariff->update($request->only(['name', 'amount', 'classroom_id', 'type']));

        // Update existing unpaid student bills title & amount
        StudentBill::where('spp_tariff_id', $tariff->id)
            ->where('status', 'unpaid')
            ->where('paid_amount', 0)
            ->update([
                'title' => $tariff->name,
                'amount' => $tariff->amount
            ]);

        // Synchronize scope: add eligible students, delete no longer eligible
        $studentQuery = User::where('school_id', $tariff->school_id)
            ->whereHas('role', function ($q) {
                $q->where('name', 'siswa');
            });

        if ($tariff->classroom_id) {
            $studentQuery->whereHas('studentProfile', function ($q) use ($tariff) {
                $q->where('classroom_id', $tariff->classroom_id);
            });
        }

        $eligibleStudentIds = $studentQuery->pluck('id')->toArray();

        // 1. Create bills for newly eligible students
        foreach ($eligibleStudentIds as $sId) {
            $exists = StudentBill::where('student_id', $sId)
                ->where('spp_tariff_id', $tariff->id)
                ->exists();

            if (!$exists) {
                StudentBill::create([
                    'student_id' => $sId,
                    'spp_tariff_id' => $tariff->id,
                    'title' => $tariff->name,
                    'amount' => $tariff->amount,
                    'paid_amount' => 0,
                    'due_date' => Carbon::now()->addDays(15)->format('Y-m-d'),
                    'status' => 'unpaid',
                ]);
            }
        }

        // 2. Remove bills for students no longer eligible (if unpaid)
        StudentBill::where('spp_tariff_id', $tariff->id)
            ->whereNotIn('student_id', $eligibleStudentIds)
            ->where('status', 'unpaid')
            ->where('paid_amount', 0)
            ->delete();

        return response()->json([
            'status' => 'success',
            'data' => $tariff
        ]);
    }

    /**
     * Delete an SPP Tariff.
     */
    public function deleteTariff(string $id): JsonResponse
    {
        $tariff = SppTariff::findOrFail($id);

        // Delete associated unpaid bills
        StudentBill::where('spp_tariff_id', $tariff->id)
            ->where('status', 'unpaid')
            ->where('paid_amount', 0)
            ->delete();

        $tariff->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Tariff and associated unpaid bills deleted successfully.'
        ]);
    }

    /**
     * Get a list of student payments (with search and date filters).
     */
    public function getPayments(Request $request): JsonResponse
    {
        $user = $request->user();
        $roleName = $user->role->name;

        $query = StudentPayment::with(['student.studentProfile.classroom', 'verifier', 'bills']);

        if (in_array($roleName, ['siswa'])) {
            $query->where('student_id', $user->id);
        } elseif (in_array($roleName, ['orang_tua'])) {
            $parentProfile = $user->parentProfile;
            $studentIds = $parentProfile ? $parentProfile->children->pluck('user_id')->toArray() : [];
            $query->whereIn('student_id', $studentIds);
        } elseif (in_array($roleName, ['admin_sekolah', 'tata_usaha'])) {
            $schoolId = $user->school_id;
            $query->whereHas('student', function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            });
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Role not authorized for SPP Payments.'
            ], 403);
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('studentProfile', function ($sp) use ($search) {
                      $sp->where('nisn', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('payment_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('payment_date', '<=', $request->end_date);
        }

        if ($request->filled('payment_method') && $request->payment_method !== 'semua') {
            $query->where('payment_method', strtolower($request->payment_method));
        }

        $payments = $query->orderBy('payment_date', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $payments
        ]);
    }

    /**
     * Get details of a single student payment.
     */
    public function getPaymentDetails(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $roleName = $user->role->name;

        $payment = StudentPayment::with([
            'student.studentProfile.classroom',
            'student.school',
            'verifier',
            'bills'
        ])->findOrFail($id);

        // Authorization check
        if (in_array($roleName, ['siswa'])) {
            if ($payment->student_id !== $user->id) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
            }
        } elseif (in_array($roleName, ['orang_tua'])) {
            $parentProfile = $user->parentProfile;
            $studentIds = $parentProfile ? $parentProfile->children->pluck('user_id')->toArray() : [];
            if (!in_array($payment->student_id, $studentIds)) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
            }
        } elseif (in_array($roleName, ['admin_sekolah', 'tata_usaha'])) {
            if ($payment->student->school_id !== $user->school_id) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized role'], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $payment
        ]);
    }

    /**
     * Handle Midtrans Payment Webhook Notification
     */
    public function handleNotification(Request $request): JsonResponse
    {
        // 1. Handle Test Ping from Midtrans Dashboard
        $orderId = $request->input('order_id');
        $transaction = $request->input('transaction_status');

        if (empty($orderId) && empty($transaction)) {
            return response()->json([
                'status' => 'success',
                'message' => 'Midtrans notification webhook endpoint is active and ready.'
            ], 200);
        }

        try {
            $serverKey = config('midtrans.server_key');
            if (empty($serverKey)) {
                return response()->json(['status' => 'error', 'message' => 'Midtrans server key not configured'], 500);
            }

            \Midtrans\Config::$serverKey = $serverKey;
            \Midtrans\Config::$isProduction = (bool) config('midtrans.is_production', false);

            $type = $request->input('payment_type');
            $fraud = $request->input('fraud_status');

            $payment = StudentPayment::where('reference_number', $orderId)->first();

            if (!$payment) {
                // Return 200 OK so test/mock orders from Midtrans dashboard don't fail HTTP check
                return response()->json([
                    'status' => 'success',
                    'message' => 'Test order notification received (Payment reference not found in database)'
                ], 200);
            }

            if ($transaction == 'capture') {
                if ($type == 'credit_card') {
                    if ($fraud == 'challenge') {
                        $payment->status = 'pending';
                    } else {
                        $payment->status = 'success';
                    }
                }
            } else if ($transaction == 'settlement') {
                $payment->status = 'success';
            } else if ($transaction == 'pending') {
                $payment->status = 'pending';
            } else if ($transaction == 'deny' || $transaction == 'expire' || $transaction == 'cancel') {
                $payment->status = 'failed';
            }

            $payment->save();

            // If payment became success, update associated student bills
            if ($payment->status === 'success') {
                DB::transaction(function () use ($payment) {
                    $paymentItems = StudentPaymentItem::where('student_payment_id', $payment->id)->get();
                    foreach ($paymentItems as $item) {
                        $bill = StudentBill::lockForUpdate()->find($item->student_bill_id);
                        if ($bill) {
                            $bill->paid_amount += $item->amount_paid;
                            if ($bill->paid_amount >= $bill->amount) {
                                $bill->status = 'paid';
                            } else if ($bill->paid_amount > 0) {
                                $bill->status = 'partial';
                            }
                            $bill->save();
                        }
                    }
                });
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Notification processed successfully',
                'payment_status' => $payment->status
            ], 200);

        } catch (\Exception $e) {
            Log::error('Midtrans Webhook Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'success',
                'message' => 'Webhook received with note: ' . $e->getMessage()
            ], 200);
        }
    }
}
