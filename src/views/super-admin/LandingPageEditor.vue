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
                  @click="previewMode = !previewMode"
                >
                  <i class="fas fa-eye me-2"></i
                  >{{ previewMode ? "Edit Mode" : "Preview" }}
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
                    <label class="form-label fw-bold">Main Heading</label>
                    <input
                      v-model="landingPage.hero.title"
                      type="text"
                      class="form-control"
                    />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Subtitle</label>
                    <input
                      v-model="landingPage.hero.subtitle"
                      type="text"
                      class="form-control"
                    />
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label fw-bold">Description</label>
                  <textarea
                    v-model="landingPage.hero.description"
                    class="form-control"
                    rows="3"
                  ></textarea>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold"
                      >Primary Button Text</label
                    >
                    <input
                      v-model="landingPage.hero.primaryButtonText"
                      type="text"
                      class="form-control"
                    />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold"
                      >Secondary Button Text</label
                    >
                    <input
                      v-model="landingPage.hero.secondaryButtonText"
                      type="text"
                      class="form-control"
                    />
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold"
                      >Background Image URL</label
                    >
                    <input
                      v-model="landingPage.hero.backgroundImage"
                      type="url"
                      class="form-control"
                    />
                    <small class="text-muted">Or upload file below</small>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold"
                      >Upload Background Image</label
                    >
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
                    <label class="form-label fw-bold"
                      >Gradient Start Color</label
                    >
                    <input
                      v-model="landingPage.hero.gradientStart"
                      type="color"
                      class="form-control form-control-color"
                    />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Gradient End Color</label>
                    <input
                      v-model="landingPage.hero.gradientEnd"
                      type="color"
                      class="form-control form-control-color"
                    />
                  </div>
                </div>
              </div>

              <!-- Features Section -->
              <div v-show="activeTab === 'features'">
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
                        <label class="form-label"
                          >Icon (FontAwesome class)</label
                        >
                        <input
                          v-model="feature.icon"
                          type="text"
                          class="form-control"
                          placeholder="fas fa-rocket"
                        />
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

              <!-- Pricing Plans -->
              <div v-show="activeTab === 'pricing'">
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
                  v-for="(plan, index) in landingPage.pricing"
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
                        <label class="form-label">Plan Name</label>
                        <input
                          v-model="plan.name"
                          type="text"
                          class="form-control"
                        />
                      </div>
                      <div class="col-md-3 mb-3">
                        <label class="form-label">Price (IDR)</label>
                        <input
                          v-model="plan.price"
                          type="number"
                          class="form-control"
                        />
                      </div>
                      <div class="col-md-3 mb-3">
                        <label class="form-label">Billing Period</label>
                        <select v-model="plan.period" class="form-control">
                          <option value="monthly">Monthly</option>
                          <option value="yearly">Yearly</option>
                        </select>
                      </div>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Features (one per line)</label>
                      <textarea
                        v-model="plan.features"
                        class="form-control"
                        rows="4"
                        placeholder="Feature 1&#10;Feature 2&#10;Feature 3"
                      ></textarea>
                    </div>
                    <div class="form-check">
                      <input
                        v-model="plan.isPopular"
                        type="checkbox"
                        class="form-check-input"
                        :id="'popular-' + index"
                      />
                      <label class="form-check-label" :for="'popular-' + index">
                        Mark as Popular (highlighted)
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Testimonials -->
              <div v-show="activeTab === 'testimonials'">
                <div class="d-flex justify-content-between mb-3">
                  <h6>Customer Testimonials</h6>
                  <button
                    class="btn btn-sm btn-primary"
                    @click="addTestimonial"
                  >
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
                        <label class="form-label">Customer Name</label>
                        <input
                          v-model="testimonial.name"
                          type="text"
                          class="form-control"
                        />
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label">Company/Title</label>
                        <input
                          v-model="testimonial.company"
                          type="text"
                          class="form-control"
                        />
                      </div>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Testimonial Text</label>
                      <textarea
                        v-model="testimonial.text"
                        class="form-control"
                        rows="3"
                      ></textarea>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Avatar URL</label>
                      <input
                        v-model="testimonial.avatar"
                        type="url"
                        class="form-control"
                      />
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Rating (1-5)</label>
                      <input
                        v-model.number="testimonial.rating"
                        type="number"
                        min="1"
                        max="5"
                        class="form-control"
                      />
                    </div>
                  </div>
                </div>
              </div>

              <!-- Footer -->
              <div v-show="activeTab === 'footer'">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Company Name</label>
                    <input
                      v-model="landingPage.footer.companyName"
                      type="text"
                      class="form-control"
                    />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Copyright Text</label>
                    <input
                      v-model="landingPage.footer.copyright"
                      type="text"
                      class="form-control"
                    />
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Email</label>
                    <input
                      v-model="landingPage.footer.email"
                      type="email"
                      class="form-control"
                    />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Phone</label>
                    <input
                      v-model="landingPage.footer.phone"
                      type="tel"
                      class="form-control"
                    />
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label fw-bold">Address</label>
                  <textarea
                    v-model="landingPage.footer.address"
                    class="form-control"
                    rows="2"
                  ></textarea>
                </div>
                <div class="row">
                  <div class="col-md-3 mb-3">
                    <label class="form-label">Facebook URL</label>
                    <input
                      v-model="landingPage.footer.social.facebook"
                      type="url"
                      class="form-control"
                    />
                  </div>
                  <div class="col-md-3 mb-3">
                    <label class="form-label">Twitter URL</label>
                    <input
                      v-model="landingPage.footer.social.twitter"
                      type="url"
                      class="form-control"
                    />
                  </div>
                  <div class="col-md-3 mb-3">
                    <label class="form-label">Instagram URL</label>
                    <input
                      v-model="landingPage.footer.social.instagram"
                      type="url"
                      class="form-control"
                    />
                  </div>
                  <div class="col-md-3 mb-3">
                    <label class="form-label">LinkedIn URL</label>
                    <input
                      v-model="landingPage.footer.social.linkedin"
                      type="url"
                      class="form-control"
                    />
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
import { ref, reactive } from "vue";

const activeTab = ref("hero");
const previewMode = ref(false);

const landingPage = reactive({
  hero: {
    title: "Platform ISP Billing Terlengkap",
    subtitle: "Kelola Bisnis ISP Anda dengan Mudah",
    description:
      "Sistem manajemen billing ISP yang powerful, user-friendly, dan scalable untuk mengembangkan bisnis internet Anda.",
    primaryButtonText: "Mulai Gratis",
    secondaryButtonText: "Lihat Demo",
    backgroundImage: "",
    gradientStart: "#667eea",
    gradientEnd: "#764ba2",
  },
  features: [
    {
      icon: "fas fa-rocket",
      title: "Fast & Reliable",
      description:
        "Infrastructure yang stabil dan cepat untuk operasional ISP Anda",
    },
    {
      icon: "fas fa-shield-alt",
      title: "Secure & Compliant",
      description:
        "Keamanan data terjamin dengan enkripsi dan compliance standards",
    },
    {
      icon: "fas fa-chart-line",
      title: "Analytics & Reports",
      description: "Dashboard analytics lengkap untuk monitor bisnis real-time",
    },
  ],
  pricing: [
    {
      name: "Starter",
      price: 500000,
      period: "monthly",
      features:
        "Up to 100 customers\nBasic billing features\nEmail support\n1 ISP location",
      isPopular: false,
    },
    {
      name: "Professional",
      price: 1500000,
      period: "monthly",
      features:
        "Up to 500 customers\nAdvanced billing & invoicing\nPriority support\n5 ISP locations\nCustom reports",
      isPopular: true,
    },
    {
      name: "Enterprise",
      price: 3500000,
      period: "monthly",
      features:
        "Unlimited customers\nFull features unlocked\n24/7 support\nUnlimited locations\nWhite-label option\nDedicated account manager",
      isPopular: false,
    },
  ],
  testimonials: [
    {
      name: "John Doe",
      company: "ABC Internet Service",
      text: "Platform ini sangat membantu dalam mengelola billing dan customer management. Highly recommended!",
      avatar: "https://i.pravatar.cc/150?img=1",
      rating: 5,
    },
  ],
  footer: {
    companyName: "ISP Billing System",
    copyright: "© 2026 ISP Billing System. All rights reserved.",
    email: "support@ispbilling.com",
    phone: "+62 812-3456-7890",
    address: "Jl. Tech Valley No. 123, Jakarta, Indonesia",
    social: {
      facebook: "https://facebook.com",
      twitter: "https://twitter.com",
      instagram: "https://instagram.com",
      linkedin: "https://linkedin.com",
    },
  },
});

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
  landingPage.pricing.push({
    name: "New Plan",
    price: 0,
    period: "monthly",
    features: "",
    isPopular: false,
  });
};

const removePricingPlan = (index) => {
  landingPage.pricing.splice(index, 1);
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

const uploadHeroImage = (event) => {
  const file = event.target.files[0];
  if (file) {
    // TODO: Upload to server and get URL
    const reader = new FileReader();
    reader.onload = (e) => {
      landingPage.hero.backgroundImage = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const saveLandingPage = () => {
  // TODO: API call to save
  console.log("Saving landing page:", landingPage);
  alert("Landing page saved successfully!");
};
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
