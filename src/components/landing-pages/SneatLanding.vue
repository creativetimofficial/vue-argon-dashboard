<template>
  <div class="isp-landing-page">
    <!-- Navbar -->
    <nav class="layout-navbar navbar navbar-expand-xl navbar-light bg-white shadow-sm fixed-top" style="z-index: 1030;">
      <div class="container-xxl">
      <div class="navbar-nav-right d-flex align-items-center justify-content-between w-100" id="navbar-collapse">
        <div class="navbar-brand app-brand demo d-flex py-0 me-4">
          <span class="app-brand-logo demo">
             <!-- Placeholder Logo Icon -->
            <svg width="25" viewBox="0 0 25 42" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
              <defs>
                <path d="M13.7918663,0.358365126 L3.39788168,7.44174259 C0.566865006,9.69408886 -0.379795268,12.4788597 0.557900856,15.7960551 C0.68998853,16.2305145 1.09562888,17.7872135 3.12357076,19.2293357 C3.8146334,19.7207684 5.32369333,20.3834223 7.65075054,21.2172976 L7.59773219,21.2525164 L2.63468769,24.5493413 C0.445452254,26.3002124 0.0884951797,28.5083815 1.56381646,31.1738486 C2.83770406,32.8170431 5.20850219,33.2640127 7.09180128,32.5391577 C8.347334,32.0559211 11.4559176,30.0011079 16.4175519,26.3747182 C18.0338572,24.4997857 18.6973423,22.4544883 18.4080071,20.2388261 C17.963753,17.5346866 16.1776345,15.5799961 13.0496516,14.3747546 L10.9194936,13.4715819 L18.6192054,7.984237 L13.7918663,0.358365126 Z" id="path-1"></path>
                <path d="M5.47320593,6.00457225 C4.05321814,8.216144 4.36334763,10.0722806 6.40359441,11.5729822 C8.61520715,12.571656 10.0999176,13.2171421 10.8577257,13.5094407 L15.5088241,14.433041 L18.6192054,7.984237 C15.5364148,3.11535317 13.9273018,0.573395879 13.7918663,0.358365126 C13.5790555,0.511491653 10.8061687,2.3935607 5.47320593,6.00457225 Z" id="path-3"></path>
                <path d="M7.50063644,21.2294429 L12.3234468,23.3159332 C14.1688022,24.7579751 14.397098,26.4880487 13.008334,28.506154 C11.6195701,30.5242593 10.3099883,31.790241 9.07958868,32.3040991 C5.78142938,33.4346997 4.13234973,34 4.13234973,34 C4.13234973,34 2.75489982,33.0538207 2.37032616e-14,31.1614621 C-0.55822714,27.8186216 -0.55822714,26.0572515 -4.05231404e-15,25.8773518 C0.83734071,25.6075023 2.77988457,22.8248993 3.3049379,22.52991 C3.65497346,22.3332504 5.05353963,21.8997614 7.50063644,21.2294429 Z" id="path-4"></path>
                <path d="M20.6,7.13333333 L25.6,13.8 C26.2627417,14.6836556 26.0836556,15.9372583 25.2,16.6 C24.8538077,16.8596443 24.4327404,17 24,17 L14,17 C12.8954305,17 12,16.1045695 12,15 C12,14.5672596 12.1403557,14.1461923 12.4,13.8 L17.4,7.13333333 C18.0627417,6.24967773 19.3163444,6.07059163 20.2,6.73333333 Z" id="path-5"></path>
              </defs>
              <g id="g-app-brand" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <g id="Brand-Logo" transform="translate(-27.000000, -15.000000)">
                  <g id="Icon" transform="translate(27.000000, 15.000000)">
                    <g id="Mask" transform="translate(0.000000, 8.000000)">
                      <mask id="mask-2" fill="white">
                        <use xlink:href="#path-1"></use>
                      </mask>
                      <use fill="#696cff" xlink:href="#path-1"></use>
                      <g id="Path-3" mask="url(#mask-2)">
                        <use fill="#696cff" xlink:href="#path-3"></use>
                        <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-3"></use>
                      </g>
                      <g id="Path-4" mask="url(#mask-2)">
                        <use fill="#696cff" xlink:href="#path-4"></use>
                        <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-4"></use>
                      </g>
                    </g>
                    <g id="Triangle" transform="translate(19.000000, 11.000000) rotate(-300.000000) translate(-19.000000, -11.000000) ">
                      <use fill="#696cff" xlink:href="#path-5"></use>
                      <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-5"></use>
                    </g>
                  </g>
                </g>
              </g>
            </svg>
          </span>
          <span class="app-brand-text demo menu-text fw-bolder ms-2 text-capitalize" v-if="ispName">{{ ispName }}</span>
          <span class="app-brand-text demo menu-text fw-bolder ms-2" v-else>ISP Undefined</span>
        </div>

        <!-- Mobile Menu Toggle -->
        <button 
          class="navbar-toggler d-xl-none ms-auto" 
          type="button" 
          @click="mobileMenuOpen = !mobileMenuOpen"
          aria-label="Toggle navigation"
        >
          <i class="bx" :class="mobileMenuOpen ? 'bx-x' : 'bx-menu'"></i>
        </button>

        <!-- Desktop Navigation -->
        <div class="d-none d-xl-flex align-items-center ms-auto">
          <ul class="navbar-nav flex-row align-items-center mb-0">
            <li class="nav-item me-3">
              <a href="#features" class="nav-link text-body fw-semibold">Fitur</a>
            </li>
            <li class="nav-item me-3">
              <a href="#pricing" class="nav-link text-body fw-semibold">Paket</a>
            </li>
            <li class="nav-item me-3">
              <a href="#contact" class="nav-link text-body fw-semibold">Kontak</a>
            </li>
          </ul>
          <div class="d-flex ms-2 align-items-center" style="padding-top: 12px;">
            <router-link to="/login" class="btn btn-outline-primary me-2">Masuk</router-link>
            <router-link to="/register" class="btn btn-primary">Daftar</router-link>
          </div>
        </div>
      </div>

      <!-- Mobile Menu Dropdown -->
      <div class="container-xxl px-4" v-show="mobileMenuOpen">
        <div class="mobile-menu d-xl-none py-3 px-3 border-top">
          <ul class="navbar-nav">
            <li class="nav-item mb-2">
              <a href="#features" class="nav-link text-body fw-semibold" @click="mobileMenuOpen = false">Fitur</a>
            </li>
            <li class="nav-item mb-2">
              <a href="#pricing" class="nav-link text-body fw-semibold" @click="mobileMenuOpen = false">Paket</a>
            </li>
            <li class="nav-item mb-3">
              <a href="#contact" class="nav-link text-body fw-semibold" @click="mobileMenuOpen = false">Kontak</a>
            </li>
          </ul>
          <div class="d-flex gap-2 mt-2">
            <router-link to="/login" class="btn btn-outline-primary flex-fill" @click="mobileMenuOpen = false">Masuk</router-link>
            <router-link to="/register" class="btn btn-primary flex-fill" @click="mobileMenuOpen = false">Daftar</router-link>
          </div>
        </div>
      </div>
      </div>
    </nav>


    <!-- Hero Section -->
    <div class="container-xxl flex-grow-1 container-p-y" style="margin-top: 80px;">
      <div class="row align-items-center py-5">
        <div class="col-lg-6 mb-4 order-0 text-center text-lg-start">
            <h1 class="display-4 fw-bold text-primary mb-3">Internet Cepat,<br>Koneksi Tepat.</h1>
            <p class="fs-5 text-muted mb-4">
                {{ ispName }} menghadirkan layanan internet broadband fiber optik dengan kecepatan tinggi dan stabil untuk rumah dan bisnismu.
            </p>
            <div class="d-flex flex-column flex-sm-row justify-content-center justify-content-lg-start gap-3">
                <router-link to="/register" class="btn btn-lg btn-primary shadow-sm hero-btn">Mulai Berlangganan</router-link>
                <a href="#pricing" class="btn btn-lg btn-outline-secondary hero-btn">Lihat Paket</a>
            </div>
             <div class="row mt-5">
                <div class="col-4">
                    <h3 class="mb-0 fw-bold">100+</h3>
                    <small class="text-muted">Pelanggan</small>
                </div>
                <div class="col-4">
                     <h3 class="mb-0 fw-bold">99%</h3>
                    <small class="text-muted">Uptime</small>
                </div>
                <div class="col-4">
                     <h3 class="mb-0 fw-bold">24/7</h3>
                     <small class="text-muted">Support</small>
                </div>
            </div>
        </div>
        <div class="col-lg-6 text-center">
             <img src="https://images.unsplash.com/photo-1551434678-e076c223a692?w=600&h=450&fit=crop" alt="Hero Image" class="img-fluid rounded-3 shadow-sm" style="max-height: 450px; object-fit: cover;">
        </div>
      </div>

      <!-- Features Section -->
      <div id="features" class="py-5">
        <div class="text-center mb-5">
            <span class="badge bg-label-primary rounded-pill mb-2">Kenapa Memilih Kami?</span>
            <h3 class="fw-bold">Layanan Internet Terbaik Untukmu</h3>
            <p class="text-muted">Kami menjamin pengalaman berselancar yang lancar tanpa hambatan.</p>
        </div>
        
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm p-3">
                    <div class="card-body">
                         <div class="avatar avatar-md bg-label-primary rounded mb-3">
                             <i class='bx bx-rocket fs-3 m-2'></i>
                         </div>
                         <h5 class="fw-bold">Kecepatan Tinggi</h5>
                         <p class="text-muted">Akses internet up to 1Gbps dengan jaringan Fiber Optik terbaru.</p>
                    </div>
                </div>
            </div>
             <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm p-3">
                    <div class="card-body">
                         <div class="avatar avatar-md bg-label-success rounded mb-3">
                             <i class='bx bx-money fs-3 m-2'></i>
                         </div>
                         <h5 class="fw-bold">Harga Terjangkau</h5>
                         <p class="text-muted">Paket internet hemat mulai dari 100ribuan tanpa biaya tersembunyi.</p>
                    </div>
                </div>
            </div>
             <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm p-3">
                    <div class="card-body">
                         <div class="avatar avatar-md bg-label-info rounded mb-3">
                             <i class='bx bx-support fs-3 m-2'></i>
                         </div>
                         <h5 class="fw-bold">Support 24/7</h5>
                         <p class="text-muted">Tim teknis kami siap membantu kendala jaringan Anda kapan saja.</p>
                    </div>
                </div>
            </div>
             <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm p-3">
                    <div class="card-body">
                         <div class="avatar avatar-md bg-label-warning rounded mb-3">
                             <i class='bx bx-wifi fs-3 m-2'></i>
                         </div>
                         <h5 class="fw-bold">Stabil & Unlimited</h5>
                         <p class="text-muted">Koneksi stabil tanpa batasan kuota (FUP) untuk aktivitas digitalmu.</p>
                    </div>
                </div>
            </div>
             <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm p-3">
                    <div class="card-body">
                         <div class="avatar avatar-md bg-label-danger rounded mb-3">
                             <i class='bx bx-shield-quarter fs-3 m-2'></i>
                         </div>
                         <h5 class="fw-bold">Aman & Privat</h5>
                         <p class="text-muted">Jaringan yang aman dengan proteksi firewall untuk melindungi data Anda.</p>
                    </div>
                </div>
            </div>
             <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm p-3">
                    <div class="card-body">
                         <div class="avatar avatar-md bg-label-secondary rounded mb-3">
                             <i class='bx bx-game typcn-anchor fs-3 m-2'></i>
                         </div>
                         <h5 class="fw-bold">Low Latency</h5>
                         <p class="text-muted">Ping rendah yang sangat cocok untuk gaming dan streaming 4K.</p>
                    </div>
                </div>
            </div>
        </div>
      </div>

      <!-- Pricing Section (Mocked) -->
      <div id="pricing" class="py-5 bg-white rounded-3 px-4">
         <div class="text-center mb-5">
            <span class="badge bg-label-primary rounded-pill mb-2">Pilihan Paket</span>
            <h3 class="fw-bold">Pilih Paket Sesuai Kebutuhan</h3>
            <p class="text-muted">Berlangganan sekarang dan nikmati promo menariknya.</p>
        </div>
        <div class="row justify-content-center">
            <!-- Paket 1 -->
            <div class="col-md-4 mb-4">
                <div class="card border shadow-none h-100">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h5 class="fw-bold">Rumahan</h5>
                             <h2 class="fw-bold text-primary mb-0">Rp 150.000<small class="text-muted fs-6">/bulan</small></h2>
                        </div>
                        <ul class="list-unstyled mb-4">
                             <li class="mb-2"><i class="bx bx-check text-primary me-2"></i> Kecepatan up to 10 Mbps</li>
                             <li class="mb-2"><i class="bx bx-check text-primary me-2"></i> Kuota Unlimited</li>
                             <li class="mb-2"><i class="bx bx-check text-primary me-2"></i> Ideal untuk 2-3 Perangkat</li>
                             <li class="mb-2"><i class="bx bx-x text-muted me-2"></i> IP Public Static</li>
                        </ul>
                         <div class="d-grid">
                            <router-link to="/register" class="btn btn-outline-primary">Pilih Paket</router-link>
                        </div>
                    </div>
                </div>
            </div>
             <!-- Paket 2 -->
             <div class="col-md-4 mb-4">
                <div class="card border border-primary shadow-sm h-100 relative">
                     <div class="badge bg-primary position-absolute top-0 start-50 translate-middle px-3 rounded-pill">Populer</div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h5 class="fw-bold">Keluarga</h5>
                             <h2 class="fw-bold text-primary mb-0">Rp 250.000<small class="text-muted fs-6">/bulan</small></h2>
                        </div>
                        <ul class="list-unstyled mb-4">
                             <li class="mb-2"><i class="bx bx-check text-primary me-2"></i> Kecepatan up to 30 Mbps</li>
                             <li class="mb-2"><i class="bx bx-check text-primary me-2"></i> Kuota Unlimited</li>
                             <li class="mb-2"><i class="bx bx-check text-primary me-2"></i> Ideal untuk 4-6 Perangkat</li>
                             <li class="mb-2"><i class="bx bx-x text-muted me-2"></i> IP Public Static</li>
                        </ul>
                         <div class="d-grid">
                            <router-link to="/register" class="btn btn-primary">Pilih Paket</router-link>
                        </div>
                    </div>
                </div>
            </div>
             <!-- Paket 3 -->
             <div class="col-md-4 mb-4">
                <div class="card border shadow-none h-100">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h5 class="fw-bold">Gamer & Bisnis</h5>
                             <h2 class="fw-bold text-primary mb-0">Rp 500.000<small class="text-muted fs-6">/bulan</small></h2>
                        </div>
                        <ul class="list-unstyled mb-4">
                             <li class="mb-2"><i class="bx bx-check text-primary me-2"></i> Kecepatan up to 100 Mbps</li>
                             <li class="mb-2"><i class="bx bx-check text-primary me-2"></i> Kuota Unlimited</li>
                             <li class="mb-2"><i class="bx bx-check text-primary me-2"></i> Prioritas Trafik</li>
                             <li class="mb-2"><i class="bx bx-check text-primary me-2"></i> IP Public Static</li>
                        </ul>
                         <div class="d-grid">
                            <router-link to="/register" class="btn btn-outline-primary">Pilih Paket</router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
      
       <!-- CTA Section -->
      <section class="py-5">
         <div class="card bg-primary text-white overflow-hidden">
            <div class="card-body py-5 px-md-5 text-center text-md-start">
               <div class="row align-items-center">
                  <div class="col-md-8">
                     <h3 class="text-white fw-bold mb-2">Siap untuk koneksi lebih cepat?</h3>
                     <p class="mb-md-0 text-white opacity-75">Bergabunglah dengan ratusan pelanggan lain yang telah mempercayakan koneksi internet mereka kepada kami.</p>
                  </div>
                  <div class="col-md-4 text-center text-md-end mt-4 mt-md-0">
                     <router-link to="/register" class="btn btn-white text-primary fw-bold">Daftar Sekarang</router-link>
                  </div>
               </div>
            </div>
         </div>
      </section>

    </div>
    
     <!-- Footer -->
    <footer id="contact" class="content-footer footer bg-footer-theme border-top mt-5">
      <div class="container-xxl py-5">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5 class="fw-bold mb-3">{{ ispName }}</h5>
                <p class="text-muted">
                    Penyedia layanan internet (ISP) terpercaya dengan komitmen memberikan koneksi stabil dan layanan terbaik.
                </p>
            </div>
             <div class="col-md-2 mb-4">
                <h6 class="fw-bold mb-3">Layanan</h6>
                <ul class="list-unstyled text-muted">
                    <li class="mb-2"><a href="#" class="text-muted">Internet Rumah</a></li>
                     <li class="mb-2"><a href="#" class="text-muted">Internet Bisnis</a></li>
                      <li class="mb-2"><a href="#" class="text-muted">Dedicated Server</a></li>
                </ul>
            </div>
             <div class="col-md-2 mb-4">
                <h6 class="fw-bold mb-3">Perusahaan</h6>
                <ul class="list-unstyled text-muted">
                    <li class="mb-2"><a href="#" class="text-muted">Tentang Kami</a></li>
                     <li class="mb-2"><a href="#" class="text-muted">Karir</a></li>
                      <li class="mb-2"><a href="#" class="text-muted">Kontak</a></li>
                </ul>
            </div>
             <div class="col-md-4 mb-4">
                <h6 class="fw-bold mb-3">Hubungi Kami</h6>
                 <ul class="list-unstyled text-muted">
                    <li class="mb-2"><i class='bx bx-phone me-2'></i> 0812-3456-7890</li>
                     <li class="mb-2"><i class='bx bx-envelope me-2'></i> support@{{ ispName.toLowerCase().replace(/\s+/g, '') }}.com</li>
                      <li class="mb-2"><i class='bx bx-map me-2'></i> Jakarta, Indonesia</li>
                </ul>
            </div>
        </div>
        <hr class="my-4">
        <div class="text-center text-muted">
           © {{ new Date().getFullYear() }}, {{ ispName }}. All rights reserved.
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
/* Scoped styles to ensure Sneat look avoids conflicts with Argon if any */
.isp-landing-page {
    background-color: #f5f5f9; /* Sneat default bg */
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

/* Fixed navbar at top */
.layout-navbar {
    background: rgba(255, 255, 255, 0.98) !important;
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
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

.navbar-toggler {
    border: none;
    padding: 0.5rem;
    font-size: 1.5rem;
    background: transparent;
}

.navbar-toggler:focus {
    box-shadow: none;
}

.text-primary {
    color: #696cff !important;
}

.bg-primary {
    background-color: #696cff !important;
}
.btn-primary {
    background-color: #696cff !important;
    border-color: #696cff !important;
}
.btn-outline-primary {
     color: #696cff !important;
    border-color: #696cff !important;
}
.btn-outline-primary:hover {
    background-color: #696cff !important;
    color: #fff !important;
}
.bg-label-primary {
    background-color: #e7e7ff !important;
    color: #696cff !important;
}

/* Responsive hero buttons */
.hero-btn {
    font-size: 1.125rem !important;
    padding: 0.75rem 1.5rem !important;
}

@media (max-width: 576px) {
    .hero-btn {
        font-size: 0.95rem !important;
        padding: 0.6rem 1.2rem !important;
    }
}

@media (max-width: 400px) {
    .hero-btn {
        font-size: 0.875rem !important;
        padding: 0.5rem 1rem !important;
    }
}
</style>
