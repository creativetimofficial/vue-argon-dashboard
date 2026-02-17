<?php

use Illuminate\Support\Facades\Route;

// Main application route (Super Admin, Client Area)
// This will ONLY match localhost (not subdomains)
Route::get('/', function () {
    return view('welcome');
});

// ISP Admin routes (accessed via subdomain or custom domain)
// These routes will ONLY be accessible via tenant domains (e.g., irvan.localhost)
// DetectTenant middleware will abort(404) if accessed via localhost
Route::middleware('detect.tenant')->group(function () {
    // Root and login pages
    Route::get('/', function () {
        return view('isp-admin.login');
    })->name('isp-admin.root');
    
    Route::get('/isp-admin/login', function () {
        return view('isp-admin.login');
    })->name('isp-admin.login');
    
    // Dashboard (requires authentication)
    Route::get('/dashboard', function () {
        return view('isp-admin.dashboard');
    })->name('isp-admin.dashboard');
    
    // Other ISP Admin pages
    Route::get('/customers', function () {
        return view('isp-admin.dashboard'); // Placeholder
    })->name('isp-admin.customers');
    
    Route::get('/mikrotik', function () {
        return view('isp-admin.dashboard'); // Placeholder
    })->name('isp-admin.mikrotik');
});
