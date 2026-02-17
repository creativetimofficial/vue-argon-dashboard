<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'isp_id',
        'hero_title',
        'hero_subtitle',
        'hero_image',
        'hero_cta_text',
        'hero_cta_link',
        'hero_gradient_from',
        'hero_gradient_to',
        'show_features',
        'features',
        'show_pricing',
        'pricing_plans',
        'show_testimonials',
        'testimonials',
        'show_logos',
        'logos',
        'show_faqs',
        'faqs',
        'show_contact',
        'contact_info',
        'footer_company_name',
        'footer_copyright',
        'footer_social_links',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'custom_html',
        'custom_css',
        'custom_js',
        'is_active',
        // Styling fields
        'font_family',
        'font_size_base',
        'color_primary',
        'color_secondary',
        'color_accent',
        'color_text',
        'color_background',
        'spacing_scale',
        'border_radius_base',
        'button_style',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_features' => 'boolean',
        'show_pricing' => 'boolean',
        'show_testimonials' => 'boolean',
        'show_logos' => 'boolean',
        'show_faqs' => 'boolean',
        'show_contact' => 'boolean',
        'features' => 'array',
        'pricing_plans' => 'array',
        'testimonials' => 'array',
        'logos' => 'array',
        'faqs' => 'array',
        'contact_info' => 'array',
        'footer_social_links' => 'array',
        'spacing_scale' => 'decimal:2',
    ];
}