<template>
  <div class="py-4 container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h5 class="mb-0">Landing Page Editor</h5>
                <p class="text-sm mb-0">
                  Customize tampilan landing page promosi
                </p>
              </div>
              <div>
                <button
                  class="btn btn-outline-primary btn-sm me-2"
                  @click="togglePreview"
                >
                  <i class="fas fa-eye me-2"></i
                  >Preview
                </button>
                <button class="btn btn-primary btn-sm" @click="saveLandingPage">
                  <i class="fas fa-save me-2"></i>Simpan Perubahan
                </button>
              </div>
            </div>
          </div>

          <div class="card-body">
            <!-- Tabs -->
            <ul class="nav nav-tabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button
                  :class="['nav-link', activeTab === 'hero' ? 'active' : '']"
                  @click="activeTab = 'hero'"
                >
                  Hero Section
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button
                  :class="[
                    'nav-link',
                    activeTab === 'features' ? 'active' : '',
                  ]"
                  @click="activeTab = 'features'"
                >
                  Features
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button
                  :class="['nav-link', activeTab === 'pricing' ? 'active' : '']"
                  @click="activeTab = 'pricing'"
                >
                  Pricing Plans
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button
                  :class="[
                    'nav-link',
                    activeTab === 'testimonials' ? 'active' : '',
                  ]"
                  @click="activeTab = 'testimonials'"
                >
                  Testimonials
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button
                  :class="['nav-link', activeTab === 'logos' ? 'active' : '']"
                  @click="activeTab = 'logos'"
                >
                  Logos
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button
                  :class="['nav-link', activeTab === 'faqs' ? 'active' : '']"
                  @click="activeTab = 'faqs'"
                >
                  FAQs
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button
                  :class="['nav-link', activeTab === 'styling' ? 'active' : '']"
                  @click="activeTab = 'styling'"
                >
                  Styling
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button
                  :class="['nav-link', activeTab === 'footer' ? 'active' : '']"
                  @click="activeTab = 'footer'"
                >
                  Footer & Contact
                </button>
              </li>
            </ul>

            <div class="tab-content mt-4">
              <!-- Hero Section -->
              <div v-show="activeTab === 'hero'">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Hero Title</label>
                    <input
                      v-model="landingPage.hero_title"
                      type="text"
                      class="form-control"
                    />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Hero Subtitle</label>
                    <input
                      v-model="landingPage.hero_subtitle"
                      type="text"
                      class="form-control"
                    />
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">CTA Button Text</label>
                    <input
                      v-model="landingPage.hero_cta_text"
                      type="text"
                      class="form-control"
                    />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">CTA Button Link</label>
                    <input
                      v-model="landingPage.hero_cta_link"
                      type="text"
                      class="form-control"
                    />
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Background Image URL</label>
                    <input
                      v-model="landingPage.hero_image"
                      type="url"
                      class="form-control"
                    />
                    <small class="text-muted">Or upload file below</small>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Upload Background Image</label>
                    <input
                      type="file"
                      class="form-control"
                      accept="image/*"
                      @change="uploadHeroImage"
                    />
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Gradient Start Color</label>
                    <input
                      v-model="landingPage.hero_gradient_from"
                      type="color"
                      class="form-control form-control-color"
                    />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Gradient End Color</label>
                    <input
                      v-model="landingPage.hero_gradient_to"
                      type="color"
                      class="form-control form-control-color"
                    />
                  </div>
                </div>
              </div>

              <!-- Features Section -->
              <div v-show="activeTab === 'features'">
                <div class="form-check form-switch mb-3">
                  <input
                    v-model="landingPage.show_features"
                    class="form-check-input"
                    type="checkbox"
                    id="showFeatures"
                  />
                  <label class="form-check-label" for="showFeatures">Tampilkan Fitur</label>
                </div>
                <div v-if="landingPage.show_features">
                  <div class="d-flex justify-content-between mb-3">
                    <h6>Feature Items</h6>
                    <button class="btn btn-sm btn-primary" @click="addFeature">
                      <i class="fas fa-plus me-2"></i>Tambah Feature
                    </button>
                  </div>
                  <div
                    v-for="(feature, index) in landingPage.features"
                    :key="index"
                    class="card mb-3"
                  >
                    <div class="card-body">
                      <div class="d-flex justify-content-between mb-2">
                        <h6 class="mb-0">Feature {{ index + 1 }}</h6>
                        <button
                          class="btn btn-sm btn-danger"
                          @click="removeFeature(index)"
                        >
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                      <div class="row">
                        <div class="col-md-4 mb-3">
                          <label class="form-label">Icon</label>
                          <div class="input-group">
                            <span class="input-group-text"><i :class="feature.icon"></i></span>
                            <select v-model="feature.icon" class="form-select">
                              <option v-for="icon in availableIcons" :key="icon.class" :value="icon.class">
                                {{ icon.name }}
                              </option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-8 mb-3">
                          <label class="form-label">Title</label>
                          <input
                            v-model="feature.title"
                            type="text"
                            class="form-control"
                          />
                        </div>
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea
                          v-model="feature.description"
                          class="form-control"
                          rows="2"
                        ></textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Pricing Plans -->
              <div v-show="activeTab === 'pricing'">
                <div class="form-check form-switch mb-3">
                  <input
                    v-model="landingPage.show_pricing"
                    class="form-check-input"
                    type="checkbox"
                    id="showPricing"
                  />
                  <label class="form-check-label" for="showPricing">Tampilkan Harga</label>
                </div>
                <div v-if="landingPage.show_pricing">
                  <div class="d-flex justify-content-between mb-3">
                    <h6>Pricing Tiers</h6>
                    <button
                      class="btn btn-sm btn-primary"
                      @click="addPricingPlan"
                    >
                      <i class="fas fa-plus me-2"></i>Tambah Plan
                    </button>
                  </div>
                  <div
                    v-for="(plan, index) in landingPage.pricing_plans"
                    :key="index"
                    class="card mb-3"
                  >
                    <div class="card-body">
                      <div class="d-flex justify-content-between mb-2">
                        <h6 class="mb-0">Plan {{ index + 1 }}</h6>
                        <button
                          class="btn btn-sm btn-danger"
                          @click="removePricingPlan(index)"
                        >
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <label class="form-label">Name</label>
                          <input
                            v-model="plan.name"
                            type="text"
                            class="form-control"
                          />
                        </div>
                        <div class="col-md-6 mb-3">
                          <label class="form-label">Price</label>
                          <input
                            v-model="plan.price"
                            type="number"
                            class="form-control"
                          />
                        </div>
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Features (newline separated)</label>
                        <textarea
                          v-model="plan.features"
                          class="form-control"
                          rows="3"
                        ></textarea>
                      </div>
                      <div class="form-check">
                        <input
                          v-model="plan.isPopular"
                          class="form-check-input"
                          type="checkbox"
                          :id="'popular' + index"
                        />
                        <label class="form-check-label" :for="'popular' + index">
                          Popular Plan
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Testimonials -->
              <div v-show="activeTab === 'testimonials'">
                <div class="form-check form-switch mb-3">
                  <input
                    v-model="landingPage.show_testimonials"
                    class="form-check-input"
                    type="checkbox"
                    id="showTestimonials"
                  />
                  <label class="form-check-label" for="showTestimonials">Tampilkan Testimoni</label>
                </div>
                <div v-if="landingPage.show_testimonials">
                  <div class="d-flex justify-content-between mb-3">
                    <h6>Testimonials</h6>
                    <button class="btn btn-sm btn-primary" @click="addTestimonial">
                      <i class="fas fa-plus me-2"></i>Tambah Testimonial
                    </button>
                  </div>
                  <div
                    v-for="(testimonial, index) in landingPage.testimonials"
                    :key="index"
                    class="card mb-3"
                  >
                    <div class="card-body">
                      <div class="d-flex justify-content-between mb-2">
                        <h6 class="mb-0">Testimonial {{ index + 1 }}</h6>
                        <button
                          class="btn btn-sm btn-danger"
                          @click="removeTestimonial(index)"
                        >
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <label class="form-label">Name</label>
                          <input
                            v-model="testimonial.name"
                            type="text"
                            class="form-control"
                          />
                        </div>
                        <div class="col-md-6 mb-3">
                          <label class="form-label">Company</label>
                          <input
                            v-model="testimonial.company"
                            type="text"
                            class="form-control"
                          />
                        </div>
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Text</label>
                        <textarea
                          v-model="testimonial.text"
                          class="form-control"
                          rows="2"
                        ></textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Styling Tab -->
              <div v-show="activeTab === 'styling'">
                <h6 class="mb-4">Customize Tampilan Landing Page</h6>
                
                <!-- Font Settings -->
                <div class="card mb-4">
                  <div class="card-body">
                    <h6 class="card-title">Font Settings</h6>
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label class="form-label">Font Family</label>
                        <select v-model="landingPage.font_family" class="form-select">
                          <option value="Inter">Inter</option>
                          <option value="Poppins">Poppins</option>
                          <option value="Roboto">Roboto</option>
                          <option value="Open Sans">Open Sans</option>
                          <option value="Lato">Lato</option>
                        </select>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label">Base Font Size</label>
                        <input v-model="landingPage.font_size_base" type="text" class="form-control" placeholder="16px" />
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Color Palette -->
                <div class="card mb-4">
                  <div class="card-body">
                    <h6 class="card-title">Color Palette</h6>
                    <div class="row">
                      <div class="col-md-4 mb-3">
                        <label class="form-label">Primary Color</label>
                        <div class="input-group">
                          <input v-model="landingPage.color_primary" type="color" class="form-control form-control-color" />
                          <input v-model="landingPage.color_primary" type="text" class="form-control" />
                        </div>
                      </div>
                      <div class="col-md-4 mb-3">
                        <label class="form-label">Secondary Color</label>
                        <div class="input-group">
                          <input v-model="landingPage.color_secondary" type="color" class="form-control form-control-color" />
                          <input v-model="landingPage.color_secondary" type="text" class="form-control" />
                        </div>
                      </div>
                      <div class="col-md-4 mb-3">
                        <label class="form-label">Accent Color</label>
                        <div class="input-group">
                          <input v-model="landingPage.color_accent" type="color" class="form-control form-control-color" />
                          <input v-model="landingPage.color_accent" type="text" class="form-control" />
                        </div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label">Text Color</label>
                        <div class="input-group">
                          <input v-model="landingPage.color_text" type="color" class="form-control form-control-color" />
                          <input v-model="landingPage.color_text" type="text" class="form-control" />
                        </div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label">Background Color</label>
                        <div class="input-group">
                          <input v-model="landingPage.color_background" type="color" class="form-control form-control-color" />
                          <input v-model="landingPage.color_background" type="text" class="form-control" />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Layout Settings -->
                <div class="card mb-4">
                  <div class="card-body">
                    <h6 class="card-title">Layout Settings</h6>
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label class="form-label">Spacing Scale (0.5 - 2.0)</label>
                        <input v-model.number="landingPage.spacing_scale" type="number" step="0.1" min="0.5" max="2" class="form-control" />
                        <small class="text-muted">Current: {{ landingPage.spacing_scale }}x</small>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label">Border Radius</label>
                        <input v-model="landingPage.border_radius_base" type="text" class="form-control" placeholder="12px" />
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label">Button Style</label>
                        <select v-model="landingPage.button_style" class="form-select">
                          <option value="rounded">Rounded</option>
                          <option value="square">Square</option>
                          <option value="pill">Pill (Full Rounded)</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Footer -->
              <div v-show="activeTab === 'footer'">
                <div class="form-check form-switch mb-3">
                  <input
                    v-model="landingPage.show_contact"
                    class="form-check-input"
                    type="checkbox"
                    id="showContact"
                  />
                  <label class="form-check-label" for="showContact">Tampilkan Kontak</label>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Company Name</label>
                    <input
                      v-model="landingPage.footer_company_name"
                      type="text"
                      class="form-control"
                    />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Copyright Text</label>
                    <input
                      v-model="landingPage.footer_copyright"
                      type="text"
                      class="form-control"
                    />
                  </div>
                </div>
                <div class="row" v-if="landingPage.contact_info">
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Email</label>
                    <input
                      v-model="landingPage.contact_info.email"
                      type="email"
                      class="form-control"
                    />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Phone</label>
                    <input
                      v-model="landingPage.contact_info.phone"
                      type="tel"
                      class="form-control"
                    />
                  </div>
                </div>
                <h6>Social Links</h6>
                <div class="row" v-if="landingPage.footer_social_links">
                  <div class="col-md-3 mb-3">
                    <label class="form-label">Facebook</label>
                    <input v-model="landingPage.footer_social_links.facebook" type="text" class="form-control" />
                  </div>
                  <div class="col-md-3 mb-3">
                    <label class="form-label">Instagram</label>
                    <input v-model="landingPage.footer_social_links.instagram" type="text" class="form-control" />
                  </div>
                  <div class="col-md-3 mb-3">
                    <label class="form-label">Twitter</label>
                    <input v-model="landingPage.footer_social_links.twitter" type="text" class="form-control" />
                  </div>
                  <div class="col-md-3 mb-3">
                    <label class="form-label">LinkedIn</label>
                    <input v-model="landingPage.footer_social_links.linkedin" type="text" class="form-control" />
                  </div>
                </div>
              </div>

              <!-- Logos Tab -->
              <div v-show="activeTab === 'logos'">
                <div class="form-check form-switch mb-3">
                  <input
                    v-model="landingPage.show_logos"
                    class="form-check-input"
                    type="checkbox"
                    id="showLogos"
                  />
                  <label class="form-check-label" for="showLogos">Tampilkan Logos</label>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h6>Partner Logos</h6>
                  <button @click="addLogo" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i>Add Logo
                  </button>
                </div>
                <div v-for="(logo, index) in landingPage.logos" :key="index" class="card mb-3">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                      <h6 class="mb-0">Logo {{ index + 1 }}</h6>
                      <button @click="removeLogo(index)" class="btn btn-sm btn-danger">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label class="form-label">Name</label>
                        <input v-model="logo.name" type="text" class="form-control" />
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label">Image</label>
                        <div class="input-group mb-2">
                          <input v-model="logo.url" type="text" class="form-control" placeholder="https://..." />
                        </div>
                        <input type="file" class="form-control" accept="image/*" @change="(e) => uploadLogo(e, index)" />
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- FAQs Tab -->
              <div v-show="activeTab === 'faqs'">
                <div class="form-check form-switch mb-3">
                  <input
                    v-model="landingPage.show_faqs"
                    class="form-check-input"
                    type="checkbox"
                    id="showFaqs"
                  />
                  <label class="form-check-label" for="showFaqs">Tampilkan FAQs</label>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h6>Frequently Asked Questions</h6>
                  <button @click="addFaq" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i>Add FAQ
                  </button>
                </div>
                <div v-for="(faq, index) in landingPage.faqs" :key="index" class="card mb-3">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                      <h6 class="mb-0">FAQ {{ index + 1 }}</h6>
                      <button @click="removeFaq(index)" class="btn btn-sm btn-danger">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Question</label>
                      <input v-model="faq.question" type="text" class="form-control" />
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Answer</label>
                      <textarea v-model="faq.answer" class="form-control" rows="3"></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from "vue";
import axios from "axios";

const activeTab = ref("hero");
const previewMode = ref(false);
const loading = ref(false);
const API_URL = "http://localhost:8000/api";
const pageId = ref(null);

const availableIcons = [
  { class: "fas fa-star", name: "Star" },
  { class: "fas fa-rocket", name: "Rocket" },
  { class: "fas fa-leaf", name: "Leaf" },
  { class: "fas fa-cogs", name: "Settings" },
  { class: "fas fa-shield-alt", name: "Security" },
  { class: "fas fa-chart-line", name: "Analytics" },
  { class: "fas fa-wallet", name: "Wallet" },
  { class: "fas fa-users", name: "Users" },
  { class: "fas fa-headset", name: "Support" },
  { class: "fas fa-mobile-alt", name: "Mobile" },
  { class: "fas fa-globe", name: "Global" },
  { class: "fas fa-server", name: "Server" },
  { class: "fas fa-wifi", name: "Wifi" },
  { class: "fas fa-bolt", name: "Lightning" },
  { class: "fas fa-lock", name: "Lock" },
];

const landingPage = reactive({
  hero_title: "Platform ISP Billing Terlengkap",
  hero_subtitle: "Kelola Bisnis ISP Anda dengan Mudah",
  hero_image: "",
  hero_cta_text: "Mulai Sekarang",
  hero_cta_link: "/register",
  hero_gradient_from: "#667eea",
  hero_gradient_to: "#764ba2",
  show_features: true,
  features: [
    {
      icon: "fas fa-rocket",
      title: "Fast & Reliable",
      description: "Infrastructure yang stabil dan cepat untuk operasional ISP Anda",
    },
  ],
  show_pricing: true,
  pricing_plans: [
    {
      name: "Starter",
      price: 500000,
      period: "monthly",
      features: "Up to 100 customers\nBasic billing features",
      isPopular: false,
    },
  ],
  show_testimonials: true,
  testimonials: [
    {
      name: "John Doe",
      company: "ABC Internet Service",
      text: "Platform ini sangat membantu!",
      avatar: "https://i.pravatar.cc/150?img=1",
      rating: 5,
    },
  ],
  show_logos: true,
  logos: [],
  show_faqs: true,
  faqs: [],
  show_contact: true,
  contact_info: {
    email: "support@ispbilling.com",
    phone: "+62 21-1234-5678",
    address: "Jl. Jakarta No. 123",
  },
  footer_company_name: "ISP Billing System",
  footer_copyright: "© 2026 ISP Billing System. All rights reserved.",
  footer_social_links: {
    facebook: "",
    twitter: "",
    instagram: "",
    linkedin: "",
  },
  meta_title: "ISP Billing Pro",
  meta_description: "Solusi Manajemen ISP",
  meta_keywords: "isp, billing, mikrotik",
  // Styling defaults
  font_family: "Inter",
  font_size_base: "16px",
  color_primary: "#667eea",
  color_secondary: "#764ba2",
  color_accent: "#4f46e5",
  color_text: "#1a202c",
  color_background: "#ffffff",
  spacing_scale: 1.0,
  border_radius_base: "12px",
  button_style: "rounded",
  is_active: true,
});

const fetchLandingPage = async () => {
  try {
    loading.value = true;
    // Check both localStorage and sessionStorage for token
    const token = localStorage.getItem("auth_token") || sessionStorage.getItem("auth_token");
    const response = await axios.get(`${API_URL}/super-admin/landing-page`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    
    if (response.data) {
      pageId.value = response.data.id;
      
      // Clear existing data first to avoid merging
      landingPage.features = [];
      landingPage.pricing_plans = [];
      landingPage.testimonials = [];
      landingPage.logos = [];
      landingPage.faqs = [];
      
      // Now assign all data from response
      Object.assign(landingPage, response.data);
      
      // Handle potential null arrays (set to empty array if null)
      if (!landingPage.features) landingPage.features = [];
      if (!landingPage.pricing_plans) landingPage.pricing_plans = [];
      if (!landingPage.testimonials) landingPage.testimonials = [];
      if (!landingPage.logos) landingPage.logos = [];
      if (!landingPage.faqs) landingPage.faqs = [];
      if (!landingPage.contact_info) landingPage.contact_info = {};
      if (!landingPage.footer_social_links) landingPage.footer_social_links = {};
      
      console.log('Loaded landing page data:', {
        pricing_plans: landingPage.pricing_plans.length,
        testimonials: landingPage.testimonials.length,
        features: landingPage.features.length,
        logos: landingPage.logos.length,
        faqs: landingPage.faqs.length
      });
    }
  } catch (error) {
    console.error("Error fetching landing page:", error);
    alert("Error loading landing page: " + (error.response?.data?.message || error.message));
  } finally {
    loading.value = false;
  }
};

const saveLandingPage = async () => {
  try {
    loading.value = true;
    // Check both localStorage and sessionStorage for token
    const token = localStorage.getItem("auth_token") || sessionStorage.getItem("auth_token");
    
    // Prepare data (ensure booleans are handled)
    const payload = { ...landingPage };
    
    if (pageId.value) {
      await axios.put(`${API_URL}/super-admin/landing-page/${pageId.value}`, payload, {
        headers: { Authorization: `Bearer ${token}` },
      });
    } else {
      await axios.post(`${API_URL}/super-admin/landing-page`, payload, {
        headers: { Authorization: `Bearer ${token}` },
      });
    }
    
    alert("Landing page saved successfully!");
  } catch (error) {
    console.error("Error saving landing page:", error);
    alert("Failed to save: " + (error.response?.data?.message || error.message));
  } finally {
    loading.value = false;
  }
};

const uploadLogo = async (event, index) => {
  const file = event.target.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append("image", file);

  try {
    loading.value = true;
    const token = localStorage.getItem("auth_token") || sessionStorage.getItem("auth_token");
    const response = await axios.post(`${API_URL}/super-admin/landing-page/upload`, formData, {
      headers: {
        "Content-Type": "multipart/form-data",
        Authorization: `Bearer ${token}`,
      },
    });
    
    landingPage.logos[index].url = response.data.url;
    alert("Logo uploaded successfully");
  } catch (error) {
    console.error("Upload failed", error);
    alert("Upload failed: " + (error.response?.data?.message || error.message));
  } finally {
    loading.value = false;
  }
};

const addFeature = () => {
  landingPage.features.push({
    icon: "fas fa-star",
    title: "New Feature",
    description: "Feature description here",
  });
};

const removeFeature = (index) => {
  landingPage.features.splice(index, 1);
};

const addPricingPlan = () => {
  landingPage.pricing_plans.push({
    name: "New Plan",
    price: 0,
    period: "monthly",
    features: "",
    isPopular: false,
  });
};

const removePricingPlan = (index) => {
  landingPage.pricing_plans.splice(index, 1);
};

const addTestimonial = () => {
  landingPage.testimonials.push({
    name: "",
    company: "",
    text: "",
    avatar: "",
    rating: 5,
  });
};

const removeTestimonial = (index) => {
  landingPage.testimonials.splice(index, 1);
};

const addLogo = () => {
  landingPage.logos.push({
    name: "",
    url: "",
  });
};

const removeLogo = (index) => {
  landingPage.logos.splice(index, 1);
};

const addFaq = () => {
  landingPage.faqs.push({
    question: "",
    answer: "",
  });
};

const removeFaq = (index) => {
  landingPage.faqs.splice(index, 1);
};

const uploadHeroImage = async (event) => {
  const file = event.target.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append("image", file);

  try {
    loading.value = true;
    const token = localStorage.getItem("auth_token") || sessionStorage.getItem("auth_token");
    const response = await axios.post(`${API_URL}/super-admin/landing-page/upload`, formData, {
      headers: {
        "Content-Type": "multipart/form-data",
        Authorization: `Bearer ${token}`,
      },
    });
    
    landingPage.hero_image = response.data.url;
    alert("Hero image uploaded successfully");
  } catch (error) {
    console.error("Upload failed", error);
    alert("Upload failed: " + (error.response?.data?.message || error.message));
  } finally {
    loading.value = false;
  }
};

// Toggle preview - opens landing page in new tab
const togglePreview = () => {
  window.open('/', '_blank');
};

onMounted(() => {
  fetchLandingPage();
});
</script>

<style scoped>
.nav-tabs .nav-link {
  cursor: pointer;
}

.form-control-color {
  width: 100%;
  height: 38px;
}
</style>
