<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

defineProps({
  ispName: {
    type: String,
    default: 'Payneto'
  }
});

const isMenuOpen = ref(false);
const isScrolled = ref(false);

const handleScroll = () => {
  isScrolled.value = window.scrollY > 20;
};

onMounted(() => {
  window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});

const features = [
  { icon: 'bx-rocket', title: 'Kecepatan Tinggi', desc: 'Akses internet hingga 1Gbps dengan jaringan Fiber Optic terbaru.', color: 'primary' },
  { icon: 'bx-money', title: 'Harga Terjangkau', desc: 'Paket internet hemat mulai dari 100rb tanpa biaya tersembunyi.', color: 'success' },
  { icon: 'bx-support', title: 'Dukungan 24/7', desc: 'Tim teknis kami siap membantu kendala jaringan Anda kapan saja.', color: 'info' },
  { icon: 'bx-wifi', title: 'Stabil & Unlimited', desc: 'Koneksi stabil tanpa batasan kuota (FUP) untuk aktivitas digital Anda.', color: 'warning' },
  { icon: 'bx-shield-quarter', title: 'Aman & Privat', desc: 'Jaringan aman dengan perlindungan firewall untuk menjaga data Anda.', color: 'error' },
  { icon: 'bx-game', title: 'Low Latency', desc: 'Ping rendah yang sangat cocok untuk gaming dan streaming 4K.', color: 'secondary' },
];

const packages = [
  { name: 'Home', price: '150.000', speed: '10 Mbps', devices: '2-3', popular: false },
  { name: 'Family', price: '250.000', speed: '30 Mbps', devices: '4-6', popular: true },
  { name: 'Gamer & Business', price: '500.000', speed: '100 Mbps', priority: true, popular: false },
];
</script>

<template>
  <VApp>
    <!-- Custom Navbar (Sneat Style) -->
    <header :class="['landing-navbar', { 'navbar-scrolled': isScrolled }]">
      <div class="navbar-container">
        <!-- Logo -->
        <a href="#" class="navbar-brand">
          <div class="brand-icon">
            <i class="bx bx-wifi"></i>
          </div>
          <span class="brand-text">{{ ispName }}</span>
        </a>

        <!-- Desktop Menu -->
        <nav class="desktop-menu">
          <a href="#features" class="nav-link">Fitur</a>
          <a href="#pricing" class="nav-link">Paket</a>
          <a href="#contact" class="nav-link">Kontak</a>
          <div class="divider"></div>
          <router-link to="/login" class="btn-login">Masuk</router-link>
          <router-link to="/register" class="btn-register">Daftar</router-link>
        </nav>

        <!-- Mobile Toggle Button -->
        <button class="mobile-toggle" @click="isMenuOpen = !isMenuOpen">
          <i class="bx" :class="isMenuOpen ? 'bx-x' : 'bx-menu'"></i>
        </button>
      </div>
    </header>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu" :class="{ 'is-open': isMenuOpen }">
      <nav class="mobile-nav">
        <a href="#features" class="mobile-link" @click="isMenuOpen = false">Fitur</a>
        <a href="#pricing" class="mobile-link" @click="isMenuOpen = false">Paket</a>
        <a href="#contact" class="mobile-link" @click="isMenuOpen = false">Kontak</a>
      </nav>
      <div class="mobile-actions">
        <router-link to="/login" class="btn-login-mobile" @click="isMenuOpen = false">Masuk</router-link>
        <router-link to="/register" class="btn-register-mobile" @click="isMenuOpen = false">Daftar</router-link>
      </div>
    </div>

    <VMain class="bg-background pt-16" style="min-height: 100vh;">
      <!-- Hero Section -->
      <section class="py-12 py-md-24">
        <VContainer>
          <VRow align="center">
            <VCol cols="12" md="6" class="text-center text-md-left pr-md-8">
              <h1 class="text-h4 text-sm-h3 text-md-h2 font-weight-bold text-primary mb-6" style="line-height: 1.2">
                Internet Cepat,<br>Koneksi Tepat.
              </h1>
              <p class="text-body-1 text-medium-emphasis mb-8 px-4 px-md-0">
                {{ ispName }} menghadirkan layanan internet broadband fiber optic berkecepatan tinggi dan stabil untuk kebutuhan rumah dan bisnis Anda.
              </p>
              <div class="d-flex flex-column flex-sm-row justify-center justify-md-start mb-12 mb-md-0 px-4 px-md-0" style="gap: 1.5rem;">
                <VBtn size="large" color="primary" to="/register" class="font-weight-bold w-100 w-sm-auto">Mulai Berlangganan</VBtn>
                <VBtn size="large" variant="outlined" href="#pricing" class="font-weight-bold w-100 w-sm-auto">Lihat Paket</VBtn>
              </div>
              
              <VRow class="mt-8 mx-0 d-none d-md-flex">
                <VCol cols="4" class="px-0">
                  <h3 class="text-h4 font-weight-bold mb-1">100+</h3>
                  <span class="text-caption text-medium-emphasis text-uppercase">Customers</span>
                </VCol>
                <VCol cols="4" class="px-0 border-s border-e">
                  <h3 class="text-h4 font-weight-bold mb-1">99%</h3>
                  <span class="text-caption text-medium-emphasis text-uppercase">Uptime</span>
                </VCol>
                <VCol cols="4" class="px-0">
                  <h3 class="text-h4 font-weight-bold mb-1">24/7</h3>
                  <span class="text-caption text-medium-emphasis text-uppercase">Support</span>
                </VCol>
              </VRow>
            </VCol>
            
            <VCol cols="12" md="6">
              <VImg 
                src="https://images.unsplash.com/photo-1551434678-e076c223a692?w=800&h=600&fit=crop" 
                alt="Internet Connection"
                class="rounded-lg elevation-4 w-100"
                cover
                :aspect-ratio="4/3"
              />
            </VCol>
          </VRow>
        </VContainer>
      </section>

      <!-- Features Section -->
      <section id="features" class="py-16 bg-surface">
        <VContainer>
          <div class="text-center mb-12">
            <VChip color="primary" variant="tonal" size="small" class="mb-4 font-weight-bold text-uppercase">Mengapa Memilih Kami?</VChip>
            <h2 class="text-h4 font-weight-bold mb-4">Layanan Internet Terbaik Untuk Anda</h2>
            <p class="text-body-1 text-medium-emphasis">Kami menjamin pengalaman berselancar yang mulus tanpa hambatan.</p>
          </div>

          <VRow>
            <VCol v-for="feat in features" :key="feat.title" cols="12" sm="6" md="4">
              <VCard variant="outlined" class="h-100 border-opacity-50 hover-card" color="transparent">
                <VCardText class="pa-6">
                  <VAvatar :color="feat.color" variant="tonal" rounded size="48" class="mb-4">
                    <VIcon size="28">{{ feat.icon }}</VIcon>
                  </VAvatar>
                  <h3 class="text-h6 font-weight-bold mb-2">{{ feat.title }}</h3>
                  <p class="text-body-2 text-medium-emphasis mb-0">{{ feat.desc }}</p>
                </VCardText>
              </VCard>
            </VCol>
          </VRow>
        </VContainer>
      </section>

      <!-- Pricing Section -->
      <section id="pricing" class="py-16">
        <VContainer>
          <div class="text-center mb-12">
            <VChip color="primary" variant="tonal" size="small" class="mb-4 font-weight-bold text-uppercase">Pilihan Paket</VChip>
            <h2 class="text-h4 font-weight-bold mb-4">Pilih Paket Sesuai Kebutuhan</h2>
            <p class="text-body-1 text-medium-emphasis">Berlangganan sekarang dan nikmati promo menariknya.</p>
          </div>

          <VRow justify="center">
            <VCol v-for="pkg in packages" :key="pkg.name" cols="12" md="4">
              <VCard 
                :variant="pkg.popular ? 'elevated' : 'outlined'" 
                :color="pkg.popular ? 'surface' : 'transparent'"
                :elevation="pkg.popular ? 8 : 0"
                :class="['h-100 position-relative hover-card', pkg.popular ? 'border-primary border-opacity-100 border-sm' : 'border-opacity-50']"
                style="overflow: visible;"
              >
                <div v-if="pkg.popular" class="position-absolute w-100 text-center" style="top: -12px; z-index: 1;">
                  <VChip 
                    color="primary" 
                    size="small" 
                    variant="flat"
                    class="font-weight-bold px-4 elevation-2"
                  >
                    PALING POPULER
                  </VChip>
                </div>
                
                <VCardItem class="text-center pt-8 pb-4">
                  <VCardTitle class="text-h5 font-weight-bold mb-4">{{ pkg.name }}</VCardTitle>
                  <div class="d-flex justify-center align-end gap-1">
                    <span class="text-h3 font-weight-bold text-primary">Rp{{ pkg.price }}</span>
                    <span class="text-body-2 text-medium-emphasis mb-2">/bln</span>
                  </div>
                </VCardItem>

                <VCardText class="px-6 pb-8">
                  <VList density="compact" class="mb-6" style="background: transparent !important;">
                    <VListItem class="px-0" style="background: transparent !important;">
                      <template #prepend><VIcon color="primary" size="20" class="mr-3">bx-check</VIcon></template>
                      <VListItemTitle class="text-body-2 font-weight-medium">Kecepatan hingga {{ pkg.speed }}</VListItemTitle>
                    </VListItem>
                    <VListItem class="px-0" style="background: transparent !important;">
                      <template #prepend><VIcon color="primary" size="20" class="mr-3">bx-check</VIcon></template>
                      <VListItemTitle class="text-body-2 font-weight-medium">Kuota Unlimited</VListItemTitle>
                    </VListItem>
                    <VListItem v-if="pkg.devices" class="px-0" style="background: transparent !important;">
                      <template #prepend><VIcon color="primary" size="20" class="mr-3">bx-check</VIcon></template>
                      <VListItemTitle class="text-body-2 font-weight-medium">Ideal untuk {{ pkg.devices }} Perangkat</VListItemTitle>
                    </VListItem>
                    <VListItem v-if="pkg.priority" class="px-0" style="background: transparent !important;">
                      <template #prepend><VIcon color="primary" size="20" class="mr-3">bx-check</VIcon></template>
                      <VListItemTitle class="text-body-2 font-weight-medium">Prioritas Trafik</VListItemTitle>
                    </VListItem>
                    <VListItem class="px-0" :class="pkg.priority ? '' : 'text-medium-emphasis text-decoration-line-through'" style="background: transparent !important;">
                      <template #prepend><VIcon :color="pkg.priority ? 'primary' : 'medium-emphasis'" size="20" class="mr-3">{{ pkg.priority ? 'bx-check' : 'bx-x' }}</VIcon></template>
                      <VListItemTitle class="text-body-2 font-weight-medium">Public Static IP</VListItemTitle>
                    </VListItem>
                  </VList>
                  
                  <VBtn 
                    block 
                    :variant="pkg.popular ? 'elevated' : 'outlined'" 
                    color="primary" 
                    size="large"
                    to="/register"
                  >
                    Pilih Paket
                  </VBtn>
                </VCardText>
              </VCard>
            </VCol>
          </VRow>
        </VContainer>
      </section>

      <!-- CTA Section -->
      <section class="py-16">
        <VContainer>
          <VCard color="primary" class="text-white overflow-hidden elevation-6 rounded-xl">
            <VCardText class="pa-8 pa-md-12 text-center text-md-left">
              <VRow align="center">
                <VCol cols="12" md="8">
                  <h2 class="text-h4 font-weight-bold mb-4">Siap untuk koneksi lebih cepat?</h2>
                  <p class="text-body-1 opacity-90 mb-0" style="max-width: 600px">
                    Bergabunglah dengan ratusan pelanggan lain yang telah mempercayakan koneksi internetnya kepada kami.
                  </p>
                </VCol>
                <VCol cols="12" md="4" class="text-center text-md-right mt-6 mt-md-0">
                  <VBtn color="white" variant="elevated" class="text-primary font-weight-bold px-8" size="x-large" to="/register">
                    Daftar Sekarang
                  </VBtn>
                </VCol>
              </VRow>
            </VCardText>
          </VCard>
        </VContainer>
      </section>
    </VMain>

    <!-- Footer -->
    <VFooter class="bg-surface pt-16 pb-8 border-t d-block" elevation="2">
      <VContainer id="contact">
        <VRow class="mb-8">
          <VCol cols="12" md="4" class="mb-6 mb-md-0 pr-md-8 text-center text-md-left">
            <div class="d-flex align-center justify-center justify-md-start" style="gap: 1rem; margin-bottom: 2rem;">
              <VAvatar color="primary" rounded size="36">
                <VIcon color="white" size="20">bx-wifi</VIcon>
              </VAvatar>
              <span class="text-h6 font-weight-bold text-primary">{{ ispName }}</span>
            </div>
            <p class="text-body-2 text-medium-emphasis">
              Penyedia layanan internet (ISP) terpercaya dengan komitmen memberikan koneksi stabil dan layanan terbaik.
            </p>
          </VCol>
          
          <VCol cols="12" sm="4" md="2" class="mb-6 mb-sm-0 text-center text-md-left">
            <h4 class="text-subtitle-1 font-weight-bold mb-4">Layanan</h4>
            <div class="d-flex flex-column gap-2 text-body-2 text-medium-emphasis">
              <a href="#" class="text-decoration-none text-inherit hover-primary">Internet Rumah</a>
              <a href="#" class="text-decoration-none text-inherit hover-primary">Bisnis</a>
              <a href="#" class="text-decoration-none text-inherit hover-primary">Dedicated Server</a>
            </div>
          </VCol>
          
          <VCol cols="12" sm="4" md="2" class="mb-6 mb-sm-0 text-center text-md-left">
            <h4 class="text-subtitle-1 font-weight-bold mb-4">Perusahaan</h4>
            <div class="d-flex flex-column gap-2 text-body-2 text-medium-emphasis">
              <a href="#" class="text-decoration-none text-inherit hover-primary">Tentang Kami</a>
              <a href="#" class="text-decoration-none text-inherit hover-primary">Karir</a>
              <a href="#" class="text-decoration-none text-inherit hover-primary">Kontak</a>
            </div>
          </VCol>
          
          <VCol cols="12" sm="4" md="4">
            <h4 class="text-subtitle-1 font-weight-bold mb-4 text-center text-md-left">Hubungi Kami</h4>
            <VList density="compact" class="bg-transparent pa-0">
              <VListItem class="px-0 min-h-0 mb-2 justify-center justify-md-start">
                <template #prepend><VIcon size="20" class="mr-3 text-primary">bx-phone</VIcon></template>
                <VListItemTitle class="text-body-2 text-medium-emphasis">0812-3456-7890</VListItemTitle>
              </VListItem>
              <VListItem class="px-0 min-h-0 mb-2 justify-center justify-md-start">
                <template #prepend><VIcon size="20" class="mr-3 text-primary">bx-envelope</VIcon></template>
                <VListItemTitle class="text-body-2 text-medium-emphasis">support@{{ ispName.toLowerCase().replace(/\s+/g, '') }}.com</VListItemTitle>
              </VListItem>
              <VListItem class="px-0 min-h-0 justify-center justify-md-start">
                <template #prepend><VIcon size="20" class="mr-3 text-primary">bx-map</VIcon></template>
                <VListItemTitle class="text-body-2 text-medium-emphasis">Jakarta, Indonesia</VListItemTitle>
              </VListItem>
            </VList>
          </VCol>
        </VRow>
        
        <VDivider class="mb-6" />
        
        <div class="text-center text-caption text-medium-emphasis">
          © {{ new Date().getFullYear() }} {{ ispName }}. All rights reserved.
        </div>
      </VContainer>
    </VFooter>
  </VApp>
</template>

<style scoped>
/* ========== GLOBAL SNEAT-LIKE STYLES ========== */
.hover-card {
  transition: all 0.3s ease;
}
.hover-card:hover {
  transform: translateY(-5px);
  border-color: rgb(var(--v-theme-primary)) !important;
  box-shadow: 0 10px 20px -5px rgba(var(--v-theme-primary), 0.15);
}
.hover-primary {
  transition: color 0.2s ease;
}
.hover-primary:hover {
  color: rgb(var(--v-theme-primary)) !important;
}
.text-inherit {
  color: inherit;
}
html { scroll-behavior: smooth; }

/* ========== CUSTOM NAVBAR STYLES ========== */
.landing-navbar {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 1000;
  background-color: transparent;
  transition: all 0.3s ease-in-out;
  padding: 1rem 0;
  border-bottom: 1px solid transparent;
}

.landing-navbar.navbar-scrolled {
  background-color: rgba(var(--v-theme-surface), 0.95);
  backdrop-filter: blur(10px);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
  padding: 0.75rem 0;
  border-bottom: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.navbar-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.navbar-brand {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  text-decoration: none;
}

.brand-icon {
  width: 36px;
  height: 36px;
  background-color: #696cff; /* Sneat Primary */
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.25rem;
}

.brand-text {
  font-weight: 700;
  color: rgb(var(--v-theme-on-background)); /* Dynamic Theme Text */
}

/* Desktop Menu */
.desktop-menu {
  display: none;
  align-items: center;
  gap: 1.5rem;
}

@media (min-width: 992px) {
  .desktop-menu {
    display: flex;
  }
}

.nav-link {
  color: rgba(var(--v-theme-on-background), 0.75);
  font-weight: 500;
  text-decoration: none;
  transition: color 0.2s ease;
}

.nav-link:hover {
  color: #696cff;
}

.divider {
  width: 1px;
  height: 24px;
  background-color: #d9dee3;
  margin: 0 0.5rem;
}

.btn-login {
  color: #696cff;
  border: 1px solid #696cff;
  background-color: transparent;
  padding: 0.5rem 1.25rem;
  border-radius: 6px;
  font-weight: 500;
  text-decoration: none;
  transition: all 0.2s ease;
}

.btn-login:hover {
  background-color: rgba(105, 108, 255, 0.1);
}

.btn-register {
  color: white;
  background-color: #696cff;
  border: 1px solid #696cff;
  padding: 0.5rem 1.25rem;
  border-radius: 6px;
  font-weight: 500;
  text-decoration: none;
  transition: all 0.2s ease;
  box-shadow: 0 2px 4px rgba(105, 108, 255, 0.2);
}

.btn-register:hover {
  background-color: #5f61e6;
  border-color: #5f61e6;
  box-shadow: 0 4px 8px rgba(105, 108, 255, 0.3);
}

/* Mobile Toggle */
.mobile-toggle {
  display: block;
  background: none;
  border: none;
  color: rgb(var(--v-theme-on-background)); /* Theme text color */
  font-size: 1.75rem;
  cursor: pointer;
  padding: 0.25rem;
}

@media (min-width: 992px) {
  .mobile-toggle {
    display: none;
  }
}

/* Mobile Menu Overlay */
.mobile-menu {
  position: fixed;
  top: 70px; /* Below navbar */
  left: 0;
  width: 100%;
  background-color: var(--v-theme-surface);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
  border-bottom: 1px solid rgba(var(--v-theme-on-surface), 0.08);
  z-index: 999;
  padding: 1rem 1.5rem 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  
  /* Initial state: hidden and translated up */
  opacity: 0;
  visibility: hidden;
  transform: translateY(-10px);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.mobile-menu.is-open {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.mobile-nav {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.mobile-link {
  color: rgb(var(--v-theme-on-surface));
  font-weight: 500;
  text-decoration: none;
  padding: 0.75rem 1rem;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.mobile-link:hover, .mobile-link:active {
  background-color: rgba(105, 108, 255, 0.08);
  color: #696cff;
}

.mobile-actions {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  padding-top: 1rem;
  border-top: 1px solid #eceef1;
}

.btn-login-mobile {
  text-align: center;
  color: #696cff;
  border: 1px solid #696cff;
  background-color: transparent;
  padding: 0.625rem 1.25rem;
  border-radius: 6px;
  font-weight: 500;
  text-decoration: none;
}

.btn-register-mobile {
  text-align: center;
  color: white;
  background-color: #696cff;
  border: 1px solid #696cff;
  padding: 0.625rem 1.25rem;
  border-radius: 6px;
  font-weight: 500;
  text-decoration: none;
  box-shadow: 0 2px 4px rgba(105, 108, 255, 0.2);
}
</style>
