<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AccountBranch;
use Illuminate\Http\Request;

class AccountBranchController extends Controller
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

    public function account_branch_list()
    {
        return view('account_branch.accountbranchlist');
    }

    public function add_account_branch()
    {
        return view('account_branch.addaccountbranch');
    }

    public function edit_account_branch($id)
    {
        $account_branch = AccountBranch::findOrFail($id);
        return view('account_branch.editaccountbranch', compact('account_branch', 'id'));
    }
}
