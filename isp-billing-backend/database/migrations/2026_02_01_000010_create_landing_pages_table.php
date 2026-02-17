<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('isp_id')->nullable()->constrained()->onDelete('cascade');
            
            // Hero Section
            $table->string('hero_badge_text', 100)->nullable();
            $table->string('hero_title', 255)->nullable();
            $table->text('hero_subtitle')->nullable();
            $table->longText('hero_image')->nullable();
            $table->string('hero_cta_text', 100)->nullable();
            $table->string('hero_cta_link', 255)->nullable();
            $table->string('hero_gradient_from', 7)->nullable();
            $table->string('hero_gradient_to', 7)->nullable();
            
            // Features Section
            $table->boolean('show_features')->default(true);
            $table->json('features')->nullable();
            
            // Pricing Section
            $table->boolean('show_pricing')->default(true);
            $table->json('pricing_plans')->nullable();
            
            // Testimonials Section
            $table->boolean('show_testimonials')->default(true);
            $table->json('testimonials')->nullable();
            
            // Logos Section
            $table->boolean('show_logos')->default(true);
            $table->json('logos')->nullable();
            
            // FAQs Section
            $table->boolean('show_faqs')->default(true);
            $table->json('faqs')->nullable();
            
            // Contact Section
            $table->boolean('show_contact')->default(true);
            $table->json('contact_info')->nullable();
            
            // Footer
            $table->string('footer_company_name', 255)->nullable();
            $table->string('footer_copyright', 255)->nullable();
            $table->json('footer_social_links')->nullable();
            
            // SEO
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            
            // Styling
            $table->string('font_family', 100)->default('Inter');
            $table->string('font_size_base', 20)->default('16px');
            $table->string('color_primary', 7)->default('#667eea');
            $table->string('color_secondary', 7)->default('#764ba2');
            $table->string('color_accent', 7)->default('#4f46e5');
            $table->string('color_text', 7)->default('#1a202c');
            $table->string('color_background', 7)->default('#ffffff');
            $table->decimal('spacing_scale', 3, 1)->default(1.0);
            $table->string('border_radius_base', 20)->default('12px');
            $table->string('button_style', 50)->default('rounded');
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_pages');
    }
};
