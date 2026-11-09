<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('cash_transactions')) {
            Schema::create('cash_transactions', function (Blueprint $table) {
                $table->id();
                $table->string('transaction_no')->unique();
                $table->enum('type', ['income', 'expense']); // income=cash inflow/deposit, expense=cash outflow/withdrawal
                $table->decimal('amount', 12, 2);
                $table->string('description');
                $table->string('reference_no')->nullable();
                $table->date('transaction_date');
                $table->decimal('running_balance', 12, 2)->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
            });

            // Migrate existing cash records from bank_transactions table into cash_transactions
            if (Schema::hasTable('bank_transactions')) {
                $cashRecords = DB::table('bank_transactions')->where('transaction_method', 'cash')->get();
                foreach ($cashRecords as $row) {
                    $cshNo = str_replace('TXN-', 'CSH-', $row->transaction_no);
                    DB::table('cash_transactions')->insert([
                        'transaction_no' => $cshNo,
                        'type' => $row->type,
                        'amount' => $row->amount,
                        'description' => $row->description,
                        'reference_no' => $row->reference_no,
                        'transaction_date' => $row->transaction_date,
                        'running_balance' => $row->running_balance ?? 0,
                        'notes' => $row->notes,
                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at,
                    ]);
                }

                // Remove migrated cash records from bank_transactions so only bank entries remain
                DB::table('bank_transactions')->where('transaction_method', 'cash')->delete();
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_transactions');
    }
};

