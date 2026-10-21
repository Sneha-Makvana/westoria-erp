<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancePayment;
use App\Models\Attendance;
use App\Models\DepartmentModel;
use App\Models\DesignationModel;
use App\Models\Expense;
use App\Models\FollowUp;
use App\Models\Lead;
use App\Models\Meeting;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Purchases;
use App\Models\PurchaseInvoice;
use App\Models\Salary;
use App\Models\User;
use App\Services\StaffDepartmentScope;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function signin()
    {

        return view('signin');
    }
    public function forgetpassword()
    {

        return view('forgetpassword');
    }
    public function subadmin_session(Request $request)
    {
        // dd($request->all());
        $subAdminId = $request->input('subAdminId');
        session(['selectedSubAdminId' => $subAdminId]);
        return response()->json(['status' => 'success']);
    }

    public function dashboard()
    {
        $user = auth()->user();

        if ($user->role === 'staff') {
            return redirect()->route('auth.staff-dashboard');
        }

        $selectedSubAdminId = session('selectedSubAdminId');

        // ✅ Decide BranchID based on role/session
        if (! empty($selectedSubAdminId)) {
            $BranchID = $selectedSubAdminId;
        } elseif ($user->role === 'staff' && $user->branch_id) {
            $BranchID = $user->branch_id;
        } else {
            $BranchID = $user->id;
        }

        $currentYear  = Carbon::now()->year;
        $previousYear = $currentYear - 1;
        $currentMonth = Carbon::now()->month;

        // ✅ Charts
        $salesChartThisMonth    = $this->getSalesDataByMonth($currentMonth, $BranchID);
        $purchaseChartThisMonth = $this->getPurchaseDataByMonth($currentMonth, $BranchID);

        $salesChartthisyear     = $this->getSalesDataByYear($currentYear, $BranchID);
        $salesChartpreviousyear = $this->getSalesDataByYear($previousYear, $BranchID);

        $purchaseChartthisyear     = $this->getPurchaseDataByYear($currentYear, $BranchID);
        $purchaseChartpreviousyear = $this->getPurchaseDataByYear($previousYear, $BranchID);

        // ✅ Totals (branch-wise)
        $totalPurchaseAmount = $this->getPurchaseListTotalAmount($user, $BranchID);

        $totalSalesAmount = Order::where('isDeleted', '!=', 1)
            ->where('branch_id', $BranchID)
            ->sum('total_amount');

        $totalExpenseAmount = Expense::where('isDeleted', '!=', 1)
            ->where('branch_id', $BranchID)
            ->sum('amount');

        // ✅ Counts
        $customerCount = User::where('role', 'customer')
            ->where('isDeleted', '!=', 1)
            ->where('branch_id', $BranchID)
            ->count();

        $vendorCount = User::where('role', 'vendor')
            ->where('isDeleted', '!=', 1)
            ->where('branch_id', $BranchID)
            ->count();

        $purchaseInvoiceCount = PurchaseInvoice::where('isDeleted', '!=', 1)
            ->where('branch_id', $BranchID)
            ->count();

        $salesInvoiceCount = Order::where('isDeleted', '!=', 1)
            ->where('branch_id', $BranchID)
            ->count();

        $totalQuotationsCount = Order::where('isDeleted', '!=', 1)
            ->where('branch_id', $BranchID)
            ->where('quotation_status', 'quotation')
            ->count();
        $rentalProductsCount = Product::where('isDeleted', 0)
            ->where('branch_id', $BranchID)
            ->where('is_rent_available', 1)
            ->count();
        $wonQuotationsCount = Order::where('isDeleted', '!=', 1)
            ->where('branch_id', $BranchID)
            ->where('quotation_status', 'quotation')
            ->where('approved_status', 'approved')
            ->count();

        $lostQuotationsCount = Order::where('isDeleted', '!=', 1)
            ->where('branch_id', $BranchID)
            ->where('quotation_status', 'quotation')
            ->where('approved_status', 'rejected')
            ->count();

        // ✅ Recent products (branch-wise)
        $recentProducts = Product::where('isDeleted', '!=', 1)
            ->where('branch_id', $BranchID)
            ->latest()
            ->take(4)
            ->get();

        // Get 4 latest orders, each with ONE order item + its product
        $latestSalesOrders = Order::with('user')
            ->where('isDeleted', '!=', 1)
            ->where('branch_id', $BranchID)
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();

        $salesOrderIds = $latestSalesOrders->pluck('id');

        // Fetch ONE order_item per order (lowest id), with product
        $salesFirstItems = OrderItem::with('product')
            ->whereIn('order_id', $salesOrderIds)
            ->where('isDeleted', '!=', 1)
            ->orderBy('id')
            ->get()
            ->groupBy('order_id')
            ->map(fn($items) => $items->first());

        $latestSales = $latestSalesOrders->map(function ($order) use ($salesFirstItems) {
            $firstItem = $salesFirstItems->get($order->id);
            $product   = $firstItem?->product;
            return (object) [
                'order_id'       => $order->id,
                'order_number'   => $order->order_number,
                'order_date'     => $order->created_at,
                'total_amount'   => $order->total_amount,
                'payment_method' => $order->payment_method,
                'payment_status' => $order->payment_status,
                'customer_name'  => $order->user?->name,
                'product_id'     => $product?->id,
                'product_name'   => $product?->name,
                'product_code'   => $product?->SKU,
                'images'         => $product?->images,
            ];
        });

        // Dashboard Extra Sales Stats
        $salesOrdersQuery = DB::table('orders')
            ->where('isDeleted', '!=', 1)
            ->where('branch_id', $BranchID)
            ->where('quotation_status', '!=', 'quotation')
            ->where('quotation_status', '!=', 'advance_receipt');

        $totalOrdersCount = (clone $salesOrdersQuery)->count();
        $ordersThisMonthCount = (clone $salesOrdersQuery)
            ->whereMonth('created_at', Carbon::now('Asia/Kolkata')->month)
            ->whereYear('created_at', Carbon::now('Asia/Kolkata')->year)
            ->count();
        $completedOrdersCount = (clone $salesOrdersQuery)
            ->where('payment_status', 'completed')
            ->count();
        $partialOrdersCount = (clone $salesOrdersQuery)
            ->where('payment_status', 'partially')
            ->count();
        $pendingOrdersCount = (clone $salesOrdersQuery)
            ->where('payment_status', 'pending')
            ->count();

        // Dashboard Extra Purchases Stats
        // Dashboard purchases stats — derived from purchases.payment_status (same source as purchase list)
        $purchasesCountQuery = DB::table('purchase_invoice')
            ->leftJoin('purchases', function ($join) use ($BranchID) {
                $join->on('purchase_invoice.id', '=', 'purchases.invoice_id')
                    ->where('purchases.isDeleted', '=', 0)
                    ->where('purchases.branch_id', '=', $BranchID);
            })
            ->where('purchase_invoice.isDeleted', '=', 0)
            ->where('purchase_invoice.branch_id', '=', $BranchID)
            ->groupBy('purchase_invoice.id', 'purchase_invoice.created_at')
            ->select(
                'purchase_invoice.id',
                'purchase_invoice.created_at',
                DB::raw("MAX(purchases.payment_status) as payment_status")
            );

        $nowMonth = Carbon::now('Asia/Kolkata')->month;
        $nowYear  = Carbon::now('Asia/Kolkata')->year;

        $allPurchaseCounts = DB::query()->fromSub($purchasesCountQuery, 'pi_summary')
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN payment_status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN payment_status = 'partially' THEN 1 ELSE 0 END) as partially,
                SUM(CASE WHEN payment_status = 'pending'   THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN MONTH(created_at) = {$nowMonth} AND YEAR(created_at) = {$nowYear} THEN 1 ELSE 0 END) as this_month
            ")
            ->first();

        $totalPurchasesCount      = (int) ($allPurchaseCounts->total      ?? 0);
        $purchasesThisMonthCount  = (int) ($allPurchaseCounts->this_month ?? 0);
        $completedPurchasesCount  = (int) ($allPurchaseCounts->completed  ?? 0);
        $partialPurchasesCount    = (int) ($allPurchaseCounts->partially  ?? 0);
        $pendingPurchasesCount    = (int) ($allPurchaseCounts->pending    ?? 0);

        // Get 4 latest purchase invoices, each with ONE purchase item + its product
        $latestPurchaseInvoices = PurchaseInvoice::with('vendor')
            ->where('isDeleted', '!=', 1)
            ->where('branch_id', $BranchID)
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();

        $purchaseInvoiceIds = $latestPurchaseInvoices->pluck('id');

        // Fetch ONE purchase item per invoice (lowest id), with product
        $purchaseFirstItems = Purchases::with('product')
            ->whereIn('invoice_id', $purchaseInvoiceIds)
            ->where('isDeleted', '!=', 1)
            ->orderBy('id')
            ->get()
            ->groupBy('invoice_id')
            ->map(fn($items) => $items->first());

        $latestPurchases = $latestPurchaseInvoices->map(function ($invoice) use ($purchaseFirstItems) {
            $firstPurchase = $purchaseFirstItems->get($invoice->id);
            $product       = $firstPurchase?->product;
            return (object) [
                'invoice_id'     => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'bill_no'        => $invoice->bill_no,
                'grand_total'    => $invoice->grand_total,
                'purchase_date'  => $invoice->purchase_date,
                'vendor_name'    => $invoice->vendor?->name,
                'product_id'     => $product?->id,
                'product_name'   => $product?->name,
                'product_code'   => $product?->SKU,
                'images'         => $product?->images,
            ];
        });
        $latestRentalOrders = Order::with('user')
            ->where('isDeleted', '!=', 1)
            ->where('branch_id', $BranchID)
            ->where('rental_status', 'rental')
            ->latest()
            ->take(4)
            ->get();

        // Latest Rental Orders
        $latestRentalOrders = Order::with('user')
            ->where('isDeleted', '!=', 1)
            ->where('branch_id', $BranchID)
            ->where('rental_status', 'rental')
            ->latest()
            ->take(4)
            ->get();

        $rentalOrderIds = $latestRentalOrders->pluck('id');

        $rentalFirstItems = OrderItem::with('product')
            ->whereIn('order_id', $rentalOrderIds)
            ->where('isDeleted', '!=', 1)
            ->orderBy('id')
            ->get()
            ->groupBy('order_id')
            ->map(function ($items) {
                return $items->first();
            });

        $latestRentals = $latestRentalOrders->map(function ($order) use ($rentalFirstItems) {

            $firstItem = $rentalFirstItems->get($order->id);
            $product = $firstItem?->product;

            return (object)[
                'order_number' => $order->order_number,
                'order_date'   => $order->created_at,
                'total_amount' => $order->total_amount,
                'product_name' => $product?->name,
                'images'       => $product?->images,
                'customer_name'=> $order->user?->name,
            ];
        });
        // ✅ Monthly Sales Chart
        $salesData = DB::table('order_items')
            ->join('orders', function ($join) use ($BranchID) {
                $join->on('order_items.order_id', '=', 'orders.id')
                    ->where('orders.isDeleted', '!=', 1)
                    ->where('orders.branch_id', '=', $BranchID);
            })
            ->where('order_items.isDeleted', '!=', 1)
            ->select(DB::raw("MONTH(order_items.created_at) as month"), DB::raw("SUM(order_items.total_amount) as total"))
            ->groupBy(DB::raw("MONTH(order_items.created_at)"))
            ->pluck('total', 'month')
            ->toArray();

        // ✅ Monthly Purchase Chart
        $purchasesData = DB::table('purchases')
            ->leftJoin('purchase_invoice', function ($join) use ($BranchID) {
                $join->on('purchase_invoice.id', '=', 'purchases.invoice_id')
                    ->where('purchase_invoice.isDeleted', '!=', 1)
                    ->where('purchase_invoice.branch_id', '=', $BranchID);
            })
            ->where('purchases.isDeleted', '!=', 1)
            ->select(DB::raw("MONTH(purchases.created_at) as month"), DB::raw("SUM(purchases.amount_total) as total"))
            ->groupBy(DB::raw("MONTH(purchases.created_at)"))
            ->pluck('total', 'month')
            ->toArray();

        $salesChart    = [];
        $purchaseChart = [];
        for ($m = 1; $m <= 12; $m++) {
            $salesChart[]    = (float) ($salesData[$m] ?? 0);
            $purchaseChart[] = (float) ($purchasesData[$m] ?? 0);
        }

        // ✅ Branch-specific settings
        $settings         = DB::table('settings')->where('branch_id', $BranchID)->first();
        $currencySymbol   = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';
        $now              = Carbon::now('Asia/Kolkata');
        $today            = $now->toDateString();
        $showCrmDashboardSection = (bool) ($settings->show_crm_dashboard ?? true);
        $showHrDashboardSection = (bool) ($settings->show_hr_dashboard ?? true);

        // CRM subsection visibility settings
        $showCrmLeadPipeline = (bool) ($settings->show_crm_lead_pipeline ?? true);
        $showCrmConversion = (bool) ($settings->show_crm_conversion ?? true);
        $showCrmFollowupLoad = (bool) ($settings->show_crm_followup_load ?? true);
        $showCrmMeetingMomentum = (bool) ($settings->show_crm_meeting_momentum ?? true);
        $showCrmLeadStatusMix = (bool) ($settings->show_crm_lead_status_mix ?? true);
        $showCrmActivityTrend = (bool) ($settings->show_crm_activity_trend ?? true);
        $showCrmPipelineQuality = (bool) ($settings->show_crm_pipeline_quality ?? true);
        $showCrmRecentLeads = (bool) ($settings->show_crm_recent_leads ?? true);
        $showCrmNext7Days = (bool) ($settings->show_crm_next_7_days ?? true);

        // HR subsection visibility settings
        $showHrStaffStrength = (bool) ($settings->show_hr_staff_strength ?? true);
        $showHrActiveStaff = (bool) ($settings->show_hr_active_staff ?? true);
        $showHrMonthlyAttendance = (bool) ($settings->show_hr_monthly_attendance ?? true);
        $showHrPersonalProgress = (bool) ($settings->show_hr_personal_progress ?? true);
        $showHrAttendancePattern = (bool) ($settings->show_hr_attendance_pattern ?? true);
        $showHrSalaryPayrollTrend = (bool) ($settings->show_hr_salary_payroll_trend ?? true);
        $showHrPayrollSnapshot = (bool) ($settings->show_hr_payroll_snapshot ?? true);
        $showHrAttendanceWatch = (bool) ($settings->show_hr_attendance_watch ?? true);
        $showHrPayrollStatus = (bool) ($settings->show_hr_payroll_status ?? true);

        // ERP subsection visibility settings
        $showErpDashboard = (bool) ($settings->show_erp_dashboard ?? true);
        $showErpTotalSales = (bool) ($settings->show_erp_total_sales ?? true);
        $showErpTotalPurchase = (bool) ($settings->show_erp_total_purchase ?? true);
        $showErpTotalExpense = (bool) ($settings->show_erp_total_expense ?? true);
        $showErpSalesInvoiceCount = (bool) ($settings->show_erp_sales_invoice_count ?? true);
        $showErpPurchaseInvoiceCount = (bool) ($settings->show_erp_purchase_invoice_count ?? true);
        $showErpCustomersCount = (bool) ($settings->show_erp_customers_count ?? true);
        $showErpVendorsCount = (bool) ($settings->show_erp_vendors_count ?? true);
        $showErpSalesChart = (bool) ($settings->show_erp_sales_chart ?? true);
        $showErpPurchaseChart = (bool) ($settings->show_erp_purchase_chart ?? true);
        $showErpRecentSales = (bool) ($settings->show_erp_recent_sales ?? true);
        $showErpRecentPurchases = (bool) ($settings->show_erp_recent_purchases ?? true);
        $showErpRecentProducts = (bool) ($settings->show_erp_recent_products ?? true);
        $showErpProductsDelivery = (bool) ($settings->show_erp_products_delivery ?? true);
        $showErpRecentRentals = (bool) ($settings->show_erp_recent_rentals ?? true);

        $leadQuery = Lead::active()->where('branch_id', $BranchID);

        $totalLeads = (clone $leadQuery)->count();
        $newLeadsThisMonth = (clone $leadQuery)
            ->whereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
            ->count();
        $convertedLeadCount = (clone $leadQuery)
            ->where(function ($query) {
                $query->whereNotNull('converted_customer_id')
                    ->orWhere('lead_status', 'Converted');
            })
            ->count();
        $crmConversionRate = $totalLeads > 0 ? round(($convertedLeadCount / $totalLeads) * 100, 1) : 0;

        $pendingFollowUps = FollowUp::active()
            ->where('branch_id', $BranchID)
            ->whereIn('status', ['Pending', 'Rescheduled'])
            ->count();
        $overdueFollowUps = FollowUp::active()
            ->where('branch_id', $BranchID)
            ->where('follow_up_datetime', '<', $now)
            ->whereNotIn('status', ['Completed', 'Cancelled'])
            ->count();
        $meetingsThisWeek = Meeting::active()
            ->where('branch_id', $BranchID)
            ->whereBetween('scheduled_on', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])
            ->count();
        $completedMeetingsThisMonth = Meeting::active()
            ->where('branch_id', $BranchID)
            ->where('status', Meeting::STATUS_COMPLETED)
            ->whereBetween('scheduled_on', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
            ->count();

        $leadStatusRows = (clone $leadQuery)
            ->select('lead_status', DB::raw('COUNT(*) as total'))
            ->groupBy('lead_status')
            ->orderByDesc('total')
            ->get();
        $leadStatusLabels = $leadStatusRows->pluck('lead_status')->map(fn($status) => $status ?: 'Unspecified')->values();
        $leadStatusCounts = $leadStatusRows->pluck('total')->map(fn($value) => (int) $value)->values();
        $leadStatusTable = $leadStatusRows->map(function ($row) use ($totalLeads) {
            return [
                'status' => $row->lead_status ?: 'Unspecified',
                'total' => (int) $row->total,
                'share' => $totalLeads > 0 ? round(($row->total / $totalLeads) * 100, 1) : 0,
            ];
        })->take(5)->values();

        $crmMonthlyLabels = [];
        $crmMonthlyLeads = [];
        $crmMonthlyFollowUps = [];
        $crmMonthlyMeetings = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $crmMonthlyLabels[] = $month->format('M y');
            $crmMonthlyLeads[] = Lead::active()
                ->where('branch_id', $BranchID)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $crmMonthlyFollowUps[] = FollowUp::active()
                ->where('branch_id', $BranchID)
                ->whereYear('follow_up_datetime', $month->year)
                ->whereMonth('follow_up_datetime', $month->month)
                ->count();
            $crmMonthlyMeetings[] = Meeting::active()
                ->where('branch_id', $BranchID)
                ->whereYear('scheduled_on', $month->year)
                ->whereMonth('scheduled_on', $month->month)
                ->count();
        }

        $latestLeads = Lead::with(['assignedUser:id,name', 'convertedCustomer:id,name'])
            ->active()
            ->where('branch_id', $BranchID)
            ->latest()
            ->take(5)
            ->get();

        $upcomingFollowUps = FollowUp::with(['customer:id,name', 'assignedUser:id,name'])
            ->active()
            ->where('branch_id', $BranchID)
            ->whereBetween('follow_up_datetime', [$now, $now->copy()->addDays(7)])
            ->whereNotIn('status', ['Completed', 'Cancelled'])
            ->orderBy('follow_up_datetime')
            ->take(5)
            ->get()
            ->toBase()
            ->map(function ($item) {
                return [
                    'type' => 'Follow-up',
                    'title' => $item->purpose,
                    'party' => $item->customer->name ?? 'N/A',
                    'owner' => $item->assignedUser->name ?? 'Unassigned',
                    'status' => $item->status,
                    'date' => $item->follow_up_datetime,
                ];
            });

        $upcomingMeetings = Meeting::with(['customer:id,name', 'assignedUser:id,name'])
            ->active()
            ->scheduled()
            ->where('branch_id', $BranchID)
            ->whereBetween('scheduled_on', [$now, $now->copy()->addDays(7)])
            ->orderBy('scheduled_on')
            ->take(5)
            ->get()
            ->toBase()
            ->map(function ($item) {
                return [
                    'type' => 'Meeting',
                    'title' => $item->meeting_title,
                    'party' => $item->customer->name ?? 'N/A',
                    'owner' => $item->assignedUser->name ?? 'Unassigned',
                    'status' => $item->status,
                    'date' => $item->scheduled_on,
                ];
            });

        $upcomingCrmActions = $upcomingFollowUps
            ->merge($upcomingMeetings)
            ->sortBy('date')
            ->take(5)
            ->values();

        $staffQuery = User::where('role', 'staff')
            ->where('isDeleted', 0)
            ->where('branch_id', $BranchID);

        $totalStaff = (clone $staffQuery)->count();
        $activeStaff = (clone $staffQuery)->where('status', 1)->count();
        $newStaffThisMonth = (clone $staffQuery)
            ->whereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
            ->count();

        $todayAttendance = Attendance::where('branch_id', $BranchID)
            ->whereDate('date', $today)
            ->get();
        $todayPresentStaff = $todayAttendance->whereIn('status', ['P', 'H'])->unique('user_id')->count();
        $todayAbsentStaff = $todayAttendance->where('status', 'A')->unique('user_id')->count();
        $todayUnmarkedStaff = max(0, $totalStaff - $todayAttendance->unique('user_id')->count());

        $monthAttendance = Attendance::where('branch_id', $BranchID)
            ->whereYear('date', $now->year)
            ->whereMonth('date', $now->month)
            ->get();
        $presentEquivalent = $monthAttendance->where('status', 'P')->count()
            + ($monthAttendance->where('status', 'H')->count() * 0.5);
        $markedAttendance = $monthAttendance->whereIn('status', ['P', 'A', 'H'])->count();
        $monthlyAttendanceRate = $markedAttendance > 0 ? round(($presentEquivalent / $markedAttendance) * 100, 1) : 0;

        $currentMonthSalary = Salary::with('staff:id,name')
            ->where('branch_id', $BranchID)
            ->where('month', $now->month)
            ->where('year', $now->year)
            ->get();
        $salaryPaidCount = $currentMonthSalary->count();
        $salaryPendingCount = max(0, $totalStaff - $salaryPaidCount);
        $salaryPaidAmount = (float) $currentMonthSalary->sum('total_salary');
        $advanceOutstanding = (float) AdvancePayment::where('branch_id', $BranchID)
            ->where('isDeleted', 0)
            ->where('status', '!=', 'cleared')
            ->get(['amount', 'paid_amount'])
            ->sum(fn($advance) => max(0, (float) $advance->amount - (float) $advance->paid_amount));

        $hrAttendanceLabels = [];
        $hrAttendancePresent = [];
        $hrAttendanceAbsent = [];
        $hrAttendanceHalfDay = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i);
            $dailyRows = Attendance::where('branch_id', $BranchID)
                ->whereDate('date', $day->toDateString())
                ->get();
            $hrAttendanceLabels[] = $day->format('d M');
            $hrAttendancePresent[] = $dailyRows->where('status', 'P')->unique('user_id')->count();
            $hrAttendanceAbsent[] = $dailyRows->where('status', 'A')->unique('user_id')->count();
            $hrAttendanceHalfDay[] = $dailyRows->where('status', 'H')->unique('user_id')->count();
        }

        $hrSalaryLabels = [];
        $hrSalaryPaidAmounts = [];
        $hrSalaryPaidCounts = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $monthlySalaryRows = Salary::where('branch_id', $BranchID)
                ->where('month', $month->month)
                ->where('year', $month->year)
                ->get();
            $hrSalaryLabels[] = $month->format('M y');
            $hrSalaryPaidAmounts[] = (float) $monthlySalaryRows->sum('total_salary');
            $hrSalaryPaidCounts[] = $monthlySalaryRows->count();
        }

        $attendanceTable = (new Attendance())->getTable();
        $attendanceWatch = DB::table($attendanceTable)
            ->join('users', 'users.id', '=', $attendanceTable . '.user_id')
            ->where($attendanceTable . '.branch_id', $BranchID)
            ->whereYear($attendanceTable . '.date', $now->year)
            ->whereMonth($attendanceTable . '.date', $now->month)
            ->where('users.role', 'staff')
            ->where('users.isDeleted', 0)
            ->select(
                'users.id',
                'users.name',
                DB::raw("SUM(CASE WHEN {$attendanceTable}.status = 'absent' THEN 1 ELSE 0 END) as absent_days"),
                DB::raw("SUM(CASE WHEN {$attendanceTable}.status = 'half-day' THEN 1 ELSE 0 END) as half_days"),
                DB::raw("SUM(CASE WHEN {$attendanceTable}.status = 'present' THEN 1 ELSE 0 END) as present_days")
            )
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('absent_days')
            ->orderByDesc('half_days')
            ->limit(5)
            ->get();

        $salaryStaffIds = $currentMonthSalary->pluck('staff_id')->filter()->all();
        $payrollStatusRows = (clone $staffQuery)
            ->select('id', 'name', 'phone')
            ->orderBy('name')
            ->take(5)
            ->get()
            ->map(function ($staff) use ($currentMonthSalary, $salaryStaffIds) {
                $salary = $currentMonthSalary->firstWhere('staff_id', $staff->id);
                return [
                    'id' => $staff->id,
                    'name' => $staff->name,
                    'phone' => $staff->phone,
                    'status' => in_array($staff->id, $salaryStaffIds) ? 'Paid' : 'Pending',
                    'amount' => $salary ? (float) $salary->total_salary : 0,
                ];
            });

        $todayMeetings = Meeting::with(['customer:id,name', 'assignedUser:id,name'])
            ->active()
            ->where('branch_id', $BranchID)
            ->whereDate('scheduled_on', $today)
            ->orderBy('scheduled_on')
            ->get();

        $todayFollowUps = FollowUp::with(['customer:id,name', 'lead:id,name', 'assignedUser:id,name'])
            ->active()
            ->where('branch_id', $BranchID)
            ->whereDate('follow_up_datetime', $today)
            ->orderBy('follow_up_datetime')
            ->get();

        $dashboardDeliveries = Order::with(['creator:id,name', 'deliveries.deliveredBy', 'user'])
            ->where('isDeleted', '!=', 1)
            ->where('branch_id', $BranchID)
            ->where('quotation_status', 'sales')
            ->where('order_type', 'delivery')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($order) {
                $latestDelivery = $order->deliveries
                    ->sortByDesc('created_at')
                    ->first();

                return (object) [
                    'id' => $latestDelivery?->id,
                    'order_id' => $order->id,
                    'order' => $order,
                    'status' => $latestDelivery?->status ?: ($order->delivery_status ?: 'pending'),
                    'deliveredBy' => $latestDelivery?->deliveredBy ?: $order->creator,
                    'delivered_by' => $latestDelivery?->delivered_by ?: $order->created_by,
                    'has_delivery_record' => (bool) $latestDelivery,
                ];
            });

        $showTodayAlertsModal = (bool) session()->pull('showTodayAlertsModal', false) && $user->role === 'admin';

        return view('index', compact(
            'totalPurchaseAmount',
            'totalSalesAmount',
            'totalExpenseAmount',
            'customerCount',
            'vendorCount',
            'purchaseInvoiceCount',
            'salesInvoiceCount',
            'totalQuotationsCount',
            'wonQuotationsCount',
            'rentalProductsCount',
            'lostQuotationsCount',
            'recentProducts',
            'latestSales',
            'totalOrdersCount',
            'ordersThisMonthCount',
            'pendingOrdersCount',
            'partialOrdersCount',
            'completedOrdersCount',
            'latestPurchases',
            'totalPurchasesCount',
            'purchasesThisMonthCount',
            'pendingPurchasesCount',
            'partialPurchasesCount',
            'completedPurchasesCount',
            'salesChart',
            'purchaseChart',
            'currencySymbol',
            'currencyPosition',
            'salesChartthisyear',
            'salesChartpreviousyear',
            'purchaseChartthisyear',
            'purchaseChartpreviousyear',
            'salesChartThisMonth',
            'purchaseChartThisMonth',
            'todayMeetings',
            'todayFollowUps',
            'dashboardDeliveries',
            'showTodayAlertsModal',
            'currentYear',
            'previousYear',
            'showCrmDashboardSection',
            'showHrDashboardSection',
            'showCrmLeadPipeline',
            'showCrmConversion',
            'showCrmFollowupLoad',
            'showCrmMeetingMomentum',
            'showCrmLeadStatusMix',
            'showCrmActivityTrend',
            'showCrmPipelineQuality',
            'showCrmRecentLeads',
            'showCrmNext7Days',
            'showHrStaffStrength',
            'showHrActiveStaff',
            'showHrMonthlyAttendance',
            'showHrPersonalProgress',
            'showHrAttendancePattern',
            'showHrSalaryPayrollTrend',
            'showHrPayrollSnapshot',
            'showHrAttendanceWatch',
            'showHrPayrollStatus',
            'showErpDashboard',
            'showErpTotalSales',
            'showErpTotalPurchase',
            'showErpTotalExpense',
            'showErpSalesInvoiceCount',
            'showErpPurchaseInvoiceCount',
            'showErpCustomersCount',
            'showErpVendorsCount',
            'showErpSalesChart',
            'showErpPurchaseChart',
            'showErpRecentSales',
            'showErpRecentPurchases',
            'showErpRecentProducts',
            'showErpProductsDelivery',
            'showErpRecentRentals',
            'totalLeads',
            'newLeadsThisMonth',
            'convertedLeadCount',
            'crmConversionRate',
            'pendingFollowUps',
            'overdueFollowUps',
            'meetingsThisWeek',
            'completedMeetingsThisMonth',
            'leadStatusLabels',
            'leadStatusCounts',
            'leadStatusTable',
            'crmMonthlyLabels',
            'crmMonthlyLeads',
            'crmMonthlyFollowUps',
            'crmMonthlyMeetings',
            'latestLeads',
            'latestRentals',
            'upcomingCrmActions',
            'totalStaff',
            'activeStaff',
            'newStaffThisMonth',
            'todayPresentStaff',
            'todayAbsentStaff',
            'todayUnmarkedStaff',
            'monthlyAttendanceRate',
            'salaryPaidCount',
            'salaryPendingCount',
            'salaryPaidAmount',
            'advanceOutstanding',
            'hrAttendanceLabels',
            'hrAttendancePresent',
            'hrAttendanceAbsent',
            'hrAttendanceHalfDay',
            'hrSalaryLabels',
            'hrSalaryPaidAmounts',
            'hrSalaryPaidCounts',
            'attendanceWatch',
            'payrollStatusRows',
        ));
    }

    private function getSalesDataByMonth($month, $BranchID, $user = null)
    {
        $salesQuery = DB::table('orders')
            ->whereMonth('created_at', $month)
            ->where('isDeleted', '!=', 1)
            ->where('branch_id', $BranchID);

        if ($user) {
            StaffDepartmentScope::applyRestrictedOrderJoinScope($salesQuery, $user, 'orders');
        }

        $salesData = $salesQuery
            ->select(
                DB::raw("DAY(created_at) as day"),
                DB::raw("SUM(total_amount) as total")
            )
            ->groupBy(DB::raw("DAY(created_at)"))
            ->pluck('total', 'day')
            ->toArray();

        $chartData = [];
        for ($d = 1; $d <= 31; $d++) {
            $chartData[] = (float) ($salesData[$d] ?? 0);
        }
        return $chartData;
    }

    private function getPurchaseDataByMonth($month, $BranchID, $user = null)
    {
        $purchaseQuery = DB::table('purchase_invoice')
            ->whereMonth('created_at', $month)
            ->where('isDeleted', '!=', 1)
            ->where('branch_id', $BranchID);

        if ($user) {
            StaffDepartmentScope::applyStaffCreatedByScope($purchaseQuery, $user, 'created_by');
        }

        $purchaseData = $purchaseQuery
            ->select(
                DB::raw("DAY(created_at) as day"),
                DB::raw("SUM(grand_total) as total")
            )
            ->groupBy(DB::raw("DAY(created_at)"))
            ->pluck('total', 'day')
            ->toArray();

        $chartData = [];
        for ($d = 1; $d <= 31; $d++) {
            $chartData[] = (float) ($purchaseData[$d] ?? 0);
        }
        return $chartData;
    }

    private function getPurchaseListTotalAmount(User $user, int $BranchID): float
    {
        $query = DB::table('purchase_invoice')
            ->where('isDeleted', '=', 0)
            ->where('branch_id', '=', $BranchID);

        StaffDepartmentScope::applyPurchaseCreatedByScope($query, $user, 'created_by');

        return (float) $query->sum('grand_total');
    }

    private function getSalesDataByYear($year, $BranchID, $user = null)
    {
        $salesQuery = DB::table('orders')
            ->whereYear('created_at', $year)
            ->where('isDeleted', '!=', 1)
            ->where('branch_id', $BranchID);

        if ($user) {
            StaffDepartmentScope::applyRestrictedOrderJoinScope($salesQuery, $user, 'orders');
        }

        $salesData = $salesQuery
            ->select(
                DB::raw("MONTH(created_at) as month"),
                DB::raw("SUM(total_amount) as total")
            )
            ->groupBy(DB::raw("MONTH(created_at)"))
            ->pluck('total', 'month')
            ->toArray();

        $chart = [];
        for ($m = 1; $m <= 12; $m++) {
            $chart[] = (float) ($salesData[$m] ?? 0);
        }
        return $chart;
    }

    private function getPurchaseDataByYear($year, $BranchID, $user = null)
    {
        $purchaseQuery = DB::table('purchase_invoice')
            ->whereYear('created_at', $year)
            ->where('isDeleted', '!=', 1)
            ->where('branch_id', $BranchID);
            
        if ($user) {
            StaffDepartmentScope::applyStaffCreatedByScope($purchaseQuery, $user, 'created_by');
        }

        $purchaseData = $purchaseQuery
            ->select(
                DB::raw("MONTH(created_at) as month"),
                DB::raw("SUM(grand_total) as total")
            )
            ->groupBy(DB::raw("MONTH(created_at)"))
            ->pluck('total', 'month')
            ->toArray();

        $chart = [];
        for ($m = 1; $m <= 12; $m++) {
            $chart[] = (float) ($purchaseData[$m] ?? 0);
        }
        return $chart;
    }

    public function profile()
    {
        $user = auth()->user()->load('userDetail', 'details');
        $departments = collect();
        $designations = collect();

        if ($user && $user->role === 'staff') {
            $departments = DepartmentModel::orderBy('department_name')->get(['id', 'department_name']);
            $designations = DesignationModel::orderBy('designation_name')->get(['id', 'department_id', 'designation_name']);
        }

        return view('profile', compact('user', 'departments', 'designations'));
    }

    public function staffDashboard()
    {
        $user = auth()->user()->load('userDetail.department');

        // Only staff can access this page; others go to main dashboard
        if ($user->role !== 'staff') {
            return redirect()->route('auth.dashboard');
        }

        $showProductsDelivery = true; // Visibility is handled by settings in the blade template
        $dashboardDeliveries = StaffDepartmentScope::buildDashboardDeliveries($user);

        // ── Permissions Flags ───────────────────────────────────────────────
        $canViewSales      = app('hasPermission')(2, 'view') || app('hasPermission')(2, 'add');
        $canViewPurchases  = app('hasPermission')(3, 'view') || app('hasPermission')(3, 'add');
        $canViewExpenses   = app('hasPermission')(5, 'view') || app('hasPermission')(5, 'add');
        $canViewAttendance = app('hasPermission')(26, 'view') || app('hasPermission')(26, 'add');
        $canViewLeaves     = app('hasPermission')(28, 'view') || app('hasPermission')(28, 'add');

        $BranchID = $user->branch_id;
        $totalPurchaseAmount = 0;
        $totalSalesAmount = 0;
        $totalExpenseAmount = 0;
        $latestSales = collect();
        $latestPurchases = collect();

        if ($canViewPurchases) {
            $totalPurchaseAmount = $this->getPurchaseListTotalAmount($user, $BranchID);
        }

        if ($canViewSales) {
            $salesQuery = Order::where('isDeleted', '!=', 1);
            StaffDepartmentScope::applyOrderScope($salesQuery, $user);
            $totalSalesAmount = $salesQuery->sum('total_amount');
        }

        if ($canViewExpenses) {
            $expenseQuery = Expense::where('isDeleted', '!=', 1)
                ->where('branch_id', $BranchID);
            StaffDepartmentScope::applyStaffCreatedByScope($expenseQuery, $user);
            $totalExpenseAmount = $expenseQuery->sum('amount');
        }

        $totalOrdersCount = 0;
        $ordersThisMonthCount = 0;
        $completedOrdersCount = 0;
        $partialOrdersCount = 0;
        $pendingOrdersCount = 0;

        if ($canViewSales) {
            $latestSalesQuery = DB::table('orders')
                ->where('orders.isDeleted', '!=', 1)
                ->where('orders.branch_id', $BranchID);

            StaffDepartmentScope::applyRestrictedOrderJoinScope($latestSalesQuery, $user, 'orders');

            $latestSales = $latestSalesQuery
                ->joinSub(
                    DB::table('order_items')
                        ->selectRaw('MIN(id) as id, order_id')
                        ->where('isDeleted', '!=', 1)
                        ->groupBy('order_id'),
                    'oi',
                    function ($join) {
                        $join->on('orders.id', '=', 'oi.order_id');
                    }
                )
                ->join('order_items', 'order_items.id', '=', 'oi.id')
                ->leftJoin('users as customers', 'orders.user_id', '=', 'customers.id')
                ->join('products', function ($join) {
                    $join->on('order_items.product_id', '=', 'products.id')
                        ->where('products.isDeleted', '!=', 1);
                })
                ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->select(
                    'orders.id as order_id',
                    'orders.order_number',
                    'orders.created_at as order_date',
                    'orders.total_amount',
                    'orders.payment_method',
                    'customers.name as customer_name',
                    'products.id as product_id',
                    'products.name as product_name',
                    'products.SKU as product_code',
                    'products.images',
                    'brands.name as brand_name',
                    'categories.name as category_name'
                )
                ->orderBy('orders.created_at', 'desc')
                ->limit(4)
                ->get();

            $salesOrdersQuery = DB::table('orders')
                ->where('isDeleted', '!=', 1)
                ->where('branch_id', $BranchID)
                ->where('quotation_status', '!=', 'quotation')
                ->where('quotation_status', '!=', 'advance_receipt');

            StaffDepartmentScope::applyRestrictedOrderJoinScope($salesOrdersQuery, $user, 'orders');

            $totalOrdersCount = (clone $salesOrdersQuery)->count();
            $ordersThisMonthCount = (clone $salesOrdersQuery)
                ->whereMonth('created_at', Carbon::now('Asia/Kolkata')->month)
                ->whereYear('created_at', Carbon::now('Asia/Kolkata')->year)
                ->count();
            $completedOrdersCount = (clone $salesOrdersQuery)
                ->where('payment_status', 'completed')
                ->count();
            $partialOrdersCount = (clone $salesOrdersQuery)
                ->where('payment_status', 'partially')
                ->count();
            $pendingOrdersCount = (clone $salesOrdersQuery)
                ->where('payment_status', 'pending')
                ->count();
        }

        $totalPurchasesCount = 0;
        $purchasesThisMonthCount = 0;
        $completedPurchasesCount = 0;
        $partialPurchasesCount = 0;
        $pendingPurchasesCount = 0;

        if ($canViewPurchases) {
            $latestPurchasesQuery = DB::table('purchase_invoice as pi')
                ->where('pi.isDeleted', '!=', 1)
                ->where('pi.branch_id', '=', $BranchID);

            StaffDepartmentScope::applyRestrictedPurchaseJoinScope($latestPurchasesQuery, $user, 'pur', 'pi');

            $latestPurchases = $latestPurchasesQuery
                ->joinSub(
                    DB::table('purchases')
                        ->selectRaw('MIN(id) as id, invoice_id')
                        ->where('isDeleted', '!=', 1)
                        ->groupBy('invoice_id'),
                    'p',
                    function ($join) {
                        $join->on('pi.id', '=', 'p.invoice_id');
                    }
                )
                ->join('purchases as pur', 'pur.id', '=', 'p.id')
                ->leftJoin('users as vendors', 'pi.vendor_id', '=', 'vendors.id')
                ->leftJoin('products as pr', function ($join) {
                    $join->on('pr.id', '=', 'pur.item')
                        ->where('pr.isDeleted', '!=', 1);
                })
                ->leftJoin('brands as br', 'pr.brand_id', '=', 'br.id')
                ->leftJoin('categories as cat', 'pr.category_id', '=', 'cat.id')
                ->select(
                    'pi.id as invoice_id',
                    'pi.invoice_number',
                    'pi.bill_no',
                    'pi.grand_total',
                    'pi.created_at as purchase_date',
                    'vendors.name as vendor_name',
                    'pr.id as product_id',
                    'pr.name as product_name',
                    'pr.SKU as product_code',
                    'pr.images',
                    'br.name as brand_name',
                    'cat.name as category_name'
                )
                ->orderBy('pi.created_at', 'desc')
                ->limit(4)
                ->get();
        }

        $now          = \Carbon\Carbon::now('Asia/Kolkata');
        $currentMonth = $now->month;
        $currentYear  = $now->year;
        $monthName    = $now->format('F Y');
        $previousYear = $currentYear - 1;

        if ($canViewPurchases) {
            $purchasesCountQuery = DB::table('purchase_invoice')
                ->leftJoin('purchases', function ($join) use ($BranchID) {
                    $join->on('purchase_invoice.id', '=', 'purchases.invoice_id')
                        ->where('purchases.isDeleted', '=', 0)
                        ->where('purchases.branch_id', '=', $BranchID);
                })
                ->where('purchase_invoice.isDeleted', '!=', 1)
                ->where('purchase_invoice.branch_id', $BranchID);
                
            StaffDepartmentScope::applyRestrictedPurchaseJoinScope($purchasesCountQuery, $user, 'purchases', 'purchase_invoice');

            $totalPurchasesCount = (clone $purchasesCountQuery)->distinct('purchase_invoice.id')->count('purchase_invoice.id');
            $purchasesThisMonthCount = (clone $purchasesCountQuery)
                ->whereMonth('purchase_invoice.created_at', Carbon::now('Asia/Kolkata')->month)
                ->whereYear('purchase_invoice.created_at', Carbon::now('Asia/Kolkata')->year)
                ->distinct('purchase_invoice.id')->count('purchase_invoice.id');
            $completedPurchasesCount = (clone $purchasesCountQuery)
                ->where('purchases.payment_status', 'Completed')
                ->distinct('purchase_invoice.id')->count('purchase_invoice.id');
            $partialPurchasesCount = (clone $purchasesCountQuery)
                ->where('purchases.payment_status', 'Partially')
                ->distinct('purchase_invoice.id')->count('purchase_invoice.id');
            $pendingPurchasesCount = (clone $purchasesCountQuery)
                ->where('purchases.payment_status', 'Pending')
                ->distinct('purchase_invoice.id')->count('purchase_invoice.id');
        }

        $salesChartThisMonth    = $this->getSalesDataByMonth($currentMonth, $BranchID, $user);
        $purchaseChartThisMonth = $this->getPurchaseDataByMonth($currentMonth, $BranchID, $user);
        $salesChartthisyear     = $this->getSalesDataByYear($currentYear, $BranchID, $user);
        $salesChartpreviousyear = $this->getSalesDataByYear($previousYear, $BranchID, $user);
        $purchaseChartthisyear     = $this->getPurchaseDataByYear($currentYear, $BranchID, $user);
        $purchaseChartpreviousyear = $this->getPurchaseDataByYear($previousYear, $BranchID, $user);

        // Recent Rentals
        $latestRentalsQuery = DB::table('orders')
            ->where('orders.isDeleted', '!=', 1)
            ->where('orders.branch_id', $BranchID)
            ->where('orders.order_type', 'rent');
        StaffDepartmentScope::applyRestrictedOrderJoinScope($latestRentalsQuery, $user, 'orders');

        $latestRentals = $latestRentalsQuery
            ->joinSub(
                DB::table('order_items')
                    ->selectRaw('MIN(id) as id, order_id')
                    ->where('isDeleted', '!=', 1)
                    ->groupBy('order_id'),
                'oi',
                function ($join) {
                    $join->on('orders.id', '=', 'oi.order_id');
                }
            )
            ->join('order_items', 'order_items.id', '=', 'oi.id')
            ->leftJoin('users as customers', 'orders.user_id', '=', 'customers.id')
            ->join('products', function ($join) {
                $join->on('order_items.product_id', '=', 'products.id')
                    ->where('products.isDeleted', '!=', 1);
            })
            ->select(
                'orders.id as order_id',
                'orders.order_number',
                'orders.created_at as order_date',
                'orders.total_amount',
                'customers.name as customer_name',
                'products.name as product_name',
                'products.images'
            )
            ->orderBy('orders.created_at', 'desc')
            ->limit(4)
            ->get();

        // ── Attendance stats for current month ──────────────────────────────
        $attendanceRecords = \App\Models\Attendance::where('user_id', $user->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->get();

        $totalPresent = $attendanceRecords->where('status', 'present')->unique('date')->count();
        $leaveModel   = new \App\Models\LeaveModel();
        $totalLeave   = $leaveModel->getLeavesCount($user->id, sprintf('%04d-%02d', $currentYear, $currentMonth));

        // ── Follow-up count (assigned to or created by this staff) ──────────
        $totalFollowUps = \App\Models\FollowUp::active()
            ->where('branch_id', $user->branch_id)
            ->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                    ->orWhere('created_by', $user->id);
            })
            ->count();

        // ── Meeting count (assigned to or created by this staff) ────────────
        $totalMeetings = \App\Models\Meeting::active()
            ->where('branch_id', $user->branch_id)
            ->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                    ->orWhere('created_by', $user->id);
            })
            ->count();

        // ── Recent follow-ups (latest 10) ────────────────────────────────────
        $recentFollowUps = \App\Models\FollowUp::with(['customer:id,name', 'lead:id,name', 'assignedUser:id,name'])
            ->active()
            ->where('branch_id', $user->branch_id)
            ->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                    ->orWhere('created_by', $user->id);
            })
            ->orderByDesc('follow_up_datetime')
            ->limit(10)
            ->get();

        // ── Recent meetings (latest 10) ──────────────────────────────────────
        $recentMeetings = \App\Models\Meeting::with(['customer:id,name', 'assignedUser:id,name'])
            ->active()
            ->where('branch_id', $user->branch_id)
            ->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                    ->orWhere('created_by', $user->id);
            })
            ->orderByDesc('scheduled_on')
            ->limit(10)
            ->get();

        // ── Today's Attendance — Source of Truth: Attendance Table ─────────
        $todayIST = \Carbon\Carbon::now('Asia/Kolkata');
        $todayStr = $todayIST->toDateString();  // e.g. "2026-06-01"

        // ── Settings: standard working hours ────────────────────────────
        $settings = \App\Models\Setting::where('branch_id', $user->branch_id ?? 0)->first()
            ?? \App\Models\Setting::first();

        $currencySymbol   = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';

        // ERP subsection visibility settings
        $showErpDashboard = (bool) ($settings->show_erp_dashboard ?? true);
        $showErpTotalSales = (bool) ($settings->show_erp_total_sales ?? true);
        $showErpTotalPurchase = (bool) ($settings->show_erp_total_purchase ?? true);
        $showErpTotalExpense = (bool) ($settings->show_erp_total_expense ?? true);
        $showErpSalesChartCard = (bool) ($settings->show_erp_sales_chart ?? true);
        $showErpPurchaseChartCard = (bool) ($settings->show_erp_purchase_chart ?? true);
        $showErpRecentSales = (bool) ($settings->show_erp_recent_sales ?? true);
        $showErpRecentPurchases = (bool) ($settings->show_erp_recent_purchases ?? true);
        $showErpRecentRentals = (bool) ($settings->show_erp_recent_rentals ?? true);
        $showErpProductsDelivery = (bool) ($settings->show_erp_products_delivery ?? true);


        $workingHoursStr = ($settings && $settings->working_hours) ? $settings->working_hours : '08:30:00';
        $parts   = explode(':', $workingHoursStr);
        $wHours  = isset($parts[0]) ? (int)$parts[0] : 8;
        $wMins   = isset($parts[1]) ? (int)$parts[1] : 30;
        $wSecs   = isset($parts[2]) ? (int)$parts[2] : 0;
        $standardSeconds = ($wHours * 3600) + ($wMins * 60) + $wSecs;
        if ($standardSeconds <= 0) {
            $standardSeconds = 30600;
            $wHours = 8;
            $wMins = 30;
            $wSecs = 0;
        }
        $standardHoursFormatted = sprintf('%02d:%02d:%02d', $wHours, $wMins, $wSecs);

        // ── Fetch today's Attendance records ──────────────────────────────
        $todaySessions = \App\Models\Attendance::where('user_id', $user->id)
            ->where('date', $todayStr)
            ->orderBy('check_in_time', 'asc')
            ->get();

        // ── Safe defaults (no sessions today = blank state) ───────────────
        $todayCheckInTime        = null;
        $todayCheckInUnix        = null;
        $todayCheckOutTime       = null;
        $todayCheckOutUnix       = null;
        $hoursWorkedFormatted    = '00:00:00';
        $remainingHoursFormatted = $standardHoursFormatted;
        $workedPercentage        = 0;
        $remainingPercentage     = 100;
        $todayStatus             = 'Not Checked In';
        $totalWorkedSeconds      = 0;
        $activeSession           = null; // Maintained for view compatibility

        if ($todaySessions->isNotEmpty()) {

            // ── Helper: safely parse a time string (H:i:s or H:i) ───────────
            $parseIST = function (string $timeStr) use ($todayStr): \Carbon\Carbon {
                if (strlen($timeStr) <= 5) {
                    $timeStr .= ':00';
                }
                return \Carbon\Carbon::parse($todayStr . ' ' . $timeStr, 'Asia/Kolkata');
            };

            $firstSession = $todaySessions->first();

            // ── Check In ────────────────────────────────────────────────────
            $checkInCarbon    = $parseIST($firstSession->check_in_time);
            $todayCheckInTime = $checkInCarbon->format('h:i A');
            $todayCheckInUnix = $checkInCarbon->timestamp;

            $todayStatus = 'Checked Out';

            // ── Check Out & Session Summation ───────────────────────────────
            foreach ($todaySessions as $session) {
                if (empty($session->check_in_time)) continue;
                $inCarbon = $parseIST($session->check_in_time);

                if (!empty($session->check_out_time)) {
                    $outCarbon = $parseIST($session->check_out_time);
                    $totalWorkedSeconds += max(0, $outCarbon->diffInSeconds($inCarbon));

                    $todayCheckOutTime = $outCarbon->format('h:i A');
                    $todayCheckOutUnix = $outCarbon->timestamp;
                } else {
                    $todayStatus        = 'Checked In';
                    $activeSession      = (object)['id' => $session->id];
                    $totalWorkedSeconds += max(0, $todayIST->diffInSeconds($inCarbon));
                    $todayCheckOutTime  = null;
                    $todayCheckOutUnix  = null;
                }
            }

            $remainingSeconds        = max(0, $standardSeconds - $totalWorkedSeconds);
            $hoursWorkedFormatted    = sprintf('%02d:%02d:%02d', intdiv($totalWorkedSeconds, 3600), intdiv($totalWorkedSeconds % 3600, 60), $totalWorkedSeconds % 60);
            $remainingHoursFormatted = sprintf('%02d:%02d:%02d', intdiv($remainingSeconds, 3600), intdiv($remainingSeconds % 3600, 60), $remainingSeconds % 60);
            $workedPercentage        = min(100, round(($totalWorkedSeconds / $standardSeconds) * 100, 2));
            $remainingPercentage     = min(100, round(($remainingSeconds  / $standardSeconds) * 100, 2));
        }

        return view('staff_dashboard', compact(
            'user',
            'totalPresent',
            'totalLeave',
            'totalFollowUps',
            'totalMeetings',
            'recentFollowUps',
            'recentMeetings',
            'monthName',
            'currentMonth',
            'currentYear',
            'previousYear',
            'todayStr',
            'todaySessions',
            'todayCheckInTime',
            'todayCheckOutTime',
            'hoursWorkedFormatted',
            'remainingHoursFormatted',
            'workedPercentage',
            'remainingPercentage',
            'todayStatus',
            'activeSession',
            'standardHoursFormatted',
            'todayCheckInUnix',
            'todayCheckOutUnix',
            'standardSeconds',
            'totalWorkedSeconds',
            'showProductsDelivery',
            'dashboardDeliveries',
            'canViewSales',
            'canViewPurchases',
            'canViewExpenses',
            'canViewAttendance',
            'canViewLeaves',
            'totalPurchaseAmount',
            'totalSalesAmount',
            'totalExpenseAmount',
            'latestSales',
            'latestPurchases',
            'currencySymbol',
            'currencyPosition',
            'totalOrdersCount',
            'ordersThisMonthCount',
            'completedOrdersCount',
            'partialOrdersCount',
            'pendingOrdersCount',
            'totalPurchasesCount',
            'purchasesThisMonthCount',
            'completedPurchasesCount',
            'partialPurchasesCount',
            'pendingPurchasesCount',
            'salesChartThisMonth',
            'purchaseChartThisMonth',
            'salesChartthisyear',
            'salesChartpreviousyear',
            'purchaseChartthisyear',
            'purchaseChartpreviousyear',
            'latestRentals',
            'showErpDashboard',
            'showErpTotalSales',
            'showErpTotalPurchase',
            'showErpTotalExpense',
            'showErpSalesChartCard',
            'showErpPurchaseChartCard',
            'showErpRecentSales',
            'showErpRecentPurchases',
            'showErpRecentRentals',
            'showErpProductsDelivery'
        ));
    }
    public function taxrates()
    {
        return view('taxrates');
    }
    public function currency()
    {
        return view('currency');
    }
    public function ajaxSearch(Request $request)
    {
        // Accept search / query / q — blank search must NOT return the full catalog
        $query = trim((string) (
            $request->input('search')
            ?? $request->input('q')
            ?? $request->input('query')
            ?? ''
        ));

        $authUser = auth()->user();
        $selectedSubAdminId = session('selectedSubAdminId');

        if (! empty($selectedSubAdminId)) {
            $BranchID = $selectedSubAdminId;
        } elseif ($authUser && $authUser->role === 'staff' && $authUser->branch_id) {
            $BranchID = $authUser->branch_id;
        } elseif ($authUser) {
            $BranchID = $authUser->id;
        } else {
            $BranchID = null;
        }

        $settings = $BranchID
            ? DB::table('settings')->where('branch_id', $BranchID)->first()
            : DB::table('settings')->first();
        $currencySymbol   = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';

        // Search users with role customer or vendor
        $users = User::where(function ($q) use ($query) {
            if ($query === '') {
                $q->whereRaw('1 = 0');
                return;
            }
            $q->where('name', 'LIKE', "%{$query}%")
                ->orWhere('phone', 'LIKE', "%{$query}%");
        })
            ->whereIn('role', ['customer', 'vendor'])
            ->when($BranchID, function ($q) use ($BranchID) {
                $q->where('branch_id', $BranchID);
            })
            ->take(10)
            ->get(['id', 'name', 'email', 'phone', 'profile_image', 'role']);

        // Search products by name, barcode, SKU (word-wise: "multi mala" matches both words)
        // Cap results — returning thousands freezes POS autosuggest UI.
        $productLimit = max(5, min(50, (int) $request->get('limit', 25)));
        $productQuery = Product::query()
            ->with('category:id,name')
            ->matchingSearch($query)
            ->where('isDeleted', '!=', 1)
            ->where('status', 'active')
            ->when($BranchID, function ($q) use ($BranchID) {
                $q->where('branch_id', $BranchID);
            });

        if ($query === '') {
            $productsTotal = 0;
            $products = collect();
        } else {
            $productsTotal = (clone $productQuery)->count();
            $likePrefix = addcslashes($query, '%_\\') . '%';
            $likeAny = '%' . addcslashes($query, '%_\\') . '%';
            $products = (clone $productQuery)
                ->orderByRaw(
                    'CASE
                        WHEN name LIKE ? THEN 0
                        WHEN name LIKE ? THEN 1
                        WHEN SKU LIKE ? OR barcode LIKE ? THEN 2
                        ELSE 3
                     END',
                    [$likePrefix, $likeAny, $likeAny, $likeAny]
                )
                ->orderBy('name')
                ->limit($productLimit)
                ->get([
                    'id',
                    'name',
                    'price',
                    'mrp',
                    'quantity',
                    'category_id',
                    'gst_option',
                    'product_gst',
                    'barcode',
                    'SKU',
                    'selling_prices',
                    'is_rent_available',
                    'rent_price',
                    'rent_period',
                    'images',
                    'branch_id',
                ]);
        }

        // Search orders by order number or customer name
        $orders = Order::with('user:id,name')
            ->when($query !== '', function ($q) use ($query) {
                $q->where(function ($inner) use ($query) {
                    $inner->where('order_number', 'LIKE', "%{$query}%")
                        ->orWhereHas('user', function ($uq) use ($query) {
                            $uq->where('name', 'LIKE', "%{$query}%");
                        });
                });
            }, function ($q) {
                $q->whereRaw('1 = 0');
            })
            ->take(10)
            ->get(['id', 'order_number', 'user_id', 'total_amount', 'payment_status']);

        // Search purchase invoices
        $purchaseInvoices = PurchaseInvoice::when($query !== '', function ($q) use ($query) {
                $q->where('invoice_number', 'LIKE', "%{$query}%");
            }, function ($q) {
                $q->whereRaw('1 = 0');
            })
            ->take(10)
            ->get(['id', 'invoice_number', 'grand_total']);

        // Helper to format amount
        $formatCurrency = function ($amount) use ($currencySymbol, $currencyPosition) {
            return $currencyPosition === 'left'
                ? $currencySymbol . number_format($amount, 2)
                : number_format($amount, 2) . $currencySymbol;
        };

        $results = [
            'users'             => $users->map(function ($user) {
                return [
                    'id'            => $user->id,
                    'name'          => $user->name,
                    'email'         => $user->email,
                    'phone'         => $user->phone,
                    'profile_image' => $user->profile_image
                        ? asset(env('ImagePath') . 'storage/' . $user->profile_image)
                        : asset(env('ImagePath') . 'admin/assets/img/customer/default.jpg'),
                    'role'          => $user->role,
                ];
            }),
            'products'          => $products->map(function ($product) use ($formatCurrency) {
                $imageFileName = null;
                if (! empty($product->images)) {
                    $imagesArray = json_decode($product->images, true);
                    if (is_array($imagesArray) && count($imagesArray) > 0) {
                        $imageFileName = $imagesArray[0];
                    }
                }
                $sellingPrices = $product->sellingPriceOptions();
                $wholesaleRates = Product::resolveWholesaleRates($sellingPrices, (float) $product->price);
                $wholesalePrice = $wholesaleRates[0]['price'] ?? null;
                return [
                    'id'          => $product->id,
                    'name'        => $product->name,
                    'price'       => (float) $product->price,
                    'price_formatted' => $formatCurrency($product->price),
                    'wholesale_price' => $wholesalePrice,
                    'wholesale_price_formatted' => $wholesalePrice !== null ? $formatCurrency($wholesalePrice) : null,
                    'wholesale_rates' => $wholesaleRates,
                    'mrp'         => $product->mrp,
                    'quantity'    => (float) ($product->quantity ?? 0),
                    'category_id' => $product->category_id,
                    'category_name' => $product->category?->name,
                    'is_warranty_category' => strtolower(trim((string) ($product->category?->name ?? ''))) === 'warranty',
                    'gst_option'  => $product->gst_option,
                    'product_gst' => $product->product_gst,
                    'barcode'     => $product->barcode,
                    'SKU'         => $product->SKU,
                    'selling_prices' => $sellingPrices,
                    'is_rent_available' => $product->is_rent_available,
                    'rent_price'  => $product->rent_price,
                    'rent_period' => $product->rent_period,
                    'rent_price_formatted' => $product->rent_price ? $formatCurrency($product->rent_price) : null,
                    'image'       => $imageFileName ? asset(env('ImagePath') . 'storage/' . $imageFileName) : asset(env('ImagePath') . 'admin/assets/img/product/noimage.png'),
                ];
            }),
            'products_total'    => $productsTotal,
            'products_limit'    => $productLimit,
            'search'            => $query,
            'orders'            => $orders->map(function ($order) use ($formatCurrency) {
                return [
                    'id'             => $order->id,
                    'order_number'   => $order->order_number,
                    'total_amount'   => $formatCurrency($order->total_amount), // formatted total
                    'payment_status' => $order->payment_status,
                    'user_name'      => $order->user ? $order->user->name : 'N/A',
                ];
            }),
            'purchase_invoices' => $purchaseInvoices->map(function ($invoice) use ($formatCurrency) {
                return [
                    'id'             => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'grand_total'    => $formatCurrency($invoice->grand_total),
                ];
            }),
        ];

        return response()->json($results);
    }
}
