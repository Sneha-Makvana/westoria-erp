<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Services\PendingEmiService;
use App\Services\StaffDepartmentScope;
use App\Models\BankMaster;
use App\Models\Category;
use App\Models\LabourItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentStore;
use App\Models\Sales_Labour_Items;
use App\Models\Product;
use App\Models\SalesReturn;
use App\Models\Setting;
use App\Models\TaxRate;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Delivery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesController extends Controller
{
    private function fallbackSetting(?int $branchId = null): Setting
    {
        return Setting::where('branch_id', $branchId)->first()
            ?? Setting::first()
            ?? new Setting([
                'name' => 'Fablead Developer & Technolab',
                'email' => 'info@gmail.com',
                'phone' => 1234567890,
                'address' => 'Adajan Surat',
                'logo' => 'admin/assets/img/logo-image.jpg',
                'currency_symbol' => '₹',
                'currency_position' => 'left',
            ]);
    }

    private function attachOrderCustomerDetails(Order $order): void
    {
        $orderUser = $order->relationLoaded('user')
            ? $order->user
            : ($order->user_id ? User::with('userDetail')->find($order->user_id) : null);

        if ($orderUser) {
            $order->customer_name       = $orderUser->name ?? 'Default-customer';
            $order->customer_email      = $orderUser->email ?? '';
            $order->customer_phone      = $orderUser->phone ?? '';
            $order->customer_address    = optional($orderUser->userDetail)->address ?? '';
            $order->customer_city       = optional($orderUser->userDetail)->city ?? '';
            $order->customer_country    = optional($orderUser->userDetail)->country ?? '';
        } else {
            $order->customer_name       = $order->customer_name ?? 'Default-customer';
            $order->customer_email      = $order->customer_email ?? '';
            $order->customer_phone      = $order->customer_phone ?? '';
            $order->customer_address    = $order->customer_address ?? '';
            $order->customer_city       = $order->customer_city ?? '';
            $order->customer_country    = $order->customer_country ?? '';
        }
    }

    private function normalizeDeliveryStatus(?string $status): string
    {
        $normalized = strtolower(trim((string) $status));

        return match ($normalized) {
            'partial', 'partially', 'partially_delivered' => 'partially_delivered',
            'delivered' => 'delivered',
            'cancelled', 'canceled' => 'cancelled',
            default => 'pending',
        };
    }

    public function productsDelivery(Request $request)
    {
        $user = auth()->user();
        $subAdminId = session('selectedSubAdminId');

        if ($user && $user->role === 'staff' && $user->branch_id) {
            $branchId = $user->branch_id;
        } elseif ($user && $user->role === 'admin' && !empty($subAdminId)) {
            $branchId = $subAdminId;
        } else {
            $branchId = $user ? $user->id : null;
        }

        $setting = $this->fallbackSetting($branchId);
        $status = trim((string) $request->query('status', ''));
        $orderNo = trim((string) $request->query('order_no', ''));
        $fromDate = trim((string) $request->query('from', ''));
        $toDate = trim((string) $request->query('to', ''));

        $query = Order::with(['creator:id,name', 'deliveries.deliveredBy'])
            ->where('isDeleted', '!=', 1)
            ->where('quotation_status', 'sales')
            ->where('order_type', 'delivery');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($orderNo !== '') {
            $query->where('order_number', 'like', '%' . $orderNo . '%');
        }

        if ($status !== '') {
            if ($status === 'partially_delivered') {
                $query->where(function ($q) {
                    $q->where('delivery_status', 'partial')
                      ->orWhere('delivery_status', 'partially_delivered');
                });
            } else {
                $query->where('delivery_status', $status);
            }
        }

        if ($fromDate !== '') {
            $query->whereDate('created_at', '>=', $fromDate);
        }

        if ($toDate !== '') {
            $query->whereDate('created_at', '<=', $toDate);
        }

        $orders = $query->orderByDesc('created_at')->get()->map(function ($order) {
            $latestDelivery = $order->deliveries->sortByDesc('created_at')->first();
            $status = $this->normalizeDeliveryStatus($latestDelivery?->status ?: $order->delivery_status);

            return (object) [
                'id' => $latestDelivery?->id,
                'order_id' => $order->id,
                'order' => $order,
                'status' => $status,
                'deliveredBy' => $latestDelivery?->deliveredBy ?: $order->creator,
                'delivered_by' => $latestDelivery?->delivered_by ?: $order->created_by,
                'has_delivery_record' => (bool) $latestDelivery,
            ];
        });

        return view('sales.products-delivery', compact('orders', 'setting', 'status', 'orderNo', 'fromDate', 'toDate'));
    }

    public function productsDeliveryData(Request $request)
    {
        $user = auth()->user();
        $subAdminId = $request->query('selectedSubAdminId') ?: session('selectedSubAdminId');

        if ($user && $user->role === 'staff' && $user->branch_id) {
            $branchId = $user->branch_id;
        } elseif ($user && $user->role === 'admin' && !empty($subAdminId)) {
            $branchId = $subAdminId;
        } else {
            $branchId = $user ? $user->id : null;
        }

        $setting = $this->fallbackSetting($branchId);
        $status = trim((string) $request->query('status', ''));
        $orderNo = trim((string) $request->query('order_no', ''));
        $fromDate = trim((string) $request->query('from', ''));
        $toDate = trim((string) $request->query('to', ''));
        
        // ✅ Pagination parameters
        $page = max(1, (int) $request->query('page', 1));
        $perPage = min(100, max(1, (int) $request->query('per_page', 10)));

        $query = Order::with(['creator:id,name', 'deliveries.deliveredBy', 'user.userDetail'])
            ->where('isDeleted', '!=', 1)
            ->where('quotation_status', 'sales')
            ->where('order_type', 'delivery');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($orderNo !== '') {
            $query->where('order_number', 'like', '%' . $orderNo . '%');
        }

        if ($status !== '') {
            if ($status === 'partially_delivered') {
                $query->where(function ($q) {
                    $q->where('delivery_status', 'partial')
                        ->orWhere('delivery_status', 'partially_delivered');
                });
            } else {
                $query->where('delivery_status', $status);
            }
        }

        if ($fromDate !== '') {
            $query->whereDate('created_at', '>=', $fromDate);
        }

        if ($toDate !== '') {
            $query->whereDate('created_at', '<=', $toDate);
        }

        // ✅ Get total count for pagination
        $totalCount = $query->count();
        $lastPage = max(1, (int) ceil($totalCount / $perPage));
        $page = min($page, $lastPage);

        $orders = $query->orderByDesc('created_at')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->map(function ($order) {
                $latestDelivery = $order->deliveries->sortByDesc('created_at')->first();
                $status = $this->normalizeDeliveryStatus($latestDelivery?->status ?: $order->delivery_status);

                return [
                    'id' => $latestDelivery?->id,
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'created_at' => optional($order->created_at)->format('d-M-Y h:i A'),
                    'customer_name' => $order->customer_name ?: ($order->user?->name ?? 'Default-customer'),
                    'total' => (float) ($order->grand_total ?? $order->total_amount ?? 0),
                    'currency_symbol' => $setting->currency_symbol ?? '₹',
                    'delivered_by' => $latestDelivery?->deliveredBy?->name ?? $latestDelivery?->delivered_by ?? $order->creator?->name ?? '--',
                    'status' => $status,
                    'has_delivery_record' => (bool) $latestDelivery,
                    'pdf_url' => route('sales.delivery.challan.pdf', $order->id),
                    'delivery_url' => route('sales.delivery', $order->id),
                    'status_update_url' => $latestDelivery
                        ? route('sales.delivery.status.update', $latestDelivery->id)
                        : route('sales.order.delivery.status.update', $order->id),
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $orders,
            'pagination' => [
                'current_page' => (int) $page,
                'last_page' => $lastPage,
                'per_page' => (int) $perPage,
                'total' => $totalCount,
                'from' => $orders->count() > 0 ? (($page - 1) * $perPage) + 1 : 0,
                'to' => $orders->count() > 0 ? (($page - 1) * $perPage) + $orders->count() : 0,
            ],
        ]);
    }

    public function sales_list(Request $request)
    {
        $user = auth()->user();
        $subAdminId = session('selectedSubAdminId');

        // 🔹 Decide branch_id based on role
        if ($user->role === 'staff' && $user->branch_id) {
            $branchIdToUse = $user->branch_id;
        } elseif ($user->role === 'admin' && ! empty($subAdminId)) {
            $branchIdToUse = $subAdminId;
        } else {
            $branchIdToUse = $user->id;
        }

        $years = Order::where('isDeleted', 0)
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        $banks = BankMaster::where('isDeleted', 0)
            ->where('branch_id', $branchIdToUse)
            ->get();

        $setting = $this->fallbackSetting($branchIdToUse);
        $financialYearEnabled = (bool) ($setting->financial_year ?? true);

        // Staff list for staff-wise filter and assignment
        $staffList = User::where('role', 'staff')
            ->where('isDeleted', 0)
            ->where('branch_id', $branchIdToUse)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('sales/saleslist', compact('years', 'banks', 'financialYearEnabled', 'staffList', 'setting'));
    }

    public function import_sales(Request $request)
    {
        return view('sales/importsalesinvoice');
    }

    public function add_sales(Request $request)
    {

        return view('sales/add-sales');
    }
    public function edit_sales($id)
    {
        $sales = Order::where('isDeleted', 0)->find($id);
        if (! $sales) {
            return redirect()->route('sales.list')->with('error', 'Sales record not found.');
        }

        $saleType = $sales->quotation_status ?: 'sales';
        if (! in_array($saleType, ['sales', 'quotation', 'rental', 'advance_receipt'], true)) {
            $saleType = 'sales';
        }

        return redirect()->route('sales.add', [
            'sale_type' => $saleType,
            'edit_order_id' => $sales->id,
        ]);

        $user       = auth()->user();
        $subAdminId = session('selectedSubAdminId');

        // 🔹 Decide branch_id based on role
        if ($user->role === 'staff' && $user->branch_id) {
            $branchIdToUse = $user->branch_id;
        } elseif ($user->role === 'admin' && ! empty($subAdminId)) {
            $branchIdToUse = $subAdminId;
        } else {
            $branchIdToUse = $user->id;
        }

        // 🔹 Load sales record - IMPORTANT: Load product_gst_details from order_items
        $sales = Order::with(['exchangeMaterial', 'exchangeType', 'order_items' => function ($query) {
            $query->select(
                'id',
                'order_id',
                'product_id',
                'product_variant_id',
                'product_gst_details',
                'product_gst_total',
                'quantity',
                'price',
                'discount_percentage',
                'discount_amount',
                'total_amount',
                'imei_no',
                'is_rental',
                'rent_value',
                'rent_period',
                'rent_start_at',
                'rent_end_at',
                'rent_price'
            );
        }, 'order_items.product', 'payments.bank'])->find($id);

        if (! $sales) {
            return redirect()->route('sales.list')->with('error', 'Sales record not found.');
        }

        $latestEmiPayment = $sales->payments
            ->filter(function ($payment) {
                return (int) ($payment->isDeleted ?? 0) === 0
                    && strtolower((string) ($payment->payment_method ?? '')) === 'emi';
            })
            ->sortByDesc('id')
            ->first();

        if ($latestEmiPayment) {
            $sales->setAttribute('emi_down_payment', $sales->emi_down_payment ?? $latestEmiPayment->emi_down_payment);
            $sales->setAttribute('emi_loan_amount', $sales->emi_loan_amount ?? $latestEmiPayment->emi_loan_amount);
            $sales->setAttribute('emi_interest_rate', $sales->emi_interest_rate ?? $latestEmiPayment->emi_interest_rate);
            $sales->setAttribute('emi_tenure', $sales->emi_tenure ?? $latestEmiPayment->emi_tenure);
            $sales->setAttribute('emi_monthly_amount', $sales->emi_monthly_amount ?? $latestEmiPayment->emi_monthly_amount);
            $sales->setAttribute('emi_aadhar_number', $sales->emi_aadhar_number ?? $latestEmiPayment->emi_aadhar_number);
            $sales->setAttribute('emi_pan_number', $sales->emi_pan_number ?? $latestEmiPayment->emi_pan_number);
            $sales->setAttribute('emi_guarantor_name', $sales->emi_guarantor_name ?? $latestEmiPayment->emi_guarantor_name);
        }

        $update_id = $id;

        // 🔹 Load related data
        $usernamesQuery = User::where('role', 'customer')
            ->where('branch_id', $branchIdToUse)
            ->where('isDeleted', 0);
        StaffDepartmentScope::applyStaffCreatedByScope($usernamesQuery, $user);
        $usernames = $usernamesQuery->get();

        $category = Category::where('isDeleted', 0)
            ->where('branch_id', $branchIdToUse)
            ->get();

        $TaxRate = TaxRate::where('status', 'active')->where('isDeleted', 0)
            ->where('branch_id', $branchIdToUse)
            ->get();

        $existingProductIds = [];
        if (isset($sales) && !empty($sales->order_items)) {
            foreach ($sales->order_items as $item) {
                if (isset($item->product_id)) {
                    $existingProductIds[] = $item->product_id;
                }
            }
        }

        $products = Product::with(['category', 'unit', 'variants'])
            ->where('branch_id', $branchIdToUse)
            ->where(function ($query) use ($existingProductIds) {
                $query->where(function ($q) {
                    $q->where('status', 'active')
                      ->where('availablility', 'in_stock');
                });
                if (!empty($existingProductIds)) {
                    $query->orWhereIn('id', $existingProductIds);
                }
            })
            ->get();

        $setting = $this->fallbackSetting($branchIdToUse);

        $enableVariants = $setting ? ($setting->enable_variant_product ?? 1) : 1;
        if (!$enableVariants) {
            $products = $products->filter(function($product) use ($existingProductIds) {
                return $product->item_type !== 'variant' || in_array($product->id, $existingProductIds);
            })->values();
        }

        $labourItems = LabourItem::where('created_by', $branchIdToUse)
            ->where('isDeleted', false)
            ->get();
        $banks = BankMaster::where('branch_id', $branchIdToUse)
            ->where('status', 1)
            ->where('isDeleted', 0)
            ->get();

        // Staff list for assignment dropdown (all users)
        $staffList = User::where('role', 'staff')
            ->where('branch_id', $branchIdToUse)
            ->where('isDeleted', 0)
            ->orderBy('name')
            ->get(['id', 'name']);
        $materials = \App\Models\Material::where('branch_id', $branchIdToUse)
            ->where('is_deleted', 0)
            ->with(['types' => function($q) {
                $q->where('is_deleted', 0);
            }])
            ->orderBy('name')
            ->get();

        return view('sales/edit-sales', compact('sales', 'TaxRate', 'category', 'usernames', 'products', 'update_id', 'setting', 'labourItems', 'banks', 'staffList', 'userRole', 'materials'));
    }

    public function sales_details($id)
    {
        $user       = auth()->user();
        $subAdminId = session('selectedSubAdminId');

        // 🔹 Decide branch_id based on role
        if ($user->role === 'staff' && $user->branch_id) {
            $branchIdToUse = $user->branch_id;
        } elseif ($user->role === 'admin' && ! empty($subAdminId)) {
            $branchIdToUse = $subAdminId;
        } else {
            $branchIdToUse = $user->id;
        }

        // 🔹 Get branch-specific setting
        $setting          = $this->fallbackSetting($branchIdToUse);
        $currencySymbol   = $setting->currency_symbol ?? '₹';
        $currencyPosition = $setting->currency_position ?? 'left';

        // 🔹 Load sales with order items + products
        $sales = Order::with(['order_items.product'])->find($id);

        if (! $sales) {
            return redirect()->route('sales.list')->with('error', 'Order not found.');
        }
        $totalPaid = PaymentStore::where('order_id', $id)
            ->where('isDeleted', 0)
            ->sum('payment_amount');

        // ✅ Final payable amount (IMPORTANT)
        $finalAmount =
            ($sales->total_amount ?? 0)
            + ($sales->total_gst ?? 0)
            - ($sales->discount_amount ?? 0);
        // dd($finalAmount);
        // ✅ Pending & Extra calculation
        $pendingAmount = max(0, $finalAmount - $totalPaid);
        $extraPaid     = max(0, $totalPaid - $finalAmount);
        // dd($pendingAmount);
        // dd($extraPaid);
        // ✅ Attach values for Blade
        $sales->final_amount   = $finalAmount;
        $sales->total_paid     = $totalPaid;
        $sales->pending_amount = $pendingAmount;
        $sales->extra_paid     = $extraPaid;

        // dd($totalPaid);
        // dd($extraPaid);

        // 🔹 Company info (branch-specific setting)
        $compenyinfo = $setting;

        // 🔹 Get taxes (safely handle null/empty tax_id)
        // $taxIds = ! empty($sales->tax_id) ? json_decode($sales->tax_id, true) : [];

        // $taxes  = ! empty($taxIds)
        //     ? TaxRate::where('branch_id', $branchIdToUse)
        //     ->whereIn('id', $taxIds)
        //     ->where('isDeleted', 0)
        //     ->get()
        //     : collect();

        // 🔹 Order items & totals
        $orderItems  = OrderItem::with(['product', 'variant'])->where('order_id', $id)->get();
        $totalAmount = $orderItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });


        $view_id = $id; // define view_id for blade
        $user    = $sales->user_id ? User::with('userDetail')->find($sales->user_id) : null;
        $userAddress = $user && $user->userDetail ? $user->userDetail->address : null;
        $userDeliveryAddress = $user && $user->userDetail ? $user->userDetail->delivery_address : null;

        if ($user) {
            $sales->customer_role       = ucfirst($user->role ?? 'Customer');
            $sales->customer_name       = $user->name ?? 'Default-customer';
            $sales->customer_email      = $user->email ?? '';
            $sales->customer_phone      = $user->phone ?? '';
            $sales->customer_address    = optional($user->userDetail)->address ?? '';
            $sales->customer_city       = optional($user->userDetail)->city ?? '';
            $sales->customer_country    = optional($user->userDetail)->country ?? '';
            $sales->customer_gst_number = $user->gst_number ?? '';
            $sales->customer_pan_number = $user->pan_number ?? '';
        } else {
            // default values if no user is linked
            $sales->customer_role       = 'Customer';
            $sales->customer_name       = 'Default-customer';
            $sales->customer_email      = '';
            $sales->customer_phone      = '';
            $sales->customer_address    = '';
            $sales->customer_city       = '';
            $sales->customer_country    = '';
            $sales->customer_gst_number = '';
            $sales->customer_pan_number = '';
        }

        // ✅ Check if payment already started for this order
        $hasPaymentStarted = PaymentStore::where('order_id', $view_id)
            ->where('isDeleted', 0)
            ->exists();

        // ✅ Check if return already started for this order
        $hasReturnStarted = \App\Models\SalesReturn::where('order_id', $view_id)->exists() || \App\Models\RentalReturn::where('order_id', $view_id)->exists();

        $pageTitle = match ($sales->quotation_status ?? 'sales') {
            'quotation'        => 'Sale Quotation Details',
            'advance_receipt'  => 'Advance Receipt Details',
            default            => 'Sale Details',
        };

        return view('sales.sales-details', compact(
            'view_id',
            'sales',
            'totalAmount',
            'compenyinfo',
            'setting',
            'userAddress',
            'userDeliveryAddress',
            'orderItems',
            'currencySymbol',
            'currencyPosition',
            'hasPaymentStarted',
            'hasReturnStarted',
            'pageTitle'
        ));
    }

    public function salse_invoice($id)
    {
        $user       = auth()->user();
        $subAdminId = session('selectedSubAdminId');

        // 🔹 Decide branch_id based on role
        if ($user->role === 'staff' && $user->branch_id) {
            $branchIdToUse = $user->branch_id;
        } elseif ($user->role === 'admin' && ! empty($subAdminId)) {
            $branchIdToUse = $subAdminId;
        } else {
            $branchIdToUse = $user->id;
        }
        $view_id = $id;
        $sales   = Order::find($view_id);
        $setting = $this->fallbackSetting($branchIdToUse); // Get currency info
        // dd($setting);
        if (! $sales) {
            return redirect()->route('sales.list')->with('error', 'Order not found.');
        }
        $totalPaid = PaymentStore::where('order_id', $view_id)
            ->where('isDeleted', 0)
            ->sum('payment_amount');

        $extraPaid = max(0, $totalPaid - ($sales->total_amount ?? 0));

        $sales->total_paid = $totalPaid;
        $sales->extra_paid = $extraPaid;
        // $taxIds      = json_decode($sales->tax_id, true);
        // $taxes       = TaxRate::where('branch_id', $branchIdToUse)->whereIn('id', $taxIds)->where('isDeleted', 0)->get();
        $orderItems  = OrderItem::with(['product', 'variant'])->where('order_id', $view_id)->get();
        $totalAmount = $orderItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $user = $sales->user_id ? User::with('userDetail')->find($sales->user_id) : null;

        $userAddress = $user && $user->userDetail ? $user->userDetail->address : null;
        $userDeliveryAddress = $user && $user->userDetail ? $user->userDetail->delivery_address : null;

        // ✅ Check if payment already started for this order
        $hasPaymentStarted = PaymentStore::where('order_id', $view_id)
            ->where('isDeleted', 0)
            ->exists();

        // ✅ Check if return already started for this order
        $hasReturnStarted = \App\Models\SalesReturn::where('order_id', $view_id)->exists() || \App\Models\RentalReturn::where('order_id', $view_id)->exists();

        $pageTitle = match ($sales->quotation_status ?? 'sales') {
            'quotation'        => 'Sale Quotation Invoice',
            'advance_receipt'  => 'Advance Receipt Invoice',
            default            => 'Sale Invoice',
        };

        return view('sales/salse-invoice', compact('view_id', 'sales', 'totalAmount', 'setting', 'userAddress', 'userDeliveryAddress', 'orderItems', 'hasPaymentStarted', 'hasReturnStarted', 'pageTitle'));
    }

    // Show delivery page for a sale
    public function delivery($id)
    {
        $order = Order::with('order_items.product')->find($id);
        if (! $order) {
            return redirect()->route('sales.list')->with('error', 'Order not found.');
        }

        // Compute delivered qty per order item
        $orderItems = $order->order_items->map(function ($item) {
            $delivered = Delivery::where('order_item_id', $item->id)->where('order_id', $item->order_id)->sum('delivered_quantity');
            $item->delivered_quantity = (float) $delivered;
            $item->remaining_to_deliver = max(0, (float) $item->quantity - (float) $delivered);
            return $item;
        });

        // previous deliveries for this order
        $previousDeliveries = Delivery::where('order_id', $order->id)->with(['orderItem', 'product'])->get();

        // branch/company settings (fallback)
        $user       = auth()->user();
        $subAdminId = session('selectedSubAdminId');
        $branchIdToUse = $user->role === 'staff' && $user->branch_id ? $user->branch_id : ($user->role === 'admin' && ! empty($subAdminId) ? $subAdminId : $user->id);
        $setting = $this->fallbackSetting($branchIdToUse);

        // Attach customer details to $order (so view mirrors sales details)
        $orderUser = $order->user_id ? User::with('userDetail')->find($order->user_id) : null;
        if ($orderUser) {
            $order->customer_role       = ucfirst($orderUser->role ?? 'Customer');
            $order->customer_name       = $orderUser->name ?? 'Default-customer';
            $order->customer_email      = $orderUser->email ?? '';
            $order->customer_phone      = $orderUser->phone ?? '';
            $order->customer_address    = optional($orderUser->userDetail)->address ?? '';
            $order->customer_city       = optional($orderUser->userDetail)->city ?? '';
            $order->customer_country    = optional($orderUser->userDetail)->country ?? '';
            $order->customer_gst_number = $orderUser->gst_number ?? '';
            $order->customer_pan_number = $orderUser->pan_number ?? '';
        } else {
            $order->customer_role       = 'Customer';
            $order->customer_name       = $order->customer_name ?? 'Default-customer';
            $order->customer_email      = $order->customer_email ?? '';
            $order->customer_phone      = $order->customer_phone ?? '';
            $order->customer_address    = $order->customer_address ?? '';
            $order->customer_city       = $order->customer_city ?? '';
            $order->customer_country    = $order->customer_country ?? '';
            $order->customer_gst_number = $order->customer_gst_number ?? '';
            $order->customer_pan_number = $order->customer_pan_number ?? '';
        }

        return view('sales.delivery', compact('order', 'orderItems', 'previousDeliveries', 'setting'));
    }

    // Store delivery entries
    public function storeDelivery(Request $request)
    {
        Log::info('storeDelivery called', ['request' => $request->all()]);
        try {
            $data = $request->validate([
                'order_id' => 'required|integer|exists:orders,id',
                'items' => 'required|array',
                'items.*.order_item_id' => 'required|integer|exists:order_items,id',
                'items.*.delivered_quantity' => 'required|numeric|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('storeDelivery validation failed', ['errors' => $e->errors(), 'request' => $request->all()]);
            throw $e; // rethrow so Laravel handles the redirect/back with errors as usual
        }

        $order = Order::find($data['order_id']);
        if (! $order) {
            return back()->with('error', 'Order not found.');
        }

        DB::beginTransaction();
        try {
            $totalDelivered = 0;
            $createdDeliveryIds = [];
            foreach ($data['items'] as $it) {
                $orderItem = OrderItem::find($it['order_item_id']);
                if (! $orderItem) continue;

                $alreadyDelivered = Delivery::where('order_item_id', $orderItem->id)->where('order_id', $order->id)->sum('delivered_quantity');
                $remaining = max(0, $orderItem->quantity - $alreadyDelivered);
                $toDeliver = (float) $it['delivered_quantity'];

                if ($toDeliver <= 0) continue;
                if ($toDeliver > $remaining) {
                    Log::warning('Attempt to over-deliver', ['order_id' => $order->id, 'order_item_id' => $orderItem->id, 'toDeliver' => $toDeliver, 'remaining' => $remaining]);
                    DB::rollBack();
                    return back()->with('error', "Delivered quantity for product {$orderItem->product_id} cannot exceed remaining quantity.");
                }

                $delivery = Delivery::create([
                    'order_id' => $order->id,
                    'order_item_id' => $orderItem->id,
                    'product_id' => $orderItem->product_id,
                    'delivered_quantity' => $toDeliver,
                    'ordered_quantity' => $orderItem->quantity,
                    'status' => ($toDeliver === $orderItem->quantity) ? 'delivered' : 'partially_delivered',
                    'delivered_by' => auth()->id(),
                    'delivered_at' => now(),
                ]);
                if ($delivery && $delivery->id) {
                    $createdDeliveryIds[] = $delivery->id;
                    Log::info('Delivery created', ['delivery_id' => $delivery->id, 'order_id' => $order->id, 'order_item_id' => $orderItem->id, 'qty' => $toDeliver]);
                } else {
                    Log::warning('Delivery::create returned null or no id', ['order_id' => $order->id, 'order_item_id' => $orderItem->id]);
                }
                $totalDelivered += $toDeliver;
            }

            // Update order delivery_status: delivered / partial / pending
            $allOrdered = $order->order_items->sum('quantity');
            $allDelivered = Delivery::where('order_id', $order->id)->sum('delivered_quantity');
            if ($allDelivered >= $allOrdered) {
                $order->delivery_status = 'delivered';
            } elseif ($allDelivered > 0) {
                $order->delivery_status = 'partial';
            } else {
                $order->delivery_status = 'pending';
            }
            $order->save();

            DB::commit();
            Log::info('storeDelivery completed and committed', ['order_id' => $order->id, 'totalDelivered' => $totalDelivered, 'created' => $createdDeliveryIds]);
            $query = [];
            if (! empty($createdDeliveryIds)) {
                $query['delivery_ids'] = implode(',', $createdDeliveryIds);
            }
            $url = route('sales.delivery.challan.pdf', ['id' => $order->id]);
            if (! empty($query)) {
                $url .= '?' . http_build_query($query);
            }
            return redirect($url)->with('success', 'Delivery recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('storeDelivery exception', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Error saving delivery: ' . $e->getMessage());
        }
    }

    public function getPendingEmis(Request $request)
    {
        $user = auth()->user();
        $selectedSubAdminId = session('selectedSubAdminId');
        $branchId = ! empty($selectedSubAdminId)
            ? (int) $selectedSubAdminId
            : ($user->role === 'staff' && $user->branch_id ? (int) $user->branch_id : (int) $user->id);

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

    public function updateDeliveryStatus(Request $request, Delivery $delivery)
    {
        $user = auth()->user();
        $selectedSubAdminId = session('selectedSubAdminId');
        $branchIdToUse = $user->role === 'staff' && $user->branch_id
            ? $user->branch_id
            : (! empty($selectedSubAdminId) ? $selectedSubAdminId : $user->id);

        $delivery->load('order');
        if (! $delivery->order || (int) $delivery->order->branch_id !== (int) $branchIdToUse) {
            abort(404);
        }

        $allowedStatuses = [
            'pending',
            'delivered',
            'partially_delivered',
            'cancelled',
        ];

        $data = $request->validate([
            'status' => 'required|string|in:' . implode(',', $allowedStatuses),
        ]);

        $delivery->status = $data['status'];
        $delivery->save();

        $delivery->order->delivery_status = $data['status'] === 'partially_delivered'
            ? 'partial'
            : $data['status'];
        $delivery->order->save();

        return response()->json([
            'status' => true,
            'message' => 'Delivery status updated successfully.',
            'data' => [
                'id' => $delivery->id,
                'status' => $delivery->status,
            ],
        ]);
    }

    public function updateOrderDeliveryStatus(Request $request, Order $order)
    {
        $user = auth()->user();
        $selectedSubAdminId = session('selectedSubAdminId');
        $branchIdToUse = $user->role === 'staff' && $user->branch_id
            ? $user->branch_id
            : (! empty($selectedSubAdminId) ? $selectedSubAdminId : $user->id);

        if ((int) $order->branch_id !== (int) $branchIdToUse || (int) $order->isDeleted === 1) {
            abort(404);
        }

        $allowedStatuses = [
            'pending',
            'delivered',
            'partially_delivered',
            'cancelled',
        ];

        $data = $request->validate([
            'status' => 'required|string|in:' . implode(',', $allowedStatuses),
        ]);

        $order->delivery_status = $data['status'] === 'partially_delivered'
            ? 'partial'
            : $data['status'];
        $order->save();

        $latestDelivery = Delivery::where('order_id', $order->id)
            ->latest('created_at')
            ->first();

        if ($latestDelivery) {
            $latestDelivery->status = $data['status'];
            $latestDelivery->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Order delivery status updated successfully.',
            'data' => [
                'id' => $order->id,
                'status' => $data['status'],
            ],
        ]);
    }

    // public function salse_invoice_pdf($id)
    // {
    //     $view_id    = $id;
    //     $sales      = Order::find($view_id);
    //     $user       = Auth::user();
    //     $subAdminId = session('selectedSubAdminId') ?? $user->id;

    //     if ($user->role === 'staff' && $user->branch_id) {
    //         $setting = Setting::where('branch_id', $user->branch_id)->first();
    //     } else {
    //         $setting = Setting::where('branch_id', $subAdminId)->first();
    //     }

    //     if (! $sales) {
    //         return redirect()->route('sales.list')->with('error', 'Order not found.');
    //     }
    //     $labourItems = Sales_Labour_Items::where('order_id', $id)
    //         ->with('labourItem')
    //         ->get();
    //         // dd($labourItems);
    //     $labourCost = 0;
    //     if ($labourItems && $labourItems->isNotEmpty()) {
    //         foreach ($labourItems as $labourItem) {
    //             $labourCost += ($labourItem->qty ?? 0) * ($labourItem->price ?? 0);
    //         }
    //     }

    //     // Fetch user data (assuming 'user_id' in orders table)
    //     $user = $sales->user_id ? User::with('userDetail')->find($sales->user_id) : null;

    //     // Helper function for currency formatting
    //     $formatCurrency = function ($amount) use ($setting) {
    //         $amount = number_format($amount, 2);
    //         return $setting->currency_position === 'right'
    //             ? $amount . $setting->currency_symbol
    //             : $setting->currency_symbol . $amount;
    //     };

    //     // ✅ Subtotal (Amount before GST)
    //     $orderItems = OrderItem::where('order_id', $view_id)->get();
    //     $subtotal   = $orderItems->sum(function ($item) {
    //         return $item->price * $item->quantity;
    //     });

    //     // dd($orderItems);

    //     // ✅ Discount
    //     $discountPercent = $sales->discount ?? 0;
    //     $discountAmount  = ($discountPercent / 100) * $subtotal;
    //     $afterDiscount   = $subtotal - $discountAmount;

    //     // ✅ Tax calculation only if gst_option = 'with'
    //     // $taxDetails = [];
    //     // if ($sales->gst_option === 'with_gst') {
    //     //     $taxIds = json_decode($sales->tax_id, true) ?? [];
    //     //     if (! empty($taxIds)) {
    //     //         $taxes = TaxRate::whereIn('id', $taxIds)->get();
    //     //         foreach ($taxes as $tax) {
    //     //             $taxAmount    = ($tax->tax_rate / 100) * $afterDiscount;
    //     //             $taxDetails[] = [
    //     //                 'name'             => $tax->tax_name,
    //     //                 'rate'             => $tax->tax_rate,
    //     //                 'amount'           => $taxAmount,
    //     //                 'formatted_amount' => $formatCurrency($taxAmount),
    //     //             ];
    //     //         }
    //     //     }
    //     // }
    //     $totalGstAmount = 0;
    //     $taxSummary = [];

    //     foreach ($orderItems as $item) {
    //         $totalGstAmount += (float) ($item->product_gst_total ?? 0);
    //         $gstDetails = $item->product_gst_details;

    //         // Handle legacy/double-encoded JSON payloads.
    //         if (is_string($gstDetails)) {
    //             $gstDetails = json_decode($gstDetails, true);
    //             if (is_string($gstDetails)) {
    //                 $gstDetails = json_decode($gstDetails, true);
    //             }
    //         }

    //         if (is_array($gstDetails) && isset($gstDetails['tax_name'])) {
    //             $gstDetails = [$gstDetails];
    //         }

    //         if (!empty($gstDetails) && is_array($gstDetails)) {
    //             foreach ($gstDetails as $tax) {
    //                 if (!is_array($tax)) {
    //                     continue;
    //                 }

    //                 $taxName = $tax['tax_name'] ?? 'GST';
    //                 $taxRate = $tax['tax_rate'] ?? 0;
    //                 $taxAmount = (float) ($tax['tax_amount'] ?? 0);
    //                 $key = $taxName . '_' . $taxRate;

    //                 if (!isset($taxSummary[$key])) {
    //                     $taxSummary[$key] = [
    //                         'name'   => $taxName,
    //                         'rate'   => $taxRate,
    //                         'amount' => 0,
    //                     ];
    //                 }

    //                 $taxSummary[$key]['amount'] += $taxAmount;
    //             }
    //         }
    //     }

    //     // format GST summary
    //     $taxDetails = [];
    //     foreach ($taxSummary as $tax) {
    //         $taxDetails[] = [
    //             'name'             => $tax['name'],
    //             'rate'             => $tax['rate'],
    //             'amount'           => $tax['amount'],
    //             'formatted_amount' => $formatCurrency($tax['amount']),
    //         ];
    //     }

    //     // final total
    //     $finalTotal = $afterDiscount + $totalGstAmount;


    //     // ✅ Final total
    //     $finalTotal = $afterDiscount + collect($taxDetails)->sum('amount');

    //     // ✅ Prepare formatted values
    //     $formattedSubtotal       = $formatCurrency($subtotal);
    //     $formattedDiscountAmount = $formatCurrency($discountAmount);
    //     $formattedAfterDiscount  = $formatCurrency($afterDiscount);
    //     $formattedFinalTotal     = $formatCurrency($finalTotal);

    //     // ✅ Retrieve customer data
    //     $customer = $user ? [
    //         'name'       => $user->name ?? 'walk-in-customer',
    //         'email'      => $user->email ?? '',
    //         'phone'      => $user->phone ?? '',
    //         'address'    => optional($user->userDetail)->address ?? '',
    //         'gst_number' => $user->gst_number ?? '',
    //         'pan_number' => $user->pan_number ?? '',
    //     ] : [
    //         'name'       => 'walk-in-customer',
    //         'email'      => '',
    //         'phone'      => '',
    //         'address'    => '',
    //         'gst_number' => '',
    //         'pan_number' => '',
    //     ];

    //     $paidAmount = PaymentStore::where('order_id', $sales->id)
    //         ->where('isDeleted', 0)
    //         ->sum('payment_amount');

    //     // ✅ Fetch returns
    //     $returns = \App\Models\SalesReturn::with('items.product')
    //         ->where('order_id', $view_id)
    //         ->get();

    //     // ✅ Pending amount (single source of truth)
    //     $pendingAmount = $sales->remaining_amount ?? 0;

    //     // ✅ Extra Paid calculation
    //         $totalOrderAmount = $sales->total_amount ?? $finalTotal; // fallback safety
    //         $extraPaid = max(0, $paidAmount - $totalOrderAmount);

    //     // ✅ Prepare data for view
    //     $pdfData = [
    //         'view_id'                => $view_id,
    //         'sales'                  => $sales,
    //         'setting'                => $setting,
    //         'orderItems'             => $orderItems,
    //         'salesItems'             => $orderItems,
    //         'labourItems'            => $labourItems,
    //         'returns'                => $returns,
    //         'taxDetails1'            => $taxDetails,
    //         'totalGst'      => $formatCurrency($totalGstAmount),
    //         'finalTotal'    => $formatCurrency($finalTotal),
    //         'customer'               => [
    //             'name'    => $user->name ?? 'walk-in-customer',
    //             'email'   => $user->email ?? '',
    //             'phone'   => $user->phone ?? '',
    //             'pan_number'   => $user->pan_number ?? '',
    //             'gst_number'   => $user->gst_number ?? '',
    //             'address' => optional($user->userDetail)->address ?? 'arga',
    //         ],
    //         'user'                   => $user ? $user->toArray() : null,
    //         'subtotal'               => $formattedSubtotal,
    //         'discountPercent'        => $discountPercent,
    //         'discountAmount'         => $formattedDiscountAmount,
    //         'afterDiscount'          => (float) $afterDiscount,  // numeric
    //         'formattedAfterDiscount' => $formattedAfterDiscount, // formatted
    //         'finalTotal'             => $formattedFinalTotal,
    //         'taxDetails1'            => $taxDetails,
    //         'paidAmount'             => $paidAmount,
    //         'pendingAmount'          => $pendingAmount,
    //         'extraPaid'              => $extraPaid,
    //     ];
    //     // dd($pdfData);

    //     // ✅ Load and render PDF
    //     // $pdf = PDF::loadView('sales.salse-invoice-pdf', $pdfData);

    //     // return $pdf->stream('invoice_' . $view_id . '.pdf');
    //     // ===== Invoice size condition =====
    //         if ($setting && $setting->invoice_size === 'small') {

    //             $pdf = PDF::loadView('sales.salse-invoice-small-pdf', $pdfData)
    //                     ->setPaper('A5', 'portrait');

    //         } else {

    //             $pdf = PDF::loadView('sales.salse-invoice-pdf', $pdfData)
    //                     ->setPaper('A4', 'portrait');
    //         }
    //         // ==================================

    //     return $pdf->stream('invoice_' . $view_id . '.pdf');
    // }
    public function salse_invoice_pdf($id)
    {
        $view_id    = $id;
        $sales      = Order::find($view_id);

        if (! $sales) {
            abort(404, 'Order not found.');
        }

        if (($sales->quotation_status ?? '') === 'quotation') {
            // abort(404, 'Invoice not available.'); // Allow quotation PDF download
        }

        $setting = $this->fallbackSetting($sales->branch_id ?? null);

        $labourItems = Sales_Labour_Items::where('order_id', $id)
            ->with('labourItem')
            ->get();

        $labourCost = 0;
        if ($labourItems && $labourItems->isNotEmpty()) {
            foreach ($labourItems as $labourItem) {
                $labourCost += ($labourItem->qty ?? 0) * ($labourItem->price ?? 0);
            }
        }

        // Fetch user data
        $user = $sales->user_id ? User::with('userDetail')->find($sales->user_id) : null;

        // Helper function for currency formatting
        $formatCurrency = function ($amount) use ($setting) {
            $amount = number_format($amount, 2);
            return $setting->currency_position === 'right'
                ? $amount . $setting->currency_symbol
                : $setting->currency_symbol . $amount;
        };

        // Subtotal
        $orderItems = OrderItem::with(['product.unit', 'variant'])->where('order_id', $view_id)->get();
        $subtotal   = $orderItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        // Discount
        $discountPercent = $sales->discount ?? 0;
        $discountAmount  = ($discountPercent / 100) * $subtotal;
        $afterDiscount   = $subtotal - $discountAmount;

        // Calculate GST
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
                            'name'   => $taxName,
                            'rate'   => $taxRate,
                            'amount' => 0,
                        ];
                    }

                    $taxSummary[$key]['amount'] += $taxAmount;
                }
            }
        }

        // Format GST summary
        $taxDetails = [];
        foreach ($taxSummary as $tax) {
            $taxDetails[] = [
                'name'             => $tax['name'],
                'rate'             => $tax['rate'],
                'amount'           => $tax['amount'],
                'formatted_amount' => $formatCurrency($tax['amount']),
            ];
        }

        // Calculate Return Amount
        $totalReturnAmount = 0;
        $returns = \App\Models\SalesReturn::with('items.product')
            ->where('order_id', $view_id)
            ->get();

        $allItemsFullyReturned = false;

        if ($returns->isNotEmpty()) {
            foreach ($returns as $ret) {
                $totalReturnAmount += (float) ($ret->total_amount ?? 0);
            }

            // Check if all items are fully returned
            $orderItemsQuantities = [];
            foreach ($orderItems as $item) {
                $orderItemsQuantities[$item->id] = $item->quantity;
            }

            $returnedQuantities = [];
            foreach ($returns as $ret) {
                foreach ($ret->items as $retItem) {
                    if (!isset($returnedQuantities[$retItem->order_item_id])) {
                        $returnedQuantities[$retItem->order_item_id] = 0;
                    }
                    $returnedQuantities[$retItem->order_item_id] += $retItem->quantity;
                }
            }

            $allItemsFullyReturned = true;
            foreach ($orderItemsQuantities as $orderItemId => $originalQty) {
                $returnedQty = $returnedQuantities[$orderItemId] ?? 0;
                if ($returnedQty < $originalQty) {
                    $allItemsFullyReturned = false;
                    break;
                }
            }
        }

        // Get shipping charge
        $shippingCharge = (float) ($sales->shipping ?? 0);

        // Calculate return amount with shipping if fully returned
        $totalReturnWithShipping = $totalReturnAmount;
        if ($allItemsFullyReturned && $totalReturnAmount > 0) {
            $totalReturnWithShipping = $totalReturnAmount + $shippingCharge;
        }

        // Final total
        $finalTotal = $afterDiscount + $totalGstAmount + $shippingCharge + $labourCost;

        $allPayments = PaymentStore::where('order_id', $sales->id)
            ->where('isDeleted', 0)
            ->orderBy('created_at', 'asc')
            ->get();

        $paidAmount = $allPayments->sum('payment_amount');

        $emiPayments = PaymentStore::where('order_id', $sales->id)
            ->where('isDeleted', 0)
            ->where(function ($query) {
                $query->where('payment_method', 'emi')
                    ->orWhere('payment_type', 'emi');
            })
            ->whereNotNull('emi_month')
            ->orderBy('emi_month')
            ->get();

        // Pending amount = Final Total - Total Returns - Paid Amount
        $pendingAmount = max(0, $finalTotal - $totalReturnWithShipping - $paidAmount);

        // Extra Paid calculation
        $extraPaid = max(0, $paidAmount - ($finalTotal - $totalReturnWithShipping));

        // Prepare formatted values
        $formattedSubtotal       = $formatCurrency($subtotal);
        $formattedDiscountAmount = $formatCurrency($discountAmount);
        $formattedAfterDiscount  = $formatCurrency($afterDiscount);
        $formattedTotalGstAmount = $formatCurrency($totalGstAmount);
        $formattedShippingCharge = $formatCurrency($shippingCharge);
        $formattedLabourCost     = $formatCurrency($labourCost);
        $formattedFinalTotal     = $formatCurrency($finalTotal);
        $formattedReturnAmount   = $formatCurrency($totalReturnWithShipping);
        $formattedPaidAmount     = $formatCurrency($paidAmount);
        $formattedPendingAmount  = $formatCurrency($pendingAmount);
        $formattedExtraPaid      = $formatCurrency($extraPaid);

        // Determine return status
        $returnStatus = 'No return';
        $returnStatusColor = '#28c76f';
        if ($totalReturnAmount > 0) {
            if ($totalReturnWithShipping >= $finalTotal) {
                $returnStatus = 'Fully Returned';
                $returnStatusColor = '#ea5455';
            } else {
                $returnStatus = 'Partially Returned';
                $returnStatusColor = '#ff9f43';
            }
        }

        // Prepare customer data (name/phone must print on thermal bill)
        $customerName = trim((string) (
            ($user?->company_name ?? '')
            ?: ($user?->name ?? '')
            ?: ($sales->customer_name ?? '')
            ?: 'Default-customer'
        ));
        $customerPhone = trim((string) (
            ($user?->phone ?? '')
            ?: ($sales->customer_phone ?? '')
        ));
        $customer = [
            'name'       => trim((string) ($user?->name ?? '')) ?: $customerName,
            'company_name' => trim((string) ($user?->company_name ?? '')),
            'email'      => $user?->email ?? '',
            'phone'      => $customerPhone,
            'address'    => optional($user?->userDetail)->address ?? '',
            'delivery_address' => optional($user?->userDetail)->delivery_address ?? '',
            'gst_number' => $user?->gst_number ?? '',
            'pan_number' => $user?->pan_number ?? '',
        ];

        // dd($formattedPaidAmount);
        // Prepare data for view
        $pdfData = [
            'view_id'                => $view_id,
            'sales'                  => $sales,
            'setting'                => $setting,
            'orderItems'             => $orderItems,
            'salesItems'             => $orderItems,
            'labourItems'            => $labourItems,
            'returns'                => $returns,
            'taxDetails1'            => $taxDetails,
            'totalGst'               => $formattedTotalGstAmount,
            'finalTotal'             => $formattedFinalTotal,
            'subtotal'               => $formattedSubtotal,
            'discountPercent'        => $discountPercent,
            'discountAmount'         => $formattedDiscountAmount,
            'afterDiscount'          => (float) $afterDiscount,
            'formattedAfterDiscount' => $formattedAfterDiscount,
            'shippingCharge'         => $formattedShippingCharge,
            'labourCost'             => $formattedLabourCost,
            'returnAmount'           => $formattedReturnAmount,
            'returnStatus'           => $returnStatus,
            'returnStatusColor'      => $returnStatusColor,
            'totalReturnAmount'      => $totalReturnWithShipping,
            'allItemsFullyReturned'  => $allItemsFullyReturned,
            'customer'               => $customer,
            'user'                   => $user ? $user->toArray() : null,
            'paidAmount' => $paidAmount,
            'pendingAmount'          => $formattedPendingAmount,
            'extraPaid'              => $formattedExtraPaid,
            'pendingAmountNumeric'   => $pendingAmount,
            'emiPayments'            => $emiPayments,
            'allPayments'            => $allPayments,
        ];

        $inv = invoice_custom($setting);
        $printHindi = request()->boolean('hindi');
        if ($printHindi) {
            foreach ($orderItems as $item) {
                $product = $item->product ?? null;
                if (! $product && ! empty($item->product_id)) {
                    $product = \App\Models\Product::find($item->product_id);
                    if ($product) {
                        $item->setRelation('product', $product);
                    }
                }
                if ($product) {
                    ensure_product_hindi_name($product);
                }
            }
        }
        $pdfData['inv'] = $inv;
        $pdfData['printHindi'] = $printHindi;
        $pdfData['dp'] = max(0, min(4, (int) ($inv['decimal_places'] ?? 2)));

        if (request()->boolean('bt_receipt')) {
            $btItems = [];
            foreach ($orderItems as $item) {
                $qty = (float) $item->quantity;
                $price = (float) $item->price;
                $btItems[] = [
                    'name' => (string) ($item->product->name ?? 'Item'),
                    'qty' => $qty,
                    'unit' => (string) ($item->product->unit->short_name ?? ''),
                    'price' => $price,
                    'amount' => $price * $qty,
                ];
            }
            if (isset($labourItems)) {
                foreach ($labourItems as $labour) {
                    $qty = (float) $labour->qty;
                    $price = (float) $labour->price;
                    $btItems[] = [
                        'name' => (string) ($labour->labourItem->item_name ?? 'Labour'),
                        'qty' => $qty,
                        'unit' => '',
                        'price' => $price,
                        'amount' => $price * $qty,
                    ];
                }
            }

            return response()->json([
                'shop' => (string) ($setting->name ?? ''),
                'phone' => (string) ($setting->phone ?? ''),
                'gst' => (string) ($setting->gst_num ?? ''),
                'invoice' => (string) ($sales->order_number ?? ''),
                'date' => $sales->created_at ? $sales->created_at->timezone('Asia/Kolkata')->format('d/m/Y h:i A') : '',
                'payment' => (string) ($sales->payment_method ?? ''),
                'items' => $btItems,
                'total' => (float) ($sales->total_amount ?? 0),
                'paid' => (float) $paidAmount,
                'fast' => $this->fastThermalReceipt($pdfData),
            ]);
        }

        // Load and render PDF
        $printMode = request('print_mode');
        $useThermal = in_array($printMode, ['regular', 'thermal'], true)
            ? $printMode === 'thermal'
            : ($setting && $setting->invoice_size === 'small');
        if ($useThermal) {
            $template = request('template');
            if ($template === null || $template === '') {
                $template = (string) ($inv['preferred_template'] ?? 1);
            }
            $viewName = 'sales.salse-invoice-small-pdf';
            if ($template == '2') {
                $viewName = 'sales.salse-invoice-small-pdf-2';
            } elseif ($template == '3') {
                $viewName = 'sales.salse-invoice-small-pdf-3';
            } elseif ($template == '4') {
                $viewName = 'sales.salse-invoice-small-pdf-4';
            } elseif ($template == '5') {
                $viewName = 'sales.salse-invoice-small-pdf-5';
            }
            $thermalLines = count($orderItems) + count($labourItems ?? []);
            $pdf = make_thermal_invoice_pdf($viewName, $pdfData, $thermalLines);
        } else {
            [$paperSize, $paperOrientation] = invoice_regular_paper($setting);
            $pdf = PDF::loadView('sales.salse-invoice-pdf', $pdfData)
                ->setPaper($paperSize, $paperOrientation);
        }

        return $pdf->stream('invoice_' . $view_id . '.pdf');
    }

    /**
     * Structured copy of the small thermal invoice for native ESC/POS text printing over Bluetooth.
     */
    private function fastThermalReceipt(array $pdfData): array
    {
        $sales = $pdfData['sales'];
        $setting = $pdfData['setting'];
        $orderItems = $pdfData['orderItems'] ?? [];
        $labourItems = $pdfData['labourItems'] ?? [];
        extract(invoice_thermal_view_data($pdfData));

        $fmtQty = function ($q) {
            $q = (float) $q;
            return fmod($q, 1.0) == 0.0 ? number_format($q, 0) : number_format($q, 2);
        };
        $grouping = ! array_key_exists('amount_grouping', $inv) || ! empty($inv['amount_grouping']);
        $wordsFormat = ($inv['amount_words_format'] ?? 'indian') === 'international' ? 'international' : 'indian';
        $money = function ($v) use ($dp, $grouping, $wordsFormat) {
            return invoice_money((float) $v, $dp, $grouping, $wordsFormat);
        };
        $invOn = function (string $key) use ($inv) {
            return ! array_key_exists($key, $inv) || ! empty($inv[$key]);
        };

        $subTotal = 0.0;
        $totalQty = 0.0;
        $items = [];
        $serial = 0;
        foreach ($orderItems as $item) {
            $qty = (float) $item->quantity;
            $subTotal += ((float) $item->price) * $qty;
            $totalQty += $qty;
            $lineMrp = (float) ($item->mrp ?? $item->price);
            $lineSp = $useMrpAsSp ? $lineMrp : (float) $item->price;
            $extra = [];
            if (! empty($inv['show_item_barcode']) && ! empty($item->product->barcode)) {
                $extra[] = (string) $item->product->barcode;
            }
            if (! empty($inv['show_hsn']) && ! empty($item->product->hsn_code ?? $item->product->hsn ?? null)) {
                $extra[] = 'HSN: '.($item->product->hsn_code ?? $item->product->hsn);
            }
            if (! empty($inv['product_second_line']) && ! empty($item->product->short_name ?? $item->variant->name ?? null)) {
                $extra[] = (string) ($item->product->short_name ?? ($item->variant->name ?? ''));
            }
            if (! empty($inv['savings_in_line_item']) && $lineMrp > $lineSp) {
                $extra[] = 'Saved '.$money(($lineMrp - $lineSp) * $qty);
            }
            $discountAmt = (float) ($item->discount_amount ?? 0);
            $items[] = [
                'no' => ++$serial,
                'name' => (string) $productName($item->product ?? 'Product'),
                'qty' => $fmtQty($qty).($invOn('show_unit') ? ($item->product->unit->short_name ?? 'Pcs') : ''),
                'mrp' => $money($lineMrp),
                'sp' => $money($lineSp),
                'amount' => $money($lineSp * $qty),
                'extra' => $extra,
                'disc' => $discountAmt > 0
                    ? 'Disc.('.rtrim(rtrim(number_format((float) ($item->discount_percentage ?? 0), 2), '0'), '.').'%)'
                    : '',
                'discAmt' => $discountAmt > 0 ? '-'.$money($discountAmt) : '',
            ];
        }
        $labourTotal = 0.0;
        foreach ($labourItems as $labour) {
            $qty = (float) $labour->qty;
            $amt = ((float) $labour->price) * $qty;
            $labourTotal += $amt;
            $totalQty += $qty;
            $items[] = [
                'no' => ++$serial,
                'name' => (string) ($labour->labourItem->item_name ?? 'Labour'),
                'qty' => $fmtQty($qty),
                'mrp' => '',
                'sp' => $money($labour->price),
                'amount' => $money($amt),
                'extra' => [],
                'disc' => '',
                'discAmt' => '',
            ];
        }

        $grandTotal = isset($sales->total_amount) ? (float) $sales->total_amount : ($subTotal + $labourTotal);
        $paidAmount = (float) ($pdfData['paidAmount'] ?? 0);
        $pendingAmount = $grandTotal - $paidAmount;
        $payRaw = trim((string) ($paymentModeLabel ?: ($sales->payment_method ?? 'Cash')));
        $thanks = trim((string) ($setting->footer_text ?? '')) ?: 'Thanks for business with us!';
        $priceLabel = trim((string) $spLabel);
        if ($priceLabel === '' || strcasecmp($priceLabel, 'SP') === 0) {
            $priceLabel = 'Price';
        }

        $cust = is_array($pdfData['customer'] ?? null) ? $pdfData['customer'] : [];
        $customerLines = [];
        $custName = trim((string) (($cust['company_name'] ?? '') ?: ($cust['name'] ?? '')));
        $custPhone = trim((string) ($cust['phone'] ?? ''));
        if (! empty($inv['show_customer_name']) && $custName !== '') {
            $customerLines[] = 'Customer: '.$custName;
        }
        if (! empty($inv['show_customer_phone']) && $custPhone !== '') {
            $customerLines[] = 'Ph. No.: '.$custPhone;
        }
        if (! empty($inv['show_customer_address']) && ($cust['address'] ?? '') !== '') {
            $customerLines[] = (string) $cust['address'];
        }
        $custGst = $cust['gst_number'] ?? '';
        if (! empty($inv['show_customer_gst']) && $custGst !== '') {
            $customerLines[] = 'Cust. GST: '.$custGst;
        }
        if (! empty($inv['show_salesman']) && $salesmanName !== '') {
            $customerLines[] = 'Salesman: '.$salesmanName;
        }

        $savings = '';
        if (! empty($inv['show_savings']) && $invSavingsAmount > 0) {
            $savings = ($inv['savings_label'] ?? 'You Saved').' Rs '.$money($invSavingsAmount);
            if ($invSavingsPct > 0) {
                $savings .= ' ('.rtrim(rtrim(number_format($invSavingsPct, 1), '0'), '.').'%)';
            }
            $savings .= ' on this purchase';
        }
        $paymentLine = '';
        if (! empty($inv['show_payment_mode']) && $paymentModeLabel !== '') {
            $paymentLine = 'Payment Mode: '.$paymentModeLabel.($paidAmount ? ': Rs '.$money($paidAmount) : '');
        }
        $heading = (! empty($inv['enable_custom_heading']) && trim((string) ($inv['custom_heading_text'] ?? '')) !== '')
            ? (string) $inv['custom_heading_text']
            : (! empty($inv['bill_of_supply']) ? 'Bill of Supply' : 'Invoice');
        $fssai = $setting->fssai_number ?? $setting->fssai ?? null;

        $taxLines = [];
        if (! empty($inv['show_tax_details'])) {
            $taxRows = [];
            foreach ($orderItems as $item) {
                $gst = $item->product_gst_details ?? null;
                if (is_string($gst)) {
                    $gst = json_decode($gst, true);
                    if (is_string($gst)) {
                        $gst = json_decode($gst, true);
                    }
                }
                if (is_array($gst) && isset($gst['tax_name'])) {
                    $gst = [$gst];
                }
                if (! is_array($gst)) {
                    continue;
                }
                $line = (float) ($item->price ?? 0) * (float) ($item->quantity ?? 0);
                foreach ($gst as $g) {
                    $rate = (float) ($g['tax_rate'] ?? 0);
                    if ($rate <= 0) {
                        continue;
                    }
                    $key = ($g['tax_name'] ?? 'GST').' '.rtrim(rtrim(number_format($rate, 2), '0'), '.').'%';
                    $taxRows[$key] = ($taxRows[$key] ?? 0) + round($line * $rate / 100, 2);
                }
            }
            foreach ($taxRows as $label => $amt) {
                $taxLines[] = ['label' => $label, 'value' => $money($amt)];
            }
        }

        return [
            'cols' => ! empty($is2inch) ? 32 : 48,
            'fontPx' => $fontPx,
            'logo' => $logoBase64,
            'logoPx' => $logoHeightPx,
            'shop' => $invOn('show_company_name') ? (string) ($setting->name ?? '') : '',
            'address' => (! empty($inv['show_shop_address']) && trim((string) ($setting->address ?? '')) !== '') ? trim((string) $setting->address) : '',
            'phone' => ($invOn('show_shop_phone') && ! empty($setting->phone)) ? 'Ph.No.: '.$setting->phone : '',
            'email' => (! empty($inv['show_shop_email']) && trim((string) ($setting->email ?? '')) !== '') ? 'Email: '.trim((string) $setting->email) : '',
            'gst' => (! empty($inv['show_shop_gst']) && ! empty($setting->gst_num)) ? 'GST: '.$setting->gst_num : '',
            'fssai' => (! empty($inv['show_fssai']) && ! empty($fssai)) ? 'FSSAI: '.$fssai : '',
            'heading' => $heading,
            'saleLabel' => ($payRaw === '' || strcasecmp($payRaw, 'cash') === 0) ? 'Cash Sale' : $payRaw,
            'date' => 'Date: '.date('d/m/Y', strtotime($sales->created_at)),
            'time' => 'Time: '.date('h:i A', strtotime($sales->created_at)),
            'customer' => $customerLines,
            'show' => [
                'serial' => ! empty($inv['show_serial']),
                'qty' => ! empty($inv['show_item_qty']),
                'mrp' => ! empty($inv['show_item_mrp']),
                'sp' => ! empty($inv['show_item_sp']),
                'total' => ! empty($inv['show_item_total']),
            ],
            'priceLabel' => $priceLabel,
            'items' => $items,
            'totalLabel' => ! empty($inv['show_total_item_count']) ? 'Total: '.$fmtQty($totalQty) : 'Total',
            'subTotal' => $money($subTotal + $labourTotal),
            'grandTotal' => $money($grandTotal),
            'paid' => $invOn('show_received') ? $money($paidAmount) : '',
            'balance' => ($invOn('show_balance') && $pendingAmount > 0) ? $money($pendingAmount) : '',
            'taxLines' => $taxLines,
            'amountWords' => (! empty($inv['show_amount_words']) && $grandTotal > 0)
                ? 'Amount in words: '.invoice_amount_words(round($grandTotal), $wordsFormat)
                : '',
            'remarks' => (! empty($inv['show_remarks']) && trim((string) ($sales->remarks ?? '')) !== '')
                ? 'Remarks: '.trim((string) $sales->remarks)
                : '',
            'bold' => $invOn('text_styling'),
            'extraLines' => max(0, min(10, (int) ($inv['extra_end_lines'] ?? 0))),
            'cashDrawer' => ! empty($inv['open_cash_drawer']),
            'savings' => $savings,
            'paymentLine' => $paymentLine,
            'outstanding' => (! empty($inv['show_customer_outstanding']) && $pendingAmount > 0)
                ? 'Outstanding: Rs '.$money($pendingAmount)
                : '',
            'qr' => $qrBase64,
            'thanks' => $thanks,
            'terms' => ! empty($inv['enable_terms']) ? trim((string) ($setting->terms_condition_eng ?? '')) : '',
        ];
    }

    // Show delivery challan (printable). If `delivery_ids` query param provided, show only those deliveries.
    public function deliveryChallan($id, Request $request)
    {
        $order = Order::with(['user.userDetail', 'order_items.product'])->find($id);
        if (! $order) {
            return redirect()->route('sales.list')->with('error', 'Order not found.');
        }

        $this->attachOrderCustomerDetails($order);
        $setting = $this->fallbackSetting($order->branch_id ?? auth()->user()->branch_id ?? null);

        $deliveryIds = $request->query('delivery_ids');
        if ($deliveryIds) {
            $ids = array_filter(array_map('trim', explode(',', $deliveryIds)));
            $deliveries = Delivery::whereIn('id', $ids)
                ->with(['orderItem.product.unit', 'product.unit', 'deliveredBy'])
                ->get();
        } else {
            $deliveries = Delivery::where('order_id', $order->id)
                ->with(['orderItem.product.unit', 'product.unit', 'deliveredBy'])
                ->orderByDesc('id')
                ->limit(20)
                ->get();
        }

        return view('sales.delivery-challan', compact('order', 'deliveries', 'setting'));
    }

    // Generate delivery challan PDF
    public function deliveryChallanPdf($id, Request $request)
    {
        $order = Order::with(['user.userDetail', 'order_items.product'])->find($id);
        if (! $order) {
            return redirect()->route('sales.list')->with('error', 'Order not found.');
        }

        $this->attachOrderCustomerDetails($order);
        $setting = $this->fallbackSetting($order->branch_id ?? auth()->user()->branch_id ?? null);

        $deliveryIds = $request->query('delivery_ids');
        if ($deliveryIds) {
            $ids = array_filter(array_map('trim', explode(',', $deliveryIds)));
            $deliveries = Delivery::whereIn('id', $ids)
                ->with(['orderItem.product.unit', 'product.unit', 'deliveredBy'])
                ->get();
        } else {
            $deliveries = Delivery::where('order_id', $order->id)
                ->with(['orderItem.product.unit', 'product.unit', 'deliveredBy'])
                ->orderByDesc('id')
                ->limit(20)
                ->get();
        }

        $data = [
            'order' => $order,
            'deliveries' => $deliveries,
            'setting' => $setting,
            'challan_number' => 'DC-' . $order->id,
        ];

        $pdf = Pdf::loadView('sales.delivery-challan-pdf', $data)->setPaper('A4', 'portrait');
        return $pdf->stream('delivery_challan_' . $order->id . '.pdf');
    }


    public function sales_report(Request $request)
    {
        $user         = Auth()->user();
        $branchId     = $user->id ?? null;
        $UserBranchId = $user->branch_id ?? null;
        $userRole     = $user->role ?? '';
        $subAdminId   = session('selectedSubAdminId');

        // Decide branch based on role
        if ($userRole === 'sub-admin') {
            $branchIdToUse = $branchId;
        } elseif ($userRole === 'admin' && $subAdminId) {
            $branchIdToUse = $subAdminId;
        } elseif ($userRole === 'staff') {
            $branchIdToUse = $UserBranchId;
        } else {
            $branchIdToUse = $branchId;
        }

        $customersQuery = User::where('role', 'customer')
            ->where('branch_id', $branchIdToUse)
            ->where('isDeleted', 0)
            ->orderBy('name');
        StaffDepartmentScope::applyStaffCreatedByScope($customersQuery, $user);
        $customers = $customersQuery->get();

        // ✅ Fetch categories based on branch
        if ($userRole === 'staff') {
            $categories = Category::where('isDeleted', 0)
                ->where('branch_id', $UserBranchId)
                ->orderBy('name')
                ->get();
        } else {
            $categories = Category::where('isDeleted', 0)
                ->where('branch_id', $branchIdToUse)
                ->orderBy('name')
                ->get();
        }

        // ✅ Fetch brands based on branch
        $brands = \App\Models\Brand::where('isDeleted', 0)
            ->where('branch_id', $branchIdToUse)
            ->orderBy('name')
            ->get();

        $staffList = collect();
        if (in_array($userRole, ['admin', 'sub-admin'], true)) {
            $staffList = User::where('role', 'staff')
                ->where('isDeleted', 0)
                ->where('branch_id', $branchIdToUse)
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        // ✅ Fetch products based on branch (only products that exist in orders)
        $productIds = \App\Models\OrderItem::where('isDeleted', 0)
            ->whereHas('order', function ($q) use ($branchIdToUse) {
                $q->where('branch_id', $branchIdToUse)->where('isDeleted', 0);
            })
            ->distinct()
            ->pluck('product_id');

        $products = \App\Models\Product::whereIn('id', $productIds)
            ->where('isDeleted', 0)
            ->orderBy('name')
            ->get();

        return view('sales/salesreport', compact('customers', 'categories', 'brands', 'staffList', 'products'));
    }
    public function pos()
    {
        $user       = auth()->user();
        $userRole   = $user->role ?? '';
        $userId     = $user->id ?? null;
        $branchId   = $user->branch_id ?? null;
        $subAdminId = session('selectedSubAdminId');

        // 🔹 Decide which branch_id to use
        if ($userRole === 'sub-admin' && $userId) {
            $branchIdToUse = $userId;
        } elseif ($userRole === 'admin' && ! empty($subAdminId)) {
            $branchIdToUse = $subAdminId;
        } elseif ($userRole === 'staff' && $branchId) {
            $branchIdToUse = $branchId;
        } else {
            $branchIdToUse = $userId;
        }
        // dd($branchIdToUse);
        // 🔹 Get settings
        $setting           = Setting::where('branch_id', $branchIdToUse)->first();
        $currency_symbol   = $setting->currency_symbol ?? '₹';
        $currency_position = $setting->currency_position ?? 'left';

        // 🔹 Common queries
        $categories = Category::where('isDeleted', 0)
            ->where('branch_id', $branchIdToUse)
            ->orderBy('id', 'desc')
            ->get();
        $taxRates = TaxRate::where('status', 'active')->where('isDeleted', 0)
            ->where('branch_id', $branchIdToUse)
            ->get();
        $taxes = $taxRates;

        $brands = \App\Models\Brand::where('isDeleted', 0)
            ->where('branch_id', $branchIdToUse)
            ->orderBy('id', 'desc')
            ->get();

        $units = \App\Models\Unit::where('is_delete', 0)
            ->where('created_by', $branchIdToUse)
            ->orderBy('id', 'desc')
            ->get();

        // 🔹 Banks
        $banks = BankMaster::where('branch_id', $branchIdToUse)
            ->where('status', 1)
            ->where('isDeleted', 0)
            ->get();

        // $customers = User::where('role', 'customer')
        //     ->where('branch_id', $branchIdToUse)
        //     ->where('isDeleted', 0)
        //     ->get();
        $customersQuery = User::where('role', 'customer')
            ->where('branch_id', $branchIdToUse)
            ->where('isDeleted', 0);
        StaffDepartmentScope::applyStaffCreatedByScope($customersQuery, $user);
        $customers = $customersQuery->get();
        // dd($customers);
        // 🔹 Vendors only for Staff or default case
        $vendors = collect(); // empty collection if not needed
        if ($userRole === 'staff' || $userRole === 'admin' || $userRole === 'sub-admin') {
            $vendors = User::where('role', 'vendor')
                ->where('branch_id', $branchIdToUse)
                ->where('isDeleted', 0)
                ->get();
        }

        // Staff list for assignment dropdown (all users)
        $staffList = User::where('role', 'staff')
            ->where('branch_id', $branchIdToUse)
            ->where('isDeleted', 0)
            ->orderBy('name')
            ->get(['id', 'name']);

        $materials = \App\Models\Material::where('branch_id', $branchIdToUse)
            ->where('is_deleted', 0)
            ->with(['types' => function($q) {
                $q->where('is_deleted', 0);
            }])
            ->orderBy('name')
            ->get();

        $isPosEdit = request()->filled('edit_order_id');
        $convertOrderId = request('convert_order_id') ?: request('edit_order_id');
        $convertOrder = null;
        $convertOrderAdvancePaid = 0;
        if ($convertOrderId) {
            $convertOrder = \App\Models\Order::with(['user', 'exchangeMaterial', 'exchangeType', 'order_items' => function ($query) {
                $query->select(
                    'id',
                    'order_id',
                    'product_id',
                    'product_gst_details',
                    'product_gst_total',
                    'quantity',
                    'price',
                    'discount_percentage',
                    'discount_amount',
                    'total_amount',
                    'imei_no'
                );
            }, 'order_items.product'])->find($convertOrderId);

            if ($convertOrder && !$isPosEdit) {
                // Only converting quotation/advance → sale treats prior payments as advance.
                // Editing a sale must not: those rows are this bill's payment, not an advance.
                $convertOrderAdvancePaid = \App\Models\PaymentStore::where('order_id', $convertOrderId)
                    ->where(function ($q) {
                        $q->whereNull('isDeleted')->orWhere('isDeleted', 0);
                    })
                    ->sum('payment_amount');
            }
        }

        // 🔹 Generate preview next order numbers for display on POS page
        $salePrefix = 'SI/HO/';
        $nextSaleSeq = (int) (Order::query()
            ->where('quotation_status', 'sales')
            ->where('isDeleted', 0)
            ->whereRaw('UPPER(order_number) REGEXP ?', ['^SI/HO/[0-9]+$'])
            ->selectRaw('MAX(CAST(SUBSTRING_INDEX(order_number, "/", -1) AS UNSIGNED)) as max_sequence')
            ->value('max_sequence') ?? 0) + 1;
        $nextSaleNo = $salePrefix . $nextSaleSeq;

        $nextQuotationSeq = (int) (Order::query()
            ->where('quotation_status', 'quotation')
            ->where('isDeleted', 0)
            ->whereRaw("order_number REGEXP '^Q-[0-9]+$'")
            ->selectRaw("MAX(CAST(SUBSTRING(order_number, 3) AS UNSIGNED)) as max_sequence")
            ->value('max_sequence') ?? 0) + 1;
        $nextQuotationNo = 'Q-' . $nextQuotationSeq;

        $nextAdvanceSeq = (int) (Order::query()
            ->where('quotation_status', 'advance_receipt')
            ->where('isDeleted', 0)
            ->whereRaw("order_number REGEXP '^ADV-[0-9]+$'")
            ->selectRaw("MAX(CAST(SUBSTRING(order_number, 5) AS UNSIGNED)) as max_sequence")
            ->value('max_sequence') ?? 0) + 1;
        $nextAdvanceNo = 'ADV-' . $nextAdvanceSeq;

        $nextRentalSeq = (int) (Order::query()
            ->where('quotation_status', 'rental')
            ->where('isDeleted', 0)
            ->whereRaw("order_number REGEXP '^RO-[0-9]+$'")
            ->selectRaw("MAX(CAST(SUBSTRING(order_number, 4) AS UNSIGNED)) as max_sequence")
            ->value('max_sequence') ?? 0) + 1;
        $nextRentalNo = 'RO-' . str_pad($nextRentalSeq, 6, '0', STR_PAD_LEFT);

        return view('sales.pos', compact(
            'categories',
            'taxRates',
            'taxes',
            'brands',
            'units',
            'customers',
            'vendors',
            'currency_symbol',
            'currency_position',
            'setting',
            'banks',
            'staffList',
            'userRole',
            'convertOrder',
            'convertOrderAdvancePaid',
            'isPosEdit',
            'nextSaleNo',
            'nextQuotationNo',
            'nextAdvanceNo',
            'nextRentalNo',
            'materials'
        ));
    }

    public function sale_report($ids)
    {
        $authUser   = auth()->user();
        $subAdminId = session('selectedSubAdminId');

        // Decide branch_id based on role
        if ($authUser->role === 'staff' && $authUser->branch_id) {
            $branchIdToUse = $authUser->branch_id;
        } elseif ($authUser->role === 'admin' && ! empty($subAdminId)) {
            $branchIdToUse = $subAdminId;
        } else {
            $branchIdToUse = $authUser->id;
        }

        $idsArray = explode(',', $ids);

        // Eager load related models
        $sales = OrderItem::with(['product.category', 'variant', 'invoice', 'user.userDetail'])
            ->whereIn('id', $idsArray)
            ->get();

        if ($sales->isEmpty()) {
            return redirect()->route('sales.index')->with('error', 'No sales data found.');
        }

        // Get settings
        $settings         = Setting::where('branch_id', $branchIdToUse)->first();
        $currencySymbol   = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';

        // Process each sale
        $totalAmount    = 0;
        $discountAmount = 0;
        $taxDetails     = [];
        // 🔹 GST / Tax Calculation
        // $taxDetails     = [];
        $totalTaxAmount = 0;

        foreach ($sales as $sale) {
            $gstDetails = $sale->product_gst_details;
            $rowGSTOption = !empty($gstDetails) ? 'with_gst' : 'without_gst';
            $rowTaxes     = [];
            $rowTaxAmount = 0;

            if (is_array($gstDetails)) {
                foreach ($gstDetails as $tax) {
                    $name = $tax['tax_name'] ?? ($tax['name'] ?? 'GST');
                    $rate = $tax['tax_rate'] ?? ($tax['rate'] ?? 0);
                    $amount = $tax['tax_amount'] ?? ($tax['amount'] ?? 0);

                    if ($rate > 0 || $amount > 0) {
                        $rowTaxes[] = [
                            'name'   => strtoupper($name),
                            'rate'   => $rate,
                            'amount' => $amount,
                        ];
                        $rowTaxAmount += $amount;

                        // accumulate overall tax totals for summary
                        $taxKey = strtolower($name);
                        if (! isset($taxDetails[$taxKey])) {
                            $taxDetails[$taxKey] = [
                                'name'   => strtoupper($name),
                                'rate'   => $rate,
                                'amount' => 0,
                            ];
                        }
                        $taxDetails[$taxKey]['amount'] += $amount;
                    }
                }
            }

            // Attach to sale row
            $sale->rowGSTOption = $rowGSTOption;
            $sale->rowTaxes     = $rowTaxes;
            $sale->rowTaxAmount = $rowTaxAmount;

            // Final total per row = database total_amount (which includes GST and discount)
            $sale->rowFinalTotal = $sale->total_amount;

            $totalAmount = $sales->sum('rowFinalTotal');
        }
        $totalAmount = round($totalAmount);

        // Customer info (from first sale)
        $customer    = $sales->first()->user ?? null;
        $userDetails = $customer ? $customer->userDetail : null;

        return view('sales.sale_report', compact(
            'sales',
            'settings',
            'discountAmount',
            'totalAmount',
            'taxDetails',
            'currencySymbol',
            'currencyPosition',
            'customer',
            'userDetails',
            'ids'
        ));
    }

    public function export_sales_report_pdf($ids)
    {
        $authUser   = auth()->user();
        $subAdminId = session('selectedSubAdminId');

        // 🔹 Decide branch_id based on role
        if ($authUser->role === 'staff' && $authUser->branch_id) {
            $branchIdToUse = $authUser->branch_id;
        } elseif ($authUser->role === 'admin' && ! empty($subAdminId)) {
            $branchIdToUse = $subAdminId;
        } else {
            $branchIdToUse = $authUser->id;
        }

        $idsArray = explode(',', $ids);

        // 🔹 Eager load related models
        $sales = OrderItem::with(['product.category', 'variant', 'invoice', 'order', 'user.userDetail'])
            ->whereIn('id', $idsArray)
            ->get();

        if ($sales->isEmpty()) {
            return redirect()->route('sales.index')->with('error', 'No sales data found.');
        }

        // 🔹 Get settings
        $setting          = Setting::where('branch_id', $branchIdToUse)->first();
        $currencySymbol   = $setting->currency_symbol ?? '₹';
        $currencyPosition = $setting->currency_position ?? 'left';

        $subtotal = $sales->sum('total_amount');

        // 🔹 Discount calculation
        $discountAmount = 0;
        foreach ($sales as $sale) {
            if ($sale->invoice && $sale->invoice->discount) {
                $discountPercent = $sale->invoice->discount;
                $discountAmount += ($sale->total_amount * $discountPercent) / 100;
            }
        }

        $subtotalAfterDiscount = $subtotal - $discountAmount;

        // 🔹 GST / Tax Calculation
        $taxDetails     = [];
        $totalTaxAmount = 0;

        foreach ($sales as $sale) {
            $gstDetails = $sale->product_gst_details;
            $rowGSTOption = !empty($gstDetails) ? 'with_gst' : 'without_gst';
            $rowTaxes     = [];
            $rowTaxAmount = 0;

            if (is_array($gstDetails)) {
                foreach ($gstDetails as $tax) {
                    $name = $tax['tax_name'] ?? ($tax['name'] ?? 'GST');
                    $rate = $tax['tax_rate'] ?? ($tax['rate'] ?? 0);
                    $amount = $tax['tax_amount'] ?? ($tax['amount'] ?? 0);

                    if ($rate > 0 || $amount > 0) {
                        $rowTaxes[] = [
                            'name'   => strtoupper($name),
                            'rate'   => $rate,
                            'amount' => $amount,
                        ];
                        $rowTaxAmount += $amount;

                        // accumulate overall tax totals for summary
                        $taxKey = strtolower($name);
                        if (! isset($taxDetails[$taxKey])) {
                            $taxDetails[$taxKey] = [
                                'name'   => strtoupper($name),
                                'rate'   => $rate,
                                'amount' => 0,
                            ];
                        }
                        $taxDetails[$taxKey]['amount'] += $amount;
                    }
                }
            }

            // Attach to sale row
            $sale->rowGSTOption = $rowGSTOption;
            $sale->rowTaxes     = $rowTaxes;
            $sale->rowTaxAmount = $rowTaxAmount;

            // Final total per row = database total_amount (which includes GST and discount)
            $sale->rowFinalTotal = $sale->total_amount;

            $totalTaxAmount += $rowTaxAmount;
        }

        // 🔹 Total after discount + taxes
        $totalAmount = $sales->sum('rowFinalTotal');
        $totalAmount = round($totalAmount);

        $pdfData = [
            'sales'            => $sales,
            'setting'          => $setting,
            'currencySymbol'   => $currencySymbol,
            'currencyPosition' => $currencyPosition,
            'subtotal'         => $subtotal,
            'discountAmount'   => $discountAmount,
            // 'afterDiscount'    => $afterDiscount,
            'taxDetails'       => $taxDetails,
            'totalTaxAmount'   => $totalTaxAmount,
            'totalAmount'      => $totalAmount,
            // 'ids' => $ids
        ];

        // Load PDF
        $pdf = PDF::loadView('sales.sales-invoice-report-pdf', $pdfData)
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'defaultFont'          => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
            ]);

        return $pdf->download('sales_report.pdf');
    }

    public function export_sales_report_excel($ids)
    {
        $authUser   = auth()->user();
        $subAdminId = session('selectedSubAdminId');

        // 🔹 Decide branch_id based on role
        if ($authUser->role === 'staff' && $authUser->branch_id) {
            $branchIdToUse = $authUser->branch_id;
        } elseif ($authUser->role === 'admin' && ! empty($subAdminId)) {
            $branchIdToUse = $subAdminId;
        } else {
            $branchIdToUse = $authUser->id;
        }

        $idsArray = explode(',', $ids);

        // 🔹 Eager load related models
        $sales = OrderItem::with(['product.category', 'variant', 'invoice', 'order', 'user.userDetail'])
            ->whereIn('id', $idsArray)
            ->get();

        if ($sales->isEmpty()) {
            return redirect()->route('sales.report')->with('error', 'No sales data found.');
        }

        $filename = 'sales_report_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Order Number',
            'Product',
            'Order Date',
            'Customer Name',
            'Category',
            'Original Price',
            'Discount Amount',
            'Discount (%)',
            // 'Final Unit Price',
            'Quantity',
            'Taxes',
            'Total'
        ];

        $callback = function() use ($sales, $columns, $branchIdToUse) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns);

            foreach ($sales as $sale) {
                $discountPercent = (float) ($sale->discount_percentage ?? 0);
                if ($discountPercent <= 0) {
                    $discountPercent = (float) ($sale->invoice->discount ?? 0);
                }
                $originalUnitPrice = $sale->price;
                $discountAmount = (float) ($sale->discount_amount ?? 0);
                if ($discountAmount <= 0 && $discountPercent > 0) {
                    $discountAmount = ($originalUnitPrice * $discountPercent) / 100;
                }
                // $finalUnitPrice = $originalUnitPrice - $discountAmount;

                $gstDetails = $sale->product_gst_details;
                $rowGSTOption = !empty($gstDetails) ? 'with_gst' : 'without_gst';
                $rowTaxAmount = 0;
                $taxDetailsStr = 'N/A';

                if (is_array($gstDetails)) {
                    $taxes = [];
                    foreach ($gstDetails as $tax) {
                        $name = $tax['tax_name'] ?? ($tax['name'] ?? 'GST');
                        $rate = $tax['tax_rate'] ?? ($tax['rate'] ?? 0);
                        $amount = $tax['tax_amount'] ?? ($tax['amount'] ?? 0);

                        if ($rate > 0 || $amount > 0) {
                            $rowTaxAmount += $amount;
                            $taxes[] = strtoupper($name) . " (" . $rate . "%): " . number_format($amount, 2);
                        }
                    }
                    if (!empty($taxes)) {
                        $taxDetailsStr = implode(', ', $taxes);
                    }
                }

                $rowFinalTotal = $sale->total_amount;

                $row = [
                    $sale->invoice->order_number ?? 'N/A',
                    $sale->product->name ?? '-',
                    $sale->order->created_at ? $sale->order->created_at->format('d-m-Y h:i A') : 'N/A',
                    $sale->user->name ?? 'N/A',
                    $sale->product->category->name ?? 'N/A',
                    number_format((float)$originalUnitPrice, 2, '.', ''),
                    number_format((float)$discountAmount, 2, '.', ''),
                    $discountPercent . '%',
                    // number_format((float)$finalUnitPrice, 2, '.', ''),
                    $sale->quantity,
                    $taxDetailsStr,
                    number_format((float)$sale->total_amount, 2, '.', ''),
                ];

                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function show_sales_report_page(Request $request)
    {
        try {
            // 🔹 Get inputs directly from the request
            $ids      = $request->input('ids');
            $branchId = $request->input('branch');

            // 🔹 Validate required parameters
            if (empty($ids)) {
                abort(404, 'No sales selected.');
            }
            if (empty($branchId)) {
                abort(404, 'Branch ID is missing.');
            }

            // 🔹 Convert to array if comma-separated
            $idsArray = explode(',', $ids);

            // 🔹 Fetch sales data with relationships
            $sales = OrderItem::with(['product.category', 'variant', 'invoice', 'user.userDetail'])
                ->whereIn('id', $idsArray)
                ->get();

            if ($sales->isEmpty()) {
                abort(404, 'No sales found.');
            }

            // 🔹 Fetch settings for branch
            $settings         = Setting::where('branch_id', $branchId)->first();
            $currencySymbol   = $settings->currency_symbol ?? '₹';
            $currencyPosition = $settings->currency_position ?? 'left';

            // 🔹 Initialize totals
            $taxDetails     = [];
            $totalAmount    = 0;
            $discountAmount = 0;

            foreach ($sales as $sale) {
                $rowTaxes     = [];
                $rowTaxAmount = 0;

                $rowGSTOption   = $sale->invoice->gst_option ?? 'without_gst';
                $rowTaxIds      = $sale->invoice->tax_id;
                $rowTaxIdsArray = is_array($rowTaxIds) ? $rowTaxIds : (json_decode($rowTaxIds, true) ?: []);

                // 🔹 Fetch applicable tax rates
                $rowTaxRates = collect();
                if ($rowGSTOption === 'with_gst' && ! empty($rowTaxIdsArray)) {
                    $rowTaxRates = TaxRate::where('status', 'active')
                        ->where('branch_id', $branchId)
                        ->where('isDeleted', 0)
                        ->whereIn('id', $rowTaxIdsArray)
                        ->get();
                }

                // 🔹 Apply discount (if any)
                $unitPrice = $sale->price;
                if ($sale->invoice && $sale->invoice->discount) {
                    $discountPercent = $sale->invoice->discount;
                    $discountPerUnit = ($unitPrice * $discountPercent) / 100;
                    $unitPrice -= $discountPerUnit;
                    $discountAmount += $discountPerUnit * $sale->quantity;
                }

                // 🔹 Calculate taxes per item
                foreach ($rowTaxRates as $tax) {
                    $taxBase = $unitPrice * $sale->quantity;
                    $amount  = $taxBase * ($tax->tax_rate / 100);

                    $rowTaxes[] = [
                        'name'   => $tax->tax_name,
                        'rate'   => $tax->tax_rate,
                        'amount' => $amount,
                    ];

                    $rowTaxAmount += $amount;

                    // Accumulate total tax details
                    if (! isset($taxDetails[$tax->id])) {
                        $taxDetails[$tax->id] = [
                            'name'   => $tax->tax_name,
                            'rate'   => $tax->tax_rate,
                            'amount' => 0,
                        ];
                    }
                    $taxDetails[$tax->id]['amount'] += $amount;
                }

                // 🔹 Attach row-level summary
                $sale->rowGSTOption = $rowGSTOption;
                $sale->rowTaxes     = $rowTaxes;
                $sale->rowTaxAmount = $rowTaxAmount;

                // 🔹 Final total per item
                $sale->rowFinalTotal = ($unitPrice * $sale->quantity) + $rowTaxAmount;
            }

            // 🔹 Calculate final grand total
            $totalAmount = $sales->sum('rowFinalTotal');
            $totalAmount = round($totalAmount);

            // 🔹 Customer info (first sale user)
            $customer    = $sales->first()->user ?? null;
            $userDetails = $customer ? $customer->userDetail : null;

            // 🔹 Prepare data for Blade view
            $data = [
                'sales'            => $sales,
                'settings'         => $settings,
                'discountAmount'   => $discountAmount,
                'totalAmount'      => $totalAmount,
                'taxDetails'       => $taxDetails,
                'currencySymbol'   => $currencySymbol,
                'currencyPosition' => $currencyPosition,
                'customer'         => $customer,
                'userDetails'      => $userDetails,
                'ids'              => $ids,
            ];

            // ✅ Return view without requiring authentication
            return view('sales.web_sale_report', $data);
        } catch (\Throwable $e) {
            abort(500, 'Error loading sales report: ' . $e->getMessage());
        }
    }

    public function tds_report(Request $request)
    {
        $user         = Auth()->user();
        $branchId     = $user->id ?? null;
        $userBranchId = $user->branch_id ?? null;
        $userRole     = $user->role ?? '';
        $subAdminId   = session('selectedSubAdminId');

        if ($userRole === 'sub-admin') {
            $branchIdToUse = $branchId;
        } elseif ($userRole === 'admin' && $subAdminId) {
            $branchIdToUse = $subAdminId;
        } elseif ($userRole === 'staff') {
            $branchIdToUse = $userBranchId;
        } else {
            $branchIdToUse = $branchId;
        }

        $customersQuery = User::where('role', 'customer')
            ->where('branch_id', $branchIdToUse)
            ->where('isDeleted', 0)
            ->orderBy('name');
        StaffDepartmentScope::applyStaffCreatedByScope($customersQuery, $user);
        $customers = $customersQuery->get();

        return view('sales.tdsreport', compact('customers'));
    }
}
