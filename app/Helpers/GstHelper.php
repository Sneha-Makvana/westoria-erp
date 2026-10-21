<?php

if (!function_exists('normalizeGstStateCode')) {
    function normalizeGstStateCode(?string $stateCode, ?string $gstNumber = null): ?string
    {
        if (!empty($gstNumber) && strlen($gstNumber) >= 2) {
            return substr($gstNumber, 0, 2);
        }
        if (!empty($stateCode)) {
            $code = substr(trim($stateCode), 0, 2);
            if (is_numeric($code)) {
                return str_pad($code, 2, '0', STR_PAD_LEFT);
            }
        }
        return null;
    }
}

if (!function_exists('resolveBranchGstStateCode')) {
    function resolveBranchGstStateCode($branchId = null): ?string
    {
        if (!$branchId) return '24'; // Default fallback
        $setting = \App\Models\Setting::where('branch_id', $branchId)->first();
        return normalizeGstStateCode($setting->state_code ?? null, $setting->gst_num ?? null) ?: '24';
    }
}

if (!function_exists('resolveCustomerGstStateCode')) {
    function resolveCustomerGstStateCode($user = null): ?string
    {
        if (!$user) return null;
        return normalizeGstStateCode($user->state_code ?? null, $user->gst_number ?? null);
    }
}

if (!function_exists('remapGstComponentAmountsForPlaceOfSupply')) {
    function remapGstComponentAmountsForPlaceOfSupply(array $components, ?string $sellerState, ?string $buyerState): array
    {
        $igst = (float) ($components['igst'] ?? 0);
        $cgst = (float) ($components['cgst'] ?? 0);
        $sgst = (float) ($components['sgst'] ?? 0);
        $cess = (float) ($components['cess'] ?? 0);
        
        $totalGst = $igst + $cgst + $sgst;
        
        if ($sellerState !== null && $buyerState !== null && $sellerState !== $buyerState) {
            // Inter-state
            $igst = $totalGst;
            $cgst = 0;
            $sgst = 0;
        } else {
            // Intra-state (or unknown, default to intra-state)
            $igst = 0;
            $cgst = $totalGst / 2;
            $sgst = $totalGst / 2;
        }
        
        return compact('igst', 'cgst', 'sgst', 'cess');
    }
}

if (!function_exists('mapGstRatesForPlaceOfSupply')) {
    function mapGstRatesForPlaceOfSupply($gstDetails, ?string $sellerState, ?string $buyerState): array
    {
        if (empty($gstDetails)) return [];
        if (is_string($gstDetails)) {
            $gstDetails = json_decode($gstDetails, true) ?? [];
        }
        
        $mapped = [];
        foreach ($gstDetails as $tax) {
            if (!is_array($tax)) continue;
            
            $name = strtoupper(trim((string) ($tax['tax_name'] ?? $tax['name'] ?? $tax['tax_type'] ?? '')));
            $rate = (float) ($tax['tax_rate'] ?? $tax['rate'] ?? 0);
            
            if ($rate <= 0) continue;
            
            $tax['tax_name'] = $name;
            $tax['tax_rate'] = $rate;
            $mapped[] = $tax;
        }
        return $mapped;
    }
}

if (!function_exists('resolveProductGstTotalRate')) {
    function resolveProductGstTotalRate(array $rates): float
    {
        $total = 0.0;
        foreach ($rates as $tax) {
            $name = strtoupper($tax['tax_name'] ?? '');
            $rate = (float) ($tax['tax_rate'] ?? 0);
            if ($name === 'IGST') {
                return $rate; // If IGST is present, it's the total rate
            }
            if ($name === 'CGST' || $name === 'SGST' || $name === 'UTGST' || str_contains($name, 'GST')) {
                $total += $rate;
            }
        }
        return $total;
    }
}

if (!function_exists('normalizeGstTaxComponents')) {
    function normalizeGstTaxComponents($taxes, ?string $sellerState = null, ?string $buyerState = null): array
    {
        $igst = $cgst = $sgst = $cess = 0.0;

        if (empty($taxes)) {
            return compact('igst', 'cgst', 'sgst', 'cess');
        }

        if (is_string($taxes)) {
            $taxes = json_decode($taxes, true);
        }

        if (!is_array($taxes)) {
            return compact('igst', 'cgst', 'sgst', 'cess');
        }

        foreach ($taxes as $tax) {
            if (!is_array($tax)) {
                continue;
            }

            $name = strtoupper(trim($tax['tax_name'] ?? $tax['name'] ?? ''));
            $amount = (float) ($tax['tax_amount'] ?? $tax['amount'] ?? 0);

            if ($amount <= 0) {
                continue;
            }

            if ($name === 'IGST') {
                $igst += $amount;
            } elseif ($name === 'CGST') {
                $cgst += $amount;
            } elseif ($name === 'SGST') {
                $sgst += $amount;
            } elseif ($name === 'CESS') {
                $cess += $amount;
            } elseif ($name === '' || preg_match('/(^|[^A-Z])GST([^A-Z]|$)/', $name)) {
                $cgst += $amount / 2.0;
                $sgst += $amount / 2.0;
            }
        }

        $components = compact('igst', 'cgst', 'sgst', 'cess');
        
        if ($sellerState !== null && $buyerState !== null) {
            return remapGstComponentAmountsForPlaceOfSupply($components, $sellerState, $buyerState);
        }
        
        return $components;
    }
}

if (!function_exists('parseGstJson')) {
    function parseGstJson($json)
    {
        $components = normalizeGstTaxComponents($json);

        return [
            'igst' => $components['igst'],
            'cgst' => $components['cgst'],
            'sgst' => $components['sgst'],
        ];
    }
}
