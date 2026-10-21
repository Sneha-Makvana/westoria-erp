<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // ERP Dashboard main toggle (if not exists)
            if (! Schema::hasColumn('settings', 'show_erp_dashboard')) {
                $table->boolean('show_erp_dashboard')->default(true)->after('show_hr_dashboard');
            }
            
            // ERP Dashboard subsections - Top Metric Boxes
            if (! Schema::hasColumn('settings', 'show_erp_total_sales')) {
                $table->boolean('show_erp_total_sales')->default(true)->after('show_erp_dashboard');
            }
            if (! Schema::hasColumn('settings', 'show_erp_total_purchase')) {
                $table->boolean('show_erp_total_purchase')->default(true)->after('show_erp_total_sales');
            }
            if (! Schema::hasColumn('settings', 'show_erp_total_expense')) {
                $table->boolean('show_erp_total_expense')->default(true)->after('show_erp_total_purchase');
            }
            
            // ERP Dashboard subsections - Count Boxes
            if (! Schema::hasColumn('settings', 'show_erp_sales_invoice_count')) {
                $table->boolean('show_erp_sales_invoice_count')->default(true)->after('show_erp_total_expense');
            }
            if (! Schema::hasColumn('settings', 'show_erp_purchase_invoice_count')) {
                $table->boolean('show_erp_purchase_invoice_count')->default(true)->after('show_erp_sales_invoice_count');
            }
            if (! Schema::hasColumn('settings', 'show_erp_customers_count')) {
                $table->boolean('show_erp_customers_count')->default(true)->after('show_erp_purchase_invoice_count');
            }
            if (! Schema::hasColumn('settings', 'show_erp_vendors_count')) {
                $table->boolean('show_erp_vendors_count')->default(true)->after('show_erp_customers_count');
            }
            
            // ERP Dashboard subsections - Charts
            if (! Schema::hasColumn('settings', 'show_erp_sales_chart')) {
                $table->boolean('show_erp_sales_chart')->default(true)->after('show_erp_vendors_count');
            }
            if (! Schema::hasColumn('settings', 'show_erp_purchase_chart')) {
                $table->boolean('show_erp_purchase_chart')->default(true)->after('show_erp_sales_chart');
            }
            
            // ERP Dashboard subsections - Tables
            if (! Schema::hasColumn('settings', 'show_erp_recent_sales')) {
                $table->boolean('show_erp_recent_sales')->default(true)->after('show_erp_purchase_chart');
            }
            if (! Schema::hasColumn('settings', 'show_erp_recent_purchases')) {
                $table->boolean('show_erp_recent_purchases')->default(true)->after('show_erp_recent_sales');
            }
            if (! Schema::hasColumn('settings', 'show_erp_recent_products')) {
                $table->boolean('show_erp_recent_products')->default(true)->after('show_erp_recent_purchases');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $columns = [
                'show_erp_dashboard',
                'show_erp_total_sales',
                'show_erp_total_purchase',
                'show_erp_total_expense',
                'show_erp_sales_invoice_count',
                'show_erp_purchase_invoice_count',
                'show_erp_customers_count',
                'show_erp_vendors_count',
                'show_erp_sales_chart',
                'show_erp_purchase_chart',
                'show_erp_recent_sales',
                'show_erp_recent_purchases',
                'show_erp_recent_products',
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
