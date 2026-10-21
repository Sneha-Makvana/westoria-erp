<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('row_material_inventory', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('row_material_id');
            $table->decimal('initial_stock', 15, 3)->default(0);
            $table->decimal('current_stock', 15, 3)->default(0);
            $table->string('type')->nullable();

            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('create_by')->nullable();

            $table->date('date')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('row_material_id');
            $table->index('branch_id');
            $table->index('create_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('row_material_inventory');
    }
};