<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // Top 4 metric boxes
            if (! Schema::hasColumn('settings', 'show_crm_lead_pipeline')) {
                $table->boolean('show_crm_lead_pipeline')->default(true)->after('show_crm_dashboard');
            }
            if (! Schema::hasColumn('settings', 'show_crm_conversion')) {
                $table->boolean('show_crm_conversion')->default(true)->after('show_crm_lead_pipeline');
            }
            if (! Schema::hasColumn('settings', 'show_crm_followup_load')) {
                $table->boolean('show_crm_followup_load')->default(true)->after('show_crm_conversion');
            }
            if (! Schema::hasColumn('settings', 'show_crm_meeting_momentum')) {
                $table->boolean('show_crm_meeting_momentum')->default(true)->after('show_crm_followup_load');
            }
            
            // Charts and sections
            if (! Schema::hasColumn('settings', 'show_crm_lead_status_mix')) {
                $table->boolean('show_crm_lead_status_mix')->default(true)->after('show_crm_meeting_momentum');
            }
            if (! Schema::hasColumn('settings', 'show_crm_activity_trend')) {
                $table->boolean('show_crm_activity_trend')->default(true)->after('show_crm_lead_status_mix');
            }
            if (! Schema::hasColumn('settings', 'show_crm_pipeline_quality')) {
                $table->boolean('show_crm_pipeline_quality')->default(true)->after('show_crm_activity_trend');
            }
            if (! Schema::hasColumn('settings', 'show_crm_recent_leads')) {
                $table->boolean('show_crm_recent_leads')->default(true)->after('show_crm_pipeline_quality');
            }
            if (! Schema::hasColumn('settings', 'show_crm_next_7_days')) {
                $table->boolean('show_crm_next_7_days')->default(true)->after('show_crm_recent_leads');
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $columns = [
                'show_crm_lead_pipeline',
                'show_crm_conversion',
                'show_crm_followup_load',
                'show_crm_meeting_momentum',
                'show_crm_lead_status_mix',
                'show_crm_activity_trend',
                'show_crm_pipeline_quality',
                'show_crm_recent_leads',
                'show_crm_next_7_days',
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
