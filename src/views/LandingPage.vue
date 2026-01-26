<template>
  <div class="landing-page">
    <!-- Navigation -->
    <nav
      class="navbar navbar-expand-lg position-absolute top-0 z-index-3 w-100 shadow-none my-3 navbar-transparent"
    >
      <div class="container">
        <a class="navbar-brand font-weight-bolder text-white" href="/">
          <i class="fas fa-broadcast-tower me-2"></i>
          ISP Billing System
        </a>
        <button
          class="navbar-toggler shadow-none ms-2"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navigation"
        >
          <span class="navbar-toggler-icon mt-2">
            <span class="navbar-toggler-bar bar1"></span>
            <span class="navbar-toggler-bar bar2"></span>
            <span class="navbar-toggler-bar bar3"></span>
          </span>
        </button>
        <div class="collapse navbar-collapse w-100 pt-3 pb-2 py-lg-0">
          <ul class="navbar-nav navbar-nav-hover ms-auto">
            <li class="nav-item mx-2">
              <a
                class="nav-link ps-2 d-flex cursor-pointer align-items-center text-white"
                @click="scrollToPricing"
              >
                <i class="fas fa-tags me-1"></i> Harga
              </a>
            </li>
            <li class="nav-item mx-2">
              <router-link
                to="/login"
                class="nav-link ps-2 d-flex align-items-center text-white"
              >
                <i class="fas fa-sign-in-alt me-1"></i> Login
              </router-link>
            </li>
            <li class="nav-item ms-2">
              <argon-button
                color="success"
                variant="gradient"
                size="sm"
                @click="goToRegister"
              >
                <i class="fas fa-user-plus me-1"></i> Daftar Sekarang
              </argon-button>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <section
      class="hero-section min-vh-100 position-relative d-flex align-items-center"
      style="overflow: visible; padding-bottom: 100px"
    >
      <div
        class="position-absolute w-100 h-100 top-0 start-0"
        style="
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          z-index: 0;
        "
      ></div>

      <div class="container position-relative pt-7" style="z-index: 1">
        <div class="row align-items-center">
          <div class="col-lg-6 text-lg-start text-center">
            <span class="badge badge-white text-dark mb-3">
              <i class="fas fa-star text-warning"></i> Platform #1 untuk ISP
            </span>
            <h1
              class="display-1 font-weight-bold text-white mb-4"
              style="line-height: 1.2"
            >
              Sistem Billing ISP Terlengkap
            </h1>
            <p class="lead text-white mb-4 opacity-9 pe-lg-5">
              Platform manajemen ISP all-in-one dengan integrasi Mikrotik,
              payment gateway otomatis, dan sistem billing yang powerful. Kelola
              ribuan pelanggan dengan mudah.
            </p>
            <div
              class="d-flex gap-3 justify-content-lg-start justify-content-center mb-5"
            >
              <argon-button
                color="white"
                size="lg"
                class="shadow-lg px-5"
                @click="scrollToPricing"
              >
                <i class="fas fa-tags me-2"></i>
                Lihat Paket
              </argon-button>
              <argon-button
                color="success"
                variant="gradient"
                size="lg"
                class="shadow-lg px-5"
                @click="goToRegister"
              >
                <i class="fas fa-rocket me-2"></i>
                Mulai Gratis
              </argon-button>
            </div>

            <!-- Stats -->
            <div class="row mt-5 pt-3">
              <div class="col-4">
                <h3 class="text-white font-weight-bold mb-0">1,200+</h3>
                <p class="text-white opacity-8 mb-0 text-sm">ISP Aktif</p>
              </div>
              <div class="col-4">
                <h3 class="text-white font-weight-bold mb-0">50K+</h3>
                <p class="text-white opacity-8 mb-0 text-sm">Pelanggan</p>
              </div>
              <div class="col-4">
                <h3 class="text-white font-weight-bold mb-0">99.9%</h3>
                <p class="text-white opacity-8 mb-0 text-sm">Uptime</p>
              </div>
            </div>
          </div>
          <div class="col-lg-6 text-center mt-lg-0 mt-5 position-relative">
            <div class="position-relative" style="z-index: 2">
              <div class="bg-white p-5 rounded-3 shadow-xl">
                <i
                  class="fas fa-chart-line text-primary"
                  style="font-size: 120px; opacity: 0.8"
                ></i>
                <h4 class="text-dark mt-4">Dashboard ISP Billing</h4>
                <p class="text-muted">Kelola bisnis ISP Anda dengan mudah</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Wave -->
      <div
        class="position-absolute bottom-0 start-0 w-100"
        style="z-index: 1; margin-bottom: -2px"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 1440 320"
          style="display: block"
          preserveAspectRatio="none"
          width="100%"
          height="100"
        >
          <path
            fill="#ffffff"
            fill-opacity="1"
            d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,138.7C960,139,1056,117,1152,96C1248,75,1344,53,1392,42.7L1440,32L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"
          ></path>
        </svg>
      </div>
    </section>

    <!-- Features Section -->
    <section
      class="features-section py-7 bg-gray-100"
      style="position: relative; z-index: 2"
    >
      <div class="container">
        <div class="row text-center mb-5">
          <div class="col-lg-8 mx-auto">
            <span class="badge badge-primary mb-3">Fitur Lengkap</span>
            <h2 class="display-3 font-weight-bold mb-3">
              Semua yang Anda Butuhkan untuk Mengelola ISP
            </h2>
            <p class="lead text-muted">
              Platform lengkap dengan fitur-fitur modern untuk mempermudah
              operasional bisnis ISP Anda
            </p>
          </div>
        </div>

        <div class="row g-4">
          <div
            v-for="(feature, index) in features"
            :key="index"
            class="col-lg-3 col-md-6"
          >
            <div class="card h-100 shadow-sm border-0 hover-lift">
              <div class="card-body text-center p-4">
                <div
                  class="icon icon-shape rounded-circle mx-auto mb-3"
                  :class="`bg-gradient-${feature.color}`"
                  style="width: 64px; height: 64px"
                >
                  <i
                    :class="`${feature.icon} text-white opacity-10 text-lg`"
                  ></i>
                </div>
                <h5 class="font-weight-bold mb-2">{{ feature.title }}</h5>
                <p class="text-sm text-muted mb-0">{{ feature.description }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing Section -->
    <section
      id="pricing"
      class="pricing-section py-7 bg-white"
      style="position: relative; z-index: 2"
    >
      <div class="container">
        <div class="row text-center mb-5">
          <div class="col-lg-8 mx-auto">
            <span class="badge badge-success mb-3">Harga Terjangkau</span>
            <h2 class="display-3 font-weight-bold mb-3">
              Pilih Paket yang Sesuai dengan Kebutuhan Anda
            </h2>
            <p class="lead text-muted">
              Mulai dari paket starter hingga enterprise, semua dengan fitur
              lengkap
            </p>
          </div>
        </div>

        <div class="row g-4">
          <div
            v-for="(pkg, index) in packages"
            :key="index"
            class="col-lg-4 col-md-6"
          >
            <div
              class="card shadow-lg border-0 h-100"
              :class="{ 'border-success border-2': pkg.popular }"
            >
              <div
                v-if="pkg.popular"
                class="position-absolute top-0 start-50 translate-middle"
              >
                <span class="badge badge-success px-3 py-2"
                  >Paling Populer</span
                >
              </div>
              <div class="card-header text-center pt-4 pb-3 bg-transparent">
                <h5 class="font-weight-bold text-uppercase">{{ pkg.name }}</h5>
                <h1 class="display-2 font-weight-bold mt-3">
                  {{ formatCurrency(pkg.price) }}
                </h1>
                <p class="text-sm text-muted mb-0">/bulan</p>
              </div>
              <div class="card-body">
                <ul class="list-unstyled">
                  <li
                    v-for="(feature, idx) in pkg.features"
                    :key="idx"
                    class="py-2 border-bottom"
                  >
                    <i class="fas fa-check text-success me-2"></i>
                    <span>{{ feature }}</span>
                  </li>
                </ul>
              </div>
              <div class="card-footer bg-transparent text-center pt-0 pb-4">
                <argon-button
                  :color="pkg.popular ? 'success' : 'dark'"
                  :variant="pkg.popular ? 'gradient' : 'outline'"
                  class="w-100"
                  size="lg"
                  @click="selectPackage(pkg)"
                >
                  Pilih Paket {{ pkg.name }}
                </argon-button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section
      class="cta-section py-7 position-relative"
      style="
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        z-index: 2;
      "
    >
      <div class="container position-relative">
        <div class="row text-center">
          <div class="col-lg-8 mx-auto">
            <h2 class="display-3 font-weight-bold text-white mb-4">
              Siap Meningkatkan Bisnis ISP Anda?
            </h2>
            <p class="lead text-white opacity-9 mb-5">
              Bergabunglah dengan lebih dari 1,200 ISP yang telah mempercayai
              platform kami untuk mengelola bisnis mereka
            </p>
            <argon-button
              color="white"
              size="lg"
              class="shadow-lg px-5"
              @click="goToRegister"
            >
              <i class="fas fa-rocket me-2"></i>
              Mulai Sekarang - Gratis 14 Hari
            </argon-button>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer
      class="footer py-5 bg-gray-100"
      style="position: relative; z-index: 2"
    >
      <div class="container">
        <div class="row">
          <div class="col-lg-4 mb-lg-0 mb-4">
            <h5 class="font-weight-bold mb-3">
              <i class="fas fa-broadcast-tower me-2"></i>
              ISP Billing System
            </h5>
            <p class="text-sm text-muted">
              Platform manajemen ISP terlengkap di Indonesia
            </p>
          </div>
          <div class="col-lg-8">
            <div class="row">
              <div class="col-md-4 col-6 mb-4">
                <h6 class="font-weight-bold text-dark">Perusahaan</h6>
                <ul class="list-unstyled">
                  <li class="mb-2">
                    <a href="#" class="text-muted text-sm">Tentang Kami</a>
                  </li>
                  <li class="mb-2">
                    <a href="#" class="text-muted text-sm">Kontak</a>
                  </li>
                </ul>
              </div>
              <div class="col-md-4 col-6 mb-4">
                <h6 class="font-weight-bold text-dark">Produk</h6>
                <ul class="list-unstyled">
                  <li class="mb-2">
                    <a href="#" class="text-muted text-sm">Fitur</a>
                  </li>
                  <li class="mb-2">
                    <a href="#pricing" class="text-muted text-sm">Harga</a>
                  </li>
                </ul>
              </div>
              <div class="col-md-4 col-6 mb-4">
                <h6 class="font-weight-bold text-dark">Bantuan</h6>
                <ul class="list-unstyled">
                  <li class="mb-2">
                    <a href="#" class="text-muted text-sm">Dokumentasi</a>
                  </li>
                  <li class="mb-2">
                    <a href="#" class="text-muted text-sm">Support</a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <hr class="my-4" />
        <div class="row align-items-center">
          <div class="col-md-6">
            <p class="text-sm text-muted mb-0">
              © 2026 ISP Billing System. All rights reserved.
            </p>
          </div>
          <div class="col-md-6 text-md-end">
            <a href="#" class="text-muted me-3"
              ><i class="fab fa-facebook"></i
            ></a>
            <a href="#" class="text-muted me-3"
              ><i class="fab fa-twitter"></i
            ></a>
            <a href="#" class="text-muted"><i class="fab fa-instagram"></i></a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import ArgonButton from "@/components/ArgonButton.vue";

const router = useRouter();

const features = ref([
  {
    icon: "fas fa-wallet",
    color: "primary",
    title: "Payment Gateway",
    description:
      "Integrasi otomatis dengan Midtrans & Xendit untuk pembayaran realtime",
  },
  {
    icon: "fas fa-network-wired",
    color: "info",
    title: "Mikrotik Integration",
    description: "Kelola bandwidth dan isolasi pelanggan langsung dari sistem",
  },
  {
    icon: "fas fa-file-invoice-dollar",
    color: "success",
    title: "Billing Otomatis",
    description: "Generate invoice otomatis dan kirim ke pelanggan via email",
  },
  {
    icon: "fas fa-users",
    color: "warning",
    title: "Manajemen Pelanggan",
    description: "Database pelanggan lengkap dengan riwayat pembayaran",
  },
  {
    icon: "fas fa-tools",
    color: "danger",
    title: "Ticketing System",
    description: "Kelola keluhan dan permintaan pelanggan dengan mudah",
  },
  {
    icon: "fas fa-chart-line",
    color: "dark",
    title: "Laporan Lengkap",
    description: "Dashboard analytics dan laporan keuangan real-time",
  },
  {
    icon: "fas fa-user-shield",
    color: "primary",
    title: "Multi-Level Access",
    description: "Role management untuk admin, teknisi, dan customer",
  },
  {
    icon: "fas fa-mobile-alt",
    color: "success",
    title: "Responsive Design",
    description: "Akses dari desktop, tablet, atau smartphone dengan mudah",
  },
]);

const packages = ref([
  {
    name: "Starter",
    price: 299000,
    popular: false,
    features: [
      "Maksimal 100 Pelanggan",
      "1 User Admin",
      "2 Mikrotik Router",
      "Payment Gateway",
      "Email Notification",
      "Support via Email",
    ],
  },
  {
    name: "Professional",
    price: 599000,
    popular: true,
    features: [
      "Maksimal 500 Pelanggan",
      "3 User Admin",
      "5 Mikrotik Router",
      "Payment Gateway",
      "SMS & Email Notification",
      "Priority Support",
      "Custom Domain",
    ],
  },
  {
    name: "Enterprise",
    price: 1299000,
    popular: false,
    features: [
      "Unlimited Pelanggan",
      "Unlimited User",
      "Unlimited Router",
      "Payment Gateway",
      "SMS & Email & WhatsApp",
      "24/7 Support",
      "Custom Domain",
      "API Access",
    ],
  },
]);

const formatCurrency = (amount) => {
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    minimumFractionDigits: 0,
  }).format(amount);
};

const scrollToPricing = () => {
  const pricingSection = document.getElementById("pricing");
  if (pricingSection) {
    pricingSection.scrollIntoView({ behavior: "smooth" });
  }
};

const goToRegister = () => {
  router.push("/register");
};

const selectPackage = (pkg) => {
  router.push({
    name: "Register",
    query: { package: pkg.name.toLowerCase() },
  });
};
</script>

<style scoped>
.landing-page {
  width: 100%;
  overflow-x: hidden;
}

.hero-section {
  position: relative;
  overflow: visible !important;
}

.hover-lift {
  transition: all 0.3s ease;
  cursor: pointer;
}

.hover-lift:hover {
  transform: translateY(-10px);
  box-shadow:
    0 20px 25px -5px rgba(0, 0, 0, 0.1),
    0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
}

.badge {
  font-size: 0.875rem;
  padding: 0.5rem 1rem;
}

.icon-shape {
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.navbar-toggler-bar {
  display: block;
  position: relative;
  width: 22px;
  height: 2px;
  background: white;
  border-radius: 1px;
  margin: 4px auto;
  transition: all 0.2s;
}

.cursor-pointer {
  cursor: pointer;
}
</style>
