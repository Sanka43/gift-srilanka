<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = User::whereHas('role', fn ($q) => $q->where('name', 'customer'))
            ->withCount('orders')
            ->latest()
            ->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }
}
