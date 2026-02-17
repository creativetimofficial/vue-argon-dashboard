<template>
  <div class="creative-landing-page text-white overflow-hidden">
    <div class="background-shapes">
      <div class="shape shape-1"></div>
      <div class="shape shape-2"></div>
      <div class="shape shape-3"></div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-transparent fixed-top py-3">
      <div class="container-xl backdrop-blur rounded-3 px-4 py-2 mt-2">
        <a class="navbar-brand fw-bold fs-3" href="#">
          <span class="text-gradient">{{ ispName || 'Creative ISP' }}</span>
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
            <li class="nav-item"><a class="nav-link px-3" href="#home">Home</a></li>
            <li class="nav-item"><a class="nav-link px-3" href="#features">Features</a></li>
            <li class="nav-item"><a class="nav-link px-3" href="#pricing">Pricing</a></li>
            <li class="nav-item"><a class="nav-link px-3" href="#contact">Contact</a></li>
          </ul>
          <div class="d-flex ms-3" style="padding-top: 12px;">
            <router-link to="/login" class="btn btn-outline-light rounded-pill px-4 me-2">Masuk</router-link>
            <router-link to="/register" class="btn btn-gradient rounded-pill px-4 shadow-lg">Daftar</router-link>
          </div>
        </div>
      </div>

      <!-- Mobile Menu -->
      <div class="container-xl px-4 mt-2" v-show="mobileMenuOpen">
        <div class="mobile-menu backdrop-blur rounded-3 d-lg-none py-3 px-3 border-top border-white-50">
          <ul class="navbar-nav">
            <li class="nav-item mb-2"><a class="nav-link" href="#home" @click="mobileMenuOpen = false">Home</a></li>
            <li class="nav-item mb-2"><a class="nav-link" href="#features" @click="mobileMenuOpen = false">Features</a></li>
            <li class="nav-item mb-2"><a class="nav-link" href="#pricing" @click="mobileMenuOpen = false">Pricing</a></li>
            <li class="nav-item mb-3"><a class="nav-link" href="#contact" @click="mobileMenuOpen = false">Contact</a></li>
          </ul>
          <div class="d-flex gap-2 mt-2">
            <router-link to="/login" class="btn btn-outline-light rounded-pill flex-fill" @click="mobileMenuOpen = false">Masuk</router-link>
            <router-link to="/register" class="btn btn-gradient rounded-pill flex-fill shadow-lg" @click="mobileMenuOpen = false">Daftar</router-link>
          </div>
        </div>
      </div>
    </nav>


    <!-- Hero Section -->
    <header id="home" class="min-vh-100 d-flex align-items-center position-relative z-index-1">
      <div class="container-xl">
        <div class="row align-items-center">
          <div class="col-lg-7">
            <h1 class="display-2 fw-black mb-4 lh-sm">
              Experience the <br>
              <span class="text-gradient">Fastest Internet</span>
            </h1>
            <p class="lead text-white-75 mb-5 mw-md-500">
              Jelajahi dunia maya tanpa batas dengan kecepatan cahaya. 
              Streaming, gaming, dan bekerja lebih lancar dari sebelumnya.
            </p>
            <div class="d-flex flex-column flex-sm-row gap-3">
              <button class="btn btn-gradient btn-lg rounded-pill px-5 py-3 shadow-glow transition-transform hover-scale hero-btn">
                Mulai Berlangganan
              </button>
            </div>
            
            <div class="row mt-5">
              <div class="col-auto">
                <h3 class="fw-bold mb-0">1Gbps</h3>
                <small class="text-white-50">Max Speed</small>
              </div>
              <div class="col-auto border-start border-white-50 ps-4 mx-4">
                <h3 class="fw-bold mb-0">0%</h3>
                <small class="text-white-50">Lag</small>
              </div>
              <div class="col-auto border-start border-white-50 ps-4">
                <h3 class="fw-bold mb-0">24h</h3>
                <small class="text-white-50">Support</small>
              </div>
            </div>
          </div>
          <div class="col-lg-5 d-none d-lg-block position-relative">
             <div class="floating-card glassmorphism p-4 rounded-4 shadow-lg" style="transform: rotate(-5deg); z-index: 2; position: absolute; top: 0; right: 0;">
                <div class="d-flex align-items-center mb-3">
                   <div class="avatar bg-success rounded-circle p-2 me-3"><i class='bx bx-wifi text-white'></i></div>
                   <div>
                      <h6 class="mb-0 fw-bold">Connected</h6>
                      <small class="text-success">Signal Excellent</small>
                   </div>
                </div>
                <div class="progress" style="height: 6px; width: 200px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 95%"></div>
                </div>
             </div>
             <img src="https://demos.creative-tim.com/soft-ui-design-system/assets/img/shapes/waves-white.svg" class="position-absolute w-100 opacity-5" style="bottom: -20%; left: -20%; z-index: 0;">
          </div>
        </div>
      </div>
    </header>

    <!-- Features Section -->
    <section id="features" class="py-5 mx-3">
        <div class="container-xl glassmorphism rounded-5 p-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Fitur Unggulan</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card p-4 rounded-4 hover-lift bg-white-10 text-center">
                        <div class="icon-box mb-3 mx-auto bg-gradient rounded-circle d-flex align-items-center justify-content-center shadow-lg" style="width: 60px; height: 60px;">
                            <i class='bx bx-rocket fs-3'></i>
                        </div>
                        <h4>Ultra Fast</h4>
                        <p class="text-white-50">Download file besar dalam hitungan detik.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card p-4 rounded-4 hover-lift bg-white-10 text-center">
                        <div class="icon-box mb-3 mx-auto bg-gradient rounded-circle d-flex align-items-center justify-content-center shadow-lg" style="width: 60px; height: 60px;">
                            <i class='bx bx-shield-quarter fs-3'></i>
                        </div>
                        <h4>Secure</h4>
                        <p class="text-white-50">Privasi Anda adalah prioritas utama kami.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card p-4 rounded-4 hover-lift bg-white-10 text-center">
                        <div class="icon-box mb-3 mx-auto bg-gradient rounded-circle d-flex align-items-center justify-content-center shadow-lg" style="width: 60px; height: 60px;">
                            <i class='bx bx-headphone fs-3'></i>
                        </div>
                        <h4>24/7 Support</h4>
                        <p class="text-white-50">Bantuan teknis kapanpun Anda butuhkan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-5">
      <div class="container-xl py-5">
        <div class="text-center mb-5">
          <h2 class="fw-bold mb-4">Pilih Paket Anda</h2>
        </div>
        <div class="row g-4 justify-content-center">
            <!-- Card 1 -->
            <div class="col-md-4">
                <div class="card bg-dark border-0 rounded-4 overflow-hidden h-100 shadow-lg hover-lift">
                    <div class="card-body p-4 position-relative z-index-1">
                        <h5 class="fw-bold text-white-50">Basic</h5>
                        <h2 class="fw-bold display-5 my-3">150k</h2>
                        <ul class="list-unstyled mb-4 text-white-75">
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i> 10 Mbps</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i> Unlimited Data</li>
                        </ul>
                        <router-link to="/register" class="btn btn-outline-light w-100 rounded-pill">Pilih</router-link>
                    </div>
                </div>
            </div>
            <!-- Card 2 -->
             <div class="col-md-4">
                <div class="card bg-gradient border-0 rounded-4 overflow-hidden h-100 shadow-glow hover-lift transform-scale">
                    <div class="card-body p-4 position-relative z-index-1">
                        <div class="badge bg-white text-dark rounded-pill mb-2">Most Popular</div>
                        <h5 class="fw-bold text-white">Pro Gamer</h5>
                        <h2 class="fw-bold display-5 my-3 text-white">250k</h2>
                        <ul class="list-unstyled mb-4 text-white">
                            <li class="mb-2"><i class='bx bxs-check-circle text-white me-2'></i> 30 Mbps</li>
                            <li class="mb-2"><i class='bx bxs-check-circle text-white me-2'></i> Low Latency</li>
                            <li class="mb-2"><i class='bx bxs-check-circle text-white me-2'></i> Priority Support</li>
                        </ul>
                        <router-link to="/register" class="btn btn-white w-100 rounded-pill text-dark fw-bold">Pilih</router-link>
                    </div>
                </div>
            </div>
            <!-- Card 3 -->
             <div class="col-md-4">
                 <div class="card bg-dark border-0 rounded-4 overflow-hidden h-100 shadow-lg hover-lift">
                    <div class="card-body p-4 position-relative z-index-1">
                        <h5 class="fw-bold text-white-50">Business</h5>
                        <h2 class="fw-bold display-5 my-3">500k</h2>
                        <ul class="list-unstyled mb-4 text-white-75">
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i> 100 Mbps</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i> Dedicated IP</li>
                        </ul>
                         <router-link to="/register" class="btn btn-outline-light w-100 rounded-pill">Pilih</router-link>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </section>

     <!-- Footer -->
    <footer id="contact" class="py-4 text-center text-white-50">
        <small>© {{ new Date().getFullYear() }} {{ ispName }}. Designed with Passion.</small>
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
.creative-landing-page {
    background-color: #0f172a;
    font-family: 'Outfit', sans-serif;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    min-height: 100vh;
}

.background-shapes .shape {
    position: fixed;
    border-radius: 50%;
    filter: blur(80px);
    z-index: 0;
}
.shape-1 {
    width: 400px;
    height: 400px;
    background: #7c3aed;
    top: -100px;
    left: -100px;
    opacity: 0.5;
    animation: move 10s infinite alternate;
}
.shape-2 {
    width: 300px;
    height: 300px;
    background: #2563eb;
    bottom: 10%;
    right: -100px;
    opacity: 0.4;
    animation: move 15s infinite alternate-reverse;
}
.shape-3 {
    width: 200px;
    height: 200px;
    background: #ec4899;
    top: 40%;
    left: 40%;
    opacity: 0.3;
    animation: pulse 8s infinite;
}

@keyframes move {
    from { transform: translate(0, 0); }
    to { transform: translate(50px, 50px); }
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.backdrop-blur {
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255,255,255,0.1);
}

.glassmorphism {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.text-gradient {
    background: linear-gradient(135deg, #60a5fa, #c084fc);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.btn-gradient {
    background: linear-gradient(135deg, #60a5fa, #7c3aed);
    border: none;
    color: white;
}

.hover-lift:hover {
    transform: translateY(-5px);
    transition: transform 0.3s ease;
}

.shadow-glow {
    box-shadow: 0 0 20px rgba(124, 58, 237, 0.5);
}

.bg-white-10 {
    background: rgba(255,255,255,0.03);
}

.z-index-1 {
    z-index: 1;
}

/* Mobile menu styling */
.mobile-menu {
    animation: slideDown 0.3s ease;
}

/* Better background for mobile menu in Creative theme */
.creative-landing-page .backdrop-blur {
    background: rgba(15, 23, 42, 0.95) !important;
    backdrop-filter: blur(20px);
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

.border-white-50 {
    border-color: rgba(255, 255, 255, 0.5) !important;
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
