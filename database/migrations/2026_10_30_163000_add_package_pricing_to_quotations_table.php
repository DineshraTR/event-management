<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            if (!Schema::hasColumn('quotations', 'package_name')) {
                $table->string('package_name')->nullable()->after('event_sub_category_id');
            }
            if (!Schema::hasColumn('quotations', 'package_cost_price')) {
                $table->decimal('package_cost_price', 12, 2)->default(0)->after('package_name');
            }
            if (!Schema::hasColumn('quotations', 'package_sell_price')) {
                $table->decimal('package_sell_price', 12, 2)->default(0)->after('package_cost_price');
            }
            if (!Schema::hasColumn('quotations', 'additional_services_total')) {
                $table->decimal('additional_services_total', 12, 2)->default(0)->after('package_sell_price');
            }
            if (!Schema::hasColumn('quotations', 'other_charges_total')) {
                $table->decimal('other_charges_total', 12, 2)->default(0)->after('additional_services_total');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn([
                'package_name',
                'package_cost_price',
                'package_sell_price',
                'additional_services_total',
                'other_charges_total',
            ]);
        });
    }
};

