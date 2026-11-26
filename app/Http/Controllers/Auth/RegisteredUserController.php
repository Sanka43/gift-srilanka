<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Models\Product;
use App\Services\CartService;
use App\Services\RegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     * Email format is validated; password is hashed before storing.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterUserRequest $request, RegistrationService $registrationService): RedirectResponse
    {
        $user = $registrationService->registerCustomer($request->validated());

        $pendingCartItem = session()->get('pending_cart_item');

        Auth::login($user);
        $request->session()->regenerate();

        $registrationService->sendPostRegistrationNotifications($user);

        if ($pendingCartItem !== null && isset($pendingCartItem['product_id'])) {
            session()->forget('pending_cart_item');
            $product = Product::find($pendingCartItem['product_id'] ?? null);
            if ($product && $product->status) {
                $quantity = (int) ($pendingCartItem['quantity'] ?? 1);
                if ($quantity >= 1 && $product->stock >= $quantity) {
                    app(CartService::class)->add($product->id, $quantity);
                }
            }
        }

        if (! empty($pendingCartItem['buy_now'])) {
            session()->forget('url.intended');

            return redirect()->route('checkout.index')->with('status', 'Account created. You are logged in.');
        }

        return redirect()->route('home')->with('status', 'Account created. You are logged in.');
    }
}
