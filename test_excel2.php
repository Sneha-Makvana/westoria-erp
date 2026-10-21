<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $file = '123_6556.xlsx';
    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file);
    $reader->setReadDataOnly(true);
    $spreadsheet = $reader->load($file);
    $sheet = $spreadsheet->getActiveSheet();
    $maxRow = $sheet->getHighestRow();
    echo "Max Row: $maxRow\n";
    for ($r = 1; $r <= 2; $r++) {
        for ($c = 1; $c <= 30; $c++) {
            $val = $sheet->getCellByColumnAndRow($c, $r)->getValue();
            echo "R$r C$c: " . $val . "\n";
        }
    }
    echo "Success\n";
} catch (\Exception $e) {
    echo $e->getMessage() . "\n" . $e->getTraceAsString();
}
