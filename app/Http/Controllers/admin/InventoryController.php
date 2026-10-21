<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryController extends Controller
{
    public function index()
    {
       $user = auth()->user();
        $userRole = $user->role ?? '';
        $userId = $user->id ?? null;
        $BranchId = $user->branch_id ?? null;
        $subAdminId = session('selectedSubAdminId');
        if ($userRole === 'sub-admin') {
            $brands = Brand::where('isDeleted', 0)->where('branch_id', $userId)->get();
            $categories = Category::where('isDeleted', 0)->where('branch_id', $userId)->get();
        } elseif ($userRole === 'admin' && $subAdminId) {
            $brands = Brand::where('isDeleted', 0)->where('branch_id', $subAdminId)->get();
            $categories = Category::where('isDeleted', 0)->where('branch_id', $subAdminId)->get();
        } elseif(strtolower($userRole) === 'staff') {
            $brands = Brand::where('isDeleted', 0)->where('branch_id', $BranchId)->get();
            $categories = Category::where('isDeleted', 0)->where('branch_id', $BranchId)->get();
        }else{
            $brands = Brand::where('isDeleted', 0)->where('branch_id', $userId)->get();
            $categories = Category::where('isDeleted', 0)->where('branch_id', $userId)->get();   
        }
        $branches = \App\Models\User::whereIn('role', ['admin', 'sub-admin'])
            ->where('isDeleted', 0)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('inventory/index', compact('categories', 'brands', 'branches'));
    }
    public function View(Request $request ,$id)
    {
        $product = Product::where('isDeleted', 0)->find($id);
        return view('inventory/view', compact('product'));
    }

    public function materialIndex()
    {
        $user = auth()->user();
        $userRole = $user->role ?? '';
        $userId = $user->id ?? null;
        $BranchId = $user->branch_id ?? null;
        $subAdminId = session('selectedSubAdminId');
        if ($userRole === 'sub-admin') {
            $brands = Brand::where('isDeleted', 0)->where('branch_id', $userId)->get();
            $categories = Category::where('isDeleted', 0)->where('branch_id', $userId)->get();
        } elseif ($userRole === 'admin' && $subAdminId) {
            $brands = Brand::where('isDeleted', 0)->where('branch_id', $subAdminId)->get();
            $categories = Category::where('isDeleted', 0)->where('branch_id', $subAdminId)->get();
        } elseif(strtolower($userRole) === 'staff') {
            $brands = Brand::where('isDeleted', 0)->where('branch_id', $BranchId)->get();
            $categories = Category::where('isDeleted', 0)->where('branch_id', $BranchId)->get();
        }else{
            $brands = Brand::where('isDeleted', 0)->where('branch_id', $userId)->get();
            $categories = Category::where('isDeleted', 0)->where('branch_id', $userId)->get();   
        }
        $branches = \App\Models\User::whereIn('role', ['admin', 'sub-admin'])
            ->where('isDeleted', 0)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('inventory.material_index', compact('categories', 'brands', 'branches'));
    }

    public function materialView(Request $request ,$id)
    {
        $product = \App\Models\RowMaterial::where('isDeleted', 0)->find($id);
        if ($product) {
            $product->name = $product->row_materialname;
        }
        return view('inventory.material_view', compact('product'));
    }

    public function exchangeIndex()
    {
        return view('inventory.exchange_index');
    }
}
