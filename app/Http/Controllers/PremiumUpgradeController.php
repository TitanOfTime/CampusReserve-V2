<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;

class PremiumUpgradeController extends Controller
{
    /**
     * Initialize Stripe Checkout Session and redirect the user.
     */
    public function checkout(): RedirectResponse
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'CampusReserve Premium Upgrade',
                    ],
                    'unit_amount' => 1000, // $10.00 USD in cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('premium.success'),
            'cancel_url' => route('premium.cancel'),
        ]);

        return redirect($session->url);
    }

    /**
     * Handle payment success.
     */
    public function success(): RedirectResponse
    {
        $user = auth()->user();
        
        $user->update([
            'is_premium' => true,
        ]);

        return redirect()->route('dashboard')->with('success', 'Welcome to CampusReserve Premium! You now have access to premium boardroom and meeting room bookings.');
    }

    /**
     * Handle payment cancellation.
     */
    public function cancel(): RedirectResponse
    {
        return redirect()->route('dashboard')->with('warning', 'Premium upgrade checkout was cancelled.');
    }
}
