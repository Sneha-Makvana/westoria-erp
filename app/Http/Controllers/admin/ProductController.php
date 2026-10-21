<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Models\User;
use App\Services\ProductImportPreviewStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    /* -------------------------------------------------
     | 🔹 Helpers
     -------------------------------------------------*/

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

    private function getBrandsAndCategories($branchId)
    {
        return [
            'brands' => Brand::where('isDeleted', 0)
                ->where('branch_id', $branchId)
                ->get(),

            'categories' => Category::where('isDeleted', 0)
                ->where('branch_id', $branchId)
                ->get(),
        ];
    }

    /* -------------------------------------------------
     | 🔹 Product List
     -------------------------------------------------*/
    public function product_list()
    {
        $branchId = $this->resolveBranchId();
        $data     = $this->getBrandsAndCategories($branchId);

        return view('product/productlist', $data);
    }

    /* -------------------------------------------------
     | 🔹 Quick Update Products (full page)
     -------------------------------------------------*/
    public function product_quick_update()
    {
        $branchId = $this->resolveBranchId();
        $data     = $this->getBrandsAndCategories($branchId);

        return view('product/quick_update', $data);
    }

    /* -------------------------------------------------
     | 🔹 Add Product
     -------------------------------------------------*/
    public function add_product()
    {
        $branchId = $this->resolveBranchId();
        $data     = $this->getBrandsAndCategories($branchId);
        $units    = Unit::where('is_delete', 0)
            ->where('created_by', $branchId)
            ->get();

        $branches = [];
        if (strtolower(auth()->user()->role ?? '') === 'admin') {
            $branches = User::whereIn('role', ['admin', 'sub-admin'])
                ->where('isDeleted', 0)
                ->orderBy('name')
                ->get();
        }

        $nextSku = Product::getNextAvailableSku($branchId);

        return view('product/addproduct', compact('units', 'branches', 'nextSku') + $data);
    }

    /* -------------------------------------------------
     | 🔹 Edit Product
     -------------------------------------------------*/
    public function edit_product($id)
    {
        $product = Product::with(['category', 'brand'])->findOrFail($id);

        $branchId = $this->resolveBranchId();
        $data     = $this->getBrandsAndCategories($branchId);
        $units = Unit::where('is_delete', 0)
            ->where('created_by', $branchId)
            ->get();

        $branches = [];
        if (strtolower(auth()->user()->role ?? '') === 'admin') {
            $branches = User::whereIn('role', ['admin', 'sub-admin'])
                ->where('isDeleted', 0)
                ->orderBy('name')
                ->get();
        }
        return view('product/editproduct', array_merge(
            ['product' => $product, 'units' => $units, 'branches' => $branches],
            $data
        ));
    }

    /* -------------------------------------------------
     | 🔹 Product Detail (legacy)
     -------------------------------------------------*/
    public function product_detail($id)
    {
        return view('product/product-details', [
            'view_id' => $id,
        ]);
    }

    /* -------------------------------------------------
     | 🔹 Product Import
     -------------------------------------------------*/
    public function product_import()
    {
        return view('product/importproduct');
    }

    public function product_import_upload(Request $request)
    {
        @set_time_limit(300);

        $request->validate([
            'csv_file' => 'required|file|max:51200',
        ], [
            'csv_file.max' => 'File is too large. Maximum allowed size is 50 MB.',
        ]);

        $token = ProductImportPreviewStore::createPendingFromUpload(
            $request->file('csv_file'),
            (int) auth()->id(),
            (int) $this->resolveBranchId()
        );

        return redirect()->route('product.import.preview', $token);
    }

    public function product_import_preview(string $token)
    {
        $cached = Cache::get(ProductImportPreviewStore::cacheKey($token));
        if (!is_array($cached) || (int) ($cached['user_id'] ?? 0) !== (int) auth()->id()) {
            return redirect()
                ->route('product.import')
                ->with('import_error', 'Import preview expired or not found. Please upload the file again.');
        }

        return view('product/importproduct_preview', [
            'token'       => $token,
            'file_name'   => (string) ($cached['file_name'] ?? 'Import file'),
            'total_count' => (int) ($cached['total_count'] ?? 0),
            'valid_count' => (int) ($cached['valid_count'] ?? 0),
            'status'      => (string) ($cached['status'] ?? 'pending'),
            'per_page'    => 100,
        ]);
    }

    /* -------------------------------------------------
     | 🔹 Product View
     -------------------------------------------------*/
    public function product_view($id)
    {
        $product = Product::with(['category', 'brand', 'unit'])->findOrFail($id);

        return view('product/product_view', [
            'product' => $product,
            'view_id' => $id,
        ]);
    }
    public function profitLossReportIndex(Request $request)
    {
        $branchId = $this->resolveBranchId();

        $products = Product::where('isDeleted', 0)
            ->where('branch_id', $branchId)
            ->orderBy('name')
            ->get();

        $categories = Category::where('isDeleted', 0)
            ->where('branch_id', $branchId)
            ->orderBy('name')
            ->get();

        $vendors = User::where('role', 'vendor')
            ->where('branch_id', $branchId)
            ->where('isDeleted', 0)
            ->orderBy('name')
            ->get();

        $customers = User::where('role', 'customer')
            ->where('branch_id', $branchId)
            ->where('isDeleted', 0)
            ->orderBy('name')
            ->get();

        return view('reports.profit-loss-report', compact('products', 'categories', 'vendors', 'customers'));
    }

    public function profitLossStatementIndex(Request $request)
    {
        $branchId = $this->resolveBranchId();
        $settings = \App\Models\Setting::where('branch_id', $branchId)->first()
            ?? \App\Models\Setting::first();

        return view('reports.profit-loss-statement', compact('settings'));
    }

    public function quickSearchByBarcode(Request $request)
    {
        $request->validate(['barcode' => 'required|string']);
        
        $branchId = $this->resolveBranchId();
        
        $product = Product::where('isDeleted', 0)
            ->where('branch_id', $branchId)
            ->where(function ($q) use ($request) {
                $q->where('barcode', $request->barcode)
                  ->orWhere('product_code', $request->barcode)
                  ->orWhere('SKU', $request->barcode);
            })
            ->first();

        if ($product) {
            return response()->json([
                'status' => 'success',
                'product_id' => $product->id,
                'redirect_url' => route('product.view', $product->id)
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Product Not Found'
        ], 404);
    }

    public function quickSearchByImage(Request $request)
    {
        $request->validate(['image' => 'required|image|max:10240']);

        // Mock visual match: return "Product Not Found" to trigger SweetAlert
        return response()->json([
            'status' => 'error',
            'message' => 'Product Not Found (Visual Match Mock)'
        ], 404);
    }
}
