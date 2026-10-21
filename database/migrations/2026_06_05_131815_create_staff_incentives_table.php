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
        Schema::create('staff_incentives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('month_year', 10);
            $table->decimal('total_sales', 12, 2)->default(0);
            $table->decimal('incentive_percentage', 5, 2)->default(0);
            $table->decimal('incentive_amount', 10, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['user_id', 'month_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_incentives');
    }
};
