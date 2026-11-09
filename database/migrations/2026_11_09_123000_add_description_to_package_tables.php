<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('event_packages') && !Schema::hasColumn('event_packages', 'description')) {
            Schema::table('event_packages', function (Blueprint $table) {
                $table->text('description')->nullable()->after('package_name');
            });
        }

        if (Schema::hasTable('order_packages') && !Schema::hasColumn('order_packages', 'description')) {
            Schema::table('order_packages', function (Blueprint $table) {
                $table->text('description')->nullable()->after('package_name');
            });
        }

        if (Schema::hasTable('quotation_packages') && !Schema::hasColumn('quotation_packages', 'description')) {
            Schema::table('quotation_packages', function (Blueprint $table) {
                $table->text('description')->nullable()->after('package_name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('event_packages') && Schema::hasColumn('event_packages', 'description')) {
            Schema::table('event_packages', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }

        if (Schema::hasTable('order_packages') && Schema::hasColumn('order_packages', 'description')) {
            Schema::table('order_packages', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }

        if (Schema::hasTable('quotation_packages') && Schema::hasColumn('quotation_packages', 'description')) {
            Schema::table('quotation_packages', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
    }
};
