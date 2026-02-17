<template>
  <div class="modern-landing-page font-sans text-gray-800 antialiased">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm" style="z-index: 1030;">
      <div class="container-xl">
        <a class="navbar-brand fw-bold text-dark fs-3" href="#">
          <i class='bx bx-wifi text-primary me-2'></i>{{ ispName || 'ISP Provider' }}
        </a>
        
        <!-- Mobile Toggle -->
        <button 
          class="navbar-toggler" 
          type="button" 
          @click="mobileMenuOpen = !mobileMenuOpen"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Desktop Menu -->
        <div class="d-none d-lg-flex ms-auto align-items-center">
          <ul class="navbar-nav flex-row align-items-center mb-0">
            <li class="nav-item"><a class="nav-link px-3 fw-medium text-secondary" href="#home">Beranda</a></li>
            <li class="nav-item"><a class="nav-link px-3 fw-medium text-secondary" href="#features">Keunggulan</a></li>
            <li class="nav-item"><a class="nav-link px-3 fw-medium text-secondary" href="#pricing">Paket & Harga</a></li>
            <li class="nav-item"><a class="nav-link px-3 fw-medium text-secondary" href="#contact">Kontak</a></li>
          </ul>
          <div class="d-flex ms-3" style="padding-top: 12px;">
            <router-link to="/login" class="btn btn-outline-dark px-4 me-2">Masuk</router-link>
            <router-link to="/register" class="btn btn-dark px-4">Daftar</router-link>
          </div>
        </div>
      </div>

      <!-- Mobile Menu -->
      <div class="container-xl px-4" v-show="mobileMenuOpen">
        <div class="mobile-menu d-lg-none py-3 px-3 border-top">
          <ul class="navbar-nav">
            <li class="nav-item mb-2"><a class="nav-link fw-medium text-secondary" href="#home" @click="mobileMenuOpen = false">Beranda</a></li>
            <li class="nav-item mb-2"><a class="nav-link fw-medium text-secondary" href="#features" @click="mobileMenuOpen = false">Keunggulan</a></li>
            <li class="nav-item mb-2"><a class="nav-link fw-medium text-secondary" href="#pricing" @click="mobileMenuOpen = false">Paket & Harga</a></li>
            <li class="nav-item mb-3"><a class="nav-link fw-medium text-secondary" href="#contact" @click="mobileMenuOpen = false">Kontak</a></li>
          </ul>
          <div class="d-flex gap-2 mt-2">
            <router-link to="/login" class="btn btn-outline-dark flex-fill" @click="mobileMenuOpen = false">Masuk</router-link>
            <router-link to="/register" class="btn btn-dark flex-fill" @click="mobileMenuOpen = false">Daftar</router-link>
          </div>
        </div>
      </div>
    </nav>


    <!-- Hero Section -->
    <header id="home" class="py-5 bg-light border-bottom" style="margin-top: 80px;">
      <div class="container-xl py-5">
        <div class="row align-items-center g-5">
          <div class="col-lg-6">
            <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill mb-3 fw-bold">Internet Super Cepat</span>
            <h1 class="display-3 fw-bolder mb-4 text-dark lh-sm">
              Koneksi Tanpa Batas<br>Untuk Masa Depan
            </h1>
            <p class="lead text-secondary mb-5">
              Nikmati pengalaman internet fiber optik yang stabil dan cepat. Cocok untuk bekerja, belajar, dan hiburan tanpa hambatan.
            </p>
            <div class="d-flex flex-column flex-sm-row gap-3">
              <router-link to="/register" class="btn btn-primary btn-lg px-5 py-3 fw-bold shadow-sm hero-btn">Pasang Sekarang</router-link>
              <a href="#pricing" class="btn btn-white btn-lg px-5 py-3 fw-bold border shadow-sm hero-btn">Lihat Paket</a>
            </div>
            <div class="row mt-5 pt-3 border-top">
              <div class="col-auto me-4">
                <div class="d-flex align-items-center">
                  <i class='bx bxs-user-check fs-2 text-primary me-2'></i>
                  <div>
                    <h5 class="fw-bold mb-0">Reliabel</h5>
                    <small class="text-muted">99.9% Uptime</small>
                  </div>
                </div>
              </div>
              <div class="col-auto">
                <div class="d-flex align-items-center">
                  <i class='bx bxs-check-shield fs-2 text-primary me-2'></i>
                  <div>
                    <h5 class="fw-bold mb-0">Aman</h5>
                    <small class="text-muted">Proteksi DDOS</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6 text-center">
              <!-- Placeholder for professional image -->
            <div class="position-relative">
                <div class="bg-primary position-absolute rounded-circle opacity-10 blur-3xl" style="width: 300px; height: 300px; top: 10%; right: 10%; filter: blur(60px); z-index: 0;"></div>
                <img src="https://demos.creative-tim.com/soft-ui-design-system/assets/img/illustrations/rocket-white.png" alt="Hero" class="img-fluid position-relative z-index-1" style="max-height: 500px;">
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Features Section -->
    <section id="features" class="py-5 bg-white">
      <div class="container-xl py-5">
        <div class="text-center mb-5 mw-800 mx-auto">
          <h2 class="fw-bold mb-3">Mengapa Memilih Kami?</h2>
          <p class="text-muted fs-5">Kami memberikan standar baru dalam layanan internet broadband yang fokus pada kualitas dan kepuasan pelanggan.</p>
        </div>
        
        <div class="row g-4">
          <div class="col-md-4">
            <div class="d-flex align-items-start p-4 rounded-3 hover-shadow transition-all">
              <div class="icon-square bg-primary text-white rounded-3 me-3 fs-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 50px; height: 50px;">
                <i class='bx bx-rocket'></i>
              </div>
              <div>
                <h4 class="fw-bold mb-2">High Speed Fiber</h4>
                <p class="text-secondary mb-0">Teknologi fiber optik terbaru menjamin kecepatan transfer data maksimal hingga 1Gbps.</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="d-flex align-items-start p-4 rounded-3 hover-shadow transition-all">
              <div class="icon-square bg-primary text-white rounded-3 me-3 fs-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 50px; height: 50px;">
                <i class='bx bx-infinite'></i>
              </div>
              <div>
                <h4 class="fw-bold mb-2">Unlimited Quota</h4>
                <p class="text-secondary mb-0">Bebas akses streaming, gaming, dan download sepuasnya tanpa batasan kuota (FUP).</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="d-flex align-items-start p-4 rounded-3 hover-shadow transition-all">
              <div class="icon-square bg-primary text-white rounded-3 me-3 fs-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 50px; height: 50px;">
                <i class='bx bx-support'></i>
              </div>
              <div>
                <h4 class="fw-bold mb-2">Premium Support</h4>
                <p class="text-secondary mb-0">Layanan pelanggan prioritas yang siap membantu Anda 24/7 melalui WhatsApp dan Telepon.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-5 bg-light">
      <div class="container-xl py-5">
        <div class="text-center mb-5">
          <h2 class="fw-bold">Paket Internet</h2>
          <p class="text-muted fs-5">Transparansi harga tanpa biaya tersembunyi.</p>
        </div>

        <div class="row align-items-center g-4">
            <!-- Basic Plan -->
           <div class="col-lg-4">
             <div class="card border-0 shadow-sm h-100 p-3">
               <div class="card-body">
                 <h5 class="fw-bold text-secondary mb-3">Starter</h5>
                 <div class="d-flex align-items-baseline mb-4">
                   <h2 class="fw-bold mb-0 text-dark">Rp 150.000</h2>
                   <span class="text-muted ms-2">/bulan</span>
                 </div>
                 <ul class="list-unstyled mb-5 text-secondary">
                   <li class="mb-3 d-flex"><i class='bx bx-check-circle text-primary me-2 mt-1'></i> 10 Mbps Speed</li>
                   <li class="mb-3 d-flex"><i class='bx bx-check-circle text-primary me-2 mt-1'></i> Unlimited Quota</li>
                   <li class="mb-3 d-flex"><i class='bx bx-check-circle text-primary me-2 mt-1'></i> Basic Support</li>
                 </ul>
                 <router-link to="/register" class="btn btn-outline-dark w-100 py-2 fw-medium">Pilih Paket</router-link>
               </div>
             </div>
           </div>

           <!-- Pro Plan (Feautured) -->
           <div class="col-lg-4">
             <div class="card border-primary shadow h-100 p-0 overflow-hidden transform-scale-sm">
               <div class="bg-primary text-center py-2 text-white fw-bold small text-uppercase spacing-wide">Paling Laris</div>
               <div class="card-body p-4">
                 <h5 class="fw-bold text-dark mb-3">Family</h5>
                 <div class="d-flex align-items-baseline mb-4">
                   <h2 class="fw-bold mb-0 text-primary">Rp 250.000</h2>
                   <span class="text-muted ms-2">/bulan</span>
                 </div>
                 <ul class="list-unstyled mb-5 text-secondary">
                   <li class="mb-3 d-flex"><i class='bx bxs-check-circle text-primary me-2 mt-1'></i> <strong>30 Mbps</strong> Speed</li>
                   <li class="mb-3 d-flex"><i class='bx bxs-check-circle text-primary me-2 mt-1'></i> Unlimited Quota</li>
                   <li class="mb-3 d-flex"><i class='bx bxs-check-circle text-primary me-2 mt-1'></i> Prioritas Support</li>
                   <li class="mb-3 d-flex"><i class='bx bxs-check-circle text-primary me-2 mt-1'></i> Free Instalasi</li>
                 </ul>
                 <router-link to="/register" class="btn btn-primary w-100 py-3 fw-bold">Pilih Paket</router-link>
               </div>
             </div>
           </div>

           <!-- Business Plan -->
           <div class="col-lg-4">
             <div class="card border-0 shadow-sm h-100 p-3">
               <div class="card-body">
                 <h5 class="fw-bold text-secondary mb-3">Gamer / Bisnis</h5>
                 <div class="d-flex align-items-baseline mb-4">
                   <h2 class="fw-bold mb-0 text-dark">Rp 500.000</h2>
                   <span class="text-muted ms-2">/bulan</span>
                 </div>
                 <ul class="list-unstyled mb-5 text-secondary">
                   <li class="mb-3 d-flex"><i class='bx bx-check-circle text-primary me-2 mt-1'></i> 100 Mbps Speed</li>
                   <li class="mb-3 d-flex"><i class='bx bx-check-circle text-primary me-2 mt-1'></i> Unlimited Quota</li>
                   <li class="mb-3 d-flex"><i class='bx bx-check-circle text-primary me-2 mt-1'></i> Dedicated IP Public</li>
                 </ul>
                 <router-link to="/register" class="btn btn-outline-dark w-100 py-2 fw-medium">Pilih Paket</router-link>
               </div>
             </div>
           </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-dark text-white py-5">
      <div class="container-xl">
        <div class="row gy-4">
          <div class="col-lg-4">
            <h4 class="fw-bold mb-3">{{ ispName }}</h4>
            <p class="text-white-50">Menghubungkan Anda dengan dunia melalui koneksi internet berkualitas tinggi dan terpercaya.</p>
          </div>
          <div class="col-6 col-lg-2 offset-lg-2">
            <h6 class="fw-bold mb-3 text-white">Produk</h6>
            <ul class="list-unstyled text-white-50">
              <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Internet Rumah</a></li>
              <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Bisnis</a></li>
            </ul>
          </div>
          <div class="col-6 col-lg-2">
            <h6 class="fw-bold mb-3 text-white">Bantuan</h6>
            <ul class="list-unstyled text-white-50">
              <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Cek Cover area</a></li>
              <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">FAQ</a></li>
            </ul>
          </div>
          <div class="col-lg-2">
            <h6 class="fw-bold mb-3 text-white">Sosial</h6>
            <div class="d-flex gap-3">
              <a href="#" class="text-white"><i class='bx bxl-facebook fs-4'></i></a>
              <a href="#" class="text-white"><i class='bx bxl-instagram fs-4'></i></a>
              <a href="#" class="text-white"><i class='bx bxl-twitter fs-4'></i></a>
            </div>
          </div>
        </div>
        <div class="border-top border-secondary mt-5 pt-4 text-center text-white-50">
          <small>&copy; {{ new Date().getFullYear() }} {{ ispName }}. Hak Cipta Dilindungi.</small>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue';

defineProps({
    ispName: String
});

const mobileMenuOpen = ref(false);
</script>

<style scoped>
.modern-landing-page {
  font-family: 'Inter', sans-serif;
  color: #344767;
}
.bg-soft-primary {
  background-color: rgba(59, 130, 246, 0.1);
}
.btn-primary {
  background-color: #3b82f6;
  border-color: #3b82f6;
}
.btn-primary:hover {
  background-color: #2563eb;
  border-color: #2563eb;
}
.text-primary {
  color: #3b82f6 !important;
}
.hover-shadow:hover {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  transform: translateY(-2px);
}
.transition-all {
  transition: all 0.3s ease;
}
.transform-scale-sm:hover {
    transform: scale(1.02);
    transition: transform 0.3s;
}
.fs-7 {
    font-size: 0.875rem;
}

/* Mobile menu styling */
.mobile-menu {
    animation: slideDown 0.3s ease;
    background: #fff;
    border-radius: 0.5rem;
    margin-top: 0.5rem;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.navbar-toggler:focus {
    box-shadow: none;
}

/* Responsive hero buttons */
.hero-btn {
    font-size: 1.125rem !important;
    padding: 0.75rem 2rem !important;
}

@media (max-width: 576px) {
    .hero-btn {
        font-size: 0.95rem !important;
        padding: 0.6rem 1.5rem !important;
    }
}

@media (max-width: 400px) {
    .hero-btn {
        font-size: 0.875rem !important;
        padding: 0.5rem 1.2rem !important;
    }
}
</style>
