<?php

namespace App\Jobs;

use App\Mail\BackInStockMail;
use App\Models\StockNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBackInStockNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $productId,
        public int $notificationId
    ) {}

    public function handle(): void
    {
        $notification = StockNotification::query()
            ->where('id', $this->notificationId)
            ->where('product_id', $this->productId)
            ->whereNull('notified_at')
            ->first();

        if (! $notification) {
            return;
        }

        Mail::to($notification->email)->send(new BackInStockMail($notification->product, $notification->email));
        $notification->update(['notified_at' => now()]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Back-in-stock notification job failed', [
            'product_id' => $this->productId,
            'notification_id' => $this->notificationId,
            'message' => $exception->getMessage(),
        ]);
    }
}
