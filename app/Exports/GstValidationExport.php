<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class GstValidationExport implements WithMultipleSheets
{
    public function __construct(private readonly array $result)
    {
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->result['sheets'] ?? [] as $sheet) {
            $rows = [];
            $headers = array_merge($sheet['headers'] ?? [], [
                'status',
                'error_message',
                'correct_taxable_value',
                'correct_cgst',
                'correct_sgst',
                'correct_igst',
                'correct_total_gst',
                'correct_total_value',
            ]);

            $rows[] = $headers;

            foreach ($sheet['rows'] ?? [] as $row) {
                $original = $row['original_row'] ?? [];
                $line = [];

                for ($i = 0; $i < count($sheet['headers'] ?? []); $i++) {
                    $line[] = $original[$i] ?? null;
                }

                $line[] = $row['status'] ?? '';
                $line[] = $row['error_message'] ?? '';
                $line[] = $row['correct_taxable_value'] ?? 0;
                $line[] = $row['correct_cgst'] ?? 0;
                $line[] = $row['correct_sgst'] ?? 0;
                $line[] = $row['correct_igst'] ?? 0;
                $line[] = $row['correct_total_gst'] ?? 0;
                $line[] = $row['correct_total_value'] ?? 0;

                $rows[] = $line;
            }

            $sheets[] = new GstValidationSheetExport($sheet['sheet_name'] ?? 'Sheet', $rows);
        }

        if ($sheets === []) {
            $sheets[] = new GstValidationSheetExport('No Data', [['No validation rows found']]);
        }

        return $sheets;
    }
}
