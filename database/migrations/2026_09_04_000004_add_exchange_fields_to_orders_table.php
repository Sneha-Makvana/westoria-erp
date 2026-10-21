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
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('exchange_material_id')->nullable()->after('other_charges');
            $table->unsignedBigInteger('exchange_type_id')->nullable()->after('exchange_material_id');
            $table->decimal('exchange_price', 15, 2)->nullable()->default(0)->after('exchange_type_id');
            $table->decimal('exchange_weight', 15, 3)->nullable()->default(0)->after('exchange_price');
            $table->decimal('exchange_amount', 15, 2)->nullable()->default(0)->after('exchange_weight');
            $table->json('exchange_details')->nullable()->after('exchange_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'exchange_material_id',
                'exchange_type_id',
                'exchange_price',
                'exchange_weight',
                'exchange_amount',
                'exchange_details',
            ]);
        });
    }
};
