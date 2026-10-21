<?php

use App\Http\Controllers\admin\AccountLedgerController;
use App\Http\Controllers\admin\AdvancePaymentController;
use App\Http\Controllers\admin\AppointmentController;
use App\Http\Controllers\admin\AttendanceController;
use App\Http\Controllers\admin\AttendanceeController;
use App\Http\Controllers\admin\CompanyRulesController;
use App\Http\Controllers\admin\DepartmentController;
use App\Http\Controllers\admin\DesignationController;
use App\Http\Controllers\admin\HolidaysController;
use App\Http\Controllers\admin\LeaveController;
use App\Http\Controllers\admin\LeaveTypeController;
use App\Http\Controllers\api\PayrollController as ApiPayrollController;
use App\Http\Controllers\admin\AuthController;
use App\Http\Controllers\api\ChangePasswordController;
use App\Http\Controllers\admin\BalanceSheetController;
use App\Http\Controllers\admin\BankMasterController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\ConnectedDevicesController;
use App\Http\Controllers\admin\CreditNoteItemController;
use App\Http\Controllers\admin\CreditNotesTypeController;
use App\Http\Controllers\admin\CustomerController;
use App\Http\Controllers\api\CustomerController as ApiCustomerController;
use App\Http\Controllers\admin\CustomInvoiceController;
use App\Http\Controllers\admin\InvoiceCustomizeController;
use App\Http\Controllers\admin\DebitNoteItemController;
use App\Http\Controllers\admin\ExpenseController;
use App\Http\Controllers\admin\ExpenseTypeController;
use App\Http\Controllers\admin\FollowUpController;
use App\Http\Controllers\admin\GstSalesReportController;
use App\Http\Controllers\admin\IncomeSheetController;
use App\Http\Controllers\admin\InventoryController;
use App\Http\Controllers\admin\InvoiceController;
use App\Http\Controllers\admin\LabourItemController;
use App\Http\Controllers\admin\MeetingController;
use App\Http\Controllers\admin\LeadController;
use App\Http\Controllers\admin\NotificationController;
use App\Http\Controllers\admin\PlanController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\api\ProductController as ApiProductController;
use App\Http\Controllers\admin\PurchaseController;
use App\Http\Controllers\admin\PurchaseReturnController;
use App\Http\Controllers\admin\QuotationController;
use App\Http\Controllers\admin\SalaryController;
use App\Http\Controllers\admin\TicketController;
use App\Http\Controllers\admin\TypeController;
use App\Http\Controllers\admin\MaterialController;
use App\Http\Controllers\admin\SalesController;
use App\Http\Controllers\admin\SalesPoolReportController;
use App\Http\Controllers\admin\SalesReturnController;
use App\Http\Controllers\admin\RentalReturnController;
use App\Http\Controllers\admin\PaymentInController;
use App\Http\Controllers\admin\SettingController;
use App\Http\Controllers\admin\StaffController;
use App\Http\Controllers\admin\SubBranchController;
use App\Http\Controllers\admin\TableTruncateController;
use App\Http\Controllers\admin\transactionController;
use App\Http\Controllers\admin\VendorController;
use App\Http\Controllers\admin\FinancerController;
use App\Http\Controllers\api\GstController;
use App\Http\Controllers\api\GstSalesReportController as ApiGstSalesReportController;
use App\Http\Controllers\api\LoginController;
use App\Http\Controllers\api\PurchaseController as ApiPurchaseController;
use App\Http\Controllers\api\SalesController as ApiSalesController;
use App\Http\Controllers\api\FinancerController as ApiFinancerController;
use App\Http\Controllers\BiometricPunchController;
use App\Http\Controllers\Gstr1ExportController;
use App\Http\Controllers\Gstr2ExportController;
use App\Http\Controllers\GSTR2gstController;
use App\Http\Controllers\GstWorkbookValidationController;
use App\Http\Controllers\admin\SalesReceiptPaymentController;
use App\Http\Controllers\Gstr3bExportController;
use App\Http\Controllers\PurchaseGSTR9CController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesGstr1ExportController;
use App\Http\Controllers\SalesGstr3bExportController;
use App\Http\Controllers\SalesGSTR9CController;
use App\Http\Controllers\SalesGSTR9Controller;
use App\Http\Controllers\SmtpSettingController;
use App\Http\Controllers\UnitController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\RowMaterialController;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\admin\ManufacturingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/barcode-designer', function () {
    return view('product.barcode_designer');
})->name('barcode.designer');

Route::get('/migrate/users', function () {
    Artisan::call('migrate --path=/database/migrations/2025_08_19_092634_add_gst_number_and_pan_number_to_users_table.php');
    return "Migration for users table run successfully.";
});
Route::get('/run-migrations', function () {
    return Illuminate\Support\Facades\Schema::getColumnListing('product_barcode_histories');
});

Route::post('/clear-subadmin-session', function () {
    session()->forget('selectedSubAdminId');
    return response()->json(['status' => 'cleared']);
});
Route::post('/set-subadmin-session', [AuthController::class, 'subadmin_session'])->name('subadmin_session');

Route::get('sales/report/view-page', [SalesController::class, 'show_sales_report_page']);
Route::get('purchase/report/view-page', [PurchaseController::class, 'show_purchase_report_page']);
Route::get('/expense/report/view-page', [ExpenseController::class, 'show_expense_report_page']);
Route::get('/sales/invoice/pdf/{id}', [SalesController::class, 'salse_invoice_pdf'])->name('sales.invoice.pdf');

use App\Http\Controllers\ChallanBillController;

// Biometric attendance — public page + device push endpoints (no login)
Route::get('/attendance/biometric-punches', [BiometricPunchController::class, 'publicPage'])->name('biometric.public');
Route::get('/attendance/biometric-punches/data', [BiometricPunchController::class, 'publicData'])->name('biometric.public.data');
Route::match(['get', 'post'], '/iclock/cdata', [BiometricPunchController::class, 'receivePush']);
Route::match(['get', 'post'], '/iclock/getrequest', [BiometricPunchController::class, 'heartbeat']);
Route::post('/attendance/biometric-sync', [BiometricPunchController::class, 'syncFromDevice'])->name('biometric.sync');
Route::post('/attendance/biometric-import-realtime', [BiometricPunchController::class, 'importRealtimeExport'])->name('biometric.import.realtime');
Route::post('/attendance/biometric-test-row', [BiometricPunchController::class, 'insertTestRow'])->name('biometric.test.row');
Route::post('/attendance/biometric-import-json', [BiometricPunchController::class, 'importJson'])->name('biometric.import.json');

Route::middleware(['guest:web'])->group(function () {
    Route::get('/', [AuthController::class, 'signin'])->name('auth.signin');
    // Route::get('/login', [LoginController::class, 'login'])->name('login');
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
        return "Database fixed from web!";
    });
});

Route::post('/', [LoginController::class, 'login'])->name('login');
Route::post('/auth/face-login', [LoginController::class, 'faceLogin'])->name('auth.face-login');

// QZ Tray print signing + trust install files
Route::get('/qz/override.crt', [\App\Http\Controllers\QzTrayController::class, 'overrideCrt'])->name('qz.override');
Route::get('/qz/install-qz-trust.bat', [\App\Http\Controllers\QzTrayController::class, 'installBat'])->name('qz.install-bat');

Route::middleware(['auth:web'])->group(function () {
    Route::get('/qz/certificate', [\App\Http\Controllers\QzTrayController::class, 'certificate'])->name('qz.certificate');
    Route::post('/qz/sign', [\App\Http\Controllers\QzTrayController::class, 'sign'])->name('qz.sign');
    Route::get('/qz/sign', [\App\Http\Controllers\QzTrayController::class, 'sign']);
});

Route::middleware(['auth:web', 'auto.permission'])->group(function () {
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

      //plans
    // Route::get('/plan', [PlanController::class,'index'])->name('plans.planlist');
    // Route::get('/add-plan', [PlanController::class,'create'])->name('plans.addplan');
    // Route::post('/add-plan', [PlanController::class,'store'])->name('plans.store');
    // Route::get('/edit-plan/{plan}', [PlanController::class,'edit'])->name('plans.edit');
    // Route::put('/edit-plan/{plan}', [PlanController::class,'update'])->name('plans.update');
    // Route::delete('/delete-plan/{plan}', [PlanController::class,'destroy'])->name('plans.destroy');
    Route::get('/my-plan', [PlanController::class,'myplan'])->name('plans.myplan');
    // Route::get('/plan-details', [PlanController::class, 'myPlanDetails'])->name('plans.details');

    // Search bar
    Route::get('/search-users', [AuthController::class, 'ajaxSearch'])->name('users.ajaxSearch');

    // Brand
    Route::get('/brands', [BrandController::class, 'brand_list'])->name('brand.list');
    Route::get('/add-brand', [BrandController::class, 'add_brand'])->name('brand.add');
    Route::get('/edit-brand/{id}', [BrandController::class, 'edit_brand'])->name('brand.edit');
    //authcontroller

    Route::get('/forgetpassword', [AuthController::class, 'forgetpassword'])->name('auth.forgetpassword');
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('auth.dashboard');
    Route::get('/staff-dashboard', [AuthController::class, 'staffDashboard'])->name('auth.staff-dashboard');
    Route::get('/profile', [AuthController::class, 'profile'])->name('auth.profile');
    Route::get('/change-password', function () {
        return view('change-password');
    })->name('auth.change-password');
    Route::get('/taxrates', [AuthController::class, 'taxrates'])->name('auth.taxrates');
    Route::get('/currency', [AuthController::class, 'currency'])->name('auth.currency');

    // Income Statement
    Route::get('/income-statement', [IncomeSheetController::class, 'index'])->name('income-statement.index');
    Route::get('/income-statement/pdf', [IncomeSheetController::class, 'generatePdf'])->name('income-statement.pdf');
    Route::get('/income-statement/excel', [IncomeSheetController::class, 'generateExcel'])->name('income-statement.excel');

    Route::get('/sales-receipt-payment', [SalesReceiptPaymentController::class, 'index'])->name('sales.receipt.index');
    Route::get('/sales-receipt-payment/customer/{customer}/orders', [SalesReceiptPaymentController::class, 'customerOrders'])->name('sales.receipt.orders');
    Route::get('/sales-receipt-payment/vendor/{vendor}/invoices', [SalesReceiptPaymentController::class, 'vendorInvoices'])->name('sales.receipt.vendor-invoices');
    Route::post('/sales-receipt-payment/store', [SalesReceiptPaymentController::class, 'store'])->name('sales.receipt.store');
    Route::put('/sales-receipt-payment/transaction/{payment}', [SalesReceiptPaymentController::class, 'updateTransaction'])->name('sales.receipt.transaction.update');
    Route::delete('/sales-receipt-payment/transaction/{payment}', [SalesReceiptPaymentController::class, 'deleteTransaction'])->name('sales.receipt.transaction.delete');

    Route::get('/inventory/boms', [ManufacturingController::class, 'bomIndex'])->name('inventory.bom.list');
    Route::get('/inventory/boms/create', [ManufacturingController::class, 'bomCreate'])->name('inventory.bom.add');
    Route::get('/inventory/boms/{id}/view', [ManufacturingController::class, 'bomView'])->whereNumber('id')->name('inventory.bom.view');
    Route::get('/inventory/boms/{id}/edit', [ManufacturingController::class, 'bomEdit'])->whereNumber('id')->name('inventory.bom.edit');
    Route::get('/inventory/productions', [ManufacturingController::class, 'productionIndex'])->name('inventory.production.list');
    Route::get('/inventory/productions/create', [ManufacturingController::class, 'productionCreate'])->name('inventory.production.add');
    Route::get('/inventory/productions/{id}/edit', [ManufacturingController::class, 'productionEdit'])->whereNumber('id')->name('inventory.production.edit');
    Route::get('/inventory/productions/{id}', [ManufacturingController::class, 'productionView'])->whereNumber('id')->name('inventory.production.view');

    // Category
    Route::get('/categories', [CategoryController::class, 'category_list'])->name('category.list');
    Route::get('/add-category', [CategoryController::class, 'add_category'])->name('category.add');
    Route::get('/edit-category/{id}', [CategoryController::class, 'edit_category'])->name('category.edit');
    // Product
    Route::get('/products', [ProductController::class, 'product_list'])->name('product.list');
    Route::get('/products/quick-update', [ProductController::class, 'product_quick_update'])->name('product.quick-update');
    Route::get('/add-product', [ProductController::class, 'add_product'])->name('product.add');
    Route::get('/edit-product/{id}', [ProductController::class, 'edit_product'])->name('product.edit');
    Route::get('/pricing-updates', [\App\Http\Controllers\PriceUpdateController::class, 'index'])->name('pricing-updates');
    
    // Pricing Updates API (using web middleware)
    Route::get('/pricing-updates/data', [\App\Http\Controllers\PriceUpdateController::class, 'getPendingUpdates'])->name('api.pricing-updates.data');
    Route::post('/pricing-updates/{id}/confirm', [\App\Http\Controllers\PriceUpdateController::class, 'confirm'])->name('api.pricing-updates.confirm');
    Route::post('/pricing-updates/{id}/reject', [\App\Http\Controllers\PriceUpdateController::class, 'reject'])->name('api.pricing-updates.reject');
    // Route::get('/product-detail', [ProductController::class, 'product_detail'])->name('product.detail');
    Route::get('/product-detail/{id}', [ProductController::class, 'product_detail'])->name('product.detail');
    Route::get('/import-product', [ProductController::class, 'product_import'])->name('product.import');
    Route::post('/import-product/upload', [ProductController::class, 'product_import_upload'])->name('product.import.upload');
    Route::get('/import-product/preview/{token}', [ProductController::class, 'product_import_preview'])->name('product.import.preview');
    Route::get('/product-view/{id}', [ProductController::class, 'product_view'])->name('product.view');
    Route::post('/quick-product/search-barcode', [ProductController::class, 'quickSearchByBarcode'])->name('product.quick-search.barcode');
    Route::post('/quick-product/search-image', [ProductController::class, 'quickSearchByImage'])->name('product.quick-search.image');

    // Account Branch
    Route::get('/account-branches', [\App\Http\Controllers\admin\AccountBranchController::class, 'account_branch_list'])->name('account_branch.list');
    Route::get('/add-account-branch', [\App\Http\Controllers\admin\AccountBranchController::class, 'add_account_branch'])->name('account_branch.add');
    Route::get('/edit-account-branch/{id}', [\App\Http\Controllers\admin\AccountBranchController::class, 'edit_account_branch'])->name('account_branch.edit');

    Route::get('/product/import/sample-file', function () {
        $filePath = public_path('admin/assets/csvfile/Productimportfile.csv');

        if (! file_exists($filePath)) {
            abort(404, 'Sample file not found.');
        }

        return response()->download($filePath, 'Productimportfile.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    })->name('product.import.sample');

    // purchase
    // Route::get('/purchase', [ProductController::class, 'product_list'])->name('Product.list');
    // Route::get('/add-product', [ProductController::class, 'add_product'])->name('Product.add');
    // Route::get('/edit-product', [ProductController::class, 'edit_product'])->name('Product.edit');
    // Route::get('/product-detail', [ProductController::class, 'product_detail'])->name('Product.detail');
  // Route::get('/import-product', [ProductController::class, 'product_import'])->name('Product.import');

  // Profit Loss Report
    Route::get('/profit-loss-report', [ProductController::class, 'profitLossReportIndex'])->name('profit-loss-report.index');
  //Route::get('/profit-loss-statement', [ProductController::class, 'profitLossStatementIndex'])->name('profit-loss-statement.index');
    Route::get('/profit-loss-report/data', [ApiProductController::class, 'getProfitLossData'])->name('profit-loss-report.data');
    Route::get('/profit-loss-report/pdf', [ApiProductController::class, 'profitLossPdf'])->name('profit-loss-report.pdf');
     Route::get('/profit-loss-statement', [ReportController::class, 'profitLossStatementIndex'])->name('profit-loss-statement.index');
    Route::get('/profit-loss-statement/data', [ReportController::class, 'profitLossStatementData'])->name('profit-loss-statement.data');
    Route::get('/profit-loss-statement/pdf', [ReportController::class, 'profitLossStatementPdf'])->name('profit-loss-statement.pdf');
    Route::get('/profit-loss-statement/excel', [ReportController::class, 'profitLossStatementExcel'])->name('profit-loss-statement.excel');

    // unit

    Route::get('/units', [UnitController::class, 'unit_list'])->name('unit.list');

    // Type Master & Material Master
    Route::get('/types', [TypeController::class, 'type_list'])->name('type.list');
    Route::get('/materials', [MaterialController::class, 'material_list'])->name('material.list');

    // quotation
    Route::get('/quotation', [QuotationController::class, 'quotation_list'])->name('quotation.list');
    Route::get('/add-quotation', [QuotationController::class, 'add_quotation'])->name('quotation.add');
    Route::get('/edit-quotation', [QuotationController::class, 'edit_quotation'])->name('quotation.edit');
    // Customer
    Route::get('/customer', [CustomerController::class, 'customer_list'])->name('customer.list');
    Route::get('/add-customer', [CustomerController::class, 'add_customer'])->name('customer.add');
    Route::get('/import-customer', [CustomerController::class, 'import_customer'])->name('customer.import');
    Route::post('/import-customer-list', [ApiCustomerController::class, 'importCustomers'])->name('customer.import-list');
    Route::get('/edit-customer/{num}', [CustomerController::class, 'edit_customer'])->name('customer.edit');
    Route::get('/customer-report', [CustomerController::class, 'customer_report'])->name('customer.report');
    Route::get('/customer-view/{id}', [CustomerController::class, 'customer_view'])->name('customer.view');

     //Row material
    Route::get('/row-materials', [RowMaterialController::class, 'row_material_list'])->name('row_material.list');
    Route::get('/add-row-material', [RowMaterialController::class, 'add_row_material'])->name('row_material.add');
    Route::get('/edit-row-material/{id}', [RowMaterialController::class, 'edit_row_material'])->name('row_material.edit');
    Route::get('/row-material-detail/{id}', [RowMaterialController::class, 'rowmaterial_detail'])->name('rowmaterial.detail');

    // Bank
    Route::get('/banks', [BankMasterController::class, 'bank_list'])->name('banks.index');
    Route::get('/banks/create', [BankMasterController::class, 'bank_create'])->name('banks.create');
    Route::get('/edit-bank/{num}', [BankMasterController::class, 'edit_bank'])->name('banks.edit');
    Route::get('/bank-report', [BankMasterController::class, 'bank_report'])->name('banks.report');
    Route::get('/bank-view/{id}', [BankMasterController::class, 'bank_view'])->name('banks.view');

    // credit note items
    Route::get('/credit-note-items', [CreditNoteItemController::class, 'index'])->name('credit-notes-items.index');
    Route::get('/credit-note-items/create', [CreditNoteItemController::class, 'create'])->name('credit-notes-items.create');
    Route::get('/view-credit-note-items/{id}', [CreditNoteItemController::class, 'show'])->name('credit-notes-items.show');
    Route::get('/edit-credit-note-items/{id}', [CreditNoteItemController::class, 'edit'])->name('credit-notes-items.edit');
    Route::post('/update-credit-note-items/{id}', [CreditNoteItemController::class, 'update'])->name('credit-notes-items.update');

    // credit notes type
    Route::get('/credit-notes', [CreditNotesTypeController::class, 'creditnotes_list'])->name('credit-notes.index');
    Route::get('credit-notes/create', [CreditNotesTypeController::class, 'create'])->name('credit-notes.create');
    Route::get('/edit-credit-notes/{id}', [CreditNotesTypeController::class, 'edit'])->name('credit-notes.edit');

    // Debit notes item type
    Route::get('/debit-note-items', [DebitNoteItemController::class, 'index'])->name('debit-notes-items.index');
    Route::get('/debit-note-items/create', [DebitNoteItemController::class, 'create'])->name('debit-notes-items.create');
    Route::get('/debit-note-items/edit/{id}', [DebitNoteItemController::class, 'edit'])->name('debit-notes-items.edit');
    Route::get('/debit-note-items/view/{id}', [DebitNoteItemController::class, 'show'])->name('debit-notes-items.show');
    Route::post('/debit-note-items/store', [DebitNoteItemController::class, 'store'])->name('debit-notes-items.store');

    // Challan Bills
    Route::resource('challan-bills', ChallanBillController::class);
    Route::get('/challan-bills/{id}/pdf', [ChallanBillController::class, 'generatePdf'])->name('challan-bills.pdf');
    Route::get('/challan-bills/customer-details/{id}', [ChallanBillController::class, 'getCustomerDetails'])->name('challan-bills.customer-details');

    // Sales
    Route::get('/sales', [SalesController::class, 'sales_list'])->name('sales.list');
    Route::get('/import-sales', [SalesController::class, 'import_sales'])->name('sales.import');
    // Route::get('/add-sales', [SalesController::class, 'add_sales'])->name('sales.add');
    Route::get('/edit-sales/{num}', [SalesController::class, 'edit_sales'])->name('sales.edit');
    Route::get('/sales-report', [SalesController::class, 'sales_report'])->name('sales.report');
    
    // Sales Pool Report
    Route::get('/sales-pool-report', [SalesPoolReportController::class, 'index'])->name('sales-pool-report.index');
    Route::get('/sales-pool-report/summary', [SalesPoolReportController::class, 'getSummaryStats'])->name('sales-pool-report.summary');
    Route::get('/sales-pool-report/hot-items', [SalesPoolReportController::class, 'getHotSellingItems'])->name('sales-pool-report.items');
    Route::get('/sales-pool-report/hot-customers', [SalesPoolReportController::class, 'getHotCustomers'])->name('sales-pool-report.customers');
    Route::get('/sales-pool-report/customers', [SalesPoolReportController::class, 'getCustomers'])->name('sales-pool-report.get-customers');
    Route::get('/sales-pool-report/staff-members', [SalesPoolReportController::class, 'getStaffMembers'])->name('sales-pool-report.get-staff');
    
    Route::get('/sales-details/{num}', [SalesController::class, 'sales_details'])->name('sales.details');
    // Delivery pages
    Route::get('/sales/products-delivery', [SalesController::class, 'productsDelivery'])->name('sales.products_delivery');
    Route::get('/sales/products-delivery/data', [SalesController::class, 'productsDeliveryData'])->name('sales.products_delivery.data');
    Route::get('/sales/delivery/{id}', [SalesController::class, 'delivery'])->name('sales.delivery');
    Route::post('/sales/delivery/store', [SalesController::class, 'storeDelivery'])->name('sales.delivery.store');
    Route::post('/sales/delivery/{delivery}/status', [SalesController::class, 'updateDeliveryStatus'])->name('sales.delivery.status.update');
    Route::post('/sales/order/{order}/delivery-status', [SalesController::class, 'updateOrderDeliveryStatus'])->name('sales.order.delivery.status.update');
    Route::get('/sales/pending-emis', [SalesController::class, 'getPendingEmis'])->name('sales.pending_emis');
    Route::get('/sales/delivery-challan/{id}', [SalesController::class, 'deliveryChallan'])->name('sales.delivery.challan');
    Route::get('/sales/delivery/pdf/{id}', [SalesController::class, 'deliveryChallanPdf'])->name('sales.delivery.challan.pdf');
    Route::get('/sales-invoice/{num}', [SalesController::class, 'salse_invoice'])->name('sales.invoice');
    Route::get('/add-sales', [SalesController::class, 'pos'])->name('sales.add');

    // Vendor
    Route::get('/vendors', [VendorController::class, 'vendor_list'])->name('vendor.list');
    Route::get('/add-vendor', [VendorController::class, 'add_vendor'])->name('vendor.add');
    Route::get('/edit-vendor/{num}', [VendorController::class, 'edit_vendor'])->name('vendor.edit');
    Route::get('/vendor-report', [VendorController::class, 'vendor_report'])->name('vendor.report');
    Route::get('/vendor-view/{id}', [VendorController::class, 'vendor_view'])->name('vendor.view');
    Route::get('/import-vendor', [VendorController::class, 'import_vendor'])->name('vendor.import');
    Route::get('/vendor-import-sample', [VendorController::class, 'import_sample'])->name('vendor.import.sample');

    // Financers
    Route::get('/financers', [FinancerController::class, 'financer_list'])->name('financer.list');
    Route::get('/financer/{id}/view', [FinancerController::class, 'view_financer'])->name('financer.view');
    Route::get('/edit-financer/{id}', [FinancerController::class, 'edit_financer'])->name('financer.edit');
    Route::get('/import-financer', [FinancerController::class, 'import_financer'])->name('financer.import');
    Route::get('/add-financer', [FinancerController::class, 'add_financer'])->name('financer.add');
    Route::get('/financers/data', [ApiFinancerController::class, 'getAllFinancer'])->name('financer.data');
    Route::post('/import-financer-list', [ApiFinancerController::class, 'importFinancers'])->name('financer.import-list');
    Route::post('/financer/store', [ApiFinancerController::class, 'storeFinancer'])->name('financer.store');
    Route::post('/financer/update/{id}', [ApiFinancerController::class, 'updateFinancer'])->name('financer.update');

    // Follow Up
    Route::get('/follow-ups', [FollowUpController::class, 'follow_up_list'])->name('followup.list');
    Route::get('/add-follow-up', [FollowUpController::class, 'add_follow_up'])->name('followup.add');
    Route::get('/edit-follow-up/{id}', [FollowUpController::class, 'edit_follow_up'])->name('followup.edit');
    Route::get('/follow-up-view/{id}', [FollowUpController::class, 'follow_up_view'])->name('followup.view');
    Route::get('/follow-up-report', [FollowUpController::class, 'follow_up_report'])->name('followup.report');
    Route::post('/follow-up/store', [FollowUpController::class, 'store'])->name('followup.store');
    Route::put('/follow-up/{id}/update', [FollowUpController::class, 'update'])->name('followup.update');
    Route::get('/follow-up/{id}/show', [FollowUpController::class, 'show'])->name('followup.show');
    Route::delete('/follow-up/{id}/delete', [FollowUpController::class, 'destroy'])->name('followup.delete');
    Route::get('/api/follow-up/customers', [FollowUpController::class, 'getCustomers'])->name('followup.customers');
    Route::get('/api/follow-up/staff', [FollowUpController::class, 'getStaff'])->name('followup.staff');

    // Leads
    Route::get('/leads', [LeadController::class, 'lead_list'])->name('lead.list');
    Route::get('/add-lead', [LeadController::class, 'add_lead'])->name('lead.add');
    Route::get('/edit-lead/{id}', [LeadController::class, 'edit_lead'])->name('lead.edit');
    Route::get('/lead-view/{id}', [LeadController::class, 'view_lead'])->name('lead.view');

       // Meeting
    Route::get('/meetings', [MeetingController::class, 'meeting_list'])->name('meeting.list');
    Route::get('/add-meeting', [MeetingController::class, 'add_meeting'])->name('meeting.add');
    Route::get('/edit-meeting/{id}', [MeetingController::class, 'edit_meeting'])->name('meeting.edit');
    Route::get('/meeting-view/{id}', [MeetingController::class, 'meeting_view'])->name('meeting.view');
    Route::get('/meeting-report', [MeetingController::class, 'meeting_report'])->name('meeting.report');
    Route::post('/meeting/store', [MeetingController::class, 'store'])->name('meeting.store');
    Route::put('/meeting/{id}/update', [MeetingController::class, 'update'])->name('meeting.update');
    Route::get('/meeting/{id}/show', [MeetingController::class, 'show'])->name('meeting.show');
    Route::delete('/meeting/{id}/delete', [MeetingController::class, 'destroy'])->name('meeting.delete');
    Route::get('/api/meeting/branches', [MeetingController::class, 'getBranches'])->name('meeting.branches');
    Route::get('/api/meeting/customers', [MeetingController::class, 'getCustomers'])->name('meeting.customers');
    Route::get('/api/meeting/staff', [MeetingController::class, 'getStaff'])->name('meeting.staff');

    //settings
    Route::get('/setting', [SettingController::class, 'generalsettings'])->name('setting.generalsettings');
    Route::get('/setting/customize-invoice', [InvoiceCustomizeController::class, 'index'])->name('setting.customize-invoice');
    Route::post('/setting/customize-invoice', [InvoiceCustomizeController::class, 'save'])->name('setting.customize-invoice.save');
    Route::get('/setting/invoice-print', [\App\Http\Controllers\admin\InvoicePrintSettingController::class, 'index'])->name('setting.invoice-print');
    Route::post('/setting/invoice-print', [\App\Http\Controllers\admin\InvoicePrintSettingController::class, 'save'])->name('setting.invoice-print.save');
    Route::get('/connected-devices', [ConnectedDevicesController::class, 'connected_devices'])->name('setting.connecteddevices');
    Route::get('/scanner', [ConnectedDevicesController::class, 'scanner'])->name('setting.scanner');
    Route::get('/generate-device-code', [ConnectedDevicesController::class, 'generateCode']);
    Route::post('/connect-device', [ConnectedDevicesController::class, 'connect']);
    Route::get('/check-device/{code}', [ConnectedDevicesController::class, 'check']);
    Route::get('/disconnect-device/{code}', [ConnectedDevicesController::class, 'disconnect']);
    Route::get('/get-session-device', [ConnectedDevicesController::class, 'getSessionDevice']);
    Route::post('/submit-device-scan', [ConnectedDevicesController::class, 'submitScan']);
    Route::get('/pull-device-scans', [ConnectedDevicesController::class, 'pullScans']);
    Route::get('/settings/smtp', [SmtpSettingController::class, 'index'])
    ->name('setting.smtpsettings');
    Route::get('/notifications', [NotificationController::class, 'getNotifications'])->name('notifications');
    Route::get('/notifications/live-updates', [NotificationController::class, 'liveUpdates'])->name('notifications.live');

    Route::get('/notifications/view', [NotificationController::class, 'getAllNotifications'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}/delete', [NotificationController::class, 'delete']);
    Route::delete('/notifications/delete-all', [NotificationController::class, 'deleteAll']);
    Route::get('/notifications/view', [NotificationController::class, 'getAllNotifications'])->name('notifications.index');



    Route::get('/setting/facebook-app-configuration', [SettingController::class, 'facebookappconfiguration'])->name('setting.facebookappconfiguration');
    Route::get('/table-truncate', [TableTruncateController::class, 'index'])->name('table-truncate.index');
    Route::post('/table-truncate/all', [TableTruncateController::class, 'truncateAll'])->name('table-truncate.truncate-all');
    Route::post('/table-truncate/{table}', [TableTruncateController::class, 'truncate'])->name('table-truncate.truncate');

    // GST Export
    //Route::get('/gst/gstr1/export-excel', [Gstr1ExportController::class, 'export'])->name('exports.gstr1.excel');
    Route::get('/gst/gstr1/export-excel', [Gstr1ExportController::class, 'export'])->name('exports.gstr1.excel');
    Route::get('/gst/gstr2/export-excel', [Gstr2ExportController::class, 'export'])->name('exports.gstr2.excel');
    Route::get('/gst/gstr3b/export-excel', [Gstr3bExportController::class, 'export'])->name('exports.gstr3b.excel');
    Route::get('/gstr1/export', [SalesGstr1ExportController::class, 'export'])->name('sales.gstr1.export');

    Route::get('/gstr2/export', [GSTR2gstController::class, 'export'])
        ->name('sales.gstr2.export');
    Route::get('/gstr3b/export', [SalesGstr3bExportController::class, 'export'])->name('sales.gstr3b.export');
    Route::get('/gst-sales-report/gstr3b-export', [ApiGstSalesReportController::class, 'exportGstr3b'])->name('gst.sales.report.gstr3b.export');

    // Test route for debugging (remove in production)
    // Route::get('/gstr3b/test', function() {
    //     return 'GSTR3B route is working!';
    // });

    // Test route for GSTR2 debugging (remove in production)
    Route::get('/gstr2/test', function () {
        return 'GSTR2 route is working!';
    });

    Route::get('/gstr9c/export', [SalesGSTR9CController::class, 'export'])
        ->name('sales.gstr9c.export');

    Route::get('/gstr9/export', [SalesGSTR9Controller::class, 'export'])
        ->name('sales.gstr9.export');

    Route::get('/gstr9c/purchase/export', [PurchaseGSTR9CController::class, 'export'])
        ->name('purchase.gstr9c.export');

    Route::get('/account-ledger-list', [AccountLedgerController::class, 'index'])->name('account_ledger.list');
    Route::get('/account-ledger', [AccountLedgerController::class, 'add'])->name('account_ledger.add');

    //salesreturn

    Route::get('/create-rental-return', [RentalReturnController::class, 'create_rental_return'])->name('rentalreturn.add');
    Route::get('/rental-return-list', [RentalReturnController::class, 'rental_return_list'])->name('rentalreturn.list');
    Route::get('/rental-return-pdf/{id}', [RentalReturnController::class, 'rental_return_pdf'])->name('rental.return.pdf');
    Route::get('/create-sales-return', [SalesReturnController::class, 'createsales_return'])->name('salesreturn.add');
    Route::get('/create-sales-returns', [SalesReturnController::class, 'createsales_returns'])->name('salesreturn.adds');

    Route::get('/edit-sales-return', [SalesReturnController::class, 'editsales_return'])->name('salesreturn.edit');
    Route::get('/edit-sales-returns', [SalesReturnController::class, 'editsales_returns'])->name('salesreturn.edits');

    Route::get('/sales-return-list', [SalesReturnController::class, 'salesreturn_list'])->name('salesreturn.list');
    Route::get('/sales-return-lists', [SalesReturnController::class, 'salesreturn_lists'])->name('salesreturn.lists');
    Route::get('/sales-return-pdf/{id}', [SalesReturnController::class, 'sales_return_pdf'])->name('sales.return.pdf');

    Route::get('/payment-in', [PaymentInController::class, 'index'])->name('paymentin.index');
    Route::get('/payment-in/customer/{customer}', [PaymentInController::class, 'customerData'])->name('paymentin.customer');
    Route::post('/payment-in', [PaymentInController::class, 'store'])->name('paymentin.store');

    // Purchase
    // Route::post('/import-purchase-list', [ApiPurchaseController::class, 'importPurchaseList'])->name('purchase.import-list');
    // Route::post('/import-sales-list', [ApiSalesController::class, 'importSalesList'])->name('sales.import-list');
    Route::get('/purchase-list', [PurchaseController::class, 'purchase_list'])->name('purchase.lists');
    Route::get('/row-material-purchase-list', [PurchaseController::class, 'row_material_purchase_list'])->name('purchase.row_material.lists');
    Route::get('/add-purchase', [PurchaseController::class, 'add_purchase'])->name('purchase.add');
    Route::get('/add-row-material-purchase', [PurchaseController::class, 'add_row_material_purchase'])->name('purchase.row_material.add');
    Route::get('/view-row-material-purchase/{id}', [PurchaseController::class, 'view_row_material_purchase'])->name('purchase.row_material.view');
    Route::get('/edit-row-material-purchase/{id}', [PurchaseController::class, 'edit_row_material_purchase'])->name('purchase.row_material.edit');
    Route::get('/edit-purchase/{num}', [PurchaseController::class, 'edit_purchase'])->name('purchase.edit');
    Route::get('/purchase-invoice', [PurchaseController::class, 'purchase_invoice'])->name('purchase.invoice');
    Route::get('/print-purchase/{id}', [PurchaseController::class, 'printPurchase'])->name('purchase.print');
    Route::get('/purchase-order-report', [PurchaseController::class, 'purchase_order_report'])->name('purchase.orderreport');
    Route::get('/import-purchase', [PurchaseController::class, 'import_purchase'])->name('purchase.import');
    Route::get('/purchase-report', [PurchaseController::class, 'purchase_report'])->name('purchase.report');    
    // Route::get('/purchase-details', [PurchaseController::class, 'purchase_details'])->name('purchase.details');
    Route::get('/purchase-details', [PurchaseController::class, 'purchaseDetails'])->name('purchase.details');
    Route::get('/purchase-view/{id}', [PurchaseController::class, 'getPurchaseDetails'])->name('purchase.viewData');
    Route::get('/purchase/invoice/pdf/{id}', [PurchaseController::class, 'purchase_invoice_pdf'])->name('purchase.invoice.pdf');

    // Route::get('/purchases/pdf', [PurchaseController::class, 'purchases_report']);
    // Custom Invoice
    Route::get('/custom-invoice-list', [CustomInvoiceController::class, 'custom_invoice_list'])->name('custom_invoice.lists');
    Route::get('/add-custom-invoice', [CustomInvoiceController::class, 'add_custom_invoice'])->name('custom_invoice.add');
    Route::get('/edit-custom-invoice/{num}', [CustomInvoiceController::class, 'edit_custom_invoice'])->name('custom_invoice.edit');
    Route::get('/custom-invoice-print/{num}', [CustomInvoiceController::class, 'custom_invoice_print'])->name('custom_invoice.print');
    Route::get('/invoice-view/{id}', [CustomInvoiceController::class, 'custom_invoice_view'])->name('custom_invoice.view');
    Route::get('/custom-invoice/pdf/{id}', [CustomInvoiceController::class, 'custom_invoice_pdf'])->name('custom_invoice.pdf');
    Route::get('/custom-invoice/payment-history/{id}', [CustomInvoiceController::class, 'getHistory'])->name('custom_invoice.payment_history');
    // Route::post('/custom-invoice/make-payment', [CustomInvoiceController::class, 'makePaymentSubmit'])->name('custom_invoice.make_payment_submit');
    Route::get('/export-invoice', [CustomInvoiceController::class, 'exportCustom_invoice'])->name('custom_invoice.export');
    Route::get('/export-invoice-pdf', [CustomInvoiceController::class, 'exportInvoicePDF'])->name('custom_invoice.pdf.export');
    // Purchase Reports
    Route::get('/purchases/report/{ids}', [PurchaseController::class, 'purchases_report']);
    Route::get('/purchases/report/{ids}/export-pdf', [PurchaseController::class, 'export_purchases_report_pdf'])->name('purchase.report.exportPdf');
    Route::get('/purchases/report/{ids}/export-excel', [PurchaseController::class, 'export_purchases_report_excel'])->name('purchase.report.exportExcel');

    // Sales Reports
    Route::get('/sales/report/{ids}', [SalesController::class, 'sale_report']);
    Route::get('/sales/report/{ids}/export-pdf', [SalesController::class, 'export_sales_report_pdf'])->name('sale.report.exportPdf');
    Route::get('/sales/report/{ids}/export-excel', [SalesController::class, 'export_sales_report_excel'])->name('sale.report.exportExcel');

    // TDS Reports
      // TDS page (UI)
    Route::get('/tds', function () {
        return view('tds.index');
    });
    Route::get('/tds-report', [SalesController::class, 'tds_report'])->name('tds.report');

    // //Purchase Return
    // Route::get('admin/get-vendor-name/{invoiceId}', [PurchaseReturnController::class, 'getVendorName']);
    // Route::get('admin/get-invoice-products/{invoiceId}', [PurchaseReturnController::class, 'getInvoiceProducts']);
    // Route::post('/admin/update-quantity', [PurchaseReturnController::class, 'updateQuantity']);

    //purchasereturn
    Route::get('/create-purchase-return', [PurchaseReturnController::class, 'create_purchase_return'])->name('purchasereturn.add');
    Route::get('/edit-purchase-return', [PurchaseReturnController::class, 'edit_purchase_return'])->name('purchase.edit');
    Route::get('/purchase-return-list', [PurchaseReturnController::class, 'purchase_return_list'])->name('purchasereturn.list');
    Route::get('/purchase-return-pdf/{id}', [PurchaseReturnController::class, 'purchaseReturnPdf'])->name('purchasereturn.pdf');
    //expense
    Route::get('/create-expense', [ExpenseController::class, 'create_expense'])->name('expense.add');
    // Route::get('/edit-purchase/{num}', [PurchaseController::class, 'edit_purchase'])->name('purchase.edit');
    Route::get('/edit-expense/{num}', [ExpenseController::class, 'edit_expense'])->name('expense.edit');
    Route::get('/expense-category', [ExpenseController::class, 'expense_category'])->name('expense.category');
    Route::get('/expense-list', [ExpenseController::class, 'expense_list'])->name('expense.list');
    Route::get('/seed-modules-now', function () {
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'ModulesSeeder']);
        return 'Seeded!';
    });
    Route::get('/expense-report', [ExpenseController::class, 'expense_report'])->name('expense.report');
    Route::get('/expense/report/{ids}', [ExpenseController::class, 'expense_report_view']);
    Route::get('/expense/report/{ids}/export-pdf', [ExpenseController::class, 'expense_report_pdf'])->name('expense.report.exportPdf');
    Route::get('/expense/pdf/{id}', [ExpenseController::class, 'downloadSingleExpensePdf'])->name('expense.single.pdf');
    Route::get('/export-expense', [ExpenseController::class, 'exportExpense'])->name('expense.export');
    Route::get('/export-expense-pdf', [ExpenseController::class, 'exportExpensePdf'])->name('expense.export-pdf');

    // Tickets
    Route::get('/ticket-list', [TicketController::class, 'index'])->name('ticket.list');
    Route::get('/ticket-create', [TicketController::class, 'create_ticket'])->name('ticket.add');
    Route::post('/ticket-store', [TicketController::class, 'store_ticket'])->name('ticket.store');
    Route::get('/ticket-edit/{id}', [TicketController::class, 'edit_ticket'])->name('ticket.edit');
    Route::put('/ticket-update/{id}', [TicketController::class, 'update_ticket'])->name('ticket.update');
    Route::delete('/ticket-delete/{id}', [TicketController::class, 'destroy_ticket'])->name('ticket.destroy');
    Route::get('/ticket-view/{id}', [TicketController::class, 'view_ticket'])->name('ticket.view');
    Route::get('/ticket-history/{customerId}', [TicketController::class, 'customer_history'])->name('ticket.history');
    Route::get('/ticket-report', [TicketController::class, 'report'])->name('ticket.report');
    Route::get('/ticket-report-pdf', [TicketController::class, 'reportPdf'])->name('ticket.report.pdf');
    Route::get('/ticket-get-order-products', [TicketController::class, 'get_order_products'])->name('ticket.get_order_products');
    Route::get('/ticket-get-customer-orders', [TicketController::class, 'get_customer_orders'])->name('ticket.get_customer_orders');

    //Labour Item
    Route::get('/labour-item', [LabourItemController::class, 'index'])->name('labour_item.all_labour_item');

    //transactions
    Route::get('/bankbook', [transactionController::class, 'bankBook'])->name('transaction.bankbook');
    Route::get('/cashbook', [transactionController::class, 'cashBook'])->name('transaction.cashbook');

    // Expense Type
    Route::get('/create-expense-type', [ExpenseTypeController::class, 'create_expense_type'])->name('expensetype.add');
    Route::get('/expense-type-list', [ExpenseTypeController::class, 'expense_type_list'])->name('expensetype.list');
    Route::get('/expense-type/edit/{id}', [ExpenseTypeController::class, 'edit'])->name('expensetype.edit');
    Route::get('/edit-expense-type/{num}', [ExpenseTypeController::class, 'edit_expense_type'])->name('expensetype.edit');

    //invoice
    Route::get('/inventory-report', [InvoiceController::class, 'inventory_report'])->name('inventory.report');
    Route::get('/invoice-report', [InvoiceController::class, 'invoice_report'])->name('invoice.report');

    // GST 3B report page (frontend view only)
    Route::get('/gst/gstr-3b', function () {
        return view('gst.gstr3b');
    })->name('gst.gstr3b');

    // GST 3B data endpoint for web (uses session auth, no API token required)
    Route::get('/gst/gstr-3b/data', [GstController::class, 'gstr3b']);
    Route::get('/gst/gstr-3b/export', [GstController::class, 'exportGstr3b'])->name('gst.gstr3b.export');
    Route::get('/gst/gstr-3b/export-excel', [GstController::class, 'exportGstr3bExcel'])->name('gst.gstr3b.exportExcel');

    // GST listing pages (purchase/sales) for sidebar
    Route::get('/gst/reports/purchase-list', function () {
        return view('gst.purchase_list');
    })->name('gst.reports.purchase');
    Route::get('/gst/reports/sales-list', [GstSalesReportController::class, 'index'])->name('gst.sales_list');
    Route::get('/gst/reports/hsn-summary', [GstSalesReportController::class, 'hsnSummary'])->name('gst.hsn_summary');

    Route::get('/gst/validate-workbook', [GstWorkbookValidationController::class, 'create'])->name('gst.validation.create');
    Route::post('/gst/validate-workbook', [GstWorkbookValidationController::class, 'store'])->name('gst.validation.store');
    Route::get('/gst/validate-workbook/{id}', [GstWorkbookValidationController::class, 'show'])->name('gst.validation.result');
    Route::get('/gst/validate-workbook/{id}/download', [GstWorkbookValidationController::class, 'download'])->name('gst.validation.download');

    // GSTR-1 page and data
    Route::get('/gst/gstr-1', function () {
        return view('gst.gstr1');
    })->name('gst.gstr1');
    Route::get('/gst/gstr-1/data', [GstController::class, 'gstr1']);
    Route::get('/gst/gstr-1/export', [GstController::class, 'exportGstr1'])->name('gst.gstr1.export');
    Route::get('/gst/gstr-1/export-excel', [GstController::class, 'exportGstr1Excel'])->name('gst.gstr1.exportExcel');
    // routes/web.php
    Route::get('/exports/gstr1', [\App\Http\Controllers\ReportController::class, 'exportGstr1Excel'])
        ->name('exports.gstr1');

    //today
    // routes/web.php

    //today

    // GSTR-2 (purchase) page and data
    Route::get('/gst/gstr-2', function () {
        return view('gst.gstr2');
    })->name('gst.gstr2');
    Route::get('/gst/gstr-2/data', [GstController::class, 'gstr2']);
    Route::get('/gst/gstr-2/pdf', [App\Http\Controllers\api\GstController::class, 'gstr2Pdf'])->name('gst.gstr2.pdf');
    Route::get('/gst/gstr-2/export-excel', [GstController::class, 'exportGstr2Excel'])->name('gst.gstr2.exportExcel');
    Route::get('/export-gstr2', [Gstr2ExportController::class, 'export'])->name('exports.gstr2.excel');

    // GSTR-9C page and data
    Route::get('/gst/gstr-9c', function () {
        return view('gst.gstr9c');
    })->name('gst.gstr9c');
    Route::get('/gst/gstr-9c/data', [GstController::class, 'gstr9c']);
    Route::get('/gst/gstr-9c/export', [GstController::class, 'exportGstr9c'])->name('gst.gstr9c.export');
    Route::get('/gst/gstr-9c/export-excel', [GstController::class, 'exportGstr9cExcel'])->name('gst.gstr9c.exportExcel');

    // GSTR-9 page and data
    Route::get('/gst/gstr-9', function () {
        return view('gst.gstr9');
    })->name('gst.gstr9');
    Route::get('/gst/gstr-9/data', [GstController::class, 'gstr9']);
    Route::get('/gst/gstr-9/export', [GstController::class, 'exportGstr9'])->name('gst.gstr9.export');
    Route::get('/gst/gstr-9/export-excel', [GstController::class, 'exportGstr9Excel'])->name('gst.gstr9.exportExcel');

    // TDS page (UI)
    Route::get('/tds', function () {
        return view('tds.index');
    });

    Route::get('/custom-invoice/{id}', [CustomInvoiceController::class, 'show'])->name('custom-invoice.view');
    // Route::post('/get_user_data', [SalesController::class, 'get_user_data'])->name('sales.get_user_data');

    Route::get('/staff', [StaffController::class, 'staff_list'])->name('staff.list');
    Route::get('/add-staff', [StaffController::class, 'add_staff'])->name('staff.add');
    Route::get('/edit-staff/{num}', [StaffController::class, 'edit_staff'])->name('staff.edit');
    Route::get('/staff-report', [StaffController::class, 'staff_report'])->name('staff.report');
    Route::get('/staff-view/{id}', [StaffController::class, 'staff_view'])->name('staff.view');

    Route::get('/sub-branch', [SubBranchController::class, 'subbranch_list'])->name('subbranch.list');
    Route::get('/add-subbranch', [SubBranchController::class, 'add_subbranch'])->name('subbranch.add');
    Route::get('/edit-subbranch/{id}', [SubBranchController::class, 'edit_subbranch'])->name('subbranch.edit');
    Route::get('/delete-subbranch/{id}', [SubBranchController::class, 'delete_subbranch'])->name('subbranch.delete');
    Route::get('/view-subbranch/{id}', [SubBranchController::class, 'view_subbranch'])->name('subbranch.view');

    Route::get('/inventory-list', [InventoryController::class, 'index'])->name('inventory.list');
    Route::get('/inventory-View/{id}', [InventoryController::class, 'View'])->name('inventory.View');

    Route::get('/material-inventory-list', [InventoryController::class, 'materialIndex'])->name('material-inventory.list');
    Route::get('/material-inventory-View/{id}', [InventoryController::class, 'materialView'])->name('material-inventory.View');
    Route::get('/exchange-inventory-list', [InventoryController::class, 'exchangeIndex'])->name('exchange-inventory.list');

    // Accounting balancesheet =========
    Route::get('/balance-sheet', [BalanceSheetController::class, 'index'])->name('accounting.balance-sheet');

    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::get('/appointments/edit/{id}', [AppointmentController::class, 'edit'])->name('appointments.edit');
    Route::get('/appointments/view/{id}', [AppointmentController::class, 'view'])->name('appointments.view');

    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.list');
    Route::get('/attendance/add', [AttendanceController::class, 'add'])->name('attendance.create');
    Route::get('/attendance/manage', [AttendanceController::class, 'manage'])->name('attendance.manage');
    Route::get('/attendance/manage/summary', [AttendanceController::class, 'manageSummary'])->name('attendance.manage.summary');
    Route::get('/attendance/manage/history/{employee_id}', [AttendanceController::class, 'manageHistory'])->name('attendance.manage.history');

    Route::get('/advance_pay', [AdvancePaymentController::class, 'index'])->name('advance_pay.index');
    Route::get('/advance_pay/create', [AdvancePaymentController::class, 'create'])->name('advance_pay.create');
    Route::post('/advance_pay/store', [AdvancePaymentController::class, 'store'])->name('advance_pay.store');
    Route::get('/advance_pay/{id}', [AdvancePaymentController::class, 'show'])->name('advance_pay.show');
    Route::get('/advance_pay/{id}/edit', [AdvancePaymentController::class, 'edit'])->name('advance_pay.edit');
    Route::put('/advance_pay/{id}/update', [AdvancePaymentController::class, 'update'])->name('advance_pay.update');
    Route::delete('/advance_pay/{id}/delete', [AdvancePaymentController::class, 'destroy'])->name('advance_pay.destroy');

    // Salary
    Route::get('/salary-list', [SalaryController::class, 'List'])->name('salary.list');
    Route::get('/salary/view', [SalaryController::class, 'viewList'])->name('salary.view');
    Route::get('/my-salary', [SalaryController::class, 'mySalary'])->name('salary.my');

    // HR Dashboard
    Route::get('/hr/dashboard', [AuthController::class, 'hrDashboard'])->name('hr.dashboard');

    // Company Rules pages
    Route::get('/view-rules', [CompanyRulesController::class, 'display_rules'])->name('company-rules.view-rules');
    Route::get('/creates-rules', [CompanyRulesController::class, 'create_rules'])->name('company-rules.create-rules');
    Route::get('/company-rules', [CompanyRulesController::class, 'company_rules'])->name('company-rules.show');
    Route::get('/company-rules/create', [CompanyRulesController::class, 'create'])->name('company-rules.create');

    // Department pages
    Route::get('/department', [DepartmentController::class, 'createPage'])->name('department.create');
    Route::get('/department/{id}', [DepartmentController::class, 'createPage'])->name('department.edit');
    Route::get('/departmentview', [DepartmentController::class, 'indexPage'])->name('department.view');

    // Designation pages
    Route::get('/designation', [DesignationController::class, 'createPage'])->name('designation.create');
    Route::post('/designation/edit', [DesignationController::class, 'openEditPage'])->name('designation.edit.open');
    Route::get('/designationview', [DesignationController::class, 'indexPage'])->name('designation.view');

    // Holiday pages
    Route::get('/company-holidays', [HolidaysController::class, 'calendarPage'])->name('holidays.calendar');
    Route::get('/holidays', [HolidaysController::class, 'indexPage'])->name('holidays.index');
    Route::get('/holidays/add', [HolidaysController::class, 'createPage'])->name('holidays.create');
    Route::get('/holidays/edit/{id}', [HolidaysController::class, 'editPage'])->name('holidays.edit');

    // Leave type pages
    Route::get('/leave_type', [LeaveTypeController::class, 'createPage'])->name('leave-type.create');
    Route::post('/leave_type/edit', [LeaveTypeController::class, 'openEditPage'])->name('leave-type.edit.open');
    Route::get('/leavetypeview', [LeaveTypeController::class, 'indexPage'])->name('leave-type.view');

    // Leave pages
    Route::get('/leaveview', [LeaveController::class, 'indexPage'])->name('leave.view');
    Route::get('/leave-request', [LeaveController::class, 'leaveRequest'])->name('leave.request');
    Route::post('/leave-request/{leaveId}/status', [LeaveController::class, 'updateStatusWeb'])->name('leave.request.status');
    Route::get('/leaveviews', [LeaveController::class, 'legacyPage'])->name('leave.views');
    Route::get('/addleave', [LeaveController::class, 'createPage'])->name('leave.add');

    // Converted attendance module from legacy CodeIgniter screens
    Route::prefix('staff/attendance')->name('attendence.')->group(function () {
        Route::get('/summary', [AttendanceeController::class, 'display'])->name('summary');
        Route::get('/list', [AttendanceeController::class, 'view'])->name('list');
        Route::get('/calendar', [AttendanceeController::class, 'viewCalendar'])->name('calendar');
        Route::post('/checkin', [AttendanceeController::class, 'checkIn'])->name('checkin.web');
        Route::post('/checkout', [AttendanceeController::class, 'checkOut'])->name('checkout.web');
    });

    // omsai-ERP style staff check-in / check-out (web session, GPS capture)
    Route::get('/staff/check-status',  [AttendanceeController::class, 'staffCheckStatus'])->name('staff.checkstatus');
    Route::post('/staff/check-in',     [AttendanceeController::class, 'staffCheckIn'])->name('staff.checkin');
    Route::post('/staff/check-out',    [AttendanceeController::class, 'staffCheckOut'])->name('staff.checkout');

    // Backward-compatible URLs used inside the legacy attendance UI
    Route::get('/attendence', [AttendanceeController::class, 'display'])->name('attendence.manage');
    Route::get('/view', [AttendanceeController::class, 'view'])->name('attendence.view');
    Route::get('/view-calendar', [AttendanceeController::class, 'viewCalendar'])->name('attendence.view_calendar');

    // Payroll pages
    Route::prefix('payroll')->name('payroll.')->group(function () {
        Route::get('/', [ApiPayrollController::class, 'page'])->name('create');
        Route::get('/{id}', [ApiPayrollController::class, 'page'])->whereNumber('id')->name('edit');
        Route::get('/salary', [ApiPayrollController::class, 'groupsalaryPage'])->name('salary');
        Route::get('/salary-details', [ApiPayrollController::class, 'salaryDetails'])->name('salary-details');
        Route::get('/incentive', [ApiPayrollController::class, 'incentiveList'])->name('incentive');
        Route::post('/incentive/save', [ApiPayrollController::class, 'saveIncentives'])->name('incentive.save');
        Route::get('/profile/{id}', [ApiPayrollController::class, 'profilePage'])->whereNumber('id')->name('profile');
        Route::get('/details/{id}', [ApiPayrollController::class, 'getProfile'])->whereNumber('id')->name('details');
        Route::get('/download-slip/{id}', [ApiPayrollController::class, 'downloadSlip'])->whereNumber('id')->name('download-slip');
    });
    Route::get('/payrollview', [ApiPayrollController::class, 'display'])->name('payroll.list');
});
