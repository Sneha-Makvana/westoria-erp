<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->dropForeignIfExists('product_inventory', 'product_inventory_product_id_foreign');

        $this->dropForeignIfExists('purchases', 'purchases_vendor_id_foreign');
        $this->dropForeignIfExists('purchases', 'purchases_invoice_id_foreign');
        $this->dropForeignIfExists('purchases', 'purchases_item_foreign');

        $this->dropForeignIfExists('purchase_invoice', 'purchase_invoice_vendor_id_foreign');

        $this->dropForeignIfExists('purchase_returns', 'purchase_returns_purchase_id_foreign');

        $this->dropForeignIfExists('purchase_return_items', 'purchase_return_items_purchase_return_id_foreign');
        $this->dropForeignIfExists('purchase_return_items', 'purchase_return_items_product_id_foreign');

        $this->dropForeignIfExists('salaries', 'salaries_staff_id_foreign');

        $this->dropForeignIfExists('sales_labour_items', 'sales_labour_items_order_id_foreign');
        $this->dropForeignIfExists('sales_labour_items', 'sales_labour_items_labour_item_id_foreign');

        $this->dropForeignIfExists('sales_returns', 'sales_returns_order_id_foreign');

        $this->dropForeignIfExists('sales_return_items', 'sales_return_items_sales_return_id_foreign');
        $this->dropForeignIfExists('sales_return_items', 'sales_return_items_product_id_foreign');

        $this->dropForeignIfExists('user_permissions', 'user_permissions_user_id_foreign');
        $this->dropForeignIfExists('user_permissions', 'user_permissions_module_id_foreign');

        $this->dropForeignIfExists('credit_note_items', 'credit_note_items_credite_note_id_foreign');
        $this->dropForeignIfExists('credit_note_items', 'credit_note_items_user_id_foreign');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->addForeignIfMissing('product_inventory', 'product_inventory_product_id_foreign', 'product_id', 'products', 'id', 'cascade');

        $this->addForeignIfMissing('purchases', 'purchases_vendor_id_foreign', 'vendor_id', 'vendors', 'id', 'set null');
        $this->addForeignIfMissing('purchases', 'purchases_invoice_id_foreign', 'invoice_id', 'purchase_invoice', 'id', 'set null');
        $this->addForeignIfMissing('purchases', 'purchases_item_foreign', 'item', 'products', 'id', 'set null');

        $this->addForeignIfMissing('purchase_invoice', 'purchase_invoice_vendor_id_foreign', 'vendor_id', 'vendors', 'id', 'set null');

        $this->addForeignIfMissing('purchase_returns', 'purchase_returns_purchase_id_foreign', 'purchase_id', 'purchases', 'id', 'set null');

        $this->addForeignIfMissing('purchase_return_items', 'purchase_return_items_purchase_return_id_foreign', 'purchase_return_id', 'purchase_returns', 'id', 'cascade');
        $this->addForeignIfMissing('purchase_return_items', 'purchase_return_items_product_id_foreign', 'product_id', 'products', 'id', 'set null');

        $this->addForeignIfMissing('salaries', 'salaries_staff_id_foreign', 'staff_id', 'staff', 'id', 'set null');

        $this->addForeignIfMissing('sales_labour_items', 'sales_labour_items_order_id_foreign', 'order_id', 'orders', 'id', 'cascade');
        $this->addForeignIfMissing('sales_labour_items', 'sales_labour_items_labour_item_id_foreign', 'labour_item_id', 'labour_items', 'id', 'set null');

        $this->addForeignIfMissing('sales_returns', 'sales_returns_order_id_foreign', 'order_id', 'orders', 'id', 'set null');

        $this->addForeignIfMissing('sales_return_items', 'sales_return_items_sales_return_id_foreign', 'sales_return_id', 'sales_returns', 'id', 'cascade');
        $this->addForeignIfMissing('sales_return_items', 'sales_return_items_product_id_foreign', 'product_id', 'products', 'id', 'set null');

        $this->addForeignIfMissing('user_permissions', 'user_permissions_user_id_foreign', 'user_id', 'users', 'id', 'cascade');
        $this->addForeignIfMissing('user_permissions', 'user_permissions_module_id_foreign', 'module_id', 'modules', 'id', 'cascade');

        $this->addForeignIfMissing('credit_note_items', 'credit_note_items_credite_note_id_foreign', 'credite_note_id', 'credit_notes_type', 'id', 'set null');
        $this->addForeignIfMissing('credit_note_items', 'credit_note_items_user_id_foreign', 'user_id', 'users', 'id', 'set null');
    }

    private function dropForeignIfExists(string $table, string $constraint): void
    {
        try {
            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraint}`");
        } catch (\Throwable $e) {
            // Ignore if missing/already dropped.
        }
    }

    private function addForeignIfMissing(
        string $table,
        string $constraint,
        string $column,
        string $referencesTable,
        string $referencesColumn,
        string $onDelete
    ): void {
        try {
            DB::statement(
                "ALTER TABLE `{$table}` ADD CONSTRAINT `{$constraint}` FOREIGN KEY (`{$column}`) REFERENCES `{$referencesTable}` (`{$referencesColumn}`) ON DELETE {$onDelete}"
            );
        } catch (\Throwable $e) {
            // Ignore if already exists or incompatible data.
        }
    }
};
