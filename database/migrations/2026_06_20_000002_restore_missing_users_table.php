<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            return;
        }

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->string('name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('membership_no')->nullable();
            $table->date('customer_since')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('phone')->nullable()->index();
            $table->string('alternate_phone')->nullable();
            $table->decimal('credit_limit', 15, 2)->nullable();
            $table->unsignedInteger('credit_days')->nullable();
            $table->string('gst_number', 20)->nullable();
            $table->string('gstin_status')->nullable();
            $table->string('gender')->nullable();
            $table->string('pan_number', 10)->nullable();
            $table->string('pan_status')->nullable();
            $table->string('tin_number')->nullable();
            $table->string('account_group')->nullable();
            $table->string('state_code')->nullable();
            $table->string('state_name', 100)->nullable();
            $table->string('customer_category')->nullable();
            $table->unsignedSmallInteger('age')->nullable();
            $table->date('dob')->nullable();
            $table->date('doa')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->json('face_descriptor')->nullable();
            $table->text('profile_image')->nullable();
            $table->string('role')->nullable()->default('user');
            $table->boolean('status')->nullable()->default(1);
            $table->boolean('isDeleted')->default(0);
            $table->boolean('haspermission')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->rememberToken();
            $table->timestamps();

            if (Schema::hasTable('plans')) {
                $table->foreign('plan_id')->references('id')->on('plans')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        // This is a repair migration. Never remove an existing users table on rollback.
    }
};
