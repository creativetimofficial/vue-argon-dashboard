<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\SuperAdmin\SubscriptionPackageController;
use App\Http\Controllers\API\SuperAdmin\ISPManagementController;
use App\Http\Controllers\API\SuperAdmin\PaymentGatewayController;
use App\Http\Controllers\API\Auth\ForgotPasswordController;
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
    Route::get('/login', function () {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    })->name('login'); // Fix for Route [login] not defined
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/super-admin/login', [LoginController::class, 'superAdminLogin']);
    Route::post('/isp-admin/login', [LoginController::class, 'ispAdminLogin']);
    Route::post('/technician/login', [LoginController::class, 'technicianLogin']);
    Route::post('/customer/login', [LoginController::class, 'customerLogin']);
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/verify-email/{token}', [RegisterController::class, 'verifyEmail']); // GET with token in URL
    Route::post('/verify-email', [RegisterController::class, 'verifyEmail']); // POST for backward compatibility
    // Password Reset
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetCode']);
    Route::post('/verify-reset-code', [ForgotPasswordController::class, 'verifyResetCode']);
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);
    
    // Magic Link Login
    Route::get('/magic-login/{user}', [\App\Http\Controllers\API\Auth\MagicLoginController::class, 'login'])
        ->name('auth.magic-login')
        ->middleware('signed');
});
Route::get('/tenant-info', [LoginController::class, 'getTenantInfo']);

// ISP Domain Management
Route::prefix('v1')->group(function () {
    Route::post('/isp-domains/add', [\App\Http\Controllers\Api\ISPDomainController::class, 'addDomain']);
    Route::get('/isp-domains/verify', [\App\Http\Controllers\Api\ISPDomainController::class, 'verifyDomain']);
    Route::get('/isp-domains', [\App\Http\Controllers\Api\ISPDomainController::class, 'getDomains']);
    Route::delete('/isp-domains/{id}', [\App\Http\Controllers\Api\ISPDomainController::class, 'deleteDomain']);
});

// ISP Admin Authentication Routes
Route::prefix('isp-admin')->group(function () {
    Route::post('/register', [\App\Http\Controllers\Api\ISPAdminAuthController::class, 'register']);
    Route::post('/login', [\App\Http\Controllers\Api\ISPAdminAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [\App\Http\Controllers\Api\ISPAdminAuthController::class, 'logout']);
        Route::get('/user', [\App\Http\Controllers\Api\ISPAdminAuthController::class, 'user']);
    });
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

        // Withdrawal Management
        Route::get('withdrawals', [\App\Http\Controllers\API\SuperAdmin\WithdrawalController::class, 'index']);
        Route::post('withdrawals/{id}/approve', [\App\Http\Controllers\API\SuperAdmin\WithdrawalController::class, 'approve']);
        Route::post('withdrawals/{id}/reject', [\App\Http\Controllers\API\SuperAdmin\WithdrawalController::class, 'reject']);

        // Server Management
        Route::post('servers/{id}/test-connection', [\App\Http\Controllers\API\SuperAdmin\ServerController::class, 'checkConnection']);
        Route::post('servers/{id}/create-vpn', [\App\Http\Controllers\API\SuperAdmin\ServerController::class, 'createVpnAccount']);
        Route::apiResource('servers', \App\Http\Controllers\API\SuperAdmin\ServerController::class);

        // Landing Pages
        Route::post('landing-page/upload', [LandingPageController::class, 'uploadImage']);
        Route::get('landing-page', [LandingPageController::class, 'index']); // Get active landing page
        Route::get('landing-page/{id}', [LandingPageController::class, 'show']); // Get specific landing page
        Route::put('landing-page/{id}', [LandingPageController::class, 'update']); // Update landing page
        Route::apiResource('landing-pages', LandingPageController::class);

        // ISP Themes
        Route::get('isp-themes/default', [IspThemeController::class, 'getDefault']);
        Route::get('isp-themes/active', [IspThemeController::class, 'getActiveTheme']);
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
            // Lazy Cleanup: Check for expired subscriptions on Dashboard load
            try {
                \Illuminate\Support\Facades\Artisan::call('isp:check-expiry');
            } catch (\Exception $e) {
                // Ignore errors to not block dashboard load
            }
            
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
        
        // Client Area
        Route::prefix('client-area')->group(function () {
            Route::get('stats', [ClientAreaController::class, 'stats']);
            Route::get('services', [ClientAreaController::class, 'services']);
            Route::get('packages', [ClientAreaController::class, 'packages']);
            Route::get('servers', [ClientAreaController::class, 'getServers']);
            Route::get('orders', [ClientAreaController::class, 'orders']);
            Route::post('orders', [ClientAreaController::class, 'createOrder']);
            Route::post('orders/{id}/cancel', [ClientAreaController::class, 'cancelOrder']);
            Route::delete('orders/{id}', [ClientAreaController::class, 'deleteOrder']);
            Route::get('my-services', [ClientAreaController::class, 'myServices']);
            Route::get('services/{id}', [ClientAreaController::class, 'serviceDetail']);
            Route::get('invoices', [ClientAreaController::class, 'invoices']);
            Route::get('invoices/{id}', [ClientAreaController::class, 'invoiceDetail']);
            Route::get('invoices/{id}/download', [ClientAreaController::class, 'downloadInvoice']);
            Route::post('invoices/{id}/pay', [ClientAreaController::class, 'payInvoice']);
            Route::post('invoices/verify-xendit', [ISPSubscriptionController::class, 'verifyXendit']);
            Route::post('invoices/{id}/cancel', [ClientAreaController::class, 'cancelInvoice']);
            Route::post('topup', [ClientAreaController::class, 'topup']);
            Route::get('profile', [ClientAreaController::class, 'profile']);
            Route::put('profile', [ClientAreaController::class, 'updateProfile']);
            Route::get('balance', [ClientAreaController::class, 'balance']);
            Route::get('topup-history', [ClientAreaController::class, 'topupHistory']);
            Route::post('withdraw', [ClientAreaController::class, 'withdrawRequest']);
            Route::get('withdrawal-history', [ClientAreaController::class, 'withdrawalHistory']);
            Route::put('referral-code', [ClientAreaController::class, 'updateReferralCode']);
            
            // Domain Management
            Route::get('domain-info', [\App\Http\Controllers\Api\ISPDomainController::class, 'getDomainInfo']);
            Route::post('custom-domain', [\App\Http\Controllers\Api\ISPDomainController::class, 'updateCustomDomain']);
            Route::post('custom-domain/verify', [\App\Http\Controllers\Api\ISPDomainController::class, 'verifyCustomDomain']);
        });

        // Theme Management
        Route::get('theme', [\App\Http\Controllers\API\SuperAdmin\IspThemeController::class, 'getActiveTheme']);
        Route::post('theme', [\App\Http\Controllers\API\SuperAdmin\IspThemeController::class, 'updateActiveTheme']);
    });
});

// Public routes
Route::get('/subscription-packages/public', [SubscriptionPackageController::class, 'index']);
Route::get('/landing-page', [LandingPageController::class, 'index']);
Route::get('/theme/public', [IspThemeController::class, 'getPublicTheme']);

Route::get('/notifications', function() {
    return response()->json(['data' => []]);
});

Route::post('/payment-callback', [ISPSubscriptionController::class, 'paymentCallback']); // Legacy/Midtrans
Route::post('/callback/midtrans', [ISPSubscriptionController::class, 'paymentCallback']);
Route::post('/callback/xendit', [ISPSubscriptionController::class, 'callbackXendit']);
Route::post('/callback/tripay', [ISPSubscriptionController::class, 'callbackTripay']);
Route::post('/callback/duitku', [ISPSubscriptionController::class, 'callbackDuitku']);

// DEBUG ROUTE - REMOVE LATER
// Route::get('/debug-logs', ...);
// Route::get('/debug-token', ...);