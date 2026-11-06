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
        if (!Schema::hasTable('sms_logs')) {
            Schema::create('sms_logs', function (Blueprint $table) {
                $table->id();
                $table->string('recipient', 50)->index();
                $table->unsignedBigInteger('customer_id')->nullable()->index();
                $table->string('type', 50)->index(); // quotation_created, order_created, payment_received, custom
                $table->string('reference_type', 50)->nullable(); // Quotation, Order, Payment
                $table->unsignedBigInteger('reference_id')->nullable()->index();
                $table->string('sender_id', 50)->default('CEYLON WEDS');
                $table->text('message');
                $table->string('status', 20)->default('pending')->index(); // sent, failed, pending
                $table->integer('response_code')->nullable();
                $table->text('response_body')->nullable();
                $table->text('error_message')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();

                $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
