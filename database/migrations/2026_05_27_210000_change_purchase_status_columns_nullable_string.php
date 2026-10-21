<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_invoice', function (Blueprint $table) {
            $table->string('status', 255)->nullable()->default(null)->change();
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->string('purchase_status', 255)->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('purchase_invoice', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->enum('purchase_status', ['pending', 'completed', 'cancelled'])->default('pending')->change();
        });
    }
};
