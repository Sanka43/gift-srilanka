<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| All routes under /admin. Protected by auth + admin middleware.
*/

Route::prefix('admin')
    ->middleware(['auth', 'admin'])
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [DashboardController::class, 'index']);

        Route::get('categories/subcategories', [App\Http\Controllers\Admin\CategoryController::class, 'subcategories'])->name('categories.subcategories');
        Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);
        Route::resource('products', App\Http\Controllers\Admin\ProductController::class);
        Route::patch('orders/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::post('orders/{order}/cancel-approve', [App\Http\Controllers\Admin\OrderController::class, 'approveCancel'])->name('orders.cancelApprove');
        Route::post('orders/{order}/cancel-reject', [App\Http\Controllers\Admin\OrderController::class, 'rejectCancel'])->name('orders.cancelReject');
        Route::get('orders/export', [App\Http\Controllers\Admin\OrderController::class, 'exportCsv'])->name('orders.export');
        Route::resource('orders', App\Http\Controllers\Admin\OrderController::class)->only(['index', 'show', 'edit', 'update']);

        Route::get('customers', [App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('customers.index');
        Route::get('contacts', [App\Http\Controllers\Admin\ContactController::class, 'index'])->name('contacts.index');
        Route::get('contacts/{contact}', [App\Http\Controllers\Admin\ContactController::class, 'show'])->name('contacts.show');
        Route::post('contacts/{contact}/reply', [App\Http\Controllers\Admin\ContactController::class, 'reply'])->name('contacts.reply');
        Route::get('chats', [App\Http\Controllers\Admin\ChatController::class, 'index'])->name('chats.index');
        Route::get('chats/summary', [App\Http\Controllers\Admin\ChatController::class, 'summary'])->name('chats.summary');
        Route::get('chats/{conversation}', [App\Http\Controllers\Admin\ChatController::class, 'show'])->name('chats.show');
        Route::post('chats/{conversation}/messages', [App\Http\Controllers\Admin\ChatController::class, 'store'])
            ->middleware('throttle:chat-messages')
            ->name('chats.messages.store');
        Route::get('reviews', [App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index');
        Route::get('stock-notifications', [App\Http\Controllers\Admin\StockNotificationController::class, 'index'])->name('stock-notifications.index');
        Route::get('reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    });
