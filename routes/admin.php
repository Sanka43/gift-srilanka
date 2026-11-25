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

        Route::get('payments/export', [App\Http\Controllers\Admin\PaymentController::class, 'exportCsv'])->name('payments.export');
        Route::patch('payments/{order}/status', [App\Http\Controllers\Admin\PaymentController::class, 'updateStatus'])->name('payments.updateStatus');
        Route::get('payments', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');

        Route::get('customers', [App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('customers.index');
        Route::get('users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::get('users/export', [App\Http\Controllers\Admin\UserController::class, 'exportCsv'])->name('users.export');
        Route::get('users/{user}', [App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
        Route::post('users/bulk', [App\Http\Controllers\Admin\UserController::class, 'bulk'])->name('users.bulk');
        Route::post('users/{user}/notes', [App\Http\Controllers\Admin\UserController::class, 'storeNote'])->name('users.notes.store');
        Route::delete('users/{user}/notes/{note}', [App\Http\Controllers\Admin\UserController::class, 'destroyNote'])->name('users.notes.destroy');
        Route::post('users/{user}/password-reset', [App\Http\Controllers\Admin\UserController::class, 'sendPasswordReset'])->middleware('throttle:10,1')->name('users.passwordReset');
        Route::post('users/{user}/resend-verification', [App\Http\Controllers\Admin\UserController::class, 'resendVerification'])->middleware('throttle:10,1')->name('users.resendVerification');
        Route::post('users/{user}/block', [App\Http\Controllers\Admin\UserController::class, 'block'])->name('users.block');
        Route::post('users/{user}/unblock', [App\Http\Controllers\Admin\UserController::class, 'unblock'])->name('users.unblock');
        Route::patch('users/{user}/role', [App\Http\Controllers\Admin\UserController::class, 'updateRole'])->name('users.updateRole');
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
        Route::put('settings/delivery', [App\Http\Controllers\Admin\SettingsController::class, 'updateDelivery'])->name('settings.delivery.update');
    });
