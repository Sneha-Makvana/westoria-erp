<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_barcode_histories', function (Blueprint $table) {
            $table->text('old_barcode_details')->nullable()->after('new_label_reference');
            $table->text('new_barcode_details')->nullable()->after('old_barcode_details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_barcode_histories', function (Blueprint $table) {
            $table->dropColumn(['old_barcode_details', 'new_barcode_details']);
        });
    }
};
