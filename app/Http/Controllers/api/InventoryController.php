<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\StaffPermission;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\OrderItem;
use App\Models\Purchases;
use App\Models\Brand;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Milon\Barcode\DNS1D;

class InventoryController extends Controller
{
    // public function index(Request $request)
    // {
    //     $user = Auth::guard('api')->user();
    //     $userId = $user->id;
    //     $userRole = $user->role;
    //     $userBranchId = $user->branch_id;

    //     $categoryId = $request->query('category_id');
    //     $brandId = $request->query('brand_id');
    //     $subBranchId = $request->sub_branch_id ?? $userId;

    //     // ✅ OPTIMIZED: Select only needed fields and eager load relationships
    //     $query = Product::with([
    //         'category:id,name',
    //         'brand:id,name',
    //         'product_inventory:id,product_id,initial_stock,current_stock'
    //     ])
    //         ->select('id', 'name', 'SKU', 'price', 'quantity', 'created_at', 'category_id', 'brand_id')
    //         ->where('isDeleted', 0)
    //         ->orderBy('id', 'desc');

    //     // ✅ Branch filter
    //     if ($userRole === 'admin') {
    //         if (!empty($subBranchId)) {
    //             $query->where('branch_id', $subBranchId);
    //         }
    //     } elseif ($userRole === 'sub-admin') {
    //         $query->where('branch_id', $userId);
    //     } elseif ($userRole === 'staff') {
    //         $query->where('branch_id', $userBranchId);
    //     }

    //     // ✅ Category filter
    //     if (!empty($categoryId)) {
    //         $query->where('category_id', $categoryId);
    //     }

    //     // ✅ Brand filter
    //     if (!empty($brandId)) {
    //         $query->where('brand_id', $brandId);
    //     }

    //     $products = $query->get();

    //     if ($products->isEmpty()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'No products found.'
    //         ]);
    //     }

    //     // ✅ OPTIMIZED: Use collection map for better performance
    //     $data = $products->map(function ($product) {
    //         $inventory = $product->product_inventory;
    //         return [
    //             'id' => $product->id,
    //             'product_name' => $product->name,
    //             'sku' => $product->SKU,
    //             'initial_stock' => $inventory->initial_stock ?? 0,
    //             'current_stock' => $product->quantity,
    //             'price' => $product->price,
    //             'created_at' => $product->created_at->format('Y-m-d'),
    //         ];
    //     })->toArray();

    //     return response()->json([
    //         'status' => true,
    //         'inventory' => $data
    //     ]);
    // }

    
    public function index(Request $request)
    {
        $user = Auth::guard('api')->user();
        $userId = $user->id;
        $userRole = $user->role;
        $userBranchId = $user->branch_id;

        $categoryId = $request->query('category_id');
        $brandId = $request->query('brand_id');
        $subBranchId = $request->sub_branch_id ?? $userId;
        $search = $request->query('search', '');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $perPage = $request->query('per_page', 10);
        $page = $request->query('page', 1);

        // Base query with necessary relationships
        $query = Product::with([
            'category:id,name',
            'brand:id,name',
            'variants',
            'product_inventory:id,product_id,product_variant_id,initial_stock,current_stock'
        ])
            ->select('id', 'name', 'SKU', 'price', 'quantity', 'created_at', 'category_id', 'brand_id', 'branch_id')
            ->addSelect([
                'latest_remarks' => ProductInventory::select('remarks')
                    ->whereColumn('product_id', 'products.id')
                    ->orderByDesc('id')
                    ->limit(1)
            ])
            ->where('isDeleted', 0)
            ->orderBy('id', 'desc');

        // Branch filter
        if ($userRole === 'admin') {
            if (!empty($subBranchId)) {
                $query->where('branch_id', $subBranchId);
            }
        } elseif ($userRole === 'sub-admin') {
            $query->where('branch_id', $userId);
        } elseif ($userRole === 'staff') {
            $query->where('branch_id', $userBranchId);
        }

        // Category filter
        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        // Brand filter
        if (!empty($brandId)) {
            $query->where('brand_id', $brandId);
        }

        // Date range filter
        if (!empty($startDate)) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if (!empty($endDate)) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        // Search filter (search in name or SKU)
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('SKU', 'like', "%{$search}%");
            });
        }

        // Paginate
        $paginated = $query->paginate($perPage, ['*'], 'page', $page);

        // Transform data
        $data = $paginated->map(function ($product) {
            $inventory = $product->product_inventory;

            $variantsData = $product->variants->map(function ($v) use ($product) {
                $lastInv = ProductInventory::where('product_variant_id', $v->id)
                    ->where('product_id', $product->id)
                    ->latest()
                    ->first();

                $firstInv = ProductInventory::where('product_variant_id', $v->id)
                    ->where('product_id', $product->id)
                    ->orderBy('id', 'asc')
                    ->first();

                $vInitialStock = $firstInv ? (float)$firstInv->initial_stock : (float)$v->qty;
                $vCurrentStock = $lastInv ? (float)$lastInv->current_stock : (float)$v->qty;
                $vRemarks      = $lastInv ? ($lastInv->remarks ?? 'N/A') : 'N/A';

                return [
                    'variant_id'    => $v->id,
                    'size'          => $v->size,
                    'color'         => $v->color,
                    'initial_stock' => $vInitialStock,
                    'stock'         => $vCurrentStock,
                    'current_stock' => $vCurrentStock,
                    'price'         => $v->price ?: $product->price,
                    'remarks'       => $vRemarks,
                    'label'         => trim(($v->size ?? '') . ' / ' . ($v->color ?? ''), ' /'),
                ];
            });

            return [
                'id' => $product->id,
                'product_name' => $product->name,
                'sku' => $product->SKU,
                'initial_stock' => $inventory->initial_stock ?? 0,
                'current_stock' => $variantsData->count() > 0 ? $variantsData->sum('stock') : $product->quantity,
                'price' => $product->price,
                'remarks' => $product->latest_remarks ?? null,
                'created_at' => $product->created_at->format('Y-m-d'),
                'variants' => $variantsData,
                'branch_id' => $product->branch_id,
            ];
        });

        // Low stock threshold (optional)
        $lowStockThreshold = 5; // you can fetch from settings

        return response()->json([
            'status' => true,
            'inventory' => $data,
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
                'from'         => $paginated->firstItem(),
                'to'           => $paginated->lastItem(),
            ],
            'lowStockThreshold' => $lowStockThreshold
        ]);
    }

    public function filter(Request $request)
    {
        $user = Auth::guard('api')->user();
        $userId = $user->id;
        $userRole = $user->role;
        $userBranchId = $user->branch_id;

        $categoryId   = $request->query('category_id');
        $brandId      = $request->query('brand_id');
        $subBranchId  = $request->sub_branch_id ?? $userId;
        $startDate    = $request->query('start_date');
        $endDate      = $request->query('end_date');
        
        // ✅ Pagination parameters
        $page = max(1, (int) $request->query('page', 1));
        $perPage = min(100, max(1, (int) $request->query('per_page', 10)));

        // ✅ OPTIMIZED: Select only needed fields and eager load relationships
        $query = Product::with([
            'category:id,name',
            'brand:id,name',
            'product_inventory:id,product_id,initial_stock,current_stock'
        ])
            ->select('id', 'name', 'SKU', 'price', 'quantity', 'created_at', 'category_id', 'brand_id', 'branch_id')
            ->addSelect([
                'latest_remarks' => ProductInventory::select('remarks')
                    ->whereColumn('product_id', 'products.id')
                    ->orderByDesc('id')
                    ->limit(1)
            ])
            ->where('isDeleted', 0)
            ->orderBy('id', 'desc');

        // ✅ Branch filter
        if ($userRole === 'admin') {
            if (!empty($subBranchId)) {
                $query->where('branch_id', $subBranchId);
            }
        } elseif ($userRole === 'sub-admin') {
            $query->where('branch_id', $userId);
        } elseif ($userRole === 'staff') {
            $query->where('branch_id', $userBranchId);
        }

        // ✅ Category filter
        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        // ✅ Brand filter
        if (!empty($brandId)) {
            $query->where('brand_id', $brandId);
        }

        // ✅ Date filter
        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween('created_at', [
                $startDate . " 00:00:00",
                $endDate . " 23:59:59"
            ]);
        }

        // ✅ Get total count for pagination
        $totalCount = $query->count();
        $lastPage = max(1, (int) ceil($totalCount / $perPage));
        $page = min($page, $lastPage);

        // ✅ Apply pagination
        $products = $query->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        if ($products->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No products found.',
                'pagination' => [
                    'current_page' => (int) $page,
                    'last_page' => $lastPage,
                    'per_page' => (int) $perPage,
                    'total' => $totalCount,
                    'from' => 0,
                    'to' => 0,
                ]
            ]);
        }

        // ✅ OPTIMIZED: Use collection map for better performance
        $data = $products->map(function ($product) {
            $inventory = $product->product_inventory;
            return [
                'id'            => $product->id,
                'product_name'  => $product->name,
                'sku'           => $product->SKU,
                'initial_stock' => $inventory->initial_stock ?? 0,
                'current_stock' => $product->quantity,
                'price'         => $product->price,
                'remarks'       => $product->latest_remarks ?? null,
                'created_at'    => $product->created_at->format('Y-m-d'),
                'branch_id'     => $product->branch_id,
            ];
        })->toArray();

        return response()->json([
            'status'    => true,
            'inventory' => $data,
            'pagination' => [
                'current_page' => (int) $page,
                'last_page' => $lastPage,
                'per_page' => (int) $perPage,
                'total' => $totalCount,
                'from' => $products->count() > 0 ? (($page - 1) * $perPage) + 1 : 0,
                'to' => $products->count() > 0 ? (($page - 1) * $perPage) + $products->count() : 0,
            ]
        ]);
    }
    public function products_edit_inventroy(Request $request, $id)
    {
        $user = Auth::guard('api')->user();
        if (! StaffPermission::check(17, 'edit', $user) && ! StaffPermission::check(17, 'add', $user)) {
            return response()->json([
                'status' => false,
                'message' => 'You do not have permission for this action.',
            ], 403);
        }

        $variantId = $request->query('variant_id');

        $product = Product::with('product_inventory:id,product_id,initial_stock,current_stock')
            ->select('id', 'name', 'SKU', 'price', 'quantity')
            ->find($id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ], 404);
        }

        if (!empty($variantId)) {
            $variant = \App\Models\ProductVariant::find($variantId);
            if ($variant) {
                $lastInv = ProductInventory::where('product_variant_id', $variantId)->latest()->first();
                $firstInv = ProductInventory::where('product_variant_id', $variantId)->orderBy('id', 'asc')->first();
                $vInitialStock = $firstInv ? (float)$firstInv->initial_stock : (float)$variant->qty;
                $vCurrentStock = $lastInv ? (float)$lastInv->current_stock : (float)$variant->qty;
                return response()->json([
                    'status' => true,
                    'product' => [
                        'id'                 => $product->id,
                        'product_variant_id' => $variant->id,
                        'product_name'       => $product->name . ' (' . trim(($variant->size ?? '') . ' / ' . ($variant->color ?? ''), ' /') . ')',
                        'sku'                => $product->SKU,
                        'price'              => $variant->price ?: $product->price,
                        'initial_stock'      => $vInitialStock ?: (float)$variant->qty,
                        'current_stock'      => $vCurrentStock,
                    ]
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'product' => [
                'id' => $product->id,
                'product_name' => $product->name,
                'sku' => $product->SKU,
                'price' => $product->price,
                'initial_stock' => $product->product_inventory->initial_stock ?? 0,
                'current_stock' => $product->quantity ?? 0,
            ]
        ]);
    }
    public function inventory_update(Request $request, $id)
    {
        $user = Auth::guard('api')->user();

        if (! StaffPermission::check(17, 'edit', $user) && ! StaffPermission::check(17, 'add', $user)) {
            return response()->json([
                'status' => false,
                'message' => 'You do not have permission for this action.',
            ], 403);
        }

        $userBranchId = $user->id ?? null;
        $remarks = trim((string) $request->remarks);
        if ($remarks === '') {
            $remarks = null;
        }

        if ($user->role === 'staff' && $user->branch_id) {
            $userBranchId = $user->branch_id;
        } elseif (!empty($request->selectedSubAdminId)) {
            $userBranchId = $request->selectedSubAdminId;
        } else {
            $userBranchId = $user->id;
        }

        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $variantId = $request->product_variant_id ?: ($request->variant_id ?: null);
        $adjustment = (float) $request->stock_quantity;

        if (!empty($variantId)) {
            $variant = \App\Models\ProductVariant::find($variantId);
            if ($variant) {
                $lastInv = ProductInventory::where('product_variant_id', $variantId)
                    ->where('branch_id', $userBranchId)
                    ->latest()
                    ->first();
                $currentStock = $lastInv ? (float)$lastInv->current_stock : (float)$variant->qty;

                if ($request->stock_action === 'add') {
                    $newStock = $currentStock + $adjustment;
                } elseif ($request->stock_action === 'minus') {
                    $newStock = max(0, $currentStock - $adjustment);
                } else {
                    $newStock = $currentStock;
                }

                $variant->update(['qty' => $newStock]);

                $inventoryPayload = [
                    'product_id'         => $id,
                    'product_variant_id' => $variantId,
                    'initial_stock'      => $currentStock,
                    'current_stock'      => $newStock,
                    'branch_id'          => $userBranchId,
                    'type'               => $request->stock_action === 'add' ? 'Stock Added' : 'Stock Minus',
                    'create_by'          => $user->id,
                    'date'               => now(),
                    'quantity'           => $adjustment,
                ];

                if (Schema::hasColumn('product_inventory', 'remarks')) {
                    $inventoryPayload['remarks'] = $remarks;
                }

                ProductInventory::create($inventoryPayload);

                return response()->json([
                    'status' => true,
                    'message' => 'Variant stock updated successfully',
                    'new_stock' => $newStock
                ]);
            }
        }

        // ✅ Calculate new stock based on add/minus
        $currentStock = (float) $product->quantity; // take from DB (latest)

        if ($request->stock_action === 'add') {
            $newStock = $currentStock + $adjustment;
        } elseif ($request->stock_action === 'minus') {
            $newStock = max(0, $currentStock - $adjustment); // prevent negative
        } else {
            $newStock = $currentStock; // fallback
        }

        // ✅ Update product table
        $product->update([
            'price' => $request->price,
            'quantity' => $newStock,
        ]);

        // ✅ Create inventory log
        $inventoryPayload = [
            'product_id' => $id,
            'initial_stock' => $currentStock,
            'current_stock' => $newStock,
            'branch_id' => $userBranchId,
            'type' => $request->stock_action === 'add' ? 'Stock Added' : 'Stock Minus',
            'create_by' => $user->id,
            'date' => now(),
            'quantity' => $adjustment, // optional: track how much was added/removed
        ];

        if (Schema::hasColumn('product_inventory', 'remarks')) {
            $inventoryPayload['remarks'] = $remarks;
        }

        ProductInventory::create($inventoryPayload);

        return response()->json([
            'status' => true,
            'message' => 'Stock updated successfully',
            'new_stock' => $newStock
        ]);
    }
    // public function getQuantityHistory_inventory($productId)
    // {
    //     // ✅ OPTIMIZED: Select only needed fields
    //     $product = Product::select('id', 'name')->find($productId);
    //     if (!$product) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Product not found.'
    //         ]);
    //     }

    //     // ✅ OPTIMIZED: Select only needed fields
    //     $productInventories = ProductInventory::select(
    //         'id', 'product_id', 'type', 'initial_stock', 'current_stock',
    //         'create_by', 'branch_id', 'date', 'created_at'
    //     )
    //         ->where('product_id', $productId)
    //         ->orderBy('id', 'desc')
    //         ->get();

    //     // ✅ OPTIMIZED: Bulk load all users in ONE query instead of N+1
    //     $userIds = $productInventories->pluck('create_by')
    //         ->merge($productInventories->pluck('branch_id'))
    //         ->filter()
    //         ->unique()
    //         ->toArray();

    //     $users = !empty($userIds)
    //         ? User::select('id', 'name')
    //             ->whereIn('id', $userIds)
    //             ->get()
    //             ->keyBy('id')
    //         : collect();

    //     // ✅ OPTIMIZED: Use collection map for better performance
    //     $history = $productInventories->map(function ($inventory) use ($users) {
    //         $userId = $inventory->create_by ?? $inventory->branch_id;
    //         $user = $users->get($userId);

    //         $initial = $inventory->initial_stock ?? 0;
    //         $current = $inventory->current_stock ?? 0;
    //         $qty = (!empty($initial) && !empty($current)) ? abs($current - $initial) : 0;

    //         // Determine sign based on type
    //         $signedQty = match(strtolower($inventory->type ?? '')) {
    //             'sale' => "-" . $qty,
    //             'purchase', 'stock added' => "+" . $qty,
    //             default => ($current > $initial) ? "+" . $qty : "-" . $qty
    //         };

    //         return [
    //             'type'          => $inventory->type,
    //             'initial_stock' => $initial,
    //             'current_stock' => $current,
    //             'quantity'      => $signedQty,
    //             'name'          => $user->name ?? 'Unknown',
    //             'date'          => $inventory->date
    //                 ? \Carbon\Carbon::parse($inventory->date)->format('d-M-Y')
    //                 : ($inventory->created_at ? $inventory->created_at->format('d-M-Y') : 'N/A'),
    //         ];
    //     })->values()->toArray();


    //     return response()->json([
    //         'status' => true,
    //         'product' => $product->name,
    //         'history' => $history, // Already sorted by orderBy('id', 'desc') in query
    //     ]);
    // }
    public function getQuantityHistory_inventory(Request $request, $productId)
    {
        $product = Product::select('id', 'name')->find($productId);
        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found.'
            ]);
        }

        $variantId = $request->query('variant_id');
        $search = $request->query('search', '');
        $perPage = $request->query('per_page', 10);
        $page = $request->query('page', 1);


        $query = ProductInventory::from('product_inventory as pi')
            ->leftJoin('users as u', 'pi.create_by', '=', 'u.id')
            ->select(
                'pi.*',
                'u.name as user_name'
            )
            ->where('pi.product_id', $productId);

        if (!empty($variantId)) {
            $query->where('pi.product_variant_id', $variantId);
        }

        /*
    |--------------------------------------------------------------------------
    | ✅ GLOBAL SEARCH
    |--------------------------------------------------------------------------
    */
        if (!empty($search)) {

            $query->where(function ($q) use ($search) {

                // Type search
                $q->where('pi.type', 'like', "%{$search}%")

                    // Current stock
                    ->orWhere('pi.current_stock', 'like', "%{$search}%")

                    // User name
                    ->orWhere('u.name', 'like', "%{$search}%")

                    // Date search
                    ->orWhereDate('pi.date', $search)

                    // Quantity search (calculated)
                    ->orWhereRaw("
                ABS(pi.current_stock - pi.initial_stock) LIKE ?
            ", ["%{$search}%"]);
            });
        }

        $query->orderBy('pi.id', 'desc');


        // Paginate
        $paginated = $query->paginate($perPage, ['*'], 'page', $page);

        // Build map of stock level before each transaction
        $maxIdOnPage = $paginated->max('id');
        $previousStockMap = [];
        $runningStock = 0.0;

        if ($maxIdOnPage) {
            $priorRecordsQuery = ProductInventory::where('product_id', $productId);
            if (!empty($variantId)) {
                $priorRecordsQuery->where('product_variant_id', $variantId);
            }
            $priorRecords = $priorRecordsQuery->where('id', '<=', $maxIdOnPage)
                ->orderBy('id')
                ->get(['id', 'current_stock']);

            foreach ($priorRecords as $record) {
                $previousStockMap[$record->id] = $runningStock;
                $runningStock = (float) ($record->current_stock ?? 0);
            }
        }

        // Bulk load users
        $userIds = $paginated->pluck('create_by')
            ->merge($paginated->pluck('branch_id'))
            ->filter()
            ->unique()
            ->toArray();

        $users = !empty($userIds)
            ? User::select('id', 'name')
            ->whereIn('id', $userIds)
            ->get()
            ->keyBy('id')
            : collect();

        $variantIds = $paginated->pluck('product_variant_id')->filter()->unique()->toArray();
        $variants = !empty($variantIds)
            ? \App\Models\ProductVariant::whereIn('id', $variantIds)->get()->keyBy('id')
            : collect();

        $history = $paginated->map(function ($inventory) use ($users, $previousStockMap, $variants) {
            $userId = $inventory->create_by ?? $inventory->branch_id;
            $user = $users->get($userId);
            $variant = $inventory->product_variant_id ? $variants->get($inventory->product_variant_id) : null;
            $variantInfo = $variant ? trim(($variant->size ?? '') . ' / ' . ($variant->color ?? ''), ' /') : null;

            $initial = (float) ($inventory->initial_stock ?? 0);
            $current = (float) ($inventory->current_stock ?? 0);
            
            if (array_key_exists($inventory->id, $previousStockMap)) {
                $beforeStock = $previousStockMap[$inventory->id];
                $change = round($current - $beforeStock, 2);
            } else {
                $change = round($current - $initial, 2);
            }

            if ($change > 0) {
                $signedQty = '+' . rtrim(rtrim(number_format($change, 2, '.', ''), '0'), '.');
            } elseif ($change < 0) {
                $signedQty = '-' . rtrim(rtrim(number_format(abs($change), 2, '.', ''), '0'), '.');
            } else {
                $signedQty = '0';
            }

            return [
                'type'               => $inventory->type,
                'product_variant_id' => $inventory->product_variant_id,
                'variant_name'       => $variantInfo,
                'initial_stock'      => $initial,
                'current_stock'      => $current,
                'quantity'           => $signedQty,
                'remarks'            => $inventory->remarks ?? null,
                'name'               => $user->name ?? 'Unknown',
                'date'               => $inventory->date
                    ? \Carbon\Carbon::parse($inventory->date)->format('d-M-Y')
                    : ($inventory->created_at ? $inventory->created_at->format('d-M-Y') : 'N/A'),
            ];
        })->values()->toArray();

        return response()->json([
            'status' => true,
            'product' => $product->name,
            'history' => $history,
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
                'from'         => $paginated->firstItem(),
                'to'           => $paginated->lastItem(),
            ]
        ]);
    }

    public function transfer(Request $request, $id)
    {
        $user = Auth::guard('api')->user();

        $validator = Validator::make($request->all(), [
            'to_branch_id' => 'required|exists:users,id',
            'quantity'     => 'required|numeric|gt:0',
            'remarks'      => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $toBranchId = $request->to_branch_id;
        $quantity = (float) $request->quantity;
        $remarks = $request->remarks ? trim($request->remarks) : null;

        $sourceProduct = Product::find($id);
        if (!$sourceProduct) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found.'
            ], 404);
        }

        if ($toBranchId == $sourceProduct->branch_id) {
            return response()->json([
                'status' => false,
                'message' => 'To Branch cannot be the same as From Branch.'
            ], 422);
        }

        if ($quantity > $sourceProduct->quantity) {
            return response()->json([
                'status' => false,
                'message' => 'Transfer quantity cannot exceed available stock.'
            ], 422);
        }

        DB::beginTransaction();

        try {
            // 1. Deduct quantity from the From Branch inventory
            $oldSourceStock = (float) $sourceProduct->quantity;
            $newSourceStock = $oldSourceStock - $quantity;
            $sourceProduct->update(['quantity' => $newSourceStock]);

            ProductInventory::create([
                'product_id'    => $sourceProduct->id,
                'initial_stock' => $oldSourceStock,
                'current_stock' => $newSourceStock,
                'branch_id'     => $sourceProduct->branch_id,
                'type'          => 'Stock Transfer Out',
                'create_by'     => $user->id,
                'date'          => now(),
                'remarks'       => $remarks
            ]);

            // Resolve target Brand, Category, and Unit if they are branch-specific
            $brandId = null;
            if ($sourceProduct->brand_id) {
                $sourceBrand = \App\Models\Brand::find($sourceProduct->brand_id);
                if ($sourceBrand) {
                    $targetBrand = \App\Models\Brand::where('name', $sourceBrand->name)
                        ->where('branch_id', $toBranchId)
                        ->where('isDeleted', 0)
                        ->first();
                    if (!$targetBrand) {
                        $targetBrand = $sourceBrand->replicate();
                        $targetBrand->branch_id = $toBranchId;
                        $targetBrand->isDeleted = 0;
                        $targetBrand->save();
                    }
                    $brandId = $targetBrand->id;
                }
            }

            $categoryId = null;
            if ($sourceProduct->category_id) {
                $sourceCategory = \App\Models\Category::find($sourceProduct->category_id);
                if ($sourceCategory) {
                    $targetCategory = \App\Models\Category::where('name', $sourceCategory->name)
                        ->where('branch_id', $toBranchId)
                        ->where('isDeleted', 0)
                        ->first();
                    if (!$targetCategory) {
                        $targetCategory = $sourceCategory->replicate();
                        $targetCategory->branch_id = $toBranchId;
                        $targetCategory->isDeleted = 0;
                        $targetCategory->save();
                    }
                    $categoryId = $targetCategory->id;
                }
            }

            $unitId = null;
            if ($sourceProduct->unit_id) {
                $sourceUnit = \App\Models\Unit::find($sourceProduct->unit_id);
                if ($sourceUnit) {
                    $targetUnit = \App\Models\Unit::where('unit_name', $sourceUnit->unit_name)
                        ->where('created_by', $toBranchId)
                        ->where('is_delete', 0)
                        ->first();
                    if (!$targetUnit) {
                        $targetUnit = $sourceUnit->replicate();
                        $targetUnit->created_by = $toBranchId;
                        $targetUnit->is_delete = 0;
                        $targetUnit->save();
                    }
                    $unitId = $targetUnit->id;
                }
            }

            // 2. Add quantity to the To Branch inventory (check including deleted records)
            // Use strict matching priority: SKU -> barcode -> product_code -> Name+Category+Brand combo
            $targetProductQuery = Product::where('branch_id', $toBranchId);

            if (!empty($sourceProduct->SKU)) {
                $targetProductQuery->where('SKU', $sourceProduct->SKU);
            } elseif (!empty($sourceProduct->barcode)) {
                $targetProductQuery->where('barcode', $sourceProduct->barcode);
            } elseif (!empty($sourceProduct->product_code)) {
                $targetProductQuery->where('product_code', $sourceProduct->product_code);
            } else {
                // Fallback to strict Name, Category, Brand combo matching using resolved target IDs
                $targetProductQuery->where('name', $sourceProduct->name)
                    ->where('category_id', $categoryId)
                    ->where('brand_id', $brandId);
            }

            $targetProduct = $targetProductQuery->first();

            if ($targetProduct) {
                // If it was deleted, restore it and reset quantity to 0 before adding the transferred quantity
                $oldTargetStock = $targetProduct->isDeleted ? 0.0 : (float) $targetProduct->quantity;
                $newTargetStock = $oldTargetStock + $quantity;
                
                $targetProduct->update([
                    'quantity' => $newTargetStock,
                    'isDeleted' => 0
                ]);

                ProductInventory::create([
                    'product_id'    => $targetProduct->id,
                    'initial_stock' => $oldTargetStock,
                    'current_stock' => $newTargetStock,
                    'branch_id'     => $toBranchId,
                    'type'          => 'Stock Transfer In',
                    'create_by'     => $user->id,
                    'date'          => now(),
                    'remarks'       => $remarks
                ]);
            } else {
                // Replicate the product row for the target branch
                $targetProduct = $sourceProduct->replicate();
                $targetProduct->branch_id = $toBranchId;
                $targetProduct->brand_id = $brandId;
                $targetProduct->category_id = $categoryId;
                $targetProduct->unit_id = $unitId;
                $targetProduct->quantity = $quantity;
                $targetProduct->isDeleted = 0; // Ensure it is active
                $targetProduct->save();

                ProductInventory::create([
                    'product_id'    => $targetProduct->id,
                    'initial_stock' => $quantity,
                    'current_stock' => $quantity,
                    'branch_id'     => $toBranchId,
                    'type'          => 'Stock Transfer In',
                    'create_by'     => $user->id,
                    'date'          => now(),
                    'remarks'       => $remarks
                ]);
            }

            // 3. Save transfer history details
            DB::table('stock_transfers')->insert([
                'product_id'     => $sourceProduct->id,
                'from_branch_id' => $sourceProduct->branch_id,
                'to_branch_id'   => $toBranchId,
                'quantity'       => $quantity,
                'user_id'        => $user->id,
                'date'           => now(),
                'remarks'        => $remarks,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Stock transferred successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Failed to transfer stock: ' . $e->getMessage()
            ], 500);
        }
    }

    public function materialIndex(Request $request)
    {
        $user = Auth::guard('api')->user();
        $userId = $user->id;
        $userRole = $user->role;
        $userBranchId = $user->branch_id;

        $categoryId = $request->query('category_id');
        $brandId = $request->query('brand_id');
        $subBranchId = $request->sub_branch_id ?? $userId;
        $search = $request->query('search', '');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $perPage = $request->query('per_page', 10);
        $page = $request->query('page', 1);

        $query = \App\Models\RowMaterial::with([
            'category:id,name',
            'brand:id,name',
            'row_material_inventory:row_material_inventory.id,row_material_inventory.row_material_id,row_material_inventory.initial_stock,row_material_inventory.current_stock'
        ])
            ->select('id', 'row_materialname', 'SKU', 'price', 'quantity', 'created_at', 'category_id', 'brand_id', 'branch_id')
            ->where('isDeleted', 0)
            ->orderBy('id', 'desc');

        if ($userRole === 'admin') {
            if (!empty($subBranchId)) {
                $query->where('branch_id', $subBranchId);
            }
        } elseif ($userRole === 'sub-admin') {
            $query->where('branch_id', $userId);
        } elseif ($userRole === 'staff') {
            $query->where('branch_id', $userBranchId);
        }

        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        if (!empty($brandId)) {
            $query->where('brand_id', $brandId);
        }

        if (!empty($startDate)) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if (!empty($endDate)) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('row_materialname', 'like', "%{$search}%")
                    ->orWhere('SKU', 'like', "%{$search}%");
            });
        }

        $paginated = $query->paginate($perPage, ['*'], 'page', $page);

        $data = $paginated->map(function ($material) {
            $inventory = $material->row_material_inventory;

            return [
                'id' => $material->id,
                'product_name' => $material->row_materialname,
                'sku' => $material->SKU,
                'initial_stock' => $inventory->initial_stock ?? 0,
                'current_stock' => $material->quantity,
                'price' => $material->price,
                'remarks' => null,
                'created_at' => $material->created_at->format('Y-m-d'),
                'branch_id' => $material->branch_id,
            ];
        });

        return response()->json([
            'status' => true,
            'inventory' => $data,
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
                'from'         => $paginated->firstItem(),
                'to'           => $paginated->lastItem(),
            ]
        ]);
    }

    public function materialEditInventory(Request $request, $id)
    {
        $user = Auth::guard('api')->user();
        if (! StaffPermission::check(17, 'edit', $user) && ! StaffPermission::check(17, 'add', $user)) {
            return response()->json([
                'status' => false,
                'message' => 'You do not have permission for this action.',
            ], 403);
        }

        $material = \App\Models\RowMaterial::with('row_material_inventory:row_material_inventory.id,row_material_inventory.row_material_id,row_material_inventory.initial_stock,row_material_inventory.current_stock')
            ->select('id', 'row_materialname', 'SKU', 'price', 'quantity')
            ->find($id);

        if (!$material) {
            return response()->json([
                'status' => false,
                'message' => 'Material not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'product' => [
                'id' => $material->id,
                'product_name' => $material->row_materialname,
                'sku' => $material->SKU,
                'price' => $material->price,
                'initial_stock' => $material->row_material_inventory->initial_stock ?? 0,
                'current_stock' => $material->quantity ?? 0,
            ]
        ]);
    }

    public function materialInventoryUpdate(Request $request, $id)
    {
        $user = Auth::guard('api')->user();

        if (! StaffPermission::check(17, 'edit', $user) && ! StaffPermission::check(17, 'add', $user)) {
            return response()->json([
                'status' => false,
                'message' => 'You do not have permission for this action.',
            ], 403);
        }

        $userBranchId = $user->id ?? null;
        if ($user->role === 'staff' && $user->branch_id) {
            $userBranchId = $user->branch_id;
        } elseif (!empty($request->selectedSubAdminId)) {
            $userBranchId = $request->selectedSubAdminId;
        } else {
            $userBranchId = $user->id;
        }

        $material = \App\Models\RowMaterial::find($id);

        if (!$material) {
            return response()->json([
                'status' => false,
                'message' => 'Material not found'
            ], 404);
        }

        $adjustment = (float) $request->stock_quantity;
        $currentStock = (float) $material->quantity; 

        if ($request->stock_action === 'add') {
            $newStock = $currentStock + $adjustment;
        } elseif ($request->stock_action === 'minus') {
            $newStock = max(0, $currentStock - $adjustment);
        } else {
            $newStock = $currentStock; 
        }

        $material->update([
            'price' => $request->price,
            'quantity' => $newStock,
        ]);

        $inventoryPayload = [
            'row_material_id' => $id,
            'initial_stock' => $currentStock,
            'current_stock' => $newStock,
            'branch_id' => $userBranchId,
            'type' => $request->stock_action === 'add' ? 'Stock Added' : 'Stock Minus',
            'create_by' => $user->id,
            'date' => now(),
        ];

        \App\Models\RowMaterialInventory::create($inventoryPayload);

        return response()->json([
            'status' => true,
            'message' => 'Stock updated successfully',
            'new_stock' => $newStock
        ]);
    }

    public function getQuantityHistoryMaterial(Request $request, $productId)
    {
        $material = \App\Models\RowMaterial::select('id', 'row_materialname')->find($productId);
        if (!$material) {
            return response()->json([
                'status' => false,
                'message' => 'Material not found.'
            ]);
        }

        $search = $request->query('search', '');
        $perPage = $request->query('per_page', 10);
        $page = $request->query('page', 1);

        $query = \App\Models\RowMaterialInventory::from('row_material_inventory as pi')
            ->leftJoin('users as u', 'pi.create_by', '=', 'u.id')
            ->select('pi.*', 'u.name as user_name')
            ->where('pi.row_material_id', $productId);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('pi.type', 'like', "%{$search}%")
                    ->orWhere('pi.current_stock', 'like', "%{$search}%")
                    ->orWhere('u.name', 'like', "%{$search}%")
                    ->orWhereDate('pi.date', $search)
                    ->orWhereRaw("ABS(pi.current_stock - pi.initial_stock) LIKE ?", ["%{$search}%"]);
            });
        }

        $query->orderBy('pi.id', 'desc');
        $paginated = $query->paginate($perPage, ['*'], 'page', $page);

        $maxIdOnPage = $paginated->max('id');
        $previousStockMap = [];
        $runningStock = 0.0;

        if ($maxIdOnPage) {
            $priorRecords = \App\Models\RowMaterialInventory::where('row_material_id', $productId)
                ->where('id', '<=', $maxIdOnPage)
                ->orderBy('id')
                ->get(['id', 'current_stock']);

            foreach ($priorRecords as $record) {
                $previousStockMap[$record->id] = $runningStock;
                $runningStock = (float) ($record->current_stock ?? 0);
            }
        }

        $userIds = $paginated->pluck('create_by')
            ->merge($paginated->pluck('branch_id'))
            ->filter()
            ->unique()
            ->toArray();

        $users = !empty($userIds)
            ? User::select('id', 'name')
            ->whereIn('id', $userIds)
            ->get()
            ->keyBy('id')
            : collect();

        $history = $paginated->map(function ($inventory) use ($users, $previousStockMap) {
            $userId = $inventory->create_by ?? $inventory->branch_id;
            $user = $users->get($userId);

            $initial = (float) ($inventory->initial_stock ?? 0);
            $current = (float) ($inventory->current_stock ?? 0);
            
            if (array_key_exists($inventory->id, $previousStockMap)) {
                $beforeStock = $previousStockMap[$inventory->id];
                $change = round($current - $beforeStock, 2);
            } else {
                $change = round($current - $initial, 2);
            }

            if ($change > 0) {
                $signedQty = '+' . rtrim(rtrim(number_format($change, 2, '.', ''), '0'), '.');
            } elseif ($change < 0) {
                $signedQty = '-' . rtrim(rtrim(number_format(abs($change), 2, '.', ''), '0'), '.');
            } else {
                $signedQty = '0';
            }

            return [
                'type'               => $inventory->type,
                'initial_stock'      => $initial,
                'current_stock'      => $current,
                'quantity'           => $signedQty,
                'remarks'            => null,
                'name'               => $user->name ?? 'Unknown',
                'date'               => $inventory->date
                    ? \Carbon\Carbon::parse($inventory->date)->format('d-M-Y')
                    : ($inventory->created_at ? $inventory->created_at->format('d-M-Y') : 'N/A'),
            ];
        })->values()->toArray();

        return response()->json([
            'status' => true,
            'product' => $material->row_materialname,
            'history' => $history,
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
                'from'         => $paginated->firstItem(),
                'to'           => $paginated->lastItem(),
            ]
        ]);
    }

    /**
     * Material received from customers in exchange on sales bills, built from orders.exchange_details.
     */
    public function exchangeIndex(Request $request)
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated access'], 401);
        }

        $subAdminId = $request->input('selectedSubAdminId');
        if ($subAdminId === null || $subAdminId === '') {
            $subAdminId = session('selectedSubAdminId');
        }

        $query = \App\Models\Order::with('user:id,name,phone')
            ->select('id', 'order_number', 'user_id', 'branch_id', 'created_at', 'exchange_material_id', 'exchange_type_id', 'exchange_price', 'exchange_weight', 'exchange_amount', 'exchange_details')
            ->where('isDeleted', 0)
            ->where(function ($q) {
                $q->where('quotation_status', 'sales')->orWhereNull('quotation_status');
            })
            ->where(function ($q) {
                $q->whereNotNull('exchange_details')->orWhereNotNull('exchange_material_id');
            })
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        \App\Services\StaffDepartmentScope::applyOrderScope($query, $user, $subAdminId);

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $materialNames = \App\Models\Material::pluck('name', 'id');
        $typeNames = \App\Models\Type::pluck('type_name', 'id');

        $search = mb_strtolower(trim((string) $request->query('search', '')));
        $filterMaterial = (string) $request->query('material_id', '');
        $filterType = (string) $request->query('type_id', '');

        $entries = [];
        foreach ($query->get() as $order) {
            foreach ($this->exchangeItemsFromOrder($order) as $item) {
                $materialId = (string) ($item['material_id'] ?? '');
                $typeId = (string) ($item['type_id'] ?? '');
                $qty = (float) ($item['weight'] ?? 0);
                $price = (float) ($item['price'] ?? 0);
                $amount = (float) ($item['amount'] ?? 0);
                if ($amount <= 0 && $qty > 0 && $price > 0) {
                    $amount = round($qty * $price, 2);
                }

                $entry = [
                    'order_id'      => $order->id,
                    'order_number'  => $order->order_number,
                    'date'          => $order->created_at ? $order->created_at->format('d-M-Y') : '',
                    'customer'      => $order->user->name ?? 'Walk-in',
                    'phone'         => $order->user->phone ?? '',
                    'material_id'   => $materialId,
                    'material_name' => $materialNames[$materialId] ?? ($item['material_name'] ?? 'Unknown material'),
                    'type_id'       => $typeId,
                    'type_name'     => $typeId !== '' ? ($typeNames[$typeId] ?? ($item['type_name'] ?? '')) : ($item['type_name'] ?? ''),
                    'qty'           => round($qty, 3),
                    'price'         => round($price, 2),
                    'amount'        => round($amount, 2),
                ];

                if ($search !== '') {
                    $haystack = mb_strtolower($entry['material_name'] . ' ' . $entry['type_name'] . ' ' . $entry['customer'] . ' ' . $entry['phone'] . ' ' . $entry['order_number']);
                    if (mb_strpos($haystack, $search) === false) {
                        continue;
                    }
                }

                $entries[] = $entry;
            }
        }

        $summary = [];
        foreach ($entries as $entry) {
            $key = $entry['material_id'] . '|' . $entry['type_id'];
            if (!isset($summary[$key])) {
                $summary[$key] = [
                    'material_id'   => $entry['material_id'],
                    'material_name' => $entry['material_name'],
                    'type_id'       => $entry['type_id'],
                    'type_name'     => $entry['type_name'],
                    'qty'           => 0,
                    'amount'        => 0,
                    'bills'         => [],
                    'last_date'     => $entry['date'],
                ];
            }
            $summary[$key]['qty'] += $entry['qty'];
            $summary[$key]['amount'] += $entry['amount'];
            $summary[$key]['bills'][$entry['order_id']] = true;
        }
        $summary = array_values(array_map(function ($row) {
            $row['bills'] = count($row['bills']);
            $row['qty'] = round($row['qty'], 3);
            $row['amount'] = round($row['amount'], 2);
            $row['avg_price'] = $row['qty'] > 0 ? round($row['amount'] / $row['qty'], 2) : 0;
            return $row;
        }, $summary));
        usort($summary, function ($a, $b) {
            return strcmp($a['material_name'] . $a['type_name'], $b['material_name'] . $b['type_name']);
        });

        $totals = [
            'qty'    => round(array_sum(array_column($entries, 'qty')), 3),
            'amount' => round(array_sum(array_column($entries, 'amount')), 2),
            'bills'  => count(array_unique(array_column($entries, 'order_id'))),
        ];

        if ($filterMaterial !== '') {
            $entries = array_values(array_filter($entries, function ($entry) use ($filterMaterial, $filterType) {
                return $entry['material_id'] === $filterMaterial && $entry['type_id'] === $filterType;
            }));
        }

        $perPage = max(1, min(200, (int) $request->query('per_page', 25)));
        $total = count($entries);
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min(max(1, (int) $request->query('page', 1)), $lastPage);
        $offset = ($page - 1) * $perPage;

        return response()->json([
            'status'     => true,
            'totals'     => $totals,
            'summary'    => $summary,
            'entries'    => array_slice($entries, $offset, $perPage),
            'pagination' => [
                'current_page' => $page,
                'last_page'    => $lastPage,
                'per_page'     => $perPage,
                'total'        => $total,
                'from'         => $total ? $offset + 1 : 0,
                'to'           => min($offset + $perPage, $total),
            ],
        ]);
    }

    private function exchangeItemsFromOrder($order): array
    {
        $details = $order->exchange_details;
        if (is_string($details)) {
            $details = json_decode($details, true);
        }

        if (is_array($details)) {
            if (isset($details['items']) && is_array($details['items']) && count($details['items'])) {
                return array_values(array_filter($details['items'], 'is_array'));
            }
            if (array_is_list($details) && count($details)) {
                return array_values(array_filter($details, 'is_array'));
            }
            if (!empty($details['material_id'])) {
                return [$details];
            }
        }

        if (!empty($order->exchange_material_id)) {
            return [[
                'material_id' => $order->exchange_material_id,
                'type_id'     => $order->exchange_type_id,
                'price'       => $order->exchange_price,
                'weight'      => $order->exchange_weight,
                'amount'      => $order->exchange_amount,
            ]];
        }

        return [];
    }
}
