<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatAttachmentController;
use App\Http\Controllers\CustomerChatController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\NotifyWhenAvailableController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Static pages (footer & info)
Route::get('/blog', [PageController::class, 'blog'])->name('page.blog');
Route::get('/meet-the-team', [PageController::class, 'meetTheTeam'])->name('page.team');
Route::get('/contact', [PageController::class, 'contact'])->name('page.contact');
Route::get('/shipping', [PageController::class, 'shipping'])->name('page.shipping');
Route::get('/return', [PageController::class, 'returnPolicy'])->name('page.return');
Route::get('/faq', [PageController::class, 'faq'])->name('page.faq');
Route::get('/terms', [PageController::class, 'terms'])->name('page.terms');
Route::get('/privacy', [PageController::class, 'privacy'])->name('page.privacy');

Route::get('/sitemap.xml', App\Http\Controllers\SitemapController::class)->name('sitemap');

Route::get('/robots.txt', [PageController::class, 'robots'])->name('robots');

Route::get('/dashboard', [PageController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Shop (public)
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::post('/products/{product}/notify-when-available', [NotifyWhenAvailableController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('notify.when-available');
Route::middleware(['auth', 'throttle:6,1'])->post('/review/{product}', [ReviewController::class, 'store'])->name('review.store');

// Cart (public, session-based)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->middleware('throttle:20,1')->name('cart.add');
Route::patch('/cart/update/{product}', [CartController::class, 'update'])->middleware('throttle:20,1')->name('cart.update');
Route::delete('/cart/remove/{product}', [CartController::class, 'remove'])->middleware('throttle:20,1')->name('cart.remove');

// Checkout (requires authentication and verified email)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::post('/checkout/delivery-quote', [CheckoutController::class, 'quote'])->middleware('throttle:30,1')->name('checkout.quote');
    Route::get('/checkout/confirmation/{order}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
    Route::get('/checkout/payment/{order}', [PaymentController::class, 'pay'])->name('checkout.payment.pay');
    Route::get('/checkout/payment/return/{order}', [PaymentController::class, 'return'])->name('checkout.payment.return');
    Route::get('/checkout/payment/cancel/{order}', [PaymentController::class, 'cancel'])->name('checkout.payment.cancel');
});

// PayHere Server-to-Server IPN Webhook (Exempt from CSRF in bootstrap/app.php)
Route::post('/payment/payhere/notify', [PaymentController::class, 'notify'])->name('payment.payhere.notify');

// Signed URL: <img> requests do not always send session cookies like XHR; signature replaces auth for GET.
Route::get('/chat/messages/{message}/attachment', ChatAttachmentController::class)
    ->middleware('signed')
    ->name('chat.attachments.show');

Route::get('/chat/conversation', [CustomerChatController::class, 'conversation'])->name('chat.conversation');
Route::post('/chat/messages', [CustomerChatController::class, 'store'])
    ->middleware('throttle:chat-messages')
    ->name('chat.messages.store');

Route::middleware('auth')->group(function () {
    Route::post('/contact', [App\Http\Controllers\ContactController::class, 'store'])->name('contact.store');

    Route::get('/chat/conversations/{conversation}', [CustomerChatController::class, 'show'])->name('chat.conversations.show');
    Route::get('/chat/conversations/{conversation}/messages', [CustomerChatController::class, 'messages'])->name('chat.conversations.messages');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Wishlist
    Route::get('/wishlist', [App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/add/{product}', [App\Http\Controllers\WishlistController::class, 'add'])->name('wishlist.add');
    Route::delete('/wishlist/remove/{product}', [App\Http\Controllers\WishlistController::class, 'remove'])->name('wishlist.remove');

    // Customer: order history & details (only own orders)
    Route::prefix('customer')->name('customer.')->group(function () {
        Route::get('/orders', [App\Http\Controllers\Customer\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [App\Http\Controllers\Customer\OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/request-cancel', [App\Http\Controllers\Customer\OrderController::class, 'requestCancel'])->name('orders.requestCancel');
    });

    Route::get('/my-orders', fn () => redirect()->route('customer.orders.index'))->name('orders.my');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
