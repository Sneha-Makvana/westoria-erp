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

class EwayBillController extends Controller
{
    /**
     * Public (no-auth) endpoint: Get E-Way Bill details by IRN.
     * Calls NIC/GSP URL: {Einv1URL}eiewb/v1.03/ewaybill/irn/{irn}
     */
    public function getEwayBillByIrnPublic(Request $request, string $irn)
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
            $result = $svc->getEwayBillDetailsByIrn($irn);
            return response()->json([
                'status' => true,
                'message' => 'E-Way Bill details fetched.',
                'data' => $result['data'] ?? [],
                'raw_response' => $result['raw_response'] ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::error('EWB: get by IRN failed', [
                'irn' => substr($irn, 0, 12) . '...',
                'msg' => $e->getMessage(),
            ]);
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function auth(Request $request)
    {
        // Mocking NIC API Authentication success
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
     * Generate E-Way Bill (by IRN) for an order (auth required).
     */
    public function generate(Request $request)
    {
        $authUser = Auth::guard('api')->user();
        if (!$authUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'order_id' => 'required|integer',
            'vehicle_no' => 'required|string|max:20',
            'distance' => 'required|integer|min:0|max:5000',
        ]);

        $order = Order::find((int) $request->order_id);
        if (!$order) {
            return response()->json(['status' => false, 'message' => 'Order not found'], 404);
        }

        if (empty($order->irn)) {
            return response()->json([
                'status' => false,
                'message' => 'IRN not found for this order. Please generate E-Invoice (IRN) first.',
            ], 422);
        }

        $items = OrderItem::with('product:id,hsn_code,code_name')
            ->where('order_id', $order->id)
            ->where(function ($q) {
                $q->whereNull('isDeleted')->orWhere('isDeleted', 0);
            })
            ->get();

        if ($items->count() > 0) {
            $hasGoods = $items->contains(function ($it) {
                $codeType = strtoupper((string) optional($it->product)->code_name);
                if ($codeType === 'SAC') return false;
                $hsn = (string) optional($it->product)->hsn_code;
                $digits = preg_replace('/\D/', '', $hsn);
                if ($digits === '') return false;
                return !str_starts_with($digits, '99');
            });
            if (!$hasGoods) {
                return response()->json([
                    'status' => false,
                    'message' => 'E-Way Bill cannot be generated because this invoice contains only services (SAC/HSN 99).',
                ], 422);
            }
        }

        // Resolve branch settings deterministically (avoid picking a random settings row).
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

        // Primary: selected branch. Fallback: branch_id=1 (main company settings).
        $setting = Setting::where('branch_id', $branchId)->first()
            ?? Setting::where('branch_id', 1)->first();

        Log::info('EWB: using settings branch', [
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
                'message' => 'E-Invoice settings not configured. E-Way Bill (by IRN) requires IRP credentials.',
            ], 422);
        }

        $vehicleNoNorm = strtoupper(str_replace(' ', '', (string) $request->vehicle_no));
        $distanceKm = (int) $request->distance;

        try {
            $result = $svc->generateEwayBillByIrn(
                (string) $order->irn,
                $vehicleNoNorm,
                $distanceKm
            );
        } catch (\Throwable $e) {
            $msg = $e->getMessage();

            // NIC error 4002: EWB already generated for this IRN.
            // Treat as success by fetching existing details and saving them locally.
            if (str_contains($msg, '4002')) {
                try {
                    $details = $svc->getEwayBillDetailsByIrn((string) $order->irn);
                    $data = $details['data'] ?? [];

                    $ewbNo = $data['EwbNo'] ?? $data['Ewbno'] ?? $data['ewayBillNo'] ?? $data['EwbNo'] ?? null;
                    $ewbDt = $data['EwbDt'] ?? $data['EwbDate'] ?? $data['ewayBillDate'] ?? null;
                    $validTill = $data['EwbValidTill'] ?? $data['ValidTill'] ?? $data['validUpto'] ?? null;
                    $vehFromNic = $data['VehNo'] ?? $data['Vehno'] ?? $data['vehicleNo'] ?? $data['VehicleNo'] ?? null;
                    $vehFromNic = $vehFromNic ? strtoupper(str_replace(' ', '', (string) $vehFromNic)) : null;
                    $vehToSave = $vehFromNic ?: $vehicleNoNorm;

                    if (!empty($ewbNo)) {
                        $ewbDateParsed = $ewbDt ? \Carbon\Carbon::parse($ewbDt) : now();
                        $validTillParsed = $validTill ? \Carbon\Carbon::parse($validTill) : now()->addDay();

                        $order->update([
                            'ewb_status' => 'generated',
                            'ewb_number' => (string) $ewbNo,
                            'ewb_date' => $ewbDateParsed,
                            'ewb_valid_upto' => $validTillParsed,
                            'ewb_vehicle_no' => $vehToSave,
                            'ewb_distance_km' => $distanceKm,
                            'ewb_response' => (string) ($details['raw_response'] ?? null),
                        ]);

                        return response()->json([
                            'status' => true,
                            'already_exists' => true,
                            'message' => 'E-Way Bill already generated on NIC. Saved existing details.',
                            'order_id' => $order->id,
                            'ewb_number' => (string) $ewbNo,
                            'ewb_date' => $ewbDateParsed->format('Y-m-d H:i:s'),
                            'ewb_valid_upto' => $validTillParsed->format('Y-m-d H:i:s'),
                            'vehicle_no' => $vehToSave,
                            'distance' => $distanceKm,
                        ]);
                    }

                    Log::warning('EWB: 4002 received but EWB number missing in details response', [
                        'order_id' => $order->id,
                        'data_keys' => is_array($data) ? array_keys($data) : null,
                    ]);
                } catch (\Throwable $e2) {
                    Log::error('EWB: 4002 fallback failed', [
                        'order_id' => $order->id,
                        'msg' => $e2->getMessage(),
                    ]);
                }
            }

            Log::error('EWB: generation failed', ['order_id' => $order->id, 'msg' => $msg]);
            return response()->json(['status' => false, 'message' => $msg], 422);
        }

        $ewbNo = (string) $result['ewb_no'];
        $ewbDate = $result['ewb_date'] ? \Carbon\Carbon::parse($result['ewb_date']) : now();
        $validTill = $result['valid_upto'] ? \Carbon\Carbon::parse($result['valid_upto']) : now()->addDay();

        $order->update([
            'ewb_status' => 'generated',
            'ewb_number' => $ewbNo,
            'ewb_date' => $ewbDate,
            'ewb_valid_upto' => $validTill,
            'ewb_vehicle_no' => $vehicleNoNorm,
            'ewb_distance_km' => $distanceKm,
            'ewb_response' => (string) ($result['raw_response'] ?? null),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'E-Way Bill generated and saved.',
            'order_id' => $order->id,
            'ewb_number' => $ewbNo,
            'ewb_date' => $ewbDate->format('Y-m-d H:i:s'),
            'ewb_valid_upto' => $validTill->format('Y-m-d H:i:s'),
            'vehicle_no' => $vehicleNoNorm,
            'distance' => $distanceKm,
        ]);
    }

    /**
     * Get E-Way Bill Details Dummy API
     */
    public function getEwayBill(Request $request)
    {
        $ewbNo = $request->query('ewbNo') ?? '123456789012';

        return response()->json([
            'status' => '1',
            'status_cd' => '1',
            'message' => 'E-Way Bill Details Fetched',
            'data' => [
                'ewbNo' => $ewbNo,
                'ewbDate' => date('d/m/Y H:i:s'),
                'genGstin' => '29AAAAA0000A1Z5',
                'docNo' => 'INV-001',
                'docDate' => '17/04/2026',
                'fromGstin' => '29AAAAA0000A1Z5',
                'fromTrdName' => 'Dummy Consignor Pvt Ltd',
                'toGstin' => '27BBBBB0000B1Z5',
                'toTrdName' => 'Dummy Consignee Solutions',
                'totalValue' => 50000.00,
                'status' => 'ACT',
                'itemList' => [
                    [
                        'itemNo' => 1,
                        'productName' => 'Sample Product X',
                        'productDesc' => 'Electronic Goods',
                        'hsnCode' => '8517',
                        'quantity' => 10,
                        'qtyUnit' => 'PCS',
                        'taxableAmount' => 45000.00,
                        'cgstRate' => 9,
                        'sgstRate' => 9,
                        'igstRate' => 0
                    ]
                ]
            ]
        ]);
    }

    /**
     * Cancel E-Way Bill Dummy API
     */
    public function cancelEwayBill(Request $request)
    {
        $request->validate([
            'ewbNo' => 'required',
            'cancelReason' => 'required',
            'cancelRemarks' => 'nullable'
        ]);

        return response()->json([
            'status' => '1',
            'status_cd' => '1',
            'message' => 'E-Way Bill Cancelled Successfully',
            'data' => [
                'waybillNo' => $request->ewbNo,
                'cancelDate' => date('d/m/Y H:i:s')
            ]
        ]);
    }
}
