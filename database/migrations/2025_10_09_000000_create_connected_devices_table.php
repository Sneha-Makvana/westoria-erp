<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('connected_devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_name')->nullable();
            $table->string('device_code', 50)->unique();
            $table->string('status')->default('active');
            $table->string('session_id')->nullable();
            $table->timestamps();
        });

        Schema::create('connected_device_scans', function (Blueprint $table) {
            $table->id();
            $table->string('device_code', 50)->index();
            $table->string('barcode', 191);
            $table->timestamp('consumed_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('connected_device_scans');
        Schema::dropIfExists('connected_devices');
    }
};
