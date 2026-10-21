<?php

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Sync API Docs');

// Define headers
$headers = [
    'A1' => 'Sr No',
    'B1' => 'Module/Group',
    'C1' => 'API Name',
    'D1' => 'Method',
    'E1' => 'Endpoint URL',
    'F1' => 'Headers',
    'G1' => 'Auth Type',
    'H1' => 'Bearer Token',
    'I1' => 'Request Params/Keys',
    'J1' => 'Request Body (Sample JSON)',
    'K1' => 'Response (Sample JSON)',
    'L1' => 'Status Codes',
    'M1' => 'Description/Notes'
];

foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}

// Styling the header row
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['argb' => Color::COLOR_WHITE],
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => 'FF4F81BD'], // Blue
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ]
];
$sheet->getStyle('A1:M1')->applyFromArray($headerStyle);
$sheet->getRowDimension(1)->setRowHeight(30);

// Freeze header row
$sheet->freezePane('A2');

// Auto-filter enabled on headers
$sheet->setAutoFilter('A1:M1');

$apis = [
    [
        'Sr No' => 1,
        'Module/Group' => 'Sync',
        'API Name' => 'API Login',
        'Method' => 'POST',
        'Endpoint URL' => '/api/sync/login',
        'Headers' => "Content-Type: application/json\nAccept: application/json",
        'Auth Type' => 'None',
        'Bearer Token' => 'N/A',
        'Request Params/Keys' => 'email, password',
        'Request Body (Sample JSON)' => "{\n  \"email\": \"admin@gmail.com\",\n  \"password\": \"12345678\"\n}",
        'Response (Sample JSON)' => "{\n  \"status\": true,\n  \"message\": \"Login successful.\",\n  \"token\": \"eyJ0...\",\n  \"user\": { ... }\n}",
        'Status Codes' => '200, 401, 422',
        'Description/Notes' => 'Session-free login endpoint. Returns a Passport bearer token used for other Sync APIs.'
    ],
    [
        'Sr No' => 2,
        'Module/Group' => 'Sync',
        'API Name' => 'Full Pull',
        'Method' => 'GET',
        'Endpoint URL' => '/api/sync/full-pull',
        'Headers' => "Accept: application/json",
        'Auth Type' => 'Bearer Token',
        'Bearer Token' => 'Required (from login API)',
        'Request Params/Keys' => 'None',
        'Request Body (Sample JSON)' => "",
        'Response (Sample JSON)' => "{\n  \"success\": true,\n  \"syncedAt\": \"2026-08-11T12:00:00Z\",\n  \"data\": {\n    \"categories\": [...],\n    \"brands\": [...],\n    \"products\": [...],\n    \"vendors\": [...],\n    \"purchases\": [...],\n    \"sales\": [...],\n    \"productVariants\": [...]\n  }\n}",
        'Status Codes' => '200, 401, 500',
        'Description/Notes' => 'Downloads all Categories, Brands, Units, Products, ProductVariants, Vendors, Purchases, and Sales from the server database to the desktop app.'
    ],
    [
        'Sr No' => 3,
        'Module/Group' => 'Sync',
        'API Name' => 'Full Push',
        'Method' => 'POST',
        'Endpoint URL' => '/api/sync/full-push',
        'Headers' => "Content-Type: application/json\nAccept: application/json",
        'Auth Type' => 'Bearer Token',
        'Bearer Token' => 'Required',
        'Request Params/Keys' => 'deviceId, timestamp, categories[], brands[], units[], products[], productVariants[], vendors[], purchases[], sales[]',
        'Request Body (Sample JSON)' => "{\n  \"deviceId\": \"DESKTOP-1\",\n  \"timestamp\": \"2026-08-11T12:00:00Z\",\n  \"products\": [\n    { \"id\": 1, \"name\": \"...\" }\n  ],\n  \"vendors\": [...]\n}",
        'Response (Sample JSON)' => "{\n  \"success\": true,\n  \"message\": \"Full database synced successfully\",\n  \"syncedAt\": \"2026-08-11T12:00:00Z\",\n  \"recordsProcessed\": {\n    \"products\": 1,\n    \"vendors\": 1\n  }\n}",
        'Status Codes' => '200, 401, 500',
        'Description/Notes' => 'Uploads new/modified records from Desktop App to the Server DB.'
    ],
    [
        'Sr No' => 4,
        'Module/Group' => 'Sync',
        'API Name' => 'Images Batch Upload',
        'Method' => 'POST',
        'Endpoint URL' => '/api/sync/images-batch',
        'Headers' => "Accept: application/json",
        'Auth Type' => 'Bearer Token',
        'Bearer Token' => 'Required',
        'Request Params/Keys' => 'images[] (File array), mapping (JSON string)',
        'Request Body (Sample JSON)' => "form-data:\n images[] = <file1>\n images[] = <file2>\n mapping = {\"101\": [\"file1.png\", \"file2.png\"]}",
        'Response (Sample JSON)' => "{\n  \"success\": true,\n  \"message\": \"Successfully uploaded 2 image(s).\"\n}",
        'Status Codes' => '200, 400, 401, 500',
        'Description/Notes' => 'Uploads product images and links them to the correct product ID based on the mapping JSON. Supports array of filenames for multiple images.'
    ]
];

$row = 2;
foreach ($apis as $api) {
    $col = 'A';
    foreach ($api as $key => $val) {
        $sheet->setCellValue($col . $row, $val);
        $col++;
    }
    $row++;
}

// Wrap text in Request/Response body columns
$sheet->getStyle('F2:F5')->getAlignment()->setWrapText(true);
$sheet->getStyle('J2:J5')->getAlignment()->setWrapText(true);
$sheet->getStyle('K2:K5')->getAlignment()->setWrapText(true);
$sheet->getStyle('M2:M5')->getAlignment()->setWrapText(true);

// Set column widths for readability
$sheet->getColumnDimension('A')->setWidth(6);
$sheet->getColumnDimension('B')->setWidth(15);
$sheet->getColumnDimension('C')->setWidth(20);
$sheet->getColumnDimension('D')->setWidth(10);
$sheet->getColumnDimension('E')->setWidth(30);
$sheet->getColumnDimension('F')->setWidth(35);
$sheet->getColumnDimension('G')->setWidth(15);
$sheet->getColumnDimension('H')->setWidth(25);
$sheet->getColumnDimension('I')->setWidth(40);
$sheet->getColumnDimension('J')->setWidth(50);
$sheet->getColumnDimension('K')->setWidth(50);
$sheet->getColumnDimension('L')->setWidth(15);
$sheet->getColumnDimension('M')->setWidth(40);

$writer = new Xlsx($spreadsheet);
$fileName = 'API_Documentation.xlsx';
$writer->save(__DIR__ . '/' . $fileName);

echo "Excel file generated successfully: " . __DIR__ . "\\" . $fileName . "\n";
