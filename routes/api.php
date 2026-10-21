<?php

use App\Http\Controllers\api\AdvancePaymentController;
use App\Http\Controllers\api\AppointmentController;
use App\Http\Controllers\api\AttendanceController;
use App\Http\Controllers\admin\AttendanceeController;
use App\Http\Controllers\admin\CompanyRulesController as AdminCompanyRulesController;
use App\Http\Controllers\api\PayrollController;
use App\Http\Controllers\api\HrDashboardController;
use App\Http\Controllers\api\CompanyRulesController;
use App\Http\Controllers\admin\LeaveController as AdminLeaveController;
use App\Http\Controllers\admin\DepartmentController as AdminDepartmentController;
use App\Http\Controllers\admin\DesignationController as AdminDesignationController;
use App\Http\Controllers\admin\HolidaysController as AdminHolidaysController;
use App\Http\Controllers\admin\LeaveTypeController as AdminLeaveTypeController;
use App\Http\Controllers\api\SalaryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\ManufacturingController;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use App\Http\Controllers\api\LoginController;
use App\Http\Controllers\api\ChangePasswordController;
use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\api\ProfileController;
use App\Http\Controllers\api\CustomerController;
use App\Http\Controllers\api\SupplierController;
use App\Http\Controllers\api\FinancerController;
use App\Http\Controllers\api\TaxRateController;
use App\Http\Controllers\api\CurrencyController;
use App\Http\Controllers\api\CreditNotesTypeController;
use App\Http\Controllers\api\CreditNoteItemApiController;
use App\Http\Controllers\api\DebitNoteItemApiController;
use App\Http\Controllers\api\GeneralSettingController;
use App\Http\Controllers\api\PurchaseController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\BrandController;
use App\Http\Controllers\api\SalesController;
use App\Http\Controllers\api\ExpenseController;
use App\Http\Controllers\api\ExpenseTypeController;
use App\Http\Controllers\api\SaleReturnController;
use App\Http\Controllers\admin\RentalReturnController;
use App\Http\Controllers\api\SettingController;
use App\Http\Controllers\api\CustomInvoiceController;
use App\Http\Controllers\api\StaffController;
use App\Http\Controllers\api\GstController;
use App\Http\Controllers\api\TdsController;
use App\Http\Controllers\api\LabourItemController;
use App\Http\Controllers\api\SubBranchController;
use App\Http\Controllers\api\AccountLedgerController;
use App\Http\Controllers\api\InventoryController;
use App\Http\Controllers\api\PurchaseReturnController;
use App\Http\Controllers\api\IncomeSheetController;
use App\Http\Controllers\api\BalanceSheetController;
use App\Http\Controllers\api\FacebookAppConfigurationController;
use App\Http\Controllers\api\BankMasterController;
use App\Http\Controllers\api\GSTR_1Controller;
use App\Http\Controllers\api\GstSalesReportController;
use App\Http\Controllers\api\TransactionApiController;
use App\Http\Controllers\api\TransactionController;
use App\Http\Controllers\api\ConnectedDevicesController;
use App\Http\Controllers\api\MeetingController;
use App\Http\Controllers\api\LeadController;
use App\Http\Controllers\api\NotificationController;
use App\Http\Controllers\api\SmtpSettingController;
use App\Http\Controllers\api\UnitController as ApiUnitController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\admin\SalesReceiptPaymentController;
use App\Http\Controllers\api\FollowUpController;
use App\Http\Controllers\api\TicketApiController;
use App\Http\Controllers\api\PlanController;
use App\Http\Controllers\api\RowMaterialController;
use App\Http\Controllers\api\TypeController;
use App\Http\Controllers\api\MaterialController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// E-Way Bill and E-Invoice Public Routes
Route::get('/eway-bill/irn/{irn}', [\App\Http\Controllers\api\EwayBillController::class, 'getEwayBillByIrnPublic']);
Route::post('/eway-bill/auth', [\App\Http\Controllers\api\EwayBillController::class, 'auth']);
Route::get('/einvoice/irn/{irn}', [\App\Http\Controllers\api\EinvoiceController::class, 'getEinvoiceByIrnPublic']);
Route::post('/einvoice/auth', [\App\Http\Controllers\api\EinvoiceController::class, 'auth']);


Route::post('/oauth/token', [AccessTokenController::class, 'issueToken'])->middleware(['throttle', 'passport.client']);
Route::post('/loginapi', [LoginController::class, 'login'])->name('loginapi');
Route::post('/sync/login', [\App\Http\Controllers\api\SyncController::class, 'apiLogin']);
Route::post('/sales/pdf-download', [SalesController::class, 'view_sales_report'])->name('sales.report.html');
// Route::get('sales/report/view-page', [SalesController::class, 'show_sales_report_page']);
Route::post('purchase/report/view', [PurchaseController::class, 'view_purchase_report']);
Route::post('/expense/report/view', [ExpenseController::class, 'view_expense_report']);

Route::middleware('auth:api')->get('/hr/dashboard-data', function() {
    return app(\App\Http\Controllers\api\HrDashboardController::class)->getDashboardData();
});

Route::get('/get-salary', [PayrollController::class, 'getSalary']);
Route::prefix('payroll')->group(function () {
    Route::post('/create', [PayrollController::class, 'create']);
    Route::get('/getAll', [PayrollController::class, 'getAll']);
    Route::get('/{id}', [PayrollController::class, 'getByEmployee'])->whereNumber('id');
    Route::get('/details/{id}', [PayrollController::class, 'getProfile'])->whereNumber('id');
    Route::post('/update/{id}', [PayrollController::class, 'update'])->whereNumber('id');
    Route::delete('/{id}', [PayrollController::class, 'delete'])->whereNumber('id');
    Route::post('/get-worked-hours', [PayrollController::class, 'getWorkedHours']);
    Route::post('/get-working-days', [PayrollController::class, 'getWorkingDays']);
    Route::post('/calculate-payroll', [PayrollController::class, 'calculatePayroll']);
    Route::post('/save', [PayrollController::class, 'save']);
    Route::post('/saveAll', [PayrollController::class, 'saveAll']);
    Route::post('/get-monthly-leaves', [PayrollController::class, 'getMonthLeaves']);
    Route::post('/get-leave-details', [PayrollController::class, 'getLeaveDetails']);
    Route::post('/get-remaining-leaves/{userId}', [PayrollController::class, 'getRemainingPaidLeaves'])->whereNumber('userId');
    Route::match(['get', 'post'], '/get-deduction-breakdown', [PayrollController::class, 'getDeductionBreakdown']);
    Route::post('/downloadMultiple', [PayrollController::class, 'downloadMultiple']);
    Route::post('/downloadMultipleByIds', [PayrollController::class, 'downloadMultipleByIds']);
});


Route::middleware('auth:api')->get('/dashboard-api', [LoginController::class, 'dashboardApi']);

Route::get('/test-db-update', function() {
    if (!\Illuminate\Support\Facades\Schema::hasColumn('products', 'barcode_details')) {
        \Illuminate\Support\Facades\Schema::table('products', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->json('barcode_details')->nullable();
        });
        return "Added barcode_details";
    }
    return "Already exists";
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/sales/invoice/pdf/download/{id}', [SalesController::class, 'salse_invoice_pdf_download'])->name('sales.invoice.pdf.download');
    Route::get('/purchase/invoice/pdf/download/{id}', [PurchaseController::class, 'purchase_invoice_pdf_download'])->name('purchase.invoice.pdf.download');
    Route::get('/custom-invoice/pdf/download/{id}', [CustomInvoiceController::class, 'custom_invoice_pdf_download'])->name('custom_invoice.pdf.download');


    // Route::get('/dashboard-api', [LoginController::class, 'dashboardApi']);


    Route::get('/gstr1/export-excel', [GSTR_1Controller::class, 'exportExcel']);
    Route::match(['get', 'post'], '/gstr1/export-hsn-summary', [GSTR_1Controller::class, 'exportHsnSummary']);
    Route::match(['get', 'post'], '/gstr1/hsn-summary', [GSTR_1Controller::class, 'hsnSummaryList']);
    Route::get('/gstr1/hsn-summary/orders', [GSTR_1Controller::class, 'hsnSummaryOrderOptions']);
    // Route::get('/gstr2b/export-excel', [GstSalesReportController::class, 'exportExcel']);
    Route::get('/gstr3b/export-excel', [GstSalesReportController::class, 'exportGstr3b']);



    Route::get('logout', [LoginController::class, 'logoutapi'])->name('logout');
    Route::get('getAllProduct', [ProductController::class, 'getAllProduct'])->name('getAllProduct');
    Route::get('fix-db', function() {
        \Illuminate\Support\Facades\Schema::table('products', function (\Illuminate\Database\Schema\Blueprint $table) {
            if (!\Illuminate\Support\Facades\Schema::hasColumn('products', 'mrp')) {
                $table->decimal('mrp', 15, 2)->nullable()->after('price');
            }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('products', 'batch_no')) {
                $table->string('batch_no')->nullable()->after('mrp');
            }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('products', 'label_code')) {
                $table->string('label_code')->nullable()->after('batch_no');
            }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('products', 'label_reference')) {
                $table->string('label_reference')->nullable()->after('label_code');
            }
        });
        if (!\Illuminate\Support\Facades\Schema::hasColumn('settings', 'label_formats')) {
            \Illuminate\Support\Facades\Schema::table('settings', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->json('label_formats')->nullable()->after('barcode_detail_option');
            });
        }
        return "Database fixed!";
    });

    Route::post('createProduct', [ProductController::class, 'createProduct'])->name('createProduct');
    Route::get('next-product-sku', [ProductController::class, 'getNextProductSku'])->name('nextProductSku');
    Route::get('last-added-product', [ProductController::class, 'getLastAddedProduct'])->name('lastAddedProduct');
    Route::post('resequence-product-skus', [ProductController::class, 'resequenceProductSkus'])->name('resequenceProductSkus');
    Route::post('sync-products-to-branches', [ProductController::class, 'syncCatalogToOtherBranches'])
        ->withoutMiddleware([\Illuminate\Routing\Middleware\ThrottleRequests::class])
        ->name('syncProductsToBranches');
    Route::post('sync-products-to-branches/start', [ProductController::class, 'startBackgroundCatalogSync'])
        ->withoutMiddleware([\Illuminate\Routing\Middleware\ThrottleRequests::class])
        ->name('syncProductsToBranchesStart');
    Route::get('sync-products-to-branches/status', [ProductController::class, 'catalogSyncStatus'])
        ->withoutMiddleware([\Illuminate\Routing\Middleware\ThrottleRequests::class])
        ->name('syncProductsToBranchesStatus');
    Route::post('products/generate-image', [ProductController::class, 'generateImage'])->name('generateImage');
    Route::post('products/generate-barcodes', [ProductController::class, 'generateUniqueBarcodes'])->name('generateUniqueBarcodes');
    Route::post('products/remove-background', [ProductController::class, 'removeBackground'])->name('removeBackground');
    Route::get('all-product-price-history', [ProductController::class, 'getAllPriceHistory'])->name('product.all.price.history');
    Route::get('product-barcode-history/{id}', [ProductController::class, 'getBarcodeHistory'])->name('product.barcode.history');
    Route::post('update-product-barcode-info', [ProductController::class, 'updateBarcodeInfo'])->name('product.update.barcode.info');
    Route::get('translate-product-name', [ProductController::class, 'translateProductName'])->name('product.translate.name');
    Route::post('save-product-hindi-name', [ProductController::class, 'saveProductHindiName'])->name('product.save.hindi.name');
    Route::post('apply-global-batch-no', [ProductController::class, 'applyGlobalBatchNo'])->name('product.apply.global.batch');
    Route::get('getProducts', [ProductController::class, 'getProducts'])->name('getProducts');
    Route::post('updateProduct', [ProductController::class, 'updateProduct'])->name('updateProduct');
    Route::post('quick-update-product', [ProductController::class, 'quickUpdateProduct'])->name('quickUpdateProduct');
    Route::post('deleteProduct/{id}', [ProductController::class, 'deleteProduct'])->name('deleteProduct');
    Route::post('restoreProduct/{id}', [ProductController::class, 'restoreProduct'])->name('restoreProduct');
    Route::get('importPreviewRows/{token}', [ProductController::class, 'getImportPreviewRows'])->name('importPreviewRows');
    Route::get('importPreviewStatus/{token}', [ProductController::class, 'getImportPreviewStatus'])->name('importPreviewStatus');
    Route::post('processImportPreview/{token}', [ProductController::class, 'processImportPreview'])->name('processImportPreview');
    Route::post('previewImportProducts', [ProductController::class, 'previewImportProducts'])->name('previewImportProducts');
    Route::post('importProducts', [ProductController::class, 'importProducts'])->name('importProducts');
    Route::post('remove-product-image', [ProductController::class, 'removeProductImage'])->name('removeProductImage');
    Route::get('/product-quantity-history/{productId}', [ProductController::class, 'getQuantityHistory']);
    Route::get('/export-product', [ProductController::class, 'export_product']);
    Route::get('/export-product-pdf', [ProductController::class, 'export_product_pdf']);
    Route::get('/export-all-barcodes-pdf', [ProductController::class, 'exportAllBarcodesPdf']);
    Route::get('/product-barcode-pdf/{id}', [ProductController::class, 'downloadBarcodePdf']);
    Route::get('/label-formats', [\App\Http\Controllers\api\LabelFormatController::class, 'index']);
    Route::post('/label-formats', [\App\Http\Controllers\api\LabelFormatController::class, 'store']);
    Route::delete('/label-formats/{id}', [\App\Http\Controllers\api\LabelFormatController::class, 'destroy']);
    
    // Custom Barcode Templates API
    Route::get('/barcode-templates', [\App\Http\Controllers\BarcodeTemplateController::class, 'index']);
    Route::post('/barcode-templates', [\App\Http\Controllers\BarcodeTemplateController::class, 'store']);
    Route::get('/barcode-templates/{id}', [\App\Http\Controllers\BarcodeTemplateController::class, 'show']);
    Route::put('/barcode-templates/{id}', [\App\Http\Controllers\BarcodeTemplateController::class, 'update']);
    Route::post('/barcode-templates/{id}/rename', [\App\Http\Controllers\BarcodeTemplateController::class, 'rename']);
    Route::delete('/barcode-templates/{id}', [\App\Http\Controllers\BarcodeTemplateController::class, 'destroy']);
    Route::get('/products/search-by-barcode', [ProductController::class, 'searchByBarcode'])->name('products.search-by-barcode');
    
    // Pricing Updates API moved to web.php

    // Account Branch API
    Route::get('getAllAccountBranch', [\App\Http\Controllers\api\AccountBranchController::class, 'getAllAccountBranch'])->name('getAllAccountBranch');
    Route::get('getAccountBranchById/{id}', [\App\Http\Controllers\api\AccountBranchController::class, 'getAccountBranchById'])->name('getAccountBranchById');
    Route::post('createAccountBranch', [\App\Http\Controllers\api\AccountBranchController::class, 'createAccountBranch'])->name('createAccountBranch');
    Route::post('updateAccountBranch', [\App\Http\Controllers\api\AccountBranchController::class, 'updateAccountBranch'])->name('updateAccountBranch');
    Route::post('deleteAccountBranch/{id}', [\App\Http\Controllers\api\AccountBranchController::class, 'deleteAccountBranch'])->name('deleteAccountBranch');


    // Receipt Payment API
    Route::post('/receipt-payment/store', [SalesReceiptPaymentController::class, 'apiStore'])->name('api.receipt.store');
    Route::post('/vendor-payment/store', [SalesReceiptPaymentController::class, 'apiVendorStore'])->name('api.vendor.store');


    Route::get('/manufacturing/products', [ManufacturingController::class, 'productOptions']);
    Route::get('/manufacturing/materials', [ManufacturingController::class, 'materialOptions']);
    Route::get('/manufacturing/boms', [ManufacturingController::class, 'bomList']);
    Route::get('/manufacturing/boms/{id}', [ManufacturingController::class, 'bomDetails']);
    Route::post('/manufacturing/boms', [ManufacturingController::class, 'storeBom']);
    Route::put('/manufacturing/boms/{id}', [ManufacturingController::class, 'updateBom']);
    Route::delete('/manufacturing/boms/{id}', [ManufacturingController::class, 'deleteBom']);
    Route::get('/manufacturing/bom-for-product/{productId}', [ManufacturingController::class, 'bomForProduct']);
    Route::post('/manufacturing/production-preview', [ManufacturingController::class, 'productionPreview']);
    Route::get('/manufacturing/productions', [ManufacturingController::class, 'productionList']);
    Route::get('/manufacturing/productions/{id}', [ManufacturingController::class, 'productionDetails']);
    Route::post('/manufacturing/productions', [ManufacturingController::class, 'storeProduction']);
    Route::put('/manufacturing/productions/{id}', [ManufacturingController::class, 'updateProduction']);
    Route::delete('/manufacturing/productions/{id}', [ManufacturingController::class, 'deleteProduction']);

    Route::get('income-statement', [IncomeSheetController::class, 'GetAll']);
    Route::get('balance-sheet', [BalanceSheetController::class, 'getBalanceSheet']);

    Route::get('/get-category', [ProductController::class, 'getCategory']);
    Route::get('/get-brand', [ProductController::class, 'getBrand']);
    Route::get('/get-tax-rates', [ProductController::class, 'getTaxRates']);
    Route::get('/edit_product/{id}', [ProductController::class, 'edit_product']);
    Route::get('/get-units', [ProductController::class, 'getUnits']);

     Route::get('/profit-loss-report/data', [ProductController::class, 'getProfitLossData'])->name('profit-loss-report.data');
    Route::get('profit-loss-report/pdf', [ProductController::class, 'profitLossPdf'])->name('profit-loss-report.pdf');


    Route::get('/order-report', [SalesController::class, 'orderReport']);

    // TDS API
    Route::get('/tds-order-report', [SalesController::class, 'tdsOrderReport']);
    Route::get('/tds-order-report/export-excel', [SalesController::class, 'exportTdsOrderReportExcel']);
    Route::get('/tds-order-report/export-pdf', [SalesController::class, 'exportTdsOrderReportPdf']);

    //Purchase Return
    Route::get('get-vendor-name/{invoiceId}', [PurchaseReturnController::class, 'getVendorName']);
    Route::get('get-invoice-products/{invoiceId}', [PurchaseReturnController::class, 'getInvoiceProducts']);
    Route::post('return-purchase', [PurchaseReturnController::class, 'returnPurchase']);
    Route::get('/purchase-return-list', [PurchaseReturnController::class, 'purchaseReturnList'])->name('purchaseReturnList');


    Route::get('getProfile', [ProfileController::class, 'getProfile'])->name('getProfile');
    Route::post('updateProfile', [ProfileController::class, 'updateProfile'])->name('updateProfile');
    Route::post('change-password', [ChangePasswordController::class, 'changePassword'])->name('change-password');
    Route::post('change-password-web', [ChangePasswordController::class, 'changePasswordWeb'])->name('change-password-web');
    Route::get('getProductById/{num:}', [ProductController::class, 'getProductById'])->name('getProductById');
    Route::get('get_subadmin', [ProfileController::class, 'get_subadmin'])->name('get_subadmin');

    Route::post('customers/face-match', [\App\Http\Controllers\api\FaceRecognitionController::class, 'matchFace'])->name('customers.face_match');
    Route::post('customers/face-register', [\App\Http\Controllers\api\FaceRecognitionController::class, 'registerFace'])->name('customers.face_register');
    Route::post('createCustomer', [CustomerController::class, 'createCustomer'])->name('createCustomer');
    Route::post('deleteCustomer/{num:}', [CustomerController::class, 'deleteCustomer'])->name('deleteCustomer');
    Route::get('/getCustomer/{id}', [CustomerController::class, 'getCustomer'])->name('getCustomer');
    Route::post('/updateCustomer/{id}', [CustomerController::class, 'updateCustomer'])->name('updateCustomer');
    Route::get('getAllCustomer', [CustomerController::class, 'getAllCustomer'])->name('getAllCustomer');
    Route::get('/next-customer-code', [CustomerController::class, 'getNextCustomerCode']);
    // Route::get('/customer-view/{id}/data', [CustomerController::class, 'getCustomerData']);
    Route::get('customer/{id}', [CustomerController::class, 'getCustomerProfile']);

    Route::post('/fetch-gst-details', [SupplierController::class, 'fetch'])->name('gst.fetch');
    Route::post('createSupplier', [SupplierController::class, 'createSupplier'])->name('createSupplier');
    Route::post('deleteSupplier/{num:}', [SupplierController::class, 'deleteSupplier'])->name('deleteSupplier');
    Route::get('/getSupplier/{id}', [SupplierController::class, 'getSupplier'])->name('getSupplier');
    Route::post('/updateSupplier/{id}', [SupplierController::class, 'updateSupplier'])->name('updateSupplier');
    Route::get('getAllSupplier', [SupplierController::class, 'getAllSupplier'])->name('getAllSupplier');
    Route::get('vendor/{id}', [SupplierController::class, 'getVendorProfile']);
    Route::get('getAllFinancer', [FinancerController::class, 'getAllFinancer'])->name('getAllFinancer');
    Route::post('importFinancers', [FinancerController::class, 'importFinancers'])->name('importFinancers');
    Route::post('deleteFinancer/{num:}', [FinancerController::class, 'deleteFinancer'])->name('deleteFinancer');
    Route::get('financer/{id}', [FinancerController::class, 'getFinancer']);

    Route::get('getAllCategory', [CategoryController::class, 'getAllCategory'])->name('getAllCategory');
    Route::post('addcategory', [CategoryController::class, 'addcategory'])->name('addcategory');
    Route::post('deleteCategory/{id}', [CategoryController::class, 'deleteCategory'])->name('deleteCategory');
    Route::post('updatecategory', [CategoryController::class, 'updatecategory'])->name('updatecategory');

    Route::get('getAllBrand', [BrandController::class, 'getAllBrand'])->name('getAllBrand');
    Route::post('addBrand', [BrandController::class, 'addBrand'])->name('addBrand');

    Route::get('/tax-rates', [TaxRateController::class, 'index'])->name('tax-rates.index');
    Route::post('/tax-rates/store', [TaxRateController::class, 'store'])->name('tax-rates.store');
    Route::get('/tax-rates/{id}', [TaxRateController::class, 'edit'])->name('tax-rates.edit');
    Route::post('/tax-rates/update/{id}', [TaxRateController::class, 'update'])->name('tax-rates.update');
    Route::post('/tax-rates/delete/{id}', [TaxRateController::class, 'destroy'])->name('tax-rates.destroy');

    Route::get('/currency', [CurrencyController::class, 'index'])->name('currency.index');
    Route::post('/currency/store', [TaxRateController::class, 'store'])->name('currency.store');
    Route::get('/currency/{id}', [TaxRateController::class, 'edit'])->name('currency.edit');
    Route::post('/currency/update/{id}', [TaxRateController::class, 'update'])->name('currency.update');
    Route::post('/currency/delete/{id}', [TaxRateController::class, 'destroy'])->name('currency.destroy');


     // credit notes type
    Route::get('/credit-notes-types', [CreditNotesTypeController::class, 'index'])->name('credit-notes-types.index');
    Route::post('/credit-notes-types/store', [CreditNotesTypeController::class, 'store'])->name('credit-notes-types.store');
    Route::get('/credit-notes-types/{id}', [CreditNotesTypeController::class, 'show'])->name('credit-notes-types.show');
    Route::post('/credit-notes-types/update/{id}', [CreditNotesTypeController::class, 'update'])->name('credit-notes-types.update');
    Route::post('/credit-notes-types/delete/{id}', [CreditNotesTypeController::class, 'destroy'])->name('credit-notes-types.destroy');

    // credit notes items
    Route::get('/credit-note-items-api', [CreditNoteItemApiController::class, 'index'])->name('credit-note-items.api.index');
    Route::get('/getPurchaseDetails/{id}', [CreditNoteItemApiController::class, 'getPurchaseDetails'])->name('getPurchaseDetails');
    Route::post('/credit-note-items-api/store', [CreditNoteItemApiController::class, 'store'])->name('credit-note-items.api.store');
    Route::get('/credit-note-items-api/{id}', [CreditNoteItemApiController::class, 'show'])->name('credit-note-items.api.show');
    Route::post('/credit-note-items-api/update/{id}', [CreditNoteItemApiController::class, 'update'])->name('credit-note-items.api.update');
    Route::post('/credit-note-items-api/delete/{id}', [CreditNoteItemApiController::class, 'destroy'])->name('credit-note-items.api.destroy');

    // debit note items
    Route::get('/debit-note-items', [DebitNoteItemApiController::class, 'index'])->name('debit-note-items.index');
    Route::post('/debit-note-items/store', [DebitNoteItemApiController::class, 'store'])->name('debit-note-items.store');
    Route::get('/debit-note-items/create-data', [DebitNoteItemApiController::class, 'getCreateData'])->name('debit-note-items.create-data');
    Route::get('/get-invoice-details/{invoice_number}', [DebitNoteItemApiController::class, 'getInvoiceDetails'])->name('get-invoice-details');
    Route::get('/get-order-details/{order_number}', [DebitNoteItemApiController::class, 'getOrderDetails'])->name('get-order-details');
    Route::get('/debit-note-items/{id}', [DebitNoteItemApiController::class, 'show']);
    Route::post('/debit-note-items/update/{id}', [DebitNoteItemApiController::class, 'update']);
    Route::post('/debit-note-items/delete/{id}', [DebitNoteItemApiController::class, 'destroy']);





    Route::get('/general-settings', [GeneralSettingController::class, 'show'])->name('general-settings.show');
    Route::post('/general-settings/update', [GeneralSettingController::class, 'update'])->name('general-settings.update');
    Route::post('/general-settings/delivery-terms', [GeneralSettingController::class, 'updateDeliveryTerms'])->name('general-settings.delivery-terms');
    Route::post('/company-rules/update', [GeneralSettingController::class, 'updateCompanyRules'])->name('general-company-settings.update');
    Route::post('/dashboard-settings/update', [GeneralSettingController::class, 'updateDashboardSettings'])->name('general-dashboard-settings.update');
    Route::post('/eway-bill-settings/update', [GeneralSettingController::class, 'updateEwayBillSettings'])->name('eway-bill-settings.update');
    Route::post('/einvoice-settings/update', [GeneralSettingController::class, 'updateEinvoiceSettings'])->name('einvoice-settings.update');
    Route::post('/sales-settings/update', [GeneralSettingController::class, 'updateSalesSettings'])->name('sales-settings.update');

    Route::post('/connect-device', [ConnectedDevicesController::class, 'connect']);
    Route::get('/check-device/{code}', [ConnectedDevicesController::class, 'check']);
    Route::get('/disconnect-device/{code}', [ConnectedDevicesController::class, 'disconnect']);
    Route::post('/submit-device-scan', [ConnectedDevicesController::class, 'submitScan']);
     Route::get('/smtp-settings', [SmtpSettingController::class, 'show'])->name('smtp-settings.show');
    Route::post('/smtp-settings/update', [SmtpSettingController::class, 'update'])->name('smtp-settings.update');
    // Route::get('/generate-device-code', [ConnectedDevicesController::class, 'generateCode']);
    // Route::post('/connect-device', [ConnectedDevicesController::class, 'connect']);
    // Route::get('/check-device/{code}', [ConnectedDevicesController::class, 'check']);
    // Route::get('/disconnect-device/{code}', [ConnectedDevicesController::class, 'disconnect']);
    // Route::get('/get-session-device', [ConnectedDevicesController::class, 'getSessionDevice']);

     Route::get   ('/notifications',                [NotificationController::class, 'index']);
    Route::post  ('/notifications/read-all',       [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/delete-all',     [NotificationController::class, 'deleteAll']);
    Route::post  ('/notifications/{id}/read',      [NotificationController::class, 'markAsRead']);
    Route::delete('/notifications/{id}/delete',    [NotificationController::class, 'delete']);

    Route::get('/facebook-app-configurations', [FacebookAppConfigurationController::class, 'index'])->name('facebook-app-configurations.index');
    Route::post('/facebook-app-configurations', [FacebookAppConfigurationController::class, 'store'])->name('facebook-app-configurations.store');
    // Specific routes must come before parameterized routes
    Route::get('/facebook-app-configurations/message-templates', [FacebookAppConfigurationController::class, 'getMessageTemplates'])->name('facebook-app-configurations.message-templates');
    Route::post('/facebook-app-configurations/store-templates', [FacebookAppConfigurationController::class, 'storeTemplates'])->name('facebook-app-configurations.store-templates');
    Route::get('/facebook-app-configurations/stored-templates', [FacebookAppConfigurationController::class, 'getStoredTemplates'])->name('facebook-app-configurations.stored-templates');
    Route::put('/facebook-app-configurations/templates/{templateId}/toggle-status', [FacebookAppConfigurationController::class, 'toggleTemplateStatus'])->name('facebook-app-configurations.toggle-template-status');
    Route::put('/facebook-app-configurations/templates/{templateId}/update-use-for', [FacebookAppConfigurationController::class, 'updateTemplateUseFor'])->name('facebook-app-configurations.update-template-use-for');
    // Parameterized routes come after specific routes
    Route::get('/facebook-app-configurations/{id}', [FacebookAppConfigurationController::class, 'show'])->name('facebook-app-configurations.show');
    Route::put('/facebook-app-configurations/{id}', [FacebookAppConfigurationController::class, 'update'])->name('facebook-app-configurations.update');
    Route::delete('/facebook-app-configurations/{id}', [FacebookAppConfigurationController::class, 'destroy'])->name('facebook-app-configurations.destroy');

    // bankbook
    Route::get('/bankbook', [TransactionApiController::class, 'bankBook'])->name('bankbook');
    Route::post('/bankbook/manual-entry', [TransactionApiController::class, 'storeManualBankBookEntry'])->name('bankbook.manual-entry.store');
    Route::get('/export-bankbook-pdf', [TransactionApiController::class, 'export_bankbook_pdf']);
    Route::get('/export-bankbook-excel', [TransactionApiController::class, 'export_bankbook_excel']);
     Route::get('/bankbook-payment/{id}', [TransactionApiController::class, 'getPayment'])->name('bankbook.payment.show');
    Route::post('/bankbook-payment/{id}/update', [TransactionApiController::class, 'updatePayment'])->name('bankbook.payment.update');
    Route::post('/bankbook-payment/{id}/delete', [TransactionApiController::class, 'deletePayment'])->name('bankbook.payment.delete');


    Route::post('/check-bill-no-unique', [PurchaseController::class, 'checkBillNoUnique'])->name('check_bill_no_unique');

    // Route::post('/purchase_order', [PurchaseController::class, 'purchase_order'])->name('purchase_order');
    // Route::get('/purchase_list', [PurchaseController::class, 'purchase_list'])->name('purchase_list');
    // Route::post('/purchase_delete', [PurchaseController::class, 'purchase_delete'])->name('purchase_delete');
    // Route::get('/purchase_get/{id}', [PurchaseController::class, 'showPurchase'])->name('purchase_get');
    // Route::post('/purchase_update/{id}', [PurchaseController::class, 'purchase_update'])->name('purchase_update');
    // Route::get('/purchase-report-data', [PurchaseController::class, 'fetch_purchase_report']);
    // Route::post('/make-payment-purchase', [PurchaseController::class, 'make_payment'])->name('make-payment.purchase');
    // Route::get('/purchase/payment-history/{id}', [PurchaseController::class, 'getHistory1'])->name('purchase.payment_history');
    // Route::get('/purchase/payment-history-item/{id}', [PurchaseController::class, 'getPaymentHistoryItem'])->name('purchase.payment_history_item');
    // Route::post('/purchase/payment-history/{id}/update', [PurchaseController::class, 'updatePaymentHistoryItem'])->name('purchase.payment_history.update');
    // Route::post('/purchase/payment-history/{id}/delete', [PurchaseController::class, 'deletePaymentHistoryItem'])->name('purchase.payment_history.delete');
    // Route::post('/purchase-export-excel', [PurchaseController::class, 'export_purchase_excel']);
    // Route::get('/export-purchase', [PurchaseController::class, 'export_purchase']);
    // Route::get('/export-purchase-pdf', [PurchaseController::class, 'export_purchase_pdf']);
    // Route::post('/purchases/report/export-pdf-api', [PurchaseController::class, 'export_purchases_report_pdf_api'])->name('purchase.reportapi.exportPdf');
    // Route::post('/sales/report/export-pdf-api', [SalesController::class, 'export_sales_report_pdf_api'])->name('sale.reportapi.exportPdf');
    // Route::post('/expense/report/export-pdf-api', [ExpenseController::class, 'expense_report_pdf_api'])->name('expense.reportapi.exportPdf');
    // Route::get('/export-invoice-pdf-api', [CustomInvoiceController::class, 'exportInvoicePDFAPI'])->name('custom_invoice.pdfapi.export');
    // Route::get('/export-invoice-excel-api', [CustomInvoiceController::class, 'exportCustom_invoice_api'])->name('custom_invoice.export');
     Route::post('/purchase_order', [PurchaseController::class, 'purchase_order'])->name('purchase_order');
    Route::post('/row-material-purchase_order', [PurchaseController::class, 'row_material_purchase_order'])->name('row_material_purchase_order');
    Route::get('/row-material-purchase-list', [PurchaseController::class, 'row_material_purchase_list'])->name('row_material_purchase_list');
    Route::get('/row-material-purchase/{id}', [PurchaseController::class, 'showRowMaterialPurchase'])->name('row_material_purchase_get');
    Route::put('/row-material-purchase/{id}', [PurchaseController::class, 'updateRowMaterialPurchase'])->name('row_material_purchase_update');
    Route::delete('/row-material-purchase/{id}', [PurchaseController::class, 'deleteRowMaterialPurchase'])->name('row_material_purchase_delete');
    Route::get('/purchase_list', [PurchaseController::class, 'purchase_list'])->name('purchase_list');
    Route::post('/purchase_delete', [PurchaseController::class, 'purchase_delete'])->name('purchase_delete');
    Route::get('/purchase_get/{id}', [PurchaseController::class, 'showPurchase'])->name('purchase_get');
    Route::post('/purchase_update/{id}', [PurchaseController::class, 'purchase_update'])->name('purchase_update');
    Route::get('/purchase-report-data', [PurchaseController::class, 'fetch_purchase_report']);
    Route::post('/make-payment-purchase', [PurchaseController::class, 'make_payment'])->name('make-payment.purchase');
    Route::get('/purchase/payment-history/{id}', [PurchaseController::class, 'getHistory1'])->name('purchase.payment_history');
    Route::post('/purchase-export-excel', [PurchaseController::class, 'export_purchase_excel']);
    Route::get('/export-purchase', [PurchaseController::class, 'export_purchase']);
    Route::get('/export-purchase-pdf', [PurchaseController::class, 'export_purchase_pdf']);
    Route::post('/purchases/report/export-pdf-api', [PurchaseController::class, 'export_purchases_report_pdf_api'])->name('purchase.reportapi.exportPdf');
    Route::post('/sales/report/export-pdf-api', [SalesController::class, 'export_sales_report_pdf_api'])->name('sale.reportapi.exportPdf');
    // Route::post('/expense/report/export-pdf-api', [ExpenseController::class, 'expense_report_pdf_api'])->name('expense.reportapi.exportPdf');
    Route::post('/expense/report/export-pdf-api', [ExpenseController::class, 'expense_report_pdf_api'])->name('expense.reportapi.exportPdf');
    Route::get('/export-invoice-pdf-api', [CustomInvoiceController::class, 'exportInvoicePDFAPI'])->name('custom_invoice.pdfapi.export');
    Route::get('/export-invoice-excel-api', [CustomInvoiceController::class, 'exportCustom_invoice_api'])->name('custom_invoice.export');

    Route::post('deleteBrand/{id}', [BrandController::class, 'deleteBrand'])->name('deleteBrand');
    Route::post('updateBrand', [BrandController::class, 'updateBrand'])->name('updateBrand');

    Route::get('getProductsByCategory/{id}', [SalesController::class, 'getProductsByCategory'])->name('getProductsByCategory');
    Route::get('get-available-serials/{productId}', [SalesController::class, 'getAvailableSerials']);

    Route::get('/product-view/{id}', [ProductController::class, 'getProductDetails']);
    // Route::get('/edit_product/{id}', [ProductController::class, 'getProductedit']);
    Route::get('/products/{id}/variants', [ProductController::class, 'getVariants']);

    Route::post('order_sale', [SalesController::class, 'order_sale'])->name('order_sale');
    Route::get('customer-deposit-balance/{customerId}', [SalesController::class, 'customerDepositBalance'])->name('customer.deposit.balance');
    Route::post('import-sales-list', [SalesController::class, 'importSalesList'])->name('importSalesList');
    Route::post('import-purchase-list', [PurchaseController::class, 'importPurchaseList'])->name('importPurchaseList');
    Route::get('get_orders', [SalesController::class, 'get_orders'])->name('get_orders');
    Route::get('getsalseById/{id}', [SalesController::class, 'getsalseById'])->name('getsalseById');
    Route::post('delete/{id}', [SalesController::class, 'delete'])->name('delete');
    Route::post('update_sale', [SalesController::class, 'update_sale'])->name('update_sale');
    Route::post('convert-quotation-to-sale/{id}', [SalesController::class, 'convertQuotationToSale'])->name('convert_quotation_to_sale');
    Route::post('/orders/{id}/assign-staff', [SalesController::class, 'assignStaff'])->name('orders.assign_staff');
    Route::post('/orders/{id}/order-type', [SalesController::class, 'updateOrderType'])->name('orders.update_order_type');
    Route::post('/orders/{id}/upload-qr', [SalesController::class, 'uploadQrCode'])->name('orders.upload_qr');
    Route::get('/orders/filter', [SalesController::class, 'getFilteredOrders']);
    Route::get('/order/payment-history/{id}', [SalesController::class, 'getHistory1'])->name('order.payment_history');
    Route::get('/pdf-orders', [SalesController::class, 'exportOrdersPDF'])->name('pdf.export');
    Route::get('/export-order', [SalesController::class, 'exportOrders']);
    Route::get('/product-by-barcode/{barcode}', [SalesController::class, 'getByBarcode'])->name('getByBarcode');
    Route::post('check-rental-availability', [SalesController::class, 'checkRentalAvailabilityApi']);
    Route::get('/purchase-product-chart', [PurchaseController::class, 'purchaseProductChart']);


    Route::post('/expenses/store', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('/expenses/list', [ExpenseController::class, 'getExpenses'])->name('expenses.list');
    Route::post('/expenses/{id}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
    Route::get('/expenses/{id}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
    Route::put('/expenses/{id}', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::get('/expenses/report', [ExpenseController::class, 'getExpensesReport']);

    // Expense-Type
    Route::post('/expense-type/store', [ExpenseTypeController::class, 'store'])->name('expense-type.store');
    Route::get('/expense-types', [ExpenseTypeController::class, 'list']);
    Route::get('/expense-types/{id}', [ExpenseTypeController::class, 'show']);
    Route::put('/expense-types/{id}', [ExpenseTypeController::class, 'update']);
    Route::post('/expenses-type/{id}', [ExpenseTypeController::class, 'destroy'])->name('expenses-type.destroy');

    Route::get('/getSaleDetails/{id}', [SaleReturnController::class, 'getSaleDetails'])->name('getSaleDetails')->where('id', '.*');
    Route::get('/sales-return-list', [SaleReturnController::class, 'salesReturnList'])->name('sales_return_list');
    Route::post('/return_sale', [SaleReturnController::class, 'return_sale'])->name('return_sale');

    Route::get('/getRentalDetails/{id}', [RentalReturnController::class, 'getRentalDetails'])->name('getRentalDetails')->where('id', '.*');
    Route::post('/return_rental', [RentalReturnController::class, 'return_rental'])->name('return_rental');
    Route::get('/rental-return-list', [RentalReturnController::class, 'rentalReturnList'])->name('rental_return_list');


    // GST / GSTR-3B report endpoint
    Route::get('/gstr-3b', [GstController::class, 'gstr3b']);

    Route::post('/customer_invoice/store', [CustomInvoiceController::class, 'store'])->name('/customer_invoice/store');
    Route::get('/custom_invoice_list', [CustomInvoiceController::class, 'list'])->name('/customer_invoice/list');
    Route::post('/custom_invoice_delete', [CustomInvoiceController::class, 'delete'])->name('/customer_invoice/delete');
    Route::get('/edit-invoice/{id}', [CustomInvoiceController::class, 'getInvoiceData']);
    Route::post('/custom-invoice/make-payment', [CustomInvoiceController::class, 'makePaymentSubmit'])->name('custom_invoice.make_payment_submit');

    Route::get('/custom_invoice_get/{id}', [CustomInvoiceController::class, 'get'])->name('/customer_invoice/get');
    Route::post('/custom_invoice_update/{id}', [CustomInvoiceController::class, 'update'])->name('/customer_invoice/update');
    Route::get('/custom_invoice/payment-history/{id}', [CustomInvoiceController::class, 'getHistory1'])->name('custom_invoice.payment_history');

    Route::post('createStaff', [StaffController::class, 'createStaff'])->name('createCustomer');
    Route::post('deleteStaff/{num:}', [StaffController::class, 'deleteStaff'])->name('deleteCustomer');
    Route::get('/getStaff/{id}', [StaffController::class, 'getStaff'])->name('getCustomer');
    Route::post('/updateStaff/{id}', [StaffController::class, 'updateStaff'])->name('updateCustomer');
    Route::get('getAllStaff', [StaffController::class, 'getAllStaff'])->name('getAllCustomer');
    // Route::get('/customer-view/{id}/data', [CustomerController::class, 'getCustomerData']);
    Route::get('staff/{id}', [StaffController::class, 'getStaffProfile']);
    Route::get('modules', [StaffController::class, 'index']);
    Route::post('/staff/register-face', [StaffController::class, 'registerFace'])->name('staff.register-face');

    // TDS API (separate module)
    Route::post('/tds/store', [TdsController::class, 'store']);
    Route::get('/tds/list', [TdsController::class, 'list']);
    Route::get('/tds/export', [TdsController::class, 'export']);

    Route::post('createSubbranch', [SubBranchController::class, 'createSubbranch'])->name('createSubbranch');
    Route::post('deleteSubbranch/{num:}', [SubBranchController::class, 'deleteSubbranch'])->name('deleteSubbranch');
    Route::get('/getSubbranch/{id}', [SubBranchController::class, 'getSubbranch'])->name('getSubbranch');
    Route::post('/updateSubbranch', [SubBranchController::class, 'updateSubbranch'])->name('updateSubbranch');
    Route::get('getAllSubbranch', [SubBranchController::class, 'getAllSubbranch'])->name('getAllSubbranch');
    // Route::get('/customer-view/{id}/data', [CustomerController::class, 'getCustomerData']);
    Route::get('subbranch/{id}', [SubBranchController::class, 'getSubbranchProfile']);

    Route::get('/account-ledger', [AccountLedgerController::class, 'getAccountLedger'])->name('account_ledger.list');
    Route::get('/get-payment-details', [AccountLedgerController::class, 'getPaymentDetails'])->name('get.payment.details');
    Route::get('/export-account-ledger', [AccountLedgerController::class, 'exportAccountLedgerExcel']);
    Route::post('/account-ledger/download-pdf', [AccountLedgerController::class, 'downloadAccountLedgerPDF']);


    Route::get('/inventory-list', [InventoryController::class, 'index'])->name('inventory.list');
    Route::get('/products_edit_inventroy/{id}', [InventoryController::class, 'products_edit_inventroy'])->name('inventory.edit');
    Route::post('/inventory_update/{id}', [InventoryController::class, 'inventory_update'])->name('inventory.update');
    Route::post('/inventory_transfer/{id}', [InventoryController::class, 'transfer'])->name('inventory.transfer');
    Route::get('/getQuantityHistory_inventory/{id}', [InventoryController::class, 'getQuantityHistory_inventory'])->name('inventory.getQuantityHistory_inventory');

    Route::get('/material-inventory-list', [InventoryController::class, 'materialIndex'])->name('material-inventory.list');
    Route::get('/material_edit_inventory/{id}', [InventoryController::class, 'materialEditInventory'])->name('material-inventory.edit');
    Route::post('/material_inventory_update/{id}', [InventoryController::class, 'materialInventoryUpdate'])->name('material-inventory.update');
    Route::get('/getQuantityHistory_material/{id}', [InventoryController::class, 'getQuantityHistoryMaterial'])->name('material-inventory.getQuantityHistory_material');

    Route::get('/exchange-inventory-list', [InventoryController::class, 'exchangeIndex'])->name('api.exchange-inventory.list');

    Route::get('/products/filter', [InventoryController::class, 'filter']);

    Route::get('/sales/payment-history/{id}', [SalesController::class, 'getHistory'])->name('sales.payment_history');
    Route::get('/sales/pending-emis', [SalesController::class, 'getPendingEmis'])->name('api.sales.pending_emis');
    Route::post('/sales/make-payment', [SalesController::class, 'makePaymentSubmit'])->name('sales.make_payment_submit');


    // Salary
    Route::get('/salaries', [SalaryController::class, 'index']);
    Route::post('/salaries/pay', [SalaryController::class, 'store']);
    Route::put('/salaries/update/{id}', [SalaryController::class, 'update']);
    Route::get('/salaries/years', [SalaryController::class, 'getSalaryYears']);
    Route::get('/staff', [SalaryController::class, 'getActiveStaff']);
    Route::get('/salaries/export', [SalaryController::class, 'export']);
    Route::get('/salaries/pdf', [SalaryController::class, 'generatePDF']);
    // Route::get('/salaries/staff/pdf', [SalaryController::class, 'generateStaffPDF']);
    Route::get('/salaries/staff/pdf', [SalaryController::class, 'generateStaffPDF'])->name('salary.staff_pdf');

    Route::get('/api-attendance', [AttendanceController::class, 'index']);

    //advance pay
    Route::post('/advance-payments/{id}', [AdvancePaymentController::class, 'update']);

        //Labour Item
    Route::get('/get-all-labour-items', [LabourItemController::class, 'getAllLabourItems']);
    Route::post('/add-labour-item', [LabourItemController::class, 'addLabourItem']);
    Route::post('/update-labour-item/{id}', [LabourItemController::class, 'updateLabourItem']);
    Route::post('/delete-labour-item/{id}', [LabourItemController::class, 'deleteLabourItem']);

    Route::apiResource('advance-payments', AdvancePaymentController::class);
    // routes/api.php
    Route::get('/advance-payment-filters', [AdvancePaymentController::class, 'getFilters']);
    Route::get('/export-advance-payments', [AdvancePaymentController::class, 'exportAdvancePayments'])
        ->name('export.advance-payments');
    Route::get('/advance-history', [SalaryController::class, 'getAdvanceHistory']);


    // Row Material
    Route::get('/get-row-materials', [RowMaterialController::class, 'getAllRowMaterials'])->name('getAllRowMaterial');
    Route::post('/create-row-material', [RowMaterialController::class, 'createRowMaterial'])->name('createRowMaterial');
    Route::post('/update-row-material', [RowMaterialController::class, 'updateRowMaterial'])->name('updateRowMaterial');
    Route::post('/update_row_material', [RowMaterialController::class, 'updateRowMaterial']); // Alias for blade compatibility
    Route::post('/delete-row-material/{id}', [RowMaterialController::class, 'deleteRowMaterial'])->name('deleteRowMaterial');
    Route::get('/export-row-material', [RowMaterialController::class, 'export_row_material']);
    Route::get('/export-row-material-pdf', [RowMaterialController::class, 'export_row_material_pdf']);
    Route::get('/edit-row-material/{id}', [RowMaterialController::class, 'edit_row_material']);
    Route::get('/edit_row_material/{id}', [RowMaterialController::class, 'edit_row_material']); // Alias for blade compatibility
    Route::get('/rowmaterial-view/{id}', [RowMaterialController::class, 'getRowMaterial']); // For detail view

    // Type Master & Material Master
    Route::apiResource('types', TypeController::class);
    Route::apiResource('materials', MaterialController::class);

    //banks


    Route::get('/all-banks', [BankMasterController::class, 'index'])->name('banks.index');
    Route::get('/banks/create', [BankMasterController::class, 'create'])->name('banks.create');
    Route::get('/banks/data', [BankMasterController::class, 'getData'])->name('banks.data');
    Route::post('/banks', [BankMasterController::class, 'store'])->name('banks.store');
    Route::get('/banks/{id}', [BankMasterController::class, 'show'])->name('banks.show');
    Route::get('/banks/{id}/edit', [BankMasterController::class, 'edit'])->name('banks.edit');
    Route::put('/banks/{id}', [BankMasterController::class, 'update'])->name('banks.update');
    Route::delete('/delete/banks/{id}', [BankMasterController::class, 'destroy'])->name('banks.destroy');

    Route::get('/cashbook/data', [TransactionController::class, 'getCashbookData'])->name('cashbook.data');
    Route::get('/export-cashbook-pdf', [TransactionController::class, 'exportCashbookPdf'])->name('cashbook.export_pdf');
    Route::get('/export-cashbook-excel', [TransactionController::class, 'exportCashbookExcel']);
    Route::get('/payment-store/{id}', [TransactionController::class, 'getPayment'])->name('payment.show');
    Route::post('/payment-store/{id}/update', [TransactionController::class, 'updatePayment'])->name('payment.update');
    Route::post('/payment-store/{id}/delete', [TransactionController::class, 'deletePayment'])->name('payment.delete');

    Route::get('/appointments', [AppointmentController::class, 'getAllAppointments']);
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::get('/appointments/{id}', [AppointmentController::class, 'show']);
    Route::post('/appointments/{id}', [AppointmentController::class, 'update']);
    Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy']);
    Route::get('/export-appointments', [AppointmentController::class, 'exportAppointments']);

    Route::post('/attendance/store', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/filter', [AttendanceController::class, 'filter'])->name('attendance.filter');
    Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');
    Route::post('/attendance/bulk-store', [AttendanceController::class, 'bulkStore'])->name('attendance.bulk-store');
    Route::get('/get-employees', [AttendanceeController::class, 'getEmployees']);
    Route::get('/attendance/absent-reason', [AttendanceeController::class, 'getAbsentReason']);
    Route::post('/attendance/update-reason', [AttendanceeController::class, 'updateStatus']);
    Route::post('/attendance/checkin', [AttendanceeController::class, 'checkIn']);
    Route::post('/attendance/checkout', [AttendanceeController::class, 'checkOut']);
    // Alias route: handles both check-in and checkout depending on 'action' in the body
    Route::post('/attendance/check-in', function (\Illuminate\Http\Request $request) {
        $action = $request->input('action', 'checkin');
        $controller = app(\App\Http\Controllers\admin\AttendanceeController::class);
        if ($action === 'checkout') {
            return $controller->checkOut();
        }
        return $controller->checkIn();
    });
    Route::get('/attendance/status', [AttendanceeController::class, 'getStatus']);
    Route::get('/attendance/getAttendanceData', [AttendanceeController::class, 'getAttendanceData']);
    Route::get('/attendance/getAttendance/{month}/{year}', [AttendanceeController::class, 'getAttendance'])
        ->whereNumber('month')
        ->whereNumber('year');
    Route::get('/dashboard-stats', [AttendanceeController::class, 'getDashboardStats']);
    Route::get('/leave/getLeaveDetails/{id}', [AttendanceeController::class, 'getLeaveDetails'])->whereNumber('id');
    Route::get('/attendance/get-face-photo', [AttendanceeController::class, 'getFacePhoto']);
    Route::post('/attendance/face-checkin', [AttendanceeController::class, 'faceCheckIn']);
    Route::delete('/attendance/delete-today', [AttendanceeController::class, 'deleteTodayAttendance']);
    Route::get('/attendance/day-records', [AttendanceeController::class, 'getDayAttendanceRecords']);
    Route::post('/attendance/update-day-records', [AttendanceeController::class, 'updateDayAttendanceRecords']);
    Route::post('/attendance/bulk-update', [AttendanceeController::class, 'bulkUpdateAttendanceRecords']);

    Route::post('/attendance/store', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/filter', [AttendanceController::class, 'filter'])->name('attendance.filter');
    Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');
    Route::post('/attendance/bulk-store', [AttendanceController::class, 'bulkStore'])->name('attendance.bulk-store');

    Route::get('/gst/gstr-3b/export', [GstController::class, 'exportGstr3b'])->name('gst.gstr3b.export');
    Route::get('/gstr3b/export', [GstSalesReportController::class, 'exportGstr3b']);

    Route::post('/add-units', [ApiUnitController::class, 'store'])->name('units.store');
    Route::get('/units', [ApiUnitController::class, 'index'])->name('units.index');
    Route::post('/update-unit/{id}', [ApiUnitController::class, 'update'])->name('units.update');
    Route::delete('/delete-unit/{id}', [ApiUnitController::class, 'destroy'])->name('units.destroy');

    // ========== HR MODULE - AttendanceeController routes ==========
    Route::get('/get-employees', [AttendanceeController::class, 'getEmployees']);
    Route::get('/attendance/absent-reason', [AttendanceeController::class, 'getAbsentReason']);
    Route::post('/attendance/update-reason', [AttendanceeController::class, 'updateStatus']);
    Route::post('/attendance/checkin', [AttendanceeController::class, 'checkIn']);
    Route::post('/attendance/checkout', [AttendanceeController::class, 'checkOut']);
    // Alias route: handles both check-in and checkout depending on 'action' in the body
    Route::post('/attendance/check-in', function (\Illuminate\Http\Request $request) {
        $action = $request->input('action', 'checkin');
        $controller = app(\App\Http\Controllers\admin\AttendanceeController::class);
        if ($action === 'checkout') {
            return $controller->checkOut();
        }
        return $controller->checkIn();
    });
    Route::get('/attendance/status', [AttendanceeController::class, 'getStatus']);
    Route::get('/attendance/getAttendanceData', [AttendanceeController::class, 'getAttendanceData']);
    Route::get('/attendance/getAttendance/{month}/{year}', [AttendanceeController::class, 'getAttendance'])
        ->whereNumber('month')
        ->whereNumber('year');
    Route::get('/dashboard-stats', [AttendanceeController::class, 'getDashboardStats']);
    Route::get('/leave/getLeaveDetails/{id}', [AttendanceeController::class, 'getLeaveDetails'])->whereNumber('id');
    Route::get('/attendance/get-face-photo', [AttendanceeController::class, 'getFacePhoto']);
    Route::post('/attendance/face-checkin', [AttendanceeController::class, 'faceCheckIn']);
    Route::delete('/attendance/delete-today', [AttendanceeController::class, 'deleteTodayAttendance']);
    Route::get('/attendance/day-records', [AttendanceeController::class, 'getDayAttendanceRecords']);
    Route::post('/attendance/update-day-records', [AttendanceeController::class, 'updateDayAttendanceRecords']);
    Route::post('/attendance/bulk-update', [AttendanceeController::class, 'bulkUpdateAttendanceRecords']);

    // Company Rules API
    Route::get('/company-rules/rules', [AdminCompanyRulesController::class, 'rules'])->name('company-rules.rules');
    Route::post('/company-rules/store', [AdminCompanyRulesController::class, 'store'])->name('company-rules.store');
    Route::get('/rules_get', [AdminCompanyRulesController::class, 'rules_get'])->name('company-rules.rules_get');
    Route::get('/company-rules/edit/{id}', [AdminCompanyRulesController::class, 'edit'])->name('company-rules.edit');
    Route::post('/company-rules/update/{id}', [AdminCompanyRulesController::class, 'update'])->name('company-rules.update');
    Route::delete('/rules/{id}', [AdminCompanyRulesController::class, 'delete'])->name('company-rules.delete');
    Route::post('/company-rules/calculate-salary', [AdminCompanyRulesController::class, 'calculateSalary'])->name('company-rules.calculate-salary');
    Route::post('/company-rules/validate-attendance', [AdminCompanyRulesController::class, 'validateAttendance'])->name('company-rules.validate-attendance');

    // Department APIs
    Route::get('/department', [AdminDepartmentController::class, 'index']);
    Route::post('/department', [AdminDepartmentController::class, 'store']);
    Route::post('/department/add', [AdminDepartmentController::class, 'quickStore']);
    Route::get('/department/{id}', [AdminDepartmentController::class, 'show']);
    Route::match(['post', 'put', 'patch'], '/department/{id}', [AdminDepartmentController::class, 'update']);
    Route::delete('/department/{id}', [AdminDepartmentController::class, 'destroy']);
    Route::get('/departments', [AdminDepartmentController::class, 'all']);
    Route::get('/getdepartments', [AdminDepartmentController::class, 'all']);

    // Designation APIs
    Route::get('/designation', [AdminDesignationController::class, 'index']);
    Route::post('/designation', [AdminDesignationController::class, 'store']);
    Route::post('/designation/add', [AdminDesignationController::class, 'quickStore']);
    Route::get('/designation/{id}', [AdminDesignationController::class, 'show']);
    Route::match(['post', 'put', 'patch'], '/designation/{id}', [AdminDesignationController::class, 'update']);
    Route::delete('/designation/{id}', [AdminDesignationController::class, 'destroy']);
    Route::get('/designations', [AdminDesignationController::class, 'all']);

    // Holiday APIs (with compatibility aliases used by old frontend scripts)
    Route::get('/get_holidays', [AdminHolidaysController::class, 'index']);
    Route::get('/holidays', [AdminHolidaysController::class, 'index']);
    Route::post('/holidays/store', [AdminHolidaysController::class, 'store']);
    Route::post('/holioday/store', [AdminHolidaysController::class, 'store']);
    Route::get('/holidays/edit_holiday/{id}', [AdminHolidaysController::class, 'editHoliday']);
    Route::post('/holidays/update', [AdminHolidaysController::class, 'update']);
    Route::get('/holidays/{id}', [AdminHolidaysController::class, 'show']);
    Route::match(['put', 'patch'], '/holidays/{id}', [AdminHolidaysController::class, 'update']);
    Route::delete('/holidays/{id}', [AdminHolidaysController::class, 'destroy']);
    Route::delete('/holidays/delete/{id}', [AdminHolidaysController::class, 'destroy']);

    // Leave Type APIs
    Route::get('/leavetype', [AdminLeaveTypeController::class, 'index']);
    Route::post('/leavetype', [AdminLeaveTypeController::class, 'store']);
    Route::post('/leavetype/add', [AdminLeaveTypeController::class, 'quickStore']);
    Route::get('/leavetype/{id}', [AdminLeaveTypeController::class, 'show']);
    Route::match(['post', 'put', 'patch'], '/leavetype/{id}', [AdminLeaveTypeController::class, 'update']);
    Route::delete('/leavetype/{id}', [AdminLeaveTypeController::class, 'destroy']);

    // Leave APIs (CodeIgniter compatibility + Laravel routes)
    Route::get('/leave', [AdminLeaveController::class, 'index']);
    Route::post('/leave', [AdminLeaveController::class, 'store']);
    Route::post('/leave/update/{leaveId}', [AdminLeaveController::class, 'updateStatus'])->whereNumber('leaveId');
    Route::get('/getEmployees', [AdminLeaveController::class, 'getEmployees']);
    Route::get('/hr/dashboard-data', [HrDashboardController::class, 'getDashboardData']);

    // Staff register face (added in source)
    Route::post('/staff/register-face', [StaffController::class, 'registerFace'])->name('staff.register-face');

    // Tickets API
    Route::get('/tickets',                [TicketApiController::class, 'index']);
    Route::get('/tickets/export-excel',   [TicketApiController::class, 'exportExcel']);
    Route::get('/tickets/export-pdf',     [TicketApiController::class, 'exportPdf']);
    Route::post('/tickets/{id}/delete',   [TicketApiController::class, 'destroy']);
      // Plans CRUD
    Route::get('/getAllPlans',[PlanController::class, 'getAllPlans'])->name('plans.getAll');
    Route::get('/getPlanById/{id}',[PlanController::class, 'getPlanById'])->name('plans.getById');
    Route::post('/createPlan',[PlanController::class, 'createPlan'])->name('plans.create');
    Route::post('/updatePlan',[PlanController::class, 'updatePlan'])->name('plans.update');
    Route::post('/deletePlan/{id}',[PlanController::class, 'deletePlan'])->name('plans.delete');
    Route::post('/togglePlanStatus/{id}',[PlanController::class, 'togglePlanStatus'])->name('plans.toggleStatus');


    // E-Way Bill and E-Invoice Authenticated Routes
    Route::post('/eway-bill/generate', [\App\Http\Controllers\api\EwayBillController::class, 'generate']);
    Route::get('/eway-bill/get', [\App\Http\Controllers\api\EwayBillController::class, 'getEwayBill']);
    Route::post('/eway-bill/cancel', [\App\Http\Controllers\api\EwayBillController::class, 'cancelEwayBill']);

    Route::post('/einvoice/generate', [\App\Http\Controllers\api\EinvoiceController::class, 'generate']);
    Route::get('/einvoice/get', [\App\Http\Controllers\api\EinvoiceController::class, 'getEinvoice']);
    Route::post('/einvoice/cancel', [\App\Http\Controllers\api\EinvoiceController::class, 'cancelEinvoice']);

    // ===================== SYNC API ROUTES =====================
    Route::prefix('sync')->group(function () {
        Route::post('/full-push', [\App\Http\Controllers\api\SyncController::class, 'fullPush']);
        Route::get('/full-pull', [\App\Http\Controllers\api\SyncController::class, 'fullPull']);
        Route::post('/images-batch', [\App\Http\Controllers\api\SyncController::class, 'imagesBatch']);
    });

});

    // Follow Up API Routes
    Route::get('/getAllFollowUps', [FollowUpController::class, 'index'])->name('followup.index');
    Route::post('/follow-up/store', [FollowUpController::class, 'store'])->name('followup.store.api');
    Route::put('/follow-up/{id}/update', [FollowUpController::class, 'update'])->name('followup.update.api');
    Route::get('/follow-up/{id}/show', [FollowUpController::class, 'show'])->name('followup.show.api');
    Route::delete('/follow-up/{id}/delete', [FollowUpController::class, 'destroy'])->name('followup.delete.api');
    Route::get('/follow-up/customers', [FollowUpController::class, 'getCustomers'])->name('followup.customers.api');
    Route::get('/follow-up/staff', [FollowUpController::class, 'getStaff'])->name('followup.staff.api');

    // Leads API Routes
    Route::get('/getAllLeads', [LeadController::class, 'index'])->name('lead.index');
    Route::post('/lead/store', [LeadController::class, 'store'])->name('lead.store.api');
    Route::put('/lead/{id}/update', [LeadController::class, 'update'])->name('lead.update.api');
    Route::get('/lead/{id}/show', [LeadController::class, 'show'])->name('lead.show.api');
    Route::delete('/lead/{id}/delete', [LeadController::class, 'destroy'])->name('lead.delete.api');
    Route::post('/lead/{id}/convert-to-customer', [LeadController::class, 'convertToCustomer'])->name('lead.convert.customer.api');
    Route::get('/lead/customers', [LeadController::class, 'getCustomers'])->name('lead.customers.api');
    Route::get('/lead/staff', [LeadController::class, 'getStaff'])->name('lead.staff.api');
    Route::get('/lead/export-excel', [LeadController::class, 'exportExcel'])->name('lead.export.excel');
    Route::get('/lead/export-pdf', [LeadController::class, 'exportPdf'])->name('lead.export.pdf');

 // Meeting API Routes
    Route::get('/getAllMeetings', [MeetingController::class, 'index'])->name('meeting.index');
    Route::post('/meeting/store', [MeetingController::class, 'store'])->name('meeting.store.api');
    Route::put('/meeting/{id}/update', [MeetingController::class, 'update'])->name('meeting.update.api');
    Route::get('/meeting/{id}/show', [MeetingController::class, 'show'])->name('meeting.show.api');
    Route::delete('/meeting/{id}/delete', [MeetingController::class, 'destroy'])->name('meeting.delete.api');
    Route::get('/meeting/branches', [MeetingController::class, 'getBranches'])->name('meeting.branches.api');
    Route::get('/meeting/customers', [MeetingController::class, 'getCustomers'])->name('meeting.customers.api');
    Route::get('/meeting/staff', [MeetingController::class, 'getStaff'])->name('meeting.staff.api');



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

