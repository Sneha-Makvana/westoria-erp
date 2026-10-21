<?php

namespace App\Services;

use App\Imports\GstWorkbookImport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

class GstWorkbookValidationService
{
    private const TOLERANCE = 0.50;

    private const CORRECT = 'Correct';

    private const COLUMN_ALIASES = [
        'hsn' => ['hsn', 'hsn code', 'hsn/sac', 'hsn sac'],
        'description' => ['description', 'product description', 'goods description'],
        'uqc' => ['uqc', 'unit', 'unit quantity code'],
        'total_quantity' => ['total quantity', 'quantity', 'qty'],
        'total_value' => ['total value', 'invoice value', 'total invoice value'],
        'rate' => ['rate', 'gst rate', 'tax rate', 'rate of tax'],
        'taxable_value' => ['taxable value', 'taxable amount'],
        'igst' => ['integrated tax amount', 'integrated tax', 'igst', 'igst amount'],
        'cgst' => ['central tax amount', 'central tax', 'cgst', 'cgst amount'],
        'sgst' => ['state/ut tax amount', 'state ut tax amount', 'state tax amount', 'state/ut tax', 'sgst', 'utgst', 'sgst amount'],
        'cess' => ['cess amount', 'cess'],
    ];

    public function validateUploadedWorkbook(UploadedFile $file): array
    {
        $path = $file->store('gst-validation-uploads');

        try {
            return $this->validateWorkbook(Storage::path($path), $file->getClientOriginalName());
        } finally {
            Storage::delete($path);
        }
    }

    public function validateWorkbook(string $absolutePath, ?string $originalName = null): array
    {
        try {
            $sheetRows = Excel::toArray(new GstWorkbookImport(), $absolutePath);
            $sheetNames = $this->sheetNames($absolutePath);
        } catch (Throwable $e) {
            Log::error('GST workbook validation failed to read file', [
                'path' => $absolutePath,
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException('Unable to read the uploaded Excel workbook. Please upload a valid .xlsx file.', 0, $e);
        }

        $result = [
            'id' => (string) Str::uuid(),
            'file_name' => $originalName,
            'generated_at' => now()->toDateTimeString(),
            'summary' => $this->blankSummary(count($sheetRows)),
            'sheets' => [],
        ];

        foreach ($sheetRows as $index => $rows) {
            $sheetName = $sheetNames[$index] ?? 'Sheet ' . ($index + 1);
            $sheetResult = $this->validateSheet($sheetName, $rows);
            $result['sheets'][] = $sheetResult;
            $this->mergeSummary($result['summary'], $sheetResult);
        }

        $result['summary']['total_gst'] = round(
            $result['summary']['total_cgst'] + $result['summary']['total_sgst'] + $result['summary']['total_igst'] + $result['summary']['total_cess'],
            2
        );

        $this->storeResult($result);

        return $result;
    }

    public function storedResult(string $id): array
    {
        $path = "gst-validations/{$id}.json";

        if (!Storage::exists($path)) {
            throw new \RuntimeException('GST validation result not found or expired.');
        }

        $data = json_decode(Storage::get($path), true);

        if (!is_array($data)) {
            throw new \RuntimeException('GST validation result is not readable.');
        }

        return $data;
    }

    private function validateSheet(string $sheetName, array $rows): array
    {
        $headerInfo = $this->detectHeaderRow($rows);

        $result = [
            'sheet_name' => $sheetName,
            'header_row' => $headerInfo['row_number'],
            'headers' => $headerInfo['headers'],
            'mapped_columns' => $headerInfo['mapped_columns'],
            'rows' => [],
            'summary' => $this->blankSheetSummary(),
        ];

        if ($headerInfo['row_index'] === null) {
            return $result;
        }

        for ($i = $headerInfo['row_index'] + 1; $i < count($rows); $i++) {
            $row = $rows[$i] ?? [];
            if ($this->isEmptyRow($row)) {
                continue;
            }

            $validation = $this->validateRow($row, $headerInfo['mapped_columns']);
            if ($validation === null) {
                continue;
            }

            $validation['row_number'] = $i + 1;
            $validation['original_row'] = $row;
            $result['rows'][] = $validation;
            $this->mergeSheetRowSummary($result['summary'], $validation);
        }

        return $result;
    }

    private function validateRow(array $row, array $columns): ?array
    {
        $values = [
            'hsn' => $this->cell($row, $columns, 'hsn'),
            'description' => $this->cell($row, $columns, 'description'),
            'uqc' => $this->cell($row, $columns, 'uqc'),
            'total_quantity' => $this->number($this->cell($row, $columns, 'total_quantity')),
            'total_value' => $this->number($this->cell($row, $columns, 'total_value')),
            'rate' => $this->number($this->cell($row, $columns, 'rate')),
            'taxable_value' => $this->number($this->cell($row, $columns, 'taxable_value')),
            'igst' => $this->number($this->cell($row, $columns, 'igst')),
            'cgst' => $this->number($this->cell($row, $columns, 'cgst')),
            'sgst' => $this->number($this->cell($row, $columns, 'sgst')),
            'cess' => $this->number($this->cell($row, $columns, 'cess')),
        ];

        if ($values['taxable_value'] == 0.0 && $values['total_value'] == 0.0 && $values['rate'] == 0.0) {
            return null;
        }

        $correct = $this->correctValues($values);
        $errors = $this->rowErrors($values, $correct);

        $status = match (count($errors)) {
            0 => self::CORRECT,
            1 => $errors[0],
            default => 'Multiple Errors',
        };

        return array_merge($values, [
            'status' => $status,
            'error_message' => implode('; ', $errors),
            'correct_taxable_value' => $correct['taxable_value'],
            'correct_cgst' => $correct['cgst'],
            'correct_sgst' => $correct['sgst'],
            'correct_igst' => $correct['igst'],
            'correct_total_gst' => $correct['total_gst'],
            'correct_total_value' => $correct['total_value'],
        ]);
    }

    private function correctValues(array $values): array
    {
        $rate = max(0.0, $values['rate']);
        $taxableValue = $this->bestTaxableValue($values);

        $expectedIgst = 0.0;
        $expectedCgst = 0.0;
        $expectedSgst = 0.0;

        if ($values['igst'] > 0) {
            $expectedIgst = ($taxableValue * $rate) / 100;
        } else {
            $expectedCgst = ($taxableValue * ($rate / 2)) / 100;
            $expectedSgst = ($taxableValue * ($rate / 2)) / 100;
        }

        $totalGst = $expectedIgst + $expectedCgst + $expectedSgst + $values['cess'];

        return [
            'taxable_value' => round($taxableValue, 2),
            'igst' => round($expectedIgst, 2),
            'cgst' => round($expectedCgst, 2),
            'sgst' => round($expectedSgst, 2),
            'cess' => round($values['cess'], 2),
            'total_gst' => round($totalGst, 2),
            'total_value' => round($taxableValue + $totalGst, 2),
        ];
    }

    private function bestTaxableValue(array $values): float
    {
        $rate = max(0.0, $values['rate']);
        $actualTotalGst = $values['igst'] + $values['cgst'] + $values['sgst'] + $values['cess'];

        if ($values['total_value'] > 0 && $rate > 0) {
            return $values['total_value'] / (1 + ($rate / 100));
        }

        return max(0.0, $values['taxable_value']);
    }

    private function rowErrors(array $values, array $correct): array
    {
        $errors = [];
        $actualTotalGst = $values['cgst'] + $values['sgst'] + $values['igst'] + $values['cess'];

        if (
            $values['total_value'] > 0
            && $values['rate'] > 0
            && !$this->withinTolerance($values['taxable_value'], $correct['taxable_value'])
        ) {
            $errors[] = 'Taxable Value Mismatch';
        }

        if ($values['igst'] > 0) {
            if (!$this->withinTolerance($values['igst'], $correct['igst']) || !$this->withinTolerance($values['cgst'], 0) || !$this->withinTolerance($values['sgst'], 0)) {
                $errors[] = 'IGST Mismatch';
            }
        } else {
            if (!$this->withinTolerance($values['cgst'], $correct['cgst'])) {
                $errors[] = 'CGST Mismatch';
            }

            if (!$this->withinTolerance($values['sgst'], $correct['sgst'])) {
                $errors[] = 'SGST Mismatch';
            }

            if (!$this->withinTolerance($values['igst'], 0)) {
                $errors[] = 'IGST Mismatch';
            }
        }

        if (!$this->withinTolerance($actualTotalGst, $correct['total_gst'])) {
            $errors[] = 'Total GST Mismatch';
        }

        if (!$this->withinTolerance($values['total_value'], $correct['total_value'])) {
            $errors[] = 'Total Value Mismatch';
        }

        return array_values(array_unique($errors));
    }

    private function detectHeaderRow(array $rows): array
    {
        $best = [
            'row_index' => null,
            'row_number' => null,
            'headers' => [],
            'mapped_columns' => [],
            'score' => 0,
        ];

        foreach ($rows as $index => $row) {
            if ($this->isEmptyRow($row)) {
                continue;
            }

            $mapped = $this->mapColumns($row);
            $score = count($mapped);

            if ($score > $best['score']) {
                $best = [
                    'row_index' => $index,
                    'row_number' => $index + 1,
                    'headers' => array_values($row),
                    'mapped_columns' => $mapped,
                    'score' => $score,
                ];
            }

            if ($score >= 7) {
                break;
            }
        }

        if ($best['score'] < 3) {
            return [
                'row_index' => null,
                'row_number' => null,
                'headers' => [],
                'mapped_columns' => [],
            ];
        }

        return $best;
    }

    private function mapColumns(array $headerRow): array
    {
        $mapped = [];

        foreach ($headerRow as $index => $header) {
            $normalized = $this->normalizeHeader((string) $header);
            if ($normalized === '') {
                continue;
            }

            foreach (self::COLUMN_ALIASES as $key => $aliases) {
                if (isset($mapped[$key])) {
                    continue;
                }

                foreach ($aliases as $alias) {
                    if ($normalized === $this->normalizeHeader($alias)) {
                        $mapped[$key] = $index;
                        break;
                    }
                }
            }
        }

        return $mapped;
    }

    private function normalizeHeader(string $value): string
    {
        $value = Str::lower(trim($value));
        $value = str_replace(['₹', "\n", "\r", "\t"], ' ', $value);
        $value = preg_replace('/\\([^)]*\\)/', ' ', $value) ?? $value;
        $value = preg_replace('/[^a-z0-9]+/', ' ', $value) ?? $value;

        return trim(preg_replace('/\\s+/', ' ', $value) ?? $value);
    }

    private function cell(array $row, array $columns, string $key): mixed
    {
        return isset($columns[$key]) ? ($row[$columns[$key]] ?? null) : null;
    }

    private function number(mixed $value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        $value = trim((string) $value);
        if ($value === '') {
            return 0.0;
        }

        $value = str_replace([',', '₹', 'Rs.', 'rs.', '%'], '', $value);

        return is_numeric($value) ? (float) $value : 0.0;
    }

    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $cell) {
            if ($cell !== null && trim((string) $cell) !== '') {
                return false;
            }
        }

        return true;
    }

    private function withinTolerance(float $actual, float $expected): bool
    {
        return abs($actual - $expected) <= self::TOLERANCE;
    }

    private function sheetNames(string $absolutePath): array
    {
        $spreadsheet = IOFactory::load($absolutePath);

        return $spreadsheet->getSheetNames();
    }

    private function blankSummary(int $totalSheets): array
    {
        return array_merge($this->blankSheetSummary(), [
            'total_sheets' => $totalSheets,
            'total_hsn' => 0,
        ]);
    }

    private function blankSheetSummary(): array
    {
        return [
            'total_rows' => 0,
            'total_correct_rows' => 0,
            'total_incorrect_rows' => 0,
            'total_taxable_value' => 0.0,
            'total_cgst' => 0.0,
            'total_sgst' => 0.0,
            'total_igst' => 0.0,
            'total_cess' => 0.0,
            'total_gst' => 0.0,
            'total_invoice_value' => 0.0,
            'hsn_values' => [],
        ];
    }

    private function mergeSheetRowSummary(array &$summary, array $row): void
    {
        $summary['total_rows']++;
        $summary[$row['status'] === self::CORRECT ? 'total_correct_rows' : 'total_incorrect_rows']++;
        $summary['total_taxable_value'] += $row['taxable_value'];
        $summary['total_cgst'] += $row['cgst'];
        $summary['total_sgst'] += $row['sgst'];
        $summary['total_igst'] += $row['igst'];
        $summary['total_cess'] += $row['cess'];
        $summary['total_gst'] += $row['cgst'] + $row['sgst'] + $row['igst'] + $row['cess'];
        $summary['total_invoice_value'] += $row['total_value'];

        if (!empty($row['hsn'])) {
            $summary['hsn_values'][(string) $row['hsn']] = true;
        }
    }

    private function mergeSummary(array &$summary, array $sheet): void
    {
        $sheetSummary = $sheet['summary'] ?? [];

        foreach (['total_rows', 'total_correct_rows', 'total_incorrect_rows'] as $key) {
            $summary[$key] += (int) ($sheetSummary[$key] ?? 0);
        }

        foreach (['total_taxable_value', 'total_cgst', 'total_sgst', 'total_igst', 'total_cess', 'total_gst', 'total_invoice_value'] as $key) {
            $summary[$key] = round($summary[$key] + (float) ($sheetSummary[$key] ?? 0), 2);
        }

        foreach (($sheetSummary['hsn_values'] ?? []) as $hsn => $_) {
            $summary['hsn_values'][$hsn] = true;
        }

        $summary['total_hsn'] = count($summary['hsn_values']);
    }

    private function storeResult(array $result): void
    {
        Storage::put("gst-validations/{$result['id']}.json", json_encode($result, JSON_PRETTY_PRINT));
    }
}
