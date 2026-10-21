<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_status_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('status');
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index(['lead_id', 'created_at']);
            $table->index(['branch_id', 'updated_by']);

       });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_status_histories');
    }
};
