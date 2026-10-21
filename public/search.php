<?php
$lines = file('../app/Http/Controllers/api/ProductController.php');
foreach ($lines as $num => $line) {
    if (stripos($line, 'function update') !== false || stripos($line, 'updateProduct') !== false) {
        echo ($num + 1) . ": " . trim($line) . "\n";
    }
}
