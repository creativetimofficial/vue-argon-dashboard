<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\IspTheme;
use Illuminate\Http\Request;

class IspThemeController extends Controller
{
    public function index()
    {
        $themes = IspTheme::with('isp')->get();
        return response()->json($themes);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'system_name' => 'required|string',
            'name' => 'nullable|string',
            'landing_page_template' => 'nullable|in:sneat,modern,creative',
            'logo_light' => 'nullable|string',
            'logo_dark' => 'nullable|string',
            'favicon' => 'nullable|string',
            'color_primary' => 'nullable|string',
            'color_secondary' => 'nullable|string',
            'color_success' => 'nullable|string',
            'color_danger' => 'nullable|string',
            'color_warning' => 'nullable|string',
            'color_info' => 'nullable|string',
            'sidebar_bg_color' => 'nullable|string',
            'sidebar_text_color' => 'nullable|string',
            'sidebar_active_color' => 'nullable|string',
            'sidebar_type' => 'nullable|in:transparent,white,dark',
            'font_family' => 'nullable|string',
            'font_size' => 'nullable|string',
            'border_radius' => 'nullable|string',
            'button_style' => 'nullable|string',
            'card_shadow' => 'nullable|string',
            'dark_mode_default' => 'boolean',
            'sidebar_mini' => 'boolean',
            'fixed_navbar' => 'boolean',
            'custom_css' => 'nullable|string',
            'custom_js' => 'nullable|string',
            'additional_settings' => 'nullable|array',
            'isp_id' => 'nullable|exists:isps,id',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $theme = IspTheme::create($validated);
        return response()->json($theme, 201);
    }

    public function show($id)
    {
        $theme = IspTheme::with('isp')->findOrFail($id);
        return response()->json($theme);
    }

    public function update(Request $request, $id)
    {
        $theme = IspTheme::findOrFail($id);
        
        $validated = $request->validate([
            'system_name' => 'sometimes|string',
            'name' => 'nullable|string',
            'landing_page_template' => 'nullable|in:sneat,modern,creative',
            'logo_light' => 'nullable|string',
            'logo_dark' => 'nullable|string',
            'favicon' => 'nullable|string',
            'color_primary' => 'nullable|string',
            'color_secondary' => 'nullable|string',
            'color_success' => 'nullable|string',
            'color_danger' => 'nullable|string',
            'color_warning' => 'nullable|string',
            'color_info' => 'nullable|string',
            'sidebar_bg_color' => 'nullable|string',
            'sidebar_text_color' => 'nullable|string',
            'sidebar_active_color' => 'nullable|string',
            'sidebar_type' => 'nullable|in:transparent,white,dark',
            'font_family' => 'nullable|string',
            'font_size' => 'nullable|string',
            'border_radius' => 'nullable|string',
            'button_style' => 'nullable|string',
            'card_shadow' => 'nullable|string',
            'dark_mode_default' => 'boolean',
            'sidebar_mini' => 'boolean',
            'fixed_navbar' => 'boolean',
            'custom_css' => 'nullable|string',
            'custom_js' => 'nullable|string',
            'additional_settings' => 'nullable|array',
            'isp_id' => 'nullable|exists:isps,id',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $theme->update($validated);
        return response()->json($theme);
    }

    public function destroy($id)
    {
        $theme = IspTheme::findOrFail($id);
        $theme->delete();
        return response()->json(['message' => 'Theme deleted successfully']);
    }

    public function getDefault()
    {
        $theme = IspTheme::where('is_default', true)->first();
        return response()->json($theme);
    }

    /**
     * Get active theme for authenticated user
     */
    public function getActiveTheme(Request $request)
    {
        $user = $request->user();
        
        if ($user && $user->isp_id) {
            $theme = IspTheme::where('isp_id', $user->isp_id)
                             ->where('is_active', true)
                             ->first();
            
            if ($theme) {
                return response()->json($theme);
            }
        }
        
        $defaultTheme = IspTheme::whereNull('isp_id')
                                ->where('is_active', true)
                                ->first();
        
        if (!$defaultTheme) {
            $defaultTheme = IspTheme::where('is_default', true)->first();
        }
        
        return response()->json($defaultTheme);
    }

    /**
     * Get public theme (no authentication required)
     */
    public function getPublicTheme()
    {
        $theme = IspTheme::whereNull('isp_id')
                         ->where('is_active', true)
                         ->first();
        
        if (!$theme) {
            $theme = IspTheme::where('is_default', true)->first();
        }
        
        return response()->json($theme);
    }

    /**
     * Update the active theme for the authenticated ISP Admin
     */
    public function updateActiveTheme(Request $request)
    {
        $user = $request->user();
        
        if (!$user || !$user->isp_id) {
            return response()->json(['message' => 'Unauthorized or no ISP associated'], 403);
        }

        $validated = $request->validate([
            'system_name' => 'sometimes|string',
            'name' => 'nullable|string',
            'landing_page_template' => 'nullable|in:sneat,modern,creative',
            'logo_light' => 'nullable|string',
            'logo_dark' => 'nullable|string',
            'favicon' => 'nullable|string',
            'color_primary' => 'nullable|string',
            'color_secondary' => 'nullable|string',
            'button_primary_color' => 'nullable|string',
            'button_secondary_color' => 'nullable|string',
            'link_color' => 'nullable|string',
            'company_name' => 'nullable|string',
            'custom_css' => 'nullable|string',
            // Add other fields as needed
        ]);

        $theme = IspTheme::where('isp_id', $user->isp_id)->first();

        if ($theme) {
            $theme->update($validated);
        } else {
            // Create new theme for this ISP
            $validated['isp_id'] = $user->isp_id;
            $validated['is_active'] = true;
            $validated['system_name'] = $validated['system_name'] ?? ($user->isp->name . ' Theme');
            $theme = IspTheme::create($validated);
        }

        return response()->json($theme);
    }
}
