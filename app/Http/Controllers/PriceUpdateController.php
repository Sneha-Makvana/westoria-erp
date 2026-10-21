<?php

namespace App\Http\Controllers;

use App\Models\ProductPriceUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PriceUpdateController extends Controller
{
    /**
     * Display the pricing updates page.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $branchId = match (strtolower($user->role)) {
            'sub-admin' => $user->id,
            'admin'     => session('selectedSubAdminId') ?: $user->branch_id,
            'staff'     => $user->branch_id,
            default     => $user->branch_id,
        };
        
        $productId = $request->input('product_id');

        $pendingQuery = ProductPriceUpdate::with(['sourceProduct', 'targetProduct'])
            ->where('branch_id', $branchId)
            ->where('status', 'pending');
            
        if ($productId) {
            $pendingQuery->where(function($q) use ($productId) {
                $q->where('target_product_id', $productId)
                  ->orWhere('source_product_id', $productId);
            });
        }

        $pending = $pendingQuery->orderBy('created_at', 'desc')->get();

        $historyQuery = ProductPriceUpdate::with(['sourceProduct', 'targetProduct', 'resolver', 'branch'])
            ->where(function($query) use ($branchId) {
                $query->where('branch_id', $branchId)
                      ->orWhereHas('sourceProduct', function($q2) use ($branchId) {
                          $q2->where('branch_id', $branchId);
                      });
            })
            ->whereIn('status', ['accepted', 'rejected']);
            
        if ($productId) {
            $historyQuery->where(function($q) use ($productId) {
                $q->where('target_product_id', $productId)
                  ->orWhere('source_product_id', $productId);
            });
        }
            
        $history = $historyQuery->orderBy('updated_at', 'desc')
            ->take(50) // Limit history to last 50
            ->get();

        return view('product.pricing_updates', compact('pending', 'history'));
    }

    /**
     * Fetch pending price updates for the user's branch via API.
     */
    public function getPendingUpdates(Request $request)
    {
        $user = Auth::guard('api')->user() ?? Auth::user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $branchId = match (strtolower($user->role)) {
            'sub-admin' => $user->id,
            'admin'     => $request->input('selectedSubAdminId') ?: session('selectedSubAdminId') ?: $user->branch_id,
            'staff'     => $user->branch_id,
            default     => $user->branch_id,
        };

        $pending = ProductPriceUpdate::with(['sourceProduct', 'targetProduct'])
            ->where('branch_id', $branchId)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $history = ProductPriceUpdate::with(['sourceProduct', 'targetProduct', 'resolver', 'branch'])
            ->where(function($query) use ($branchId) {
                $query->where('branch_id', $branchId)
                      ->orWhereHas('sourceProduct', function($q2) use ($branchId) {
                          $q2->where('branch_id', $branchId);
                      });
            })
            ->whereIn('status', ['accepted', 'rejected'])
            ->orderBy('updated_at', 'desc')
            ->take(50) // Limit history to last 50
            ->get();

        return response()->json([
            'status' => true,
            'pending' => $pending,
            'history' => $history,
        ]);
    }

    /**
     * Confirm a price update.
     */
    public function confirm(Request $request, $id)
    {
        $user = Auth::guard('api')->user() ?? Auth::user();
        
        $updateReq = ProductPriceUpdate::findOrFail($id);
        
        if ($updateReq->status !== 'pending') {
            return response()->json(['status' => false, 'message' => 'This request has already been processed.']);
        }
        
        $targetProduct = $updateReq->targetProduct;
        if ($targetProduct) {
            $updateData = [];
            if ($updateReq->new_price !== null) $updateData['price'] = $updateReq->new_price;
            if ($updateReq->new_mrp !== null) $updateData['mrp'] = $updateReq->new_mrp;
            if ($updateReq->new_purchase_price !== null) $updateData['purchase_price'] = $updateReq->new_purchase_price;
            
            if (!empty($updateData)) {
                $targetProduct->update($updateData);
            }
        }
        
        $updateReq->update([
            'status' => 'accepted',
            'resolved_by' => $user->id ?? null,
        ]);
        
        return response()->json([
            'status' => true,
            'message' => 'Price update confirmed successfully.'
        ]);
    }

    /**
     * Reject a price update.
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::guard('api')->user() ?? Auth::user();
        
        $updateReq = ProductPriceUpdate::findOrFail($id);
        
        if ($updateReq->status !== 'pending') {
            return response()->json(['status' => false, 'message' => 'This request has already been processed.']);
        }
        
        $updateReq->update([
            'status' => 'rejected',
            'resolved_by' => $user->id ?? null,
        ]);
        
        return response()->json([
            'status' => true,
            'message' => 'Price update rejected.'
        ]);
    }
}
