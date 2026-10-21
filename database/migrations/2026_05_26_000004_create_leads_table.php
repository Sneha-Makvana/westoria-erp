<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('whatsapp')->nullable();
            $table->text('address');
            $table->string('image')->nullable();
            $table->string('company_name')->nullable();
            $table->string('sic_code')->nullable();
            $table->string('lead_source');
            $table->string('lead_status')->default('New');
            $table->text('comment')->nullable();
            $table->boolean('isDeleted')->default(0);
            $table->timestamps();

            $table->index(['branch_id', 'isDeleted', 'lead_status']);
            $table->index(['assigned_to', 'created_by']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
