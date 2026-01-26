<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionPackageController extends Controller
{
    public function index()
    {
        $packages = SubscriptionPackage::orderBy('sort_order')->get();
        return response()->json($packages);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:subscription_packages,slug',
            'description' => 'nullable|string',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'discount_yearly_percent' => 'nullable|integer|min:0|max:100',
            'max_customers' => 'required|integer',
            'max_users' => 'required|integer',
            'max_locations' => 'required|integer',
            // Features
            'feature_whitelabel' => 'boolean',
            'feature_api_access' => 'boolean',
            'feature_priority_support' => 'boolean',
            'feature_analytics' => 'boolean',
            'feature_multi_currency' => 'boolean',
            'feature_automated_billing' => 'boolean',
            'feature_free_subdomain' => 'boolean',
            'feature_custom_domain' => 'boolean',
            'feature_maps_interaktif' => 'boolean',
            'feature_dynamic_forwarding' => 'boolean',
            'feature_vpn_api' => 'boolean',
            'feature_payment_gateways' => 'boolean',
            'feature_custom_landing_page' => 'boolean',
            'feature_realtime_monitoring' => 'boolean',
            'feature_radius' => 'boolean',
            'feature_acs' => 'boolean',
            'feature_customer_portal' => 'boolean',
            'feature_android_app' => 'boolean',
            
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'is_popular' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $package = SubscriptionPackage::create($validated);
        return response()->json($package, 201);
    }

    public function show($id)
    {
        $package = SubscriptionPackage::findOrFail($id);
        return response()->json($package);
    }

    public function update(Request $request, $id)
    {
        $package = SubscriptionPackage::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|unique:subscription_packages,slug,' . $id,
            'description' => 'nullable|string',
            'price_monthly' => 'sometimes|numeric|min:0',
            'price_yearly' => 'sometimes|numeric|min:0',
            'discount_yearly_percent' => 'nullable|integer|min:0|max:100',
            'max_customers' => 'sometimes|integer',
            'max_users' => 'sometimes|integer',
            'max_locations' => 'sometimes|integer',
            'has_custom_branding' => 'boolean',
            'has_api_access' => 'boolean',
            'has_white_label' => 'boolean',
            'has_priority_support' => 'boolean',
            'has_dedicated_manager' => 'boolean',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'is_popular' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $package->update($validated);
        return response()->json($package);
    }

    public function destroy($id)
    {
        $package = SubscriptionPackage::findOrFail($id);
        $package->delete();
        return response()->json(['message' => 'Package deleted successfully']);
    }
}
