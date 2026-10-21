<?php

// namespace App\Http\Controllers;
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseReturn;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseReturnController extends Controller
{
    public function create_purchase_return(Request $request)
    {
        $user      = Auth::user();
        $branch_id = $user->id;

        $selectedSubAdminId = (session('selectedSubAdminId'));

        // 🔹 Decide branch_id based on role
        if ($user->role === 'staff' && $user->branch_id) {
            $branchIdToUse = $user->branch_id;
        } elseif ($user->role === 'admin' && ! empty($selectedSubAdminId)) {
            $branchIdToUse = $selectedSubAdminId;
        } else {
            $branchIdToUse = $user->id;
        }
        if ($user->role === 'staff') {
            $branchIdToUse = $user->id;

            $invoices = PurchaseInvoice::select('id', 'invoice_number', 'bill_no')
                ->where('isDeleted', 0)
                ->where('created_by', $branch_id)
                ->whereHas('vendor', function ($query) {
                    $query->where('isDeleted', 0);
                })
                ->whereHas('purchases', function ($query) {
                    $query->whereRaw('purchases.quantity > (SELECT IFNULL(SUM(quantity), 0) FROM purchase_return_items WHERE purchase_return_items.purchase_item_id = purchases.id)');
                })
                ->orderBy('id', 'desc')
                ->get();
        } else {
            $invoices = PurchaseInvoice::select('id', 'invoice_number', 'bill_no')
                ->where('isDeleted', 0)
                ->where('branch_id', $branchIdToUse)
                ->whereHas('vendor', function ($query) {
                    $query->where('isDeleted', 0);
                })
                ->whereHas('purchases', function ($query) {
                    $query->whereRaw('purchases.quantity > (SELECT IFNULL(SUM(quantity), 0) FROM purchase_return_items WHERE purchase_return_items.purchase_item_id = purchases.id)');
                })
                ->orderBy('id', 'desc')
                ->get();
        }
        // dd($invoices);
        $settings         = \DB::table('settings')->where('branch_id', $branchIdToUse)->first();
        $currencySymbol   = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';

        return view('purchasereturn.createpurchasereturn', compact(
            'invoices',
            'currencySymbol',
            'currencyPosition'
        ));
    }

    public function edit_purchase_return(Request $request)
    {
        return view('purchasereturn/editpurchasereturn');
    }
    public function purchase_return_list(Request $request)
    {
        return view('purchasereturn/purchasereturnlist');
    }

    public function purchaseReturnPdf($id)
    {
        $user = Auth::user();

        // Load return with items and their products
        $return = PurchaseReturn::with(['items.product'])->findOrFail($id);

        // Resolve vendor name via the purchase invoice
        $invoice    = PurchaseInvoice::with('vendor')->find($return->purchase_id);
        $vendorName = $invoice?->vendor?->name ?? '-';
        $vendorPhone = $invoice?->vendor?->phone ?? '-';

        // Copy bill_no from invoice if not set on the return
        if (empty($return->bill_no) && $invoice) {
            $return->bill_no = $invoice->bill_no ?? $invoice->invoice_number;
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

        // Fallback to a Setting model instance if DB row not found
        if (!$setting) {
            $setting = Setting::first();
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('purchasereturn.purchase-return-pdf', compact('return', 'vendorName', 'vendorPhone', 'setting'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('purchase_return_' . $return->id . '.pdf');
    }

}
