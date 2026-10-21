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
                'membership_no' => fn (Blueprint $table) => $table->string('membership_no')->nullable()->after('company_name'),
                'customer_since' => fn (Blueprint $table) => $table->date('customer_since')->nullable()->after('membership_no'),
                'alternate_phone' => fn (Blueprint $table) => $table->string('alternate_phone')->nullable()->after('phone'),
                'customer_category' => fn (Blueprint $table) => $table->string('customer_category')->nullable()->after('state_name'),
                'age' => fn (Blueprint $table) => $table->unsignedSmallInteger('age')->nullable()->after('customer_category'),
                'dob' => fn (Blueprint $table) => $table->date('dob')->nullable()->after('age'),
                'doa' => fn (Blueprint $table) => $table->date('doa')->nullable()->after('dob'),
                'tin_number' => fn (Blueprint $table) => $table->string('tin_number')->nullable()->after('pan_number'),
                'gstin_status' => fn (Blueprint $table) => $table->string('gstin_status')->nullable()->after('gst_number'),
                'gender' => fn (Blueprint $table) => $table->string('gender')->nullable()->after('gstin_status'),
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
        if (Schema::hasTable('user_details')) {
            $detailColumns = array_filter([
                Schema::hasColumn('user_details', 'address_line2') ? 'address_line2' : null,
                Schema::hasColumn('user_details', 'address_line3') ? 'address_line3' : null,
                Schema::hasColumn('user_details', 'pin_code') ? 'pin_code' : null,
            ]);

            if (! empty($detailColumns)) {
                Schema::table('user_details', function (Blueprint $table) use ($detailColumns) {
                    $table->dropColumn($detailColumns);
                });
            }
        }

        if (Schema::hasTable('users')) {
            $userColumns = array_filter([
                Schema::hasColumn('users', 'membership_no') ? 'membership_no' : null,
                Schema::hasColumn('users', 'customer_since') ? 'customer_since' : null,
                Schema::hasColumn('users', 'alternate_phone') ? 'alternate_phone' : null,
                Schema::hasColumn('users', 'customer_category') ? 'customer_category' : null,
                Schema::hasColumn('users', 'age') ? 'age' : null,
                Schema::hasColumn('users', 'dob') ? 'dob' : null,
                Schema::hasColumn('users', 'doa') ? 'doa' : null,
                Schema::hasColumn('users', 'tin_number') ? 'tin_number' : null,
                Schema::hasColumn('users', 'gstin_status') ? 'gstin_status' : null,
                Schema::hasColumn('users', 'gender') ? 'gender' : null,
            ]);

            if (! empty($userColumns)) {
                Schema::table('users', function (Blueprint $table) use ($userColumns) {
                    $table->dropColumn($userColumns);
                });
            }
        }
    }
};
