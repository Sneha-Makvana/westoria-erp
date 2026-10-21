<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\PurchaseInvoice;
use App\Models\Purchases;
use App\Models\Product;
use App\Models\Category;

try {
    // UPDATE THIS PATH FOR THE LIVE SERVER
    // Upload the Excel file to the 'public' folder and use the line below:
    $filePath = __DIR__ . '/Purchase - Bill Wise_3988.xlsx';
    
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
    $worksheet = $spreadsheet->getActiveSheet();
    
    // Group IMEIs by Bill No and Product Code
    $imeisByBillProduct = [];
    $totalImeis = 0;
    
    foreach ($worksheet->getRowIterator() as $row) {
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false); 
        $cells = [];
        foreach ($cellIterator as $cell) {
            $cells[] = $cell->getValue();
        }
        
        $billNo = $cells[2] ?? null;
        $productCode = $cells[8] ?? null;
        $itemModel = $cells[9] ?? null;
        $imei = $cells[10] ?? null;
        
        if ($billNo && $productCode && $imei && trim($imei) !== '' && $imei !== 'IMEI NO:') {
            // Only process if it has MOBILE in the Item/Model string from Excel OR we just process it.
            if (stripos($itemModel, 'MOBILE') !== false) {
                $imei = trim(preg_replace('/\xa0/', '', $imei));
                if (!isset($imeisByBillProduct[$billNo])) {
                    $imeisByBillProduct[$billNo] = [];
                }
                if (!isset($imeisByBillProduct[$billNo][$productCode])) {
                    $imeisByBillProduct[$billNo][$productCode] = [];
                }
                if (!in_array($imei, $imeisByBillProduct[$billNo][$productCode])) {
                    $imeisByBillProduct[$billNo][$productCode][] = $imei;
                    $totalImeis++;
                }
            }
        }
    }
    
    $updatedRecords = 0;
    $errors = [];
    
    foreach ($imeisByBillProduct as $billNo => $productsData) {
        $invoice = PurchaseInvoice::where('bill_no', $billNo)->first();
        if (!$invoice) {
            $errors[] = "Invoice not found for bill_no: $billNo";
            continue;
        }
        
        foreach ($productsData as $productCode => $imeis) {
            $product = Product::where('product_code', $productCode)->first();
            if (!$product) {
                $errors[] = "Product not found for code: $productCode";
                continue;
            }
            
            // Get all purchase items for this product in this invoice
            $purchaseItems = Purchases::where('invoice_id', $invoice->id)
                                     ->where('item', $product->id)
                                     ->orderBy('id', 'asc') // Important to order them consistently
                                     ->get();
                                     
            if ($purchaseItems->count() > 0) {
                $imeiIndex = 0;
                
                foreach ($purchaseItems as $purchaseItem) {
                    $qty = (int) $purchaseItem->quantity;
                    $assignedImeis = [];
                    
                    // Assign as many IMEIs to this row as its quantity
                    for ($i = 0; $i < $qty; $i++) {
                        if (isset($imeis[$imeiIndex])) {
                            $assignedImeis[] = $imeis[$imeiIndex];
                            $imeiIndex++;
                        }
                    }
                    
                    // Only update if we actually assigned IMEIs to this row
                    if (!empty($assignedImeis)) {
                        $purchaseItem->imei_no = json_encode($assignedImeis);
                        $purchaseItem->save();
                        $updatedRecords++;
                    } else {
                        // If there are no more IMEIs to assign, we might want to clear it if it had wrong data
                        $purchaseItem->imei_no = json_encode([]);
                        $purchaseItem->save();
                    }
                }
            } else {
                $errors[] = "Purchase record not found for invoice_id: $invoice->id and product_id: $product->id";
            }
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'message' => "Successfully updated $updatedRecords purchase records.",
        'total_imeis_found_in_excel' => $totalImeis,
        'errors' => $errors
    ]);

} catch (\Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
