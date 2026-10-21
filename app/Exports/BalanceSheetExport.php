<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class BalanceSheetExport implements FromArray, WithTitle, WithStyles, ShouldAutoSize
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Balance Sheet';
    }

    public function array(): array
    {
        $rows     = [];
        $sym      = $this->data['sym'] ?? '₹';
        $startDate = $this->data['startDate'] ?? '';
        $endDate   = $this->data['endDate'] ?? '';

        $fmtDate = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d-M-Y') : '';

        // Company header
        $rows[] = [$this->data['companyName'] ?? ''];
        $rows[] = [$this->data['companyAddress'] ?? ''];
        $rows[] = ['Contact: ' . ($this->data['companyPhone'] ?? '')];
        $rows[] = ['Balance Sheet'];
        $rows[] = ['From ' . $fmtDate($startDate) . ' to ' . $fmtDate($endDate)];
        $rows[] = [''];

        // Column headers
        $rows[] = ['ASSETS', '', 'LIABILITIES & EQUITY', ''];
        $rows[] = ['Particulars', 'Amount (' . $sym . ')', 'Particulars', 'Amount (' . $sym . ')'];

        $assetGroups     = $this->data['assetGroups'] ?? [];
        $liabilityGroups = $this->data['liabilityGroups'] ?? [];

        // Flatten both sides into parallel rows
        $assetLines     = $this->flattenGroups($assetGroups, $sym);
        $liabilityLines = $this->flattenGroups($liabilityGroups, $sym);

        $maxRows = max(count($assetLines), count($liabilityLines));

        for ($i = 0; $i < $maxRows; $i++) {
            $aLine = $assetLines[$i] ?? ['', ''];
            $lLine = $liabilityLines[$i] ?? ['', ''];
            $rows[] = array_merge($aLine, $lLine);
        }

        $rows[] = [''];

        // Totals row
        $totals = $this->data['totals'] ?? [];
        $rows[] = [
            'Total Assets',
            $sym . number_format($totals['assets'] ?? 0, 2),
            'Total Liabilities & Equity',
            $sym . number_format($totals['liabilities_equity'] ?? 0, 2),
        ];

        return $rows;
    }

    private function flattenGroups(array $groups, string $sym): array
    {
        $lines = [];
        foreach ($groups as $groupName => $items) {
            $lines[] = [$groupName, ''];           // group heading
            foreach ($items as $label => $value) {
                $lines[] = ['  ' . $label, $sym . number_format((float) $value, 2)];
            }
            $lines[] = ['', ''];                   // spacer
        }
        return $lines;
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = $sheet->getHighestColumn();

        // Company name — row 1
        $sheet->mergeCells('A1:D1');
        $sheet->mergeCells('A2:D2');
        $sheet->mergeCells('A3:D3');
        $sheet->mergeCells('A4:D4');
        $sheet->mergeCells('A5:D5');

        return [
            1 => [
                'font'      => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            2 => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            3 => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            4 => [
                'font'      => ['bold' => true, 'size' => 12],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            5 => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            7 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1B2850'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            8 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F3F4F6'],
                ],
            ],
            $lastRow => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFF3CD'],
                ],
            ],
        ];
    }
}
