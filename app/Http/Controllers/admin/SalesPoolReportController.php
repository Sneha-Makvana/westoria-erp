<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Services\StaffDepartmentScope;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesPoolReportController extends Controller
{
    /**
     * Display the Sales Pool Report page.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $branchId = $this->resolveBranchId($user);

        return view('sales.salespool.index', compact('branchId', 'user'));
    }

    /**
     * Fetch hot selling items (products) with aggregated sales data.
     * AJAX endpoint for the "Hot Selling Items" tab.
     */
    public function getHotSellingItems(Request $request)
    {
        $user = Auth::user();
        $branchId = $this->resolveBranchId($user);

        // Get date range from request
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $staffId = $request->input('staff_id');
        $customerId = $request->input('customer_id');

        // Validate dates
        if (empty($fromDate) || empty($toDate)) {
            return response()->json([
                'status' => false,
                'message' => 'Date range is required.',
            ], 400);
        }

        try {
            $fromDate = Carbon::createFromFormat('Y-m-d', $fromDate)->startOfDay();
            $toDate = Carbon::createFromFormat('Y-m-d', $toDate)->endOfDay();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid date format. Use YYYY-MM-DD.',
            ], 400);
        }

        // Build query for hot selling items
        $query = OrderItem::with(['product.category'])
            ->whereHas('order', function ($q) use ($user, $branchId, $fromDate, $toDate, $staffId, $customerId) {
                $q->where('isDeleted', 0)
                    ->where('branch_id', $branchId)
                    ->where('quotation_status', 'sales') // Only confirmed sales
                    ->whereBetween('created_at', [$fromDate, $toDate]);

                StaffDepartmentScope::applyOrderScope($q, $user);

                // Apply staff filter if provided
                if (!empty($staffId) && $staffId !== '0') {
                    $q->where(function ($staffQuery) use ($staffId) {
                        $staffQuery->where('created_by', $staffId)
                            ->orWhere('staff_id', $staffId);
                    });
                }

                // Apply customer filter if provided
                if (!empty($customerId) && $customerId !== '0') {
                    $q->where('user_id', $customerId);
                }
            });

        // Execute query and group by product
        $items = $query->get()->groupBy('product_id');

        // Calculate aggregates per product
        $hotSellingItems = [];

        foreach ($items as $productId => $orderItems) {
            $product = $orderItems->first()->product;

            if (!$product) {
                continue;
            }

            $totalQty = $orderItems->sum('quantity');
            $totalAmount = $orderItems->sum('total_amount');
            $billCount = $orderItems->pluck('order_id')->unique()->count();

            $hotSellingItems[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'category' => $product->category->name ?? 'N/A',
                'sku' => $product->SKU ?? 'N/A',
                'qty_sold' => (int) $totalQty,
                'amount' => round($totalAmount, 2),
                'bills' => $billCount,
            ];
        }

        // Sort by quantity sold (descending)
        usort($hotSellingItems, function ($a, $b) {
            return $b['qty_sold'] <=> $a['qty_sold'];
        });

        // Add rank
        foreach ($hotSellingItems as $index => &$item) {
            $item['rank'] = $index + 1;
        }

        // Get currency settings
        $settings = Setting::where('branch_id', $branchId)->first();
        $currencySymbol = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';

        return response()->json([
            'status' => true,
            'data' => $hotSellingItems,
            'currency_symbol' => $currencySymbol,
            'currency_position' => $currencyPosition,
            'count' => count($hotSellingItems),
        ]);
    }

    /**
     * Fetch hot customers with aggregated purchase data.
     * AJAX endpoint for the "Hot Customers" tab.
     */
    public function getHotCustomers(Request $request)
    {
        $user = Auth::user();
        $branchId = $this->resolveBranchId($user);

        // Get date range from request
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $staffId = $request->input('staff_id');
        $customerId = $request->input('customer_id');

        // Validate dates
        if (empty($fromDate) || empty($toDate)) {
            return response()->json([
                'status' => false,
                'message' => 'Date range is required.',
            ], 400);
        }

        try {
            $fromDate = Carbon::createFromFormat('Y-m-d', $fromDate)->startOfDay();
            $toDate = Carbon::createFromFormat('Y-m-d', $toDate)->endOfDay();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid date format. Use YYYY-MM-DD.',
            ], 400);
        }

        // Build query for orders
        $orders = Order::with(['user:id,name', 'orderItems.product'])
            ->where('isDeleted', 0)
            ->where('branch_id', $branchId)
            ->where('quotation_status', 'sales') // Only confirmed sales
            ->whereBetween('created_at', [$fromDate, $toDate]);

        StaffDepartmentScope::applyOrderScope($orders, $user);

        // Apply staff filter if provided
        if (!empty($staffId) && $staffId !== '0') {
            $orders->where(function ($q) use ($staffId) {
                $q->where('created_by', $staffId)
                    ->orWhere('staff_id', $staffId);
            });
        }

        // Apply customer filter if provided
        if (!empty($customerId) && $customerId !== '0') {
            $orders->where('user_id', $customerId);
        }

        $orders = $orders->get();

        // Group by customer and calculate aggregates
        $hotCustomers = [];

        foreach ($orders->groupBy('user_id') as $customerId => $customerOrders) {
            $customer = $customerOrders->first()->user;

            if (!$customer) {
                continue;
            }

            $totalBills = count($customerOrders);
            $totalQtyPurchased = $customerOrders->flatMap(function ($order) {
                return $order->orderItems;
            })->sum('quantity');
            $totalAmountSpent = $customerOrders->sum('total_amount');

            // Find top purchased item for this customer
            $topItem = $customerOrders
                ->flatMap(function ($order) {
                    return $order->orderItems;
                })
                ->groupBy('product_id')
                ->map(function ($items) {
                    return [
                        'product' => $items->first()->product,
                        'qty' => $items->sum('quantity'),
                    ];
                })
                ->sortByDesc('qty')
                ->first();

            $hotCustomers[] = [
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'total_bills' => $totalBills,
                'total_qty_purchased' => (int) $totalQtyPurchased,
                'total_amount_spent' => round($totalAmountSpent, 2),
                'top_purchased_item' => $topItem ? $topItem['product']->name : 'N/A',
            ];
        }

        // Sort by total amount spent (descending)
        usort($hotCustomers, function ($a, $b) {
            return $b['total_amount_spent'] <=> $a['total_amount_spent'];
        });

        // Add rank
        foreach ($hotCustomers as $index => &$customer) {
            $customer['rank'] = $index + 1;
        }

        // Get currency settings
        $settings = Setting::where('branch_id', $branchId)->first();
        $currencySymbol = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';

        return response()->json([
            'status' => true,
            'data' => $hotCustomers,
            'currency_symbol' => $currencySymbol,
            'currency_position' => $currencyPosition,
            'count' => count($hotCustomers),
        ]);
    }

    /**
     * Get summary statistics for the report.
     * Returns: Total Bills, Total Qty Sold, Total Sales, Top Customer.
     */
    public function getSummaryStats(Request $request)
    {
        $user = Auth::user();
        $branchId = $this->resolveBranchId($user);

        // Get date range from request
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $staffId = $request->input('staff_id');
        $customerId = $request->input('customer_id');

        // Validate dates
        if (empty($fromDate) || empty($toDate)) {
            return response()->json([
                'status' => false,
                'message' => 'Date range is required.',
            ], 400);
        }

        try {
            $fromDate = Carbon::createFromFormat('Y-m-d', $fromDate)->startOfDay();
            $toDate = Carbon::createFromFormat('Y-m-d', $toDate)->endOfDay();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid date format. Use YYYY-MM-DD.',
            ], 400);
        }

        // Get all sales orders in the date range
        $ordersQuery = Order::where('isDeleted', 0)
            ->where('branch_id', $branchId)
            ->where('quotation_status', 'sales')
            ->whereBetween('created_at', [$fromDate, $toDate]);

        StaffDepartmentScope::applyOrderScope($ordersQuery, $user);

        // Apply staff filter if provided
        if (!empty($staffId) && $staffId !== '0') {
            $ordersQuery->where(function ($q) use ($staffId) {
                $q->where('created_by', $staffId)
                    ->orWhere('staff_id', $staffId);
            });
        }

        // Apply customer filter if provided
        if (!empty($customerId) && $customerId !== '0') {
            $ordersQuery->where('user_id', $customerId);
        }

        $orders = $ordersQuery->with('orderItems', 'user')->get();

        // Calculate stats
        $totalBills = count($orders);
        $totalQtySold = $orders->flatMap(function ($order) {
            return $order->orderItems;
        })->sum('quantity');
        $totalSales = $orders->sum('total_amount');

        // Find top customer (by total amount spent)
        $topCustomer = $orders
            ->groupBy('user_id')
            ->map(function ($customerOrders) {
                return [
                    'name' => $customerOrders->first()->user->name ?? 'N/A',
                    'amount' => $customerOrders->sum('total_amount'),
                ];
            })
            ->sortByDesc('amount')
            ->first();

        // Get currency settings
        $settings = Setting::where('branch_id', $branchId)->first();
        $currencySymbol = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';

        return response()->json([
            'status' => true,
            'data' => [
                'total_bills' => $totalBills,
                'total_qty_sold' => (int) $totalQtySold,
                'total_sales' => round($totalSales, 2),
                'top_customer' => $topCustomer ? $topCustomer['name'] : 'N/A',
                'top_customer_amount' => $topCustomer ? round($topCustomer['amount'], 2) : 0,
            ],
            'currency_symbol' => $currencySymbol,
            'currency_position' => $currencyPosition,
        ]);
    }

    /**
     * Resolve branch ID based on user role.
     */
    private function resolveBranchId($user)
    {
        if ($user->role === 'staff' && !empty($user->branch_id)) {
            return (int) $user->branch_id;
        }

        if ($user->role === 'admin') {
            $selectedSubAdminId = session('selectedSubAdminId');
            if (!empty($selectedSubAdminId)) {
                return (int) $selectedSubAdminId;
            }
        }

        return (int) $user->id;
    }

    /**
     * Fetch list of all customers for the branch (for dropdown population).
     */
    public function getCustomers(Request $request)
    {
        $user = Auth::user();
        $branchId = $this->resolveBranchId($user);

        // Get customers from orders for the branch
        $customers = User::where('branch_id', $branchId)
            ->where('role', 'customer')
            ->where('isDeleted', 0)
            ->orderBy('name', 'ASC')
            ->select('id', 'name')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $customers,
        ]);
    }

    /**
     * Fetch list of all staff members for the branch (for dropdown population).
     */
    public function getStaffMembers(Request $request)
    {
        $user = Auth::user();
        $branchId = $this->resolveBranchId($user);

        // Get staff members for the branch
        $staffMembers = User::where('branch_id', $branchId)
            ->whereIn('role', ['admin', 'staff', 'sub-admin'])
            ->where('isDeleted', 0)
            ->orderBy('name', 'ASC')
            ->select('id', 'name')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $staffMembers,
        ]);
    }
}
