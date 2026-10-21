<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('product_barcode_histories', function (Blueprint $table) {
            $table->decimal('old_qty', 15, 2)->nullable();
            $table->decimal('new_qty', 15, 2)->nullable();
            $table->string('old_unit')->nullable();
            $table->string('new_unit')->nullable();
        });
    }

    public function down()
    {
        Schema::table('product_barcode_histories', function (Blueprint $table) {
            $table->dropColumn(['old_qty', 'new_qty', 'old_unit', 'new_unit']);
        });
    }
};
