<?php

namespace App\Http\Controllers;

use App\Models\ChallanBill;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChallanBillController extends Controller
{
    private function resolveBranchId()
    {
        $user       = auth()->user();
        $role       = strtolower($user->role ?? '');
        $subAdminId = session('selectedSubAdminId');

        return match ($role) {
            'sub-admin' => $user->id,
            'staff'     => $user->branch_id,
            'admin'     => $subAdminId ?: $user->id,
            default     => $user->id,
        };
    }

    public function index()
    {
        $branchId = $this->resolveBranchId();
        $challanBills = ChallanBill::with(['vendor', 'customer'])
                            ->where('branch_id', $branchId)
                            ->orderBy('created_at', 'desc')
                            ->get();
        return view('challan-bills.index', compact('challanBills'));
    }

    public function create()
    {
        $branchId = $this->resolveBranchId();
        
        $vendors = User::where('role', 'vendor')->where('branch_id', $branchId)->get();
        $customers = User::where('role', 'customer')->where('branch_id', $branchId)->get();
        $products = Product::where('branch_id', $branchId)
                        ->where(function($q) {
                            $q->where('status', 'active')->orWhere('status', 1);
                        })->get();
        
        return view('challan-bills.create', compact('vendors', 'customers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'challan_number' => 'required|unique:challan_bills',
            'challan_date' => 'required|date',
            'vendor_id' => 'required',
            'customer_id' => 'required',
            'products' => 'required|array|min:1',
            'products.*.product_name' => 'required',
        ], [
            'products.*.product_name.required' => 'Product Name is required.'
        ]);

        $challanBill = ChallanBill::create([
            'branch_id' => $this->resolveBranchId(),
            'challan_number' => $request->challan_number,
            'challan_date' => $request->challan_date,
            'vendor_id' => $request->vendor_id,
            'customer_id' => $request->customer_id,
            'products' => $request->products,
            'remarks' => $request->remarks,
            'prepared_by' => Auth::user()->name ?? 'Admin',
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Challan Bill created successfully.',
                'redirect_url' => route('challan-bills.index')
            ]);
        }

        return redirect()->route('challan-bills.index')->with('success', 'Challan Bill created successfully.');
    }

    public function show($id)
    {
        $branchId = $this->resolveBranchId();
        $challanBill = ChallanBill::with(['vendor', 'customer'])
                            ->where('branch_id', $branchId)
                            ->findOrFail($id);
        $setting = Setting::where('branch_id', $branchId)->first() ?? Setting::first();
        return view('challan-bills.show', compact('challanBill', 'setting'));
    }

    public function edit($id)
    {
        $branchId = $this->resolveBranchId();
        $challanBill = ChallanBill::where('branch_id', $branchId)->findOrFail($id);
        
        $vendors = User::where('role', 'vendor')->where('branch_id', $branchId)->get();
        $customers = User::where('role', 'customer')->where('branch_id', $branchId)->get();
        $products = Product::where('branch_id', $branchId)
                        ->where(function($q) {
                            $q->where('status', 'active')->orWhere('status', 1);
                        })->get();

        return view('challan-bills.edit', compact('challanBill', 'vendors', 'customers', 'products'));
    }

    public function update(Request $request, $id)
    {
        $branchId = $this->resolveBranchId();
        $challanBill = ChallanBill::where('branch_id', $branchId)->findOrFail($id);

        $request->validate([
            'challan_date' => 'required|date',
            'vendor_id' => 'required',
            'customer_id' => 'required',
            'products' => 'required|array|min:1',
            'products.*.product_name' => 'required',
        ], [
            'products.*.product_name.required' => 'Product Name is required.'
        ]);

        $challanBill->update([
            'challan_date' => $request->challan_date,
            'vendor_id' => $request->vendor_id,
            'customer_id' => $request->customer_id,
            'products' => $request->products,
            'remarks' => $request->remarks,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Challan Bill updated successfully.',
                'redirect_url' => route('challan-bills.index')
            ]);
        }

        return redirect()->route('challan-bills.index')->with('success', 'Challan Bill updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $branchId = $this->resolveBranchId();
        $challanBill = ChallanBill::where('branch_id', $branchId)->findOrFail($id);
        $challanBill->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Challan Bill deleted successfully.'
            ]);
        }

        return redirect()->route('challan-bills.index')->with('success', 'Challan Bill deleted successfully.');
    }

    public function generatePdf($id)
    {
        $branchId = $this->resolveBranchId();
        $challanBill = ChallanBill::with(['vendor', 'customer'])
                            ->where('branch_id', $branchId)
                            ->findOrFail($id);
        $setting = Setting::where('branch_id', $branchId)->first() ?? Setting::first();
        
        $pdf = Pdf::loadView('challan-bills.pdf', compact('challanBill', 'setting'));
        return $pdf->download('challan-bill-'.$challanBill->challan_number.'.pdf');
    }

    public function getCustomerDetails($id)
    {
        $customer = User::findOrFail($id);
        return response()->json($customer);
    }
}
