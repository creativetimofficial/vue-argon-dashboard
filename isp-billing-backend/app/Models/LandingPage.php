<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'is_active',
        'hero_title',
        'hero_subtitle',
        'hero_cta_text',
        'hero_cta_link',
        'hero_image',
        'features_section_title',
        'features',
        'pricing_section_title',
        'show_pricing',
        'testimonials_section_title',
        'testimonials',
        'faq_section_title',
        'faqs',
        'cta_section_title',
        'cta_section_subtitle',
        'cta_button_text',
        'cta_button_link',
        'footer_text',
        'footer_links',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_pricing' => 'boolean',
        'features' => 'array',
        'testimonials' => 'array',
        'faqs' => 'array',
        'footer_links' => 'array',
    ];
}