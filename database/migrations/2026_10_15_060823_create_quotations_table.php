<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('quotations')) {
            Schema::create('quotations', function (Blueprint $table) {
                $table->id();
                $table->string('quotation_no')->unique(); // QUO-XXXXXX
                $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
                $table->foreignId('event_sub_category_id')->nullable()->constrained('event_sub_categories')->onDelete('set null');
                $table->date('quotation_date');
                $table->date('valid_until')->nullable();
                $table->string('event_name')->nullable();
                $table->date('event_date')->nullable();
                $table->string('venue')->nullable();
                $table->decimal('sub_total', 12, 2)->default(0);
                $table->decimal('discount', 12, 2)->default(0);
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->enum('status', ['draft', 'sent', 'accepted', 'rejected'])->default('draft');
                $table->text('remarks')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
