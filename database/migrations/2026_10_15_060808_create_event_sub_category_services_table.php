<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('event_sub_category_services')) {
            Schema::create('event_sub_category_services', function (Blueprint $table) {
                $table->id();
                $table->foreignId('event_sub_category_id')->constrained('event_sub_categories')->onDelete('cascade');
                $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
                $table->decimal('cost_price', 12, 2)->default(0); // can override service cost
                $table->integer('qty')->default(1);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('event_sub_category_services');
    }
};
