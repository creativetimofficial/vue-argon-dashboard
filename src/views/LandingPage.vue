<template>
  <div class="modern-landing-page" v-if="pageData" :style="cssVars">
    <!-- Scroll Progress Indicator -->
    <div class="scroll-progress" id="scroll-progress"></div>
    
    <nav class="navbar-modern" :class="{ 'navbar-mobile-open': mobileMenuOpen }">
      <div class="container-custom">
        <div class="navbar-content">
          <a href="/" class="brand" style="gap: 0;">
            <img src="/favicon.png" alt="Payneto Logo" class="brand-logo" style="height: 48px; margin-right: -12px; position: relative; z-index: 2;" />
            <span class="brand-text" style="font-size: 1.8rem;">ayneto</span>
          </a>
          
          <!-- Hamburger Menu Button -->
          <button class="menu-toggle" @click="mobileMenuOpen = !mobileMenuOpen" aria-label="Toggle Menu">
            <i class="fas" :class="mobileMenuOpen ? 'fa-times' : 'fa-bars'"></i>
          </button>

          <!-- Desktop Navigation -->
          <div class="nav-links desktop-only">
            <a v-if="pageData.show_features" href="#features">Fitur</a>
            <a v-if="pageData.show_pricing" href="#pricing">Harga</a>
            <a v-if="pageData.show_faqs" href="#faq">FAQ</a>
            <router-link to="/login" class="btn-outline">Masuk</router-link>
            <button @click="goToRegister" class="btn-primary">Daftar Gratis</button>
          </div>
        </div>
      </div>

    </nav>
    
    <!-- Mobile Navigation Menu - Hostinger-like minimal redesign -->
    <Transition name="fade-slide">
      <div v-if="mobileMenuOpen" class="mobile-menu-overlay">
        <!-- Menu Header -->
        <div class="mobile-menu-header">
          <div class="brand" style="gap: 0;">
            <img src="/favicon.png" alt="Payneto Logo" class="brand-logo" style="height: 48px; margin-right: -12px;" />
            <span class="brand-text" style="font-size: 1.8rem;">ayneto</span>
          </div>
          <button class="menu-close" @click="mobileMenuOpen = false" aria-label="Close Menu">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <div class="mobile-nav-body">
          <div class="menu-links-card">
            <div class="menu-section-label">Menu</div>
            <a v-if="pageData.show_features" href="#features" @click="mobileMenuOpen = false">
              <span>Fitur</span>
              <i class="fas fa-chevron-right mobile-chevron"></i>
            </a>
            <a v-if="pageData.show_pricing" href="#pricing" @click="mobileMenuOpen = false">
              <span>Harga</span>
              <i class="fas fa-chevron-right mobile-chevron"></i>
            </a>
            <a v-if="pageData.show_faqs" href="#faq" @click="mobileMenuOpen = false">
              <span>FAQ</span>
              <i class="fas fa-chevron-right mobile-chevron"></i>
            </a>
          </div>

          <!-- Actions Area -->
          <div class="mobile-menu-actions">
            <router-link to="/login" class="mobile-btn-outline-sleek" @click="mobileMenuOpen = false">
              Masuk
            </router-link>
            <button @click="goToRegister(); mobileMenuOpen = false" class="mobile-btn-primary-sleek">
              Daftar Gratis
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Hero Section -->
    <section class="hero-gradient">
      <!-- Animated Background with More Particles -->
      <div class="hero-bg-animation">
        <div class="floating-shape shape-1"></div>
        <div class="floating-shape shape-2"></div>
        <div class="floating-shape shape-3"></div>
        <div class="floating-shape shape-4"></div>
        <div class="floating-shape shape-5"></div>
        <div class="floating-shape shape-6"></div>
        <div class="floating-shape shape-7"></div>
        <div class="floating-shape shape-8"></div>
        <!-- Animated Grid Pattern -->
        <div class="grid-pattern"></div>
      </div>
      
      <div class="container-custom">
        <div class="hero-grid">
          <div class="hero-content">
            <div class="badge-pill animate-fade-in" v-if="pageData.hero_badge_text">
              {{ pageData.hero_badge_text }}
            </div>
            <h1 class="hero-title animate-slide-up" style="color: #344767 !important;">{{ pageData.hero_title }}</h1>
            <p class="hero-subtitle animate-slide-up delay-1" style="color: #67748e !important;">{{ pageData.hero_subtitle }}</p>
            <div class="hero-actions animate-slide-up delay-2">
              <button @click="goToRegister" class="btn-hero-primary">
                {{ pageData.hero_cta_text }}
              </button>
              <button v-if="pageData.show_pricing" @click="scrollTo('pricing')" class="btn-hero-secondary">
                Lihat Pricing
              </button>
            </div>
          </div>
          <div class="hero-visual animate-slide-up delay-3">
            <div class="dashboard-mockup" ref="dashboardMockup" @mousemove="handleMouseMove" @mouseleave="handleMouseLeave">
              <img :src="pageData.hero_image || 'https://placehold.co/600x400/f0f0f0/667eea?text=Dashboard+Preview'" alt="Dashboard Preview" />
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Logos Section -->
    <section v-if="pageData.show_logos && pageData.logos && pageData.logos.length" class="logos-section">
      <div class="container-custom">
        <p class="logos-title">Terintegrasi dengan</p>
        <div class="logos-grid">
          <div v-for="(logo, index) in pageData.logos" :key="index" class="logo-item">
            <img :src="logo.url" :alt="logo.name" />
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section id="features" v-if="pageData.show_features && pageData.features && pageData.features.length" class="features-section observe-me">
      <div class="container-custom">
        <div class="section-header">
          <h2>Fitur Lengkap untuk Bisnis ISP Anda</h2>
          <p>Semua yang Anda butuhkan dalam satu platform</p>
        </div>
        <div class="features-grid">
          <div v-for="(feature, index) in pageData.features" :key="index" class="feature-card observe-card" :style="{ animationDelay: `${index * 0.1}s` }">
            <div class="feature-icon">
              <i :class="feature.icon || 'fas fa-star'"></i>
            </div>
            <h3>{{ feature.title }}</h3>
            <p>{{ feature.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" v-if="pageData.show_pricing && pageData.pricing_plans && pageData.pricing_plans.length" class="pricing-section observe-me">
      <div class="container-custom">
        <div class="section-header">
          <h2>Pilih Paket yang Tepat</h2>
          <p>Harga transparan, tanpa biaya tersembunyi</p>
        </div>
        <div class="pricing-grid">
          <div v-for="(plan, index) in pageData.pricing_plans" :key="index" class="pricing-card observe-card" :class="{ 'popular': plan.isPopular }" :style="{ animationDelay: `${index * 0.15}s` }">
            <div v-if="plan.isPopular" class="popular-badge">PALING POPULER</div>
            <div class="pricing-header">
              <h3>{{ plan.name }}</h3>
              <div class="price">
                <span class="currency">Rp</span>
                <span class="amount">{{ formatPrice(plan.price) }}</span>
                <span class="period">/{{ plan.period }}</span>
              </div>
            </div>
            <ul class="features-list">
              <li v-for="(feature, idx) in (plan.features ? plan.features.split('\n') : [])" :key="idx">
                <i class="fas fa-check"></i>{{ feature }}
              </li>
            </ul>
            <button @click="selectPackage(plan.name)" class="btn-pricing" :class="{ 'btn-pricing-popular': plan.isPopular }">
              Pilih Paket
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials / Team Section -->
    <section v-if="pageData.show_testimonials && pageData.testimonials && pageData.testimonials.length" class="testimonials-section">
      <div class="container-custom">
        <div class="section-header">
          <h2>Apa Kata Mereka?</h2>
          <p>Dipercaya oleh ratusan ISP di seluruh Indonesia</p>
        </div>
        <div class="testimonials-grid">
          <div v-for="(testi, index) in pageData.testimonials" :key="index" class="testimonial-card">
            <div class="quote-icon">
              <i class="fas fa-quote-left"></i>
            </div>
            <p class="testimonial-text">{{ testi.text }}</p>
            <div class="testimonial-author">
              <div class="author-avatar">
                {{ testi.name.charAt(0) }}
              </div>
              <div class="author-info">
                <h4>{{ testi.name }}</h4>
                <p>{{ testi.company }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" v-if="pageData.show_faqs && pageData.faqs && pageData.faqs.length" class="faq-section">
      <div class="container-custom">
        <div class="section-header">
          <h2>Pertanyaan yang Sering Diajukan</h2>
          <p>Temukan jawaban untuk pertanyaan Anda</p>
        </div>
        <div class="faq-container">
          <div v-for="(faq, index) in pageData.faqs" :key="index" class="faq-item" :class="{ 'active': activeFaq === index }">
            <button class="faq-question" @click="toggleFaq(index)">
              <span>{{ faq.question }}</span>
              <i class="fas" :class="activeFaq === index ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>
            <div class="faq-answer" v-show="activeFaq === index">
              <p>{{ faq.answer }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
      <div class="container-custom">
        <div class="cta-content">
          <h2>Siap untuk Memulai?</h2>
          <p>Bergabunglah dengan ratusan ISP yang sudah menggunakan platform kami</p>
          <button @click="goToRegister" class="btn-cta">
            Daftar Gratis Sekarang
          </button>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="footer-modern">
      <div class="container-custom">
        <div class="footer-grid">
          <div class="footer-col">
            <h3>
              <img src="/favicon.png" alt="Payneto Logo" class="brand-logo" style="height: 38px; vertical-align: middle; margin-right: -18px;" />
              ayneto
            </h3>
            <p>{{ pageData.meta_description }}</p>
            <div class="social-links" v-if="pageData.footer_social_links">
              <a v-if="pageData.footer_social_links.facebook" :href="pageData.footer_social_links.facebook" target="_blank">
                <i class="fab fa-facebook"></i>
              </a>
              <a v-if="pageData.footer_social_links.twitter" :href="pageData.footer_social_links.twitter" target="_blank">
                <i class="fab fa-twitter"></i>
              </a>
              <a v-if="pageData.footer_social_links.instagram" :href="pageData.footer_social_links.instagram" target="_blank">
                <i class="fab fa-instagram"></i>
              </a>
              <a v-if="pageData.footer_social_links.linkedin" :href="pageData.footer_social_links.linkedin" target="_blank">
                <i class="fab fa-linkedin"></i>
              </a>
            </div>
          </div>
          <div class="footer-col" v-if="pageData.show_contact && pageData.contact_info">
            <h4>Hubungi Kami</h4>
            <ul>
              <li><i class="fas fa-envelope"></i>{{ pageData.contact_info.email }}</li>
              <li><i class="fas fa-phone"></i>{{ pageData.contact_info.phone }}</li>
              <li><i class="fas fa-map-marker-alt"></i>{{ pageData.contact_info.address }}</li>
            </ul>
          </div>
          <div class="footer-col">
            <h4>Link Cepat</h4>
            <ul>
              <li><a href="#features">Fitur</a></li>
              <li><a href="#pricing">Harga</a></li>
              <li><a href="#faq">FAQ</a></li>
            </ul>
          </div>
        </div>
        <div class="footer-bottom">
          <p>{{ pageData.footer_copyright }}</p>
        </div>
      </div>
    </footer>
  </div>

  <!-- Loading State -->
  <div v-else class="loading-state">
    <div class="spinner"></div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watchEffect } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();
const pageData = ref(null);
const activeFaq = ref(null);
const mobileMenuOpen = ref(false);
const API_URL = "http://localhost:8000/api";
const scrollProgress = ref(0);
const dashboardMockup = ref(null);

const cssVars = computed(() => {
  if (!pageData.value) return {};
  return {
    '--color-primary': pageData.value.color_primary || '#667eea',
    '--color-secondary': pageData.value.color_secondary || '#764ba2',
    '--color-accent': pageData.value.color_accent || '#4f46e5',
    '--color-text': pageData.value.color_text || '#1a202c',
    '--color-background': pageData.value.color_background || '#ffffff',
    '--font-family': pageData.value.font_family || 'Inter',
    '--font-size-base': pageData.value.font_size_base || '16px',
    '--spacing-scale': pageData.value.spacing_scale || 1,
    '--border-radius': pageData.value.border_radius_base || '12px',
  };
});

// Dynamic Font Loader
watchEffect(() => {
  if (pageData.value?.font_family) {
    const fontName = pageData.value.font_family;
    const linkId = 'dynamic-font-loader';
    let link = document.getElementById(linkId);
    
    if (!link) {
      link = document.createElement('link');
      link.id = linkId;
      link.rel = 'stylesheet';
      document.head.appendChild(link);
    }
    
    link.href = `https://fonts.googleapis.com/css2?family=${fontName.replace(/ /g, '+')}:wght@300;400;500;600;700&display=swap`;
  }
});

const fetchLandingPage = async () => {
  try {
    const response = await axios.get(`${API_URL}/landing-page`);
    pageData.value = response.data;
  } catch (error) {
    console.error("Error fetching landing page:", error);
  }
};

const formatPrice = (amount) => {
  return new Intl.NumberFormat("en-US").format(amount);
};

const scrollTo = (id) => {
  const element = document.getElementById(id);
  if (element) {
    element.scrollIntoView({ behavior: "smooth", block: "start" });
  }
};

const goToRegister = () => {
  router.push("/register");
};

const selectPackage = (pkgName) => {
  router.push({
    name: "Register",
    query: { package: pkgName.toLowerCase() },
  });
};

const toggleFaq = (index) => {
  activeFaq.value = activeFaq.value === index ? null : index;
};

// Scroll Progress Indicator
const updateScrollProgress = () => {
  const winScroll = document.documentElement.scrollTop;
  const height = document.documentElement.scrollHeight - window.innerHeight;
  const scrolled = (winScroll / height) * 100;
  scrollProgress.value = scrolled;
  const progressBar = document.getElementById('scroll-progress');
  if (progressBar) {
    progressBar.style.width = scrolled + '%';
  }
};

// Intersection Observer for Scroll Animations
const observeElements = () => {
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('animate-in');
        }
      });
    },
    { threshold: 0.1, rootMargin: '0px 0px -100px 0px' }
  );

  // Observe sections
  document.querySelectorAll('.observe-card').forEach((el) => {
    observer.observe(el);
  });
};

// Parallax Mouse Tracking Effect
const handleMouseMove = (e) => {
  if (!dashboardMockup.value) return;
  
  const rect = dashboardMockup.value.getBoundingClientRect();
  const x = e.clientX - rect.left;
  const y = e.clientY - rect.top;
  
  const centerX = rect.width / 2;
  const centerY = rect.height / 2;
  
  const rotateX = ((y - centerY) / centerY) * -10; // Invert axis for better feel
  const rotateY = ((centerX - x) / centerX) * 10; 
  
  dashboardMockup.value.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-8px) scale(1.02)`;
};

const handleMouseLeave = () => {
  if (!dashboardMockup.value) return;
  dashboardMockup.value.style.transform = 'translateY(0)';
};

onMounted(() => {
  fetchLandingPage();
  
  // Setup scroll listener
  window.addEventListener('scroll', updateScrollProgress, { passive: true });
  
  // Setup intersection observer after DOM is ready
  // Use nextTick to ensure Vue has finished rendering
  setTimeout(() => {
    observeElements();
  }, 300);
});

// Cleanup on unmount
import { onUnmounted } from 'vue';
onUnmounted(() => {
  window.removeEventListener('scroll', updateScrollProgress);
});
</script>

<style scoped>
/* @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap'); */

/* ========== SCROLL PROGRESS INDICATOR ========== */
.scroll-progress {
  position: fixed;
  top: 0;
  left: 0;
  height: 4px;
  background: linear-gradient(90deg, var(--color-primary), var(--color-secondary), var(--color-accent));
  z-index: 9999;
  transition: width 0.1s ease-out;
  box-shadow: 0 2px 10px rgba(94, 114, 228, 0.5);
  will-change: width;
}


* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html {
  scroll-behavior: smooth;
}

.modern-landing-page {
  font-family: var(--font-family), -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  font-size: var(--font-size-base);
  color: var(--color-text);
  background: var(--color-background);
  line-height: 1.6;
  overflow-x: hidden;
}

.container-custom {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 calc(20px * var(--spacing-scale));
}

/* Navbar - Enhanced Glassmorphism */
.navbar-modern {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  background: rgba(255, 255, 255, 1); /* Force solid white */
  border-bottom: 2px solid #2dce89; /* Themed bottom border */
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  z-index: 10000; /* High z-index */
  padding: 12px 0;
  transition: all 0.3s ease;
}

.navbar-modern:hover {
  background: rgba(255, 255, 255, 0.95);
  box-shadow: 0 8px 40px rgba(0, 0, 0, 0.08);
}

.navbar-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.brand {
  font-size: calc(22px * var(--spacing-scale));
  font-weight: 700;
  background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 10px;
  transition: all 0.3s ease;
}

.brand:hover {
  transform: scale(1.05);
}

.brand i {
  background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.nav-links {
  display: flex;
  align-items: center;
  gap: calc(28px * var(--spacing-scale));
}

.nav-links a {
  color: #344767;
  text-decoration: none;
  font-weight: 600;
  position: relative;
  transition: all 0.3s ease;
}

/* BRAND ENHANCEMENTS */
.brand-logo {
  height: 42px;
  width: auto;
  object-fit: contain;
}

.brand-text {
  font-weight: 800;
  font-size: 1.4rem;
  letter-spacing: -0.5px;
  background: linear-gradient(135deg, #2DCE89, #2dcecc);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

/* MOBILE MENU TOGGLE */
.menu-toggle {
  display: none;
  background-color: #ffffff !important; /* Solid white background for the button */
  border: 1px solid rgba(0,0,0,0.1);
  font-size: 1.5rem;
  color: #2dce89 !important; /* Theme green color (Argon success/primary) */
  cursor: pointer;
  z-index: 10001;
  width: 45px;
  height: 45px;
  align-items: center;
  justify-content: center;
  border-radius: 10px;
  transition: all 0.2s;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.menu-toggle:hover {
  background: #f8f9fe !important;
  transform: scale(1.05);
  color: #2dcecc !important; /* Slightly different green on hover */
}

/* RESPONSIVE NAVBAR */
@media (max-width: 991px) {
  .desktop-only {
    display: none !important;
  }
  
  .menu-toggle {
    display: flex;
  }

  .navbar-content {
    justify-content: space-between;
  }

  /* Mobile Menu Overlay - Full Screen Solid White */
  .mobile-menu-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100vh;
    background-color: #f8f9fe !important; /* Soft solid grey background */
    z-index: 10001;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
  }

  .mobile-menu-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    background: #ffffff;
    border-bottom: 1px solid #e9ecef;
  }

  .menu-close {
    background: #f8f9fe;
    border: 1px solid #e9ecef;
    width: 38px;
    height: 38px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #8898aa;
    cursor: pointer;
    transition: all 0.2s;
  }

  .menu-close:hover {
    background: #ffffff;
    color: #2dce89;
    border-color: #2dce89;
  }

  .mobile-nav-body {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
  }

  .menu-links-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 1rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    border: 1px solid rgba(0,0,0,0.05);
  }

  .menu-section-label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #adb5bd;
    font-weight: 800;
    margin-bottom: 0.5rem;
    padding-left: 0.5rem;
  }

  .menu-links-card a {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0.5rem;
    font-size: 1.1rem;
    font-weight: 600;
    color: #32325d !important;
    text-decoration: none;
    border-bottom: 1px solid #f6f9fc;
  }

  .menu-links-card a:last-child {
    border-bottom: none;
  }

  .mobile-chevron {
    font-size: 0.8rem;
    color: #ced4da; /* Very light subtle grey */
    opacity: 0.6;
    transition: all 0.2s;
  }

  .menu-links-card a:hover .mobile-chevron {
    color: #2dce89;
    opacity: 1;
    transform: translateX(3px);
  }

  .mobile-menu-actions {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
  }

  .mobile-btn-outline-sleek {
    width: 100%;
    padding: 14px;
    background: #ffffff;
    color: #2dce89;
    border: 2px solid #2dce89;
    border-radius: 10px;
    font-weight: 700;
    font-size: 1.1rem;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
    transition: all 0.2s;
  }

  .mobile-btn-outline-sleek:hover {
    background: #f8f9fe;
    color: #2dcecc;
    border-color: #2dcecc;
  }

  .mobile-btn-primary-sleek {
    width: 100%;
    padding: 16px;
    background: #2dce89;
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 700;
    font-size: 1.1rem;
    box-shadow: 0 4px 15px rgba(45, 206, 137, 0.25);
    cursor: pointer;
    transition: all 0.2s;
  }

  /* Transition */
  .fade-slide-enter-active,
  .fade-slide-leave-active {
    transition: all 0.3s ease;
  }

  .fade-slide-enter-from {
    opacity: 0;
    transform: translateY(-20px);
  }

  .fade-slide-leave-to {
    opacity: 0;
    transform: translateY(-20px);
  }
}

.nav-links a {
  color: var(--color-text);
  text-decoration: none;
  font-weight: 600;
  position: relative;
  transition: color 0.3s ease;
}

.nav-links a::after {
  content: '';
  position: absolute;
  bottom: -4px;
  left: 0;
  width: 0;
  height: 2px;
  background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
  transition: width 0.3s ease;
}

.nav-links a:hover {
  color: var(--color-primary);
}

.nav-links a:hover::after {
  width: 100%;
}

.btn-outline {
  padding: calc(10px * var(--spacing-scale)) calc(24px * var(--spacing-scale));
  border: 2px solid var(--color-primary);
  border-radius: var(--border-radius);
  color: var(--color-primary);
  background: transparent;
  font-weight: 700;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  text-decoration: none;
  display: inline-block;
  position: relative;
  overflow: hidden;
}

.btn-outline::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
  transition: left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: -1;
}

.btn-outline:hover {
  color: white !important;
  text-decoration: none;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(94, 114, 228, 0.3);
}

.btn-outline:hover::before {
  left: 0;
}

.btn-outline:active,
.btn-outline:focus {
  color: white !important;
  text-decoration: none;
  outline: none;
}

.btn-outline:visited {
  color: var(--color-primary);
}

a.btn-outline:active,
a.btn-outline:focus {
  color: white !important;
}

.btn-primary {
  padding: calc(10px * var(--spacing-scale)) calc(24px * var(--spacing-scale));
  background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
  border: none;
  border-radius: var(--border-radius);
  color: white;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 15px rgba(94, 114, 228, 0.3);
  position: relative;
  overflow: hidden;
}

.btn-primary::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.3);
  transform: translate(-50%, -50%);
  transition: width 0.6s, height 0.6s;
}

.btn-primary:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 30px rgba(94, 114, 228, 0.4);
}

.btn-primary:hover::before {
  width: 300px;
  height: 300px;
}

/* Hero Section - Enhanced with Particles */
.hero-gradient {
  position: relative;
  background: #f8f9fa; /* Light background like Sneat */
  background-image: radial-gradient(at 0% 0%, hsla(253,16%,7%,0) 0, hsla(253,16%,7%,0) 50%), 
                    radial-gradient(at 50% 0%, hsla(225,39%,30%,0) 0, hsla(225,39%,30%,0) 50%), 
                    radial-gradient(at 100% 0%, hsla(339,49%,30%,0) 0, hsla(339,49%,30%,0) 50%);
  padding: calc(140px * var(--spacing-scale)) 0 calc(100px * var(--spacing-scale));
  margin-top: 70px;
  color: var(--color-text); /* Dark text */
  overflow: hidden;
}

.hero-gradient::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: radial-gradient(circle at 20% 50%, rgba(17, 205, 239, 0.2) 0%, transparent 50%),
              radial-gradient(circle at 80% 80%, rgba(130, 94, 228, 0.2) 0%, transparent 50%);
  animation: pulseGlow 8s ease-in-out infinite;
}

@keyframes pulseGlow {
  0%, 100% { opacity: 0.5; }
  50% { opacity: 1; }
}

.hero-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: calc(80px * var(--spacing-scale));
  align-items: center;
  position: relative;
  z-index: 1;
}

.badge-pill {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: rgba(255, 255, 255, 0.25);
  backdrop-filter: blur(10px);
  padding: calc(10px * var(--spacing-scale)) calc(20px * var(--spacing-scale));
  border-radius: 50px;
  font-size: calc(14px * var(--spacing-scale));
  font-weight: 700;
  margin-bottom: calc(28px * var(--spacing-scale));
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.hero-title {
  font-size: calc(56px * var(--spacing-scale));
  font-weight: 800;
  line-height: 1.15;
  margin-bottom: calc(24px * var(--spacing-scale));
  text-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
  letter-spacing: -0.02em;
}

.hero-subtitle {
  font-size: calc(20px * var(--spacing-scale));
  opacity: 0.95;
  margin-bottom: calc(40px * var(--spacing-scale));
  line-height: 1.8;
  font-weight: 400;
}

.hero-actions {
  display: flex;
  gap: calc(20px * var(--spacing-scale));
  flex-wrap: wrap;
}

.btn-hero-primary {
  padding: calc(16px * var(--spacing-scale)) calc(40px * var(--spacing-scale));
  background: white;
  color: var(--color-primary);
  border: none;
  border-radius: var(--border-radius);
  font-weight: 700;
  font-size: calc(17px * var(--spacing-scale));
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
  position: relative;
  overflow: hidden;
}

.btn-hero-primary::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  border-radius: 50%;
  background: rgba(94, 114, 228, 0.2);
  transform: translate(-50%, -50%);
  transition: width 0.6s, height 0.6s;
}

.btn-hero-primary:hover {
  transform: translateY(-4px);
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.25);
}

.btn-hero-primary:hover::before {
  width: 400px;
  height: 400px;
}

.btn-hero-secondary {
  padding: calc(16px * var(--spacing-scale)) calc(40px * var(--spacing-scale));
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  color: white;
  border: 2px solid rgba(255, 255, 255, 0.5);
  border-radius: var(--border-radius);
  font-weight: 700;
  font-size: calc(17px * var(--spacing-scale));
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.btn-hero-secondary:hover {
  background: white;
  color: var(--color-primary);
  border-color: white;
  transform: translateY(-4px);
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
}

.dashboard-mockup {
  background: transparent;
  border-radius: calc(var(--border-radius) * 2);
  padding: 0;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08),
              0 0 0 1px rgba(0, 0, 0, 0.04);
  transform: translateY(0);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

@keyframes float {
  0%, 100% {
    transform: perspective(1500px) rotateY(-5deg) rotateX(2deg) translateY(0px);
  }
  50% {
    transform: perspective(1500px) rotateY(-5deg) rotateX(2deg) translateY(-15px);
  }
}

.dashboard-mockup:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12),
              0 0 0 1px rgba(94, 114, 228, 0.1);
}

.dashboard-mockup img {
  width: 100%;
  border-radius: calc(var(--border-radius) * 2);
  display: block;
  transition: transform 0.4s ease;
}

.dashboard-mockup:hover img {
  transform: scale(1.02);
}

/* Logos Section */
.logos-section {
  padding: calc(40px * var(--spacing-scale)) 0;
  background: #f8f9fa;
}

.logos-title {
  text-align: center;
  color: #6c757d;
  font-size: calc(14px * var(--spacing-scale));
  margin-bottom: calc(24px * var(--spacing-scale));
  text-transform: uppercase;
  letter-spacing: 1px;
}

.logos-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: calc(32px * var(--spacing-scale));
  align-items: center;
}

.logo-item {
  display: flex;
  justify-content: center;
  align-items: center;
  opacity: 0.6;
  transition: opacity 0.3s;
}

.logo-item:hover {
  opacity: 1;
}

.logo-item img {
  max-height: 40px;
  max-width: 100%;
}

/* Features Section */
.features-section {
  padding: calc(80px * var(--spacing-scale)) 0;
}

.section-header {
  text-align: center;
  margin-bottom: calc(60px * var(--spacing-scale));
}

.section-header h2 {
  font-size: calc(36px * var(--spacing-scale));
  font-weight: 700;
  margin-bottom: calc(12px * var(--spacing-scale));
  color: var(--color-text);
}

.section-header p {
  font-size: calc(18px * var(--spacing-scale));
  color: #6c757d;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: calc(40px * var(--spacing-scale));
}

.feature-card {
  padding: calc(40px * var(--spacing-scale));
  background: white;
  border: 1px solid rgba(94, 114, 228, 0.1);
  border-radius: calc(var(--border-radius) * 1.5);
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.feature-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 4px;
  background: linear-gradient(90deg, var(--color-primary), var(--color-secondary), var(--color-accent));
  transform: scaleX(0);
  transform-origin: left;
  transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.feature-card:hover {
  transform: translateY(-12px) scale(1.02);
  box-shadow: 0 25px 50px rgba(94, 114, 228, 0.2);
  border-color: var(--color-primary);
}

.feature-card:hover::before {
  transform: scaleX(1);
}

.feature-icon {
  width: 72px;
  height: 72px;
  background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
  border-radius: calc(var(--border-radius) * 1.2);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: calc(24px * var(--spacing-scale));
  color: white;
  font-size: calc(32px * var(--spacing-scale));
  box-shadow: 0 8px 20px rgba(94, 114, 228, 0.3);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.feature-card:hover .feature-icon {
  transform: scale(1.1) rotate(5deg);
  box-shadow: 0 12px 30px rgba(94, 114, 228, 0.4);
}

.feature-card h3 {
  font-size: calc(22px * var(--spacing-scale));
  font-weight: 700;
  margin-bottom: calc(14px * var(--spacing-scale));
  color: var(--color-text);
  transition: color 0.3s ease;
}

.feature-card:hover h3 {
  color: var(--color-primary);
}

.feature-card p {
  color: #67748e;
  line-height: 1.8;
  font-size: calc(15px * var(--spacing-scale));
}

/* Pricing Section - Enhanced */
.pricing-section {
  padding: calc(100px * var(--spacing-scale)) 0;
  background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
}

.pricing-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: calc(40px * var(--spacing-scale));
}

.pricing-card {
  background: white;
  border-radius: calc(var(--border-radius) * 1.5);
  padding: calc(48px * var(--spacing-scale));
  border: 2px solid rgba(94, 114, 228, 0.1);
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.pricing-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, var(--color-primary), var(--color-secondary), var(--color-accent));
  transform: scaleX(0);
  transform-origin: left;
  transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.pricing-card:hover {
  transform: translateY(-12px) scale(1.02);
  box-shadow: 0 30px 60px rgba(94, 114, 228, 0.2);
  border-color: var(--color-primary);
}

.pricing-card:hover::before {
  transform: scaleX(1);
}

.pricing-card.popular {
  border-color: var(--color-primary);
  box-shadow: 0 20px 50px rgba(94, 114, 228, 0.25);
  transform: scale(1.05);
}

.pricing-card.popular:hover {
  transform: translateY(-12px) scale(1.07);
  box-shadow: 0 35px 70px rgba(94, 114, 228, 0.3);
}

.popular-badge {
  position: absolute;
  top: -14px;
  left: 50%;
  transform: translateX(-50%);
  background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
  color: white;
  padding: calc(8px * var(--spacing-scale)) calc(20px * var(--spacing-scale));
  border-radius: 50px;
  font-size: calc(11px * var(--spacing-scale));
  font-weight: 800;
  letter-spacing: 1px;
  box-shadow: 0 4px 15px rgba(94, 114, 228, 0.4);
}

.pricing-header h3 {
  font-size: calc(24px * var(--spacing-scale));
  font-weight: 700;
  margin-bottom: calc(16px * var(--spacing-scale));
  text-transform: uppercase;
  letter-spacing: 1px;
}

.price {
  margin-bottom: calc(32px * var(--spacing-scale));
  display: flex;
  align-items: baseline;
  gap: 4px;
  flex-wrap: wrap; /* Allow wrapping on small screens */
}

.currency {
  font-size: clamp(16px, 4vw, 20px);
  font-weight: 600;
  color: var(--color-text);
  white-space: nowrap;
}

.amount {
  font-size: clamp(32px, 8vw, 48px);
  font-weight: 800;
  color: var(--color-text);
  letter-spacing: -1px;
}

.period {
  font-size: clamp(14px, 3.5vw, 16px);
  color: #6c757d;
  white-space: nowrap;
}

.features-list {
  list-style: none;
  margin-bottom: calc(32px * var(--spacing-scale));
}

.features-list li {
  padding: calc(12px * var(--spacing-scale)) 0;
  color: var(--color-text);
  display: flex;
  align-items: flex-start;
  gap: 12px;
}

.features-list i {
  color: var(--color-primary);
  margin-top: 4px;
}

.btn-pricing {
  width: 100%;
  padding: calc(14px * var(--spacing-scale));
  border: 2px solid var(--color-primary);
  background: transparent;
  color: var(--color-primary);
  border-radius: var(--border-radius);
  font-weight: 700;
  font-size: calc(16px * var(--spacing-scale));
  cursor: pointer;
  transition: all 0.3s;
}

.btn-pricing:hover {
  background: var(--color-primary);
  color: white;
}

.btn-pricing-popular {
  background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
  color: white;
  border: none;
}

.btn-pricing-popular:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
}

/* Testimonials Section */
.testimonials-section {
  padding: calc(80px * var(--spacing-scale)) 0;
}

.testimonials-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: calc(32px * var(--spacing-scale));
}

.testimonial-card {
  background: white;
  border: 1px solid #e9ecef;
  border-radius: var(--border-radius);
  padding: calc(32px * var(--spacing-scale));
  transition: all 0.3s;
}

.testimonial-card:hover {
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.quote-icon {
  font-size: calc(32px * var(--spacing-scale));
  color: var(--color-primary);
  opacity: 0.2;
  margin-bottom: calc(16px * var(--spacing-scale));
}

.testimonial-text {
  color: var(--color-text);
  line-height: 1.7;
  margin-bottom: calc(24px * var(--spacing-scale));
}

.testimonial-author {
  display: flex;
  align-items: center;
  gap: calc(16px * var(--spacing-scale));
}

.author-avatar {
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 700;
  font-size: calc(20px * var(--spacing-scale));
}

.author-info h4 {
  font-size: calc(16px * var(--spacing-scale));
  font-weight: 600;
  margin-bottom: 4px;
}

.author-info p {
  font-size: calc(14px * var(--spacing-scale));
  color: #6c757d;
}

/* FAQ Section */
.faq-section {
  padding: calc(80px * var(--spacing-scale)) 0;
  background: #f8f9fa;
}

.faq-container {
  max-width: 800px;
  margin: 0 auto;
}

.faq-item {
  background: white;
  border-radius: var(--border-radius);
  margin-bottom: calc(16px * var(--spacing-scale));
  overflow: hidden;
  border: 1px solid #e9ecef;
}

.faq-question {
  width: 100%;
  padding: calc(20px * var(--spacing-scale)) calc(24px * var(--spacing-scale));
  background: white;
  border: none;
  text-align: left;
  font-size: calc(16px * var(--spacing-scale));
  font-weight: 600;
  color: var(--color-text);
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: all 0.3s;
}

.faq-question:hover {
  background: #f8f9fa;
}

.faq-item.active .faq-question {
  color: var(--color-primary);
}

.faq-answer {
  padding: 0 calc(24px * var(--spacing-scale)) calc(20px * var(--spacing-scale));
  color: #6c757d;
  line-height: 1.7;
}

/* CTA Section - Enhanced with Animation */
.cta-section {
  padding: calc(100px * var(--spacing-scale)) 0;
  background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
  background-size: 200% 200%;
  color: white;
  text-align: center;
  position: relative;
  overflow: hidden;
  animation: gradientShift 15s ease infinite;
}

.cta-section::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
  animation: rotate 30s linear infinite;
}

@keyframes rotate {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.cta-content {
  position: relative;
  z-index: 1;
}

.cta-content h2 {
  font-size: calc(48px * var(--spacing-scale));
  font-weight: 800;
  margin-bottom: calc(20px * var(--spacing-scale));
  text-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
}

.cta-content p {
  font-size: calc(20px * var(--spacing-scale));
  opacity: 0.95;
  margin-bottom: calc(40px * var(--spacing-scale));
  font-weight: 400;
}

.btn-cta {
  padding: calc(18px * var(--spacing-scale)) calc(56px * var(--spacing-scale));
  background: white;
  color: var(--color-primary);
  border: none;
  border-radius: var(--border-radius);
  font-weight: 800;
  font-size: calc(18px * var(--spacing-scale));
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  position: relative;
  overflow: hidden;
}

.btn-cta::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  border-radius: 50%;
  background: rgba(94, 114, 228, 0.2);
  transform: translate(-50%, -50%);
  transition: width 0.6s, height 0.6s;
}

.btn-cta:hover {
  transform: translateY(-4px) scale(1.05);
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
}

.btn-cta:hover::before {
  width: 400px;
  height: 400px;
}

/* Footer */
.footer-modern {
  background: #1a202c;
  color: white;
  padding: calc(60px * var(--spacing-scale)) 0 calc(30px * var(--spacing-scale));
}

.footer-grid {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr;
  gap: calc(48px * var(--spacing-scale));
  margin-bottom: calc(40px * var(--spacing-scale));
}

.footer-col h3 {
  font-size: calc(20px * var(--spacing-scale));
  margin-bottom: calc(16px * var(--spacing-scale));
  display: flex;
  align-items: center;
  gap: 8px;
}

.footer-col h4 {
  font-size: calc(16px * var(--spacing-scale));
  margin-bottom: calc(16px * var(--spacing-scale));
  font-weight: 600;
}

.footer-col p {
  color: rgba(255, 255, 255, 0.7);
  line-height: 1.7;
  margin-bottom: calc(20px * var(--spacing-scale));
}

.footer-col ul {
  list-style: none;
}

.footer-col ul li {
  margin-bottom: calc(12px * var(--spacing-scale));
  color: rgba(255, 255, 255, 0.7);
  display: flex;
  align-items: center;
  gap: 10px;
}

.footer-col ul li i {
  color: var(--color-primary);
}

.footer-col a {
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
  transition: color 0.3s;
}

.footer-col a:hover {
  color: white;
}

.social-links {
  display: flex;
  gap: calc(16px * var(--spacing-scale));
}

.social-links a {
  width: 40px;
  height: 40px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s;
}

.social-links a:hover {
  background: var(--color-primary);
  transform: translateY(-2px);
}

.footer-bottom {
  text-align: center;
  padding-top: calc(30px * var(--spacing-scale));
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  color: rgba(255, 255, 255, 0.5);
}

/* Loading State */
.loading-state {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid var(--color-primary);
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 992px) {
  .hero-grid {
    grid-template-columns: 1fr;
    gap: calc(40px * var(--spacing-scale));
  }
  
  .features-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .pricing-grid {
    grid-template-columns: 1fr;
  }
  
  .testimonials-grid {
    grid-template-columns: 1fr;
  }
  
  .footer-grid {
    grid-template-columns: 1fr;
  }
  
  .logos-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .nav-links {
    display: none;
  }
  
  .hero-title {
    font-size: calc(32px * var(--spacing-scale));
  }
  
  .features-grid {
    grid-template-columns: 1fr;
  }
}

/* Animations */
@keyframes float {
  0%, 100% {
    transform: translateY(0px) rotate(0deg);
  }
  50% {
    transform: translateY(-20px) rotate(5deg);
  }
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Hero Background Animation */
.hero-bg-animation {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  overflow: hidden;
  z-index: 0;
}

.floating-shape {
  position: absolute;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  animation: float 6s ease-in-out infinite;
}

.shape-1 {
  width: 300px;
  height: 300px;
  top: 10%;
  left: 5%;
  animation-delay: 0s;
  animation-duration: 8s;
}

.shape-2 {
  width: 200px;
  height: 200px;
  top: 60%;
  right: 10%;
  animation-delay: 1s;
  animation-duration: 10s;
}

.shape-3 {
  width: 150px;
  height: 150px;
  bottom: 20%;
  left: 15%;
  animation-delay: 2s;
  animation-duration: 7s;
}

.shape-4 {
  width: 250px;
  height: 250px;
  top: 30%;
  right: 20%;
  animation-delay: 1.5s;
  animation-duration: 9s;
}

/* Additional Floating Shapes for More Dynamic Effect */
.shape-5 {
  width: 180px;
  height: 180px;
  top: 50%;
  left: 30%;
  animation-delay: 0.5s;
  animation-duration: 11s;
  background: rgba(255, 255, 255, 0.08);
}

.shape-6 {
  width: 120px;
  height: 120px;
  bottom: 30%;
  right: 25%;
  animation-delay: 2.5s;
  animation-duration: 8.5s;
  background: rgba(255, 255, 255, 0.12);
}

.shape-7 {
  width: 220px;
  height: 220px;
  top: 70%;
  left: 50%;
  animation-delay: 1.2s;
  animation-duration: 10.5s;
  background: rgba(255, 255, 255, 0.07);
}

.shape-8 {
  width: 160px;
  height: 160px;
  top: 15%;
  right: 35%;
  animation-delay: 3s;
  animation-duration: 9.5s;
  background: rgba(255, 255, 255, 0.09);
}

/* Animated Grid Pattern */
.grid-pattern {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-image: 
    linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
    linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
  background-size: 50px 50px;
  animation: gridMove 20s linear infinite;
  opacity: 0.5;
}

@keyframes gridMove {
  0% {
    transform: translate(0, 0);
  }
  100% {
    transform: translate(50px, 50px);
  }
}

/* Entrance Animations */
.animate-fade-in {
  animation: fadeIn 0.8s ease-out;
}

.animate-slide-up {
  animation: slideUp 0.8s ease-out;
}

.delay-1 {
  animation-delay: 0.2s;
  opacity: 0;
  animation-fill-mode: forwards;
}

.delay-2 {
  animation-delay: 0.4s;
  opacity: 0;
  animation-fill-mode: forwards;
}

.delay-3 {
  animation-delay: 0.6s;
  opacity: 0;
  animation-fill-mode: forwards;
}

/* ========== SCROLL-BASED ANIMATIONS ========== */
/* Start with elements visible, then add animation class */
.observe-me,
.observe-card {
  opacity: 1;
  transform: translateY(0);
}

/* Only apply initial hidden state when will-animate class is added */
.observe-me.will-animate,
.observe-card.will-animate {
  opacity: 0;
  transform: translateY(40px);
  transition: opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1),
              transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.observe-me.animate-in,
.observe-card.animate-in {
  opacity: 1 !important;
  transform: translateY(0) !important;
}

/* ========== ENHANCED HOVER EFFECTS WITH 3D TILT ========== */
.feature-card {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  will-change: transform;
  position: relative;
  overflow: hidden;
}

.feature-card::after {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: linear-gradient(
    45deg,
    transparent 30%,
    rgba(255, 255, 255, 0.1) 50%,
    transparent 70%
  );
  transform: translateX(-100%);
  transition: transform 0.6s;
}

.feature-card:hover::after {
  transform: translateX(100%);
}

.feature-card:hover {
  transform: translateY(-12px) scale(1.02) rotateX(2deg);
  box-shadow: 0 25px 50px rgba(94, 114, 228, 0.25);
}

.feature-card:hover .feature-icon {
  animation: iconBounce 0.6s ease;
}

@keyframes iconBounce {
  0%, 100% { transform: scale(1) rotate(0deg); }
  25% { transform: scale(1.1) rotate(-5deg); }
  50% { transform: scale(1.15) rotate(5deg); }
  75% { transform: scale(1.1) rotate(-3deg); }
}

/* ========== PRICING CARD ENHANCEMENTS ========== */
.pricing-card {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  will-change: transform;
  position: relative;
  overflow: hidden;
}

.pricing-card::after {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: linear-gradient(
    45deg,
    transparent 30%,
    rgba(255, 255, 255, 0.15) 50%,
    transparent 70%
  );
  transform: translateX(-100%);
  transition: transform 0.6s;
}

.pricing-card:hover::after {
  transform: translateX(100%);
}

.pricing-card:hover {
  transform: translateY(-12px) scale(1.03);
  box-shadow: 0 30px 60px rgba(94, 114, 228, 0.3);
}

/* Shimmer Effect on Popular Badge */
.popular-badge {
  position: relative;
  overflow: hidden;
}

.popular-badge::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(255, 255, 255, 0.4),
    transparent
  );
  animation: shimmer 2s infinite;
}

@keyframes shimmer {
  0% { left: -100%; }
  100% { left: 100%; }
}

/* Pulse Animation on Price Amount */
.pricing-card.popular .amount {
  animation: pricePulse 2s ease-in-out infinite;
}

@keyframes pricePulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.05); }
}

.testimonial-card {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  will-change: transform;
  position: relative;
  overflow: hidden;
}

.testimonial-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(94, 114, 228, 0.05),
    transparent
  );
  transition: left 0.5s;
}

.testimonial-card:hover::before {
  left: 100%;
}

.testimonial-card:hover {
  transform: translateY(-8px) scale(1.02);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

/* ========== BUTTON ENHANCEMENTS ========== */
.btn-hero-primary {
  position: relative;
  overflow: hidden;
  will-change: transform;
}

.btn-hero-primary::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.3);
  transform: translate(-50%, -50%);
  transition: width 0.6s, height 0.6s;
}

.btn-hero-primary:hover::before {
  width: 400px;
  height: 400px;
}

/* Glow Effect on Hover */
.btn-hero-primary:hover {
  animation: buttonGlow 1.5s ease-in-out infinite;
}

@keyframes buttonGlow {
  0%, 100% {
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15),
                0 0 20px rgba(94, 114, 228, 0.3);
  }
  50% {
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.25),
                0 0 40px rgba(94, 114, 228, 0.6);
  }
}

/* Gradient Animation */
.hero-gradient {
  position: relative;
  background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
  background-size: 200% 200%;
  animation: gradientShift 15s ease infinite;
  will-change: background-position;
}

@keyframes gradientShift {
  0% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0% 50%;
  }
}

/* ========== NAVBAR ENHANCEMENTS ========== */
.navbar-modern {
  will-change: background, box-shadow;
}

/* ========== PERFORMANCE OPTIMIZATIONS ========== */
/* Use GPU acceleration for smooth animations */
.floating-shape,
.feature-card,
.pricing-card,
.testimonial-card,
.btn-hero-primary,
.btn-primary,
.dashboard-mockup {
  transform: translateZ(0);
  backface-visibility: hidden;
  perspective: 1000px;
}

/* Reduce motion for users who prefer it */
@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}

/* ========== HIDE RECAPTCHA BADGE ========== */
/* Hide reCAPTCHA badge on landing page - it's loaded by login/register pages */
/* Using display:none instead of visibility:hidden for complete removal */
.grecaptcha-badge {
  display: none !important;
  visibility: hidden !important;
  opacity: 0 !important;
  pointer-events: none !important;
}
</style>
