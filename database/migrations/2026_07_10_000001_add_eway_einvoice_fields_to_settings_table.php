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
        Schema::table('settings', function (Blueprint $table) {
            // E-Way Bill fields
            $table->boolean('enable_eway_bill')->default(false);
            $table->string('eway_gstin_number')->nullable();
            $table->string('eway_legal_business_name')->nullable();
            $table->string('eway_pan_number')->nullable();
            $table->string('eway_state')->nullable();
            $table->string('eway_state_code')->nullable();
            $table->string('eway_address_line_1')->nullable();
            $table->string('eway_address_line_2')->nullable();
            $table->string('eway_city')->nullable();
            $table->string('eway_pin_code')->nullable();
            $table->string('eway_api_username')->nullable();
            $table->string('eway_api_password')->nullable();
            $table->string('eway_app_key_client_id')->nullable();

            // E-Invoice fields
            $table->boolean('enable_einvoice')->default(false);
            $table->string('einvoice_api_username')->nullable();
            $table->string('einvoice_api_password')->nullable();
            $table->string('einvoice_client_id')->nullable();
            $table->string('einvoice_client_secret')->nullable();
            $table->string('einvoice_api_endpoint_url')->nullable();
            $table->string('einvoice_gstin')->nullable();
            $table->text('einvoice_nic_public_key')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'enable_eway_bill',
                'eway_gstin_number',
                'eway_legal_business_name',
                'eway_pan_number',
                'eway_state',
                'eway_state_code',
                'eway_address_line_1',
                'eway_address_line_2',
                'eway_city',
                'eway_pin_code',
                'eway_api_username',
                'eway_api_password',
                'eway_app_key_client_id',
                'enable_einvoice',
                'einvoice_api_username',
                'einvoice_api_password',
                'einvoice_client_id',
                'einvoice_client_secret',
                'einvoice_api_endpoint_url',
                'einvoice_gstin',
                'einvoice_nic_public_key'
            ]);
        });
    }
};
