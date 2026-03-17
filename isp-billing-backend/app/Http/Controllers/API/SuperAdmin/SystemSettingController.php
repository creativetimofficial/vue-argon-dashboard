<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    /**
     * Get docs and social settings.
     */
    public function getDocumentation()
    {
        $settings = SystemSetting::get('documentation', [
            'help_title' => 'Need Help ?',
            'help_description' => 'Please check our docs',
            'docs_link' => 'https://example.com/docs',
            'youtube_link' => 'https://youtube.com',
            'social_media' => []
        ]);

        return response()->json(['settings' => $settings]);
    }

    /**
     * Update documentation settings.
     */
    public function updateDocumentation(Request $request)
    {
        $validated = $request->validate([
            'help_title' => 'required|string|max:100',
            'help_description' => 'required|string|max:255',
            'docs_link' => 'required|url',
            'youtube_link' => 'nullable|url',
            'social_media' => 'nullable|array',
        ]);

        SystemSetting::set('documentation', $validated);

        return response()->json([
            'message' => 'Settings updated successfully',
            'settings' => $validated
        ]);
    }

    /**
     * Get main domain setting.
     */
    public function getMainDomain()
    {
        $settings = SystemSetting::get('main_domain', [
            'base_domain' => 'yourdomain.com',
        ]);
        return response()->json(['settings' => $settings]);
    }

    /**
     * Update main domain setting.
     */
    public function updateMainDomain(Request $request)
    {
        $validated = $request->validate([
            'base_domain' => 'required|string|max:255',
        ]);

        SystemSetting::set('main_domain', $validated);

        return response()->json([
            'message' => 'Main domain updated successfully',
            'settings' => $validated
        ]);
    }
}
