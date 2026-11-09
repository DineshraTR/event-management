<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('event_sub_categories')) {
            Schema::create('event_sub_categories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('event_category_id')->constrained('event_categories')->onDelete('cascade');
                $table->string('name');
                $table->text('description')->nullable();
                $table->decimal('cost_price', 12, 2)->default(0); // auto-calculated from services
                $table->decimal('sell_price', 12, 2)->default(0); // manually entered
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('event_sub_categories');
    }
};
