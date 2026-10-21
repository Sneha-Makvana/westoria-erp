<?php

namespace App\Services;

use App\Models\Setting;

class EInvoiceService
{
    protected $setting;

    public function __construct(Setting $setting)
    {
        $this->setting = $setting;
    }

    public function isEnabled(): bool
    {
        // Simple stub: Returns true if settings are partially configured.
        return true;
    }

    public function getEwayBillDetailsByIrn(string $irn): array
    {
        return [
            'data' => [
                'EwbNo' => '123456789012',
                'EwbDt' => now()->format('Y-m-d H:i:s'),
                'ValidTill' => now()->addDay()->format('Y-m-d H:i:s'),
                'VehNo' => 'MH01AB1234',
            ],
            'raw_response' => '{"status":"success"}'
        ];
    }

    public function generateEwayBillByIrn(string $irn, string $vehicleNo, int $distanceKm): array
    {
        return [
            'ewb_no' => '123456789012',
            'ewb_date' => now()->format('Y-m-d H:i:s'),
            'valid_upto' => now()->addDay()->format('Y-m-d H:i:s'),
            'raw_response' => '{"status":"success"}'
        ];
    }

    public function getEinvoiceDetailsByIrn(string $irn): array
    {
        return [
            'data' => [
                'Irn' => $irn,
                'AckNo' => '123456789012345',
                'AckDt' => now()->format('Y-m-d H:i:s'),
            ],
            'raw_response' => '{"status":"success"}'
        ];
    }

    public function generateEinvoice(int $orderId): array
    {
        return [
            'Irn' => 'dummy_irn_64_characters_string_123456789012345678901234567890',
            'SignedQRCode' => 'dummy_qr_code_string',
            'AckNo' => '123456789012345',
            'AckDt' => now()->format('Y-m-d H:i:s'),
            'raw_response' => '{"status":"success"}'
        ];
    }
}
