<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('purpose');
            $table->text('comment')->nullable();
            $table->string('priority')->default('Medium');
            $table->string('status')->default('Pending');
            $table->dateTime('follow_up_datetime');
            $table->boolean('isDeleted')->default(0);
            $table->timestamps();

            $table->index(['branch_id', 'isDeleted', 'follow_up_datetime']);
            $table->index(['customer_id', 'assigned_to']);

      });
    }

    public function down(): void
    {
        Schema::dropIfExists('follow_ups');
    }
};
