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
        // 1. Add package and cost breakdown columns to orders table
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'event_sub_category_id')) {
                $table->unsignedBigInteger('event_sub_category_id')->nullable()->after('event_id');
            }
            if (!Schema::hasColumn('orders', 'package_name')) {
                $table->string('package_name')->nullable()->after('event_sub_category_id');
            }
            if (!Schema::hasColumn('orders', 'package_cost_price')) {
                $table->decimal('package_cost_price', 12, 2)->default(0)->after('package_name');
            }
            if (!Schema::hasColumn('orders', 'package_sell_price')) {
                $table->decimal('package_sell_price', 12, 2)->default(0)->after('package_cost_price');
            }
            if (!Schema::hasColumn('orders', 'additional_services_total')) {
                $table->decimal('additional_services_total', 12, 2)->default(0)->after('package_sell_price');
            }
            if (!Schema::hasColumn('orders', 'other_charges_total')) {
                $table->decimal('other_charges_total', 12, 2)->default(0)->after('additional_services_total');
            }
        });

        // 2. Add is_package_item flag to order_services
        Schema::table('order_services', function (Blueprint $table) {
            if (!Schema::hasColumn('order_services', 'is_package_item')) {
                $table->boolean('is_package_item')->default(false)->after('service_id');
            }
        });

        // 3. Add relational context columns to cash_transactions
        Schema::table('cash_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('cash_transactions', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('reference_no');
            }
            if (!Schema::hasColumn('cash_transactions', 'order_id')) {
                $table->unsignedBigInteger('order_id')->nullable()->after('customer_id');
            }
            if (!Schema::hasColumn('cash_transactions', 'payment_id')) {
                $table->unsignedBigInteger('payment_id')->nullable()->after('order_id');
            }
            if (!Schema::hasColumn('cash_transactions', 'expense_id')) {
                $table->unsignedBigInteger('expense_id')->nullable()->after('payment_id');
            }
        });

        // 4. Add relational context columns to bank_transactions
        Schema::table('bank_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('bank_transactions', 'bank_id')) {
                $table->unsignedBigInteger('bank_id')->nullable()->after('account_type');
            }
            if (!Schema::hasColumn('bank_transactions', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('reference_no');
            }
            if (!Schema::hasColumn('bank_transactions', 'order_id')) {
                $table->unsignedBigInteger('order_id')->nullable()->after('customer_id');
            }
            if (!Schema::hasColumn('bank_transactions', 'payment_id')) {
                $table->unsignedBigInteger('payment_id')->nullable()->after('order_id');
            }
            if (!Schema::hasColumn('bank_transactions', 'expense_id')) {
                $table->unsignedBigInteger('expense_id')->nullable()->after('payment_id');
            }
            if (!Schema::hasColumn('bank_transactions', 'cheque_id')) {
                $table->unsignedBigInteger('cheque_id')->nullable()->after('expense_id');
            }
        });

        // 5. Add reference and bank_id to payments
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'reference')) {
                $table->string('reference', 100)->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('payments', 'bank_id')) {
                $table->unsignedBigInteger('bank_id')->nullable()->after('bank_name');
            }
        });

        // 6. Add bank_id and category_id to expenses
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'bank_id')) {
                $table->unsignedBigInteger('bank_id')->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('expenses', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable()->after('category');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'event_sub_category_id',
                'package_name',
                'package_cost_price',
                'package_sell_price',
                'additional_services_total',
                'other_charges_total',
            ]);
        });

        Schema::table('order_services', function (Blueprint $table) {
            $table->dropColumn('is_package_item');
        });

        Schema::table('cash_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'customer_id',
                'order_id',
                'payment_id',
                'expense_id',
            ]);
        });

        Schema::table('bank_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'bank_id',
                'customer_id',
                'order_id',
                'payment_id',
                'expense_id',
                'cheque_id',
            ]);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'reference',
                'bank_id',
            ]);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn([
                'bank_id',
                'category_id',
            ]);
        });
    }
};

