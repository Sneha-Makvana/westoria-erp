<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('meeting_title');
            $table->string('meeting_type');
            $table->text('agenda')->nullable();
            $table->text('address')->nullable();
            $table->dateTime('scheduled_on');
            $table->string('status')->default('Scheduled');
            $table->boolean('isDeleted')->default(0);
            $table->timestamps();

            $table->index(['branch_id', 'isDeleted', 'scheduled_on']);
            $table->index(['customer_id', 'assigned_to']);

       });
    }

    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
