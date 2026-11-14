<?php

namespace App\Services\Purchase;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PurchaseBillVisionService
{
    public function isConfigured(): bool
    {
        return $this->geminiKey() !== '' || $this->openaiKey() !== '';
    }

    /**
     * @return array<string, mixed>
     */
    public function extract(UploadedFile $file): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException(
                'Add GEMINI_API_KEY or OPENAI_API_KEY in .env to read bill images.'
            );
        }

        [$mime, $base64] = $this->prepareImage($file);

        $prompt = $this->prompt();

        $raw = $this->geminiKey() !== ''
            ? $this->callGemini($mime, $base64, $prompt)
            : $this->callOpenAi($mime, $base64, $prompt);

        $data = $this->decodeJson($raw);
        $data['items'] = array_values(array_filter(
            array_map([InvoiceFields::class, 'row'], array_values(array_filter($data['items'] ?? [], 'is_array'))),
            fn ($item) => ! InvoiceFields::isSummaryRow($item)
        ));
        $data = InvoiceFields::document($data);
        $data = $this->normalizePayload($data);
        if ($this->needsItemReread($data)) {
            try {
                $rereadRaw = $this->geminiKey() !== ''
                    ? $this->callGemini($mime, $base64, $this->missingRowsPrompt($data))
                    : $this->callOpenAi($mime, $base64, $this->missingRowsPrompt($data));
                $extra = $this->decodeJson($rereadRaw);
                if (is_array($extra['items'] ?? null) && $extra['items'] !== []) {
                    $extra = $this->normalizePayload($extra);
                    $data['items'] = InvoiceFields::addMissing($data['items'], $extra['items']);
                    foreach ($extra['warnings'] ?? [] as $warning) {
                        $data['warnings'][] = $warning;
                    }
                }
            } catch (\Throwable) {
                $data['warnings'][] = 'Some product rows could not be read. Check the page for missing serial numbers.';
            }
            $data = $this->noteMissingSerials($data);
        }

        return $data;
    }

    /**
     * @return array{0: string, 1: string}
     */

    /** @param array<string, mixed> $data */
    private function needsItemReread(array $data): bool
    {
        $items = $data['items'] ?? [];
        $expected = (int) ($data['expected_item_count'] ?? 0);
        if ($expected > count($items)) {
            return true;
        }

        $serials = [];
        foreach ($items as $item) {
            $serial = (int) ($item['serial_no'] ?? 0);
            if ($serial > 0) {
                $serials[$serial] = true;
            }
        }
        if ($serials === []) {
            return false;
        }

        $first = (int) ($data['first_serial'] ?? 0);
        $last = (int) ($data['last_serial'] ?? 0);
        if ($first <= 0) {
            $first = min(array_keys($serials));
        }
        if ($last <= 0) {
            $last = max(array_keys($serials));
        }
        if ($last < $first || ($last - $first) > 80) {
            return false;
        }

        for ($serial = $first; $serial <= $last; $serial++) {
            if (! isset($serials[$serial])) {
                return true;
            }
        }

        return false;
    }

    /** @param array<string, mixed> $data */
    private function missingRowsPrompt(array $data): string
    {
        $serials = [];
        foreach ($data['items'] ?? [] as $item) {
            $serial = (int) ($item['serial_no'] ?? 0);
            if ($serial > 0) {
                $serials[] = $serial;
            }
        }
        sort($serials);
        $have = $serials === [] ? 'none' : implode(', ', $serials);
        $first = (int) ($data['first_serial'] ?? 0);
        $last = (int) ($data['last_serial'] ?? 0);
        $expected = (int) ($data['expected_item_count'] ?? 0);

        return <<<PROMPT
You are re-reading the same invoice page. Return ONLY valid JSON.
Already captured serial numbers: {$have}.
Visible range first_serial={$first}, last_serial={$last}, expected_item_count={$expected}.
Return every missing product SN row and no rows already captured.
Skip TOTAL B/F, TOTAL C/F, TOTAL BIF, TOTAL CIF, Continued, Grand Total and tax summary lines.
A carried-forward amount at the top of the Amount column is not a product amount. amount must be that row's quantity × rate when those cells are printed.
Schema: {"items":[{"serial_no":0,"name":"","hsn":"","barcode":"","mrp":null,"purchase_quantity":null,"free_quantity":null,"discount_percent":null,"gst_percent":null,"rate":null,"amount":null}]}
If none are missing, return {"items":[]}.
PROMPT;
    }

    /** @param array<string, mixed> $data */
    private function noteMissingSerials(array $data): array
    {
        $serials = [];
        foreach ($data['items'] ?? [] as $item) {
            $serial = (int) ($item['serial_no'] ?? 0);
            if ($serial > 0) {
                $serials[$serial] = true;
            }
        }
        if ($serials === []) {
            return $data;
        }

        $first = min(array_keys($serials));
        $last = max(array_keys($serials));
        $printedLast = (int) ($data['last_serial'] ?? 0);
        if ($printedLast > $last && ($printedLast - $first) <= 80) {
            $last = $printedLast;
        }
        $missing = [];
        for ($serial = $first; $serial <= $last && count($missing) < 20; $serial++) {
            if (! isset($serials[$serial])) {
                $missing[] = $serial;
            }
        }
        if ($missing !== []) {
            $data['warnings'][] = 'Some product rows still look missing for serial '.implode(', ', $missing).'. Check those lines on the bill.';
        }

        return $data;
    }

    private function prepareImage(UploadedFile $file): array
    {
        $path = $file->getRealPath();
        if ($path === false) {
            throw new RuntimeException('Could not read the uploaded bill image.');
        }

        $mime = (string) ($file->getMimeType() ?: 'image/jpeg');
        $binary = @file_get_contents($path);

        if ($binary === false || $binary === '') {
            throw new RuntimeException('Could not read the uploaded bill image.');
        }

        // Keep fine table text at the uploaded resolution; avoid lossy recompression.
        return [$mime, base64_encode($binary)];
    }

    private function prompt(): string
    {
        return <<<'PROMPT'
You are reading an Indian retail / wholesale purchase bill / tax invoice / bill of supply photo (general merchandise, garments, electronics, FMCG — NOT a pharmacy medicine bill).
Return ONLY valid JSON (no markdown). Read English and Gujarati. Ignore handwritten ticks.

Schema:
{
  "ocr_text": "complete document transcription preserving lines and table alignment",
  "vendor_gstin": "string", "payment_details": {}, "additional_fields": {},
  "vendor_name": "string",
  "vendor_phone": "digits only or empty",
  "vendor_address": "string",
  "vendor_email": "string",
  "bill_no": "string",
  "bill_date": "YYYY-MM-DD if possible, else as printed",
  "customer_name": "string",
  "gross_amount": null, "taxable_amount": null, "invoice_discount_amount": null, "invoice_discount_percent": null,
  "page_number": null, "page_role": "single, continuation, or final", "grand_total_is_final": false,
  "expected_item_count": 0, "first_serial": null, "last_serial": null,
  "cgst_amount": null, "sgst_amount": null, "igst_amount": null, "rounding_adjustment": null,
  "grand_total": 0,
  "amount_in_words": "string",
  "previous_balance": null,
  "current_balance": null,
  "payment_status": "paid or unpaid",
  "payment_mode": "cash, upi, bank_transfer, card, cheque, or empty",
  "items": [
    {
      "serial_no": null,
      "name": "product name in English, include size/color/variant if printed",
      "hsn": "HSN/SAC code or empty",
      "barcode": "barcode/EAN if printed, else empty",
      "mrp": null,
      "purchase_quantity": null, "gst_percent": null, "cgst": null, "sgst": null, "igst": null, "taxable_amount": null,
      "source_row": "page:table:row", "source_text": "verbatim row including continuation lines", "cells": {"original header": "raw cell value"},
      "free_quantity": null,
      "discount_percent": null,
      "discount_amount": null,
      "rate": null,
      "amount": null
    }
  ]
}

Rules:
- First transcribe the COMPLETE document into ocr_text, then detect tables and semantic column roles.
- items are product rows only (not totals, bank details, or amount in words).
- Do NOT extract pharmacy fields: no batch_no, expiry, mfg date, manufacturer codes, or medicine pack columns.
- purchase_quantity is the stock quantity column (Qty / Quantity / Pcs / Units).
- Read MRP, free/scheme qty and line discount when printed. CD%/DISC%/Disc map to discount_percent.
- rate and amount are numbers without commas. Prefer Amount = Rate x qty when no line discount/tax is shown.
- Prefer the printed grand total of this bill only.
- Read every SN / Sr No. product row. Fill expected_item_count, first_serial and last_serial.
- Do not create items for TOTAL, TOTAL B/F, TOTAL C/F, Grand Total, Round Off, Item Discount, Bill Discount, CGST/SGST Payable.
- page_role is continuation/final/single as usual. On continuation pages set grand_total to 0 and grand_total_is_final false.
- Copy CGST/SGST/IGST, BILL DISCOUNT and ROUND OFF only from the summary page. ITEM DISCOUNT must not become invoice_discount_amount.
PROMPT;
    }


    private function callGemini(string $mime, string $base64, string $prompt): string
    {
        $preferred = trim((string) config('services.gemini.model', 'gemini-2.5-flash-lite'));
        // Keep bill parsing on lightweight Gemini models only.
        $models = array_values(array_unique(array_filter([
            str_contains($preferred, 'lite') ? $preferred : 'gemini-2.5-flash-lite',
            'gemini-2.5-flash-lite',
            'gemini-3.1-flash-lite',
            'gemini-3.5-flash-lite',
        ])));

        $payload = [
            'contents' => [[
                'parts' => [
                    ['text' => $prompt],
                    ['inline_data' => ['mime_type' => $mime, 'data' => $base64]],
                ],
            ]],
            'generationConfig' => [
                'temperature' => 0.1,
                'maxOutputTokens' => 16384,
                'responseMimeType' => 'application/json',
            ],
        ];

        $lastError = 'Gemini request failed.';

        foreach ($models as $model) {
            for ($attempt = 1; $attempt <= 2; $attempt++) {
                $response = $this->postGemini($model, $payload);

                if ($response->failed() && str_contains(strtolower($response->body()), 'thinking')) {
                    $payload['generationConfig']['thinkingConfig'] = ['thinkingBudget' => 0];
                    $response = $this->postGemini($model, $payload);
                }

                if ($response->successful()) {
                    $text = $this->geminiText($response->json());
                    if ($text !== '') {
                        return $text;
                    }
                    $lastError = 'Gemini did not return bill data. Try a clearer photo.';
                    break;
                }

                $lastError = $this->httpError('Gemini', $response->json(), $response->body());

                if ($this->isBusyError($lastError) && $attempt < 2) {
                    usleep(1200000);

                    continue;
                }

                if ($this->shouldTryNextGeminiModel($lastError)) {
                    break;
                }

                throw new RuntimeException($lastError);
            }
        }

        if ($this->isBusyError($lastError)) {
            throw new RuntimeException(
                'Google free servers are busy right now. Wait 20 seconds and click Read bill again.'
            );
        }

        throw new RuntimeException($lastError);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function postGemini(string $model, array $payload)
    {
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/'.$model.':generateContent';

        return Http::timeout(90)
            ->acceptJson()
            ->withHeaders([
                'x-goog-api-key' => $this->geminiKey(),
            ])
            ->post($url, $payload);
    }

    /**
     * @param  mixed  $json
     */
    private function geminiText($json): string
    {
        $parts = data_get($json, 'candidates.0.content.parts', []);
        if (! is_array($parts)) {
            return '';
        }

        foreach ($parts as $part) {
            if (! is_array($part)) {
                continue;
            }
            if (! empty($part['thought'])) {
                continue;
            }
            $text = trim((string) ($part['text'] ?? ''));
            if ($text !== '') {
                return $text;
            }
        }

        return '';
    }

    private function shouldTryNextGeminiModel(string $message): bool
    {
        $message = strtolower($message);

        return $this->isBusyError($message)
            || str_contains($message, 'no longer available')
            || str_contains($message, 'not found')
            || str_contains($message, 'is not found')
            || str_contains($message, 'not supported');
    }

    private function isBusyError(string $message): bool
    {
        $message = strtolower($message);

        return str_contains($message, 'high demand')
            || str_contains($message, 'try again later')
            || str_contains($message, 'unavailable')
            || str_contains($message, 'overloaded')
            || str_contains($message, 'resource exhausted')
            || str_contains($message, 'rate limit')
            || str_contains($message, 'too many requests')
            || str_contains($message, 'quota');
    }

    private function callOpenAi(string $mime, string $base64, string $prompt): string
    {
        $model = (string) config('services.openai.model', 'gpt-4o-mini');

        $response = Http::timeout(90)
            ->acceptJson()
            ->withToken($this->openaiKey())
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'temperature' => 0.1,
                'max_tokens' => 16384,
                'response_format' => ['type' => 'json_object'],
                'messages' => [[
                    'role' => 'user',
                    'content' => [
                        ['type' => 'text', 'text' => $prompt],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => 'data:'.$mime.';base64,'.$base64,
                            ],
                        ],
                    ],
                ]],
            ]);

        if ($response->failed()) {
            throw new RuntimeException($this->httpError('OpenAI', $response->json(), $response->body()));
        }

        $text = data_get($response->json(), 'choices.0.message.content');
        if (! is_string($text) || trim($text) === '') {
            throw new RuntimeException('OpenAI did not return bill data. Try a clearer photo.');
        }

        return $text;
    }

    /**
     * @param  mixed  $json
     */
    private function httpError(string $provider, $json, string $body): string
    {
        $message = is_array($json)
            ? (string) (data_get($json, 'error.message') ?: data_get($json, 'error') ?: '')
            : '';

        if ($message === '') {
            $message = trim($body) !== '' ? substr($body, 0, 240) : $provider.' request failed.';
        }

        return $provider.' could not read the bill: '.$message;
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeJson(string $raw): array
    {
        $raw = trim($raw);
        if (str_starts_with($raw, '```')) {
            $raw = preg_replace('/^```(?:json)?\s*/i', '', $raw) ?? $raw;
            $raw = preg_replace('/\s*```$/', '', $raw) ?? $raw;
        }

        $decoded = json_decode($raw, true);
        if (! is_array($decoded)) {
            $decoded = $this->salvageTruncatedJson($raw);
        }

        return $decoded;
    }

    /**
     * @return array<string, mixed>
     */
    private function salvageTruncatedJson(string $raw): array
    {
        $start = strpos($raw, '{');
        if ($start === false) {
            throw new RuntimeException('Could not parse bill data from the image. Please try another photo.');
        }

        $raw = substr($raw, $start);
        for ($attempt = 0; $attempt < 8; $attempt++) {
            $candidate = rtrim($raw, ", \t\n\r");
            $candidate .= str_repeat(']', max(0, substr_count($candidate, '[') - substr_count($candidate, ']')));
            $candidate .= str_repeat('}', max(0, substr_count($candidate, '{') - substr_count($candidate, '}')));
            $decoded = json_decode($candidate, true);
            if (is_array($decoded)) {
                $decoded['warnings'][] = 'The page was only partly read, so missing product rows were requested again.';

                return $decoded;
            }

            $cut = strrpos($raw, '}');
            if ($cut === false) {
                break;
            }
            $raw = substr($raw, 0, $cut + 1);
        }

        throw new RuntimeException('Could not parse bill data from the image. Please try another photo.');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizePayload(array $data): array
    {
        $items = [];
        $amountCorrections = 0;
        foreach ($data['items'] ?? [] as $row) {
            if (! is_array($row)) {
                continue;
            }

            $row = InvoiceFields::row($row);
            $name = trim((string) ($row['name'] ?? $row['description'] ?? ''));
            if ($name === '' || InvoiceFields::isSummaryRow(['name' => $name])) {
                continue;
            }

            $row = InvoiceFields::row($row);
            $items[] = array_replace($row, [
                'extraction_metadata' => $row,
                'name' => $name,
                'hsn' => trim((string) ($row['hsn'] ?? $row['hsn_code'] ?? '')),
                'barcode' => trim((string) ($row['barcode'] ?? $row['ean'] ?? $row['upc'] ?? '')),
                'mrp' => $this->toFloat($row['mrp'] ?? 0),
                'purchase_quantity' => $this->toFloat($row['purchase_quantity'] ?? $row['purchase_qty'] ?? $row['qty'] ?? 0),
                'bags' => 0.0,
                'weight' => 0.0,
                'free_quantity' => $this->toFloat($row['free_quantity'] ?? $row['free_qty'] ?? $row['scheme_qty'] ?? 0),
                'discount_percent' => $this->toFloat($row['discount_percent'] ?? $row['discount_pct'] ?? 0),
                'discount_amount' => $this->toFloat($row['discount_amount'] ?? 0),
                'gst_percent' => $this->toFloat($row['gst_percent'] ?? $row['gst'] ?? $row['tax_percent'] ?? 0),
                'cgst' => $this->toFloat($row['cgst'] ?? $row['cgst_amount'] ?? 0),
                'sgst' => $this->toFloat($row['sgst'] ?? $row['sgst_amount'] ?? 0),
                'igst' => $this->toFloat($row['igst'] ?? $row['igst_amount'] ?? 0),
                'taxable_amount' => $this->toFloat($row['taxable_amount'] ?? $row['taxable_value'] ?? 0),
                'serial_no' => (int) round($this->toFloat($row['serial_no'] ?? $row['sn'] ?? $row['sr_no'] ?? 0)),
                'rate' => $rate = $this->toFloat($row['rate'] ?? 0),
                'amount' => $amount = $this->toFloat($row['amount'] ?? $row['total'] ?? 0),
            ]);
            $quantity = $items[array_key_last($items)]['purchase_quantity'];
            $free = $items[array_key_last($items)]['free_quantity'];
            $expected = round($quantity * $rate, 2);
            if ($quantity > 0 && $rate > 0 && $amount > ($expected * 1.35) + 1) {
                $items[array_key_last($items)]['amount'] = $expected;
                $amountCorrections++;
            } elseif ($quantity > $free && $free > 0 && $rate > 0 && $amount > 0 && abs(($quantity - $free) * $rate - $amount) <= 0.05 && abs($expected - $amount) > 0.05) {
                $items[array_key_last($items)]['purchase_quantity'] = round($quantity - $free, 2);
            } elseif ($quantity > 0 && $rate > 0 && $amount > 0 && $expected > $amount + 0.05) {
                $implied = $amount / $rate;
                if (abs($implied - round($implied)) <= 0.02 && round($implied) > 0 && $quantity > $implied) {
                    $items[array_key_last($items)]['purchase_quantity'] = (float) round($implied);
                }
            }
        }
        if ($amountCorrections > 0) {
            $data['warnings'][] = $amountCorrections.' row amount(s) were corrected to quantity × rate because a page carried-forward total had been read as the product amount.';
        }

        if ($items === []) {
            throw new RuntimeException('No product lines were found on this bill. Use a clearer photo of the item table.');
        }

        $payload = array_replace($data, [
            'vendor_name' => trim((string) ($data['vendor_name'] ?? '')),
            'vendor_phone' => preg_replace('/\D+/', '', (string) ($data['vendor_phone'] ?? '')) ?: '',
            'vendor_address' => trim((string) ($data['vendor_address'] ?? '')),
            'vendor_email' => trim((string) ($data['vendor_email'] ?? '')),
            'vendor_gstin' => trim((string) ($data['vendor_gstin'] ?? '')),
            'bill_no' => trim((string) ($data['bill_no'] ?? '')),
            'bill_date' => $this->normalizeDate((string) ($data['bill_date'] ?? '')),
            'customer_name' => trim((string) ($data['customer_name'] ?? '')),
            'gross_amount' => $this->toFloat($data['gross_amount'] ?? $data['subtotal'] ?? 0),
            'taxable_amount' => $this->toFloat($data['taxable_amount'] ?? 0),
            'invoice_discount_amount' => $this->toFloat($data['invoice_discount_amount'] ?? $data['bill_discount_amount'] ?? $data['overall_discount'] ?? 0),
            'invoice_discount_percent' => $this->toFloat($data['invoice_discount_percent'] ?? $data['bill_discount_percent'] ?? 0),
            'cgst_amount' => $this->toFloat($data['cgst_amount'] ?? $data['cgst'] ?? 0),
            'sgst_amount' => $this->toFloat($data['sgst_amount'] ?? $data['sgst'] ?? 0),
            'igst_amount' => $this->toFloat($data['igst_amount'] ?? $data['igst'] ?? 0),
            'rounding_adjustment' => $this->toFloat($data['rounding_adjustment'] ?? $data['round_off'] ?? $data['rounding_off'] ?? 0),
            'page_number' => (int) ($data['page_number'] ?? 0),
            'page_role' => strtolower(trim((string) ($data['page_role'] ?? ''))),
            'grand_total_is_final' => filter_var($data['grand_total_is_final'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'expected_item_count' => (int) ($data['expected_item_count'] ?? 0),
            'first_serial' => (int) ($data['first_serial'] ?? 0),
            'last_serial' => (int) ($data['last_serial'] ?? 0),
            'grand_total' => $this->toFloat($data['grand_total'] ?? 0),
            'amount_in_words' => trim((string) ($data['amount_in_words'] ?? '')),
            'payment_status' => strtolower(trim((string) ($data['payment_status'] ?? 'unpaid'))) === 'paid' ? 'paid' : 'unpaid',
            'payment_mode' => strtolower(trim((string) ($data['payment_mode'] ?? ''))),
            'previous_balance' => isset($data['previous_balance']) ? $this->toFloat($data['previous_balance']) : null,
            'current_balance' => isset($data['current_balance']) ? $this->toFloat($data['current_balance']) : null,
            'items' => $items,
        ]);
        $discount = InvoiceFields::dropDuplicateInvoiceDiscount(
            (float) $payload['invoice_discount_amount'],
            (float) $payload['invoice_discount_percent'],
            $items
        );
        if ($discount['cleared']) {
            $payload['invoice_discount_amount'] = 0;
            $payload['invoice_discount_percent'] = 0;
            $payload['warnings'][] = 'The printed item-discount total was not subtracted again as a bill discount.';
        }

        return $payload;
    }

    private function normalizeDate(string $value): string
    {
        return InvoiceFields::date($value) ?? $value;
    }



    private function toFloat(mixed $value): float
    {
        if (is_numeric($value)) {
            return round((float) $value, 2);
        }

        $normalized = preg_replace('/[^0-9.\-]/', '', (string) $value);

        return is_numeric($normalized) ? round((float) $normalized, 2) : 0.0;
    }

    private function geminiKey(): string
    {
        return trim((string) config('services.gemini.key'));
    }

    private function openaiKey(): string
    {
        return trim((string) config('services.openai.key'));
    }
}
