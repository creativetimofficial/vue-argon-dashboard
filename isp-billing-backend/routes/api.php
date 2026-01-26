<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\SuperAdmin\SubscriptionPackageController;
use App\Http\Controllers\API\SuperAdmin\ISPManagementController;
use App\Http\Controllers\API\SuperAdmin\PaymentGatewayController;
use App\Http\Controllers\API\SuperAdmin\LandingPageController;
use App\Http\Controllers\API\SuperAdmin\IspThemeController;
use App\Http\Controllers\API\SuperAdmin\DashboardController;
use App\Http\Controllers\API\ISPAdmin\ISPSubscriptionController;
use App\Http\Controllers\API\ISPAdmin\ClientAreaController;
use App\Http\Controllers\API\SuperAdmin\ISPServiceController;
use App\Http\Controllers\API\SuperAdmin\ISPOrderManagementController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/super-admin/login', [LoginController::class, 'superAdminLogin']);
    Route::post('/isp-admin/login', [LoginController::class, 'ispAdminLogin']);
    Route::post('/technician/login', [LoginController::class, 'technicianLogin']);
    Route::post('/customer/login', [LoginController::class, 'customerLogin']);
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/verify-email/{token}', [RegisterController::class, 'verifyEmail'])->name('verify.email');
    Route::post('/forgot-password', [LoginController::class, 'forgotPassword']);
    Route::post('/reset-password', [LoginController::class, 'resetPassword']);
});

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/auth/logout', [LoginController::class, 'logout']);
    Route::get('/auth/user', [LoginController::class, 'user']);
    Route::put('/auth/profile', [LoginController::class, 'updateProfile']);
    Route::put('/auth/password', [LoginController::class, 'updatePassword']);

    // Super Admin routes
    Route::middleware(['role:super_admin'])->prefix('super-admin')->group(function () {
        Route::get('dashboard/stats', [DashboardController::class, 'stats']);
        Route::get('dashboard/recent-isps', [DashboardController::class, 'recentIsps']);
    });

    Route::middleware(['role:super_admin'])->prefix('super-admin')->group(function () {
        // Dashboard
        Route::get('dashboard/stats', [DashboardController::class, 'stats']);
        Route::get('dashboard/recent-isps', [DashboardController::class, 'recentIsps']);
        
        // Subscription Packages
        Route::apiResource('subscription-packages', SubscriptionPackageController::class);

        // ISP Management
        Route::get('isp-management/stats', [ISPManagementController::class, 'stats']);
        Route::post('isp-management/{id}/approve', [ISPManagementController::class, 'approve']);
        Route::post('isp-management/{id}/reject', [ISPManagementController::class, 'reject']);
        Route::post('isp-management/{id}/update-subscription', [ISPManagementController::class, 'updateSubscription']);
        Route::apiResource('isp-management', ISPManagementController::class);

        // Payment Gateways
        Route::apiResource('payment-gateways', PaymentGatewayController::class);

        // Landing Pages
        Route::apiResource('landing-pages', LandingPageController::class);

        // ISP Themes
        Route::get('isp-themes/default', [IspThemeController::class, 'getDefault']);
        Route::apiResource('isp-themes', IspThemeController::class);

        // ISP Services (for ISP to order)
        Route::apiResource('isp-services', ISPServiceController::class);

        // ISP Orders Management (approve/reject trial orders)
        Route::get('isp-orders', [ISPOrderManagementController::class, 'index']);
        Route::get('isp-orders/{id}', [ISPOrderManagementController::class, 'show']);
        Route::post('isp-orders/{id}/approve', [ISPOrderManagementController::class, 'approve']);
        Route::post('isp-orders/{id}/reject', [ISPOrderManagementController::class, 'reject']);
    });

    // ISP Admin routes
    Route::middleware(['role:isp_admin'])->prefix('isp-admin')->group(function () {
        Route::get('dashboard', function () {
            $user = auth()->user();
            $isp = $user->isp()->with('subscriptionPackage')->first();
            return response()->json([
                'user' => $user,
                'isp' => $isp,
                'subscription' => $isp ? $isp->subscriptionPackage : null,
            ]);
        });
        
        // Subscription
        Route::post('subscribe', [ISPSubscriptionController::class, 'subscribe']);
        Route::post('payment-callback', [ISPSubscriptionController::class, 'paymentCallback']);
        
        // Client Area
        Route::prefix('client-area')->group(function () {
            Route::get('stats', [ClientAreaController::class, 'stats']);
            Route::get('services', [ClientAreaController::class, 'services']);
            Route::get('packages', [ClientAreaController::class, 'packages']);
            Route::get('orders', [ClientAreaController::class, 'orders']);
            Route::post('orders', [ClientAreaController::class, 'createOrder']);
            Route::post('orders/{id}/cancel', [ClientAreaController::class, 'cancelOrder']);
            Route::delete('orders/{id}', [ClientAreaController::class, 'deleteOrder']);
            Route::get('my-services', [ClientAreaController::class, 'myServices']);
            Route::get('services/{id}', [ClientAreaController::class, 'serviceDetail']);
            Route::get('invoices', [ClientAreaController::class, 'invoices']);
            Route::get('invoices/{id}', [ClientAreaController::class, 'invoiceDetail']);
            Route::get('profile', [ClientAreaController::class, 'profile']);
            Route::put('profile', [ClientAreaController::class, 'updateProfile']);
            Route::get('balance', [ClientAreaController::class, 'balance']);
            Route::get('topup-history', [ClientAreaController::class, 'topupHistory']);
            Route::put('referral-code', [ClientAreaController::class, 'updateReferralCode']);
        });
    });
});

// Public routes
Route::get('/subscription-packages/public', [SubscriptionPackageController::class, 'index']);
Route::get('/landing-page', [LandingPageController::class, 'index']);

Route::get('/notifications', function() {
    return response()->json(['data' => []]);
});
