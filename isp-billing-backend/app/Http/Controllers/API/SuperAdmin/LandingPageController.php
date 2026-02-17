<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\LandingPage;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index()
    {
        // Super Admin: Return default landing page without tenant filtering
        if (auth()->check() && auth()->user()->role === 'super_admin') {
            $page = LandingPage::whereNull('isp_id')->where('is_active', true)->first();
            if (!$page) {
                $page = LandingPage::first();
            }
            return response()->json($page);
        }

        // Tenant Awareness for ISP Admins
        if (app()->bound('current_isp')) {
            $isp = app('current_isp');
            
            // Try to find custom landing page for this ISP
            $page = LandingPage::where('isp_id', $isp->id)->where('is_active', true)->first();
            
            if ($page) {
                 return response()->json($page);
            }
            
            // Fallback: Use default page but override branding
            $defaultPage = LandingPage::whereNull('isp_id')->where('is_active', true)->first();
            
            if ($defaultPage) {
                $data = $defaultPage->toArray();
                // Overrides
                $data['footer_company_name'] = $isp->company_name ?? $isp->name;
                // You can add more overrides here if needed
                // e.g. $data['hero_title'] = "Welcome to " . $data['footer_company_name'];
                
                // If ISP has a logo, we might want to pass it, though LandingPage structure is strict.
                // For now, let's assuming branding name is enough or frontend can fetch logo separately.
                return response()->json($data);
            }
        }

        $page = LandingPage::whereNull('isp_id')->where('is_active', true)->first();
        if (!$page) {
            $page = LandingPage::first();
        }
        return response()->json($page);
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/landing-page'), $imageName);
            
            return response()->json([
                'url' => url('uploads/landing-page/' . $imageName),
                'message' => 'Image uploaded successfully'
            ]);
        }

        return response()->json(['message' => 'No image uploaded'], 400);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'isp_id' => 'nullable|exists:isps,id',
            'hero_title' => 'required|string',
            'hero_subtitle' => 'nullable|string',
            'hero_image' => 'nullable|string',
            'hero_cta_text' => 'nullable|string',
            'hero_cta_link' => 'nullable|string',
            'hero_gradient_from' => 'nullable|string',
            'hero_gradient_to' => 'nullable|string',
            'show_features' => 'nullable|boolean',
            'features' => 'nullable|array',
            'show_pricing' => 'nullable|boolean',
            'pricing_plans' => 'nullable|array',
            'show_testimonials' => 'nullable|boolean',
            'testimonials' => 'nullable|array',
            'show_logos' => 'nullable|boolean',
            'logos' => 'nullable|array',
            'show_faqs' => 'nullable|boolean',
            'faqs' => 'nullable|array',
            'show_contact' => 'nullable|boolean',
            'contact_info' => 'nullable|array',
            'footer_company_name' => 'nullable|string',
            'footer_copyright' => 'nullable|string',
            'footer_social_links' => 'nullable|array',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'custom_html' => 'nullable|string',
            'custom_css' => 'nullable|string',
            'custom_js' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            // Styling fields
            'font_family' => 'nullable|string|max:50',
            'font_size_base' => 'nullable|string|max:10',
            'color_primary' => 'nullable|string|max:20',
            'color_secondary' => 'nullable|string|max:20',
            'color_accent' => 'nullable|string|max:20',
            'color_text' => 'nullable|string|max:20',
            'color_background' => 'nullable|string|max:20',
            'spacing_scale' => 'nullable|numeric|min:0.5|max:2',
            'border_radius_base' => 'nullable|string|max:10',
            'button_style' => 'nullable|string|max:20',
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
            'isp_id' => 'nullable|exists:isps,id',
            'hero_title' => 'nullable|string',
            'hero_subtitle' => 'nullable|string',
            'hero_image' => 'nullable|string',
            'hero_cta_text' => 'nullable|string',
            'hero_cta_link' => 'nullable|string',
            'hero_gradient_from' => 'nullable|string',
            'hero_gradient_to' => 'nullable|string',
            'show_features' => 'nullable|boolean',
            'features' => 'nullable|array',
            'show_pricing' => 'nullable|boolean',
            'pricing_plans' => 'nullable|array',
            'show_testimonials' => 'nullable|boolean',
            'testimonials' => 'nullable|array',
            'show_logos' => 'nullable|boolean',
            'logos' => 'nullable|array',
            'show_faqs' => 'nullable|boolean',
            'faqs' => 'nullable|array',
            'show_contact' => 'nullable|boolean',
            'contact_info' => 'nullable|array',
            'footer_company_name' => 'nullable|string',
            'footer_copyright' => 'nullable|string',
            'footer_social_links' => 'nullable|array',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'custom_html' => 'nullable|string',
            'custom_css' => 'nullable|string',
            'custom_js' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            // Styling fields
            'font_family' => 'nullable|string|max:50',
            'font_size_base' => 'nullable|string|max:10',
            'color_primary' => 'nullable|string|max:20',
            'color_secondary' => 'nullable|string|max:20',
            'color_accent' => 'nullable|string|max:20',
            'color_text' => 'nullable|string|max:20',
            'color_background' => 'nullable|string|max:20',
            'spacing_scale' => 'nullable|numeric|min:0.5|max:2',
            'border_radius_base' => 'nullable|string|max:10',
            'button_style' => 'nullable|string|max:20',
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
