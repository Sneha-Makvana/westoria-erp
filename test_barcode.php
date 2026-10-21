<?php
require __DIR__.'/vendor/autoload.php';
$generator = new \Milon\Barcode\DNS1D();
$b1 = $generator->getBarcodePNG('12323', 'C128', 1.35, 34);
$b2 = $generator->getBarcodePNG('12323, 23213, 32323, 21321', 'C128', 1.35, 34);
echo "b1 length: " . strlen($b1) . "\n";
echo "b2 length: " . strlen($b2) . "\n";
