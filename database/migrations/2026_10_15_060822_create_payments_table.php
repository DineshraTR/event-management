<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->string('payment_no')->unique(); // PAY-XXXXXX
                $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
                $table->decimal('amount', 12, 2);
                $table->enum('payment_type', ['advance', 'partial', 'full']);
                $table->enum('payment_method', ['cash', 'cheque', 'bank_transfer'])->default('cash');
                $table->string('cheque_no')->nullable();
                $table->string('bank_name')->nullable();
                $table->date('payment_date');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
