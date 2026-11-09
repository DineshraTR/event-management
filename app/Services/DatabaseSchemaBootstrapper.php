<?php

namespace App\Services;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class DatabaseSchemaBootstrapper
{
    protected static bool $executed = false;

    /**
     * Auto-provisions and verifies required tables and columns.
     * Designed specifically for shared-hosting environments without SSH/terminal access.
     */
    public static function boot(): void
    {
        if (self::$executed) {
            return;
        }

        self::$executed = true;

        try {
            self::autoMigratePending();
            self::ensureMultiPackageTables();
            self::ensureLegacyColumns();
            self::ensureAuditLogsTable();
            self::ensureSmsLogsTable();
            self::backfillLegacyPackages();
        } catch (\Throwable $e) {
            Log::error('DatabaseSchemaBootstrapper failed: ' . $e->getMessage());
        }
    }

    /**
     * Automatically runs any pending migrations when migration files are added or updated.
     */
    protected static function autoMigratePending(): void
    {
        try {
            $migrationsPath = database_path('migrations');
            if (!is_dir($migrationsPath)) {
                return;
            }

            $cacheKey = 'sys_db_last_migration_check';
            $lastChecked = \Illuminate\Support\Facades\Cache::get($cacheKey);
            $lastModified = filemtime($migrationsPath);

            if (!$lastChecked || $lastChecked < $lastModified) {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                \Illuminate\Support\Facades\Cache::forever($cacheKey, time());
            }
        } catch (\Throwable $e) {
            Log::warning('DatabaseSchemaBootstrapper autoMigratePending: ' . $e->getMessage());
        }
    }

    /**
     * Ensures event_packages, order_packages, and quotation_packages exist.
     */
    protected static function ensureMultiPackageTables(): void
    {
        // 1. event_packages table
        if (!Schema::hasTable('event_packages')) {
            Schema::create('event_packages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('event_id');
                $table->unsignedBigInteger('event_sub_category_id')->nullable();
                $table->string('package_name');
                $table->text('description')->nullable();
                $table->decimal('cost_price', 12, 2)->default(0);
                $table->decimal('sell_price', 12, 2)->default(0);
                $table->integer('qty')->default(1);
                $table->decimal('total_price', 12, 2)->default(0);
                $table->timestamps();

                $table->foreign('event_id')->references('id')->on('events')->cascadeOnDelete();
                $table->foreign('event_sub_category_id')->references('id')->on('event_sub_categories')->nullOnDelete();
            });
        }

        // 2. order_packages table
        if (!Schema::hasTable('order_packages')) {
            Schema::create('order_packages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                $table->unsignedBigInteger('event_package_id')->nullable();
                $table->unsignedBigInteger('event_sub_category_id')->nullable();
                $table->string('package_name');
                $table->text('description')->nullable();
                $table->decimal('cost_price', 12, 2)->default(0);
                $table->decimal('sell_price', 12, 2)->default(0);
                $table->integer('qty')->default(1);
                $table->decimal('total_price', 12, 2)->default(0);
                $table->timestamps();

                $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
                $table->foreign('event_package_id')->references('id')->on('event_packages')->nullOnDelete();
                $table->foreign('event_sub_category_id')->references('id')->on('event_sub_categories')->nullOnDelete();
            });
        }

        // 3. quotation_packages table
        if (!Schema::hasTable('quotation_packages')) {
            Schema::create('quotation_packages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('quotation_id');
                $table->unsignedBigInteger('event_sub_category_id')->nullable();
                $table->string('package_name');
                $table->text('description')->nullable();
                $table->decimal('cost_price', 12, 2)->default(0);
                $table->decimal('sell_price', 12, 2)->default(0);
                $table->integer('qty')->default(1);
                $table->decimal('total_price', 12, 2)->default(0);
                $table->timestamps();

                $table->foreign('quotation_id')->references('id')->on('quotations')->cascadeOnDelete();
                $table->foreign('event_sub_category_id')->references('id')->on('event_sub_categories')->nullOnDelete();
            });
        }
    }

    /**
     * Ensures audit_logs table exists for comprehensive activity tracking.
     */
    protected static function ensureAuditLogsTable(): void
    {
        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('user_name')->nullable();
                $table->string('user_email')->nullable();
                $table->string('action', 50)->index();
                $table->string('module', 50)->index();
                $table->unsignedBigInteger('record_id')->nullable();
                $table->string('record_title')->nullable();
                $table->text('description')->nullable();
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent')->nullable();
                $table->timestamps();

                $table->index(['created_at', 'module']);
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            });
        }
    }

    /**
     * Ensures necessary auxiliary columns exist across existing tables.
     */
    protected static function ensureLegacyColumns(): void
    {
        // Check orders table
        if (Schema::hasTable('orders')) {
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
        }

        // Check order_services table
        if (Schema::hasTable('order_services')) {
            Schema::table('order_services', function (Blueprint $table) {
                if (!Schema::hasColumn('order_services', 'is_package_item')) {
                    $table->boolean('is_package_item')->default(false)->after('service_id');
                }
            });
        }

        // Check quotations table
        if (Schema::hasTable('quotations')) {
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

        // Check events table
        if (Schema::hasTable('events')) {
            try {
                Schema::table('events', function (Blueprint $table) {
                    if (Schema::hasColumn('events', 'event_sub_category_id')) {
                        $table->unsignedBigInteger('event_sub_category_id')->nullable()->change();
                    }
                    if (!Schema::hasColumn('events', 'package_name')) {
                        $table->string('package_name')->nullable()->after('event_sub_category_id');
                    }
                    if (!Schema::hasColumn('events', 'package_cost_price')) {
                        $table->decimal('package_cost_price', 12, 2)->default(0)->after('package_name');
                    }
                    if (!Schema::hasColumn('events', 'package_sell_price')) {
                        $table->decimal('package_sell_price', 12, 2)->default(0)->after('package_cost_price');
                    }
                });
            } catch (\Throwable $e) {}
        }

        // Check event_packages table
        if (Schema::hasTable('event_packages')) {
            Schema::table('event_packages', function (Blueprint $table) {
                if (!Schema::hasColumn('event_packages', 'category_name')) {
                    $table->string('category_name')->nullable()->after('package_name');
                }
                if (!Schema::hasColumn('event_packages', 'description')) {
                    $table->text('description')->nullable()->after('package_name');
                }
                if (!Schema::hasColumn('event_packages', 'unit_price')) {
                    $table->decimal('unit_price', 12, 2)->default(0)->after('sell_price');
                }
                if (!Schema::hasColumn('event_packages', 'deliverables')) {
                    $table->json('deliverables')->nullable()->after('total_price');
                }
            });
        }

        // Check order_packages table
        if (Schema::hasTable('order_packages')) {
            Schema::table('order_packages', function (Blueprint $table) {
                if (!Schema::hasColumn('order_packages', 'category_name')) {
                    $table->string('category_name')->nullable()->after('package_name');
                }
                if (!Schema::hasColumn('order_packages', 'description')) {
                    $table->text('description')->nullable()->after('package_name');
                }
                if (!Schema::hasColumn('order_packages', 'unit_price')) {
                    $table->decimal('unit_price', 12, 2)->default(0)->after('sell_price');
                }
                if (!Schema::hasColumn('order_packages', 'deliverables')) {
                    $table->json('deliverables')->nullable()->after('total_price');
                }
            });
        }

        // Check quotation_packages table
        if (Schema::hasTable('quotation_packages')) {
            Schema::table('quotation_packages', function (Blueprint $table) {
                if (!Schema::hasColumn('quotation_packages', 'category_name')) {
                    $table->string('category_name')->nullable()->after('package_name');
                }
                if (!Schema::hasColumn('quotation_packages', 'description')) {
                    $table->text('description')->nullable()->after('package_name');
                }
                if (!Schema::hasColumn('quotation_packages', 'unit_price')) {
                    $table->decimal('unit_price', 12, 2)->default(0)->after('sell_price');
                }
                if (!Schema::hasColumn('quotation_packages', 'deliverables')) {
                    $table->json('deliverables')->nullable()->after('total_price');
                }
            });
        }

        // Check banks table
        if (Schema::hasTable('banks')) {
            Schema::table('banks', function (Blueprint $table) {
                if (!Schema::hasColumn('banks', 'is_primary')) {
                    $table->boolean('is_primary')->default(false)->after('is_active');
                }
            });
        }
    }

    /**
     * Ensures sms_logs table exists for customer notification delivery history.
     */
    protected static function ensureSmsLogsTable(): void
    {
        if (!Schema::hasTable('sms_logs')) {
            Schema::create('sms_logs', function (Blueprint $table) {
                $table->id();
                $table->string('recipient', 50)->index();
                $table->unsignedBigInteger('customer_id')->nullable()->index();
                $table->string('type', 50)->index(); // quotation_created, order_created, payment_received, custom
                $table->string('reference_type', 50)->nullable(); // Quotation, Order, Payment
                $table->unsignedBigInteger('reference_id')->nullable()->index();
                $table->string('sender_id', 50)->default('CEYLON WEDS');
                $table->text('message');
                $table->string('status', 20)->default('pending')->index(); // sent, failed, pending
                $table->integer('response_code')->nullable();
                $table->text('response_body')->nullable();
                $table->text('error_message')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();

                $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            });
        }
    }

    /**
     * Automatically backfills legacy packages for old events, orders, and quotations
     * into the new multi-package tables so that past records remain fully visible, editable, and manageable.
     */
    protected static function backfillLegacyPackages(): void
    {
        // 1. Backfill legacy Events into event_packages
        if (Schema::hasTable('events') && Schema::hasTable('event_packages')) {
            try {
                $legacyEvents = \App\Models\Event::whereDoesntHave('packages')
                    ->where(function ($q) {
                        $q->whereNotNull('event_sub_category_id')
                          ->orWhere('package_sell_price', '>', 0);
                    })
                    ->with(['subCategory.category', 'subCategory.subCategoryServices.service'])
                    ->get();

                foreach ($legacyEvents as $ev) {
                    $subCat = $ev->subCategory;
                    $pkgName = $ev->package_name ?? $subCat?->name ?? 'Event Package';
                    $catName = $subCat?->category?->name ?? 'General';
                    $cost = (float)($ev->package_cost_price ?? $subCat?->cost_price ?? 0);
                    $sell = (float)($ev->package_sell_price ?? $subCat?->sell_price ?? 0);
                    $deliverables = ($subCat && $subCat->subCategoryServices) ? $subCat->subCategoryServices->map(fn($s) => [
                        'service_id' => $s->service_id,
                        'service_name' => $s->service?->name ?? 'Service #' . $s->service_id,
                        'qty' => $s->qty
                    ])->toArray() : [];

                    \App\Models\EventPackage::create([
                        'event_id' => $ev->id,
                        'event_sub_category_id' => $ev->event_sub_category_id,
                        'package_name' => $pkgName,
                        'category_name' => $catName,
                        'cost_price' => $cost,
                        'sell_price' => $sell,
                        'unit_price' => $sell,
                        'qty' => 1,
                        'total_price' => $sell,
                        'deliverables' => $deliverables,
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('Event package backfill skipped: ' . $e->getMessage());
            }
        }

        // 2. Backfill legacy Orders into order_packages
        if (Schema::hasTable('orders') && Schema::hasTable('order_packages')) {
            try {
                $legacyOrders = \App\Models\Order::whereDoesntHave('packages')
                    ->with(['event.packages', 'event.subCategory.category', 'event.subCategory.subCategoryServices.service'])
                    ->get();

                foreach ($legacyOrders as $ord) {
                    if ($ord->event && $ord->event->packages->isNotEmpty()) {
                        foreach ($ord->event->packages as $ep) {
                            \App\Models\OrderPackage::create([
                                'order_id' => $ord->id,
                                'event_package_id' => $ep->id,
                                'event_sub_category_id' => $ep->event_sub_category_id,
                                'package_name' => $ep->package_name,
                                'category_name' => $ep->category_name,
                                'cost_price' => (float)$ep->cost_price,
                                'sell_price' => (float)$ep->sell_price,
                                'unit_price' => (float)($ep->unit_price ?? $ep->sell_price),
                                'qty' => $ep->qty ?? 1,
                                'total_price' => (float)$ep->total_price,
                                'deliverables' => $ep->deliverables,
                            ]);
                        }
                    } elseif ($ord->event && ($ord->event->event_sub_category_id || $ord->event->package_sell_price > 0)) {
                        $subCat = $ord->event->subCategory;
                        $pkgName = $ord->event->package_name ?? $subCat?->name ?? 'Event Package';
                        $catName = $subCat?->category?->name ?? 'General';
                        $cost = (float)($ord->event->package_cost_price ?? $subCat?->cost_price ?? 0);
                        $sell = (float)($ord->event->package_sell_price ?? $subCat?->sell_price ?? 0);
                        $deliverables = ($subCat && $subCat->subCategoryServices) ? $subCat->subCategoryServices->map(fn($s) => [
                            'service_id' => $s->service_id,
                            'service_name' => $s->service?->name ?? 'Service #' . $s->service_id,
                            'qty' => $s->qty
                        ])->toArray() : [];

                        \App\Models\OrderPackage::create([
                            'order_id' => $ord->id,
                            'event_package_id' => null,
                            'event_sub_category_id' => $ord->event->event_sub_category_id,
                            'package_name' => $pkgName,
                            'category_name' => $catName,
                            'cost_price' => $cost,
                            'sell_price' => $sell,
                            'unit_price' => $sell,
                            'qty' => 1,
                            'total_price' => $sell,
                            'deliverables' => $deliverables,
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Order package backfill skipped: ' . $e->getMessage());
            }
        }

        // 3. Backfill legacy Quotations into quotation_packages
        if (Schema::hasTable('quotations') && Schema::hasTable('quotation_packages')) {
            try {
                $legacyQuotations = \App\Models\Quotation::whereDoesntHave('packages')
                    ->where(function ($q) {
                        $q->whereNotNull('event_sub_category_id')
                          ->orWhere('package_sell_price', '>', 0);
                    })
                    ->with(['subCategory.category', 'subCategory.subCategoryServices.service'])
                    ->get();

                foreach ($legacyQuotations as $quot) {
                    $subCat = $quot->subCategory;
                    $pkgName = $quot->package_name ?? $subCat?->name ?? 'Event Package';
                    $catName = $subCat?->category?->name ?? 'General';
                    $cost = (float)($quot->package_cost_price ?? $subCat?->cost_price ?? 0);
                    $sell = (float)($quot->package_sell_price ?? $subCat?->sell_price ?? 0);
                    $deliverables = ($subCat && $subCat->subCategoryServices) ? $subCat->subCategoryServices->map(fn($s) => [
                        'service_id' => $s->service_id,
                        'service_name' => $s->service?->name ?? 'Service #' . $s->service_id,
                        'qty' => $s->qty
                    ])->toArray() : [];

                    \App\Models\QuotationPackage::create([
                        'quotation_id' => $quot->id,
                        'event_sub_category_id' => $quot->event_sub_category_id,
                        'package_name' => $pkgName,
                        'category_name' => $catName,
                        'cost_price' => $cost,
                        'sell_price' => $sell,
                        'unit_price' => $sell,
                        'qty' => 1,
                        'total_price' => $sell,
                        'deliverables' => $deliverables,
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('Quotation package backfill skipped: ' . $e->getMessage());
            }
        }
    }
}
