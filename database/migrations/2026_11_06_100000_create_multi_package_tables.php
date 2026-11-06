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
        // 1. Create event_packages table
        if (!Schema::hasTable('event_packages')) {
            Schema::create('event_packages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
                $table->foreignId('event_sub_category_id')->nullable()->constrained('event_sub_categories')->nullOnDelete();
                $table->string('package_name');
                $table->decimal('cost_price', 12, 2)->default(0);
                $table->decimal('sell_price', 12, 2)->default(0);
                $table->integer('qty')->default(1);
                $table->decimal('total_price', 12, 2)->default(0);
                $table->timestamps();
            });
        }

        // 2. Create order_packages table
        if (!Schema::hasTable('order_packages')) {
            Schema::create('order_packages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
                $table->unsignedBigInteger('event_package_id')->nullable();
                $table->foreignId('event_sub_category_id')->nullable()->constrained('event_sub_categories')->nullOnDelete();
                $table->string('package_name');
                $table->decimal('cost_price', 12, 2)->default(0);
                $table->decimal('sell_price', 12, 2)->default(0);
                $table->integer('qty')->default(1);
                $table->decimal('total_price', 12, 2)->default(0);
                $table->timestamps();

                $table->foreign('event_package_id')->references('id')->on('event_packages')->nullOnDelete();
            });
        }

        // 3. Create quotation_packages table
        if (!Schema::hasTable('quotation_packages')) {
            Schema::create('quotation_packages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('quotation_id')->constrained('quotations')->cascadeOnDelete();
                $table->foreignId('event_sub_category_id')->nullable()->constrained('event_sub_categories')->nullOnDelete();
                $table->string('package_name');
                $table->decimal('cost_price', 12, 2)->default(0);
                $table->decimal('sell_price', 12, 2)->default(0);
                $table->integer('qty')->default(1);
                $table->decimal('total_price', 12, 2)->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_packages');
        Schema::dropIfExists('event_packages');
        Schema::dropIfExists('quotation_packages');
    }
};
