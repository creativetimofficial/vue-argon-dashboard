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
            'logo_light' => 'nullable|string',
            'logo_dark' => 'nullable|string',
            'favicon' => 'nullable|string',
            'primary_color' => 'nullable|string',
            'secondary_color' => 'nullable|string',
            'success_color' => 'nullable|string',
            'danger_color' => 'nullable|string',
            'warning_color' => 'nullable|string',
            'info_color' => 'nullable|string',
            'sidebar_bg_color' => 'nullable|string',
            'sidebar_text_color' => 'nullable|string',
            'sidebar_active_color' => 'nullable|string',
            'sidebar_type' => 'nullable|in:transparent,white,dark',
            'font_family' => 'nullable|string',
            'font_size' => 'nullable|string',
            'dark_mode_default' => 'boolean',
            'sidebar_mini' => 'boolean',
            'fixed_navbar' => 'boolean',
            'custom_css' => 'nullable|string',
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
            'logo_light' => 'nullable|string',
            'logo_dark' => 'nullable|string',
            'favicon' => 'nullable|string',
            'primary_color' => 'nullable|string',
            'secondary_color' => 'nullable|string',
            'success_color' => 'nullable|string',
            'danger_color' => 'nullable|string',
            'warning_color' => 'nullable|string',
            'info_color' => 'nullable|string',
            'sidebar_bg_color' => 'nullable|string',
            'sidebar_text_color' => 'nullable|string',
            'sidebar_active_color' => 'nullable|string',
            'sidebar_type' => 'nullable|in:transparent,white,dark',
            'font_family' => 'nullable|string',
            'font_size' => 'nullable|string',
            'dark_mode_default' => 'boolean',
            'sidebar_mini' => 'boolean',
            'fixed_navbar' => 'boolean',
            'custom_css' => 'nullable|string',
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
}
