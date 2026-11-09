<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('expenses')) {
            Schema::create('expenses', function (Blueprint $table) {
                $table->id();
                $table->string('expense_no')->unique();
                $table->enum('type', ['direct', 'normal']); // direct=event-linked, normal=general
                $table->string('category')->nullable(); // e.g., Transport, Printing
                $table->string('description');
                $table->decimal('amount', 12, 2);
                $table->date('expense_date');
                $table->foreignId('event_id')->nullable()->constrained('events')->onDelete('set null'); // for direct expenses
                $table->string('reference')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
