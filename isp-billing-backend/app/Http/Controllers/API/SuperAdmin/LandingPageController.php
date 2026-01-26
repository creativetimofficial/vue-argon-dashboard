<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\LandingPage;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index()
    {
        $page = LandingPage::where('is_active', true)->first();
        if (!$page) {
            $page = LandingPage::first();
        }
        return response()->json($page);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hero_title' => 'required|string',
            'hero_subtitle' => 'nullable|string',
            'hero_description' => 'nullable|string',
            'hero_primary_button_text' => 'nullable|string',
            'hero_primary_button_url' => 'nullable|string',
            'hero_secondary_button_text' => 'nullable|string',
            'hero_secondary_button_url' => 'nullable|string',
            'hero_background_image' => 'nullable|string',
            'hero_gradient_from' => 'nullable|string',
            'hero_gradient_to' => 'nullable|string',
            'features' => 'nullable|array',
            'pricing_plans' => 'nullable|array',
            'testimonials' => 'nullable|array',
            'footer_company_name' => 'nullable|string',
            'footer_copyright' => 'nullable|string',
            'footer_email' => 'nullable|email',
            'footer_phone' => 'nullable|string',
            'footer_address' => 'nullable|string',
            'footer_social_links' => 'nullable|array',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $page = LandingPage::create($validated);
        return response()->json($page, 201);
    }

    public function show($id)
    {
        $page = LandingPage::findOrFail($id);
        return response()->json($page);
    }

    public function update(Request $request, $id)
    {
        $page = LandingPage::findOrFail($id);
        
        $validated = $request->validate([
            'hero_title' => 'sometimes|string',
            'hero_subtitle' => 'nullable|string',
            'hero_description' => 'nullable|string',
            'hero_primary_button_text' => 'nullable|string',
            'hero_primary_button_url' => 'nullable|string',
            'hero_secondary_button_text' => 'nullable|string',
            'hero_secondary_button_url' => 'nullable|string',
            'hero_background_image' => 'nullable|string',
            'hero_gradient_from' => 'nullable|string',
            'hero_gradient_to' => 'nullable|string',
            'features' => 'nullable|array',
            'pricing_plans' => 'nullable|array',
            'testimonials' => 'nullable|array',
            'footer_company_name' => 'nullable|string',
            'footer_copyright' => 'nullable|string',
            'footer_email' => 'nullable|email',
            'footer_phone' => 'nullable|string',
            'footer_address' => 'nullable|string',
            'footer_social_links' => 'nullable|array',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $page->update($validated);
        return response()->json($page);
    }

    public function destroy($id)
    {
        $page = LandingPage::findOrFail($id);
        $page->delete();
        return response()->json(['message' => 'Landing page deleted successfully']);
    }
}
