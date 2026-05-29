<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\GoogleAuthController;

Route::get('/', function () {
    return view('welcome');
});

// Google OAuth
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');

use App\Http\Controllers\PremiumUpgradeController;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/bookings', function () {
        return view('bookings');
    })->name('bookings');

    Route::get('/profile', function () {
        return view('profile-custom');
    })->name('profile');

    // Stripe Premium Upgrade Routes
    Route::get('/upgrade/checkout', [PremiumUpgradeController::class, 'checkout'])->name('premium.checkout');
    Route::get('/upgrade/success', [PremiumUpgradeController::class, 'success'])->name('premium.success');
    Route::get('/upgrade/cancel', [PremiumUpgradeController::class, 'cancel'])->name('premium.cancel');
});

// Admin-only routes
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'is_admin'
])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
});
