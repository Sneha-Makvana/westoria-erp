<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Services\EInvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EinvoiceController extends Controller
{
    /**
     * Public (no-auth) endpoint: Get E-Invoice details by IRN.
     */
    public function getEinvoiceByIrnPublic(Request $request, string $irn)
    {
        $irn = trim((string) $irn);
        if ($irn === '' || strlen($irn) < 60) {
            return response()->json(['status' => false, 'message' => 'Invalid IRN.'], 422);
        }

        $branchId = $request->query('branch_id');
        $settingQuery = Setting::query()->whereNotNull('einvoice_api_url');
        if (!empty($branchId)) {
            $settingQuery->where('branch_id', (int) $branchId);
        }
        $setting = $settingQuery->orderByDesc('einvoice_enabled')->orderByDesc('id')->first();

        $svc = new EInvoiceService($setting ?? new Setting());
        if (!$svc->isEnabled()) {
            return response()->json([
                'status' => false,
                'message' => 'E-Invoice settings not configured in Settings.',
            ], 422);
        }

        try {
            // Assuming EInvoiceService has getEinvoiceDetailsByIrn
            $result = $svc->getEinvoiceDetailsByIrn($irn);
            return response()->json([
                'status' => true,
                'message' => 'E-Invoice details fetched.',
                'data' => $result['data'] ?? [],
                'raw_response' => $result['raw_response'] ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::error('E-Invoice: get by IRN failed', [
                'irn' => substr($irn, 0, 12) . '...',
                'msg' => $e->getMessage(),
            ]);
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function auth(Request $request)
    {
        // Mocking NIC API Authentication success for E-Invoice
        return response()->json([
            'status' => '1',
            'status_cd' => '1',
            'message' => 'Authenticated successfully',
            'data' => [
                'authtoken' => 'TOKEN_' . bin2hex(random_bytes(16)),
                'sek' => bin2hex(random_bytes(16)),
                'tokenExpiry' => date('Y-m-d H:i:s', strtotime('+6 hours'))
            ]
        ]);
    }

    /**
     * Generate E-Invoice for an order (auth required).
     */
    public function generate(Request $request)
    {
        $authUser = Auth::guard('api')->user();
        if (!$authUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'order_id' => 'required|integer',
        ]);

        $order = Order::find((int) $request->order_id);
        if (!$order) {
            return response()->json(['status' => false, 'message' => 'Order not found'], 404);
        }

        if (!empty($order->irn)) {
            return response()->json([
                'status' => false,
                'message' => 'IRN already generated for this order.',
            ], 422);
        }

        $items = OrderItem::with('product:id,hsn_code,code_name')
            ->where('order_id', $order->id)
            ->where(function ($q) {
                $q->whereNull('isDeleted')->orWhere('isDeleted', 0);
            })
            ->get();

        if ($items->count() === 0) {
            return response()->json([
                'status' => false,
                'message' => 'No items found in this order.',
            ], 422);
        }

        // Resolve branch settings deterministically
        $selectedSubAdminId = $request->input('selectedSubAdminId');
        $selectedSubAdminId = (is_string($selectedSubAdminId) && is_numeric($selectedSubAdminId))
            ? (int) $selectedSubAdminId
            : null;

        if ($authUser->role === 'staff' && !empty($authUser->branch_id)) {
            $branchId = (int) $authUser->branch_id;
        } elseif ($authUser->role === 'admin' && !empty($selectedSubAdminId)) {
            $branchId = (int) $selectedSubAdminId;
        } else {
            $branchId = (int) $authUser->id;
        }

        $setting = Setting::where('branch_id', $branchId)->first()
            ?? Setting::where('branch_id', 1)->first();

        Log::info('E-Invoice: using settings branch', [
            'order_id' => $order->id,
            'branch_id' => $branchId,
            'has_setting' => (bool) $setting,
            'einvoice_api_url' => $setting->einvoice_api_url ?? null,
            'einvoice_enabled' => $setting->einvoice_enabled ?? null,
        ]);

        $svc = new EInvoiceService($setting ?? new Setting());
        if (!$svc->isEnabled()) {
            return response()->json([
                'status' => false,
                'message' => 'E-Invoice settings not configured. E-Invoice generation requires credentials.',
            ], 422);
        }

        try {
            // Assuming EInvoiceService has generateEinvoice
            $result = $svc->generateEinvoice($order->id);
        } catch (\Throwable $e) {
            $msg = $e->getMessage();
            Log::error('E-Invoice: generation failed', ['order_id' => $order->id, 'msg' => $msg]);
            return response()->json(['status' => false, 'message' => $msg], 422);
        }

        $irn = (string) ($result['Irn'] ?? $result['irn'] ?? '');
        $qrCode = (string) ($result['SignedQRCode'] ?? $result['signedQRCode'] ?? '');
        $ackNo = (string) ($result['AckNo'] ?? $result['ackNo'] ?? '');
        $ackDate = $result['AckDt'] ?? $result['ackDt'] ?? null;
        $ackDateParsed = $ackDate ? \Carbon\Carbon::parse($ackDate) : now();

        if (empty($irn)) {
             return response()->json(['status' => false, 'message' => 'Failed to retrieve IRN from response'], 422);
        }

        $order->update([
            'irn' => $irn,
            'irn_qr_code' => $qrCode,
            'irn_ack_no' => $ackNo,
            'irn_ack_date' => $ackDateParsed,
            'irn_status' => 'generated',
            'einvoice_response' => (string) ($result['raw_response'] ?? null),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'E-Invoice generated and saved.',
            'order_id' => $order->id,
            'irn' => $irn,
            'ack_no' => $ackNo,
            'ack_date' => $ackDateParsed->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get E-Invoice Details Dummy API
     */
    public function getEinvoice(Request $request)
    {
        $irn = $request->query('irn') ?? 'dummy_irn_64_characters_string_123456789012345678901234567890';

        return response()->json([
            'status' => '1',
            'status_cd' => '1',
            'message' => 'E-Invoice Details Fetched',
            'data' => [
                'irn' => $irn,
                'ackNo' => '123456789012345',
                'ackDt' => date('d/m/Y H:i:s'),
                'sellerGstin' => '29AAAAA0000A1Z5',
                'docNo' => 'INV-001',
                'docDate' => '17/04/2026',
                'buyerGstin' => '27BBBBB0000B1Z5',
                'totalValue' => 50000.00,
                'status' => 'ACT',
            ]
        ]);
    }

    /**
     * Cancel E-Invoice Dummy API
     */
    public function cancelEinvoice(Request $request)
    {
        $request->validate([
            'irn' => 'required',
            'cancelReason' => 'required',
            'cancelRemarks' => 'nullable'
        ]);

        return response()->json([
            'status' => '1',
            'status_cd' => '1',
            'message' => 'E-Invoice Cancelled Successfully',
            'data' => [
                'irn' => $request->irn,
                'cancelDate' => date('d/m/Y H:i:s')
            ]
        ]);
    }
}
