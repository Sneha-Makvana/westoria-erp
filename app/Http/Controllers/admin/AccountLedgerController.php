<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Purchases;
use App\Models\User;
use App\Services\StaffDepartmentScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountLedgerController extends Controller
{
    public function index(Request $request)
    {
        return view('account_ledger.list');
    }

    public function add(Request $request)
    {
        $user               = Auth::user();
        $role               = $user->role;
        $userBranchId       = $user->branch_id ?? $user->id; // Use branch_id if exists
        $selectedSubAdminId = session('selectedSubAdminId');
        // 🔹 Decide which branch to filter by
        if ($role === 'admin' && ! empty($selectedSubAdminId)) {
            $branchId = User::find($selectedSubAdminId)?->id ?? $userBranchId;
        } elseif ($role === 'staff' && $user->branch_id) {
            $branchId = (int) $user->branch_id;
        } else {
            $branchId = $userBranchId;
        }

        $customersQuery = User::query()
            ->select('id', 'name', 'phone', 'branch_id as subbranch_id')
            ->where('role', 'customer')
            ->where('isDeleted', 0)
            ->where('branch_id', $branchId);

        $vendorsQuery = User::query()
            ->select('id', 'name', 'branch_id as subbranch_id')
            ->where('role', 'vendor')
            ->where('isDeleted', 0)
            ->where('branch_id', $branchId);

        StaffDepartmentScope::applyStaffCreatedByScope($customersQuery, $user);
        StaffDepartmentScope::applyStaffCreatedByScope($vendorsQuery, $user);

        $customers = $customersQuery->get();
        $vendors   = $vendorsQuery->get();

        $orderYears = Order::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->pluck('year');

        $purchaseYears = Purchases::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->pluck('year');

        $years = $orderYears->merge($purchaseYears)->unique()->sortDesc()->values();

        return view('account_ledger.add', compact('customers', 'vendors', 'years'));
    }
}
