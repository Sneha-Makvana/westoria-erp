<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Services\Purchase\PurchaseBillPages;
use App\Services\Purchase\PurchaseBillVisionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PurchaseAiBillController extends Controller
{
    public function index(): View
    {
        $branchId = $this->branchId();
        $aiReady = app(PurchaseBillVisionService::class)->isConfigured();

        return view('purchase.ai-bill', [
            'vendors' => $this->vendors($branchId),
            'categories' => $this->categories($branchId),
            'products' => $this->products($branchId),
            'aiReady' => $aiReady,
            'banks' => \App\Models\BankMaster::where('branch_id', $branchId)->where('isDeleted', 0)->where('status', 1)->get(),
        ]);
    }

    public function parse(Request $request, PurchaseBillVisionService $vision): JsonResponse
    {
        $request->validate([
            'bill' => 'required|file|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        try {
            $extracted = $vision->extract($request->file('bill'));
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        $branchId = $this->branchId();
        $products = $this->products($branchId);
        $vendors = $this->vendors($branchId);
        $categories = $this->categories($branchId);

        $matchedVendor = $this->matchVendor($extracted['vendor_name'], $extracted['vendor_phone'], $vendors);
        $generalCategory = $categories->first(fn ($category) => strtolower(trim((string) $category['name'])) === 'general');
        $defaultCategory = $categories->first();

        $items = [];
        foreach ($extracted['items'] as $item) {
            $quantity = $item['purchase_quantity'] > 0
                ? $item['purchase_quantity']
                : 1;
            $amount = (float) $item['amount'];
            if ($amount <= 0 && $item['rate'] > 0 && $quantity > 0) {
                $amount = round($item['rate'] * $quantity, 2);
            }

            $unitPrice = $item['rate'] > 0 ? (float) $item['rate'] : ($quantity > 0 ? round($amount / $quantity, 4) : 0);
            $match = $this->matchProduct($item['name'], $item['hsn'], $item['barcode'] ?? '', $products);

            $items[] = array_replace($item, [
                'name' => $item['name'],
                'hsn' => $item['hsn'],
                'barcode' => $item['barcode'] ?? '',
                'mrp' => $item['mrp'],
                'rate' => $item['rate'],
                'amount' => $amount,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'product_id' => $match['id'] ?? null,
                'category_id' => $match['category_id'] ?? ($generalCategory['id'] ?? 'General'),
                'current_stock' => $match['quantity'] ?? 0,
                'action' => $match ? 'update' : 'create',
            ]);
        }

        $grandTotal = (float) $extracted['grand_total'];
        $itemsTotal = round(array_sum(array_column($items, 'amount')), 2);
        if ($grandTotal <= 0 && ($extracted['page_role'] ?? '') !== 'continuation') {
            $grandTotal = $itemsTotal;
        }

        return response()->json([
            'success' => true,
            'warnings' => $extracted['warnings'] ?? [],
            'header' => array_replace(collect($extracted)->except(['items'])->all(), [
                'vendor_id' => $matchedVendor['id'] ?? null,
                'vendor_name' => $extracted['vendor_name'],
                'vendor_phone' => $extracted['vendor_phone'],
                'vendor_address' => $extracted['vendor_address'],
                'vendor_email' => $extracted['vendor_email'] ?? '',
                'vendor_gstin' => $extracted['vendor_gstin'] ?? '',
                'bill_no' => $extracted['bill_no'],
                'bill_date' => $extracted['bill_date'],
                'customer_name' => $extracted['customer_name'],
                'grand_total' => $grandTotal,
                'grand_total_extracted' => (float) $extracted['grand_total'] > 0,
                'grand_total_is_final' => (bool) ($extracted['grand_total_is_final'] ?? false),
                'page_role' => $extracted['page_role'] ?? '',
                'page_number' => (int) ($extracted['page_number'] ?? 0),
                'expected_item_count' => (int) ($extracted['expected_item_count'] ?? 0),
                'first_serial' => (int) ($extracted['first_serial'] ?? 0),
                'last_serial' => (int) ($extracted['last_serial'] ?? 0),
                'items_total' => $itemsTotal,
                'invoice_discount_amount' => (float) ($extracted['invoice_discount_amount'] ?? 0),
                'invoice_discount_percent' => (float) ($extracted['invoice_discount_percent'] ?? 0),
                'cgst_amount' => (float) ($extracted['cgst_amount'] ?? 0),
                'sgst_amount' => (float) ($extracted['sgst_amount'] ?? 0),
                'igst_amount' => (float) ($extracted['igst_amount'] ?? 0),
                'rounding_adjustment' => (float) ($extracted['rounding_adjustment'] ?? 0),
                'amount_in_words' => $extracted['amount_in_words'],
                'payment_status' => $extracted['payment_status'] ?? 'unpaid',
                'payment_mode' => $extracted['payment_mode'] ?? '',
            ]),
            'items' => $items,
        ]);
    }

    public function mergePages(Request $request): JsonResponse
    {
        $pages = $request->input('pages', []);
        if (! is_array($pages) || $pages === []) {
            return response()->json(['success' => false, 'message' => 'No pages to combine.'], 422);
        }
        if (count($pages) > 8) {
            return response()->json(['success' => false, 'message' => 'Upload at most 8 pages of one bill.'], 422);
        }

        return response()->json(PurchaseBillPages::merge($pages));
    }

    private function branchId(): int
    {
        $user = Auth::user();
        $selectedSubAdminId = session('selectedSubAdminId');

        if ($user->role === 'staff' && $user->branch_id) {
            return (int) $user->branch_id;
        }

        if ($user->role === 'admin' && ! empty($selectedSubAdminId)) {
            return (int) $selectedSubAdminId;
        }

        return (int) $user->id;
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function vendors(int $branchId)
    {
        return User::where('role', 'vendor')
            ->where('branch_id', $branchId)
            ->where('isDeleted', 0)
            ->orderBy('name')
            ->get(['id', 'name', 'phone'])
            ->map(fn (User $vendor) => [
                'id' => $vendor->id,
                'name' => $vendor->name,
                'phone' => $vendor->phone,
            ])
            ->values();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function categories(int $branchId)
    {
        return Category::where('branch_id', $branchId)
            ->where('isDeleted', 0)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Category $category) => [
                'id' => $category->id,
                'name' => $category->name,
            ])
            ->values();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function products(int $branchId)
    {
        return Product::where('isDeleted', 0)
            ->where('status', 'active')
            ->where(function ($query) use ($branchId) {
                $query->where('branch_id', $branchId)->orWhereNull('branch_id');
            })
            ->orderBy('name')
            ->get(['id', 'name', 'category_id', 'hsn_code', 'barcode', 'quantity', 'price', 'mrp'])
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'category_id' => $product->category_id,
                'hsn_code' => $product->hsn_code,
                'barcode' => $product->barcode,
                'quantity' => (float) $product->quantity,
                'price' => (float) $product->price,
                'mrp' => (float) $product->mrp,
            ])
            ->values();
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $vendors
     * @return array<string, mixed>|null
     */
    private function matchVendor(string $name, string $phone, $vendors): ?array
    {
        if ($phone !== '') {
            $byPhone = $vendors->first(function ($vendor) use ($phone) {
                return preg_replace('/\D+/', '', (string) $vendor['phone']) === $phone;
            });
            if ($byPhone) {
                return $byPhone;
            }
        }

        $needle = $this->normalizeName($name);
        if ($needle === '') {
            return null;
        }

        $best = null;
        $bestScore = 0;
        foreach ($vendors as $vendor) {
            similar_text($needle, $this->normalizeName((string) $vendor['name']), $percent);
            if ($percent > $bestScore) {
                $bestScore = $percent;
                $best = $vendor;
            }
        }

        return $bestScore >= 72 ? $best : null;
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $products
     * @return array<string, mixed>|null
     */
    private function matchProduct(string $name, string $hsn, string $barcode, $products): ?array
    {
        $barcode = trim($barcode);
        if ($barcode !== '') {
            $byBarcode = $products->first(function ($product) use ($barcode) {
                return trim((string) ($product['barcode'] ?? '')) === $barcode;
            });
            if ($byBarcode) {
                return $byBarcode;
            }
        }

        $needle = $this->normalizeName($name);
        $hsn = trim($hsn);

        $best = null;
        $bestScore = 0;

        foreach ($products as $product) {
            $haystack = $this->normalizeName((string) $product['name']);
            similar_text($needle, $haystack, $percent);
            $nameScore = (float) $percent;

            if ($needle !== '' && $haystack !== '' && (str_contains($haystack, $needle) || str_contains($needle, $haystack))) {
                $nameScore = max($nameScore, 88);
            }

            // Product identity is by name (and barcode above). HSN only breaks ties.
            if ($nameScore < 78) {
                continue;
            }

            $score = $nameScore;
            if ($hsn !== '' && trim((string) ($product['hsn_code'] ?? '')) === $hsn) {
                $score += 12;
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $product;
            }
        }

        return $bestScore >= 78 ? $best : null;
    }

    private function normalizeName(string $value): string
    {
        $value = mb_strtolower($value);
        $value = preg_replace('/[\p{Gujarati}\p{Devanagari}]+/u', ' ', $value) ?? $value;
        $value = preg_replace('/[^a-z0-9]+/i', ' ', $value) ?? $value;

        return trim(preg_replace('/\s+/', ' ', $value) ?? $value);
    }
}
