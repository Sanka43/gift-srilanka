<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockNotification;
use Illuminate\View\View;

class StockNotificationController extends Controller
{
    public function index(): View
    {
        $notifications = StockNotification::with('product')->whereNull('notified_at')->latest()->paginate(15);
        return view('admin.stock-notifications.index', compact('notifications'));
    }
}
