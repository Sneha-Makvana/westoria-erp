<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\BankMaster;
use App\Models\CustomerDeposit;
use App\Models\Order;
use App\Models\PaymentIn;
use App\Models\PaymentStore;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentInController extends Controller
{
    public function index()
    {
        $branchId = $this->branchId();
        $setting = Setting::where('branch_id', $branchId)->first();
        $currencySymbol = $setting->currency_symbol ?? '₹';
        $currencyPosition = $setting->currency_position ?? 'left';

        $customers = User::query()
            ->select('id', 'name', 'phone', 'company_name')
            ->where('role', 'customer')
            ->where('isDeleted', 0)
            ->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            })
            ->whereRaw('LOWER(name) <> ?', ['default customer'])
            ->orderBy('name')
            ->get();

        $banks = BankMaster::query()
            ->where('branch_id', $branchId)
            ->where('isDeleted', 0)
            ->where('status', 1)
            ->orderBy('bank_name')
            ->get();

        $receipts = PaymentIn::query()
            ->with(['customer:id,name,phone'])
            ->where('branch_id', $branchId)
            ->latest('id')
            ->limit(50)
            ->get();

        return view('paymentin.index', compact(
            'customers',
            'banks',
            'receipts',
            'currencySymbol',
            'currencyPosition'
        ));
    }

    public function customerData(int $customer): JsonResponse
    {
        $branchId = $this->branchId();
        $user = User::query()
            ->where('id', $customer)
            ->where('role', 'customer')
            ->where('isDeleted', 0)
            ->first();

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Customer not found.'], 404);
        }

        $orders = $this->pendingOrders($customer, $branchId);
        $balance = CustomerDeposit::balanceFor($customer, $branchId);

        return response()->json([
            'status' => true,
            'customer_id' => $customer,
            'customer_name' => $user->company_name ?: $user->name,
            'balance' => $balance,
            'pending_total' => round($orders->sum('remaining_amount'), 2),
            'orders' => $orders->values(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|integer|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,online',
            'bank_id' => 'nullable|integer|exists:bank_master,id',
            'payment_date' => 'nullable|date',
            'settle_old_bills' => 'required|in:0,1',
            'remarks' => 'nullable|string|max:500',
        ]);

        $branchId = $this->branchId();
        $customer = User::where('id', $validated['customer_id'])->where('role', 'customer')->first();
        if (!$customer || strcasecmp((string) $customer->name, 'Default Customer') === 0) {
            return back()->withInput()->with('error', 'Select a real customer.');
        }

        $method = $validated['payment_method'];
        if ($method === 'online' && empty($validated['bank_id'])) {
            return back()->withInput()->with('error', 'Select a bank for online payment.');
        }

        $amount = round((float) $validated['amount'], 2);
        $settle = (int) $validated['settle_old_bills'] === 1;
        $authUser = Auth::user();
        $settledAmount = 0.0;
        $depositAmount = $amount;

        try {
            DB::transaction(function () use ($validated, $branchId, $customer, $method, $amount, $settle, $authUser, $request, &$settledAmount, &$depositAmount) {
                $settledAmount = 0.0;
                $depositAmount = $amount;
                $pendingOrders = $this->pendingOrders((int) $customer->id, $branchId);

                if ($settle && $pendingOrders->isNotEmpty()) {
                    $remainingToDistribute = $amount;
                    foreach ($pendingOrders as $orderData) {
                        if ($remainingToDistribute <= 0) {
                            break;
                        }
                        $order = Order::lockForUpdate()->find($orderData['id']);
                        if (!$order) {
                            continue;
                        }
                        $orderRemaining = $this->orderRemaining($order);
                        if ($orderRemaining <= 0) {
                            continue;
                        }
                        $payNow = min($remainingToDistribute, $orderRemaining);
                        $newRemaining = round(max(0, $orderRemaining - $payNow), 2);

                        PaymentStore::create([
                            'user_id' => $customer->id,
                            'order_id' => $order->id,
                            'payment_amount' => $payNow,
                            'payment_date' => $request->filled('payment_date') ? $request->payment_date : now('Asia/Kolkata')->toDateString(),
                            'payment_method' => $method,
                            'payment_type' => $newRemaining <= 0 ? 'fully' : 'partially',
                            'cash_amount' => $method === 'cash' ? $payNow : 0,
                            'upi_amount' => $method === 'online' ? $payNow : 0,
                            'remaining_amount' => $newRemaining,
                            'status' => 'credit',
                            'bank_id' => $method === 'online' ? ($validated['bank_id'] ?? null) : null,
                            'remarks' => $validated['remarks'] ?? 'Payment-In',
                            'isDeleted' => 0,
                            'created_at' => now(),
                        ]);

                        $order->update([
                            'remaining_amount' => $newRemaining,
                            'payment_status' => $newRemaining <= 0 ? 'completed' : 'partially',
                        ]);

                        $settledAmount += $payNow;
                        $remainingToDistribute -= $payNow;
                    }
                    $depositAmount = round(max(0, $amount - $settledAmount), 2);
                }

                if ($depositAmount > 0) {
                    $notes = 'Payment-In' . (!empty($validated['remarks']) ? (': ' . $validated['remarks']) : '');
                    CustomerDeposit::credit(
                        (int) $customer->id,
                        $depositAmount,
                        $branchId,
                        null,
                        'payment_in',
                        mb_substr($notes, 0, 255),
                        $authUser?->id
                    );
                }

                PaymentIn::create([
                    'receipt_number' => 'PIN-' . now()->format('ymdHis') . substr((string) mt_rand(100, 999), 0, 3),
                    'customer_id' => $customer->id,
                    'branch_id' => $branchId,
                    'amount' => $amount,
                    'payment_method' => $method,
                    'bank_id' => $method === 'online' ? ($validated['bank_id'] ?? null) : null,
                    'payment_date' => $request->filled('payment_date') ? $request->payment_date : now('Asia/Kolkata')->toDateString(),
                    'settle_old_bills' => $settle,
                    'settled_amount' => round($settledAmount, 2),
                    'deposit_amount' => $depositAmount,
                    'remarks' => $validated['remarks'] ?? null,
                    'created_by' => $authUser?->id,
                ]);
            });
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage() ?: 'Could not save Payment-In.');
        }

        $success = 'Payment-In saved.';
        if ($settle && $settledAmount > 0 && $depositAmount > 0) {
            $success = 'Payment-In saved. Settled old bills and extra amount added to customer balance.';
        } elseif ($settle && $settledAmount > 0) {
            $success = 'Payment-In saved. Amount applied to old bills.';
        } else {
            $success = 'Payment-In saved. Amount added to customer balance.';
        }

        return redirect()->route('paymentin.index')->with('success', $success);
    }

    private function pendingOrders(int $customerId, int $branchId)
    {
        $orders = Order::query()
            ->where('isDeleted', 0)
            ->where('user_id', $customerId)
            ->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            })
            ->where(function ($q) {
                $q->whereNull('quotation_status')->orWhere('quotation_status', 'sales');
            })
            ->orderBy('created_at')
            ->get();

        return $orders->map(function (Order $order) {
            $remaining = $this->orderRemaining($order);
            if ($remaining <= 0) {
                return null;
            }

            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'order_date' => optional($order->created_at)->format('d-M-Y'),
                'total_amount' => (float) ($order->total_amount ?? 0),
                'remaining_amount' => $remaining,
            ];
        })->filter()->values();
    }

    private function orderRemaining(Order $order): float
    {
        if ($order->remaining_amount !== null && $order->remaining_amount !== '') {
            return max(0, round((float) $order->remaining_amount, 2));
        }

        $totalPaid = (float) PaymentStore::query()
            ->where('order_id', $order->id)
            ->where('isDeleted', 0)
            ->sum('payment_amount');

        return max(0, round((float) ($order->total_amount ?? 0) - $totalPaid, 2));
    }

    private function branchId(): int
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }
        $subAdminId = session('selectedSubAdminId');
        if ($user->role === 'staff' && !empty($user->branch_id)) {
            return (int) $user->branch_id;
        }
        if ($user->role === 'admin' && !empty($subAdminId)) {
            return (int) $subAdminId;
        }

        return (int) $user->id;
    }
}
