<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function customer_list(Request $request)
    {
        return view('customer/customerlist');
    }
    public function add_customer(Request $request)
    {
        // Auto-generate next sequential customer code (CUST0001, CUST0002, ...)
        $lastCustomer = User::where('role', 'customer')
            ->where('isDeleted', 0)
            ->whereNotNull('customer_code')
            ->where('customer_code', '!=', '')
            ->orderByRaw("LENGTH(customer_code) DESC, customer_code DESC")
            ->first();

        $nextNumber = 1;
        if ($lastCustomer && preg_match('/(\d+)$/', $lastCustomer->customer_code, $m)) {
            $nextNumber = (int)$m[1] + 1;
        }
        $nextCustomerCode = 'CUST' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return view('customer/addcustomer', compact('nextCustomerCode'));
    }
    public function import_customer(Request $request)
    {
        return view('customer/importcustomer');
    }
    public function edit_customer(Request $request)
    {
        return view('customer/editcustomer');
    }
    public function customer_report(Request $request)
    {
        return view('customer/customerreport');
    }
    public function customer_view($id)
    {
        return view('customer.view_customer', ['id' => $id]);
    }
}
