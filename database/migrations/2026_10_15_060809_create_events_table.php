<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('events')) {
            Schema::create('events', function (Blueprint $table) {
                $table->id();
                $table->string('event_no')->unique(); // auto-generated EV-XXXXXX
                $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
                $table->foreignId('event_sub_category_id')->constrained('event_sub_categories')->onDelete('cascade');
                $table->string('event_name');
                $table->date('event_date');
                $table->date('today_date'); // registration date
                $table->string('venue')->nullable();
                $table->text('remarks')->nullable();
                $table->decimal('package_cost_price', 12, 2)->default(0);
                $table->decimal('package_sell_price', 12, 2)->default(0);
                $table->decimal('additional_services_total', 12, 2)->default(0);
                $table->decimal('other_charges_total', 12, 2)->default(0);
                $table->decimal('sub_total', 12, 2)->default(0);
                $table->decimal('discount', 12, 2)->default(0);
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->decimal('advance_payment', 12, 2)->default(0);
                $table->decimal('balance', 12, 2)->default(0);
                $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
                $table->boolean('notification_7_sent')->default(false);
                $table->boolean('notification_3_sent')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
