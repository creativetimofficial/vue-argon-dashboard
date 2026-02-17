<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ISPService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ISPServiceController extends Controller
{
    /**
     * Get all ISP services
     */
    public function index()
    {
        $services = ISPService::orderBy('sort_order')->get();
        return response()->json($services);
    }

    /**
     * Create new ISP service
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:isp_services,slug',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,quarterly,semi_annual,annual',
            'trial_days' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer',
            'requires_manual_approval' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $service = ISPService::create($validated);
        return response()->json($service, 201);
    }

    /**
     * Get service detail
     */
    public function show($id)
    {
        $service = ISPService::findOrFail($id);
        return response()->json($service);
    }

    /**
     * Update service
     */
    public function update(Request $request, $id)
    {
        $service = ISPService::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|unique:isp_services,slug,' . $id,
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:50',
            'price' => 'sometimes|numeric|min:0',
            'billing_cycle' => 'sometimes|in:monthly,quarterly,semi_annual,annual',
            'trial_days' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer',
            'requires_manual_approval' => 'boolean',
        ]);

        $service->update($validated);
        return response()->json($service);
    }

    /**
     * Delete service
     */
    public function destroy($id)
    {
        $service = ISPService::findOrFail($id);
        $service->delete();
        return response()->json(['message' => 'Service deleted successfully']);
    }
}
