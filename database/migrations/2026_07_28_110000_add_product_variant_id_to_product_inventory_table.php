<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_inventory', function (Blueprint $table) {
            if (!Schema::hasColumn('product_inventory', 'product_variant_id')) {
                $table->unsignedBigInteger('product_variant_id')->nullable()->after('product_id');
                $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            }
        });

        // Data migration: Backfill product_variant_id for existing product_inventory records
        try {
            $inventories = DB::table('product_inventory')->whereNull('product_variant_id')->get();
            foreach ($inventories as $inv) {
                if (!$inv->product_id) {
                    continue;
                }
                $variants = DB::table('product_variants')->where('product_id', $inv->product_id)->get();
                if ($variants->count() === 1) {
                    DB::table('product_inventory')
                        ->where('id', $inv->id)
                        ->update(['product_variant_id' => $variants->first()->id]);
                } else if ($variants->count() > 1) {
                    Log::warning("Migration backfill warning: product_inventory ID {$inv->id} for product_id {$inv->product_id} has multiple variants. Skipping automatic backfill for manual review.");
                }
            }
        } catch (\Throwable $e) {
            Log::error("Data migration error in product_inventory backfill: " . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_inventory', function (Blueprint $table) {
            if (Schema::hasColumn('product_inventory', 'product_variant_id')) {
                $table->dropForeign(['product_variant_id']);
                $table->dropColumn('product_variant_id');
            }
        });
    }
};
