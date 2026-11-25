<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\DeliveryFeeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(DeliveryFeeService $delivery): View
    {
        return view('admin.settings.index', ['delivery' => $delivery->settings()]);
    }

    public function updateDelivery(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'enabled' => ['nullable', 'boolean'],
            'start_name' => ['required', 'string', 'max:255'],
            'start_lat' => ['required', 'numeric', 'between:-90,90'],
            'start_lng' => ['required', 'numeric', 'between:-180,180'],
            'base_km' => ['required', 'numeric', 'min:0', 'max:1000'],
            'base_fee' => ['required', 'numeric', 'min:0', 'max:1000000'],
            'per_km_fee' => ['required', 'numeric', 'min:0', 'max:1000000'],
            'max_radius_km' => ['required', 'numeric', 'min:0', 'max:5000'],
            'free_above' => ['required', 'numeric', 'min:0', 'max:100000000'],
        ]);

        $values = [];
        foreach ($data as $key => $value) {
            $values['delivery.' . $key] = $value;
        }
        $values['delivery.enabled'] = $request->boolean('enabled') ? '1' : '0';

        Setting::setMany($values);

        return redirect()->route('admin.settings.index')->with('success', 'Delivery settings saved.');
    }
}
