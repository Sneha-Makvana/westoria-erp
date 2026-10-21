<?php
$lines = file('resources/views/sales/pos.blade.php');
foreach ($lines as $num => $line) {
    if (stripos($line, 'scanner') !== false || stripos($line, 'barcode') !== false || stripos($line, 'selectedItems') !== false) {
        echo ($num + 1) . ": " . trim($line) . "\n";
    }
}
