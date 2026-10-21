<?php

namespace App\Services;

use App\Http\Controllers\api\ProductController;

/**
 * Thin wrapper so streaming parser can reuse ProductController import mapping rules.
 */
class ProductImportRowMapper
{
    public function buildHeaderCells(array $rowValues, int $maxCol): array
    {
        $cells = [];
        for ($c = 0; $c < $maxCol; $c++) {
            $cells[$c + 1] = $this->normalizeImportHeader((string) ($rowValues[$c] ?? ''));
        }

        return $cells;
    }

    public function buildHeaderCellsFromIndexed(array $rowCells, int $maxCol): array
    {
        $cells = [];
        for ($c = 1; $c <= $maxCol; $c++) {
            $cells[$c] = $this->normalizeImportHeader((string) ($rowCells[$c] ?? ''));
        }

        return $cells;
    }

    public function buildRowCells(array $rowValues, int $maxCol): array
    {
        $rowCells = [];
        for ($c = 0; $c < $maxCol; $c++) {
            $rowCells[$c + 1] = $this->stringifyCell($rowValues[$c] ?? '');
        }

        return $rowCells;
    }

    public function stringifyCell($value): string
    {
        if ($value === null) {
            return '';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        if (is_string($value)) {
            return $value;
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        }

        return '';
    }

    public function detectHeaderMap(array $cells): ?array
    {
        $foundHeader = false;
        foreach ($cells as $cellVal) {
            if ($this->isHeaderMarker((string) $cellVal)) {
                $foundHeader = true;
                break;
            }
        }

        if (!$foundHeader) {
            return null;
        }

        $headerMap = [];
        foreach ($cells as $colIdx => $colName) {
            if ($colName !== '' && !isset($headerMap[$colName])) {
                $headerMap[$colName] = $colIdx;
            }
        }

        return [
            'header_map' => $headerMap,
            'max_col'    => max(count($cells), 20),
        ];
    }

    public function isEmptyRow(array $rowCells): bool
    {
        $nonEmpty = array_filter($rowCells, fn ($v) => trim((string) $v) !== '' && trim((string) $v) !== '-');

        return empty($nonEmpty);
    }

    private function isHeaderMarker(string $cellVal): bool
    {
        $compact = preg_replace('/[^a-z0-9]/', '', $cellVal) ?? '';

        return str_contains($cellVal, 'item code')
            || str_contains($cellVal, 'item name')
            || str_contains($cellVal, 'item/model')
            || str_contains($cellVal, 'item model')
            || $cellVal === 'items'
            || str_contains($cellVal, 'sale price')
            || str_contains($cellVal, 'selling price')
            || str_contains($cellVal, 'default mrp')
            || str_contains($cellVal, 'opening stock')
            || str_contains($compact, 'itemcode')
            || str_contains($compact, 'itemname')
            || str_contains($compact, 'saleprice')
            || str_contains($cellVal, 'product*')
            || str_contains($cellVal, 's.no');
    }

    private function normalizeImportHeader(?string $header): string
    {
        $header = strtolower(trim((string) $header));
        $header = str_replace(["\r\n", "\n", "\r", "\t", '_', "\u{00A0}"], ' ', $header);
        $header = preg_replace('/[\x00-\x1F\x7F]/', '', $header) ?? $header;
        $header = str_replace(['*', ':', '(', ')', '.'], ' ', $header);
        $header = preg_replace('/\s+/', ' ', $header) ?? $header;

        return trim($header);
    }
}
