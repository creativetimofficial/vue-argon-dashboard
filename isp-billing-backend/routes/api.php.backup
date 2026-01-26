<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\Auth\EmailVerificationController;

// Super Admin Controllers
use App\Http\Controllers\API\SuperAdmin\ISPAdminController;
use App\Http\Controllers\API\SuperAdmin\PackageController as SuperAdminPackageController;
use App\Http\Controllers\API\SuperAdmin\PaymentGatewayController as SuperAdminPaymentGatewayController;
use App\Http\Controllers\API\SuperAdmin\WebCustomizationController;
use App\Http\Controllers\API\SuperAdmin\DashboardController as SuperAdminDashboardController;

// ISP Admin Controllers
use App\Http\Controllers\API\ISPAdmin\CustomerController;
use App\Http\Controllers\API\ISPAdmin\PackageController as ISPPackageController;
use App\Http\Controllers\API\ISPAdmin\MikrotikController;
use App\Http\Controllers\API\ISPAdmin\TechnicianController;
use App\Http\Controllers\API\ISPAdmin\PaymentApprovalController;
use App\Http\Controllers\API\ISPAdmin\InstallationRequestController as ISPInstallationRequestController;
use App\Http\Controllers\API\ISPAdmin\RepairTicketController as ISPRepairTicketController;
use App\Http\Controllers\API\ISPAdmin\DashboardController as ISPAdminDashboardController;

// Technician Controllers
use App\Http\Controllers\API\Technician\InstallationRequestController as TechnicianInstallationController;
use App\Http\Controllers\API\Technician\RepairTicketController as TechnicianRepairController;
use App\Http\Controllers\API\Technician\DashboardController as TechnicianDashboardController;

// Customer Controllers
use App\Http\Controllers\API\Customer\BillingController;
use App\Http\Controllers\API\Customer\PaymentController;
use App\Http\Controllers\API\Customer\ComplaintController;
use App\Http\Controllers\API\Customer\DashboardController as CustomerDashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/customization', [WebCustomizationController::class, 'getPublic']);
Route::get('/packages/public', [SuperAdminPackageController::class, 'getPublicPackages']);

// Authentication routes
Route::prefix('auth')->group(function () {
    // Login routes for different roles
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/super-admin/login', [LoginController::class, 'superAdminLogin']);
    Route::post('/isp-admin/login', [LoginController::class, 'ispAdminLogin']);
    Route::post('/technician/login', [LoginController::class, 'technicianLogin']);
    Route::post('/customer/login', [LoginController::class, 'customerLogin']);

    // Registration (ISP registration)
    Route::post('/register/isp', [RegisterController::class, 'registerISP']);

    // Email verification
    Route::post('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->name('verification.verify');
    Route::post('/email/resend', [EmailVerificationController::class, 'resend']);

    // Password reset
    Route::post('/forgot-password', [LoginController::class, 'forgotPassword']);
    Route::post('/reset-password', [LoginController::class, 'resetPassword']);
});

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {

    // Common routes for all authenticated users
    Route::post('/auth/logout', [LoginController::class, 'logout']);
    Route::get('/auth/user', [LoginController::class, 'user']);
    Route::put('/auth/profile', [LoginController::class, 'updateProfile']);
    Route::put('/auth/password', [LoginController::class, 'updatePassword']);

    // ===========================================
    // SUPER ADMIN ROUTES
    // ===========================================
    Route::middleware(['role:super_admin'])->prefix('super-admin')->group(function () {

        // Dashboard
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index']);
        Route::get('/analytics', [SuperAdminDashboardController::class, 'analytics']);

        // ISP Management
        Route::apiResource('isps', ISPAdminController::class);
        Route::put('/isps/{isp}/verify', [ISPAdminController::class, 'verify']);
        Route::put('/isps/{isp}/activate', [ISPAdminController::class, 'activate']);
        Route::put('/isps/{isp}/suspend', [ISPAdminController::class, 'suspend']);
        Route::get('/isps/{isp}/admins', [ISPAdminController::class, 'getAdmins']);
        Route::get('/isps/{isp}/customers', [ISPAdminController::class, 'getCustomers']);
        Route::get('/isps/{isp}/statistics', [ISPAdminController::class, 'getStatistics']);

        // Package Management (for ISPs)
        Route::apiResource('packages', SuperAdminPackageController::class);
        Route::put('/packages/{package}/toggle', [SuperAdminPackageController::class, 'toggle']);

        // Payment Gateway Configuration
        Route::apiResource('payment-gateways', SuperAdminPaymentGatewayController::class);
        Route::put('/payment-gateways/{gateway}/toggle', [SuperAdminPaymentGatewayController::class, 'toggle']);
        Route::post('/payment-gateways/{gateway}/test', [SuperAdminPaymentGatewayController::class, 'test']);

        // Web Customization
        Route::get('/customization', [WebCustomizationController::class, 'index']);
        Route::put('/customization', [WebCustomizationController::class, 'update']);
        Route::post('/customization/logo', [WebCustomizationController::class, 'uploadLogo']);
        Route::post('/customization/favicon', [WebCustomizationController::class, 'uploadFavicon']);
        Route::post('/customization/hero-image', [WebCustomizationController::class, 'uploadHeroImage']);

        // Reports & Analytics
        Route::get('/reports/revenue', [SuperAdminDashboardController::class, 'revenueReport']);
        Route::get('/reports/isps', [SuperAdminDashboardController::class, 'ispReport']);
        Route::get('/reports/subscriptions', [SuperAdminDashboardController::class, 'subscriptionReport']);

        // System Settings
        Route::get('/settings', [SuperAdminDashboardController::class, 'getSettings']);
        Route::put('/settings', [SuperAdminDashboardController::class, 'updateSettings']);
    });

    // ===========================================
    // ISP ADMIN ROUTES
    // ===========================================
    Route::middleware(['role:isp_admin'])->prefix('isp-admin')->group(function () {

        // Dashboard
        Route::get('/dashboard', [ISPAdminDashboardController::class, 'index']);
        Route::get('/analytics', [ISPAdminDashboardController::class, 'analytics']);

        // Customer Management
        Route::apiResource('customers', CustomerController::class);
        Route::put('/customers/{customer}/activate', [CustomerController::class, 'activate']);
        Route::put('/customers/{customer}/suspend', [CustomerController::class, 'suspend']);
        Route::put('/customers/{customer}/terminate', [CustomerController::class, 'terminate']);
        Route::post('/customers/{customer}/change-package', [CustomerController::class, 'changePackage']);
        Route::get('/customers/{customer}/invoices', [CustomerController::class, 'getInvoices']);
        Route::get('/customers/{customer}/payments', [CustomerController::class, 'getPayments']);
        Route::get('/customers/{customer}/tickets', [CustomerController::class, 'getTickets']);

        // Package Management (Internet Packages)
        Route::apiResource('packages', ISPPackageController::class);
        Route::put('/packages/{package}/toggle', [ISPPackageController::class, 'toggle']);

        // Mikrotik Management
        Route::apiResource('mikrotik', MikrotikController::class);
        Route::post('/mikrotik/{router}/test', [MikrotikController::class, 'testConnection']);
        Route::post('/mikrotik/{router}/sync-users', [MikrotikController::class, 'syncUsers']);
        Route::post('/mikrotik/{router}/add-user', [MikrotikController::class, 'addUser']);
        Route::put('/mikrotik/{router}/update-user/{username}', [MikrotikController::class, 'updateUser']);
        Route::delete('/mikrotik/{router}/delete-user/{username}', [MikrotikController::class, 'deleteUser']);
        Route::get('/mikrotik/{router}/active-users', [MikrotikController::class, 'getActiveUsers']);

        // Technician Management
        Route::apiResource('technicians', TechnicianController::class);
        Route::put('/technicians/{technician}/toggle', [TechnicianController::class, 'toggle']);
        Route::get('/technicians/{technician}/performance', [TechnicianController::class, 'getPerformance']);
        Route::get('/technicians/available', [TechnicianController::class, 'getAvailable']);

        // Installation Requests
        Route::apiResource('installation-requests', ISPInstallationRequestController::class);
        Route::put('/installation-requests/{request}/assign', [ISPInstallationRequestController::class, 'assign']);
        Route::put('/installation-requests/{request}/cancel', [ISPInstallationRequestController::class, 'cancel']);

        // Repair Tickets
        Route::apiResource('repair-tickets', ISPRepairTicketController::class);
        Route::put('/repair-tickets/{ticket}/assign', [ISPRepairTicketController::class, 'assign']);
        Route::put('/repair-tickets/{ticket}/close', [ISPRepairTicketController::class, 'close']);

        // Payment Approval (Cash & Transfer)
        Route::get('/payments/pending', [PaymentApprovalController::class, 'pending']);
        Route::put('/payments/{payment}/approve', [PaymentApprovalController::class, 'approve']);
        Route::put('/payments/{payment}/reject', [PaymentApprovalController::class, 'reject']);

        // Invoices
        Route::get('/invoices', [ISPAdminDashboardController::class, 'getInvoices']);
        Route::post('/invoices/generate', [ISPAdminDashboardController::class, 'generateInvoices']);

        // Reports
        Route::get('/reports/revenue', [ISPAdminDashboardController::class, 'revenueReport']);
        Route::get('/reports/customers', [ISPAdminDashboardController::class, 'customerReport']);
        Route::get('/reports/payments', [ISPAdminDashboardController::class, 'paymentReport']);
        Route::get('/reports/technicians', [ISPAdminDashboardController::class, 'technicianReport']);

        // Settings
        Route::get('/settings', [ISPAdminDashboardController::class, 'getSettings']);
        Route::put('/settings', [ISPAdminDashboardController::class, 'updateSettings']);

        // Payment Gateway (ISP Specific)
        Route::get('/payment-gateways', [PaymentApprovalController::class, 'getGateways']);
        Route::post('/payment-gateways', [PaymentApprovalController::class, 'createGateway']);
        Route::put('/payment-gateways/{gateway}', [PaymentApprovalController::class, 'updateGateway']);
    });

    // ===========================================
    // TECHNICIAN ROUTES
    // ===========================================
    Route::middleware(['role:technician'])->prefix('technician')->group(function () {

        // Dashboard
        Route::get('/dashboard', [TechnicianDashboardController::class, 'index']);
        Route::get('/statistics', [TechnicianDashboardController::class, 'statistics']);

        // Installation Requests
        Route::get('/installation-requests', [TechnicianInstallationController::class, 'index']);
        Route::get('/installation-requests/{request}', [TechnicianInstallationController::class, 'show']);
        Route::put('/installation-requests/{request}/accept', [TechnicianInstallationController::class, 'accept']);
        Route::put('/installation-requests/{request}/start', [TechnicianInstallationController::class, 'start']);
        Route::put('/installation-requests/{request}/complete', [TechnicianInstallationController::class, 'complete']);
        Route::post('/installation-requests/{request}/photos', [TechnicianInstallationController::class, 'uploadPhotos']);

        // Repair Tickets
        Route::get('/repair-tickets', [TechnicianRepairController::class, 'index']);
        Route::get('/repair-tickets/{ticket}', [TechnicianRepairController::class, 'show']);
        Route::put('/repair-tickets/{ticket}/accept', [TechnicianRepairController::class, 'accept']);
        Route::put('/repair-tickets/{ticket}/start', [TechnicianRepairController::class, 'start']);
        Route::put('/repair-tickets/{ticket}/resolve', [TechnicianRepairController::class, 'resolve']);
        Route::post('/repair-tickets/{ticket}/photos', [TechnicianRepairController::class, 'uploadPhotos']);

        // Update Status
        Route::put('/status', [TechnicianDashboardController::class, 'updateStatus']);
        Route::put('/location', [TechnicianDashboardController::class, 'updateLocation']);
    });

    // ===========================================
    // CUSTOMER ROUTES
    // ===========================================
    Route::middleware(['role:customer'])->prefix('customer')->group(function () {

        // Dashboard
        Route::get('/dashboard', [CustomerDashboardController::class, 'index']);
        Route::get('/service-info', [CustomerDashboardController::class, 'serviceInfo']);

        // Billing & Invoices
        Route::get('/invoices', [BillingController::class, 'index']);
        Route::get('/invoices/{invoice}', [BillingController::class, 'show']);
        Route::get('/invoices/{invoice}/download', [BillingController::class, 'download']);
        Route::get('/current-bill', [BillingController::class, 'currentBill']);

        // Payments
        Route::post('/payments/gateway', [PaymentController::class, 'payViaGateway']);
        Route::post('/payments/cash', [PaymentController::class, 'payViaCash']);
        Route::post('/payments/transfer', [PaymentController::class, 'payViaTransfer']);
        Route::get('/payments/history', [PaymentController::class, 'history']);
        Route::get('/payments/{payment}', [PaymentController::class, 'show']);

        // Payment Gateway Callbacks
        Route::post('/payments/midtrans/notification', [PaymentController::class, 'midtransNotification']);
        Route::post('/payments/xendit/callback', [PaymentController::class, 'xenditCallback']);

        // Complaints
        Route::apiResource('complaints', ComplaintController::class)->only(['index', 'store', 'show']);
        Route::post('/complaints/{complaint}/photos', [ComplaintController::class, 'uploadPhotos']);

        // Package Information
        Route::get('/package', [CustomerDashboardController::class, 'currentPackage']);
        Route::get('/packages/available', [CustomerDashboardController::class, 'availablePackages']);

        // Service Status
        Route::get('/connection-status', [CustomerDashboardController::class, 'connectionStatus']);
    });

    // ===========================================
    // NOTIFICATIONS (All authenticated users)
    // ===========================================
    Route::prefix('notifications')->group(function () {
        Route::get('/', [\App\Http\Controllers\API\NotificationController::class, 'index']);
        Route::get('/unread', [\App\Http\Controllers\API\NotificationController::class, 'unread']);
        Route::put('/{notification}/read', [\App\Http\Controllers\API\NotificationController::class, 'markAsRead']);
        Route::put('/read-all', [\App\Http\Controllers\API\NotificationController::class, 'markAllAsRead']);
        Route::delete('/{notification}', [\App\Http\Controllers\API\NotificationController::class, 'destroy']);
    });
});

// Payment Gateway Webhooks (No authentication required)
Route::post('/webhooks/midtrans', [PaymentController::class, 'midtransWebhook']);
Route::post('/webhooks/xendit', [PaymentController::class, 'xenditWebhook']);
