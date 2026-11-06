<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Services\DatabaseSchemaBootstrapper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        DatabaseSchemaBootstrapper::boot();

        // Register Auth Audit Log Listeners
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Login::class, function ($event) {
            \App\Services\AuditLogger::logAuth('login', $event->user);
        });

        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Logout::class, function ($event) {
            \App\Services\AuditLogger::logAuth('logout', $event->user);
        });

        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Failed::class, function ($event) {
            \App\Services\AuditLogger::logAuth('failed_login', $event->user, $event->credentials['email'] ?? null);
        });
    }
}
