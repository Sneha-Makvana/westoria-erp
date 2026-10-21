<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\AccountBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AccountBranchController extends Controller
{
    public function getAllAccountBranch(Request $request)
    {
        $user = Auth::guard('api')->user();

        $branchId = match (strtolower($user->role)) {
            'admin'     => $request->sub_branch_id ?: $user->id,
            'sub-admin' => $user->id,
            'staff'     => $user->branch_id,
            default     => $user->id,
        };

        $perPage = (int) $request->input('per_page', 10);
        $page = (int) $request->input('page', 1);
        $search = trim((string) $request->input('search', ''));
        $shouldPaginate = $request->has('page') || $request->has('per_page') || $request->filled('search');

        $query = AccountBranch::query()
            ->where('isDeleted', 0)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%")
                        ->orWhere('phone', 'LIKE', "%{$search}%");
                });
            });

        $pagination = null;

        if ($shouldPaginate) {
            $paginated = $query->latest('id')->paginate($perPage, ['*'], 'page', $page);
            $account_branches = $paginated->items();

            $pagination = [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'next_page_url' => $paginated->nextPageUrl(),
                'prev_page_url' => $paginated->previousPageUrl(),
            ];
        } else {
            $account_branches = $query->latest('id')->get();
        }

        return response()->json([
            'status'            => true,
            'data'              => $account_branches,
            'pagination'        => $pagination,
        ]);
    }

    public function getAccountBranchById($id)
    {
        $account_branch = AccountBranch::find($id);
        if ($account_branch) {
            return response()->json(['status' => true, 'account_branch' => $account_branch], 200);
        } else {
            return response()->json(['status' => false, 'error' => 'Account Branch not found'], 404);
        }
    }

    public function createAccountBranch(Request $request)
    {
        $user = Auth::guard('api')->user();

        $branchId = match (strtolower($user->role)) {
            'sub-admin' => $user->id,
            'staff'     => $user->branch_id,
            'admin'     => $request->branch_id ?? $user->id,
            default     => $user->id,
        };

        $rules = [
            'name'                => 'required|string|max:255',
            'branch_code'         => 'nullable|string|max:255',
            'branch_company_name' => 'nullable|string|max:255',
            'email'               => 'nullable|email|max:255',
            'phone'               => 'nullable|string|max:20',
            'phone_2'             => 'nullable|string|max:20',
            'address'             => 'nullable|string',
            'address_line_2'      => 'nullable|string',
            'address_line_3'      => 'nullable|string',
            'city'                => 'nullable|string|max:255',
            'area'                => 'nullable|string|max:255',
            'state'               => 'nullable|string|max:255',
            'zip_code'            => 'nullable|string|max:20',
            'tin'                 => 'nullable|string|max:255',
            'gstin'               => 'nullable|string|max:255',
            'pan'                 => 'nullable|string|max:255',
            'opened_on'           => 'nullable|date',
            'closed_on'           => 'nullable|date',
            'branch_type'         => 'nullable|string|max:255',
            'status'              => 'nullable|in:active,inactive',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $account_branch = AccountBranch::create([
            'branch_id'           => $branchId,
            'name'                => $validated['name'],
            'branch_code'         => $validated['branch_code'] ?? null,
            'branch_company_name' => $validated['branch_company_name'] ?? null,
            'email'               => $validated['email'] ?? null,
            'phone'               => $validated['phone'] ?? null,
            'phone_2'             => $validated['phone_2'] ?? null,
            'address'             => $validated['address'] ?? null,
            'address_line_2'      => $validated['address_line_2'] ?? null,
            'address_line_3'      => $validated['address_line_3'] ?? null,
            'city'                => $validated['city'] ?? null,
            'area'                => $validated['area'] ?? null,
            'state'               => $validated['state'] ?? null,
            'zip_code'            => $validated['zip_code'] ?? null,
            'tin'                 => $validated['tin'] ?? null,
            'gstin'               => $validated['gstin'] ?? null,
            'pan'                 => $validated['pan'] ?? null,
            'opened_on'           => $validated['opened_on'] ?? null,
            'closed_on'           => $validated['closed_on'] ?? null,
            'branch_type'         => $validated['branch_type'] ?? null,
            'status'              => $validated['status'] ?? 'active',
            'create_by'           => $user->id,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Account Branch created successfully',
            'account_branch' => $account_branch->fresh(),
        ], 200);
    }

    public function updateAccountBranch(Request $request)
    {
        $user = Auth::guard('api')->user();
        $account_branch = AccountBranch::findOrFail($request->id);

        $rules = [
            'id'                  => 'required|exists:account_branches,id',
            'name'                => 'required|string|max:255',
            'branch_code'         => 'nullable|string|max:255',
            'branch_company_name' => 'nullable|string|max:255',
            'email'               => 'nullable|email|max:255',
            'phone'               => 'nullable|string|max:20',
            'phone_2'             => 'nullable|string|max:20',
            'address'             => 'nullable|string',
            'address_line_2'      => 'nullable|string',
            'address_line_3'      => 'nullable|string',
            'city'                => 'nullable|string|max:255',
            'area'                => 'nullable|string|max:255',
            'state'               => 'nullable|string|max:255',
            'zip_code'            => 'nullable|string|max:20',
            'tin'                 => 'nullable|string|max:255',
            'gstin'               => 'nullable|string|max:255',
            'pan'                 => 'nullable|string|max:255',
            'opened_on'           => 'nullable|date',
            'closed_on'           => 'nullable|date',
            'branch_type'         => 'nullable|string|max:255',
            'status'              => 'nullable|in:active,inactive',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $account_branch->update([
            'name'                => $validated['name'],
            'branch_code'         => $validated['branch_code'] ?? null,
            'branch_company_name' => $validated['branch_company_name'] ?? null,
            'email'               => $validated['email'] ?? null,
            'phone'               => $validated['phone'] ?? null,
            'phone_2'             => $validated['phone_2'] ?? null,
            'address'             => $validated['address'] ?? null,
            'address_line_2'      => $validated['address_line_2'] ?? null,
            'address_line_3'      => $validated['address_line_3'] ?? null,
            'city'                => $validated['city'] ?? null,
            'area'                => $validated['area'] ?? null,
            'state'               => $validated['state'] ?? null,
            'zip_code'            => $validated['zip_code'] ?? null,
            'tin'                 => $validated['tin'] ?? null,
            'gstin'               => $validated['gstin'] ?? null,
            'pan'                 => $validated['pan'] ?? null,
            'opened_on'           => $validated['opened_on'] ?? null,
            'closed_on'           => $validated['closed_on'] ?? null,
            'branch_type'         => $validated['branch_type'] ?? null,
            'status'              => $validated['status'] ?? $account_branch->status,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Account Branch updated successfully',
            'account_branch' => $account_branch->fresh(),
        ], 200);
    }

    public function deleteAccountBranch($id)
    {
        $account_branch = AccountBranch::find($id);

        if (! $account_branch) {
            return response()->json(['status' => false, 'error' => 'Account Branch not found'], 404);
        }

        $account_branch->update(['isDeleted' => 1]);

        return response()->json([
            'status'  => true,
            'message' => 'Account Branch deleted successfully',
        ], 200);
    }
}
