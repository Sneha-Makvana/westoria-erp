<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meeting_id');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->dateTime('reminder_at')->nullable();
            $table->dateTime('queued_at')->nullable();
            $table->dateTime('processed_at')->nullable();
            $table->string('queue_status')->default('pending');
            $table->string('email_status')->default('pending');
            $table->string('whatsapp_status')->default('pending');
            $table->dateTime('email_sent_at')->nullable();
            $table->dateTime('whatsapp_sent_at')->nullable();
            $table->string('email_recipient')->nullable();
            $table->string('whatsapp_recipient')->nullable();
            $table->string('twilio_message_sid')->nullable();
            $table->text('email_error')->nullable();
            $table->text('whatsapp_error')->nullable();
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamps();

            $table->index(['meeting_id', 'branch_id']);
            $table->index(['queue_status', 'reminder_at']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_reminder_logs');
    }
};
