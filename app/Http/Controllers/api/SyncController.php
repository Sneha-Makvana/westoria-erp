<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Unit;
use App\Models\Product;
use App\Models\User;
use App\Models\ProductVariant;
use App\Models\PurchaseInvoice;
use App\Models\Purchases;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SyncController extends Controller
{
    // =============================================
    // AUTH: POST /api/sync/login
    // Get Bearer Token for Sync APIs
    // =============================================
    public function apiLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)
                    ->where('isDeleted', '!=', 1)
                    ->orderByDesc('id')
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid email or password.',
            ], 401);
        }

        if ((int)($user->status ?? 1) === 0) {
            return response()->json([
                'status'  => false,
                'message' => 'Your account is inactive. Please contact admin.',
            ], 403);
        }

        // Create Passport token (no session needed)
        $token = $user->createToken('SyncAPIToken')->accessToken;

        return response()->json([
            'status'  => true,
            'message' => 'Login successful.',
            'token'   => $token,
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ], 200);
    }

    // Desktop App → Server (Upload full local DB)
    // =============================================
    public function fullPush(Request $request)
    {
        try {
            DB::beginTransaction();

            $categories       = $request->input('categories', []);
            $brands           = $request->input('brands', []);
            $units            = $request->input('units', []);
            $products         = $request->input('products', []);
            $productVariants  = $request->input('productVariants', []);
            $vendors          = $request->input('vendors', []);
            $purchases        = $request->input('purchases', []);
            $sales            = $request->input('sales', []);

            // ---- 1. Sync Categories ----
            $catCount = 0;
            foreach ($categories as $cat) {
                Category::updateOrCreate(
                    ['id' => $cat['id']],
                    [
                        'name'      => $cat['name'],
                        'isDeleted' => $cat['isDeleted'] ?? 0,
                    ]
                );
                $catCount++;
            }

            // ---- 2. Sync Brands ----
            $brandCount = 0;
            foreach ($brands as $brand) {
                Brand::updateOrCreate(
                    ['id' => $brand['id']],
                    [
                        'name'      => $brand['name'],
                        'isDeleted' => $brand['isDeleted'] ?? 0,
                    ]
                );
                $brandCount++;
            }

            // ---- 3. Sync Units ----
            $unitCount = 0;
            foreach ($units as $unit) {
                Unit::updateOrCreate(
                    ['id' => $unit['id']],
                    [
                        'unit_name' => $unit['unitName'],
                        'is_delete' => $unit['isDelete'] ?? 0,
                    ]
                );
                $unitCount++;
            }

            // ---- 4. Sync Products ----
            $productCount = 0;
            foreach ($products as $prod) {
                Product::updateOrCreate(
                    ['id' => $prod['id']],
                    [
                        'name'            => $prod['name'],
                        'SKU'             => $prod['sku'] ?? null,
                        'category_id'     => $prod['categoryId'] ?? null,
                        'brand_id'        => $prod['brandId'] ?? null,
                        'unit_id'         => $prod['unitId'] ?? null,
                        'hsn_code'        => $prod['hsnCode'] ?? null,
                        'barcode'         => $prod['barcode'] ?? null,
                        'price'           => $prod['price'] ?? 0,
                        'purchase_price'  => $prod['purchasePrice'] ?? 0,
                        'cost_price'      => $prod['purchasePrice'] ?? 0,
                        'quantity'        => $prod['quantity'] ?? 0,
                        'status'          => $prod['status'] ?? 'active',
                        'availablility'   => $prod['availability'] ?? 'in_stock',
                        'description'     => $prod['description'] ?? null,
                        'item_type'       => $prod['itemType'] ?? null,
                        'is_rent_available' => $prod['isRentAvailable'] ?? 0,
                        'isDeleted'       => $prod['isDeleted'] ?? 0,
                    ]
                );
                $productCount++;
            }

            // ---- 5. Sync Product Variants ----
            $productVariantCount = 0;
            foreach ($productVariants as $pv) {
                ProductVariant::updateOrCreate(
                    ['id' => $pv['id']],
                    [
                        'product_id'     => $pv['productId'] ?? null,
                        'size'           => $pv['size'] ?? null,
                        'color'          => $pv['color'] ?? null,
                        'qty'            => $pv['qty'] ?? 0,
                        'price'          => $pv['price'] ?? 0,
                        'purchase_price' => $pv['purchasePrice'] ?? 0,
                        'barcode'        => $pv['barcode'] ?? null,
                    ]
                );
                $productVariantCount++;
            }

            // ---- 6. Sync Vendors ----
            $vendorCount = 0;
            foreach ($vendors as $ven) {
                User::updateOrCreate(
                    ['id' => $ven['id']],
                    [
                        'name'         => $ven['name'],
                        'email'        => $ven['email'] ?? null,
                        'phone'        => $ven['phone'] ?? null,
                        'company_name' => $ven['companyName'] ?? null,
                        'gst_number'   => $ven['gstNumber'] ?? null,
                        'role'         => 'supplier', // or 'vendor' depending on system
                        'isDeleted'    => $ven['isDeleted'] ?? 0,
                    ]
                );
                $vendorCount++;
            }

            // ---- 7. Sync Purchases ----
            $purchaseCount = 0;
            $invoiceGroups = collect($purchases)->groupBy('invoiceId');
            foreach ($invoiceGroups as $invoiceId => $items) {
                // Find or create parent PurchaseInvoice
                $invoice = PurchaseInvoice::firstOrCreate(
                    ['id' => $invoiceId],
                    [
                        'invoice_number' => 'INV-' . $invoiceId,
                        'vendor_id'      => collect($items)->first()['vendorId'] ?? null,
                        'purchase_date'  => Carbon::now('Asia/Kolkata')->format('Y-m-d'),
                        'total_amount'   => collect($items)->sum(function($q) { return ($q['qty'] ?? 0) * ($q['price'] ?? 0); }),
                        'status'         => 'Completed',
                    ]
                );

                foreach ($items as $pur) {
                    Purchases::updateOrCreate(
                        ['id' => $pur['id']],
                        [
                            'invoice_id'         => $invoice->id,
                            'vendor_id'          => $pur['vendorId'] ?? null,
                            'item'               => $pur['productId'] ?? null,
                            'product_variant_id' => $pur['variantId'] ?? null,
                            'quantity'           => $pur['qty'] ?? 0,
                            'price'              => $pur['price'] ?? 0,
                            'discount_amount'    => $pur['discountAmount'] ?? 0,
                            'discount_percent'   => $pur['discountPercent'] ?? 0,
                            'isDeleted'          => $pur['isDeleted'] ?? 0,
                        ]
                    );
                    $purchaseCount++;
                }
            }

            // ---- 8. Sync Sales ----
            $saleCount = 0;
            $orderGroups = collect($sales)->groupBy('orderId');
            foreach ($orderGroups as $orderId => $items) {
                // Find or create parent Order
                $order = Order::firstOrCreate(
                    ['id' => $orderId],
                    [
                        'order_number' => 'ORD-' . $orderId,
                        'total_amount' => collect($items)->sum('totalAmount'),
                        'order_type'   => 'sales',
                    ]
                );

                foreach ($items as $sal) {
                    OrderItem::updateOrCreate(
                        ['id' => $sal['id']],
                        [
                            'order_id'           => $order->id,
                            'product_id'         => $sal['productId'] ?? null,
                            'product_variant_id' => $sal['variantId'] ?? null,
                            'quantity'           => $sal['qty'] ?? 0,
                            'price'              => $sal['price'] ?? 0,
                            'total_amount'       => $sal['totalAmount'] ?? 0,
                            'discount_percentage'=> $sal['discountPercentage'] ?? 0,
                            'discount_amount'    => $sal['discountAmount'] ?? 0,
                            'isDeleted'          => $sal['isDeleted'] ?? 0,
                        ]
                    );
                    $saleCount++;
                }
            }

            DB::commit();

            return response()->json([
                'success'   => true,
                'message'   => 'Full database synced successfully',
                'syncedAt'  => Carbon::now('UTC')->toIso8601String(),
                'recordsProcessed' => [
                    'categories'     => $catCount,
                    'brands'         => $brandCount,
                    'units'          => $unitCount,
                    'products'       => $productCount,
                    'productVariants'=> $productVariantCount,
                    'vendors'        => $vendorCount,
                    'purchases'      => $purchaseCount,
                    'sales'          => $saleCount,
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage()
            ], 500);
        }
    }

    // =============================================
    // API 2: GET /api/sync/full-pull
    // Server → Desktop App (Download full server DB)
    // =============================================
    public function fullPull(Request $request)
    {
        try {
            $categories = Category::where('isDeleted', 0)->get()->map(function ($cat) {
                return [
                    'id'        => $cat->id,
                    'name'      => $cat->name,
                    'isDeleted' => (bool)$cat->isDeleted,
                ];
            });

            $brands = Brand::where('isDeleted', 0)->get()->map(function ($brand) {
                return [
                    'id'        => $brand->id,
                    'name'      => $brand->name,
                    'isDeleted' => (bool)$brand->isDeleted,
                ];
            });

            $units = Unit::all()->map(function ($unit) {
                return [
                    'id'       => $unit->id,
                    'unitName' => $unit->unit_name,
                    'isDelete' => (bool)$unit->is_delete,
                ];
            });

            $products = Product::with(['category', 'brand', 'unit'])
                ->where('isDeleted', 0)
                ->get()
                ->map(function ($prod) {
                    $imageUrl = null;
                    if (!empty($prod->image_url)) {
                        $imageUrl = is_array($prod->image_url)
                            ? ($prod->image_url[0] ?? null)
                            : $prod->image_url;
                    }

                    return [
                        'id'              => $prod->id,
                        'name'            => $prod->name,
                        'sku'             => $prod->SKU,
                        'categoryId'      => $prod->category_id,
                        'category'        => $prod->category ? $prod->category->name : null,
                        'brandId'         => $prod->brand_id,
                        'brand'           => $prod->brand ? $prod->brand->name : null,
                        'unitId'          => $prod->unit_id,
                        'unit'            => $prod->unit ? $prod->unit->unit_name : null,
                        'hsnCode'         => $prod->hsn_code,
                        'barcode'         => $prod->barcode,
                        'price'           => (float)$prod->price,
                        'purchasePrice'   => (float)($prod->purchase_price ?? $prod->cost_price ?? 0),
                        'quantity'        => (float)$prod->quantity,
                        'status'          => $prod->status,
                        'availability'    => $prod->availablility,
                        'description'     => $prod->description,
                        'imageUrl'        => $imageUrl,
                        'itemType'        => $prod->item_type,
                        'isRentAvailable' => (bool)$prod->is_rent_available,
                        'isDeleted'       => (bool)$prod->isDeleted,
                        'variants'        => [], // extend later if needed
                    ];
                });

            $vendors = User::where('role', 'supplier')->where('isDeleted', 0)->get()->map(function ($v) {
                return [
                    'id'          => $v->id,
                    'name'        => $v->name,
                    'email'       => $v->email,
                    'phone'       => $v->phone,
                    'companyName' => $v->company_name,
                    'gstNumber'   => $v->gst_number,
                    'isDeleted'   => (bool)$v->isDeleted,
                ];
            });

            $productVariants = ProductVariant::all()->map(function ($pv) {
                return [
                    'id'            => $pv->id,
                    'productId'     => $pv->product_id,
                    'size'          => $pv->size,
                    'color'         => $pv->color,
                    'qty'           => (float)$pv->qty,
                    'price'         => (float)$pv->price,
                    'purchasePrice' => (float)$pv->purchase_price,
                    'barcode'       => $pv->barcode,
                ];
            });

            $purchases = Purchases::where('isDeleted', 0)->get()->map(function ($pur) {
                return [
                    'id'              => $pur->id,
                    'invoiceId'       => $pur->invoice_id,
                    'vendorId'        => $pur->vendor_id,
                    'productId'       => $pur->item,
                    'variantId'       => $pur->product_variant_id,
                    'qty'             => (float)$pur->quantity,
                    'price'           => (float)$pur->price,
                    'discountAmount'  => (float)$pur->discount_amount,
                    'discountPercent' => (float)$pur->discount_percent,
                    'isDeleted'       => (bool)$pur->isDeleted,
                ];
            });

            $sales = OrderItem::where('isDeleted', 0)->get()->map(function ($sal) {
                return [
                    'id'                 => $sal->id,
                    'orderId'            => $sal->order_id,
                    'productId'          => $sal->product_id,
                    'variantId'          => $sal->product_variant_id,
                    'qty'                => (float)$sal->quantity,
                    'price'              => (float)$sal->price,
                    'totalAmount'        => (float)$sal->total_amount,
                    'discountPercentage' => (float)$sal->discount_percentage,
                    'discountAmount'     => (float)$sal->discount_amount,
                    'isDeleted'          => (bool)$sal->isDeleted,
                ];
            });

            return response()->json([
                'success'  => true,
                'syncedAt' => Carbon::now('UTC')->toIso8601String(),
                'data' => [
                    'categories'      => $categories,
                    'brands'          => $brands,
                    'units'           => $units,
                    'products'        => $products,
                    'vendors'         => $vendors,
                    'purchases'       => $purchases,
                    'sales'           => $sales,
                    'productVariants' => $productVariants
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to pull data: ' . $e->getMessage()
            ], 500);
        }
    }

    // =============================================
    // API 3: POST /api/sync/images-batch
    // Batch Image Upload for Products
    // =============================================
    public function imagesBatch(Request $request)
    {
        try {
            $mappingJson = $request->input('mapping');
            $mapping     = json_decode($mappingJson, true) ?? [];

            if (!$request->hasFile('images')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No images provided.'
                ], 400);
            }

            $images  = $request->file('images');
            $uploadedCount = 0;

            // Build filename → file object map for quick lookup
            $fileMap = [];
            foreach ($images as $img) {
                $fileMap[$img->getClientOriginalName()] = $img;
            }

            // mapping format: { "101": "iphone13.jpg", "102": ["front.jpg", "back.jpg"] }
            foreach ($mapping as $productId => $fileNames) {
                $product = Product::find($productId);
                if (!$product) continue;

                if (!is_array($fileNames)) {
                    $fileNames = [$fileNames];
                }

                $paths = [];
                // if you want to append to existing, you can uncomment below:
                // $paths = json_decode($product->images, true) ?? [];

                foreach ($fileNames as $fileName) {
                    if (!isset($fileMap[$fileName])) continue;

                    $file        = $fileMap[$fileName];
                    $newFileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                    // Store to storage/app/public/product_images
                    $paths[] = $file->storeAs('product_images', $newFileName, 'public');
                    $uploadedCount++;
                }

                if (!empty($paths)) {
                    $product->images = json_encode($paths);
                    $product->save();
                    $product->syncImagesToBranchCopies();
                }
            }

            if ($uploadedCount == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Successfully uploaded 0 image(s). Filenames did not match mapping.',
                    'received_files' => array_keys($fileMap),
                    'provided_mapping' => $mapping
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully uploaded {$uploadedCount} image(s).",
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Image upload failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
