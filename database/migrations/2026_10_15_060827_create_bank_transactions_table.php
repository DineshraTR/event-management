<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('bank_transactions')) {
            Schema::create('bank_transactions', function (Blueprint $table) {
                $table->id();
                $table->string('transaction_no')->unique();
                $table->enum('type', ['income', 'expense']); // income=deposit, expense=withdrawal
                $table->enum('transaction_method', ['cash', 'cheque', 'bank_transfer']);
                $table->string('account_type')->default('cash'); // cash, bank_account name
                $table->decimal('amount', 12, 2);
                $table->string('description');
                $table->string('reference_no')->nullable();
                $table->string('cheque_no')->nullable();
                $table->string('bank_name')->nullable();
                $table->date('transaction_date');
                $table->decimal('running_balance', 12, 2)->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_transactions');
    }
};
