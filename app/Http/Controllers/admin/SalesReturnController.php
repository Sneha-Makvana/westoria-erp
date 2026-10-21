<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Services\StaffDepartmentScope;
use App\Models\Order;
use App\Models\SalesReturnItem;
use App\Models\SalesReturn;
use App\Models\TaxRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesReturnController extends Controller
{
    private function getBranchContext(): array
    {
        $user = auth()->user();

        $userId = $user->id;
        $role = $user->role;
        $userBranch = $user->branch_id;
        $selectedBranch = session('selectedSubAdminId');

        if ($role === 'admin' && $selectedBranch) {
            $branchId = $selectedBranch;
        } elseif ($role === 'sub-admin') {
            $branchId = $userId;
        } elseif ($role === 'staff') {
            $branchId = $userBranch;
        } else {
            $branchId = $userId;
        }

        return [$user, $userId, $role, $branchId];
    }

    public function createsales_return(Request $request)
    {
        [, $userId, $role, $branchId] = $this->getBranchContext();

        if ($role === 'staff') {
            $staffUser = auth()->user();
            $invoiceQuery = Order::where('isDeleted', 0)
                ->where('quotation_status', 'sales');
            StaffDepartmentScope::applyOrderScope($invoiceQuery, $staffUser);
            $invoiceNumbers = $invoiceQuery
                ->whereHas('user', function ($query) {
                    $query->where('isDeleted', 0);
                })
                ->whereHas('orderItems', function ($query) {
                    $query->whereRaw('order_items.quantity > (SELECT IFNULL(SUM(quantity), 0) FROM sales_return_items WHERE sales_return_items.order_item_id = order_items.id)');
                })
                ->orderBy('order_number', 'desc')
                ->pluck('order_number');
        } else {
            $invoiceNumbers = Order::where('isDeleted', 0)
                ->where('quotation_status', 'sales')
                ->where('branch_id', $branchId)
                ->whereHas('user', function ($query) {
                    $query->where('isDeleted', 0);
                })
                ->whereHas('orderItems', function ($query) {
                    $query->whereRaw('order_items.quantity > (SELECT IFNULL(SUM(quantity), 0) FROM sales_return_items WHERE sales_return_items.order_item_id = order_items.id)');
                })
                ->orderBy('order_number', 'desc')
                ->pluck('order_number');
        }

        $preselectOrderNumber = trim((string) $request->query('order_number', ''));
        if ($preselectOrderNumber !== '' && ! $invoiceNumbers->contains($preselectOrderNumber)) {
            $preselectQuery = Order::where('isDeleted', 0)
                ->where('quotation_status', 'sales')
                ->where('order_number', $preselectOrderNumber);
            if ($role === 'staff') {
                StaffDepartmentScope::applyOrderScope($preselectQuery, auth()->user());
            } else {
                $preselectQuery->where('branch_id', $branchId);
            }
            if ($preselectQuery->exists()) {
                $invoiceNumbers = $invoiceNumbers->prepend($preselectOrderNumber)->unique()->values();
            } else {
                $preselectOrderNumber = '';
            }
        }

        $settings = \DB::table('settings')->where('branch_id', $branchId)->first();
        $currencySymbol = $settings->currency_symbol ?? '?';
        $currencyPosition = $settings->currency_position ?? 'left';

        $TaxRate = TaxRate::where('status', 'active')->where('branch_id', $branchId)->where('isDeleted', 0)->get();

        return view('salesreturn/createsalesreturn', compact(
            'invoiceNumbers',
            'TaxRate',
            'currencySymbol',
            'currencyPosition',
            'preselectOrderNumber'
        ));
    }

    public function createsales_returns(Request $request)
    {
        return view('salesreturn/createsalesreturns');
    }

    public function editsales_return(Request $request)
    {
        return view('salesreturn/editsalesreturn');
    }

    public function editsales_returns(Request $request)
    {
        return view('salesreturn/editsalesreturns');
    }

    public function salesreturn_list(Request $request)
    {
        [, , , $branchId] = $this->getBranchContext();

        $settings = \DB::table('settings')->where('branch_id', $branchId)->first();
        $currencySymbol = $settings->currency_symbol ?? '?';
        $currencyPosition = $settings->currency_position ?? 'left';

        return view('salesreturn/salesreturnlist', compact(
            'currencySymbol',
            'currencyPosition'
        ));
    }

    public function salesreturn_list_data(Request $request)
    {
        [, $userId, $role, $branchId] = $this->getBranchContext();

        $salesReturnsQuery = SalesReturn::with([
            'order.user:id,name',
            'order.orderItems:id,order_id,quantity',
            'items:id,sales_return_id,order_item_id,quantity'
        ])
            ->orderBy('id', 'desc');

        if ($role === 'staff') {
            $salesReturnsQuery->where('created_by', $userId);
        } else {
            $salesReturnsQuery->where('branch_id', $branchId);
        }

        $salesReturns = $salesReturnsQuery->get();

        $settings = \DB::table('settings')->where('branch_id', $branchId)->first();
        $currencySymbol = $settings->currency_symbol ?? '?';
        $currencyPosition = $settings->currency_position ?? 'left';

        $data = $salesReturns->map(function ($salesReturn) {
            $subtotal = (float) ($salesReturn->subtotal ?? 0);
            $taxAmount = (float) ($salesReturn->tax_amount ?? 0);
            $discountAmount = (float) ($salesReturn->discount_amount ?? 0);
            $displayTotal = $subtotal - $discountAmount + $taxAmount;
            $shouldIncludeShipping = false;

            if ($salesReturn->order && $salesReturn->order->orderItems->isNotEmpty()) {
                $returnedUpToThisReturn = SalesReturnItem::query()
                    ->select('order_item_id', DB::raw('SUM(quantity) as total_quantity'))
                    ->whereIn('order_item_id', $salesReturn->order->orderItems->pluck('id'))
                    ->whereHas('salesReturn', function ($query) use ($salesReturn) {
                        $query->where('order_id', $salesReturn->order_id)
                            ->where('id', '<=', $salesReturn->id);
                    })
                    ->groupBy('order_item_id')
                    ->pluck('total_quantity', 'order_item_id');

                $shouldIncludeShipping = true;
                foreach ($salesReturn->order->orderItems as $orderItem) {
                    $returnedQuantity = (float) ($returnedUpToThisReturn[$orderItem->id] ?? 0);
                    $originalQuantity = (float) ($orderItem->quantity ?? 0);

                    if ($returnedQuantity + 0.0001 < $originalQuantity) {
                        $shouldIncludeShipping = false;
                        break;
                    }
                }

                if ($shouldIncludeShipping) {
                    $displayTotal += (float) ($salesReturn->order->shipping ?? 0);
                }
            }

            return [
                'id' => $salesReturn->id,
                'return_number' => $salesReturn->return_number ?? '-',
                'order_number' => $salesReturn->order->order_number ?? '-',
                'date' => optional($salesReturn->created_at)->format('d M Y, h:i A'),
                'customer' => $salesReturn->order->user->name ?? '-',
                'items_count' => (int) $salesReturn->items->count(),
                'return_qty' => (float) $salesReturn->items->sum('quantity'),
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount' => (float) ($salesReturn->discount ?? 0),
                'discount_amount' => $discountAmount,
                'total_amount' => $displayTotal,
            ];
        })->values();

        return response()->json([
            'status' => true,
            'currency_symbol' => $currencySymbol,
            'currency_position' => $currencyPosition,
            'data' => $data,
        ]);
    }

    public function salesreturn_lists(Request $request)
    {
        return $this->salesreturn_list($request);
    }

    public function sales_return_pdf($id)
    {
        $user = auth()->user();

        // Load return with items and their products
        $return = SalesReturn::with(['items.product', 'order.user.userDetail'])->findOrFail($id);

        $order = $return->order;
        
        $customer = null;
        if ($order && $order->user) {
            $customer = [
                'name'             => $order->user->name ?? 'Default-customer',
                'company_name'     => $order->user->company_name ?? '',
                'email'            => $order->user->email ?? '',
                'phone'            => $order->user->phone ?? '',
                'address'          => optional($order->user->userDetail)->address ?? '',
                'delivery_address' => optional($order->user->userDetail)->delivery_address ?? '',
                'gst_number'       => $order->user->gst_number ?? '',
                'pan_number'       => $order->user->pan_number ?? '',
            ];
        }

        // Determine branch for settings
        if ($user->role === 'staff' && $user->branch_id) {
            $branchId = $user->branch_id;
        } elseif ($user->role === 'admin') {
            $branchId = $user->id;
        } else {
            $branchId = $user->id;
        }

        $setting = DB::table('settings')->where('branch_id', $branchId)->first();

        if (!$setting) {
            $setting = \App\Models\Setting::first();
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('salesreturn.sales-return-pdf', compact('return', 'customer', 'setting'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('sales_return_' . $return->id . '.pdf');
    }
}
