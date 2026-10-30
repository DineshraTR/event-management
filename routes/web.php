<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\EventCategoryController;
use App\Http\Controllers\EventSubCategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\BankTransactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\ChequeController;
use App\Http\Controllers\ExpenseCategoryController;

// Public Auth Routes
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.post');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Application Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Users Management (Admin Only)
    Route::middleware('role:Admin')->group(function () {
        Route::resource('users', UserController::class);
    });

    // Masters / Settings
    Route::middleware('role:Admin,Manager')->group(function () {
        Route::resource('banks', BankController::class);
        Route::post('banks/{bank}/toggle-status', [BankController::class, 'toggleStatus'])->name('banks.toggle-status');
    });
    Route::resource('services', ServiceController::class);
    Route::resource('event-categories', EventCategoryController::class);
    Route::resource('event-sub-categories', EventSubCategoryController::class);
    Route::resource('expense-categories', ExpenseCategoryController::class);
    Route::get('event-sub-categories/{subCategory}/services', [EventSubCategoryController::class, 'getServices'])->name('event-sub-categories.services');
    Route::get('api/sub-category/{id}', [EventSubCategoryController::class, 'apiGetSubCategory'])->name('api.sub-category');
    Route::get('api/sub-categories/{categoryId}', [EventSubCategoryController::class, 'apiGetByCategory'])->name('api.sub-categories');

    // Customers
    Route::resource('customers', CustomerController::class);
    Route::match(['patch', 'post'], 'customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');
    Route::post('customers/quick-store', [CustomerController::class, 'quickStore'])->name('customers.quick-store');
    Route::get('api/customers/search', [CustomerController::class, 'search'])->name('api.customers.search');

    // Events
    Route::resource('events', EventController::class);
    Route::get('events/{event}/create-order', [EventController::class, 'createOrder'])->name('events.create-order');

    // Orders
    Route::resource('orders', OrderController::class);
    Route::get('orders/{order}/print', [OrderController::class, 'print'])->name('orders.print');
    Route::get('api/orders/event/{eventId}', [OrderController::class, 'getByEvent'])->name('api.orders.event');
    Route::get('api/event/{eventId}/order-details', [OrderController::class, 'apiGetEventDetails'])->name('api.event.order-details');

    // Payments
    Route::resource('payments', PaymentController::class);
    Route::get('payments/{payment}/print', [PaymentController::class, 'printReceipt'])->name('payments.print');
    Route::get('payments/create/{orderId}', [PaymentController::class, 'createForOrder'])->name('payments.create-for-order');

    // Cheques Management
    Route::get('cheques', [ChequeController::class, 'index'])->name('cheques.index');
    Route::post('cheques/store-in', [ChequeController::class, 'storeIn'])->name('cheques.store-in');
    Route::post('cheques/store-out', [ChequeController::class, 'storeOut'])->name('cheques.store-out');
    Route::post('cheques/{cheque}/deposit', [ChequeController::class, 'deposit'])->name('cheques.deposit');
    Route::post('cheques/{cheque}/realize', [ChequeController::class, 'realize'])->name('cheques.realize');
    Route::post('cheques/{cheque}/return', [ChequeController::class, 'returnCheque'])->name('cheques.return');
    Route::delete('cheques/{cheque}', [ChequeController::class, 'destroy'])->name('cheques.destroy');

    // Quotations
    Route::resource('quotations', QuotationController::class);
    Route::get('quotations/{quotation}/print', [QuotationController::class, 'print'])->name('quotations.print');
    Route::get('quotations/{quotation}/convert-to-event', [QuotationController::class, 'convertToEvent'])->name('quotations.convert-to-event');

    // Expenses
    Route::post('expenses/categories/quick-store', [ExpenseController::class, 'quickStoreCategory'])->name('expense-categories.quick-store');
    Route::resource('expenses', ExpenseController::class);

    // Bank/Cash Book
    Route::resource('bank-transactions', BankTransactionController::class);
    Route::get('cash-book', [BankTransactionController::class, 'cashBook'])->name('cash-book');
    Route::get('bank-book', [BankTransactionController::class, 'bankBook'])->name('bank-book');

    // Calendar
    Route::get('calendar', [CalendarController::class, 'index'])->name('calendar');
    Route::get('api/calendar/events', [CalendarController::class, 'getEvents'])->name('api.calendar.events');

    // Reports
    Route::get('reports/cheques', [ReportController::class, 'chequesReport'])->name('reports.cheques');
    Route::get('reports/payments', [ReportController::class, 'paymentsReport'])->name('reports.payments');
    Route::get('reports/expenses', [ReportController::class, 'expensesReport'])->name('reports.expenses');
    Route::get('reports/transactions', [ReportController::class, 'transactionsReport'])->name('reports.transactions');
    Route::get('reports/order-pl', [ReportController::class, 'orderPL'])->name('reports.order-pl');
    Route::get('reports/monthly-pl', [ReportController::class, 'monthlyPL'])->name('reports.monthly-pl');
    Route::get('reports/customer-credit', [ReportController::class, 'customerCredit'])->name('reports.customer-credit');
    Route::get('reports/order-history', [ReportController::class, 'orderHistory'])->name('reports.order-history');

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{id}/mark-read', [NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
});
