<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('orders') || ! Schema::hasTable('order_items')) {
            return;
        }

        DB::table('orders')->update(['gst_option' => 'with_gst']);

        DB::table('order_items')
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->select([
                'order_items.id',
                'order_items.price',
                'order_items.quantity',
                'order_items.discount_amount',
                'order_items.total_amount',
                'products.gst_option',
                'products.product_gst',
            ])
            ->orderBy('order_items.id')
            ->chunk(200, function ($items) {
                foreach ($items as $item) {
                    $inclusiveAmount = (float) ($item->total_amount ?? 0);

                    if ($inclusiveAmount <= 0) {
                        $inclusiveAmount = max(
                            0,
                            ((float) ($item->price ?? 0) * (float) ($item->quantity ?? 0))
                                - (float) ($item->discount_amount ?? 0)
                        );
                    }

                    $rates = $this->productGstRates($item->gst_option, $item->product_gst);
                    $inclusiveGst = $this->inclusiveGstDetails($inclusiveAmount, $rates);

                    DB::table('order_items')
                        ->where('id', $item->id)
                        ->update([
                            'product_gst_details' => ! empty($inclusiveGst['details'])
                                ? json_encode($inclusiveGst['details'])
                                : null,
                            'product_gst_total' => $inclusiveGst['total'],
                        ]);
                }
            });
    }

    public function down(): void
    {
        // Data-only backfill; do not destroy existing GST values on rollback.
    }

    private function productGstRates(?string $gstOption, $productGst): array
    {
        if ($gstOption !== 'with_gst' || empty($productGst)) {
            return [[
                'tax_name' => 'GST',
                'tax_rate' => 18,
            ]];
        }

        $gstData = json_decode((string) $productGst, true);

        if (! is_array($gstData)) {
            return [[
                'tax_name' => 'GST',
                'tax_rate' => 18,
            ]];
        }

        $rates = [];
        foreach ($gstData as $tax) {
            if (! is_array($tax) || (float) ($tax['tax_rate'] ?? 0) <= 0) {
                continue;
            }

            $rates[] = [
                'tax_name' => $tax['tax_name'] ?? 'GST',
                'tax_rate' => (float) ($tax['tax_rate'] ?? 0),
            ];
        }

        return ! empty($rates) ? $rates : [[
            'tax_name' => 'GST',
            'tax_rate' => 18,
        ]];
    }

    private function inclusiveGstDetails(float $inclusiveAmount, array $rates): array
    {
        $totalRate = array_sum(array_map(fn ($tax) => (float) ($tax['tax_rate'] ?? 0), $rates));

        if ($inclusiveAmount <= 0 || $totalRate <= 0) {
            return ['details' => [], 'total' => 0.0];
        }

        $totalGstAmount = round(($inclusiveAmount * $totalRate) / (100 + $totalRate), 2);
        $details = [];
        $allocated = 0.0;
        $lastIndex = count($rates) - 1;

        foreach (array_values($rates) as $index => $tax) {
            $rate = (float) ($tax['tax_rate'] ?? 0);
            $taxAmount = $index === $lastIndex
                ? round($totalGstAmount - $allocated, 2)
                : round(($totalGstAmount * $rate) / $totalRate, 2);

            $allocated += $taxAmount;
            $details[] = [
                'tax_name' => $tax['tax_name'] ?? 'GST',
                'tax_rate' => $rate,
                'tax_amount' => $taxAmount,
            ];
        }

        return ['details' => $details, 'total' => $totalGstAmount];
    }
};
