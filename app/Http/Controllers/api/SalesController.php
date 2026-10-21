<?php
namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Mail\SalesOrderCreatedMail;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentStore;
use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\ProductVariant;
use App\Models\Sales_Labour_Items;
use App\Models\SalesReturn;
use App\Models\Setting;
use App\Models\TaxRate;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\WhatsAppMessageTemplate;
use App\Services\MailConfigService;
use App\Services\NotificationService;
use App\Services\PendingEmiService;
use App\Services\StaffDepartmentScope;
use App\Services\WhatsAppService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use App\Models\Unit;
use App\Models\CustomerDeposit;
use Illuminate\Support\Facades\Schema;

class SalesController extends Controller
{
    private function inclusiveGstDetails(float $baseAmount, ?Product $product = null, bool $globalWithGst = true): array
    {
        if (! $globalWithGst || ! $product || $product->gst_option !== 'with_gst') {
            return ['details' => [], 'total' => 0.0];
        }

        $rates = $this->productGstRates($product);

        $totalRate = collect($rates)->sum(fn ($tax) => (float) ($tax['tax_rate'] ?? 0));

        if ($baseAmount <= 0 || $totalRate <= 0) {
            return ['details' => [], 'total' => 0.0];
        }

        // GST is calculated as: base amount × rate%
        $totalGstAmount = round($baseAmount * ($totalRate / 100), 2);
        
        $details = [];
        $allocated = 0.0;
        $lastIndex = count($rates) - 1;

        foreach (array_values($rates) as $index => $tax) {
            $rate = (float) ($tax['tax_rate'] ?? 0);
            $taxAmount = $index === $lastIndex
                ? round($totalGstAmount - $allocated, 2)
                : round(($totalGstAmount * $rate) / $totalRate, 2);

            $allocated += $taxAmount;
            $details[] = [
                'tax_name' => $tax['tax_name'] ?? 'GST',
                'tax_rate' => $rate,
                'tax_amount' => $taxAmount,
            ];
        }

        return ['details' => $details, 'total' => $totalGstAmount];
    }

    private function productGstRates(?Product $product): array
    {
        if (! $product || $product->gst_option !== 'with_gst' || empty($product->product_gst)) {
            return [];
        }

        $gstData = is_array($product->product_gst)
            ? $product->product_gst
            : json_decode((string) $product->product_gst, true);

        if (! is_array($gstData)) {
            return [];
        }

        return collect($gstData)
            ->filter(fn ($tax) => is_array($tax) && (float) ($tax['tax_rate'] ?? 0) > 0)
            ->map(fn ($tax) => [
                'tax_name' => $tax['tax_name'] ?? 'GST',
                'tax_rate' => (float) ($tax['tax_rate'] ?? 0),
            ])
            ->values()
            ->all();
    }

    private function resolveFinancialYearRange(?string $financialYear): ?array
    {
        if (empty($financialYear)) {
            return null;
        }

        if (!preg_match('/^(\d{4})-(\d{4})$/', trim($financialYear), $matches)) {
            return null;
        }

        $startYear = (int) $matches[1];
        $endYear = (int) $matches[2];

        if ($endYear !== $startYear + 1) {
            return null;
        }

        $from = Carbon::create($startYear, 4, 1)->startOfDay()->toDateTimeString();
        $to = Carbon::create($endYear, 3, 31)->endOfDay()->toDateTimeString();

        return [$from, $to];
    }

    private function applyFinancialYearFilter($query, ?string $financialYear, string $column = 'created_at'): void
    {
        $range = $this->resolveFinancialYearRange($financialYear);
        if (!$range) {
            return;
        }

        $query->whereBetween($column, $range);
    }

    private function resolveSalesSettingsBranchId($user, $selectedSubAdminID): int
    {
        if ($user->role === 'staff' && !empty($user->branch_id)) {
            return (int) $user->branch_id;
        }

        $normalizedSubAdminId = StaffDepartmentScope::normalizeSubAdminId($selectedSubAdminID);
        if ($user->role === 'admin' && $normalizedSubAdminId !== null) {
            return $normalizedSubAdminId;
        }

        return (int) $user->id;
    }

    private function resolveSelectedSubAdminIdFromRequest(Request $request): ?int
    {
        $raw = $request->input('selectedSubAdminId');
        if ($raw === null || $raw === '') {
            $raw = session('selectedSubAdminId');
        }

        return StaffDepartmentScope::normalizeSubAdminId($raw);
    }

    private function findWhatsAppOrderTemplate(int $branchId, array $useForCandidates): ?WhatsAppMessageTemplate
    {
        $templates = WhatsAppMessageTemplate::select(
                'id',
                'branch_id',
                'name',
                'components',
                'language',
                'status',
                'category',
                'use_for_template',
                'on_off',
                'isDeleted'
            )
            ->where('branch_id', $branchId)
            ->where('on_off', 'active')
            ->where('isDeleted', 0)
            ->get();

        if ($templates->isEmpty()) {
            return null;
        }

        $normalize = static function ($value): string {
            return trim(mb_strtolower((string) $value));
        };

        $candidates = collect($useForCandidates)
            ->filter(fn ($value) => trim((string) $value) !== '')
            ->map(fn ($value) => trim((string) $value))
            ->unique()
            ->values();

        foreach ($candidates as $candidate) {
            $normalizedCandidate = $normalize($candidate);

            $template = $templates->first(function ($template) use ($normalize, $normalizedCandidate) {
                return $normalize($template->use_for_template ?? '') === $normalizedCandidate;
            });

            if ($template) {
                return $template;
            }
        }

        $aliasMap = [
            'pending order' => [
                'pending order',
                'subscription pending',
                'order_pending_notification',
                'order_panding_notification',
                'order_pending',
            ],
            'subscription pending' => [
                'subscription pending',
                'pending order',
                'order_pending_notification',
                'order_panding_notification',
                'order_pending',
            ],
            'complete order' => [
                'complete order',
                'subscription complete',
                'subscription_invoice_pdf',
                'order_complete_notification',
                'order_confirmation',
                'order_conform',
            ],
            'subscription complete' => [
                'subscription complete',
                'complete order',
                'subscription_invoice_pdf',
                'order_complete_notification',
                'order_confirmation',
                'order_conform',
            ],
            'order invoice pdf' => [
                'order invoice pdf',
                'subscription_invoice_pdf',
                'complete order',
                'subscription complete',
            ],
        ];

        $aliasCandidates = [];
        foreach ($candidates as $candidate) {
            $aliasCandidates = array_merge($aliasCandidates, $aliasMap[$normalize($candidate)] ?? [$candidate]);
        }

        $aliasCandidates = array_values(array_unique(array_map($normalize, $aliasCandidates)));

        return $templates->first(function ($template) use ($normalize, $aliasCandidates) {
            return in_array($normalize($template->use_for_template ?? ''), $aliasCandidates, true)
                || in_array($normalize($template->name ?? ''), $aliasCandidates, true);
        });
    }

    private function findWhatsAppTemplateByName(int $branchId, string $templateName): ?WhatsAppMessageTemplate
    {
        $normalizedName = trim(mb_strtolower($templateName));
        if ($normalizedName === '') {
            return null;
        }

        return WhatsAppMessageTemplate::select(
                'id',
                'branch_id',
                'name',
                'components',
                'language',
                'status',
                'category',
                'use_for_template',
                'on_off',
                'isDeleted'
            )
            ->where('branch_id', $branchId)
            ->where('on_off', 'active')
            ->where('isDeleted', 0)
            ->get()
            ->first(function ($template) use ($normalizedName) {
                return trim(mb_strtolower((string) ($template->name ?? ''))) === $normalizedName;
            });
    }

    private function resolveFinancialYearWindowFromDate(Carbon $referenceDate): array
    {
        $reference = $referenceDate->copy()->timezone('Asia/Kolkata');
        $startYear = $reference->month >= 4 ? $reference->year : ($reference->year - 1);

        $from = Carbon::create($startYear, 4, 1, 0, 0, 0, 'Asia/Kolkata');
        $to = Carbon::create($startYear + 1, 3, 31, 23, 59, 59, 'Asia/Kolkata');

        return [$from, $to];
    }

    private function isFinancialYearOrderNumberingEnabled(int $branchId): bool
    {
        $setting = Setting::where('branch_id', $branchId)->first();
        return (bool) ($setting->financial_year ?? false);
    }

    private function getNextFinancialYearOrderSequence(
        int $branchId,
        string $quotationStatus,
        Carbon $referenceDate
    ): int {
        [$from, $to] = $this->resolveFinancialYearWindowFromDate($referenceDate);

        $query = Order::query()
            ->where('branch_id', $branchId)
            ->whereBetween('created_at', [$from, $to]);

        if ($quotationStatus === 'quotation') {
            $maxSequence = (int) ($query
                ->where('quotation_status', 'quotation')
                ->whereRaw("order_number REGEXP '^Q-[0-9]+$'")
                ->selectRaw("MAX(CAST(SUBSTRING(order_number, 3) AS UNSIGNED)) as max_sequence")
                ->value('max_sequence') ?? 0);

            return $maxSequence + 1;
        }

        if ($quotationStatus === 'advance_receipt') {
            $maxSequence = (int) ($query
                ->where('quotation_status', 'advance_receipt')
                ->whereRaw("order_number REGEXP '^ADV-[0-9]+$'")
                ->selectRaw("MAX(CAST(SUBSTRING(order_number, 5) AS UNSIGNED)) as max_sequence")
                ->value('max_sequence') ?? 0);

            return $maxSequence + 1;
        }

        if ($quotationStatus === 'rental') {
            $maxSequence = (int) ($query
                ->where('quotation_status', 'rental')
                ->whereRaw("order_number REGEXP '^RO-[0-9]+$'")
                ->selectRaw("MAX(CAST(SUBSTRING(order_number, 4) AS UNSIGNED)) as max_sequence")
                ->value('max_sequence') ?? 0);

            return $maxSequence + 1;
        }

        $maxSequence = (int) ($query
            ->where('quotation_status', 'sales')
            ->whereRaw("order_number REGEXP '^[0-9]+$' AND CHAR_LENGTH(order_number) <= 9")
            ->selectRaw("MAX(CAST(order_number AS UNSIGNED)) as max_sequence")
            ->value('max_sequence') ?? 0);

        return $maxSequence + 1;
    }

    private function generateFinancialYearOrderNumber(
        int $branchId,
        string $quotationStatus,
        Carbon $referenceDate
    ): string {
        $nextSequence = $this->getNextFinancialYearOrderSequence($branchId, $quotationStatus, $referenceDate);
        if ($quotationStatus === 'quotation') {
            return 'Q-' . $nextSequence;
        } elseif ($quotationStatus === 'advance_receipt') {
            return 'ADV-' . $nextSequence;
        } elseif ($quotationStatus === 'rental') {
            return 'RO-' . str_pad($nextSequence, 6, '0', STR_PAD_LEFT);
        }
        return (string) $nextSequence;
    }

    private const SALES_ORDER_NUMBER_PREFIX = 'SI/HO/';

    /** Next sequence for sales orders: SI/HO/51, SI/HO/52, ... (globally unique across all branches). */
    private function getNextSalesOrderSequence(int $branchId = 0): int
    {
        // Unique constraint is global on order_number — include soft-deleted / any status
        // so we never propose a number that still occupies the unique index.
        $maxSequence = (int) (Order::query()
            ->whereRaw('UPPER(order_number) REGEXP ?', ['^SI/HO/[0-9]+$'])
            ->selectRaw('MAX(CAST(SUBSTRING_INDEX(order_number, "/", -1) AS UNSIGNED)) as max_sequence')
            ->value('max_sequence') ?? 0);

        return max(1, $maxSequence + 1);
    }

    private function salesOrderNumberExists(string $orderNumber): bool
    {
        return Order::where('order_number', $orderNumber)->exists()
            || Order::whereRaw('UPPER(order_number) = ?', [strtoupper($orderNumber)])->exists();
    }

    private function generateSalesOrderNumber(int $branchId): string
    {
        $prefix   = self::SALES_ORDER_NUMBER_PREFIX;
        // Global lock — uniqueness is on order_number alone (not per branch).
        $lockName = 'sales_order_number_global';
        $timeout  = 15;

        try {
            $locked = DB::selectOne('SELECT GET_LOCK(?, ?) AS acquired', [$lockName, $timeout]);

            if (! $locked || ! (int) ($locked->acquired ?? 0)) {
                Log::warning("Could not acquire global sales order number lock");
            }

            // Prefer recycled numbers only when the unique slot is actually free.
            $recycledRows = DB::table('recycled_order_numbers')
                ->where('branch_id', $branchId)
                ->orderByRaw("CAST(SUBSTRING_INDEX(order_number, '/', -1) AS UNSIGNED) ASC")
                ->limit(20)
                ->get();

            foreach ($recycledRows as $recycled) {
                DB::table('recycled_order_numbers')->where('id', $recycled->id)->delete();
                if (! empty($recycled->order_number) && ! $this->salesOrderNumberExists($recycled->order_number)) {
                    return $recycled->order_number;
                }
                Log::warning('Skipped recycled order number still in use', [
                    'order_number' => $recycled->order_number ?? null,
                    'branch_id' => $branchId,
                ]);
            }

            $candidate = $this->getNextSalesOrderSequence($branchId);
            $maxTries  = 500;

            while ($maxTries-- > 0) {
                $orderNumber = $prefix . $candidate;
                if (! $this->salesOrderNumberExists($orderNumber)) {
                    return $orderNumber;
                }
                $candidate++;
            }

            Log::error("Could not find unique order number after many attempts for branch {$branchId}");
            return $prefix . time() . '-' . mt_rand(100, 999);

        } finally {
            DB::selectOne('SELECT RELEASE_LOCK(?)', [$lockName]);
        }
    }

    /**
     * Create order; on duplicate order_number, regenerate and retry a few times.
     */
    private function createOrderWithUniqueNumber(array $orderDataPayload, int $branchId, bool $isSalesOrder): Order
    {
        $attempts = 0;
        $maxAttempts = 5;

        while (true) {
            try {
                return Order::create($orderDataPayload);
            } catch (\Illuminate\Database\QueryException $e) {
                $isDuplicate = ($e->errorInfo[1] ?? null) == 1062
                    || str_contains(strtolower($e->getMessage()), 'duplicate entry')
                    || str_contains(strtolower($e->getMessage()), 'orders_order_number_unique');

                $attempts++;
                if (! $isDuplicate || ! $isSalesOrder || $attempts >= $maxAttempts) {
                    throw $e;
                }

                $orderDataPayload['order_number'] = $this->generateSalesOrderNumber($branchId);
                Log::warning('Retrying order create after duplicate order_number', [
                    'attempt' => $attempts,
                    'new_order_number' => $orderDataPayload['order_number'],
                    'branch_id' => $branchId,
                ]);
            }
        }
    }

    public function getPendingEmis(Request $request)
    {
        $user = Auth::guard('api')->user();
        $selectedSubAdminID = $request->input('selectedSubAdminId') ?? session('selectedSubAdminId');
        $branchId = $this->resolveSalesSettingsBranchId($user, $selectedSubAdminID);
        [$year, $month] = PendingEmiService::parseMonth($request->input('month'));
        $pendingEmis = PendingEmiService::getPendingEmis($branchId, $year, $month);
        $settings = Setting::where('branch_id', $branchId)->first();

        return response()->json([
            'status' => true,
            'month' => sprintf('%04d-%02d', $year, $month),
            'count' => count($pendingEmis),
            'currency_symbol' => $settings->currency_symbol ?? '₹',
            'currency_position' => $settings->currency_position ?? 'left',
            'data' => $pendingEmis,
        ]);
    }

    public function getHistory($order_id)
    {
        $history = PaymentStore::where('order_id', $order_id)
            ->where(function ($q) {
                $q->whereNull('isDeleted')->orWhere('isDeleted', 0);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $order = Order::findOrFail($order_id);

        $totalPaid = $history->sum('payment_amount');

        // Calculate Return Amount
        $returnAmount = \App\Models\SalesReturn::where('order_id', $order_id)->sum('total_amount');

        // ✅ Dynamic calculation (NO column)
        $extraPaid = max(0, $totalPaid - ($order->total_amount - $returnAmount));
        $remaining = max(0, ($order->total_amount - $returnAmount) - $totalPaid);

        return response()->json([
            'status' => 'success',
            'data' => $history,
            'summary' => [
                'order_total' => $order->total_amount,
                'total_paid' => $totalPaid,
                'return_amount' => $returnAmount,
                'extra_paid' => $extraPaid,
                'remaining' => $remaining,
            ],
        ]);
    }
    public function makePaymentSubmit(Request $request)
    {
        // dd($request->all());
        // $user_id = Auth::id();
        $user_id = Auth::guard('api')->user()->id;

        $request->validate([

            'order_id' => 'nullable|integer',
            'payment_amount' => 'nullable|numeric',
            'payment_date' => 'nullable|date',
            'payment_type' => 'nullable|string',
            'emi_month' => 'nullable|integer',
            'pending_date' => 'nullable|date',
            'new_emi_value' => 'nullable',
            'emi_paid_value' => 'nullable|numeric',
            'bank_id' => 'nullable|integer',
            'remarks' => 'nullable|string|max:500',
        ]);

        if ($request->filled('order_id')) {
            // Determine payment amount
            $paymentAmount = $request->emi_total_new ?? $request->emi_total ?? $request->amount ?? $request->upi_online_amount ?? 0;

            if ($request->filled('cash_amount') && $request->filled('online_amount')) {
                $paymentAmount = (float) $request->cash_amount + (float) $request->online_amount;
            } elseif ($request->filled('fully_cash_amount') && $request->filled('full_online_amount')) {
                $paymentAmount = (float) $request->fully_cash_amount + (float) $request->full_online_amount;
            } elseif ($request->cashAmount) {
                $paymentAmount = $request->cashAmount;
            } elseif ($request->upi_online_amount) {
                $paymentAmount = $request->upi_online_amount;
            } elseif ($request->emi_monthly) {
                $paymentAmount = $request->emi_monthly;
            } elseif ($request->filled('emi_down_payment') && !$request->filled('emi_month')) {
                $paymentAmount = $request->emi_down_payment;
            }

            // Determine payment type
            if (
                in_array($request->paid_type, ['cash_partially']) ||
                in_array($request->online_type, ['online_partially']) ||
                in_array($request->cash_online_type, ['cash_online_partially'])
            ) {
                $type = 'partially';
            } elseif (
                in_array($request->paid_type, ['cash_fully']) ||
                in_array($request->online_type, ['online_fully']) ||
                in_array($request->cash_online_type, ['cash_online_fully']) ||
                $request->payment_type === 'fully'

            ) {
                $type = 'fully';
            } elseif (
                in_array($request->emi_type, ['emi']) ||
                strtolower((string) $request->payment_type) === 'emi' ||
                strtolower((string) $request->payment_method) === 'emi'
            ) {
                $type = 'emi';
            } else {
                $type = 'fully';
            }

            // Fetch Invoice if exists
            $invoice = $request->order_id ? Order::find($request->order_id) : null;

            $isNewEmi = $request->filled('emi_loan_amount');

            if ($type === 'emi' && $invoice && !$isNewEmi) {
                $emiMonth = (int) $request->emi_month;
                $emiTenure = (int) preg_replace('/[^0-9]/', '', (string) ($invoice->emi_tenure ?? ''));

                if ($emiTenure <= 0) {
                    return response()->json([
                        'errors' => ['emi_month' => ['This order does not have an EMI tenure configured.']],
                    ], 422);
                }

                if ($emiMonth <= 0 || $emiMonth > $emiTenure) {
                    return response()->json([
                        'errors' => ['emi_month' => ['Please select a valid EMI month.']],
                    ], 422);
                }

                $paidEmiMonths = PaymentStore::where('order_id', $invoice->id)
                    ->where(function ($q) {
                        $q->whereNull('isDeleted')->orWhere('isDeleted', 0);
                    })
                    ->where(function ($q) {
                        $q->where('payment_method', 'emi')->orWhere('payment_type', 'emi');
                    })
                    ->whereNotNull('emi_month')
                    ->pluck('emi_month')
                    ->map(fn ($month) => (int) $month)
                    ->filter(fn ($month) => $month > 0)
                    ->unique()
                    ->values();

                if ($paidEmiMonths->contains($emiMonth)) {
                    return response()->json([
                        'errors' => ['emi_month' => ['This EMI month has already been paid.']],
                    ], 422);
                }

                $nextEmiMonth = 1;
                while ($nextEmiMonth <= $emiTenure && $paidEmiMonths->contains($nextEmiMonth)) {
                    $nextEmiMonth++;
                }

                if ($emiMonth !== $nextEmiMonth) {
                    return response()->json([
                        'errors' => ['emi_month' => ["Please pay EMI month {$nextEmiMonth} first."]],
                    ], 422);
                }
            }

            // Calculate remaining amount
            $currentRemaining = $invoice->remaining_amount ?? 0;
            $newRemaining = max(0, $currentRemaining - $paymentAmount);
            // dd($currentRemaining,$newRemaining);

            // $payment = PaymentStore::create([
            //     'user_id'           => $user_id,
            //     'order_id'          => $request->order_id,
            //     'custom_invoice_id' => 0,
            //     'payment_amount'    => $paymentAmount,
            //     'remaining_amount'  => $newRemaining,
            //     'payment_method'    => $request->payment_method ?? $request->payment_type ?? '',
            //     'payment_date'      => \Carbon\Carbon::now(),
            //     'payment_type'      => $type,
            //     'cash_amount'       => $request->cash_amount,
            //     'upi_amount'        => $request->online_amount,
            //     'emi_month'         => $request->emi_month ?? 1,
            //     'isDeleted'         => 0,
            // ]);
            // Handle cash_online_partially separately
            // ✅ Handle cash_online cases separately
            if ($request->cash_online_type === 'cash_online_partially' || $request->cash_online_type === 'cash_online_fully') {
                $payments = [];

                // 1️⃣ Handle Cash entry (partially or fully)
                $cashValue = $request->cash_online_type === 'cash_online_partially'
                    ? $request->cash_amount
                    : $request->fully_cash_amount;

                if (!empty($cashValue) && $cashValue > 0) {
                    $cashPayment = PaymentStore::create([
                        'user_id' => $user_id,
                        'order_id' => $request->order_id,
                        'custom_invoice_id' => null,
                        'payment_amount' => $cashValue,
                        'remaining_amount' => $newRemaining,
                        'payment_method' => 'cash',
                        'payment_date' => $request->filled('payment_date') ? $request->payment_date : \Carbon\Carbon::now(),
                        'payment_type' => ($request->cash_online_type === 'cash_online_partially') ? 'partially' : 'fully',
                        'cash_amount' => $cashValue,
                        'upi_amount' => 0,
                        'emi_month' => $request->emi_month ?? 1,
                        'bank_id' => $request->bank_id,
                        'remarks' => $request->remarks,
                        'status' => 'credit',
                        'isDeleted' => 0,
                    ]);
                    $payments[] = $cashPayment;
                }

                // 2️⃣ Handle Online entry (partially or fully)
                $onlineValue = $request->cash_online_type === 'cash_online_partially'
                    ? $request->online_amount
                    : $request->full_online_amount;

                if (!empty($onlineValue) && $onlineValue > 0) {
                    $onlinePayment = PaymentStore::create([
                        'user_id' => $user_id,
                        'order_id' => $request->order_id,
                        'custom_invoice_id' => null,
                        'payment_amount' => $onlineValue,
                        'remaining_amount' => $newRemaining,
                        'payment_method' => 'online',
                        'payment_date' => $request->filled('payment_date') ? $request->payment_date : \Carbon\Carbon::now(),
                        'payment_type' => ($request->cash_online_type === 'cash_online_partially') ? 'partially' : 'fully',
                        'cash_amount' => 0,
                        'upi_amount' => $onlineValue,
                        'emi_month' => $request->emi_month ?? 1,
                        'bank_id' => $request->bank_id,
                        'remarks' => $request->remarks,
                        'status' => 'credit',
                        'isDeleted' => 0,
                    ]);
                    $payments[] = $onlinePayment;
                }

                $payment = $payments; // return both records as array
            } else {
                // Default single payment record for all other cases
                $paymentData = [
                    'user_id' => $user_id,
                    'order_id' => $request->order_id,
                    'custom_invoice_id' => null,
                    'payment_amount' => $paymentAmount,
                    'remaining_amount' => $newRemaining,
                    'payment_method' => $request->payment_method ?? $request->payment_type ?? '',
                    'payment_date' => $request->filled('payment_date') ? $request->payment_date : \Carbon\Carbon::now(),
                    'payment_type' => $type,
                    'cash_amount' => $request->cash_amount,
                    'upi_amount' => $request->online_amount,
                    'emi_month' => $request->filled('emi_month') ? (int) $request->emi_month : null,
                    'bank_id' => $request->bank_id,
                    'remarks' => $request->remarks,
                    'status' => 'credit',
                    'isDeleted' => 0,
                ];

                if ($type === 'emi' && $invoice) {
                    $paymentData['emi_down_payment'] = $isNewEmi ? $request->emi_down_payment : $invoice->emi_down_payment;
                    $paymentData['emi_loan_amount'] = $isNewEmi ? $request->emi_loan_amount : $invoice->emi_loan_amount;
                    $paymentData['emi_interest_rate'] = $isNewEmi ? $request->emi_interest_rate : $invoice->emi_interest_rate;
                    $paymentData['emi_tenure'] = $isNewEmi ? ($request->emi_tenure === 'custom' ? $request->emi_custom_tenure : $request->emi_tenure) : $invoice->emi_tenure;
                    $paymentData['emi_monthly_amount'] = $request->filled('emi_monthly_amount')
                        ? (float) $request->emi_monthly_amount
                        : ($request->filled('emi_box_monthly_amount') ? (float) $request->emi_box_monthly_amount : $invoice->emi_monthly_amount);
                    $paymentData['emi_aadhar_number'] = $isNewEmi ? $request->emi_aadhar_number : $invoice->emi_aadhar_number;
                    $paymentData['emi_pan_number'] = $isNewEmi ? $request->emi_pan_number : $invoice->emi_pan_number;
                    $paymentData['emi_guarantor_name'] = $isNewEmi ? $request->emi_guarantor_name : $invoice->emi_guarantor_name;
                    $paymentData['emi_do_id'] = $isNewEmi ? $request->emi_do_id : ($invoice->emi_do_id ?? null);
                }

                $payment = PaymentStore::create($paymentData);
            }

            // Update invoice
            if ($invoice) {
                // $updateData = ['remaining_amount' => $newRemaining];

                // // ✅ Auto set payment_status when fully paid
                // if ($newRemaining <= 0) {
                //     $updateData['payment_status'] = 'completed';
                // } else {
                //     $updateData['payment_status'] = 'Pending';
                // }
                $updateData = ['remaining_amount' => $newRemaining];
                $totalAmount = $invoice->total_amount ?? 0;
                // ✅ Auto set payment_status when fully paid
                if ($newRemaining <= 0) {
                    // Fully paid
                    $updateData['payment_status'] = 'completed';
                } elseif ($newRemaining < $totalAmount) {
                    // Some amount paid, still balance remaining
                    $updateData['payment_status'] = 'partially';
                } else {
                    // No payment done
                    $updateData['payment_status'] = 'pending';
                }
                // If new EMI is being set
                $method = strtolower((string) ($request->payment_method ?? $request->payment_type ?? ''));
                if ($method === 'emi' || $type === 'emi') {
                    $updateData['payment_method'] = 'emi';
                    if ($isNewEmi) {
                        $updateData['emi_down_payment'] = $request->emi_down_payment;
                        $updateData['emi_loan_amount'] = $request->emi_loan_amount;
                        $updateData['emi_interest_rate'] = $request->emi_interest_rate;
                        $updateData['emi_tenure'] = $request->emi_tenure === 'custom' ? $request->emi_custom_tenure : $request->emi_tenure;
                        $updateData['emi_monthly_amount'] = $request->emi_box_monthly_amount ?? $request->emi_monthly_amount;
                        $updateData['emi_aadhar_number'] = $request->emi_aadhar_number;
                        $updateData['emi_pan_number'] = $request->emi_pan_number;
                        $updateData['emi_guarantor_name'] = $request->emi_guarantor_name;
                        $updateData['emi_do_id'] = $request->emi_do_id;
                    }
                } elseif ($request->filled('payment_method') || $request->filled('payment_type')) {
                    $rawMethod = $request->payment_method ?? $request->payment_type ?? 'cash';
                    $updateData['payment_method'] = match (strtolower((string) $rawMethod)) {
                        'online', 'upi' => 'online',
                        'cash' => 'cash',
                        'emi' => 'emi',
                        default => 'cash',
                    };
                }

                // Update next pending date if provided
                if ($request->pending_date) {
                    $updateData['next_pending_date'] = $request->pending_date;
                }

                $invoice->update($updateData);
            }
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Payment submitted successfully.',
            'data' => $payment,
        ]);
    }

    // In your controller
    public function getProductsByCategory(Request $request, $categoryId)
    {
        $settings = DB::table('settings')->first();
        $currencySymbol = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';
        
        $enableVariants = $settings ? (bool)$settings->enable_variant_product : true;

        $isRental = $request->boolean('rental'); // true/false

        $productsQuery = Product::with('category', 'unit')
            ->where('category_id', $categoryId)
            ->where('isDeleted', 0)
            ->where('status', 'active') // ✅ Only active products
            ->when(!$enableVariants, function ($q) {
                $q->where('item_type', '!=', 'variant')->where(function($sub) {
                    $sub->whereNull('item_type')->orWhere('item_type', 'normal');
                });
            });

        // ✅ If rental mode, show only rental products
        if ($isRental) {
            $productsQuery->where('is_rent_available', 1);
        }

        $products = $productsQuery->get();

        // dd($products);
        return response()->json([
            'status' => true,
            'currency_symbol' => $currencySymbol,
            'currency_position' => $currencyPosition,
            'data' => $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'purchase_price' => $product->purchase_price,
                    'price' => $product->price,
                    'quantity' => $product->quantity,
                    'image' => $product->images,
                    'image_url' => $product->image_url,
                    'category_name' => $product->category->name,
                    'categoryId' => $product->category_id,
                    'category_name' => $product->category ? $product->category->name : null,
                    'category_id' => $product->category_id,
                    'is_warranty_category' => $this->isWarrantyCategoryProduct($product),
                    'gst_option' => $product->gst_option,
                    'product_gst' => $product->product_gst,
                    'unit' => $product->unit ? $product->unit->unit_name : null,
                    'is_rent_available' => $product->is_rent_available,
                    'rent_price' => $product->rent_price,
                    'rent_period' => $product->rent_period,
                    'mrp' => $product->mrp,
                    'SKU' => $product->SKU,
                    'barcode' => $product->barcode,
                    'selling_prices' => $product->sellingPriceOptions(),
                ];
            }),
        ]);
    }

    private function isWarrantyCategoryProduct(?Product $product): bool
    {
        return strtolower(trim((string) ($product?->category?->name ?? ''))) === 'warranty';
    }

    private function availableSaleStock(?Product $product, $variantId, $branchId): float
    {
        if (!$product) {
            return 0;
        }

        if ($variantId) {
            $variant = ProductVariant::find($variantId);
            $fromVariant = (float) ($variant->qty ?? 0);
            $lastInv = ProductInventory::where('product_variant_id', $variantId)
                ->where('branch_id', $branchId)
                ->latest('id')
                ->first();
            $fromInv = $lastInv ? (float) $lastInv->current_stock : $fromVariant;
            return max(0, min($fromVariant, $fromInv));
        }

        $fromProduct = (float) ($product->quantity ?? 0);
        $lastInv = ProductInventory::where('product_id', $product->id)
            ->where(function ($q) {
                $q->whereNull('product_variant_id')->orWhere('product_variant_id', 0);
            })
            ->where('branch_id', $branchId)
            ->latest('id')
            ->first();
        $fromInv = $lastInv ? (float) $lastInv->current_stock : $fromProduct;
        return max(0, min($fromProduct, $fromInv));
    }

    private function formatLocalBarcodeProduct(Product $product, $scannedBarcode = null): array
    {
        return [
            'matched_barcode' => $scannedBarcode,
            'id' => $product->id,
            'name' => $product->name,
            'SKU' => $product->SKU,
            'hsn_code' => $product->hsn_code,
            'barcode' => $product->barcode,
            'description' => $product->description,
            'purchase_price' => $product->purchase_price,
            'price' => $product->price,
            'quantity' => $product->quantity,
            'image' => $product->images,
            'category_id' => $product->category_id,
            'category_name' => $product->category?->name,
            'is_warranty_category' => $this->isWarrantyCategoryProduct($product),
            'brand_id' => $product->brand_id,
            'unit_id' => $product->unit_id,
            'status' => $product->status,
            'availablility' => $product->availablility,
            'gst_option' => $product->gst_option,
            'product_gst' => $product->product_gst,
            'product_gst_ids' => collect(json_decode($product->product_gst, true) ?: [])
                ->pluck('tax_id')
                ->filter()
                ->values(),
            'is_rent_available' => $product->is_rent_available,
            'rent_price' => $product->rent_price,
            'rent_period' => $product->rent_period,
            'mrp' => $product->mrp,
            'rate_from_barcode' => !empty($scannedBarcode),
            'source' => 'local',
            'source_label' => 'ERP product database',
            'selling_prices' => $product->sellingPriceOptions(),
        ];
    }

    private function fetchOpenFoodFactsProduct(string $barcode): ?array
    {
        $response = Http::timeout(8)
            ->withHeaders([
                'User-Agent' => 'erp-main-demo/1.0',
            ])
            ->get("https://world.openfoodfacts.net/api/v2/product/{$barcode}", [
                'fields' => 'code,product_name,brands,categories,image_url,quantity,generic_name,packaging_text,product_quantity',
            ]);

        if (! $response->successful()) {
            return null;
        }

        $data = $response->json();
        $product = $data['product'] ?? null;

        if (empty($product) || empty($product['product_name'])) {
            return null;
        }

        $descriptionParts = array_filter([
            $product['generic_name'] ?? null,
            $product['packaging_text'] ?? null,
            $product['categories'] ?? null,
        ]);

        return [
            'id' => null,
            'name' => $product['product_name'] ?? null,
            'SKU' => $product['code'] ?? $barcode,
            'hsn_code' => null,
            'barcode' => $product['code'] ?? $barcode,
            'description' => ! empty($descriptionParts) ? implode(' | ', $descriptionParts) : null,
            'price' => null,
            'quantity' => $product['product_quantity'] ?? null,
            'image' => $product['image_url'] ?? null,
            'category_id' => null,
            'brand_id' => null,
            'brand_name' => $product['brands'] ?? null,
            'category_name' => $product['categories'] ?? null,
            'unit_id' => null,
            'status' => 'active',
            'availablility' => 'in_stock',
            'gst_option' => null,
            'product_gst' => null,
            'product_gst_ids' => [],
            'source' => 'external',
            'source_label' => 'Open Food Facts',
        ];
    }

    private function fetchUpcItemDbProduct(string $barcode): ?array
    {
        $response = Http::timeout(8)
            ->get('https://api.upcitemdb.com/prod/trial/lookup', [
                'upc' => $barcode,
            ]);

        if (! $response->successful()) {
            return null;
        }

        $data = $response->json();
        $item = $data['items'][0] ?? null;

        if (empty($item) || empty($item['title'])) {
            return null;
        }

        $descriptionParts = array_filter([
            $item['description'] ?? null,
            $item['category'] ?? null,
        ]);

        return [
            'id' => null,
            'name' => $item['title'] ?? null,
            'SKU' => $item['upc'] ?? $barcode,
            'hsn_code' => null,
            'barcode' => $item['upc'] ?? $barcode,
            'description' => ! empty($descriptionParts) ? implode(' | ', $descriptionParts) : null,
            'price' => null,
            'quantity' => null,
            'image' => $item['images'][0] ?? null,
            'category_id' => null,
            'brand_id' => null,
            'brand_name' => $item['brand'] ?? null,
            'category_name' => $item['category'] ?? null,
            'unit_id' => null,
            'status' => 'active',
            'availablility' => 'in_stock',
            'gst_option' => null,
            'product_gst' => null,
            'product_gst_ids' => [],
            'source' => 'external',
            'source_label' => 'UPCitemdb',
        ];
    }

    private function fetchExternalBarcodeProduct(string $barcode): ?array
    {
        foreach ([
            fn () => $this->fetchOpenFoodFactsProduct($barcode),
            fn () => $this->fetchUpcItemDbProduct($barcode),
        ] as $resolver) {
            try {
                $product = $resolver();
                if (! empty($product)) {
                    return $product;
                }
            } catch (\Throwable $exception) {
                Log::warning('External barcode lookup failed', [
                    'barcode' => $barcode,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        return null;
    }

    public function getByBarcode($barcode)
    {
        try {
            $user = auth()->user();
            $branchId = $user ? ($user->branch_id ?? $user->id) : null;
            $settingsQuery = DB::table('settings');
            if ($branchId) {
                $settingsQuery->where('branch_id', $branchId);
            }
            $settings = $settingsQuery->first();
            if (!$settings) {
                $settings = DB::table('settings')->first();
            }
            $enableVariants = $settings ? (bool)$settings->enable_variant_product : true;
            $normalizedBarcode = preg_replace('/\s+/', '', trim((string) $barcode));

            if ($enableVariants) {
                // First check if a variant matches the barcode
                $variant = \App\Models\ProductVariant::with(['product.category', 'product.unit', 'product.brand'])
                    ->where(function($q) use ($barcode, $normalizedBarcode) {
                        $q->where('barcode', $barcode)
                          ->orWhere('barcode', $normalizedBarcode)
                          ->orWhereRaw("FIND_IN_SET(?, REPLACE(barcode, ' ', '')) > 0", [$normalizedBarcode]);
                    })
                    ->first();

                if ($variant && $variant->product) {
                    $variantProduct = $this->applyBarcodeDetailsOverride($variant->product, $normalizedBarcode);
                    $formattedProduct = $this->formatLocalBarcodeProduct($variantProduct, $normalizedBarcode);
                    $formattedProduct['matched_variant_id'] = $variant->id;
                    
                    return response()->json([
                        'status' => true,
                        'product' => $formattedProduct,
                    ]);
                }
            }

            // Fallback to main product barcode for backward compatibility
            $productQuery = Product::with('category', 'unit', 'brand')
                ->where(function($q) use ($barcode, $normalizedBarcode) {
                    $q->where('barcode', $barcode)
                      ->orWhere('barcode', $normalizedBarcode)
                      ->orWhereRaw("FIND_IN_SET(?, REPLACE(barcode, ' ', '')) > 0", [$normalizedBarcode]);
                })
                ->where('isDeleted', 0);
                
            if (!$enableVariants) {
                $productQuery->where('item_type', '!=', 'variant')->where(function($sub) {
                    $sub->whereNull('item_type')->orWhere('item_type', 'normal');
                });
            }
            
            $product = clone $productQuery->first();

            if ($product) {
                // Apply specific details from JSON if available
                $product = $this->applyBarcodeDetailsOverride($product, $normalizedBarcode);

                return response()->json([
                    'status' => true,
                    'product' => $this->formatLocalBarcodeProduct($product, $normalizedBarcode),
                ]);
            }

            // If still not found, try partial match as last resort
            $product = Product::where('isDeleted', 0)
                ->where(function ($q) use ($barcode, $normalizedBarcode) {
                    $q->where('barcode', 'like', "%{$barcode}%")
                        ->orWhere('barcode', 'like', "%{$normalizedBarcode}%");
                })
                ->first();
            if ($product) {
                // Check if specific barcode has custom details in JSON column
                $product = $this->applyBarcodeDetailsOverride($product, $normalizedBarcode);
                
                return response()->json([
                    'status' => true,
                    'product' => $this->formatLocalBarcodeProduct($product, $normalizedBarcode),
                ]);
            }

            $externalProduct = $this->fetchExternalBarcodeProduct($barcode);

            if ($externalProduct) {
                return response()->json([
                    'status' => true,
                    'product' => $externalProduct,
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function applyBarcodeDetailsOverride($product, $barcode)
    {
        if (empty($product->barcode_details)) {
            return $product;
        }

        $details = is_string($product->barcode_details)
            ? json_decode($product->barcode_details, true)
            : $product->barcode_details;
        if (!is_array($details)) {
            return $product;
        }

        $normalize = static function ($value) {
            return preg_replace('/\s+/', '', trim((string) $value));
        };
        $needle = $normalize($barcode);
        $specificDetails = null;

        foreach ($details as $code => $row) {
            if (!is_array($row)) {
                continue;
            }
            if ($normalize($code) === $needle) {
                $specificDetails = $row;
                break;
            }
        }

        if (is_array($specificDetails)) {
            if (isset($specificDetails['price']) && $specificDetails['price'] !== '') {
                $product->price = $specificDetails['price'];
            }
            if (isset($specificDetails['mrp']) && $specificDetails['mrp'] !== '') {
                $product->mrp = $specificDetails['mrp'];
            }
            if (isset($specificDetails['batch_no']) && $specificDetails['batch_no'] !== '') {
                $product->batch_no = $specificDetails['batch_no'];
            }
            if (isset($specificDetails['label_code']) && $specificDetails['label_code'] !== '') {
                $product->label_code = $specificDetails['label_code'];
            }
            if (isset($specificDetails['label_reference']) && $specificDetails['label_reference'] !== '') {
                $product->label_reference = $specificDetails['label_reference'];
            }
        }

        return $product;
    }



    // public function order_sale(Request $request)
    // {
    //     // ✅ Detect quotation
    //     $isQuotation = $request->quotation_status === 'quotation';

    //     // ✅ Validation (payment required ONLY for sales)
    //     $request->validate([
    //         'customer_id' => 'nullable|string|max:255',
    //         'customer_phone' => 'nullable|string|max:255',
    //         'payment_method' => $isQuotation ? 'nullable|string' : 'required|string',
    //         'bank_id'        => 'nullable|integer',
    //         'subtotal'       => 'required|numeric|min:0',
    //         'discount'       => 'nullable|numeric|min:0|max:100',
    //         'remarks'        => 'nullable|string|max:500',
    //         'tax'            => 'nullable|array',
    //         'items'          => 'required|array|min:1',
    //         'labour_items'   => 'nullable|array',
    //         'shipping' => 'nullable|numeric|min:0',
    //         'items.*.product_id' => 'required|integer|exists:products,id',
    //         'items.*.quantity' => 'required|integer|min:1',
    //         'items.*.price' => 'required|numeric|min:0',
    //         'items.*.total' => 'required|numeric|min:0',
    //         'total' => 'required|numeric|min n:0',
    //     ]);





    //     DB::beginTransaction();

    //     try {

    //         $userData = Auth::guard('api')->user();
    //         $userRole = $userData->role;

    //         // Branch logic
    //         if ($userRole === 'staff') {
    //             $branchIdToUse = $userData->branch_id;
    //         } elseif ($userRole === 'admin' && ! empty($request->selectedSubAdminId)) {
    //             $branchIdToUse = $request->selectedSubAdminId;
    //         } else {
    //             $branchIdToUse = $userData->id;
    //         }

    //         /*
    //     |--------------------------------------------------------------------------
    //     | STOCK CHECK (ONLY SALES)
    //     |--------------------------------------------------------------------------
    //     */
    //         $productIds = array_column($request->items, 'product_id');
    //         $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

    //         if (!$isQuotation) {
    //             foreach ($request->items as $item) {
    //                 $product = $products->get($item['product_id']);

    //                 if (!$product || $product->quantity < $item['quantity']) {
    //                     DB::rollBack();
    //                     return response()->json([
    //                         'status' => false,
    //                         'message' => "Insufficient stock for product: " .
    //                             ($product->name ?? "ID: {$item['product_id']}"),
    //                     ], 400);
    //                 }
    //             }
    //         }

    //         /*
    //     |--------------------------------------------------------------------------
    //     | CUSTOMER
    //     |--------------------------------------------------------------------------
    //     */
    //         if (!is_numeric($request->customer_id) && !empty($request->customer_id)) {

    //             $vendor = User::create([
    //                 'name' => $request->customer_id,
    //                 'phone' => $request->customer_phone,
    //                 'role' => 'customer',
    //                 'status' => 1,
    //                 'branch_id' => $branchIdToUse,
    //                 'created_by' => $userData->id,
    //             ]);

    //             UserDetail::create(['user_id' => $vendor->id]);

    //             $customer_id = $vendor->id;
    //         } elseif (is_numeric($request->customer_id)) {

    //             $customer_id = $request->customer_id;
    //         } else {

    //             $customer_id = User::where('role', 'customer')
    //                 ->where('name', 'Default Customer')
    //                 ->value('id');
    //         }

    //         /*
    //     |--------------------------------------------------------------------------
    //     | PAYMENT STATUS
    //     |--------------------------------------------------------------------------
    //     */
    //         $paymentMethod = $isQuotation
    //             ? 'pending'
    //             : $request->payment_method;

    //         $payment_status =
    //             (!$isQuotation && $paymentMethod != 'pending')
    //             ? 'completed'
    //             : 'pending';

    //         $remain =
    //             $payment_status === 'completed'
    //             ? 0
    //             : $request->total;

    //         // order_number is NOT NULL, so generate it before creating the order.
    //         $nextOrderId = (Order::max('id') ?? 0) + 1;
    //         $orderNumber = $isQuotation
    //             ? 'Q-' . $nextOrderId
    //             : now()->format('Ymd') . str_pad($nextOrderId, 5, '0', STR_PAD_LEFT);

    //         /*
    //     |--------------------------------------------------------------------------
    //     | CREATE ORDER
    //     |--------------------------------------------------------------------------
    //     */
    //         $order = Order::create([
    //             'order_number' => $orderNumber,
    //             'user_id' => $customer_id,
    //             'payment_method' => $paymentMethod,
    //             'discount' => $request->discount ?? 0,
    //             'tax_id' => !empty($request->tax) ? json_encode($request->tax) : null,
    //             'gst_option' => $request->gst_option === 'with'
    //                 ? 'with_gst'
    //                 : 'without_gst',
    //             'total_amount' => $request->total,
    //             'remaining_amount' => $remain,
    //             'payment_status' => $payment_status,
    //             'quotation_status' => $isQuotation ? 'quotation' : 'sales',
    //                'shipping'         => $request->shipping ?? 0,
    //             'remarks'          => $request->remarks,
    //             'branch_id'        => $branchIdToUse,
    //             'created_by'       => $userData->id,
    //         ]);

    //         /*
    //     |--------------------------------------------------------------------------
    //     | ITEMS + INVENTORY
    //     |--------------------------------------------------------------------------
    //     */
    //         $orderItemsData = [];
    //         $inventoryData = [];
    //         $productUpdates = [];
    //         $now = now();

    //         $lastInventories = ProductInventory::whereIn('product_id', $productIds)
    //             ->latest()
    //             ->get()
    //             ->groupBy('product_id')
    //             ->map(fn($g) => $g->first());

    //         foreach ($request->items as $item) {

    //             $product = $products->get($item['product_id']);
    //             $quantity = $item['quantity'];
    //             $price = $item['price'];

    //             $discountAmount = $item['discount_amount'] ?? 0;
    //             $finalAmount = ($price * $quantity) - $discountAmount;

    //             $newQuantity = $product->quantity - $quantity;

    //             // ✅ Reduce stock ONLY for SALES
    //             if (!$isQuotation) {
    //                 $productUpdates[$product->id] = ['quantity' => $newQuantity];
    //             }

    //             $orderItemsData[] = [
    //                 'order_id' => $order->id,
    //                 'user_id' => $customer_id,
    //                 'category_id' => $item['categoryId'] ?? null,
    //                 'product_id' => $item['product_id'],
    //                 'quantity'   => $quantity,
    //                 'price'      => $price,
    //                 'discount_percentage' => $item['discount_percentage'] ?? 0,
    //                 'discount_amount' => $discountAmount,
    //                 'product_gst_details' => isset($item['product_gst_details']) ? $item['product_gst_details'] : null,
    //                 'product_gst_total' => $item['product_gst_total'] ?? 0,
    //                 'total_amount' => $finalAmount,
    //                 'branch_id' => $branchIdToUse,
    //                 'created_by' => $userData->id,
    //                 'created_at' => $now,
    //                 'updated_at' => $now,
    //             ];

    //             // ✅ Inventory ONLY for SALES
    //             if (!$isQuotation) {
    //                 $lastInventory = $lastInventories->get($product->id);

    //                 $inventoryData[] = [
    //                     'product_id' => $product->id,
    //                     'initial_stock' => $lastInventory->initial_stock ?? $product->quantity,
    //                     'current_stock' => $newQuantity,
    //                     'branch_id' => $branchIdToUse,
    //                     'create_by' => $userData->id,
    //                     'type' => 'Sale',
    //                     'date' => $now,
    //                     'created_at' => $now,
    //                     'updated_at' => $now,
    //                 ];
    //             }
    //         }

    //         OrderItem::insert($orderItemsData);

    //         // ✅ Add Labour Items (with type for quotations/sales)
    //         if (!empty($request->labour_items)) {
    //             $labourItemsData = [];
    //             foreach ($request->labour_items as $litem) {
    //                 $labourItemsData[] = [
    //                     'order_id' => $order->id,
    //                     'user_id' => $customer_id,
    //                     'labour_item_id' => $litem['labour_item_id'],
    //                     'qty' => $litem['qty'],
    //                     'price' => $litem['price'],
    //                     'created_at' => $now,
    //                     'updated_at' => $now,
    //                 ];
    //             }
    //             Sales_Labour_Items::insert($labourItemsData);
    //         }

    //         if (!$isQuotation) {
    //             foreach ($productUpdates as $id => $data) {
    //                 Product::where('id', $id)->update($data);
    //             }

    //             if (!empty($inventoryData)) {
    //                 ProductInventory::insert($inventoryData);
    //             }
    //         }

    //         /*
    //     |--------------------------------------------------------------------------
    //     | PAYMENT (ONLY SALES)
    //     |--------------------------------------------------------------------------
    //     */
    //         if (!$isQuotation && $paymentMethod != 'pending') {

    //             PaymentStore::create([
    //                 'user_id' => $customer_id,
    //                 'order_id' => $order->id,
    //                 'payment_amount' => $request->total,
    //                 'payment_date' => now(),
    //                 'payment_method' => $paymentMethod,
    //                 'payment_type' => 'full',
    //                 'created_at' => now(),
    //             ]);
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'status' => true,
    //             'message' => $isQuotation
    //                 ? 'Quotation saved successfully!'
    //                 : 'Order placed successfully!',
    //             'order_id' => $order->id,
    //         ], 201);
    //     } catch (\Exception $e) {

    //         DB::rollBack();
    //         Log::error('Order placement error: ' . $e->getMessage());

    //         return response()->json([
    //             'status' => false,
    //             'message' => $e->getMessage(),
    //         ], 500);
    //     }
    // }
    private function generateAndSendSubscriptionInvoice(int $orderId, int $customerId, int $branchId): ?array
    {
        try {
            Log::info('Subscription invoice flow started', [
                'order_id' => $orderId,
                'customer_id' => $customerId,
                'branch_id' => $branchId,
            ]);

            $order = Order::with(['orderItems.product'])->find($orderId);
            if (! $order) {
                return null;
            }

            $planOrderItem = $order->orderItems->first(function ($item) {
                return (bool) optional($item->product)->is_plan;
            });

            if (! $planOrderItem || ! $planOrderItem->product) {
                return null;
            }

            $user = User::select('id', 'name', 'phone', 'age', 'gender', 'distance_from_shop')
                ->find($customerId);
            if (! $user) {
                return [
                    'status' => false,
                    'message' => 'Customer not found for subscription invoice generation.',
                ];
            }

            $userDetails = UserDetail::where('user_id', $customerId)->first();
            $product = $planOrderItem->product;

            $normalizedPlanItems = collect($product->plan_items ?? [])
                ->map(function ($item) {
                    if (is_array($item)) {
                        $itemId = (int) ($item['item_id'] ?? $item['product_id'] ?? $item['id'] ?? 0);
                        $quantity = (int) ($item['quantity'] ?? 1);
                    } elseif (is_numeric($item)) {
                        $itemId = (int) $item;
                        $quantity = 1;
                    } else {
                        $itemId = 0;
                        $quantity = 0;
                    }

                    if ($itemId <= 0) {
                        return null;
                    }

                    return [
                        'item_id' => $itemId,
                        'quantity' => max(1, $quantity),
                    ];
                })
                ->filter()
                ->values();

            $planItemIds = $normalizedPlanItems
                ->pluck('item_id')
                ->unique()
                ->values();

            $planProducts = $planItemIds->isEmpty()
                ? collect()
                : Product::select('id', 'name', 'price')
                    ->whereIn('id', $planItemIds)
                    ->get()
                    ->keyBy('id');

            $planItemsDetailed = $normalizedPlanItems
                ->map(function ($item) use ($planProducts) {
                    $meal = $planProducts->get((int) $item['item_id']);
                    if (! $meal) {
                        return null;
                    }

                    $quantity = (int) ($item['quantity'] ?? 1);
                    $price = (float) ($meal->price ?? 0);

                    return [
                        'name' => (string) $meal->name,
                        'price' => $price,
                        'quantity' => max(1, $quantity),
                        'line_total' => $price * max(1, $quantity),
                    ];
                })
                ->filter()
                ->values();

            $planItems = $planItemsDetailed
                ->map(fn($item) => $this->normalizePlanMealName((string) ($item['name'] ?? '')))
                ->filter()
                ->unique()
                ->values()
                ->all();

            $totalSubscriptionAmount = (float) $planItemsDetailed->sum(function ($item) {
                if (isset($item['line_total'])) {
                    return (float) $item['line_total'];
                }

                return (float) ($item['price'] ?? 0);
            });
            if ($totalSubscriptionAmount <= 0) {
                $totalSubscriptionAmount = (float) ($planOrderItem->total_amount ?? 0);
            }

            $distanceFromShop = (float) ($user->distance_from_shop ?? 0);
            $deliveryCharge = (float) ($order->extra_charges ?? 0);

            $setting = Setting::where('branch_id', $branchId)->first();

            // Build base64 logo for PDF (external URLs don't load in PDF renderers)
            $logoBase64 = null;
            $logoMime   = 'image/png';
            if ($setting && !empty($setting->logo)) {
                $logoPath = storage_path('app/public/' . $setting->logo);
                if (file_exists($logoPath)) {
                    $logoBase64 = base64_encode(file_get_contents($logoPath));
                    $logoMime   = mime_content_type($logoPath);
                }
            }

            $pdf = Pdf::loadView('sales.subscription_invoice', [
                'order' => $order,
                'user' => $user,
                'userDetails' => $userDetails,
                'product' => $product,
                'planItems' => $planItems,
                'planItemsDetailed' => $planItemsDetailed,
                'totalSubscriptionAmount' => $totalSubscriptionAmount,
                'deliveryCharge' => $deliveryCharge,
                'distanceFromShop' => $distanceFromShop,
                'setting' => $setting,
                'logoBase64' => $logoBase64,
                'logoMime' => $logoMime,
            ])->setPaper('A4', 'portrait')->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
            ]);

            Storage::disk('public')->makeDirectory('subscriptions');

            $fileName = 'subscription_invoice_' . $order->id . '_' . now()->format('Ymd_His') . '.pdf';
            $relativePath = 'subscriptions/' . $fileName;
            Storage::disk('public')->put($relativePath, $pdf->output());
            $absolutePath = Storage::disk('public')->path($relativePath);

            $fileUrl = asset(env('ImagePath') . 'storage/' . $relativePath);

            // WhatsApp is sent after order save: "Subscription complete" / "Subscription pending" when a subscription PDF exists,
            // otherwise "Complete order" / "Pending order"; or plain text + document fallback if no template sends.

            return [
                'status' => true,
                'file_url' => $fileUrl,
                'file_name' => $fileName,
                'relative_path' => $relativePath,
                'local_path' => $absolutePath,
                'whatsapp' => null,
            ];
        } catch (\Throwable $e) {
            Log::error('Subscription invoice generation failed', [
                'order_id' => $orderId,
                'customer_id' => $customerId,
                'branch_id' => $branchId,
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => false,
                'message' => 'Failed to generate/send subscription invoice: ' . $e->getMessage(),
            ];
        }
    }

    private function generateSalesInvoicePdfForWhatsApp(Order $order, int $branchId): ?array
    {
        try {
            $sales = Order::with(['staff:id,name', 'creator:id,name'])->find($order->id);
            if (! $sales) {
                return null;
            }

            $setting = Setting::where('branch_id', $branchId)->first();
            if (! $setting) {
                $setting = (object) [
                    'currency_position' => 'left',
                    'currency_symbol' => 'Rs.',
                    'invoice_size' => null,
                ];
            }

            $labourItems = Sales_Labour_Items::where('order_id', $sales->id)
                ->with('labourItem')
                ->get();

            $labourCost = 0;
            foreach ($labourItems as $labourItem) {
                $labourCost += ((float) ($labourItem->qty ?? 0)) * ((float) ($labourItem->price ?? 0));
            }

            $customerModel = $sales->user_id ? User::with('userDetail')->find($sales->user_id) : null;

            $formatCurrency = function ($amount) use ($setting) {
                $amount = number_format((float) $amount, 2);
                return ($setting->currency_position ?? 'left') === 'right'
                    ? $amount . ($setting->currency_symbol ?? 'Rs.')
                    : ($setting->currency_symbol ?? 'Rs.') . $amount;
            };

            $orderItems = OrderItem::where('order_id', $sales->id)->get();
            $subtotal = $orderItems->sum(function ($item) {
                return (float) ($item->total_amount ?? (((float) ($item->price ?? 0)) * ((float) ($item->quantity ?? 0))));
            });

            $discountPercent = (float) ($sales->discount ?? 0);
            $discountAmount = ($discountPercent / 100) * $subtotal;
            $afterDiscount = $subtotal - $discountAmount;

            $totalGstAmount = 0;
            $taxSummary = [];

            foreach ($orderItems as $item) {
                $totalGstAmount += (float) ($item->product_gst_total ?? 0);
                $gstDetails = $item->product_gst_details;

                if (is_string($gstDetails)) {
                    $gstDetails = json_decode($gstDetails, true);
                    if (is_string($gstDetails)) {
                        $gstDetails = json_decode($gstDetails, true);
                    }
                }

                if (is_array($gstDetails) && isset($gstDetails['tax_name'])) {
                    $gstDetails = [$gstDetails];
                }

                if (!empty($gstDetails) && is_array($gstDetails)) {
                    foreach ($gstDetails as $tax) {
                        if (!is_array($tax)) {
                            continue;
                        }

                        $taxName = $tax['tax_name'] ?? 'GST';
                        $taxRate = $tax['tax_rate'] ?? 0;
                        $taxAmount = (float) ($tax['tax_amount'] ?? 0);
                        $key = $taxName . '_' . $taxRate;

                        if (!isset($taxSummary[$key])) {
                            $taxSummary[$key] = [
                                'name' => $taxName,
                                'rate' => $taxRate,
                                'amount' => 0,
                            ];
                        }

                        $taxSummary[$key]['amount'] += $taxAmount;
                    }
                }
            }

            $taxDetails = collect($taxSummary)->map(function ($tax) use ($formatCurrency) {
                return [
                    'name' => $tax['name'],
                    'rate' => $tax['rate'],
                    'amount' => $tax['amount'],
                    'formatted_amount' => $formatCurrency($tax['amount']),
                ];
            })->values()->all();

            $returns = SalesReturn::with('items.product')
                ->where('order_id', $sales->id)
                ->get();

            $totalReturnAmount = (float) $returns->sum('total_amount');
            $shippingCharge = (float) ($sales->shipping ?? 0);
            $finalTotal = $afterDiscount + $totalGstAmount + $shippingCharge + $labourCost;
            $paidAmount = (float) PaymentStore::where('order_id', $sales->id)
                ->where(function ($q) {
                    $q->whereNull('isDeleted')->orWhere('isDeleted', 0);
                })
                ->sum('payment_amount');
            $pendingAmount = max(0, $finalTotal - $totalReturnAmount - $paidAmount);
            $extraPaid = max(0, $paidAmount - ($finalTotal - $totalReturnAmount));

            $emiPayments = PaymentStore::where('order_id', $sales->id)
                ->where(function ($q) {
                    $q->whereNull('isDeleted')->orWhere('isDeleted', 0);
                })
                ->where(function ($query) {
                    $query->where('payment_method', 'emi')
                        ->orWhere('payment_type', 'emi');
                })
                ->whereNotNull('emi_month')
                ->orderBy('emi_month')
                ->get();

            $pdfData = [
                'view_id' => $sales->id,
                'sales' => $sales,
                'setting' => $setting,
                'orderItems' => $orderItems,
                'salesItems' => $orderItems,
                'labourItems' => $labourItems,
                'returns' => $returns,
                'taxDetails1' => $taxDetails,
                'taxDetails' => $taxDetails,
                'totalGst' => $formatCurrency($totalGstAmount),
                'finalTotal' => $formatCurrency($finalTotal),
                'subtotal' => $formatCurrency($subtotal),
                'discountPercent' => $discountPercent,
                'discountAmount' => $formatCurrency($discountAmount),
                'afterDiscount' => (float) $afterDiscount,
                'formattedAfterDiscount' => $formatCurrency($afterDiscount),
                'shippingCharge' => $formatCurrency($shippingCharge),
                'labourCost' => $formatCurrency($labourCost),
                'returnAmount' => $formatCurrency($totalReturnAmount),
                'returnStatus' => $totalReturnAmount > 0 ? 'Returned' : 'No return',
                'returnStatusColor' => $totalReturnAmount > 0 ? '#ea5455' : '#28c76f',
                'totalReturnAmount' => $totalReturnAmount,
                'allItemsFullyReturned' => false,
                'customer' => [
                    'name' => $customerModel->name ?? 'Default-customer',
                    'company_name' => $customerModel->company_name ?? '',
                    'email' => $customerModel->email ?? '',
                    'phone' => $customerModel->phone ?? '',
                    'address' => optional($customerModel?->userDetail)->address ?? '',
                    'delivery_address' => optional($customerModel?->userDetail)->delivery_address ?? '',
                    'gst_number' => $customerModel->gst_number ?? '',
                    'pan_number' => $customerModel->pan_number ?? '',
                ],
                'user' => $customerModel ? $customerModel->toArray() : null,
                'paidAmount' => $paidAmount,
                'pendingAmount' => $formatCurrency($pendingAmount),
                'extraPaid' => $formatCurrency($extraPaid),
                'pendingAmountNumeric' => $pendingAmount,
                'emiPayments' => $emiPayments,
            ];

            $pdf = (($setting->invoice_size ?? null) === 'small')
                ? make_thermal_invoice_pdf('sales.salse-invoice-small-pdf', $pdfData, $pdfData['orderItems'] ?? ($pdfData['order_items'] ?? 1))
                : Pdf::loadView('sales.salse-invoice-pdf', $pdfData)->setPaper(...invoice_regular_paper($setting));

            Storage::disk('public')->makeDirectory('sales-invoices');

            $fileName = 'invoice_' . $sales->id . '_' . now()->format('Ymd_His') . '.pdf';
            $relativePath = 'sales-invoices/' . $fileName;
            Storage::disk('public')->put($relativePath, $pdf->output());

            return [
                'status' => true,
                'file_url' => asset(env('ImagePath') . 'storage/' . $relativePath),
                'file_name' => $fileName,
                'relative_path' => $relativePath,
            ];
        } catch (\Throwable $e) {
            Log::error('Sales invoice PDF generation for WhatsApp failed', [
                'order_id' => $order->id ?? null,
                'branch_id' => $branchId,
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => false,
                'message' => 'Failed to generate sales invoice PDF: ' . $e->getMessage(),
            ];
        }
    }

    public function checkRentalAvailabilityApi(Request $request)
    {
        $request->validate([
            'product_id'    => 'required|integer|exists:products,id',
            'rent_start_at' => 'required|date',
            'rent_end_at'   => 'required|date|after:rent_start_at',
            'quantity'      => 'required|integer|min:1',
        ]);

        $orderItem   = new OrderItem();
        $isAvailable = $orderItem->checkRentalAvailability(
            $request->product_id,
            $request->rent_start_at,
            $request->rent_end_at,
            $request->quantity
        );

        return response()->json([
            'status'    => true,
            'available' => $isAvailable,
            'message'   => $isAvailable ? 'Product is available' : 'Product not available for selected dates',
            'debug'     => [
                'product_id' => $request->product_id,
                'rent_start' => $request->rent_start_at,
                'rent_end' => $request->rent_end_at,
                'qty' => $request->quantity
            ]
        ]);
    }

    public function order_sale(Request $request)
    {
        // ✅ Detect quotation or advance receipt
        $isQuotation        = $request->quotation_status === 'quotation';
        $isAdvanceReceipt   = $request->quotation_status === 'advance_receipt';
        $skipStockDeduction = $isQuotation || $isAdvanceReceipt; // both skip stock
        $indiaNow = now('Asia/Kolkata');
        
        $rentalStatus = $request->rental_status === 'rental' ? 'rental' : 'sales';
        $isRental     = $rentalStatus === 'rental';

        // ✅ Validation (payment required ONLY for sales)
        $request->validate([
            'customer_id'     => 'nullable|string|max:255',
            'customer_phone'  => 'nullable|string|max:255',
            'order_date'      => 'nullable|date',
            'customer_email'  => 'nullable|email|max:255',
            'payment_method'  => ($isQuotation || $isAdvanceReceipt || $isRental) ? 'nullable|string' : 'required|string',
            'bank_id'        => 'nullable|integer',
            'paid_type' => 'nullable|in:cash_partially,cash_fully',
            'online_type' => 'nullable|in:online_partially,online_fully',
            'cash_online_type' => 'nullable|in:cash_online_partially,cash_online_fully',
            'amount' => 'nullable|numeric|min:0',
            'payment_amount' => 'nullable|numeric|min:0',
            'pending_amount' => 'nullable|numeric|min:0',
            'cash_amount' => 'nullable|numeric|min:0',
            'online_amount' => 'nullable|numeric|min:0',
            'subtotal'       => 'required|numeric|min:0',
            'discount'       => 'nullable|numeric|min:0|max:100',
            'remarks'        => 'nullable|string|max:500',
            'payment_remarks' => 'nullable|string|max:500',
            'tax'            => 'nullable|array',
            'items'          => 'required|array|min:1',
            'labour_items'   => 'nullable|array',
            'shipping' => 'nullable|numeric|min:0',
            'other_charges_name' => 'nullable|array',
            'other_charges_name.*' => 'nullable|string|max:255',
            'other_charges_amount' => 'nullable|array',
            'other_charges_amount.*' => 'nullable|numeric|min:0',
            'exchange_material_id' => 'nullable|integer|exists:materials,id',
            'exchange_type_id'     => 'nullable|integer|exists:types,id',
            'exchange_price'       => 'nullable|numeric|min:0',
            'exchange_weight'      => 'nullable|numeric|min:0',
            'exchange_amount'      => 'nullable|numeric|min:0',
            'exchange_details'     => 'nullable',
            'exchange_surplus'     => 'nullable|numeric|min:0',
            'exchange_surplus_action' => 'nullable|in:return_cash,deposit',
            'customer_deposit_applied' => 'nullable|numeric|min:0',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'tds_percentage' => 'nullable|numeric|min:0|max:100',
            'tds_amount' => 'nullable|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'emi_down_payment'   => 'nullable|numeric|min:0',
            'emi_loan_amount'    => 'nullable|numeric|min:0',
            'emi_interest_rate'  => 'nullable|numeric|min:0|max:100',
            'emi_tenure'         => 'nullable|string',
            'emi_monthly_amount' => 'nullable|numeric|min:0',
            'emi_aadhar_number'  => 'nullable|string|max:20',
            'emi_pan_number'     => 'nullable|string|max:20',
            'emi_guarantor_name' => 'nullable|string|max:255',
        ]);





        DB::beginTransaction();

        try {

            $userData = Auth::guard('api')->user();
            $userRole = $userData->role;

            // Branch logic
            if ($userRole === 'staff') {
                $branchIdToUse = $userData->branch_id;
            } elseif ($userRole === 'admin' && ! empty($request->selectedSubAdminId)) {
                $branchIdToUse = $request->selectedSubAdminId;
            } else {
                $branchIdToUse = $userData->id;
            }

            $productIds = array_column($request->items, 'product_id');
            $products = Product::with('category')->whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');
            $branchSetting = Setting::where('branch_id', $branchIdToUse)->first();
            $isTdsEnabled = (bool) ($branchSetting->tds_apply ?? false);

            $shippingAmount = (float) ($request->shipping ?? 0);
            $labourSubtotal = 0;
            if (!empty($request->labour_items) && is_array($request->labour_items)) {
                foreach ($request->labour_items as $labourItem) {
                    $labourSubtotal += ((float) ($labourItem['qty'] ?? 0)) * ((float) ($labourItem['price'] ?? 0));
                }
            }

            $productTotalBeforeTds = 0;
            $productGstTotal = 0;
            $globalWithGst = ($request->gst_option === 'without' || $request->gst_option === 'without_gst') ? false : true;
            foreach ($request->items as $item) {
                $product = $products->get($item['product_id']);
                $isRentalItem = ($item['is_rental'] ?? 0) == 1;
                $rentMultiplier = $isRentalItem ? max(1, (float) ($item['rent_value'] ?? 1)) : 1;
                $baseAmount = ((float) ($item['price'] ?? 0)) * ((float) ($item['quantity'] ?? 0)) * $rentMultiplier;
                $itemDiscountAmount = (float) ($item['discount_amount'] ?? 0);
                
                $inclusiveGst = $this->inclusiveGstDetails($baseAmount, $product, $globalWithGst);
                $itemGstAmount = $inclusiveGst['total'];
                $productGstTotal += $itemGstAmount;
                
                $productTotalBeforeTds += max(0, $baseAmount + $itemGstAmount - $itemDiscountAmount);
            }

            $otherCharges = [];
            $otherChargesTotal = 0;
            if (!empty($request->other_charges_name) && is_array($request->other_charges_name)) {
                foreach ($request->other_charges_name as $index => $name) {
                    $amount = (float) ($request->other_charges_amount[$index] ?? 0);
                    if (!empty($name) && $amount > 0) {
                        $otherCharges[] = [
                            'name' => $name,
                            'amount' => $amount
                        ];
                        $otherChargesTotal += $amount;
                    }
                }
            }

            $exchangeAmount = max(0, (float) ($request->exchange_amount ?? 0));
            // Bill-level additional discount is % of amount after item discounts (excl. GST) — matches POS + print.
            $afterItemDiscountBase = max(0, $productTotalBeforeTds - $productGstTotal);
            $billDiscountPercent = max(0, min(100, (float) ($request->discount ?? 0)));
            $billDiscountAmount = round(($afterItemDiscountBase * $billDiscountPercent) / 100, 2);
            $grossBeforeExchange = max(0, $productTotalBeforeTds - $billDiscountAmount + $shippingAmount + $labourSubtotal + $otherChargesTotal);
            $preTdsTotal = max(0, $grossBeforeExchange - $exchangeAmount);
            $tdsPercentage = $isTdsEnabled ? max(0, min(100, (float) ($request->tds_percentage ?? 0))) : 0;
            $tdsAmount = $isTdsEnabled ? round(($preTdsTotal * $tdsPercentage) / 100, 2) : 0;
            $roundedTotal = max(0, round($preTdsTotal - $tdsAmount));
            // Surplus when exchange value exceeds bill gross (owed back / depositable).
            $exchangeSurplus = max(0, round($exchangeAmount - $grossBeforeExchange, 2));
            $exchangeSurplusAction = $exchangeSurplus > 0
                ? (in_array($request->exchange_surplus_action, ['return_cash', 'deposit'], true)
                    ? $request->exchange_surplus_action
                    : null)
                : null;
            $customerDepositApplied = max(0, (float) ($request->customer_deposit_applied ?? 0));

        /*
        |--------------------------------------------------------------------------
        | STOCK CHECK (ONLY SALES / RENTALS)
        |--------------------------------------------------------------------------
        */
            if (!$isQuotation) {
                foreach ($request->items as $item) {
                    $product = $products->get($item['product_id']);

                    if ($this->isWarrantyCategoryProduct($product)) {
                        continue;
                    }

                    if ($isRental) {
                        $isAvailable = (new OrderItem())->checkRentalAvailability(
                            $product->id,
                            $item['rent_start_at'] ?? now(),
                            $item['rent_end_at'] ?? now()->addDay(),
                            $item['quantity']
                        );
                        if (!$isAvailable) {
                            DB::rollBack();
                            return response()->json([
                                'status' => false,
                                'message' => "Insufficient rental availability for product: " .
                                    ($product->name ?? "ID: {$item['product_id']}"),
                            ], 400);
                        }
                    }
                }
            }

            /*
        |--------------------------------------------------------------------------
        | CUSTOMER
        |--------------------------------------------------------------------------
        */
            if (!is_numeric($request->customer_id) && !empty($request->customer_id)) {

                $vendor = User::create([
                    'name' => $request->customer_id,
                    'phone' => $request->customer_phone,
                    'email' => $request->customer_email,
                    'role' => 'customer',
                    'status' => 1,
                    'branch_id' => $branchIdToUse,
                    'created_by' => $userData->id,
                ]);

                UserDetail::create(['user_id' => $vendor->id]);

                $customer_id = $vendor->id;
            } elseif (is_numeric($request->customer_id)) {

                $customer_id = $request->customer_id;
            } else {

                $customer_id = User::where('role', 'customer')
                    ->where('name', 'Default Customer')
                    ->where('branch_id', $branchIdToUse)
                    ->value('id');
            }

            // Exchange surplus must choose Return Cash or Deposit when exchange > bill.
            if ($exchangeSurplus > 0 && !$exchangeSurplusAction) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Exchange value is higher than bill total. Choose Return Cash or Deposit to Customer.',
                ], 422);
            }

            if ($exchangeSurplus > 0 && $exchangeSurplusAction === 'deposit') {
                $customerName = User::where('id', $customer_id)->value('name');
                if (!$customer_id || strcasecmp((string) $customerName, 'Default Customer') === 0) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => 'Select a customer to deposit the exchange surplus.',
                    ], 422);
                }
            }

            // Apply existing customer deposit toward this bill (future use of deposited money).
            if ($customerDepositApplied > 0) {
                $customerName = User::where('id', $customer_id)->value('name');
                if (!$customer_id || strcasecmp((string) $customerName, 'Default Customer') === 0) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => 'Select a customer to use deposit balance.',
                    ], 422);
                }
                $availableDeposit = CustomerDeposit::balanceFor((int) $customer_id, (int) $branchIdToUse);
                $customerDepositApplied = min($customerDepositApplied, $availableDeposit, $roundedTotal);
            } else {
                $customerDepositApplied = 0;
            }

            $payableAfterDeposit = max(0, round($roundedTotal - $customerDepositApplied, 2));

            /*
        |--------------------------------------------------------------------------
        | PAYMENT STATUS
        |--------------------------------------------------------------------------
        */
            $rawPaymentMethod = strtolower(trim((string) $request->payment_method));
            $normalizedPaymentMethod = match ($rawPaymentMethod) {
                'cash+online', 'cash_online', 'cash + online' => 'cash_online',
                'debit card', 'debit', 'scan', 'online', 'upi' => 'online',
                'emi' => 'emi',
                'cash' => 'cash',
                'customer_deposit', 'deposit' => 'customer_deposit',
                'pending', 'pay later' => 'pending',
                default => $rawPaymentMethod !== '' ? $rawPaymentMethod : 'pending',
            };

            // For advance receipt: keep the actual payment method (cash/online) so payment is recorded.
            // For pure quotation: always 'pending' (no payment recorded).
            $paymentMethod = $isQuotation ? 'pending' : $normalizedPaymentMethod;

            $paidTypeRaw = strtolower((string) ($request->paid_type ?? $request->online_type ?? $request->cash_online_type ?? ''));
            $isPartiallyPaid = str_contains($paidTypeRaw, 'partially');

            $cashAmount = max(0, (float) ($request->cash_amount ?? 0));
            $onlineAmount = max(0, (float) ($request->online_amount ?? 0));
            $emiDownPayment = max(0, (float) ($request->emi_down_payment ?? 0));

            $requestedPaidAmount = max(0, (float) ($request->payment_amount ?? $request->amount ?? 0));
            if ($paymentMethod === 'pending' || $paymentMethod === 'customer_deposit') {
                $requestedPaidAmount = 0;
            } elseif ($requestedPaidAmount <= 0 && $request->filled('pending_amount')) {
                $requestedPaidAmount = max(0, $payableAfterDeposit - (float) $request->pending_amount);
            }
            if (!$isQuotation && $paymentMethod === 'emi') {
                $requestedPaidAmount = $emiDownPayment;
            }

            if (!$isQuotation && $paymentMethod === 'cash_online') {
                $requestedPaidAmount = $cashAmount + $onlineAmount;
                if ($requestedPaidAmount <= 0) {
                    // Backward compatibility for old payloads: treat as fully paid.
                    $requestedPaidAmount = $payableAfterDeposit;
                    $onlineAmount = $payableAfterDeposit;
                }
            } elseif (
                !$isQuotation &&
                $paymentMethod !== 'pending' &&
                $paymentMethod !== 'customer_deposit' &&
                !$request->filled('payment_amount') &&
                !$request->filled('amount') &&
                !$isPartiallyPaid
            ) {
                // Backward compatibility for old payloads: full payment by default.
                $requestedPaidAmount = $payableAfterDeposit;
            }

            // For quotation: no payment. For advance_receipt: record the advance amount.
            // Allow paid amount to exceed order total for extra advances ONLY for rentals
            $paidAmount = $isQuotation
                ? 0
                : ($isRental ? $requestedPaidAmount : min($payableAfterDeposit, $requestedPaidAmount));

            $isPosEdit = $request->boolean('is_pos_edit') || $request->filled('edit_order_id');

            // If converting quotation/advance → sale, keep prior payments as credit toward this bill.
            // Editing a paid sale must not: those payments belong to this bill and are replaced below.
            $existingPaidAmount = 0;
            if (!$isPosEdit && $request->has('convert_order_id') && $request->convert_order_id) {
                $existingPaidAmount = \App\Models\PaymentStore::where('order_id', $request->convert_order_id)
                    ->where(function ($q) {
                        $q->whereNull('isDeleted')->orWhere('isDeleted', 0);
                    })
                    ->sum('payment_amount');
            }

            // Deposit applied counts toward settling the bill.
            $totalPaidSoFar = $existingPaidAmount + $paidAmount + $customerDepositApplied;

            if ($payableAfterDeposit <= 0 && $roundedTotal <= 0) {
                $payment_status = 'completed';
            } elseif ($totalPaidSoFar <= 0) {
                $payment_status = 'pending';
            } elseif ($totalPaidSoFar < $roundedTotal) {
                $payment_status = 'partially';
            } else {
                $payment_status = 'completed';
            }

            $remain = max($roundedTotal - $totalPaidSoFar, 0);

            $orderCreatedAt = $request->filled('order_date')
                ? Carbon::parse($request->order_date)->timezone('Asia/Kolkata')
                : $indiaNow->copy();

            $financialYearNumberingEnabled = $this->isFinancialYearOrderNumberingEnabled((int) $branchIdToUse);

            $normalizedQuotationStatus = $isQuotation ? 'quotation' : ($isAdvanceReceipt ? 'advance_receipt' : ($isRental ? 'rental' : 'sales'));

            // order_number is NOT NULL, so generate it before creating the order.
            if ($isQuotation || $isAdvanceReceipt || $isRental) {
                if ($financialYearNumberingEnabled) {
                    $orderNumber = $this->generateFinancialYearOrderNumber(
                        (int) $branchIdToUse,
                        $normalizedQuotationStatus,
                        $orderCreatedAt->copy()
                    );
                } else {
                    if ($isAdvanceReceipt) {
                        // Find the max numeric suffix from existing ADV-N order numbers for this branch
                        // Search across ALL branches to ensure global uniqueness (the unique constraint is on order_number alone)
                        $maxAdvSeq = (int) (Order::where('quotation_status', 'advance_receipt')
                            ->whereRaw("order_number REGEXP '^ADV-[0-9]+$'")
                            ->selectRaw("MAX(CAST(SUBSTRING(order_number, 5) AS UNSIGNED)) as max_seq")
                            ->value('max_seq') ?? 0);
                        $orderNumber = 'ADV-' . ($maxAdvSeq + 1);
                    } elseif ($isRental) {
                        $maxRentalSeq = (int) (Order::where('quotation_status', 'rental')
                            ->whereRaw("order_number REGEXP '^RO-[0-9]+$'")
                            ->selectRaw("MAX(CAST(SUBSTRING(order_number, 4) AS UNSIGNED)) as max_seq")
                            ->value('max_seq') ?? 0);
                        $orderNumber = 'RO-' . str_pad($maxRentalSeq + 1, 6, '0', STR_PAD_LEFT);
                    } else {
                        // Quotation: find max Q-N across all branches (unique constraint is global)
                        $maxQSeq = (int) (Order::where('quotation_status', 'quotation')
                            ->whereRaw("order_number REGEXP '^Q-[0-9]+$'")
                            ->selectRaw("MAX(CAST(SUBSTRING(order_number, 3) AS UNSIGNED)) as max_seq")
                            ->value('max_seq') ?? 0);
                        $orderNumber = 'Q-' . ($maxQSeq + 1);
                    }

                    // Collision guard: keep incrementing until we find a number not already in the DB
                    $attempts = 0;
                    while (Order::where('order_number', $orderNumber)->exists() && $attempts < 50) {
                        $attempts++;
                        if ($isAdvanceReceipt) {
                            $maxAdvSeq = (int) (Order::where('quotation_status', 'advance_receipt')
                                ->whereRaw("order_number REGEXP '^ADV-[0-9]+$'")
                                ->selectRaw("MAX(CAST(SUBSTRING(order_number, 5) AS UNSIGNED)) as max_seq")
                                ->value('max_seq') ?? 0);
                            $orderNumber = 'ADV-' . ($maxAdvSeq + 1);
                        } elseif ($isRental) {
                            $maxRentalSeq = (int) (Order::where('quotation_status', 'rental')
                                ->whereRaw("order_number REGEXP '^RO-[0-9]+$'")
                                ->selectRaw("MAX(CAST(SUBSTRING(order_number, 4) AS UNSIGNED)) as max_seq")
                                ->value('max_seq') ?? 0);
                            $orderNumber = 'RO-' . str_pad($maxRentalSeq + 1, 6, '0', STR_PAD_LEFT);
                        } else {
                            $maxQSeq = (int) (Order::where('quotation_status', 'quotation')
                                ->whereRaw("order_number REGEXP '^Q-[0-9]+$'")
                                ->selectRaw("MAX(CAST(SUBSTRING(order_number, 3) AS UNSIGNED)) as max_seq")
                                ->value('max_seq') ?? 0);
                            $orderNumber = 'Q-' . ($maxQSeq + 1);
                        }
                    }
                }
            } else {
                $orderNumber = $this->generateSalesOrderNumber((int) $branchIdToUse);
            }

            /*
        |--------------------------------------------------------------------------
        | CREATE ORDER
        |--------------------------------------------------------------------------
         */
            $emiTenureRaw = $request->emi_tenure ?? null;
            if ($emiTenureRaw === 'custom') {
                $emiTenureRaw = (string) max(0, (int) ($request->emi_custom_tenure ?? 0));
            }
            $emiMonths = ($emiTenureRaw && is_numeric($emiTenureRaw)) ? (int) $emiTenureRaw : 0;
            $emiInterestRate = max(0, (float) ($request->emi_interest_rate ?? 0));
            $emiLoanAmount = max(0, (float) ($request->emi_loan_amount ?? ($roundedTotal - $emiDownPayment)));
            $emiMonthlyAmount = 0;
            if ($emiMonths > 0) {
                $interestAmount = $emiLoanAmount * ($emiInterestRate / 100);
                $emiMonthlyAmount = ($emiLoanAmount + $interestAmount) / $emiMonths;
            } elseif ($emiLoanAmount > 0) {
                $emiMonthlyAmount = $emiLoanAmount;
            }

            $orderDataPayload = [
                'order_number' => $orderNumber,
                'user_id' => $customer_id,
                'payment_method' => $paymentMethod,
                'discount' => $request->discount ?? 0,
                'tax_id' => !empty($request->tax) ? json_encode($request->tax) : null,
                'gst_option' => ($request->gst_option === 'without' || $request->gst_option === 'without_gst') ? 'without_gst' : 'with_gst',
                'total_amount' => $roundedTotal,
                'remaining_amount' => $remain,
                'payment_status' => $payment_status,
                'quotation_status' => $normalizedQuotationStatus,
                'rental_status'    => $normalizedQuotationStatus,
                'return_status'    => $isRental ? 'pending' : null,
                'shipping'         => $request->shipping ?? 0,
                'other_charges'    => !empty($otherCharges) ? $otherCharges : null,
                'exchange_material_id' => $request->exchange_material_id ?: null,
                'exchange_type_id'     => $request->exchange_type_id ?: null,
                'exchange_price'       => $request->exchange_price ? (float) $request->exchange_price : 0,
                'exchange_weight'      => $request->exchange_weight ? (float) $request->exchange_weight : 0,
                'exchange_amount'      => $exchangeAmount,
                'exchange_surplus'     => $exchangeSurplus,
                'exchange_surplus_action' => $exchangeSurplusAction,
                'exchange_details'     => !empty($request->exchange_details) ? (is_array($request->exchange_details) ? $request->exchange_details : json_decode($request->exchange_details, true)) : null,
                'tds_percentage'   => $tdsPercentage,
                'tds_amount'       => $tdsAmount,
                'deposit_amount'   => $request->deposit_amount ?? 0,
                'customer_deposit_applied' => $customerDepositApplied,
                'remarks'          => $request->remarks,
                'branch_id'        => $branchIdToUse,
                'staff_id'         => !empty($request->assigned_staff_id) ? (int) $request->assigned_staff_id : null,
                'order_type'       => $request->order_type ?? 'self_pickup',
                'updated_at'       => $orderCreatedAt,
                // EMI fields
                'emi_down_payment'  => $normalizedPaymentMethod === 'emi' ? $emiDownPayment : null,
                'emi_loan_amount'   => $normalizedPaymentMethod === 'emi' ? $emiLoanAmount : null,
                'emi_interest_rate' => $normalizedPaymentMethod === 'emi' ? $emiInterestRate : null,
                'emi_tenure'        => $normalizedPaymentMethod === 'emi' ? $emiTenureRaw : null,
                'emi_monthly_amount'  => $normalizedPaymentMethod === 'emi' ? round($emiMonthlyAmount, 2) : null,
                'emi_aadhar_number' => $normalizedPaymentMethod === 'emi' ? ($request->emi_aadhar_number ?? null) : null,
                'emi_do_id'         => $normalizedPaymentMethod === 'emi' ? ($request->emi_do_id ?? null) : null,
                'emi_pan_number'    => $normalizedPaymentMethod === 'emi' ? ($request->emi_pan_number ?? null) : null,
                'emi_guarantor_name' => $normalizedPaymentMethod === 'emi' ? ($request->emi_guarantor_name ?? null) : null,
            ];

            if ($request->has('convert_order_id') && $request->convert_order_id) {
                $order = Order::find($request->convert_order_id);
                if ($order) {
                    if ($isPosEdit) {
                        unset($orderDataPayload['order_number']);

                        // Original fully/partially paid amount is this bill's payment, not an advance.
                        // Replace it with whatever the user chooses on this edit (Pay Later, cash, etc.).
                        PaymentStore::where('order_id', $order->id)
                            ->where(function ($q) {
                                $q->whereNull('isDeleted')->orWhere('isDeleted', 0);
                            })
                            ->update(['isDeleted' => 1]);

                        $priorDeposits = CustomerDeposit::where('order_id', $order->id)
                            ->whereIn('source', ['bill_payment', 'exchange_surplus'])
                            ->orderBy('id')
                            ->get();
                        foreach ($priorDeposits as $entry) {
                            $notes = 'Reversed on bill edit #' . $order->order_number;
                            $branchForEntry = $entry->branch_id ? (int) $entry->branch_id : null;
                            if ($entry->entry_type === 'debit') {
                                CustomerDeposit::credit(
                                    (int) $entry->customer_id,
                                    (float) $entry->amount,
                                    $branchForEntry,
                                    (int) $order->id,
                                    'edit_reversal',
                                    $notes,
                                    (int) $userData->id
                                );
                            } elseif ($entry->entry_type === 'credit') {
                                $avail = CustomerDeposit::balanceFor((int) $entry->customer_id, $branchForEntry);
                                $amt = min((float) $entry->amount, $avail);
                                if ($amt > 0) {
                                    CustomerDeposit::debit(
                                        (int) $entry->customer_id,
                                        $amt,
                                        $branchForEntry,
                                        (int) $order->id,
                                        'edit_reversal',
                                        $notes,
                                        (int) $userData->id
                                    );
                                }
                            }
                        }
                    }

                    // Revert old items stock before deleting
                    $oldItems = OrderItem::where('order_id', $order->id)->get();
                    foreach ($oldItems as $oldItem) {
                        if ($oldItem->product_variant_id) {
                            $vObj = \App\Models\ProductVariant::find($oldItem->product_variant_id);
                            if ($vObj) {
                                $vLastInv = ProductInventory::where('product_variant_id', $vObj->id)->where('branch_id', $branchIdToUse)->latest()->first();
                                $vPrevStock = $vLastInv ? (float)$vLastInv->current_stock : (float)$vObj->qty;
                                $vNewStock = $vPrevStock + (float)$oldItem->quantity;
                                $vObj->update(['qty' => $vNewStock]);

                                ProductInventory::create([
                                    'product_id'         => $oldItem->product_id,
                                    'product_variant_id' => $vObj->id,
                                    'initial_stock'      => $vPrevStock,
                                    'current_stock'      => $vNewStock,
                                    'branch_id'          => $branchIdToUse,
                                    'create_by'          => $userData->id,
                                    'type'               => 'Edit Reversal',
                                    'date'               => now(),
                                ]);
                            }
                        } else if ($oldItem->product_id) {
                            $pObj = Product::find($oldItem->product_id);
                            if ($pObj) {
                                $pObj->increment('quantity', $oldItem->quantity);
                            }
                        }
                    }

                    $order->update($orderDataPayload);
                    // Delete old items so we can insert the newly submitted ones without duplicates
                    OrderItem::where('order_id', $order->id)->delete();
                    Sales_Labour_Items::where('order_id', $order->id)->delete();
                } else {
                    $orderDataPayload['created_by'] = $userData->id;
                    $orderDataPayload['created_at'] = $orderCreatedAt;
                    $order = $this->createOrderWithUniqueNumber(
                        $orderDataPayload,
                        (int) $branchIdToUse,
                        $normalizedQuotationStatus === 'sales'
                    );
                }
            } else {
                $orderDataPayload['created_by'] = $userData->id;
                $orderDataPayload['created_at'] = $orderCreatedAt;
                $order = $this->createOrderWithUniqueNumber(
                    $orderDataPayload,
                    (int) $branchIdToUse,
                    $normalizedQuotationStatus === 'sales'
                );
            }

            /*
        |--------------------------------------------------------------------------
        | ITEMS + INVENTORY
        |--------------------------------------------------------------------------
        */
            $orderItemsData = [];
            $inventoryData = [];
            $productUpdates = [];
            $now = $orderCreatedAt->copy();

            $lastInventories = ProductInventory::whereIn('product_id', $productIds)
                ->where('branch_id', $branchIdToUse)
                ->latest('id')
                ->get()
                ->groupBy('product_id')
                ->map(fn($g) => $g->first());

            foreach ($request->items as $item) {

                $product = $products->get($item['product_id']);
                $quantity = (float) $item['quantity'];
                $price = (float) $item['price'];

                $isRentalItem = ($item['is_rental'] ?? 0) == 1;
                $rentMultiplier = $isRentalItem ? max(1, (float) ($item['rent_value'] ?? 1)) : 1;
                $discountAmount = $item['discount_amount'] ?? 0;
                $subTotal = $price * $quantity * $rentMultiplier;
                
                $globalWithGst = ($request->gst_option === 'without' || $request->gst_option === 'without_gst') ? false : true;
                $inclusiveGst = $this->inclusiveGstDetails($subTotal, $product, $globalWithGst);
                $itemGstAmount = $inclusiveGst['total'];
                
                $inclusiveLineTotal = $subTotal + $itemGstAmount;
                $finalAmount = max(0, $inclusiveLineTotal - $discountAmount);

                // Rate 1 stock is products.quantity. Other prices keep their own qty.
                $currentProductStock = isset($productUpdates[$product->id]['quantity']) ? $productUpdates[$product->id]['quantity'] : (float) $product->quantity;
                $isMainPrice = abs($price - (float) $product->price) < 0.0001;
                $newQuantity = $isMainPrice ? ($currentProductStock - $quantity) : $currentProductStock;

                // ✅ Reduce stock for SALES and RENTALS (not quotation, not advance_receipt)
                // Allow overselling / negative stock so sales can proceed when available is 0.
                if (!$skipStockDeduction && !$this->isWarrantyCategoryProduct($product)) {
                    if (!isset($productUpdates[$product->id])) {
                        $productUpdates[$product->id] = [
                            'quantity' => (float) $product->quantity,
                            'rate_sales' => [],
                        ];
                    }
                    if ($isMainPrice) {
                        $productUpdates[$product->id]['quantity'] = $newQuantity;
                    } else {
                        $productUpdates[$product->id]['rate_sales'][] = [
                            'price' => $price,
                            'qty' => $quantity,
                        ];
                    }
                }

                $orderItemsData[] = [
                    'order_id' => $order->id,
                    'user_id' => $customer_id,
                    'category_id' => $item['categoryId'] ?? null,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['variant_id'] ?? null,
                    'quantity'   => $quantity,
                    'rental_quantity' => $isRental ? 0 : null,
                    'price'      => $price,
                    'discount_percentage' => $item['discount_percentage'] ?? 0,
                    'discount_amount' => $discountAmount,
                    'product_gst_details' => ! empty($inclusiveGst['details']) ? json_encode($inclusiveGst['details']) : null,
                    'product_gst_total' => $itemGstAmount,
                    'total_amount' => $finalAmount,
                    'is_rental'       => $isRentalItem,
                    'rent_value'      => $item['rent_value'] ?? null,
                    'rent_price'      => $item['rent_price'] ?? null,
                    'rent_period'     => $item['rent_period'] ?? null,
                    'rent_start_at'   => $isRentalItem && ! empty($item['rent_start_at'])
                        ? Carbon::parse($item['rent_start_at'])
                        : null,
                    'rent_end_at'     => $isRentalItem && ! empty($item['rent_end_at'])
                        ? Carbon::parse($item['rent_end_at'])
                        : null,
                    'imei_no' => $item['imei_no'] ?? null,
                    'branch_id' => $branchIdToUse,
                    'created_by' => $userData->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                // ✅ Inventory for SALES and RENTALS (not quotation, not advance_receipt)
                // Rental inventory entries use type 'Rental' so they can be distinguished in reports.
                if (!$skipStockDeduction && !$this->isWarrantyCategoryProduct($product)) {
                    $inventoryType = $isRentalItem ? 'Rental' : 'Sale';
                    $variantId = $item['variant_id'] ?? null;
                    if ($variantId) {
                        $lastInv = ProductInventory::where('product_variant_id', $variantId)
                            ->where('branch_id', $branchIdToUse)
                            ->latest()
                            ->first();
                        $variantObj = \App\Models\ProductVariant::find($variantId);
                        $prevStock = $lastInv ? (float)$lastInv->current_stock : (float)($variantObj->qty ?? 0);
                        $variantNewStock = max(0, $prevStock - $quantity);

                        if ($variantObj) {
                            $variantObj->update(['qty' => $variantNewStock]);
                        }

                        $inventoryData[] = [
                            'product_id'         => $product->id,
                            'product_variant_id' => $variantId,
                            'initial_stock'      => $prevStock,
                            'current_stock'      => $variantNewStock,
                            'branch_id'          => $branchIdToUse,
                            'create_by'          => $userData->id,
                            'type'               => $inventoryType,
                            'date'               => $now,
                            'created_at'         => $now,
                            'updated_at'         => $now,
                        ];
                    } else {
                        $lastInventory = $lastInventories->get($product->id);
                        $prevStock = $lastInventory ? (float) $lastInventory->current_stock : (float) $product->quantity;
                        $inventoryData[] = [
                            'product_id'         => $product->id,
                            'product_variant_id' => null,
                            'initial_stock'      => $prevStock,
                            'current_stock'      => $newQuantity,
                            'branch_id'          => $branchIdToUse,
                            'create_by'          => $userData->id,
                            'type'               => $inventoryType,
                            'date'               => $now,
                            'created_at'         => $now,
                            'updated_at'         => $now,
                        ];
                    }
                }
            }

            OrderItem::insert($orderItemsData);

            // ✅ Add Labour Items (with type for quotations/sales)
            if (!empty($request->labour_items)) {
                $labourItemsData = [];
                foreach ($request->labour_items as $litem) {
                    $labourItemsData[] = [
                        'order_id' => $order->id,
                        'user_id' => $customer_id,
                        'labour_item_id' => $litem['labour_item_id'],
                        'qty' => $litem['qty'],
                        'price' => $litem['price'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                Sales_Labour_Items::insert($labourItemsData);
            }

            if (!$isQuotation) {
                foreach ($productUpdates as $id => $data) {
                    $rateSales = $data['rate_sales'] ?? [];
                    unset($data['rate_sales']);
                    $stockProduct = Product::find($id);
                    if ($stockProduct) {
                        $stockProduct->quantity = $data['quantity'] ?? $stockProduct->quantity;
                        $stockProduct->applyRateSales($rateSales, (float) $stockProduct->quantity);
                        $stockProduct->save();
                    }
                }

                if (!empty($inventoryData)) {
                    ProductInventory::insert($inventoryData);
                }
            }

        /*
        |--------------------------------------------------------------------------
        | PAYMENT (SALES + ADVANCE RECEIPT)
        |--------------------------------------------------------------------------
        */
            if ((!$skipStockDeduction || $isAdvanceReceipt) && $paymentMethod !== 'pending' && $paidAmount > 0) {
                $paymentTypeForStore = $payment_status === 'partially' ? 'partially' : 'fully';
                $bankId = $request->bank_id ?: null;

                if ($paymentMethod === 'cash_online') {
                    $cashToStore = 0.0;
                    $onlineToStore = 0.0;

                    if (($cashAmount + $onlineAmount) > 0) {
                        $cashToStore = min($cashAmount, $paidAmount);
                        $onlineToStore = min($onlineAmount, max($paidAmount - $cashToStore, 0));

                        $remainingSplit = max($paidAmount - ($cashToStore + $onlineToStore), 0);
                        if ($remainingSplit > 0) {
                            $onlineToStore += $remainingSplit;
                        }
                    } else {
                        $onlineToStore = $paidAmount;
                    }

                    if ($cashToStore > 0) {
                        PaymentStore::create([
                            'user_id' => $customer_id,
                            'order_id' => $order->id,
                            'payment_amount' => $cashToStore,
                            'payment_date' => now(),
                            'payment_method' => 'cash',
                            'payment_type' => $paymentTypeForStore,
                            'cash_amount' => $cashToStore,
                            'upi_amount' => 0,
                            'remaining_amount' => $remain,
                            'bank_id' => $bankId,
                            'status' => 'credit',
                            'remarks' => $request->payment_remarks ?? $request->remarks,
                            'created_at' => now(),
                        ]);
                    }

                    if ($onlineToStore > 0) {
                        PaymentStore::create([
                            'user_id' => $customer_id,
                            'order_id' => $order->id,
                            'payment_amount' => $onlineToStore,
                            'payment_date' => now(),
                            'payment_method' => 'online',
                            'payment_type' => $paymentTypeForStore,
                            'cash_amount' => 0,
                            'upi_amount' => $onlineToStore,
                            'remaining_amount' => $remain,
                            'bank_id' => $bankId,
                            'status' => 'credit',
                            'remarks' => $request->payment_remarks ?? $request->remarks,
                            'created_at' => now(),
                        ]);
                    }
                } else {
                    $paymentStoreData = [
                        'user_id' => $customer_id,
                        'order_id' => $order->id,
                        'payment_amount' => $paidAmount,
                        'payment_date' => now(),
                        'payment_method' => $paymentMethod,
                        'payment_type' => $paymentTypeForStore,
                        'cash_amount' => $paymentMethod === 'cash' ? $paidAmount : 0,
                        'upi_amount' => $paymentMethod === 'online' ? $paidAmount : 0,
                        'remaining_amount' => $remain,
                        'bank_id' => $bankId,
                        'status' => 'credit',
                        'remarks' => $request->payment_remarks ?? $request->remarks,
                        'created_at' => now(),
                    ];

                    // Include EMI fields when payment method is EMI
                    if ($paymentMethod === 'emi') {
                        $paymentStoreData['emi_down_payment']   = $emiDownPayment;
                        $paymentStoreData['emi_loan_amount']    = $emiLoanAmount;
                        $paymentStoreData['emi_interest_rate']  = $emiInterestRate;
                        $paymentStoreData['emi_tenure']         = $emiTenureRaw;
                        $paymentStoreData['emi_monthly_amount'] = round($emiMonthlyAmount, 2);
                        $paymentStoreData['emi_aadhar_number']  = $request->emi_aadhar_number ?? null;
                        $paymentStoreData['emi_do_id']          = $request->emi_do_id ?? null;
                        $paymentStoreData['emi_pan_number']     = $request->emi_pan_number ?? null;
                        $paymentStoreData['emi_guarantor_name'] = $request->emi_guarantor_name ?? null;
                    }

                    PaymentStore::create($paymentStoreData);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | CUSTOMER DEPOSIT LEDGER (exchange surplus credit / bill apply debit)
            |--------------------------------------------------------------------------
            */
            if (!$isQuotation && $exchangeSurplus > 0 && $exchangeSurplusAction === 'deposit' && $customer_id) {
                CustomerDeposit::credit(
                    (int) $customer_id,
                    (float) $exchangeSurplus,
                    (int) $branchIdToUse,
                    (int) $order->id,
                    'exchange_surplus',
                    'Exchange surplus deposited from order #' . $order->order_number,
                    (int) $userData->id
                );
            }

            if (!$isQuotation && $customerDepositApplied > 0 && $customer_id) {
                CustomerDeposit::debit(
                    (int) $customer_id,
                    (float) $customerDepositApplied,
                    (int) $branchIdToUse,
                    (int) $order->id,
                    'bill_payment',
                    'Deposit applied on order #' . $order->order_number,
                    (int) $userData->id
                );

                // Record deposit usage in payment history for traceability.
                PaymentStore::create([
                    'user_id' => $customer_id,
                    'order_id' => $order->id,
                    'payment_amount' => $customerDepositApplied,
                    'payment_date' => now(),
                    'payment_method' => 'customer_deposit',
                    'payment_type' => $remain > 0 ? 'partially' : 'fully',
                    'cash_amount' => 0,
                    'upi_amount' => 0,
                    'remaining_amount' => $remain,
                    'bank_id' => null,
                    'status' => 'credit',
                    'remarks' => 'Customer deposit applied',
                    'created_at' => now(),
                ]);
            }


        // ==============================================
        // 🔔 CREATE NOTIFICATIONS
        // ==============================================

        // Get customer details
        $customer = User::find($customer_id);

        // 1. Notification for the admin/staff who created the order
        if ($isQuotation) {
            $creatorNotificationTitle   = 'Quotation Created Successfully';
            $creatorNotificationMessage = "Quotation #{$order->order_number} has been created successfully for customer: " . ($customer->name ?? 'N/A');
            $creatorNotificationType    = 'quotation_created';
        } elseif ($isAdvanceReceipt) {
            $creatorNotificationTitle   = 'Advance Receipt Created Successfully';
            $creatorNotificationMessage = "Advance Receipt #{$order->order_number} has been created successfully for customer: " . ($customer->name ?? 'N/A') . ". Total: {$roundedTotal}";
            $creatorNotificationType    = 'advance_receipt_created';
        } else {
            $creatorNotificationTitle   = 'Order Placed Successfully';
            $creatorNotificationMessage = "Order #{$order->order_number} has been placed successfully for customer: " . ($customer->name ?? 'N/A') . ". Total: {$roundedTotal}";
            $creatorNotificationType    = 'order_created';
        }

        NotificationService::notify(
            $userData->id,
            (int) $branchIdToUse,
            $creatorNotificationType,
            $creatorNotificationTitle,
            $creatorNotificationMessage,
            '/sales-details/' . $order->id
        );

        if (! empty($order->staff_id) && (int) $order->staff_id !== (int) $userData->id) {
            NotificationService::notifyOrderAssigned(
                (int) $order->staff_id,
                (int) $branchIdToUse,
                $order,
                $customer
            );
        }

        if (! $isQuotation) {
            NotificationService::notifyDeliveryOrderWarehouse(
                (int) $branchIdToUse,
                $order,
                $customer,
                [(int) $userData->id, (int) ($order->staff_id ?? 0)]
            );
        }

            DB::commit();
               $subscriptionInvoiceMeta = null;
            if (!$isQuotation) {
                $subscriptionInvoiceMeta = $this->generateAndSendSubscriptionInvoice(
                    (int) $order->id,
                    (int) $customer_id,
                    (int) $branchIdToUse
                );
            }
            $salesInvoiceMeta = null;
            // Load orderItems.product for plan name resolution in WhatsApp template params
            if (! $isQuotation && ! $order->relationLoaded('orderItems')) {
                $order->load('orderItems.product');
            }
               try {
                $orderTemplateWhatsAppSent = false;
                // ✅ OPTIMIZED: Only load customer if needed
                $customer = User::select('id', 'name', 'phone', 'email')->find($customer_id);
                if ($customer && !empty($customer->phone)) {
                    $waSetting = Setting::select('customer_whatsapp_message')
                        ->where('branch_id', $branchIdToUse)
                        ->first();
                    $isCustomerWhatsAppEnabled = (int) ($waSetting->customer_whatsapp_message ?? 0) === 1;

                    if (! $isCustomerWhatsAppEnabled) {
                        Log::info('Customer WhatsApp disabled in settings; skipping order WhatsApp send', [
                            'order_id' => $order->id,
                            'branch_id' => $branchIdToUse,
                        ]);
                    }

                    $phoneNumber = preg_replace('/[^0-9]/', '', $customer->phone);

                    if (!empty($phoneNumber) && $isCustomerWhatsAppEnabled) {

                        // Always generate the sales invoice PDF (used as HEADER DOCUMENT)
                        if ($salesInvoiceMeta === null) {
                            $salesInvoiceMeta = $this->generateSalesInvoicePdfForWhatsApp($order, (int) $branchIdToUse);
                        }

                        // Use subscription PDF when available, otherwise fall back to sales invoice PDF
                        $invoiceMeta     = (is_array($subscriptionInvoiceMeta) && ! empty($subscriptionInvoiceMeta['file_url'] ?? null))
                            ? $subscriptionInvoiceMeta
                            : $salesInvoiceMeta;
                        $invoiceFileUrl  = (string) data_get($invoiceMeta, 'file_url', '');
                        $invoiceFileName = (string) data_get($invoiceMeta, 'file_name', 'sales_invoice.pdf');

                        // Prefer subscription_invoice_pdf (document + invoice body), then legacy order templates
                        $template = $this->findWhatsAppTemplateByName($branchIdToUse, 'subscription_invoice_pdf');

                        if (! $template) {
                            $template = $this->findWhatsAppOrderTemplate(
                                $branchIdToUse,
                                [
                                    'Order invoice PDF',
                                    'Complete order',
                                    'Subscription complete',
                                    'Pending order',
                                    'Subscription pending',
                                ]
                            );
                        }

                        $templateName     = null;
                        $templateLanguage = 'en';

                        if (! $template) {
                            Log::warning('WhatsApp: no active order template found for branch', [
                                'branch_id'      => $branchIdToUse,
                                'order_id'       => $order->id,
                                'payment_status' => $payment_status,
                            ]);
                        } elseif ($template->status !== 'APPROVED') {
                            Log::error('WhatsApp template not approved — skipping send', [
                                'branch_id'       => $branchIdToUse,
                                'template_name'   => $template->name,
                                'template_status' => $template->status,
                            ]);
                        } elseif (empty($template->name)) {
                            Log::warning('WhatsApp template name is empty', ['branch_id' => $branchIdToUse]);
                        } else {
                            $templateName     = trim($template->name);
                            $templateLanguage = ! empty($template->language) ? $template->language : 'en';
                        }

                        // ── Build body parameters ──
                        $templateParams          = [];
                        $templateExtraComponents = [];

                        if ($templateName) {
                            // Resolve common values
                            $setting = cache()->remember("setting_branch_{$branchIdToUse}", 300, function () use ($branchIdToUse) {
                                return Setting::select('name')->where('branch_id', $branchIdToUse)->first();
                            });

                            $customerName  = $customer->name ?? 'Customer';
                            $orderNumber   = $order->order_number ?? '';
                            $totalAmount   = number_format((float) ($order->total_amount ?? 0), 2);
                            $companyName   = $setting->name ?? 'Company';

                            // {{2}}: plan product name for subscriptions, order number otherwise
                            $orderProductName = $orderNumber;
                            if (is_array($subscriptionInvoiceMeta) && ! empty($subscriptionInvoiceMeta['status'] ?? null)) {
                                $planItem = $order->orderItems->first(fn ($i) => (bool) optional($i->product)->is_plan);
                                if ($planItem && $planItem->product) {
                                    $orderProductName = $planItem->product->name;
                                }
                            }

                            $statusForTemplate   = match ($payment_status) {
                                'completed' => 'Paid',
                                'partially' => 'Partially Paid',
                                default     => 'Pending',
                            };
                            $publicInvoicePdfUrl = url('/sales/invoice/pdf/' . $order->id);

                            // Header document component (PDF attachment)
                            if (! empty($invoiceFileUrl)) {
                                $templateExtraComponents[] = [
                                    'type'       => 'header',
                                    'parameters' => [[
                                        'type'     => 'document',
                                        'document' => [
                                            'link'     => $invoiceFileUrl,
                                            'filename' => $invoiceFileName,
                                        ],
                                    ]],
                                ];
                            } else {
                                Log::warning('WhatsApp invoice PDF URL empty — sending without document header', [
                                    'order_id' => $order->id, 'branch_id' => $branchIdToUse,
                                ]);
                            }

                            // Parse body variables from template components
                            $components = is_string($template->components)
                                ? json_decode($template->components, true)
                                : $template->components;

                            if (is_array($components)) {
                                foreach ($components as $component) {
                                    if (strtoupper((string) ($component['type'] ?? '')) === 'BODY' && isset($component['text'])) {
                                        preg_match_all('/\{\{(\d+)\}\}/', $component['text'], $m);
                                        $bodyVarNums = array_values(array_unique(array_map('intval', $m[1] ?? [])));
                                        sort($bodyVarNums, SORT_NUMERIC);
                                        foreach ($bodyVarNums as $varNum) {
                                            $templateParams[] = match ((int) $varNum) {
                                                1 => $customerName,        // Hello {{1}},
                                                2 => $orderProductName,    // Your subscription order *{{2}}*
                                                3 => $companyName,         // received at *{{3}}*
                                                4 => $orderNumber,         // Order no.: {{4}}
                                                5 => $totalAmount,         // Amount: ₹{{5}}
                                                6 => $statusForTemplate,   // Status: {{6}}
                                                7 => $publicInvoicePdfUrl, // Download: {{7}}
                                                default => '',
                                            };
                                        }
                                        break;
                                    }
                                }
                            }

                            if (empty($templateParams)) {
                                $templateParams = [
                                    $customerName,
                                    $orderProductName,
                                    $companyName,
                                    $orderNumber,
                                    $totalAmount,
                                    $statusForTemplate,
                                    $publicInvoicePdfUrl,
                                ];
                            }

                            Log::info('WhatsApp order params ready', [
                                'branch_id'      => $branchIdToUse,
                                'order_id'       => $order->id,
                                'template_name'  => $templateName,
                                'payment_status' => $payment_status,
                                'params'         => $templateParams,
                                'header_doc_url' => $invoiceFileUrl,
                            ]);

                            // ── Send ──
                            try {
                                $result = WhatsAppService::sendTemplateMessage(
                                    $branchIdToUse,
                                    $phoneNumber,
                                    $templateName,
                                    $templateParams,
                                    $templateLanguage,
                                    $templateExtraComponents
                                );

                                if ($result['success']) {
                                    $orderTemplateWhatsAppSent = true;
                                    Log::info('WhatsApp order message sent successfully', [
                                        'branch_id'      => $branchIdToUse,
                                        'phone'          => $phoneNumber,
                                        'template_name'  => $templateName,
                                        'message_id'     => $result['message_id'] ?? null,
                                        'message_status' => $result['message_status'] ?? 'unknown',
                                    ]);
                                } else {
                                    Log::error('WhatsApp order message failed', [
                                        'branch_id'     => $branchIdToUse,
                                        'phone'         => $phoneNumber,
                                        'template_name' => $templateName,
                                        'error'         => $result['message'] ?? 'Unknown error',
                                        'error_code'    => $result['error_code'] ?? null,
                                    ]);
                                }
                            } catch (\Exception $e) {
                                Log::error('WhatsApp sending exception (non-blocking)', [
                                    'branch_id'     => $branchIdToUse,
                                    'phone'         => $phoneNumber,
                                    'template_name' => $templateName,
                                    'error'         => $e->getMessage(),
                                ]);
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error("WhatsApp message exception for order", [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
            }

            $mailSent = $this->sendOrderOrQuotationMail(
                $customer,
                $order,
                $isQuotation,
                $branchIdToUse
            );

            $responseData = [
                'status'   => true,
                'message'  => $isQuotation
                    ? 'Quotation saved successfully!'
                    : ($isAdvanceReceipt
                        ? 'Advance Receipt created successfully!'
                        : ($isRental
                            ? 'Rental created successfully!'
                            : 'Order placed successfully!')),
                'order_id' => $order->id,
                'mail_sent' => $mailSent,
            ];

            // Include invoice PDF metadata when available
            if (! $isQuotation) {
                if (is_array($subscriptionInvoiceMeta) && ! empty($subscriptionInvoiceMeta['file_url'] ?? null)) {
                    $responseData['subscription_invoice'] = [
                        'status'        => $subscriptionInvoiceMeta['status'] ?? true,
                        'file_url'      => $subscriptionInvoiceMeta['file_url'],
                        'file_name'     => $subscriptionInvoiceMeta['file_name'] ?? null,
                        'relative_path' => $subscriptionInvoiceMeta['relative_path'] ?? null,
                        'local_path'    => $subscriptionInvoiceMeta['local_path'] ?? null,
                        'whatsapp'      => $subscriptionInvoiceMeta['whatsapp'] ?? null,
                    ];
                }

                if (is_array($salesInvoiceMeta) && ! empty($salesInvoiceMeta['file_url'] ?? null)) {
                    $responseData['sales_invoice'] = [
                        'status'        => $salesInvoiceMeta['status'] ?? true,
                        'file_url'      => $salesInvoiceMeta['file_url'],
                        'file_name'     => $salesInvoiceMeta['file_name'] ?? null,
                        'relative_path' => $salesInvoiceMeta['relative_path'] ?? null,
                    ];
                }
            }

            return response()->json($responseData, 201);
        } catch (\Exception $e) {

            DB::rollBack();
            Log::error('Order placement error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function sendOrderOrQuotationMail(?User $customer, Order $order, bool $isQuotation, $branchId): bool
    {
        if (!$customer || empty($customer->email)) {
            Log::info(
                ($isQuotation ? 'Quotation' : 'Order')
                    . " email skipped for order #{$order->order_number}: customer email not found."
            );
            return false;
        }

        $setting = Setting::where('branch_id', $branchId)->first();
        $sendMailEnabled = is_null($setting?->send_mail) ? true : (bool) $setting->send_mail;

        if (!$sendMailEnabled) {
            Log::info(
                ($isQuotation ? 'Quotation' : 'Order')
                    . " email skipped for order #{$order->order_number}: send_mail is off for branch {$branchId}."
            );
            return false;
        }

        try {
            MailConfigService::setSMTP($branchId);
        } catch (\Throwable $e) {
            // Keep fallback behavior: if branch SMTP fails, default mail config is still usable.
            Log::warning(
                "SMTP setup failed for order #{$order->order_number} (branch: {$branchId}): "
                    . $e->getMessage()
            );
        }

        try {
            Mail::to($customer->email)->send(new SalesOrderCreatedMail($order, $customer, $isQuotation));
            Log::info(
                ($isQuotation ? 'Quotation' : 'Order')
                    . " email sent for order #{$order->order_number} to {$customer->email}."
            );
            return true;
        } catch (\Throwable $e) {
            Log::error(
                "Failed to send " . ($isQuotation ? 'quotation' : 'order')
                    . " email for order #{$order->order_number}: "
                    . $e->getMessage()
            );
            return false;
        }
    }







    public function get_orders(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated access',
            ], 401);
        }

        $userId = $user->id;
        $role = $user->role;

        // ✅ Prefer request input, then session branch
        $selectedSubAdminID = $this->resolveSelectedSubAdminIdFromRequest($request);

        // ✅ Build the query
        $query = Order::select('orders.*') // 👈 ensure created_by is included
            ->with([
                'user:id,name,phone',
                'orderItems:id,order_id',
                'creator:id,name,role',
                'staff:id,name',
            ])
            ->where('isDeleted', 0)

            // ✅ COUNT payments (for buttons / permissions)
            ->withCount([
                'payments as has_payment' => function ($q) {
                    $q->where('isDeleted', 0);
                },
            ])

            // ✅ SUM payments (for remaining / extra paid)
            ->withSum([
                'payments as total_paid' => function ($q) {
                    $q->where('isDeleted', 0);
                },
            ], 'payment_amount')
            ->withSum('returns as total_return', 'total_amount');

        StaffDepartmentScope::applyOrderScope($query, $user, $selectedSubAdminID);

        // ✅ Apply date filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        } else {
            if ($request->filled('year')) {
                $query->whereYear('created_at', $request->year);
            }

            if ($request->filled('month')) {
                $query->whereMonth('created_at', $request->month);
            }
        }

        $this->applyFinancialYearFilter($query, $request->input('financial_year'), 'created_at');

        if ($request->filled('customerId')) {
            $query->where('user_id', $request->customerId);
        }

        // ✅ Apply staff filter (admin/sub-admin can filter by assigned staff)
        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        $orderType = $request->input('order_type');
        if (in_array($orderType, ['self_pickup', 'delivery'], true)) {
            $query->where('order_type', $orderType);
        }

        $statusFilter = $request->input('status');
        if (in_array($statusFilter, ['sales', 'quotation', 'rental', 'advance_receipt'], true)) {
            $query->where('quotation_status', $statusFilter);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('payment_status', 'like', "%{$search}%")
                    ->orWhere('payment_method', 'like', "%{$search}%")
                    ->orWhere('quotation_status', 'like', "%{$search}%")
                    ->orWhere('total_amount', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('creator', function ($creatorQuery) use ($search) {
                        $creatorQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('staff', function ($staffQuery) use ($search) {
                        $staffQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // ✅ Apply sorting
        $sortBy = $request->input('sort_by', 'date_desc');

        if ($sortBy === 'order_no_asc') {
            $query->orderByRaw("CASE WHEN UPPER(order_number) REGEXP '^SI/HO/[0-9]+$' THEN 0 ELSE 1 END ASC")
                ->orderByRaw("CAST(SUBSTRING_INDEX(order_number, '/', -1) AS UNSIGNED) ASC")
                ->orderBy('created_at', 'asc')
                ->orderBy('id', 'asc');
        } elseif ($sortBy === 'order_no_desc') {
            $query->orderByRaw("CASE WHEN UPPER(order_number) REGEXP '^SI/HO/[0-9]+$' THEN 0 ELSE 1 END ASC")
                ->orderByRaw("CAST(SUBSTRING_INDEX(order_number, '/', -1) AS UNSIGNED) DESC")
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc');
        } elseif ($sortBy === 'date_asc') {
            $query->orderBy('created_at', 'asc')
                ->orderBy('id', 'asc');
        } else {
            $query->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc');
        }

        $summaryOrders = (clone $query)->get();
        $totalAmount = 0;
        $totalPendingAmount = 0;
        $totalPaidAmount = 0;

        foreach ($summaryOrders as $summaryOrder) {
            $orderTotal = (float) ($summaryOrder->total_amount ?? 0);
            $totalPaid = (float) ($summaryOrder->total_paid ?? 0);
            $totalReturn = (float) ($summaryOrder->total_return ?? 0);
            $actualTotal = max(0, $orderTotal - $totalReturn);
            $remaining = max(0, $actualTotal - $totalPaid);
            $effectivePaid = max(0, min($orderTotal, $orderTotal - $remaining));

            $totalAmount += $orderTotal;
            $totalPendingAmount += $remaining;
            $totalPaidAmount += $effectivePaid;
        }

        $perPage = (int) $request->input('per_page', 10);
        $perPage = $perPage > 0 ? min($perPage, 100) : 10;
        $get_orders = $query->paginate($perPage);
        $ordersCollection = collect($get_orders->items());

        $orderIds = $ordersCollection->pluck('id')->filter()->values();
        $emiPaidCounts = $orderIds->isEmpty()
            ? collect()
            : PaymentStore::whereIn('order_id', $orderIds)
                ->where(function ($q) {
                    $q->whereNull('isDeleted')->orWhere('isDeleted', 0);
                })
                ->whereNotNull('emi_month')
                ->where('emi_month', '>', 0)
                ->selectRaw('order_id, COUNT(DISTINCT emi_month) as paid_count')
                ->groupBy('order_id')
                ->pluck('paid_count', 'order_id');

        $ordersCollection->transform(function ($order) use ($emiPaidCounts) {

            $orderTotal = (float) ($order->total_amount ?? 0);
            $totalPaid = (float) ($order->total_paid ?? 0);
            $totalReturn = (float) ($order->total_return ?? 0);

            // ✅ Actual total after returns
            $actualTotal = $orderTotal - $totalReturn;

            // ✅ Remaining
            $remaining = max(0, $actualTotal - $totalPaid);

            // ✅ Extra Paid (Overpayment after returns)
            $extraPaid = max(0, $totalPaid - $actualTotal);

            $order->remaining_amount = $remaining;
            $order->extra_paid = $extraPaid;

            $tenure = (int) preg_replace('/[^0-9]/', '', (string) ($order->emi_tenure ?? ''));
            $paidEmiMonths = (int) ($emiPaidCounts[$order->id] ?? 0);
            $order->remaining_emi_months = $tenure > 0 ? max(0, $tenure - $paidEmiMonths) : 0;

            return $order;
        });

        // ✅ Add created_date (formatted date from orders.created_at)
        $ordersCollection->transform(function ($order) {
            $order->created_date = $order->created_at ? $order->created_at->format('Y-m-d') : null;
            return $order;
        });
        $settingsBranchId = $this->resolveSalesSettingsBranchId($user, $selectedSubAdminID);

        $settings = DB::table('settings')->where('branch_id', $settingsBranchId)->first();
        $currencySymbol = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';
        $financialYearEnabled = (bool) ($settings->financial_year ?? true);

        $ordersCollection->transform(function ($order) {
            // Convert UTC timestamp to IST and format
            $order->created_date = $order->created_at
                ? $order->created_at->format('d-M-Y h:i A')
                : null;
            // ✅ Biller logic
            if ($order->created_by && $order->creator) {
                $order->biller = $order->creator->name;
            } else {
                $order->biller = 'Admin';
            }
            $order->invoice_pdf_url = url("/sales/invoice/pdf/" . $order->id);

            // Add biller name from creator relationship
            $order->biller = $order->creator->name ?? 'Admin';

            // ✅ Staff name
            $order->staff_name = $order->staff->name ?? null;

            return $order;
        });

        return response()->json([
            'status' => true,
            'message' => 'Orders fetched successfully',
            'currency_symbol' => $currencySymbol,
            'currency_position' => $currencyPosition,
            'financial_year_enabled' => $financialYearEnabled,
            'total_amount' => $totalAmount,
            'total_pending_amount' => round($totalPendingAmount, 2),
            'total_paid_amount' => round($totalPaidAmount, 2),
            'data' => $ordersCollection,
            'pagination' => [
                'current_page' => $get_orders->currentPage(),
                'last_page' => $get_orders->lastPage(),
                'per_page' => $get_orders->perPage(),
                'total' => $get_orders->total(),
            ],
        ]);
    }

    public function get_salse_detail(Request $request) {}

    public function getsalseById($id, Request $request)
    {

        $authUser = Auth::guard('api')->user();
        // $subAdminId = session('selectedSubAdminId') ?? $authUser->id;
        $selectedSubAdminID = $request->input('selectedSubAdminId');

        if ($authUser->role === 'staff' && $authUser->branch_id) {
            $branchIdToUse = $authUser->branch_id;
        } elseif ($authUser->role === 'admin' && !empty($selectedSubAdminID)) {
            $branchIdToUse = $selectedSubAdminID;
        } else {
            $branchIdToUse = $authUser->id;
        }
        // $setting    = Setting::where('branch_id', $subAdminId)->first();

        // ✅ OPTIMIZED: Eager load all relationships in single query
        $sales = Order::with(['orderItems.product.unit', 'orderItems.variant', 'user:id,name,phone,email,gst_number,pan_number'])->find($id);

        if (!$sales) {
            return response()->json(['status' => false, 'error' => 'Sale not found'], 404);
        }
        $paidAmount = PaymentStore::where('order_id', $sales->id)
            ->where(function ($q) {
                $q->whereNull('isDeleted')->orWhere('isDeleted', 0);
            })
            ->sum('payment_amount');

        // ✅ Calculate total return amount
        $totalReturn = SalesReturn::where('order_id', $sales->id)->sum('total_amount');
        $actualTotal = max(0, $sales->total_amount - $totalReturn);

        // ✅ Extra Paid calculation (using actual total after returns)
        $extraPaid = max(0, $paidAmount - $actualTotal);

        // ✅ Pending amount calculation (dynamic to fix database inconsistencies)
        $pendingAmount = max(0, $actualTotal - $paidAmount);

        // ✅ OPTIMIZED: Cache company info
        $companyInfo = Setting::where('branch_id', $branchIdToUse)->first()
            ?? Setting::first()
            ?? new Setting([
                'name' => 'Fablead Developer & Technolab',
                'email' => 'info@gmail.com',
                'phone' => '1234567890',
                'address' => 'Adajan Surat',
                'logo' => 'admin/assets/img/logo-image.jpg',
                'currency_symbol' => '₹',
                'currency_position' => 'left',
            ]);
        // dd($companyInfo);
        $taxIdsRaw = $sales->tax_id;
        $taxIds = is_array($taxIdsRaw) ? $taxIdsRaw : (json_decode($taxIdsRaw, true) ?? []);

        // ✅ OPTIMIZED: Only query taxes if tax IDs exist
        $taxDetails = !empty($taxIds)
            ? TaxRate::whereIn('id', $taxIds)
            ->where('isDeleted', 0)
            ->where('status', 'active')
            ->get(['id', 'tax_name', 'tax_rate'])
            : collect();

        $formattedCreatedAt = $sales->created_at?->timezone('Asia/Kolkata')->format('d/m/Y h:i A');

        $labourItems = Sales_Labour_Items::where('order_id', $id)
            ->with('labourItem')
            ->get();


        $labourCost = 0;
        if ($labourItems && $labourItems->isNotEmpty()) {
            foreach ($labourItems as $labourItem) {
                $labourCost += ($labourItem->qty ?? 0) * ($labourItem->price ?? 0);
            }
        }


        // ✅ OPTIMIZED: Use already loaded relationship instead of new query
        $orderItems = $sales->orderItems->map(function ($item) {
            return [
                'id' => $item->id,
                'product' => $item->product,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'discount_percentage' => $item->discount_percentage,
                'discount_amount' => $item->discount_amount,
                'product_gst_total' => $item->product_gst_total,
                'total_amount' => $item->total_amount,
                'imei_no' => $item->imei_no,
                'product_tax' => $this->normalizeGstDetails($item->product_gst_details),
                'date' => $item->created_at?->timezone('Asia/Kolkata')->format('Y-m-d h:i A'),
                'rent_value' => $item->rent_value,
                'rent_price' => $item->rent_price,
                'rent_period' => $item->rent_period,
                'rent_start_at' => $item->rent_start_at,
                'rent_end_at' => $item->rent_end_at,
                'variant' => $item->variant,
            ];
        });

        // ✅ Fetch all returns for this order
        $returns = \App\Models\SalesReturn::with(['items.product'])
            ->where('order_id', $sales->id)
            ->get();

        // ✅ Fetch all rental returns for this order
        $rentalReturns = \App\Models\RentalReturn::with(['items.product', 'items.variant'])
            ->where('order_id', $sales->id)
            ->get();

        return response()->json([
            'status' => true,
            'sales' => [
                ...$sales->toArray(),
                'created_at' => $formattedCreatedAt,
                'user_name' => $sales->user->name ?? 'Default-customer',
                'user_phone' => $sales->user->phone ?? null,
                'user_gst_number' => $sales->user->gst_number ?? null,
                'user_pan_number' => $sales->user->pan_number ?? null,
                'taxes' => $taxDetails,
                'paid_amount' => $paidAmount,
                'pending_amount' => $pendingAmount,
                'extra_paid' => $extraPaid,
            ],
            'labour_items' => $labourItems && $labourItems->isNotEmpty() ? $labourItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'order_id' => $item->order_id,
                    'user_id' => $item->user_id,
                    'labour_item_id' => $item->labour_item_id,
                    'labour_item_name' => $item->labourItem->item_name,
                    'qty' => $item->qty ?? 0,
                    'price' => $item->price ?? 0,
                    'labourItem' => $item->labourItem ? [
                        'id' => $item->labourItem->id,
                        'item_name' => $item->labourItem->item_name ?? 'Labour Item',
                        'price' => $item->labourItem->price ?? 0,
                    ] : null,
                    'created_at' => $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : null,
                    'updated_at' => $item->updated_at ? $item->updated_at->format('Y-m-d H:i:s') : null,
                ];
            })->toArray() : [],
            'order_items' => $orderItems,
            'returns' => $returns,
            'rental_returns' => $rentalReturns,
            'company_info' => $companyInfo,
            'currency_symbol' => $companyInfo->currency_symbol ?? '₹',
            'currency_position' => $companyInfo->currency_position ?? 'left',
        ], 200);
    }

    public function delete($id)
    {
        // Find the order
        $order = Order::find($id);

        // If order not found, return error
        if (!$order) {
            return response()->json([
                'status' => false,
                'error' => 'Order not found',
            ], 404);
        }

        // ❌ Prevent deletion if payment is pending
        // if ($order->payment_status === 'pending') {
        //     return response()->json([
        //         'status' => false,
        //         'error' => 'This order cannot be deleted because its payment status is pending.',
        //     ], 400);
        // }
        // If it's a generated sales order, recycle the order number
        if ($order->order_number && str_starts_with($order->order_number, self::SALES_ORDER_NUMBER_PREFIX)) {
            DB::table('recycled_order_numbers')->insert([
                'branch_id' => $order->branch_id,
                'order_number' => $order->order_number,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Mutate order number so it frees up the unique slot
            $order->order_number = $order->order_number . '-DEL-' . $order->id;
        }

        // Soft delete: set isDeleted = 1
        $order->isDeleted = 1;
        $order->save();

        // Soft delete related order items
        OrderItem::where('order_id', $order->id)->update(['isDeleted' => 1]);

        return response()->json([
            'status' => true,
            'message' => 'Order deleted successfully.',
        ]);
    }


    // public function update_sale(Request $request)
    // {

    // dd($request->all());
    //     $validator = Validator::make($request->all(), [
    //         'update_id' => 'required|exists:orders,id',
    //         'customer_id' => 'nullable',
    //         'order_number' => 'nullable|string',
    //         'customer_phone' => 'nullable|string',
    //         'order_date'     => 'nullable|date',
    //         'product_ids'    => 'required|array',
    //         'product_ids.*'  => 'exists:products,id',
    //         'quantities'     => 'required|array',
    //         'quantities.*'   => 'numeric|min:0',
    //         'prices'         => 'nullable|array',
    //         'prices.*'       => 'numeric|min:0',
    //         'discount'       => 'numeric|min:0|max:100',
    //         'grand_total'    => 'required|numeric|min:0',
    //         'shipping'       => 'nullable|numeric|min:0',
    //         'labour_item_ids' => 'nullable|array',
    //         'labour_qtys'     => 'nullable|array',
    //         'labour_prices'   => 'nullable|array',
    //         'tds_percentage'  => 'nullable|numeric|min:0|max:100',
    //         'tds_amount'      => 'nullable|numeric|min:0',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Validation errors',
    //             'errors' => $validator->errors(),
    //         ], 422);
    //     }

    //     try {
    //         DB::beginTransaction();

    //         $order = Order::findOrFail($request->update_id);
    //         $products = Product::whereIn('id', $request->product_ids)->get();
    //         $updatedQuotationStatus = $request->quotation_status ?? $order->quotation_status;
    //         $isConvertingQuotationToSale = ($order->quotation_status ?? '') === 'quotation'
    //             && $updatedQuotationStatus === 'sales';
    //         $orderUpdatedAt = now('Asia/Kolkata');
    //         $orderCreatedAt = $request->filled('order_date')
    //             ? Carbon::createFromFormat('Y-m-d', $request->order_date, 'Asia/Kolkata')
    //                 ->setTime(
    //                     optional($order->created_at)->timezone('Asia/Kolkata')->hour ?? $orderUpdatedAt->hour,
    //                     optional($order->created_at)->timezone('Asia/Kolkata')->minute ?? $orderUpdatedAt->minute,
    //                     optional($order->created_at)->timezone('Asia/Kolkata')->second ?? $orderUpdatedAt->second
    //                 )
    //             : $order->created_at;

    //         // Fetch old order items
    //         $oldItems = OrderItem::where('order_id', $order->id)->get();
    //         $oldQuantities = $oldItems->pluck('quantity', 'product_id')->toArray();

    //         // Calculate subtotal & product-wise GST & adjust stock
    //         $subtotal = 0;
    //         $productWiseGstTotal = 0;
    //         $hasProductSpecificGst = false;

    //         foreach ($request->product_ids as $productId) {
    //             $product = $products->firstWhere('id', $productId);
    //             if ($product) {
    //                 $quantity = $request->quantities[$productId] ?? 0;
    //                 $oldQty = $oldQuantities[$productId] ?? 0;

    //                 $difference = $quantity - $oldQty;

    //                 // Stock adjustment (ONLY if NOT quotation)
    //                 if ($isConvertingQuotationToSale) {
    //                     if ($quantity > 0 && $product->quantity < $quantity) {
    //                         DB::rollBack();
    //                         return response()->json([
    //                             'success' => false,
    //                             'message' => "Stock Quantity Exceeded. Only {$product->quantity} quantity are available for '{$product->name}'.",
    //                         ], 422);
    //                     }

    //                     if ($quantity > 0) {
    //                         $product->decrement('quantity', $quantity);
    //                     }
    //                 } elseif ($order->quotation_status !== 'quotation') {
    //                     // Check if stock is available for increment
    //                     if ($difference > 0 && $product->quantity < $difference) {
    //                         DB::rollBack();
    //                         return response()->json([
    //                             'success' => false,
    //                             'message' => "Stock Quantity Exceeded. Only {$product->quantity} quantity are available for '{$product->name}'.",
    //                         ], 422);
    //                     }

    //                     if ($difference > 0) {
    //                         $product->decrement('quantity', $difference);
    //                     } elseif ($difference < 0) {
    //                         $product->increment('quantity', abs($difference));
    //                     }
    //                 }

    //                 // Calculate product total
    //                 $quantity = $request->quantities[$productId] ?? 0;
    //                 $discountPercent = $request->discounts[$productId] ?? 0;

    //                 $price = floatval($request->prices[$productId] ?? $product->price);
    //                 $baseProductTotal = $price * $quantity;

    //                 // Calculate product-wise GST if global option is with_gst
    //                 $itemGstTotal = 0;
    //                 if ($request->gst_option === 'with_gst' && $product->gst_option === 'with_gst' && $product->product_gst) {
    //                     $hasProductSpecificGst = true;
    //                     try {
    //                         $gstData = json_decode($product->product_gst, true);
    //                         if (is_array($gstData)) {
    //                             foreach ($gstData as $tax) {
    //                                 $taxRate = floatval($tax['tax_rate'] ?? 0) / 100;
    //                                 $itemGstTotal += $baseProductTotal * $taxRate;
    //                             }
    //                         }
    //                     } catch (\Exception $e) {
    //                         // Log error but continue
    //                         Log::error('Error calculating product GST: ' . $e->getMessage());
    //                     }
    //                 }

    //                 $itemDiscountAmount = ($baseProductTotal + $itemGstTotal) * ($discountPercent / 100);
    //                 $productTotal = ($baseProductTotal + $itemGstTotal) - $itemDiscountAmount;

    //                 $subtotal += $baseProductTotal;
    //                 $productWiseGstTotal += $itemGstTotal;
    //             }
    //         }

    //         $userData = Auth::guard('api')->user();
    //         $userRole = $userData->role;
    //         if ($userRole == 'staff') {
    //             $branchIdToUse = $userData->branch_id;
    //         } elseif (!empty($request->selectedSubAdminId) && $userRole == 'admin') {
    //             $branchIdToUse = $request->selectedSubAdminId ?? null;
    //         } else {
    //             $branchIdToUse = $userData->id;
    //         }

    //         // Tax & discount calculations
    //         // Products total after product-level discounts
    //         $totalProductsAfterProductDiscount = ($subtotal + $productWiseGstTotal) - $request->sum_product_discounts; // Wait, I should calculate it here

    //         // Actually, let's recalculate the whole products part to be safe and match JS
    //         $totalProductsWithGstAndDiscount = 0;
    //         $totalItemDiscounts = 0;
    //         foreach ($request->product_ids as $productId) {
    //             $product = $products->firstWhere('id', $productId);
    //             if ($product) {
    //                 $quantity = $request->quantities[$productId] ?? 0;
    //                 $discountPercent = $request->discounts[$productId] ?? 0;
    //                 $price = floatval($request->prices[$productId] ?? $product->price);
    //                 $baseProductTotal = $price * $quantity;

    //                 $itemGst = 0;
    //                 if ($request->gst_option === 'with_gst' && $product->gst_option === 'with_gst' && $product->product_gst) {
    //                     $gstData = json_decode($product->product_gst, true);
    //                     if (is_array($gstData)) {
    //                         foreach ($gstData as $tax) {
    //                             $itemGst += $baseProductTotal * (floatval($tax['tax_rate'] ?? 0) / 100);
    //                         }
    //                     }
    //                 }
    //                 $rowWithGst = $baseProductTotal + $itemGst;
    //                 $rowDiscount = $rowWithGst * ($discountPercent / 100);
    //                 $totalProductsWithGstAndDiscount += ($rowWithGst - $rowDiscount);
    //                 $totalItemDiscounts += $rowDiscount;
    //             }
    //         }

    //         $discountAmount = $totalProductsWithGstAndDiscount * ($request->discount / 100);

    //         // Add labour subtotal
    //         $labourSubtotal = 0;
    //         if ($request->has('labour_item_ids')) {
    //             foreach ($request->labour_item_ids as $index => $labourItemId) {
    //                 $qty = floatval($request->labour_qtys[$index] ?? 0);
    //                 $price = floatval($request->labour_prices[$index] ?? 0);
    //                 $labourSubtotal += $qty * $price;
    //             }
    //         }
    //         $preTdsGrandTotal = ($totalProductsWithGstAndDiscount - $discountAmount) + ($request->shipping ?? 0) + $labourSubtotal;
    //         $settings = Setting::where('branch_id', $branchIdToUse)->first();
    //         $isTdsEnabled = (bool) ($settings->tds_apply ?? false);
    //         $tdsPercentage = $isTdsEnabled ? max(0, min(100, (float) ($request->tds_percentage ?? 0))) : 0;
    //         $tdsAmount = $isTdsEnabled ? round(($preTdsGrandTotal * $tdsPercentage) / 100, 2) : 0;
    //         $grandTotal = max(0, round($preTdsGrandTotal - $tdsAmount));

    //         $totalPaid  = $order->total_amount - $order->remaining_amount;

    //         if ($totalPaid >= $grandTotal) {
    //             $remainingAmount = 0;
    //         } else {
    //             $remainingAmount = $grandTotal - $totalPaid;
    //         }

    //         // Payment status
    //         if ($remainingAmount == 0 && $totalPaid > 0) {
    //             $paymentStatus = 'completed';
    //         } elseif ($totalPaid > 0) {
    //             $paymentStatus = 'partially';
    //         } else {
    //             $paymentStatus = 'pending';
    //         }

    //         $updatedOrderNumber = $order->order_number;
    //         if ($isConvertingQuotationToSale) {
    //             $financialYearNumberingEnabled = $this->isFinancialYearOrderNumberingEnabled((int) $branchIdToUse);
    //             if ($financialYearNumberingEnabled) {
    //                 $updatedOrderNumber = $this->generateFinancialYearOrderNumber(
    //                     (int) $branchIdToUse,
    //                     false,
    //                     ($orderCreatedAt instanceof Carbon) ? $orderCreatedAt->copy() : now('Asia/Kolkata')
    //                 );
    //             } else {
    //                 $updatedOrderNumber = now()->format('Ymd') . str_pad($order->id, 5, '0', STR_PAD_LEFT);
    //             }
    //         }

    //         // Update Order
    //         $order->update([
    //             'user_id' => $request->customer_id,
    //             'user_phone' => $request->customer_phone,
    //             'payment_method' => $order->payment_method,
    //             'discount' => $request->discount,
    //             'discount_amount' => $totalItemDiscounts + $discountAmount,
    //             'gst_option' => $request->gst_option === 'with_gst' ? 'with_gst' : 'without_gst',
    //             'tax_id' => json_encode([]), // Empty array since no TaxRate
    //             'tax_amount' => $productWiseGstTotal,
    //             'subtotal' => $subtotal,
    //             'total_amount' => $grandTotal,
    //             'remaining_amount' => $remainingAmount,
    //             'payment_status'   => $paymentStatus,
    //             'quotation_status' => $updatedQuotationStatus,
    //             'order_number'     => $updatedOrderNumber,
    //             'shipping'         => $request->shipping ?? 0,
    //             'tds_percentage'   => $tdsPercentage,
    //             'tds_amount'       => $tdsAmount,
    //             'remarks'          => $request->remarks ?? $order->remarks,
    //             'created_at'       => $orderCreatedAt,
    //         ]);

    //         // Remove old items
    //         OrderItem::where('order_id', $order->id)->delete();
    //         Sales_Labour_Items::where('order_id', $order->id)->delete();

    //         // Reinsert updated order items with GST details
    //         foreach ($request->product_ids as $productId) {
    //             $product = $products->firstWhere('id', $productId);
    //             if ($product) {
    //                 $quantity     = $request->quantities[$productId] ?? 0;
    //                 $discountPercentage = $request->discounts[$productId] ?? 0;
    //                 $price        = floatval($request->prices[$productId] ?? $product->price);

    //                 $baseProductTotal = $price * $quantity;

    //                 // Calculate product GST details (based on base price)
    //                 $productGstDetails = [];
    //                 $productGstTotal = 0;

    //                 if ($request->gst_option === 'with_gst' && $product->gst_option === 'with_gst' && $product->product_gst) {
    //                     try {
    //                         $gstData = json_decode($product->product_gst, true);
    //                         if (is_array($gstData)) {
    //                             foreach ($gstData as $tax) {
    //                                 $taxRate = floatval($tax['tax_rate'] ?? 0) / 100;
    //                                 $taxAmount = $baseProductTotal * $taxRate;
    //                                 $productGstTotal += $taxAmount;

    //                                 $productGstDetails[] = [
    //                                     'tax_name' => $tax['tax_name'] ?? 'GST',
    //                                     'tax_rate' => $tax['tax_rate'] ?? 0,
    //                                     'tax_amount' => $taxAmount,
    //                                 ];
    //                             }
    //                         }
    //                     } catch (\Exception $e) {
    //                         // Log error
    //                         Log::error('Error parsing product GST: ' . $e->getMessage());
    //                     }
    //                 }

    //                 // Calculate discount on (Base + GST)
    //                 $discountAmount = ($baseProductTotal + $productGstTotal) * ($discountPercentage / 100);
    //                 $productTotal = ($baseProductTotal + $productGstTotal) - $discountAmount;

    //                 OrderItem::create([
    //                     'order_id'            => $order->id,
    //                     'user_id'             => $order->user_id ?? null,
    //                     'category_id'         => $product->category_id ?? null,
    //                     'product_id'          => $productId,
    //                     'quantity'            => $quantity,
    //                     'price'               => $price,
    //                     'discount_percentage' => $discountPercentage,
    //                     'discount_amount'     => $discountAmount,
    //                     'total_amount'        => $productTotal,
    //                     'product_gst_details' => ! empty($productGstDetails) ? json_encode($productGstDetails) : null,
    //                     'product_gst_total'   => $productGstTotal,
    //                     'branch_id'           => $branchIdToUse,
    //                     'created_by'          => $userData->id,
    //                 ]);

    //                 if ($order->quotation_status !== 'quotation') {
    //                     $lastInventory = ProductInventory::where('product_id', $productId)
    //                         ->orderBy('id', 'desc')
    //                         ->first();

    //                     ProductInventory::create([
    //                         'product_id'    => $productId,
    //                         'initial_stock' => $lastInventory->initial_stock ?? $product->quantity,
    //                         'current_stock' => $product->quantity,
    //                         'branch_id'     => $order->branch_id,
    //                         'create_by'     => Auth::id(),
    //                         'type'          => 'Update Sale',
    //                         'date'          => now(),
    //                     ]);
    //                 }
    //             }
    //         }

    //         // Reinsert labour items
    //         if ($request->has('labour_item_ids')) {
    //             foreach ($request->labour_item_ids as $index => $labourItemId) {
    //                 Sales_Labour_Items::create([
    //                     'order_id' => $order->id,
    //                     'user_id' => $order->user_id,
    //                     'labour_item_id' => $labourItemId,
    //                     'qty' => floatval($request->labour_qtys[$index] ?? 0),
    //                     'price' => floatval($request->labour_prices[$index] ?? 0),
    //                 ]);
    //             }
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Order updated successfully',
    //             'data' => $order,
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to update order',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

public function update_sale(Request $request)
{
    $userData = Auth::guard('api')->user();
    $userRole = $userData->role ?? null;

    if ($userRole == 'staff') {
        $branchIdToUse = $userData->branch_id;
    } elseif (!empty($request->selectedSubAdminId) && $userRole == 'admin') {
        $branchIdToUse = $request->selectedSubAdminId ?? null;
    } else {
        $branchIdToUse = $userData->id ?? null;
    }

    $validator = Validator::make($request->all(), [
        'update_id' => 'required|exists:orders,id',
        'customer_id' => 'nullable',
        'order_number' => [
            'nullable',
            'string',
            Rule::unique('orders', 'order_number')
                ->ignore($request->update_id)
                ->where(function ($query) use ($branchIdToUse) {
                    $query->where('isDeleted', 0);

                    if (!empty($branchIdToUse)) {
                        $query->where('branch_id', $branchIdToUse);
                    }

                    return $query;
                }),
        ],
        'customer_phone' => 'nullable|string',
        'order_date'     => 'nullable|date',
        'product_ids'    => 'required|array',
        'product_ids.*'  => 'exists:products,id',
        'quantities'     => 'required|array',
        'quantities.*'   => 'numeric|min:0',
        'prices'         => 'nullable|array',
        'prices.*'       => 'numeric|min:0',
        'discount'       => 'numeric|min:0|max:100',
        'grand_total'    => 'required|numeric|min:0',
        'shipping'       => 'nullable|numeric|min:0',
        'payment_method' => 'nullable|string',
        'bank_id'        => 'nullable|required_if:payment_method,online,cash+online|integer|exists:bank_master,id',
        'paid_type'      => 'nullable|in:full,partial',
        'payment_amount' => 'nullable|numeric|min:0',
        'pending_amount' => 'nullable|numeric|min:0',
        'cash_amount'    => 'nullable|numeric|min:0',
        'online_amount'  => 'nullable|numeric|min:0',
        'emi_down_payment' => 'nullable|numeric|min:0',
        'emi_loan_amount' => 'nullable|numeric|min:0',
        'emi_interest_rate' => 'nullable|numeric|min:0|max:100',
        'emi_tenure' => 'nullable|string',
        'emi_monthly_amount' => 'nullable|numeric|min:0',
        'emi_aadhar_number' => 'nullable|string|max:20',
        'emi_do_id' => 'nullable|string|max:255',
        'emi_pan_number' => 'nullable|string|max:20',
        'emi_guarantor_name' => 'nullable|string|max:255',
        'labour_item_ids' => 'nullable|array',
        'labour_qtys'     => 'nullable|array',
        'labour_prices'   => 'nullable|array',
        'tds_percentage'  => 'nullable|numeric|min:0|max:100',
        'tds_amount'      => 'nullable|numeric|min:0',
        'deposit_amount'  => 'nullable|numeric|min:0',
        'other_charges_name' => 'nullable|array',
        'other_charges_name.*' => 'nullable|string|max:255',
        'other_charges_amount' => 'nullable|array',
        'other_charges_amount.*' => 'nullable|numeric|min:0',
        'exchange_material_id' => 'nullable|integer|exists:materials,id',
        'exchange_type_id'     => 'nullable|integer|exists:types,id',
        'exchange_price'       => 'nullable|numeric|min:0',
        'exchange_weight'      => 'nullable|numeric|min:0',
        'exchange_amount'      => 'nullable|numeric|min:0',
        'exchange_details'     => 'nullable',
    ], [
        'order_number.unique' => 'This order number already exists.',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => $validator->errors(),
        ], 422);
    }

    try {
        DB::beginTransaction();

        $order = Order::findOrFail($request->update_id);
        $previousStaffId = $order->staff_id;
        $previousOrderType = $order->order_type;
        $products = Product::with('category')->whereIn('id', $request->product_ids)->get();
        $isRental = $request->boolean('is_rental');
        $updatedQuotationStatus = $request->quotation_status ?? $order->quotation_status;
        if ($isRental) {
            $updatedQuotationStatus = 'rental';
        }
        $isConvertingQuotationToSale = ($order->quotation_status ?? '') === 'quotation'
            && $updatedQuotationStatus === 'sales';
        $orderUpdatedAt = now('Asia/Kolkata');
        $orderCreatedAt = $request->filled('order_date')
            ? Carbon::parse($request->order_date)->timezone('Asia/Kolkata')
            : $order->created_at;

        // Fetch old order ite  ms
        $oldItems = OrderItem::where('order_id', $order->id)->get();

        // Calculate subtotal & product-wise GST & adjust stock
        $subtotal = 0;
        $productWiseGstTotal = 0;
        $hasProductSpecificGst = false;

        // RESTORE OLD STOCK FIRST
        // Restore stock for both regular sales AND rental items.
        // Rental stock was deducted on order creation; it must be restored before re-deducting with new quantities.
        // For rental items: only restore the quantity that has NOT yet been returned
        // (returned items already had their stock restored by RentalReturnController).
        $returnedQuantities = [];
        $rentalReturnItemsToUpdate = []; // To remap orphaned return items to new order items

        if ($order->quotation_status !== 'quotation') {
            foreach ($oldItems as $oldItem) {
                $product = $products->firstWhere('id', $oldItem->product_id);
                if ($product && !$this->isWarrantyCategoryProduct($product)) {
                    // For rental items, only restore the portion still out with the customer
                    $qtyToRestore = (float) $oldItem->quantity;
                    if ($oldItem->is_rental) {
                        $returnsForThisItem = \App\Models\RentalReturnItem::where('order_item_id', $oldItem->id)->get();
                        $alreadyReturned = $returnsForThisItem->sum('qty_returned');
                        
                        $key = $oldItem->product_id . '_' . ($oldItem->product_variant_id ?: 0);
                        $returnedQuantities[$key] = ($returnedQuantities[$key] ?? 0) + $alreadyReturned;
                        
                        foreach ($returnsForThisItem as $rri) {
                            $rentalReturnItemsToUpdate[] = [
                                'return_item_id' => $rri->id,
                                'match_key' => $key,
                                'assigned' => false
                            ];
                        }

                        $qtyToRestore = max(0, $qtyToRestore - (float) $alreadyReturned);
                    }
                    if ($qtyToRestore > 0) {
                        if ($oldItem->product_variant_id) {
                            $variantObj = \App\Models\ProductVariant::find($oldItem->product_variant_id);
                            if ($variantObj) {
                                $variantObj->increment('qty', $qtyToRestore);
                            }
                        }
                        $product->increment('quantity', $qtyToRestore);
                    }
                }
            }
        }

        foreach ($request->product_ids as $index => $productId) {
            $product = $products->firstWhere('id', $productId);
            if ($product) {
                $quantity = $request->quantities[$index] ?? 0;
                $variantId = $request->variant_id[$index] ?? null;

                $isRentalItem = $isRental || !empty($request->rent_value[$index]);

                // Stock adjustment for both sales AND rentals (skip only for quotations and warranty products)
                $deductQty = $quantity;
                if ($isRentalItem && $updatedQuotationStatus !== 'quotation') {
                    $key = $productId . '_' . ($variantId ?: 0);
                    $alreadyReturned = $returnedQuantities[$key] ?? 0;
                    $deductQty = max(0, $quantity - $alreadyReturned);
                }

                if ($this->isWarrantyCategoryProduct($product)) {
                    // Warranty category products are sold without inventory tracking.
                } elseif ($updatedQuotationStatus !== 'quotation') {
                    $salePrice = floatval($request->prices[$index] ?? $product->price);
                    $isMainPrice = abs($salePrice - (float) $product->price) < 0.0001;

                    if ($deductQty > 0) {
                        if ($variantId) {
                            $variantObj = \App\Models\ProductVariant::find($variantId);
                            if ($variantObj) {
                                $variantObj->qty = max(0, (float) $variantObj->qty - $deductQty);
                                $variantObj->save();
                            }
                        }
                        if ($isMainPrice) {
                            $product->quantity = max(0, (float) $product->quantity - $deductQty);
                        }
                        $product->applyRateSales(
                            $isMainPrice ? [] : [['price' => $salePrice, 'qty' => $deductQty]],
                            (float) $product->quantity
                        );
                        $product->save();
                    }
                }

                // Calculate product total
                $discountPercent = $request->discounts[$index] ?? 0;
                $rentMultiplier = $isRentalItem ? max(1, (float) ($request->rent_value[$index] ?? 1)) : 1;
                $price = floatval($request->prices[$index] ?? $product->price);
                $baseProductTotal = $price * $quantity * $rentMultiplier;

                $itemDiscountAmount = $baseProductTotal * ($discountPercent / 100);
                $productTotal = max(0, $baseProductTotal - $itemDiscountAmount);
                $globalWithGst = ($request->gst_option === 'without' || $request->gst_option === 'without_gst') ? false : true;
                $inclusiveGst = $this->inclusiveGstDetails($productTotal, $product, $globalWithGst);
                $itemGstTotal = $inclusiveGst['total'];
                if ($itemGstTotal > 0) {
                    $hasProductSpecificGst = true;
                }

                $subtotal += $baseProductTotal;
                $productWiseGstTotal += $itemGstTotal;
            }
        }

        // Tax & discount calculations
        $totalProductsWithGstAndDiscount = 0;
        $totalItemDiscounts = 0;
        $totalGstOnFullBase = 0;
        foreach ($request->product_ids as $index => $productId) {
            $product = $products->firstWhere('id', $productId);
            if ($product) {
                $quantity = $request->quantities[$index] ?? 0;
                $discountPercent = $request->discounts[$index] ?? 0;
                $price = floatval($request->prices[$index] ?? $product->price);
                
                $isRentalItem = $isRental || !empty($request->rent_value[$index]);
                $rentMultiplier = $isRentalItem ? max(1, (float) ($request->rent_value[$index] ?? 1)) : 1;
                
                $baseProductTotal = $price * $quantity * $rentMultiplier;
                $globalWithGst = ($request->gst_option === 'without' || $request->gst_option === 'without_gst') ? false : true;
                $inclusiveGst = $this->inclusiveGstDetails((float) $baseProductTotal, $product, $globalWithGst);
                $itemGstAmount = $inclusiveGst['total'];
                $inclusiveLineTotal = $baseProductTotal + $itemGstAmount;
                $totalGstOnFullBase += $itemGstAmount;

                $rowDiscount = $inclusiveLineTotal * ($discountPercent / 100);
                $totalProductsWithGstAndDiscount += max(0, $inclusiveLineTotal - $rowDiscount);
                $totalItemDiscounts += $rowDiscount;
            }
        }

        // Bill-level additional discount is % of amount after item discounts (excl. GST) — matches POS + print.
        $afterItemDiscountBase = max(0, $totalProductsWithGstAndDiscount - $totalGstOnFullBase);
        $billDiscountPercent = max(0, min(100, (float) ($request->discount ?? 0)));
        $discountAmount = ($afterItemDiscountBase * $billDiscountPercent) / 100;

        // Add labour subtotal
        $labourSubtotal = 0;
        if ($request->has('labour_item_ids')) {
            foreach ($request->labour_item_ids as $index => $labourItemId) {
                $qty = floatval($request->labour_qtys[$index] ?? 0);
                $price = floatval($request->labour_prices[$index] ?? 0);
                $labourSubtotal += $qty * $price;
            }
        }
        // Add other charges
        $otherCharges = [];
        $otherChargesTotal = 0;
        if (!empty($request->other_charges_name) && is_array($request->other_charges_name)) {
            foreach ($request->other_charges_name as $index => $name) {
                $amount = (float) ($request->other_charges_amount[$index] ?? 0);
                if (!empty($name) && $amount > 0) {
                    $otherCharges[] = [
                        'name' => $name,
                        'amount' => $amount
                    ];
                    $otherChargesTotal += $amount;
                }
            }
        }

        $exchangeAmount = max(0, (float) ($request->exchange_amount ?? 0));
        $preTdsGrandTotal = max(0, ($totalProductsWithGstAndDiscount - $discountAmount) + ($request->shipping ?? 0) + $labourSubtotal + $otherChargesTotal - $exchangeAmount);
        $settings = Setting::where('branch_id', $branchIdToUse)->first();
        $isTdsEnabled = (bool) ($settings->tds_apply ?? false);
        $tdsPercentage = $isTdsEnabled ? max(0, min(100, (float) ($request->tds_percentage ?? 0))) : 0;
        $tdsAmount = $isTdsEnabled ? round(($preTdsGrandTotal * $tdsPercentage) / 100, 2) : 0;
        $grandTotal = max(0, round($preTdsGrandTotal - $tdsAmount));

        $isQuotationOrder = $updatedQuotationStatus === 'quotation';
        $rawPaymentMethod = strtolower(trim((string) ($request->payment_method ?? $order->payment_method ?? 'pending')));
        $normalizedPaymentMethod = match ($rawPaymentMethod) {
            'cash+online', 'cash_online', 'cash + online', 'cash+bank', 'cash_bank', 'cash + bank' => 'cash_online',
            'debit', 'debit card', 'scan', 'online', 'upi' => 'online',
            'emi' => 'emi',
            'cash' => 'cash',
            'pending', 'pay later', '' => 'pending',
            default => $rawPaymentMethod,
        };

        $paymentMethodForOrder = $isQuotationOrder ? 'pending' : $normalizedPaymentMethod;
        $paidType = strtolower(trim((string) ($request->paid_type ?? '')));
        $cashAmountInput = max(0, (float) ($request->cash_amount ?? 0));
        $onlineAmountInput = max(0, (float) ($request->online_amount ?? 0));
        $pendingAmountInput = max(0, (float) ($request->pending_amount ?? 0));
        $paymentMethodIsEmi = $paymentMethodForOrder === 'emi';
        $emiDownPayment = max(0, (float) ($request->emi_down_payment ?? 0));
        $emiLoanAmount = max(0, (float) ($request->emi_loan_amount ?? 0));
        $emiInterestRate = max(0, (float) ($request->emi_interest_rate ?? 0));
        $emiTenure = trim((string) ($request->emi_tenure ?? ''));
        if ($emiTenure === 'custom') {
            $emiTenure = (string) max(0, (int) ($request->emi_custom_tenure ?? 0));
        }
        $emiMonthlyAmount = max(0, (float) ($request->emi_monthly_amount ?? 0));
        $emiAadharNumber = trim((string) ($request->emi_aadhar_number ?? ''));
        $emiDoId = trim((string) ($request->emi_do_id ?? ''));
        $emiPanNumber = trim((string) ($request->emi_pan_number ?? ''));
        $emiGuarantorName = trim((string) ($request->emi_guarantor_name ?? ''));
        if ($paymentMethodForOrder === 'emi') {
            $requestedPaidAmount = $emiDownPayment;
            if ($emiLoanAmount <= 0) {
                $emiLoanAmount = max(0, $grandTotal - $emiDownPayment);
            }
            $emiTenureMonths = is_numeric($emiTenure) ? (int) $emiTenure : 0;
            if ($emiTenureMonths > 0 && $emiMonthlyAmount <= 0) {
                $interestAmount = $emiLoanAmount * ($emiInterestRate / 100);
                $emiMonthlyAmount = ($emiLoanAmount + $interestAmount) / $emiTenureMonths;
            }
        }

        $activePayments = PaymentStore::where('order_id', $order->id)
            ->where(function ($query) {
                $query->whereNull('isDeleted')->orWhere('isDeleted', 0);
            })
            ->get();

        $existingPaidAmount = $isRental ? (float) $activePayments->sum('payment_amount') : min($grandTotal, (float) $activePayments->sum('payment_amount'));
        $outstandingAmount = max(0, $grandTotal - $existingPaidAmount);

        $requestedPaidAmount = max(0, (float) ($request->payment_amount ?? 0));
        if ($request->filled('pending_amount')) {
            $requestedPaidAmount = max(0, $outstandingAmount - ($isRental ? $pendingAmountInput : min($pendingAmountInput, $outstandingAmount)));
        }

        if ($paymentMethodForOrder === 'cash_online') {
            $splitPaidAmount = $cashAmountInput + $onlineAmountInput;
            if ($splitPaidAmount > 0) {
                $requestedPaidAmount = $splitPaidAmount;
            }
        } elseif ($paymentMethodForOrder === 'cash' && $cashAmountInput > 0) {
            $requestedPaidAmount = $cashAmountInput;
        } elseif ($paymentMethodForOrder === 'online' && $onlineAmountInput > 0) {
            $requestedPaidAmount = $onlineAmountInput;
        }

        if (!$isQuotationOrder && $paymentMethodForOrder !== 'pending' && $paidType === 'full') {
            $requestedPaidAmount = $outstandingAmount;
        }

        $additionalPaidAmount = $isQuotationOrder || $paymentMethodForOrder === 'pending'
            ? 0
            : ($isRental ? $requestedPaidAmount : min($outstandingAmount, $requestedPaidAmount));
        $totalPaid = $isRental ? ($existingPaidAmount + $additionalPaidAmount) : min($grandTotal, $existingPaidAmount + $additionalPaidAmount);
        $remainingAmount = max($grandTotal - $totalPaid, 0);

        if ($remainingAmount <= 0 && $totalPaid > 0) {
            $paymentStatus = 'completed';
        } elseif ($totalPaid > 0) {
            $paymentStatus = 'partially';
        } else {
            $paymentStatus = 'pending';
        }

        // ✅ FIXED: Proper order number handling
        $updatedOrderNumber = $order->order_number;
        $oldStatus = $order->quotation_status ?? 'sales';
        $newStatus = $updatedQuotationStatus ?? 'sales';

        $statusChanged = $oldStatus !== $newStatus;

        // Check if we should update the order number
        if ($statusChanged) {
            $financialYearNumberingEnabled = $this->isFinancialYearOrderNumberingEnabled((int) $branchIdToUse);
            $isQuotation = $newStatus === 'quotation';
            $isAdvanceReceipt = $newStatus === 'advance_receipt';
            $isRentalCheck = $newStatus === 'rental';

            if ($isQuotation || $isAdvanceReceipt || $isRentalCheck) {
                if ($financialYearNumberingEnabled) {
                    $updatedOrderNumber = $this->generateFinancialYearOrderNumber(
                        (int) $branchIdToUse,
                        $newStatus,
                        $orderCreatedAt->copy()
                    );
                } else {
                    if ($isAdvanceReceipt) {
                        $maxAdvSeq = (int) (Order::where('quotation_status', 'advance_receipt')
                            ->whereRaw("order_number REGEXP '^ADV-[0-9]+$'")
                            ->selectRaw("MAX(CAST(SUBSTRING(order_number, 5) AS UNSIGNED)) as max_seq")
                            ->value('max_seq') ?? 0);
                        $updatedOrderNumber = 'ADV-' . ($maxAdvSeq + 1);
                    } elseif ($isRentalCheck) {
                        $maxRentalSeq = (int) (Order::where('quotation_status', 'rental')
                            ->whereRaw("order_number REGEXP '^RO-[0-9]+$'")
                            ->selectRaw("MAX(CAST(SUBSTRING(order_number, 4) AS UNSIGNED)) as max_seq")
                            ->value('max_seq') ?? 0);
                        $updatedOrderNumber = 'RO-' . str_pad($maxRentalSeq + 1, 6, '0', STR_PAD_LEFT);
                    } else {
                        $maxQSeq = (int) (Order::where('quotation_status', 'quotation')
                            ->whereRaw("order_number REGEXP '^Q-[0-9]+$'")
                            ->selectRaw("MAX(CAST(SUBSTRING(order_number, 3) AS UNSIGNED)) as max_seq")
                            ->value('max_seq') ?? 0);
                        $updatedOrderNumber = 'Q-' . ($maxQSeq + 1);
                    }
                    
                    $attempts = 0;
                    while (Order::where('order_number', $updatedOrderNumber)->exists() && $attempts < 50) {
                        $attempts++;
                        if ($isAdvanceReceipt) {
                            $maxAdvSeq = (int) (Order::where('quotation_status', 'advance_receipt')
                                ->whereRaw("order_number REGEXP '^ADV-[0-9]+$'")
                                ->selectRaw("MAX(CAST(SUBSTRING(order_number, 5) AS UNSIGNED)) as max_seq")
                                ->value('max_seq') ?? 0);
                            $updatedOrderNumber = 'ADV-' . ($maxAdvSeq + 1);
                        } elseif ($isRentalCheck) {
                            $maxRentalSeq = (int) (Order::where('quotation_status', 'rental')
                                ->whereRaw("order_number REGEXP '^RO-[0-9]+$'")
                                ->selectRaw("MAX(CAST(SUBSTRING(order_number, 4) AS UNSIGNED)) as max_seq")
                                ->value('max_seq') ?? 0);
                            $updatedOrderNumber = 'RO-' . str_pad($maxRentalSeq + 1, 6, '0', STR_PAD_LEFT);
                        } else {
                            $maxQSeq = (int) (Order::where('quotation_status', 'quotation')
                                ->whereRaw("order_number REGEXP '^Q-[0-9]+$'")
                                ->selectRaw("MAX(CAST(SUBSTRING(order_number, 3) AS UNSIGNED)) as max_seq")
                                ->value('max_seq') ?? 0);
                            $updatedOrderNumber = 'Q-' . ($maxQSeq + 1);
                        }
                    }
                }
            } else {
                $updatedOrderNumber = $this->generateSalesOrderNumber((int) $branchIdToUse);
            }
        } elseif ($request->filled('order_number') && !empty($request->order_number)) {
            // Use the order number sent from the frontend if status hasn't changed
            $updatedOrderNumber = $request->order_number;
        }

        // Update Order
        $storedPaymentMethod = $isQuotationOrder
            ? 'pending'
            : ($paymentMethodIsEmi
                ? 'emi'
                : ($additionalPaidAmount > 0
                    ? $paymentMethodForOrder
                    : ($remainingAmount > 0 ? 'pending' : $order->payment_method)));

        $order->update([
            'user_id' => $request->customer_id,
            'user_phone' => $request->customer_phone,
            'payment_method' => $storedPaymentMethod,
            'discount' => $request->discount,
            'discount_amount' => $totalItemDiscounts + $discountAmount,
            'gst_option' => ($request->gst_option === 'without' || $request->gst_option === 'without_gst') ? 'without_gst' : 'with_gst',
            'tax_id' => json_encode([]),
            'tax_amount' => $productWiseGstTotal,
            'subtotal' => $subtotal,
            'total_amount' => $grandTotal,
            'remaining_amount' => $remainingAmount,
            'payment_status'   => $paymentStatus,
            'quotation_status' => $updatedQuotationStatus,
            'rental_status'    => $updatedQuotationStatus,
            'order_number'     => $updatedOrderNumber,
            'shipping'         => $request->shipping ?? 0,
            'other_charges'    => !empty($otherCharges) ? $otherCharges : null,
            'exchange_material_id' => $request->exchange_material_id ?: null,
            'exchange_type_id'     => $request->exchange_type_id ?: null,
            'exchange_price'       => $request->exchange_price ? (float) $request->exchange_price : 0,
            'exchange_weight'      => $request->exchange_weight ? (float) $request->exchange_weight : 0,
            'exchange_amount'      => $exchangeAmount,
            'exchange_details'     => !empty($request->exchange_details) ? (is_array($request->exchange_details) ? $request->exchange_details : json_decode($request->exchange_details, true)) : null,
            'tds_percentage'   => $tdsPercentage,
            'tds_amount'       => $tdsAmount,
            'deposit_amount'   => $request->deposit_amount ?? 0,
            'remarks'          => $request->remarks ?? $order->remarks,
            'staff_id'         => $request->exists('assigned_staff_id')
                ? ($request->filled('assigned_staff_id') ? (int) $request->assigned_staff_id : null)
                : $order->staff_id,
            'order_type'       => $request->order_type ?? $order->order_type ?? 'self_pickup',
            'emi_down_payment' => $paymentMethodIsEmi ? $emiDownPayment : 0,
            'emi_loan_amount' => $paymentMethodIsEmi ? $emiLoanAmount : 0,
            'emi_interest_rate' => $paymentMethodIsEmi ? $emiInterestRate : 0,
            'emi_tenure' => $paymentMethodIsEmi ? $emiTenure : null,
            'emi_monthly_amount' => $paymentMethodIsEmi ? $emiMonthlyAmount : 0,
            'emi_aadhar_number' => $paymentMethodIsEmi ? $emiAadharNumber : null,
            'emi_do_id' => $paymentMethodIsEmi ? $emiDoId : null,
            'emi_pan_number' => $paymentMethodIsEmi ? $emiPanNumber : null,
            'emi_guarantor_name' => $paymentMethodIsEmi ? $emiGuarantorName : null,
            'created_at'       => $orderCreatedAt,
        ]);

        if (!$isQuotationOrder && $paymentMethodForOrder !== 'pending' && $additionalPaidAmount > 0) {
            $paymentTypeForStore = $paymentStatus === 'completed' ? 'fully' : 'partially';
            $bankId = $paymentMethodForOrder === 'cash' ? null : ($request->bank_id ?: null);

            if ($paymentMethodForOrder === 'cash_online') {
                $cashToStore = min($cashAmountInput, $additionalPaidAmount);
                $onlineToStore = min($onlineAmountInput, max($additionalPaidAmount - $cashToStore, 0));

                if (($cashToStore + $onlineToStore) <= 0) {
                    $onlineToStore = $additionalPaidAmount;
                } elseif (($cashToStore + $onlineToStore) < $additionalPaidAmount) {
                    $onlineToStore += ($additionalPaidAmount - ($cashToStore + $onlineToStore));
                }

                if ($cashToStore > 0) {
                    PaymentStore::create([
                        'user_id' => $order->user_id,
                        'order_id' => $order->id,
                        'payment_amount' => $cashToStore,
                        'payment_date' => now(),
                        'payment_method' => 'cash',
                        'payment_type' => $paymentTypeForStore,
                        'cash_amount' => $cashToStore,
                        'upi_amount' => 0,
                        'remaining_amount' => $remainingAmount,
                        'bank_id' => $bankId,
                        'remarks' => $request->remarks,
                    ]);
                }

                if ($onlineToStore > 0) {
                    PaymentStore::create([
                        'user_id' => $order->user_id,
                        'order_id' => $order->id,
                        'payment_amount' => $onlineToStore,
                        'payment_date' => now(),
                        'payment_method' => 'online',
                        'payment_type' => $paymentTypeForStore,
                        'cash_amount' => 0,
                        'upi_amount' => $onlineToStore,
                        'remaining_amount' => $remainingAmount,
                        'bank_id' => $bankId,
                        'remarks' => $request->remarks,
                    ]);
                }
            } else {
                PaymentStore::create([
                    'user_id' => $order->user_id,
                    'order_id' => $order->id,
                    'payment_amount' => $additionalPaidAmount,
                    'payment_date' => now(),
                    'payment_method' => $paymentMethodForOrder,
                    'payment_type' => $paymentTypeForStore,
                    'cash_amount' => $paymentMethodForOrder === 'cash' ? $additionalPaidAmount : 0,
                    'upi_amount' => $paymentMethodForOrder === 'online' ? $additionalPaidAmount : 0,
                    'emi_down_payment' => $paymentMethodIsEmi ? $emiDownPayment : 0,
                    'emi_loan_amount' => $paymentMethodIsEmi ? $emiLoanAmount : 0,
                    'emi_interest_rate' => $paymentMethodIsEmi ? $emiInterestRate : 0,
                    'emi_tenure' => $paymentMethodIsEmi ? $emiTenure : null,
                    'emi_monthly_amount' => $paymentMethodIsEmi ? $emiMonthlyAmount : 0,
                    'emi_aadhar_number' => $paymentMethodIsEmi ? $emiAadharNumber : null,
                    'emi_do_id' => $paymentMethodIsEmi ? $emiDoId : null,
                    'emi_pan_number' => $paymentMethodIsEmi ? $emiPanNumber : null,
                    'emi_guarantor_name' => $paymentMethodIsEmi ? $emiGuarantorName : null,
                    'remaining_amount' => $remainingAmount,
                    'bank_id' => $bankId,
                    'remarks' => $request->remarks,
                ]);
            }
        }

        // Remove old items
        OrderItem::where('order_id', $order->id)->delete();
        Sales_Labour_Items::where('order_id', $order->id)->delete();

        // Reinsert updated order items with GST details
        foreach ($request->product_ids as $index => $productId) {
            $product = $products->firstWhere('id', $productId);
            if ($product) {
                $quantity     = $request->quantities[$index] ?? 0;
                $discountPercentage = $request->discounts[$index] ?? 0;
                $price        = floatval($request->prices[$index] ?? $product->price);

                $isRentalItem = $isRental || !empty($request->rent_value[$index]);
                $rentMultiplier = $isRentalItem ? max(1, (float) ($request->rent_value[$index] ?? 1)) : 1;

                $discountAmount = (float) ($request->discounts[$index] ?? 0);
                $subTotal = $price * $quantity * $rentMultiplier;
                $globalWithGst = ($request->gst_option === 'without' || $request->gst_option === 'without_gst') ? false : true;
                $inclusiveGst = $this->inclusiveGstDetails((float) $subTotal, $product, $globalWithGst);
                $itemGstAmount = $inclusiveGst['total'];
                $productGstDetails = $inclusiveGst['details'];
                
                $inclusiveLineTotal = $subTotal + $itemGstAmount;
                $itemDiscountAmount = ($inclusiveLineTotal * $discountPercentage) / 100;
                $productTotal = max(0, $inclusiveLineTotal - $itemDiscountAmount);

                $newItem = OrderItem::create([
                    'order_id'            => $order->id,
                    'user_id'             => $order->user_id ?? null,
                    'category_id'         => $product->category_id ?? null,
                    'product_id'          => $productId,
                    'quantity'            => $quantity,
                    'price'               => $price,
                    'discount_percentage' => $discountPercentage,
                    'discount_amount'     => $itemDiscountAmount,
                    'total_amount'        => $productTotal,
                    'imei_no'             => $request->imei_no[$index] ?? null,
                    'product_variant_id'  => $request->variant_id[$index] ?? null,
                    'product_gst_details' => ! empty($productGstDetails) ? json_encode($productGstDetails) : null,
                    'product_gst_total'   => $itemGstAmount,
                    'branch_id'           => $branchIdToUse,
                    'created_by'          => $userData->id,
                    'is_rental'           => $isRentalItem ? 1 : 0,
                    'rent_value'          => $isRentalItem ? ($request->rent_value[$index] ?? 1) : null,
                    'rent_price'          => $isRentalItem ? $price : null,
                    'rent_period'         => $isRentalItem ? ($product->rent_period ?? 'day') : null,
                    'rent_start_at'       => $isRentalItem && !empty($request->rent_start_at[$index]) ? \Carbon\Carbon::parse($request->rent_start_at[$index])->format('Y-m-d H:i:s') : null,
                    'rent_end_at'         => $isRentalItem && !empty($request->rent_end_at[$index]) ? \Carbon\Carbon::parse($request->rent_end_at[$index])->format('Y-m-d H:i:s') : null,
                ]);

                if ($isRentalItem && !empty($rentalReturnItemsToUpdate)) {
                    $key = $productId . '_' . ($request->variant_id[$index] ?: 0);
                    foreach ($rentalReturnItemsToUpdate as &$rriData) {
                        if (!$rriData['assigned'] && $rriData['match_key'] === $key) {
                            \App\Models\RentalReturnItem::where('id', $rriData['return_item_id'])
                                ->update(['order_item_id' => $newItem->id]);
                            $rriData['assigned'] = true;
                        }
                    }
                }

                if (!$this->isWarrantyCategoryProduct($product) && $updatedQuotationStatus !== 'quotation') {
                    $inventoryType = $isRentalItem ? 'Update Rental' : 'Update Sale';
                    $variantId = $request->variant_id[$index] ?? null;
                    
                    if ($variantId) {
                        $lastInventory = ProductInventory::where('product_variant_id', $variantId)
                            ->where('branch_id', $branchIdToUse)
                            ->latest()
                            ->first();
                        $variantObj = \App\Models\ProductVariant::find($variantId);
                        
                        ProductInventory::create([
                            'product_id'         => $productId,
                            'product_variant_id' => $variantId,
                            'initial_stock'      => $lastInventory ? $lastInventory->current_stock : ($variantObj->qty ?? 0),
                            'current_stock'      => $variantObj ? $variantObj->qty : 0,
                            'branch_id'          => $branchIdToUse,
                            'create_by'          => $userData->id,
                            'type'               => $inventoryType,
                            'date'               => now(),
                        ]);
                    } else {
                        $lastInventory = ProductInventory::where('product_id', $productId)
                            ->where('branch_id', $branchIdToUse)
                            ->orderBy('id', 'desc')
                            ->first();

                        ProductInventory::create([
                            'product_id'    => $productId,
                            'initial_stock' => $lastInventory ? $lastInventory->current_stock : $product->quantity,
                            'current_stock' => $product->quantity,
                            'branch_id'     => $branchIdToUse,
                            'create_by'     => $userData->id,
                            'type'          => $inventoryType,
                            'date'          => now(),
                        ]);
                    }
                }
            }
        }

        // Reinsert labour items
        if ($request->has('labour_item_ids')) {
            foreach ($request->labour_item_ids as $index => $labourItemId) {
                Sales_Labour_Items::create([
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'labour_item_id' => $labourItemId,
                    'qty' => floatval($request->labour_qtys[$index] ?? 0),
                    'price' => floatval($request->labour_prices[$index] ?? 0),
                ]);
            }
        }

        $order->refresh();
        $customer = User::find($order->user_id);
        $newStaffId = $order->staff_id ? (int) $order->staff_id : null;
        $excludeIds = [(int) $userData->id, (int) ($newStaffId ?? 0)];

        NotificationService::notifyAssigneeIfChanged(
            (int) $userData->id,
            $previousStaffId ? (int) $previousStaffId : null,
            $newStaffId,
            (int) $order->branch_id,
            'order',
            'Order Assigned to You',
            'Order #' . $order->order_number . ' has been assigned to you for customer: ' . ($customer->name ?? 'N/A') . '.',
            '/sales-details/' . $order->id
        );

        if (($order->quotation_status ?? '') !== 'quotation'
            && ($order->order_type ?? '') === 'delivery'
            && ($previousOrderType ?? '') !== 'delivery') {
            NotificationService::notifyDeliveryOrderWarehouse(
                (int) $order->branch_id,
                $order,
                $customer,
                $excludeIds
            );
        }

        DB::commit();

        $updateSuccessMessage = $updatedQuotationStatus === 'advance_receipt'
            ? 'Advance Receipt updated successfully'
            : ($updatedQuotationStatus === 'quotation'
                ? 'Quotation updated successfully'
                : 'Order updated successfully');

        return response()->json([
            'success' => true,
            'message' => $updateSuccessMessage,
            'data' => $order,
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Failed to update order',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    public function convertQuotationToSale($id)
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated access'
            ], 401);
        }

        try {
            DB::beginTransaction();

            $order = Order::with('orderItems')->findOrFail($id);

            if (!in_array($order->quotation_status ?? '', ['quotation', 'advance_receipt'])) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Only quotation or advance receipt orders can be converted.'
                ], 422);
            }

            if ($order->orderItems->isEmpty()) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'No order items found for this quotation.'
                ], 422);
            }

            foreach ($order->orderItems as $item) {
                $product = Product::with('category')->find($item->product_id);
                if (!$product) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => "Product not found for item ID {$item->id}."
                    ], 422);
                }

                $requiredQty = (float) ($item->quantity ?? 0);
                if (!$this->isWarrantyCategoryProduct($product) && $requiredQty > (float) $product->quantity) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => "Insufficient stock for '{$product->name}'. Available: {$product->quantity}, required: {$requiredQty}."
                    ], 422);
                }
            }

            foreach ($order->orderItems as $item) {
                $product = Product::with('category')->findOrFail($item->product_id);
                $deductQty = (float) ($item->quantity ?? 0);

                if ($deductQty <= 0 || $this->isWarrantyCategoryProduct($product)) {
                    continue;
                }

                $product->decrement('quantity', $deductQty);
                $product->refresh();

                $lastInventory = ProductInventory::where('product_id', $item->product_id)
                    ->orderBy('id', 'desc')
                    ->first();

                ProductInventory::create([
                    'product_id' => $item->product_id,
                    'initial_stock' => $lastInventory->initial_stock ?? $product->quantity,
                    'current_stock' => $product->quantity,
                    'branch_id' => $order->branch_id,
                    'create_by' => $user->id,
                    'type' => 'Convert Quotation to Sale',
                    'date' => now(),
                ]);
            }

            $wasAdvanceReceipt = $order->quotation_status === 'advance_receipt';
            
            $branchIdForNumbering = (int) ($order->branch_id ?: ($user->branch_id ?: $user->id));
            $salesOrderNumber = $this->generateSalesOrderNumber($branchIdForNumbering);
            $order->quotation_status = 'sales';
            $order->order_number = $salesOrderNumber;
            $order->save();

            $customer = User::find($order->user_id);
            $excludeIds = [(int) $user->id, (int) ($order->staff_id ?? 0)];

            if (! empty($order->staff_id) && (int) $order->staff_id !== (int) $user->id) {
                NotificationService::notifyOrderAssigned(
                    (int) $order->staff_id,
                    $branchIdForNumbering,
                    $order,
                    $customer
                );
            }

            NotificationService::notifyDeliveryOrderWarehouse(
                $branchIdForNumbering,
                $order,
                $customer,
                $excludeIds
            );

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => $wasAdvanceReceipt ? 'Advance Receipt converted to sales successfully.' : 'Quotation converted to sales successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Quotation conversion failed: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Failed to convert quotation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Assign (or unassign) a staff member to an order inline from the sales list.
     */
    public function assignStaff(Request $request, $id)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated'], 401);
        }

        $order = Order::find($id);

        if (!$order) {
            return response()->json(['status' => false, 'message' => 'Order not found'], 404);
        }

        $staffId = $request->input('staff_id');

        // Allow null / empty to unassign
        if ($staffId === '' || $staffId === 'none') {
            $staffId = null;
        }

        if ($staffId !== null) {
            $staffUser = User::where('id', $staffId)
                ->where('role', 'staff')
                ->where('isDeleted', 0)
                ->first();

            if (!$staffUser) {
                return response()->json(['status' => false, 'message' => 'Invalid staff member'], 422);
            }
        }

        $previousStaffId = $order->staff_id;
        $order->staff_id = $staffId;
        $order->save();

        $customer = User::find($order->user_id);
        $branchId = (int) ($order->branch_id ?? $user->branch_id ?? $user->id);

        NotificationService::notifyAssigneeIfChanged(
            (int) $user->id,
            $previousStaffId ? (int) $previousStaffId : null,
            $staffId ? (int) $staffId : null,
            $branchId,
            'order',
            'Order Assigned to You',
            'Order #' . $order->order_number . ' has been assigned to you for customer: ' . ($customer->name ?? 'N/A') . '.',
            '/sales-details/' . $order->id
        );

        return response()->json([
            'status'     => true,
            'message'    => $staffId ? 'Staff assigned successfully.' : 'Staff unassigned.',
            'staff_id'   => $order->staff_id,
            'staff_name' => $staffId ? User::find($staffId)?->name : null,
        ]);
    }

    public function updateOrderType(Request $request, $id)
    {
        $user = Auth::guard('api')->user();

        if (! $user || ! in_array($user->role, ['admin', 'sub-admin'])) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $order = Order::find($id);
        if (! $order) {
            return response()->json(['status' => false, 'message' => 'Order not found'], 404);
        }

        $orderType = strtolower(trim((string) $request->input('order_type', '')));
        if (! in_array($orderType, ['self_pickup', 'delivery'], true)) {
            return response()->json(['status' => false, 'message' => 'Invalid order type'], 422);
        }

        $order->order_type = $orderType;
        $order->save();

        return response()->json([
            'status' => true,
            'message' => 'Order type updated successfully.',
            'order_type' => $order->order_type,
            'order_type_label' => $orderType === 'delivery' ? 'Delivery' : 'Self Pickup',
        ]);
    }

    private function normalizeGstDetails($gstDetails): array
    {
        if (empty($gstDetails)) {
            return [];
        }

        if (is_string($gstDetails)) {
            $decoded = json_decode($gstDetails, true);

            // Handle double-encoded JSON string
            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
            }

            $gstDetails = $decoded;
        }

        if (!is_array($gstDetails)) {
            return [];
        }

        // Handle single GST object shape
        if (isset($gstDetails['tax_name']) || isset($gstDetails['tax_rate'])) {
            return [$gstDetails];
        }

        return array_values(array_filter($gstDetails, function ($tax) {
            return is_array($tax);
        }));
    }

    public function orderReport(Request $request)
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated access'], 401);
        }

        $userId = $user->id;
        $role = $user->role;
        $branchId = $user->branch_id;

        $effectiveBranchId = ($role === 'staff')
            ? $branchId
            : ($request->input('selectedSubAdminId') ?? $userId);

        $filter = $request->query('filter');
        $month = $request->query('month');
        $year = $request->query('year');
        $customerId = $request->query('customer_id');
        $categoryId = $request->query('category_id');
        $staffId = $request->query('staff_id');
        $search = $request->query('search'); // ✅ Add search parameter
        $dateRange = $request->query('date_range');

        $selectedSubAdminId = $request->input('selectedSubAdminId');
        $query = OrderItem::with(['product.category', 'product.brand', 'product.unit', 'order', 'order.user.userDetail'])
            ->whereHas('order', function ($q) use ($filter, $month, $year, $customerId, $staffId, $user, $effectiveBranchId, $selectedSubAdminId, $role, $dateRange) {
                $q->where('isDeleted', 0);

                if ($user->role === 'staff') {
                    StaffDepartmentScope::applyOrderScope($q, $user, $selectedSubAdminId);
                } else {
                    $q->where('branch_id', $effectiveBranchId);
                }

                if (! empty($staffId) && in_array($role, ['admin', 'sub-admin'], true)) {
                    $staffFilterId = (int) $staffId;
                    $q->where(function ($staffQuery) use ($staffFilterId) {
                        $staffQuery->where('created_by', $staffFilterId)
                            ->orWhere('staff_id', $staffFilterId);
                    });
                }

                // ✅ Date filters
                if ($filter) {
                    $today = Carbon::today();
                    switch ($filter) {
                        case 'this_week':
                            $q->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                            break;
                        case 'this_month':
                            $q->whereMonth('created_at', $today->month)->whereYear('created_at', $today->year);
                            break;
                        case 'last_6_months':
                            $q->whereBetween('created_at', [Carbon::now()->subMonths(6), Carbon::now()]);
                            break;
                        case 'this_year':
                            $q->whereYear('created_at', $today->year);
                            break;
                        case 'previous_year':
                            $q->whereYear('created_at', Carbon::now()->subYear()->year);
                            break;
                    }
                }

                // ✅ Month filter
                if ($month) {
                    $q->whereMonth('created_at', $month);
                }

                // ✅ Year filter
                if ($year) {
                    $q->whereYear('created_at', $year);
                }

                // ✅ Date range filter
                if (!empty($dateRange)) {
                    $dates = explode(' - ', $dateRange);
                    if (count($dates) == 2) {
                        try {
                            $startDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                            $endDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[1]))->endOfDay();
                            $q->whereBetween('created_at', [$startDate, $endDate]);
                        } catch (\Exception $e) {
                            // Ignore invalid date
                        }
                    }
                }

                // ✅ Customer filter
                if ($customerId) {
                    $q->where('user_id', $customerId);
                }
            });

        // ✅ Category filter (via product relation)
        if ($categoryId) {
            $query->whereHas('product', function ($p) use ($categoryId) {
                $p->where('category_id', $categoryId);
            });
        }

        // ✅ Brand filter (via product relation)
        $brandId = $request->query('brand_id');
        if ($brandId) {
            $query->whereHas('product', function ($p) use ($brandId) {
                $p->where('brand_id', $brandId);
            });
        }

        // ✅ Search filter (by product name or SKU)
        if (!empty($search)) {
            $query->whereHas('product', function ($p) use ($search) {
                $p->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('SKU', 'LIKE', "%{$search}%");
            });
        }

        // ✅ Product name filter
        $productName = $request->query('product_name');
        if (!empty($productName)) {
            $query->whereHas('product', function ($p) use ($productName) {
                $p->where('name', 'LIKE', "%{$productName}%");
            });
        }

        // 🔹 Calculate total for summary (before pagination)
        $totalSoldAmountQuery = clone $query;
        $totalSoldAmount = $totalSoldAmountQuery->get()->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        // 🔹 Fetch active tax rates for the branch once to avoid N+1 query issues
        $taxRates = TaxRate::where('status', 'active')
            ->where('branch_id', $effectiveBranchId)
            ->where('isDeleted', 0)
            ->get()
            ->keyBy('id');

        // 🔹 Apply Pagination
        $perPage = (int) $request->input('per_page', 10);
        $perPage = $perPage > 0 ? min($perPage, 100) : 10; // Keep it clean but allow up to 100
        $orderItemsPaginated = $query->latest('id')->paginate($perPage);

        $orderItems = collect($orderItemsPaginated->items())->map(function ($item) {
            $product = $item->product;
            $soldQty = $item->quantity;
            $soldAmount = $item->price * $soldQty;

            // Decode product images
            $decodedImages = json_decode($product->images, true);
            $firstImage = $decodedImages[0] ?? 'admin/assets/img/product/noimage.png';
            $rowTaxes = [];
            $gstDetails = $item->product_gst_details;
            if (is_array($gstDetails)) {
                foreach ($gstDetails as $tax) {
                    $name = $tax['tax_name'] ?? ($tax['name'] ?? 'GST');
                    $rate = $tax['tax_rate'] ?? ($tax['rate'] ?? 0);
                    $amount = $tax['tax_amount'] ?? ($tax['amount'] ?? 0);

                    if ($rate > 0 || $amount > 0) {
                        $rowTaxes[] = [
                            'name' => strtoupper($name),
                            'rate' => $rate,
                            'amount' => number_format($amount, 2),
                        ];
                    }
                }
            }

            return [
                'id' => $item->id,
                'product_id' => $product->id,
                'name' => $product->name,
                'SKU' => $product->SKU,
                'category' => $product->category->name ?? 'N/A',
                'brand' => $product->brand->name ?? 'N/A',
                'unit' => $product->unit->unit_name ?? 'N/A',
                'sold_qty' => $soldQty,
                'sold_amount' => number_format($item->total_amount, 2),
                // Simple image path
                'image' => 'storage/' . $firstImage,
                // Uses accessor (returns full URLs)
                'image_url' => $product->image_url,
                'order_number' => $item->order->order_number ?? 'N/A',
                'customer_name' => $item->order->user->name ?? 'Walk-in Customer',
                'customer_gst' => $item->order->user->gst_number ?? 'N/A',
                'customer_address' => $item->order->user->userDetail->address ?? 'N/A',
                'order_date' => $item->order->created_at ? $item->order->created_at->format('d/m/Y h:i A') : 'N/A',
                'taxes' => $rowTaxes,
                'order_id' => $item->order_id,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $orderItems,
            'total_sold_amount' => number_format($totalSoldAmount, 2),
            'pagination' => [
                'current_page' => $orderItemsPaginated->currentPage(),
                'last_page' => $orderItemsPaginated->lastPage(),
                'per_page' => $orderItemsPaginated->perPage(),
                'total' => $orderItemsPaginated->total(),
                'from' => $orderItemsPaginated->firstItem(),
                'to' => $orderItemsPaginated->lastItem(),
                'next_page_url' => $orderItemsPaginated->nextPageUrl(),
                'prev_page_url' => $orderItemsPaginated->previousPageUrl(),
            ]
        ]);
    }

    public function getFilteredOrders(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated access',
            ], 401);
        }

        $userId = $user->id;
        $role = $user->role;

        // ✅ Prefer request input, then session branch
        $selectedSubAdminID = $this->resolveSelectedSubAdminIdFromRequest($request);

        // ✅ Build the query
        $query = Order::select('orders.*') // 👈 ensure created_by is included
            ->with([
                'user:id,name,phone',
                'orderItems:id,order_id',
                'creator:id,name,role',
                'staff:id,name',
            ])
            ->where('isDeleted', 0)

            // ✅ COUNT payments (for buttons / permissions)
            ->withCount([
                'payments as has_payment' => function ($q) {
                    $q->where('isDeleted', 0);
                },
            ])

            // ✅ SUM payments (for remaining / extra paid)
            ->withSum([
                'payments as total_paid' => function ($q) {
                    $q->where('isDeleted', 0);
                },
            ], 'payment_amount')
            ->withSum('returns as total_return', 'total_amount');

        StaffDepartmentScope::applyOrderScope($query, $user, $selectedSubAdminID);

        // ✅ Apply date filter (similar to get_orders)
        if ($request->filled('date')) {
            // If specific date is provided
            $query->whereDate('created_at', $request->date);
        } else {
            // Apply year filter if provided
            if ($request->filled('year')) {
                $query->whereYear('created_at', $request->year);
            }

            // Apply month filter if provided
            if ($request->filled('month')) {
                $query->whereMonth('created_at', $request->month);
            }
        }

        $this->applyFinancialYearFilter($query, $request->input('financial_year'), 'created_at');

        // ✅ Apply customer filter if provided
        if ($request->filled('customerId')) {
            $query->where('user_id', $request->customerId);
        }

        // ✅ Apply staff filter (admin/sub-admin can filter by assigned staff)
        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        // ✅ Apply sorting
        $query->orderBy('created_at', 'desc');

        try {
            // ✅ Get the results
            $orderType = $request->input('order_type');
            if (in_array($orderType, ['self_pickup', 'delivery'], true)) {
                $query->where('order_type', $orderType);
            }

            $statusFilter = $request->input('status');
            if (in_array($statusFilter, ['sales', 'quotation', 'rental', 'advance_receipt'], true)) {
                $query->where('quotation_status', $statusFilter);
            }

            $orders = $query->get();

            // ✅ Transform data to calculate remaining/extra paid and format dates
            $orders->transform(function ($order) {
                $orderTotal = (float) ($order->total_amount ?? 0);
                $totalPaid = (float) ($order->total_paid ?? 0);

                // ✅ Remaining
                $remaining = max(0, $orderTotal - $totalPaid);

                // ✅ Extra Paid
                $extraPaid = max(0, $totalPaid - $orderTotal);

                $order->remaining_amount = $remaining;
                $order->extra_paid = $extraPaid;

                // ✅ Format date and add biller info
                $order->created_date = $order->created_at
                    ? $order->created_at->format('d-M-Y h:i A')
                    : null;

                // ✅ Biller logic
                if ($order->created_by && $order->creator) {
                    $order->biller = $order->creator->name;
                } else {
                    $order->biller = 'Admin';
                }

                // ✅ Staff name
                $order->staff_name = $order->staff->name ?? null;

                // ✅ Invoice URL
                $order->invoice_pdf_url = url("/sales/invoice/pdf/" . $order->id);

                return $order;
            });

            // ✅ Get currency settings (optimized)
            $branchIdForSettings = $this->resolveSalesSettingsBranchId($user, $selectedSubAdminID);

            $settings = DB::table('settings')->where('branch_id', $branchIdForSettings)->first();

            $currencySymbol = $settings->currency_symbol ?? '₹';
            $currencyPosition = $settings->currency_position ?? 'left';
            $financialYearEnabled = (bool) ($settings->financial_year ?? true);

            return response()->json([
                'status' => true,
                'data' => $orders,
                'currency_symbol' => $currencySymbol,
                'currency_position' => $currencyPosition,
                'financial_year_enabled' => $financialYearEnabled,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
                'currency_symbol' => '₹',
                'currency_position' => 'left',
            ], 500);
        }
    }

    public function getHistory1($order_id)
    {
        $history = PaymentStore::where('order_id', $order_id)
            ->where(function ($query) {
                $query->whereNull('isDeleted')->orWhere('isDeleted', 0);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $order = Order::findOrFail($order_id);

        $totalPaid = $history->sum('payment_amount');

        // Calculate Return Amount
        $returnAmount = \App\Models\SalesReturn::where('order_id', $order_id)->sum('total_amount');

        // ✅ Dynamic calculation (NO column)
        $extraPaid = max(0, $totalPaid - ($order->total_amount - $returnAmount));
        $remaining = max(0, ($order->total_amount - $returnAmount) - $totalPaid);

        return response()->json([
            'status' => 'success',
            'data' => $history,
            'summary' => [
                'order_total' => $order->total_amount,
                'total_paid' => $totalPaid,
                'return_amount' => $returnAmount,
                'extra_paid' => $extraPaid,
                'remaining' => $remaining,
            ],
        ]);
    }

    public function exportOrders(Request $request)
    {
        // $user = auth()->user();
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated access',
            ], 401);
        }

        $userId = $user->id;
        $role = $user->role;
        // $selectedSubAdminId = $request->query('selectedSubAdminId');
        // Sub-admin id (from dropdown or session fallback)
        $selectedSubAdminId = $request->query('selectedSubAdminId') ?? session('selectedSubAdminId') ?? $user->id;

        $year = $request->query('year');
        $month = $request->query('month');
        $date = $request->query('date');
        $customerId = $request->query('customerId');
        $formatCurrency = $request->query('format_currency');

        $settings = DB::table('settings')->first();
        $currencySymbol = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';

        try {
            $query = Order::with(['user:id,name,phone'])
                ->where('isDeleted', 0);
            // ->where('type', 'Sales');

            // 🔹 Year filter
            if (!empty($year)) {
                $query->whereYear('created_at', $year);
            }

            // 🔹 Month filter
            if (!empty($month)) {
                $query->whereMonth('created_at', $month);
            }

            // 🔹 Exact Date filter
            if (!empty($date)) {
                // Convert DD-MM-YYYY to YYYY-MM-DD
                $dateParts = explode('-', $date);
                if (count($dateParts) === 3) {
                    $formattedDate = $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0];
                    $query->whereDate('created_at', $formattedDate);
                }
            }

            // 🔹 Customer filter
            if ($request->filled('customerId')) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('name', $request->customerId);
                });
            }

            $orderType = $request->query('order_type');
            if (in_array($orderType, ['self_pickup', 'delivery'], true)) {
                $query->where('order_type', $orderType);
            }

            $statusFilter = $request->query('status');
            if (in_array($statusFilter, ['sales', 'quotation', 'rental', 'advance_receipt'], true)) {
                $query->where('quotation_status', $statusFilter);
            }

            StaffDepartmentScope::applyOrderScope($query, $user, $selectedSubAdminId);

            $orders = $query->orderBy('created_at', 'desc')->get();

            $formatIndian = function ($num) {
                $num = (float)$num;
                $explode = explode(".", number_format($num, 2, '.', ''));
                $whole = $explode[0];
                $decimal = $explode[1];

                $lastThree = substr($whole, -3);
                $restUnits = substr($whole, 0, -3);
                if ($restUnits != '') {
                    $restUnits = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $restUnits);
                    $whole = $restUnits . "," . $lastThree;
                }
                return $whole . "." . $decimal;
            };

            // Generate Excel same as before...
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'Order Number');
            $sheet->setCellValue('B1', 'Date');
            $sheet->setCellValue('C1', 'Customer Name');
            $sheet->setCellValue('D1', 'Total Amount');
            $sheet->setCellValue('E1', 'Remaining Amount');
            $sheet->setCellValue('F1', 'Payment Status');
            $sheet->setCellValue('G1', 'Payment Method');
            $sheet->getStyle('A1:G1')->getFont()->setBold(true);

            $row = 2;
            foreach ($orders as $order) {
                $sheet->setCellValue('A' . $row, $order->order_number ?? 'N/A');
                $sheet->setCellValue('B' . $row, $order->created_at->format('d-m-Y'));
                $sheet->setCellValue('C' . $row, $order->user->name ?? 'N/A');
                // $sheet->setCellValue('D' . $row, $order->total_amount ?? 0);
                // $sheet->setCellValue('E' . $row, $order->remaining_amount ?? 0);
                if ($formatCurrency === 'indian') {
                    $sheet->setCellValue('D' . $row, $formatIndian($order->total_amount ?? 0));
                    $sheet->setCellValue('E' . $row, $formatIndian($order->remaining_amount ?? 0));

                    // Set these columns as text to preserve the formatting
                    $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                } else {
                    $sheet->setCellValue('D' . $row, $order->total_amount ?? 0);
                    $sheet->setCellValue('E' . $row, $order->remaining_amount ?? 0);

                    // Set as number format
                    $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                    $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                }
                $sheet->setCellValue('F' . $row, $order->payment_status ?? 'N/A');
                $sheet->setCellValue('G' . $row, $order->payment_method ?? 'N/A');
                $row++;
            }
            foreach (range('A', 'G') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // $writer   = new Xlsx($spreadsheet);
            // $fileName = 'Sales_' . date('Ymd_His') . '.xlsx';

            // return response()->streamDownload(function () use ($writer) {
            //     $writer->save('php://output');
            // }, $fileName, [
            //     'Content-Type'                  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            //     'Access-Control-Expose-Headers' => 'Content-Disposition',
            // ]);

            // Save to public storage
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filename = 'Sales_' . date('Ymd_His') . '.xlsx';
            $relativePath = 'exports/' . $filename;

            // Save temporary file
            $temp_file = tempnam(sys_get_temp_dir(), 'excel');
            $writer->save($temp_file);
            Storage::disk('public')->put($relativePath, file_get_contents($temp_file));
            unlink($temp_file);

            // Generate public URL

            $fileUrl = asset(env('ImagePath') . 'storage/' . $relativePath);

            return response()->json([
                'status' => true,
                'message' => 'Sales Excel generated successfully.',
                'file_url' => $fileUrl,
                'file_name' => $filename,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function salse_invoice_pdf_download($id)
    {
        $view_id = $id;
        $sales = Order::with(['staff:id,name', 'creator:id,name'])->find($view_id);
        $authUser = Auth::guard('api')->user();
        $subAdminId = session('selectedSubAdminId') ?? $authUser->id;
        $setting = Setting::where('branch_id', $subAdminId)->first();

        if (!$sales) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        $labourItems = Sales_Labour_Items::where('order_id', $id)
            ->with('labourItem')
            ->get();
        // dd($labourItems);

        $labourCost = 0;
        if ($labourItems && $labourItems->isNotEmpty()) {
            foreach ($labourItems as $labourItem) {
                $labourCost += ($labourItem->qty ?? 0) * ($labourItem->price ?? 0);
            }
        }

        // Fetch user data (order belongs to which user)
        $user = $sales->user_id ? User::with('userDetail')->find($sales->user_id) : null;

        // Helper for currency formatting
        $formatCurrency = function ($amount) use ($setting) {
            $amount = number_format($amount, 2);
            return $setting->currency_position === 'right'
                ? $amount . $setting->currency_symbol
                : $setting->currency_symbol . $amount;
        };

        // ✅ Subtotal
        $orderItems = OrderItem::with(['product', 'variant'])->where('order_id', $view_id)->get();
        $subtotal = $orderItems->sum('total_amount');

        // ✅ Discount
        $discountPercent = $sales->discount ?? 0;
        $discountAmount = ($discountPercent / 100) * $subtotal;
        $afterDiscount = $subtotal - $discountAmount;

        // ✅ Tax calculation
        $taxDetails = [];
        $totalTaxAmount = 0;
        if ($sales->gst_option === 'with_gst') {
            $taxIdsRaw = $sales->tax_id;
            $taxIds = is_array($taxIdsRaw) ? $taxIdsRaw : (json_decode($taxIdsRaw, true) ?? []);
            if (!empty($taxIds)) {
                $taxes = TaxRate::whereIn('id', $taxIds)->get();
                foreach ($taxes as $tax) {
                    $taxAmount = ($tax->tax_rate / 100) * $afterDiscount;
                    $totalTaxAmount += $taxAmount;
                    $taxDetails[] = [
                        'name' => $tax->tax_name,
                        'rate' => $tax->tax_rate,
                        'amount' => $taxAmount,
                        'formatted_amount' => $formatCurrency($taxAmount),
                    ];
                }
            }
        }

        // ✅ Final total
        $finalTotal =
            $afterDiscount +
            $totalTaxAmount +
            $labourCost;

        // ✅ FIX: paid_amount is NOT a column on orders table; query payment_store by order_id
        $paidAmount    = (float) PaymentStore::where('order_id', $sales->id)
            ->where(function ($q) {
                $q->whereNull('isDeleted')->orWhere('isDeleted', 0);
            })
            ->sum('payment_amount');
        $pendingAmount = max(0, $finalTotal - $paidAmount);

        // ✅ Prepare formatted values
        $formattedSubtotal = $formatCurrency($subtotal);
        $formattedDiscountAmount = $formatCurrency($discountAmount);
        $formattedAfterDiscount = $formatCurrency($afterDiscount);
        $formattedLabourCost = $formatCurrency($labourCost);
        $formattedFinalTotal = $formatCurrency($finalTotal);
        $formattedPaidAmount = $formatCurrency($paidAmount);
        $formattedPendingAmount = $formatCurrency($pendingAmount);

        // ✅ Prepare data for PDF view
        $pdfData = [
            'view_id' => $view_id,
            'sales' => $sales,
            'setting' => $setting,
            'orderItems' => $orderItems,
            'salesItems' => $orderItems,
            'labourItems' => $labourItems,
            'customer' => [
                'name' => $user->name ?? 'Default-customer',
                'email' => $user->email ?? '',
                'phone' => $user->phone ?? '',
                'address' => optional($user->userDetail)->address ?? 'arga',
                'delivery_address' => optional($user->userDetail)->delivery_address ?? '',
            ],
            'subtotal' => $formattedSubtotal,
            'discountPercent' => $discountPercent,
            'discountAmount' => $formattedDiscountAmount,
            'afterDiscount' => $formattedAfterDiscount,
            'labourCost' => $formattedLabourCost,
            'finalTotal' => $formattedFinalTotal,
            'paidAmount' => $formattedPaidAmount,
            'pendingAmount' => $formattedPendingAmount,
            'taxDetails' => $taxDetails,
        ];

        // ========== NEW PART: INVOICE SIZE SELECTION ==========
        // Determine which view to use based on the saved invoice size
       // ========== INVOICE SIZE SELECTION ==========
if ($setting && $setting->invoice_size === 'small') {

    // 80mm Thermal — page height matches content (no long blank tail)
    $pdf = make_thermal_invoice_pdf(
        'sales.salse-invoice-small-pdf',
        $pdfData,
        $pdfData['orderItems'] ?? ($pdfData['order_items'] ?? 1)
    );

} else {

    // Normal A4 Invoice
    $pdf = PDF::loadView('sales.salse-invoice-pdf', $pdfData)
        ->setPaper(...invoice_regular_paper($setting));
}
// ============================================
        // =======================================================


        // ✅ Generate PDF
        // $pdf = PDF::loadView('sales.salse-invoice-pdf', $pdfData);

        // ✅ Save PDF to storage (public folder)
        $fileName = 'invoice_' . $view_id . '.pdf';
        // $filePath = '/storage/app/public/sales-invoices/' . $fileName;

        // Ensure directory exists
        // if (!file_exists(storage_path('/storage/app/public/sales-invoices/'))) {
        //     mkdir(storage_path('/storage/app/public/sales-invoices/'), 0777, true);
        // }

        $relativePath = 'sales-invoices/' . $fileName;
        Storage::disk('public')->put($relativePath, $pdf->output());

        // Public URL

        $fileUrl = asset(env('ImagePath') . 'storage/' . $relativePath);

        return response()->json([
            'status' => true,
            'message' => 'Sales Invoice PDF generated successfully.',
            'file_url' => $fileUrl,
            'file_name' => $fileName,
        ]);
    }

    public function exportOrdersPDF(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated access',
            ], 401);
        }

        $userId = $user->id;
        $role = $user->role;
        $userBranchId = $user->branch_id;
        $selectedSubAdminId = $request->query('selectedSubAdminId') ?? $userId;

        // 🔹 Fetch branch-wise setting
        // $setting = Setting::where('branch_id', $selectedSubAdminId)->first();
        if ($role === 'staff' && $userBranchId) {
            $branchIdToUse = $userBranchId;
        } elseif ($role === 'admin' && !empty($selectedSubAdminId)) {
            $branchIdToUse = $selectedSubAdminId;
        } elseif ($role === 'sub-admin') {
            $branchIdToUse = $userId;
        } else {
            $branchIdToUse = $userId;
        }

        // 🔹 Fetch settings branch-wise
        $setting = Setting::where('branch_id', $branchIdToUse)->first();

        try {
            $query = Order::with(['user:id,name,phone'])
                ->where('isDeleted', 0);

            // 🔹 Apply filters
            if ($request->filled('year')) {
                $query->whereYear('created_at', $request->year);
            }

            if ($request->filled('month')) {
                $query->whereMonth('created_at', $request->month);
            }

            if ($request->filled('date')) {
                $inputDate = trim($request->date);
                $formattedDate = preg_match('/^\d{2}-\d{2}-\d{4}$/', $inputDate)
                    ? implode('-', array_reverse(explode('-', $inputDate)))
                    : $inputDate;
                $query->whereDate('created_at', $formattedDate);
            }
            if ($request->filled('customerId')) {
                $query->whereHas('user', fn($q) => $q->where('name', $request->customerId));
            }

            $orderType = $request->query('order_type');
            if (in_array($orderType, ['self_pickup', 'delivery'], true)) {
                $query->where('order_type', $orderType);
            }

            $statusFilter = $request->query('status');
            if (in_array($statusFilter, ['sales', 'quotation', 'rental', 'advance_receipt'], true)) {
                $query->where('quotation_status', $statusFilter);
            }

            StaffDepartmentScope::applyOrderScope($query, $user, $selectedSubAdminId);

            $orders = $query->orderBy('created_at', 'desc')->get();

            if ($orders->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No orders found for the given filters.',
                ], 404);
            }

            // 🔹 Generate PDF
            $pdf = Pdf::loadView('sales.orders_pdf', [
                'orders' => $orders,
                'setting' => $setting,
            ])->setPaper('A4', 'landscape');

            // 🔹 Save PDF to storage
            $fileName = 'sales_report_' . now()->format('Ymd_His') . '.pdf';
            $relativePath = 'sales-reports/' . $fileName;
            Storage::disk('public')->put($relativePath, $pdf->output());

            // 🔹 Generate full URL

            $fileUrl = asset(env('ImagePath') . 'storage/' . $relativePath);

            // 🔹 Return JSON response
            return response()->json([
                'status' => true,
                'message' => 'Sales report PDF generated successfully.',
                'file_url' => $fileUrl,
                'file_name' => $fileName,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while generating PDF.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function export_sales_report_pdf_api(Request $request)
    {
        try {
            $authUser = Auth::guard('api')->user();
            $selectedSubAdminId = $request->selectedSubAdminId ?? null;

            // ✅ OPTIMIZED: Determine branch_id based on role
            if ($authUser->role === 'staff' && $authUser->branch_id) {
                $branchIdToUse = $authUser->branch_id;
            } elseif ($authUser->role === 'admin' && !empty($selectedSubAdminId)) {
                $branchIdToUse = $selectedSubAdminId;
            } else {
                $branchIdToUse = $authUser->id;
            }

            $idsArray = $request->input('ids', []);
            $idsString = implode(',', $idsArray);

            if (empty($idsArray)) {
                return response()->json([
                    'status' => false,
                    'message' => 'No sales IDs provided.',
                ]);
            }

            $salesQuery = OrderItem::with([
                'product:id,name,price,category_id',
                'product.category:id,name',
                'variant',
                'invoice:id,order_id,discount,gst_option,tax_id',
                'user:id,name',
                'user.userDetail:id,user_id,address',
            ])
                ->whereIn('id', $idsArray);

            if ($authUser) {
                $salesQuery->whereHas('order', function ($orderQuery) use ($authUser, $selectedSubAdminId) {
                    StaffDepartmentScope::applyOrderScope($orderQuery, $authUser, $selectedSubAdminId);
                });
            }

            $sales = $salesQuery->get();

            if ($sales->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No sales data found.',
                ]);
            }

            // ✅ OPTIMIZED: Cache settings
            $setting = cache()->remember("setting_branch_{$branchIdToUse}", 300, function () use ($branchIdToUse) {
                return Setting::select('name', 'currency_symbol', 'currency_position')
                    ->where('branch_id', $branchIdToUse)
                    ->first();
            });

            $currencySymbol = $setting->currency_symbol ?? '₹';
            $currencyPosition = $setting->currency_position ?? 'left';

            // ✅ OPTIMIZED: Collect all unique tax IDs first (single query instead of N queries)
            $allTaxIds = [];
            $invoices = [];

            foreach ($sales as $sale) {
                if ($sale->invoice) {
                    $invoiceId = $sale->invoice->id;
                    if (!isset($invoices[$invoiceId])) {
                        $invoices[$invoiceId] = $sale->invoice;
                        $rowTaxIdsRaw = $sale->invoice->tax_id;
                        $rowTaxIds = is_array($rowTaxIdsRaw) ? $rowTaxIdsRaw : (json_decode($rowTaxIdsRaw ?? '[]', true) ?: []);
                        if (!empty($rowTaxIds) && ($sale->invoice->gst_option ?? 'without_gst') === 'with_gst') {
                            $allTaxIds = array_merge($allTaxIds, $rowTaxIds);
                        }
                    }
                }
            }

            // ✅ OPTIMIZED: Load all tax rates in ONE query
            $allTaxIds = array_unique($allTaxIds);
            $taxRatesMap = [];
            if (!empty($allTaxIds)) {
                $taxRates = TaxRate::where('status', 'active')
                    ->where('branch_id', $branchIdToUse)
                    ->where('isDeleted', 0)
                    ->whereIn('id', $allTaxIds)
                    ->get(['id', 'tax_name', 'tax_rate'])
                    ->keyBy('id');

                $taxRatesMap = $taxRates->toArray();
            }

            // ✅ OPTIMIZED: Single pass calculation
            $subtotal = 0;
            $discountAmount = 0;
            $taxDetails = [];
            $totalTaxAmount = 0;

            foreach ($sales as $sale) {
                $subtotal += $sale->total_amount;

                // Discount calculation
                if ($sale->invoice && $sale->invoice->discount) {
                    $discountPercent = $sale->invoice->discount;
                    $discountAmount += ($sale->total_amount * $discountPercent) / 100;
                }

                // Tax calculation (using pre-loaded tax rates)
                $rowTaxAmount = 0;
                $unitPrice = $sale->price;

                // Apply discount per unit
                if ($sale->invoice && $sale->invoice->discount) {
                    $discountPercent = $sale->invoice->discount;
                    $unitPrice -= ($unitPrice * $discountPercent) / 100;
                }

                if ($sale->invoice && ($sale->invoice->gst_option ?? 'without_gst') === 'with_gst') {
                    $rowTaxIdsRaw = $sale->invoice->tax_id;
                    $rowTaxIds = is_array($rowTaxIdsRaw) ? $rowTaxIdsRaw : (json_decode($rowTaxIdsRaw ?? '[]', true) ?: []);

                    foreach ($rowTaxIds as $taxId) {
                        if (isset($taxRatesMap[$taxId])) {
                            $tax = $taxRatesMap[$taxId];
                            $taxBase = $unitPrice * $sale->quantity;
                            $amount = $taxBase * ($tax['tax_rate'] / 100);
                            $rowTaxAmount += $amount;

                            if (!isset($taxDetails[$taxId])) {
                                $taxDetails[$taxId] = [
                                    'name' => $tax['tax_name'],
                                    'rate' => $tax['tax_rate'],
                                    'amount' => 0,
                                ];
                            }
                            $taxDetails[$taxId]['amount'] += $amount;
                        }
                    }
                }

                $sale->rowFinalTotal = ($unitPrice * $sale->quantity) + $rowTaxAmount;
                $totalTaxAmount += $rowTaxAmount;
            }

            $subtotalAfterDiscount = $subtotal - $discountAmount;
            $totalAmount = $sales->sum('rowFinalTotal');

            // 🔹 Prepare PDF data
            $pdfData = [
                'sales' => $sales,
                'setting' => $setting,
                'currencySymbol' => $currencySymbol,
                'currencyPosition' => $currencyPosition,
                'subtotal' => $subtotal,
                'discountAmount' => $discountAmount,
                'taxDetails' => $taxDetails,
                'totalTaxAmount' => $totalTaxAmount,
                'totalAmount' => $totalAmount,
            ];

            // 🔹 Generate PDF
            $pdf = PDF::loadView('sales.sales-invoice-report-pdf', $pdfData)
                ->setPaper('A4', 'portrait')
                ->setOptions([
                    'defaultFont' => 'DejaVu Sans',
                    'isHtml5ParserEnabled' => true,
                ]);

            // 🔹 Save PDF file in storage
            $fileName = 'sales_report_' . now()->format('Ymd_His') . '.pdf';
            $relativePath = 'sales-reports/' . $fileName;

            Storage::disk('public')->put($relativePath, $pdf->output());

            // 🔹 Get full URL
            $fileUrl = asset(env('ImagePath') . 'storage/' . $relativePath);

            return response()->json([
                'status' => true,
                'message' => 'Sales Report PDF generated successfully.',
                'file_url' => $fileUrl,
                'file_name' => $fileName,
                'ids_used' => $idsString, // 👈 for debug
            ]);
        } catch (\Throwable $e) {
            Log::error('Sales PDF Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to generate Sales Report PDF.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function view_sales_report(Request $request)
    {
        try {
            $ids = $request->input('ids'); // can be array or comma-separated string
            $selectedSubAdminId = $request->input('selectedSubAdminId');

            if (empty($ids)) {
                return response()->json([
                    'status' => false,
                    'message' => 'No sales selected for report view.',
                ], 400);
            }

            // Convert to array if comma-separated
            $idsArray = is_array($ids) ? $ids : explode(',', $ids);

            // 🔹 Store IDs in a comma-separated string for URL
            $idsString = implode(',', $idsArray);

            // 🔹 Determine branch_id (safe fallback)
            // If user is logged in, we can still read role info
            $authUser = Auth::guard('api')->user();
            if ($authUser) {
                if ($authUser->role === 'staff' && $authUser->branch_id) {
                    $branchIdToUse = $authUser->branch_id;
                } elseif ($authUser->role === 'admin' && !empty($selectedSubAdminId)) {
                    $branchIdToUse = $selectedSubAdminId;
                } else {
                    $branchIdToUse = $authUser->id;
                }
            } else {
                // 🔹 No authentication — require branch ID from frontend
                if (!empty($selectedSubAdminId)) {
                    $branchIdToUse = $selectedSubAdminId;
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Branch ID missing (unauthenticated request).',
                    ], 400);
                }
            }

            // ✅ Generate the report URL
            // $reportUrl = url('sales/report/view-page?ids=' . urlencode($idsString) . '&branch=' . $branchIdToUse);
            $reportUrl = url('sales/report/view-page?ids=' . $idsString . '&branch=' . $branchIdToUse);

            return response()->json([
                'status' => true,
                'message' => 'Sales report link generated successfully.',
                'view_link' => $reportUrl,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to generate sales report link.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function exportTdsOrderReportPdf(Request $request)
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated access'], 401);
        }

        $query = $this->buildTdsOrderReportQuery($request, $user);
        $summary = $this->getTdsOrderReportSummary($query);
        $orders = (clone $query)->latest('id')->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No TDS records found for the selected filters.',
            ], 404);
        }

        $branchId = $this->resolveBranchIdForTdsReport($user, $request->input('selectedSubAdminId'));
        $setting = Setting::where('branch_id', $branchId)->first() ?? Setting::first();
        [$currencySymbol, $currencyPosition] = $this->getTdsCurrencySettings($setting);

        $customerLabel = 'All Customers';
        if ($request->filled('customer_id')) {
            $customer = User::select('name')->find((int) $request->query('customer_id'));
            $customerLabel = $customer->name ?? 'Selected Customer';
        }

        $reportFilters = [
            'date' => $this->getTdsDateFilterLabel($request->query('filter')),
            'month' => $this->getTdsMonthLabel($request->query('month')),
            'year' => $this->getTdsYearLabel($request->query('year')),
            'customer' => $customerLabel,
            'search' => trim((string) $request->query('search', '')),
        ];

        $reportRows = $orders
            ->map(fn($order) => $this->transformTdsOrderRow($order))
            ->values();

        $pdf = Pdf::loadView('sales.tds_report_pdf', [
            'orders' => $reportRows,
            'summary' => $summary,
            'settings' => $setting,
            'currencySymbol' => $currencySymbol,
            'currencyPosition' => $currencyPosition,
            'reportFilters' => $reportFilters,
            'generatedAt' => now()->format('d-m-Y h:i A'),
        ])->setPaper('A4', 'portrait');

        $fileName = 'tds_report_' . now()->format('Ymd_His') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $fileName, [
            'Content-Type' => 'application/pdf',
            'Access-Control-Expose-Headers' => 'Content-Disposition',
        ]);
    }

       public function exportTdsOrderReportExcel(Request $request)
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated access'], 401);
        }

        $query = $this->buildTdsOrderReportQuery($request, $user);
        $summary = $this->getTdsOrderReportSummary($query);
        $orders = (clone $query)->latest('id')->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No TDS records found for the selected filters.',
            ], 404);
        }

        $branchId = $this->resolveBranchIdForTdsReport($user, $request->input('selectedSubAdminId'));
        $setting = Setting::where('branch_id', $branchId)->first() ?? Setting::first();
        [$currencySymbol, $currencyPosition] = $this->getTdsCurrencySettings($setting);

        $formatAmount = function (float $value) use ($currencySymbol, $currencyPosition): string {
            $formatted = number_format($value, 2, '.', ',');
            return $currencyPosition === 'right' ? $formatted . $currencySymbol : $currencySymbol . $formatted;
        };

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'TDS Report');
        $sheet->setCellValue('A2', 'Generated On: ' . now()->format('d-m-Y h:i A'));
        $sheet->setCellValue(
            'A3',
            'Filters: ' .
                $this->getTdsDateFilterLabel($request->query('filter')) . ' | ' .
                $this->getTdsMonthLabel($request->query('month')) . ' | ' .
                $this->getTdsYearLabel($request->query('year'))
        );

        $headers = ['Sr No', 'Order No.', 'Order Date', 'Customer', 'Total Amount', 'TDS %', 'TDS Amount', 'Payment Status'];
        $sheet->fromArray($headers, null, 'A5');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A5:H5')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A5:H5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFF9F43');

        $row = 6;
        foreach ($orders as $index => $order) {
            $mapped = $this->transformTdsOrderRow($order);

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $mapped['order_number']);
            $sheet->setCellValue('C' . $row, $mapped['order_date']);
            $sheet->setCellValue('D' . $row, trim($mapped['customer_name'] . ' ' . $mapped['customer_phone']));
            $sheet->setCellValue('E' . $row, $formatAmount((float) $mapped['total_amount']));
            $sheet->setCellValue('F' . $row, $mapped['tds_percentage_display'] . '%');
            $sheet->setCellValue('G' . $row, $formatAmount((float) $mapped['tds_amount']));
            $sheet->setCellValue('H' . $row, ucfirst($mapped['payment_status'] ?: '-'));

            $sheet->getStyle('E' . $row)->getNumberFormat()
                ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
            $sheet->getStyle('G' . $row)->getNumberFormat()
                ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

            $row++;
        }

        $summaryRow = $row + 1;
        $sheet->setCellValue('A' . $summaryRow, 'Total Orders');
        $sheet->setCellValue('B' . $summaryRow, (int) $summary['total_orders']);
        $sheet->setCellValue('D' . $summaryRow, 'Total TDS Amount');
        $sheet->setCellValue('E' . $summaryRow, $formatAmount((float) $summary['total_tds_amount']));
        $sheet->setCellValue('G' . $summaryRow, 'Avg TDS %');
        $sheet->setCellValue('H' . $summaryRow, number_format((float) $summary['average_tds_percentage'], 2) . '%');
        $sheet->getStyle('A' . $summaryRow . ':H' . $summaryRow)->getFont()->setBold(true);

        foreach (range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $fileName = 'tds_report_' . now()->format('Ymd_His') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Access-Control-Expose-Headers' => 'Content-Disposition',
        ]);
    }

    public function tdsOrderReport(Request $request)
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated access'], 401);
        }

        $query = $this->buildTdsOrderReportQuery($request, $user);
        $summary = $this->getTdsOrderReportSummary($query);

        $perPage = (int) $request->input('per_page', 10);
        $perPage = max(10, min($perPage, 100));

        $ordersPaginated = $query->latest('id')->paginate($perPage);
        $orders = collect($ordersPaginated->items())
            ->map(fn($order) => $this->transformTdsOrderRow($order))
            ->values();

        return response()->json([
            'status' => true,
            'data' => $orders,
            'summary' => $summary,
            'pagination' => [
                'current_page' => $ordersPaginated->currentPage(),
                'last_page' => $ordersPaginated->lastPage(),
                'per_page' => $ordersPaginated->perPage(),
                'total' => $ordersPaginated->total(),
                'from' => $ordersPaginated->firstItem(),
                'to' => $ordersPaginated->lastItem(),
                'next_page_url' => $ordersPaginated->nextPageUrl(),
                'prev_page_url' => $ordersPaginated->previousPageUrl(),
            ],
        ]);
    }

      private function transformTdsOrderRow($order): array
    {
        $tdsPercentage = (float) ($order->tds_percentage ?? 0);
        $tdsAmount = (float) ($order->tds_amount ?? 0);
        $totalAmount = (float) ($order->total_amount ?? 0);

        return [
            'id' => $order->id,
            'order_number' => $order->order_number ?: ('ORD-' . $order->id),
            'order_date' => optional($order->created_at)->format('d/m/Y h:i A'),
            'customer_name' => optional($order->user)->name ?? 'Walk-in Customer',
            'customer_phone' => optional($order->user)->phone ?? '',
            'payment_status' => (string) ($order->payment_status ?? ''),
            'total_amount' => round($totalAmount, 2),
            'total_amount_display' => number_format($totalAmount, 2),
            'tds_percentage' => round($tdsPercentage, 2),
            'tds_percentage_display' => number_format($tdsPercentage, 2),
            'tds_amount' => round($tdsAmount, 2),
            'tds_amount_display' => number_format($tdsAmount, 2),
            'invoice_url' => route('sales.invoice', $order->id),
        ];
    }

    private function resolveBranchIdForTdsReport($user, $selectedSubAdminId = null): int
    {
        if ($user->role === 'staff') {
            return (int) ($user->branch_id ?? 0);
        }

        if ($user->role === 'admin' && !empty($selectedSubAdminId)) {
            return (int) $selectedSubAdminId;
        }

        return (int) $user->id;
    }

    private function getTdsDateFilterLabel(?string $filter): string
    {
        $labels = [
            'this_week' => 'This Week',
            'this_month' => 'This Month',
            'last_6_months' => 'Last 6 Months',
            'this_year' => 'This Year',
            'previous_year' => 'Previous Year',
        ];

        return $labels[$filter] ?? 'All Time';
    }

    private function getTdsMonthLabel($month): string
    {
        if (empty($month)) {
            return 'All Months';
        }

        $monthNumber = (int) $month;
        if ($monthNumber < 1 || $monthNumber > 12) {
            return 'All Months';
        }

        return Carbon::createFromDate(null, $monthNumber, 1)->format('F');
    }

    private function getTdsYearLabel($year): string
    {
        return !empty($year) ? (string) $year : 'All Years';
    }

    private function getTdsCurrencySettings(?Setting $setting): array
    {
        $symbol = trim(html_entity_decode($setting->currency_symbol ?? '&#8377;', ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $position = $setting->currency_position ?? 'left';

        return [$symbol, $position];
    }

     private function buildTdsOrderReportQuery(Request $request, $user)
    {
        $userId = $user->id;
        $role = $user->role;
        $filter = $request->query('filter');
        $month = $request->query('month');
        $year = $request->query('year');
        $customerId = $request->query('customer_id');
        $search = trim((string) $request->query('search', ''));
        $selectedSubAdminId = $request->input('selectedSubAdminId');
        $branchIdForNonStaff = $this->resolveBranchIdForTdsReport($user, $selectedSubAdminId);

        $query = Order::with(['user:id,name,phone'])
            ->where('isDeleted', 0)
            ->where(function ($q) {
                $q->where('tds_amount', '>', 0)
                    ->orWhere('tds_percentage', '>', 0);
            });

        if ($role === 'staff') {
            StaffDepartmentScope::applyOrderScope($query, $user, $selectedSubAdminId);
        } else {
            $query->where('branch_id', $branchIdForNonStaff);
        }

        if ($filter) {
            $today = Carbon::today();

            switch ($filter) {
                case 'this_week':
                    $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', $today->month)->whereYear('created_at', $today->year);
                    break;
                case 'last_6_months':
                    $query->whereBetween('created_at', [Carbon::now()->subMonths(6), Carbon::now()]);
                    break;
                case 'this_year':
                    $query->whereYear('created_at', $today->year);
                    break;
                case 'previous_year':
                    $query->whereYear('created_at', Carbon::now()->subYear()->year);
                    break;
            }
        }

        if ($month) {
            $query->whereMonth('created_at', $month);
        }

        if ($year) {
            $query->whereYear('created_at', $year);
        }

        if ($customerId) {
            $query->where('user_id', $customerId);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'LIKE', "%{$search}%")
                    ->orWhere('po_number', 'LIKE', "%{$search}%")
                    ->orWhere('payment_status', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('phone', 'LIKE', "%{$search}%");
                    });

                if (is_numeric($search)) {
                    $q->orWhere('id', (int) $search);
                }
            });
        }

        return $query;
    }

    public function importSalesList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => ['required', 'file', 'mimes:csv,txt,xls,xlsx'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please upload a valid CSV or Excel file.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $authUser = Auth::user();
        if (! $authUser) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated access',
            ], 401);
        }

        $branchId = $this->resolveImportBranchId($authUser, $request->input('selectedSubAdminId'));
        $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        if (count($rows) < 2) {
            return response()->json([
                'status' => false,
                'message' => 'The uploaded file does not contain import rows.',
            ], 422);
        }

        $requiredHeaders = ['party_name'];
        $headers = [];
        $headerRowNumber = null;

        foreach ($rows as $rowNumber => $candidateHeaderRow) {
            $candidateHeaders = [];
            foreach ($candidateHeaderRow as $column => $heading) {
                $normalized = $this->normalizeImportHeader((string) $heading);
                if ($normalized !== '') {
                    $candidateHeaders[$column] = $normalized;
                }
            }

            $matchedRequiredHeaders = array_intersect($requiredHeaders, array_values($candidateHeaders));
            if (count($matchedRequiredHeaders) === count($requiredHeaders)) {
                $headers = $candidateHeaders;
                $headerRowNumber = $rowNumber;
                break;
            }
        }

        if ($headerRowNumber === null) {
            return response()->json([
                'status' => false,
                'message' => 'Could not find the header row. Please include Party Name or Customer Name column.',
            ], 422);
        }

        foreach ($requiredHeaders as $requiredHeader) {
            if (! in_array($requiredHeader, $headers, true)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Missing required column: ' . ucwords(str_replace('_', ' ', $requiredHeader)),
                ], 422);
            }
        }

        $rows = array_slice($rows, $headerRowNumber + 1, null, true);

        $imported = 0;
        $updated = 0;
        $customersCreated = 0;
        $customersUpdated = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                $lineNumber = $index + 1;
                $mapped = $this->mapSalesImportRow($row, $headers);

                if ($this->isEmptyImportRow($mapped)) {
                    continue;
                }

                $partyName = trim((string) ($mapped['party_name'] ?? ''));
                $docNo = trim((string) ($mapped['doc_no'] ?? ''));

                if ($partyName === '') {
                    $skipped++;
                    $errors[] = "Row {$lineNumber}: Party Name or Customer Name is required.";
                    continue;
                }

                $hasOrderData = $docNo !== ''
                    || trim((string) ($mapped['doc_date'] ?? '')) !== ''
                    || trim((string) ($mapped['doc_value'] ?? '')) !== '';

                $customerResult = $this->upsertImportCustomer($mapped, $branchId, $authUser, $hasOrderData);
                $customer = $customerResult['customer'];

                if ($customerResult['created']) {
                    $customersCreated++;
                } elseif ($customerResult['updated']) {
                    $customersUpdated++;
                }

                if (! $hasOrderData) {
                    continue;
                }

                if ($docNo === '') {
                    $skipped++;
                    $errors[] = "Row {$lineNumber}: Doc No is required for order import.";
                    continue;
                }

                $docDate = $this->parseImportDate($mapped['doc_date'] ?? null);
                if (! $docDate) {
                    $skipped++;
                    $errors[] = "Row {$lineNumber}: Invalid Doc.Date.";
                    continue;
                }

                $docValue = $this->parseImportAmount($mapped['doc_value'] ?? 0);
                $statusRaw = trim((string) ($mapped['status'] ?? ''));
                $doStatusRaw = trim((string) ($mapped['do_status'] ?? ''));
                $paymentStatus = $this->mapImportPaymentStatus($statusRaw);
                $deliveryStatus = $this->mapImportDeliveryStatus($doStatusRaw);

                $orderData = [
                    'user_id' => $customer->id,
                    'import_sn' => $this->parseImportInteger($mapped['sn'] ?? null),
                    'import_party_name' => $partyName,
                    'import_doc_no' => $docNo,
                    'import_doc_date' => $docDate->toDateString(),
                    'import_doc_value' => $docValue,
                    'import_financier' => trim((string) ($mapped['financier'] ?? '')) ?: null,
                    'import_status' => $statusRaw ?: null,
                    'import_do_status' => $doStatusRaw ?: null,
                    'import_source' => 'sales_list_import',
                    'total_amount' => $docValue,
                    'remaining_amount' => $paymentStatus === 'completed' ? 0 : $docValue,
                    'payment_status' => $paymentStatus,
                    'delivery_status' => $deliveryStatus,
                    'payment_method' => $paymentStatus === 'completed' ? 'cash' : 'pending',
                    'quotation_status' => 'sales',
                    'gst_option' => 'with_gst',
                    'discount' => 0,
                    'shipping' => 0,
                    'tds_percentage' => 0,
                    'tds_amount' => 0,
                    'branch_id' => $branchId,
                    'created_by' => $authUser->id,
                    'order_type' => 'self_pickup',
                    'created_at' => $docDate->copy()->setTime(0, 0, 0),
                    'updated_at' => now('Asia/Kolkata'),
                ];

                $existingOrder = Order::where('order_number', $docNo)->first();
                if ($existingOrder) {
                    $existingOrder->update($orderData);
                    $order = $existingOrder;
                    $updated++;
                } else {
                    $order = Order::create(array_merge(['order_number' => $docNo], $orderData));
                    $imported++;
                }

                // Handle line item if present in the import row
                $itemCode = trim((string) ($mapped['item_model_code'] ?? ''));
                $itemName = trim((string) ($mapped['item_model'] ?? ''));
                $qty = $this->parseImportAmount($mapped['qty'] ?? ($mapped['quantity'] ?? 0));
                $rate = $this->parseImportAmount($mapped['rate'] ?? 0);
                $lineAmount = $this->parseImportAmount($mapped['line_amount'] ?? ($mapped['amount'] ?? 0));

                if ($itemCode !== '' || $itemName !== '' || $qty > 0 || $rate > 0 || $lineAmount > 0) {
                    $product = null;

                    if ($itemCode !== '') {
                        // Prefer SKU or barcode; only query product_code if column exists
                        $q = Product::query();
                        $q->where('SKU', $itemCode);
                        if (Schema::hasColumn('products', 'barcode')) {
                            $q->orWhere('barcode', $itemCode);
                        }
                        if (Schema::hasColumn('products', 'product_code')) {
                            $q->orWhere('product_code', $itemCode);
                        }
                        $product = $q->first();
                    }

                    if (! $product && $itemName !== '') {
                        $product = Product::where('name', $itemName)->first();
                    }

                    if (! $product) {
                        $createData = [
                            'name' => $itemName ?: ($itemCode ?: null),
                            'price' => $rate ?: null,
                            'quantity' => $qty ?: null,
                            'branch_id' => $branchId,
                            'create_by' => $authUser->id,
                        ];

                        if ($itemCode !== '') {
                            // store code in SKU if product_code missing
                            if (Schema::hasColumn('products', 'SKU')) {
                                $createData['SKU'] = $itemCode;
                            } elseif (Schema::hasColumn('products', 'barcode')) {
                                $createData['barcode'] = $itemCode;
                            } elseif (Schema::hasColumn('products', 'product_code')) {
                                $createData['product_code'] = $itemCode;
                            }
                        }

                        $product = Product::create($createData);
                    }

                    $discountAmount = (float) ($item['discount_amount'] ?? 0);
                    $subTotal = $rate * $qty;
                    $inclusiveGst = $this->inclusiveGstDetails((float) $subTotal, $product);
                    $itemGstAmount = $inclusiveGst['total'];

                    $inclusiveLineTotal = $subTotal + $itemGstAmount;
                    $finalAmount = max(0, $inclusiveLineTotal - $discountAmount);

                    // Create order item
                    OrderItem::create([
                        'order_id' => $order->id,
                        'user_id' => $customer->id,
                        'product_id' => $product->id ?? null,
                        'price' => $rate ?: null,
                        'quantity' => $qty ?: null,
                        'product_gst_details' => ! empty($inclusiveGst['details']) ? json_encode($inclusiveGst['details']) : null,
                        'product_gst_total' => $inclusiveGst['total'],
                        'total_amount' => $inclusiveLineAmount,
                        'branch_id' => $branchId,
                        'created_by' => $authUser->id,
                    ]);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Sales list import failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Import failed. Please check the file and try again.',
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Sales list imported successfully.',
            'imported' => $imported,
            'updated' => $updated,
            'customers_created' => $customersCreated,
            'customers_updated' => $customersUpdated,
            'skipped' => $skipped,
            'errors' => $errors,
        ]);
    }

    private function upsertImportCustomer(array $mapped, int $branchId, User $authUser, bool $hasOrderData): array
    {
        $partyName = $this->stringImportValue($mapped, 'party_name');
        $membershipNo = $this->stringImportValue($mapped, 'membership_no');
        $phone1 = $this->normalizeImportPhone($this->stringImportValue($mapped, 'phone_1'));
        $phone2 = $this->normalizeImportPhone($this->stringImportValue($mapped, 'phone_2'));
        $email = $this->normalizeImportEmail($this->stringImportValue($mapped, 'email'));

        $customer = $this->findImportCustomer($branchId, $partyName, $membershipNo, $phone1, $phone2, $email);
        $created = false;

        if (! $customer) {
            $customer = new User([
                'role' => 'customer',
                'branch_id' => $branchId,
                'created_by' => $authUser->id,
                'status' => 1,
            ]);
            $created = true;
        }

        if ($partyName !== '') {
            $customer->name = $partyName;
        }

        if ($membershipNo !== '') {
            $customer->membership_no = $membershipNo;
        }

        if ($this->filledImportValue($mapped, 'customer_since')) {
            $customerSince = $this->parseImportDate($mapped['customer_since']);
            if ($customerSince) {
                $customer->customer_since = $customerSince->toDateString();
            }
        }

        if ($phone1 !== '') {
            $customer->phone = $phone1;
        } elseif ($phone2 !== '') {
            $customer->phone = $phone2;
        }

        if ($phone2 !== '' && $phone2 !== $customer->phone) {
            $customer->alternate_phone = $phone2;
        }

        if ($email !== '' && $this->canUseUserValue('email', $email, $customer->id)) {
            $customer->email = $email;
        }

        $this->assignImportString($customer, 'state_name', $mapped, 'state');
        $this->assignImportString($customer, 'customer_category', $mapped, 'customer_category');
        $this->assignImportString($customer, 'pan_number', $mapped, 'pan_number', true);
        $this->assignImportString($customer, 'tin_number', $mapped, 'tin_number', true);
        $this->assignImportString($customer, 'gst_number', $mapped, 'gst_number', true);
        $this->assignImportString($customer, 'gstin_status', $mapped, 'gstin_status');
        $this->assignImportString($customer, 'gender', $mapped, 'gender');

        if ($this->filledImportValue($mapped, 'age')) {
            $age = $this->parseImportInteger($mapped['age']);
            if ($age !== null) {
                $customer->age = $age;
            }
        }

        foreach (['dob', 'doa'] as $dateField) {
            if ($this->filledImportValue($mapped, $dateField)) {
                $dateValue = $this->parseImportDate($mapped[$dateField]);
                if ($dateValue) {
                    $customer->{$dateField} = $dateValue->toDateString();
                }
            }
        }

        if (! $hasOrderData && $this->filledImportValue($mapped, 'status')) {
            $customer->status = $this->mapImportCustomerStatus((string) $mapped['status']);
        }

        $wasDirty = $customer->isDirty();
        $customer->save();

        $detail = UserDetail::firstOrNew(['user_id' => $customer->id]);
        $this->assignImportString($detail, 'address', $mapped, 'address_line1');
        $this->assignImportString($detail, 'address_line2', $mapped, 'address_line2');
        $this->assignImportString($detail, 'address_line3', $mapped, 'address_line3');
        $this->assignImportString($detail, 'pin_code', $mapped, 'pin_code');
        $this->assignImportString($detail, 'city', $mapped, 'city');

        $detailWasDirty = $detail->isDirty();
        $detail->save();

        return [
            'customer' => $customer,
            'created' => $created,
            'updated' => ! $created && ($wasDirty || $detailWasDirty),
        ];
    }

    private function findImportCustomer(
        int $branchId,
        string $partyName,
        string $membershipNo,
        string $phone1,
        string $phone2,
        string $email
    ): ?User {
        $baseQuery = User::where('role', 'customer')
            ->where('branch_id', $branchId)
            ->where('isDeleted', 0);

        if ($partyName !== '') {
            $customer = (clone $baseQuery)
                ->whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($partyName))])
                ->first();
            if ($customer) {
                return $customer;
            }
        }

        if ($membershipNo !== '') {
            $customer = (clone $baseQuery)->where('membership_no', $membershipNo)->first();
            if ($customer) {
                return $customer;
            }
        }

        if ($email !== '') {
            $customer = (clone $baseQuery)->where('email', $email)->first();
            if ($customer) {
                return $customer;
            }
        }

        if ($phone1 !== '') {
            $customer = (clone $baseQuery)->where('phone', $phone1)->first();
            if ($customer) {
                return $customer;
            }
        }

        if ($phone2 !== '') {
            $customer = (clone $baseQuery)
                ->where(function ($query) use ($phone2) {
                    $query->where('phone', $phone2)
                        ->orWhere('alternate_phone', $phone2);
                })
                ->first();

            if ($customer) {
                return $customer;
            }
        }

        return null;
    }

    private function filledImportValue(array $mapped, string $field): bool
    {
        return array_key_exists($field, $mapped) && trim((string) $mapped[$field]) !== '';
    }

    private function stringImportValue(array $mapped, string $field): string
    {
        return $this->filledImportValue($mapped, $field)
            ? trim((string) $mapped[$field])
            : '';
    }

    private function assignImportString($model, string $modelField, array $mapped, string $importField, bool $uppercase = false): void
    {
        if (! $this->filledImportValue($mapped, $importField)) {
            return;
        }

        $value = trim((string) $mapped[$importField]);
        $model->{$modelField} = $uppercase ? strtoupper($value) : $value;
    }

    private function normalizeImportPhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        if (strlen((string) $digits) > 10 && str_starts_with((string) $digits, '91')) {
            $digits = substr((string) $digits, -10);
        }

        return (string) $digits;
    }

    private function normalizeImportEmail(string $email): string
    {
        $email = strtolower(trim($email));

        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';
    }

    private function canUseUserValue(string $column, string $value, ?int $currentUserId): bool
    {
        if ($value === '') {
            return false;
        }

        return ! User::where($column, $value)
            ->when($currentUserId, fn ($query) => $query->where('id', '!=', $currentUserId))
            ->exists();
    }

    private function mapImportCustomerStatus(string $status): int
    {
        $status = strtolower(trim($status));

        return in_array($status, ['inactive', 'disabled', 'blocked', 'closed', '0', 'no'], true) ? 0 : 1;
    }

    private function resolveImportBranchId(User $user, $selectedSubAdminId): int
    {
        if ($user->role === 'staff' && ! empty($user->branch_id)) {
            return (int) $user->branch_id;
        }

        if ($user->role === 'admin' && ! empty($selectedSubAdminId)) {
            return (int) $selectedSubAdminId;
        }

        return (int) ($user->branch_id ?: $user->id);
    }

    private function normalizeImportHeader(string $header): string
    {
        $rawHeader = strtolower(trim($header));
        if (preg_match('/^phone\s*#\s*$/i', $rawHeader) || preg_match('/^mobile\s*#\s*$/i', $rawHeader)) {
            return 'phone_2';
        }

        $header = $rawHeader;
        $header = preg_replace('/[^a-z0-9]+/', '_', $header);
        $header = trim((string) $header, '_');

        return match ($header) {
            'sn', 's_no', 'sr_no', 'serial_no' => 'sn',
            'party_name', 'customer', 'customer_name' => 'party_name',
            'membership', 'membership_no', 'membership_number', 'membership_id' => 'membership_no',
            'customer_since', 'since' => 'customer_since',
            'address_line1', 'address_line_1', 'address1', 'address_1' => 'address_line1',
            'address_line2', 'address_line_2', 'address2', 'address_2' => 'address_line2',
            'address_line3', 'address_line_3', 'address3', 'address_3' => 'address_line3',
            'pin_code', 'pincode', 'pin' => 'pin_code',
            'city_town', 'city', 'town' => 'city',
            'state', 'state_name' => 'state',
            'phone', 'phoneno', 'phone_no', 'phone_number', 'phone_number_1', 'mobile', 'mobileno', 'mobile_no', 'mobile_number', 'contact', 'contact_no', 'contact_number', 'tel', 'tel_no', 'telephone', 'telephone_no', 'phone_1', 'phone1', 'phone_no_1', 'mobile_1', 'mobile1', 'mobile_no_1', 'mobile_number_1' => 'phone_1',
            'phone_2', 'phone2', 'phone_no_2', 'phone_number_2', 'mobile_2', 'mobile2', 'alternate_phone', 'alternate_mobile', 'alt_phone', 'alt_mobile' => 'phone_2',
            'email', 'email_id', 'email_address' => 'email',
            'category', 'customer_category' => 'customer_category',
            'age' => 'age',
            'dob', 'date_of_birth' => 'dob',
            'doa', 'date_of_anniversary', 'anniversary_date' => 'doa',
            'pan', 'pan_no', 'pan_number' => 'pan_number',
            'tin', 'tin_no', 'tin_number' => 'tin_number',
            'gstin_status', 'gst_status' => 'gstin_status',
            'gstin', 'gst_number', 'gst_no' => 'gst_number',
            'gender' => 'gender',
            'doc_no', 'doc_number', 'document_no', 'document_number' => 'doc_no',
            'doc_date', 'docdate', 'document_date' => 'doc_date',
            'doc_value', 'docvalue', 'document_value', 'amount', 'total' => 'doc_value',
            'financier', 'finance' => 'financier',
            'supplier', 'vendor' => 'supplier',
            'ref_doc_no', 'ref.doc.no', 'ref_doc', 'ref_document_no', 'ref_document_number' => 'ref_doc_no',
            'ref_doc_date', 'ref.document.date', 'ref_document_date' => 'ref_doc_date',
            'item_model_code', 'item_code', 'model_code', 'product_code', 'sku' => 'item_model_code',
            'item_model', 'item', 'product', 'model' => 'item_model',
            'qty', 'quantity' => 'qty',
            'rate', 'price_per_unit', 'unit_price' => 'rate',
            'line_amount', 'line_total', 'item_amount', 'amount' => 'line_amount',
            'status' => 'status',
            'do_status', 'delivery_order_status', 'delivery_status' => 'do_status',
            default => $header,
        };
    }

    private function mapSalesImportRow(array $row, array $headers): array
    {
        $mapped = [];

        foreach ($headers as $column => $field) {
            $value = $row[$column] ?? null;

            if (array_key_exists($field, $mapped) && trim((string) $mapped[$field]) !== '' && trim((string) $value) === '') {
                continue;
            }

            if (array_key_exists($field, $mapped) && trim((string) $mapped[$field]) !== '' && trim((string) $value) !== '' && $field === 'phone_1') {
                $mapped['phone_2'] = $value;
                continue;
            }

            $mapped[$field] = $value;
        }

        return $mapped;
    }

    private function isEmptyImportRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function parseImportDate($value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $value))->timezone('Asia/Kolkata');
            } catch (\Throwable) {
                return null;
            }
        }

        $value = trim((string) $value);
        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d', 'm/d/Y', 'm-d-Y'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value, 'Asia/Kolkata');
            } catch (\Throwable) {
                continue;
            }
        }

        try {
            return Carbon::parse($value, 'Asia/Kolkata');
        } catch (\Throwable) {
            return null;
        }
    }

    private function parseImportAmount($value): float
    {
        $value = preg_replace('/[^0-9.\-]/', '', (string) $value);
        return round((float) ($value !== '' ? $value : 0), 2);
    }

    private function parseImportInteger($value): ?int
    {
        $value = preg_replace('/[^0-9]/', '', (string) $value);
        return $value !== '' ? (int) $value : null;
    }

    private function mapImportPaymentStatus(string $status): string
    {
        $status = strtolower(trim($status));

        return match ($status) {
            'closed', 'complete', 'completed', 'paid' => 'completed',
            'partial', 'partially', 'partially paid' => 'partially',
            default => 'pending',
        };
    }

    private function mapImportDeliveryStatus(string $status): string
    {
        $status = strtolower(trim($status));

        return match ($status) {
            '', 'none', 'no', 'n/a', 'na' => 'none',
            'delivered', 'complete', 'completed', 'closed' => 'delivered',
            default => $status,
        };
    }

    private function getTdsOrderReportSummary($query): array
    {
        $summary = (clone $query)
            ->selectRaw('COUNT(*) as total_orders')
            ->selectRaw('SUM(COALESCE(total_amount, 0)) as total_order_amount')
            ->selectRaw('SUM(COALESCE(tds_amount, 0)) as total_tds_amount')
            ->selectRaw('AVG(CASE WHEN COALESCE(tds_percentage, 0) > 0 THEN tds_percentage END) as average_tds_percentage')
            ->first();

        return [
            'total_orders' => (int) ($summary->total_orders ?? 0),
            'total_order_amount' => round((float) ($summary->total_order_amount ?? 0), 2),
            'total_tds_amount' => round((float) ($summary->total_tds_amount ?? 0), 2),
            'average_tds_percentage' => round((float) ($summary->average_tds_percentage ?? 0), 2),
        ];
    }

    public function getAvailableSerials($productId)
    {
        try {
            $purchases = \App\Models\Purchases::where('item', $productId)
                ->whereNotNull('imei_no')
                ->where('imei_no', '!=', '')
                ->get(['imei_no']);

            $allSerials = [];
            foreach ($purchases as $p) {
                $serials = json_decode($p->imei_no, true);
                if (is_array($serials)) {
                    $allSerials = array_merge($allSerials, $serials);
                }
            }

            $soldSerials = \App\Models\OrderItem::where('product_id', $productId)
                ->whereNotNull('imei_no')
                ->where('imei_no', '!=', '')
                ->pluck('imei_no')
                ->toArray();

            $availableSerials = array_values(array_diff($allSerials, $soldSerials));

            return response()->json([
                'status' => true,
                'serials' => $availableSerials
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch serial numbers.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function uploadQrCode(Request $request, $id)
    {
        $request->validate([
            'qr_code_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $order = Order::find($id);
        if (!$order) {
            return response()->json(['status' => false, 'message' => 'Order not found.'], 404);
        }

        if ($request->hasFile('qr_code_image')) {
            $file = $request->file('qr_code_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/qr_codes', $filename);

            $order->custom_qr_code = $filename;
            $order->save();

            return response()->json([
                'status' => true,
                'message' => 'QR Code uploaded successfully.',
                'file_name' => $filename,
                'file_url' => asset('storage/qr_codes/' . $filename)
            ]);
        }

        return response()->json(['status' => false, 'message' => 'No file uploaded.'], 400);
    }

    /**
     * Customer deposit / exchange surplus balance for POS.
     */
    public function customerDepositBalance(Request $request, $customerId)
    {
        $customerId = (int) $customerId;
        if ($customerId <= 0) {
            return response()->json(['status' => false, 'balance' => 0, 'message' => 'Invalid customer'], 422);
        }

        $customer = User::where('id', $customerId)->where('role', 'customer')->first();
        if (!$customer) {
            return response()->json(['status' => false, 'balance' => 0, 'message' => 'Customer not found'], 404);
        }

        $branchId = $request->filled('branch_id')
            ? (int) $request->branch_id
            : (int) (Auth::user()->branch_id ?? $customer->branch_id ?? 0);

        $balance = CustomerDeposit::balanceFor($customerId, $branchId > 0 ? $branchId : null);

        return response()->json([
            'status' => true,
            'customer_id' => $customerId,
            'balance' => $balance,
        ]);
    }
}
