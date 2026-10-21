<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            $userColumns = [
                'credit_limit' => fn (Blueprint $table) => $table->decimal('credit_limit', 15, 2)->nullable()->after('alternate_phone'),
                'credit_days' => fn (Blueprint $table) => $table->unsignedInteger('credit_days')->nullable()->after('credit_limit'),
                'pan_status' => fn (Blueprint $table) => $table->string('pan_status')->nullable()->after('pan_number'),
                'account_group' => fn (Blueprint $table) => $table->string('account_group')->nullable()->after('gender'),
            ];

            foreach ($userColumns as $column => $definition) {
                if (! Schema::hasColumn('users', $column)) {
                    Schema::table('users', function (Blueprint $table) use ($definition) {
                        $definition($table);
                    });
                }
            }
        }

        if (Schema::hasTable('user_details')) {
            $detailColumns = [
                'address_line2' => fn (Blueprint $table) => $table->string('address_line2')->nullable()->after('address'),
                'address_line3' => fn (Blueprint $table) => $table->string('address_line3')->nullable()->after('address_line2'),
                'pin_code' => fn (Blueprint $table) => $table->string('pin_code')->nullable()->after('city'),
            ];

            foreach ($detailColumns as $column => $definition) {
                if (! Schema::hasColumn('user_details', $column)) {
                    Schema::table('user_details', function (Blueprint $table) use ($definition) {
                        $definition($table);
                    });
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users')) {
            $userColumns = array_filter([
                Schema::hasColumn('users', 'credit_limit') ? 'credit_limit' : null,
                Schema::hasColumn('users', 'credit_days') ? 'credit_days' : null,
                Schema::hasColumn('users', 'pan_status') ? 'pan_status' : null,
                Schema::hasColumn('users', 'account_group') ? 'account_group' : null,
            ]);

            if (! empty($userColumns)) {
                Schema::table('users', function (Blueprint $table) use ($userColumns) {
                    $table->dropColumn($userColumns);
                });
            }
        }
    }
};
