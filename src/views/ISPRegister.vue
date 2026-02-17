<template>
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner">
      <!-- Register Card -->
      <div class="card px-sm-6 px-0">
        <div class="card-body">
          <!-- Logo -->
          <div class="app-brand justify-content-center mb-4">
            <span class="app-brand-text demo text-heading fw-bold text-center" style="font-size: 1.75rem;">{{ ispName || 'Register' }}</span>
          </div>
          <!-- /Logo -->

          <h4 class="mb-2 text-center">Daftar Pelanggan Baru 🚀</h4>
          <p class="mb-4 text-center text-muted">Buat akun untuk mulai berlangganan</p>

          <!-- Success Alert -->
          <div v-if="successMessage" class="alert alert-success alert-dismissible mb-4" role="alert">
            {{ successMessage }}
          </div>

          <!-- Error Alert -->
          <div v-if="errorMessage" class="alert alert-danger alert-dismissible mb-4" role="alert">
            {{ errorMessage }}
             <button type="button" class="btn-close" @click="errorMessage = ''"></button>
          </div>

          <!-- Registration Form -->
          <form id="formAuthentication" class="mb-3" @submit.prevent="handleRegister">
            
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input
                type="email"
                class="form-control"
                id="email"
                name="email"
                placeholder="Masukkan email Anda"
                v-model="email"
                required
              />
            </div>

            <div class="mb-3">
              <label for="whatsapp" class="form-label">WhatsApp</label>
              <input
                type="text"
                class="form-control"
                id="whatsapp"
                name="whatsapp"
                placeholder="08xxxxxxxxxx"
                v-model="whatsapp"
                @blur="formatWhatsApp"
                required
              />
            </div>

            <div class="mb-3 form-password-toggle">
              <label class="form-label" for="password">Password</label>
              <div class="input-group input-group-merge">
                <input
                  :type="showPassword ? 'text' : 'password'"
                  id="password"
                  class="form-control"
                  name="password"
                  placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                  v-model="password"
                  required
                />
                <span class="input-group-text cursor-pointer" @click="showPassword = !showPassword" style="z-index: 10;">
                  <i class="bx" :class="showPassword ? 'bx-show' : 'bx-hide'"></i>
                </span>
              </div>
            </div>

            <div class="mb-3 form-password-toggle">
              <label class="form-label" for="passwordConfirm">Konfirmasi Password</label>
              <div class="input-group input-group-merge">
                <input
                  :type="showPasswordConfirm ? 'text' : 'password'"
                  id="passwordConfirm"
                  class="form-control"
                  name="password_confirmation"
                  placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                  v-model="passwordConfirm"
                  required
                />
                <span class="input-group-text cursor-pointer" @click="showPasswordConfirm = !showPasswordConfirm" style="z-index: 10;">
                  <i class="bx" :class="showPasswordConfirm ? 'bx-show' : 'bx-hide'"></i>
                </span>
              </div>
            </div>

            <div class="mb-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="terms" v-model="agreeTerms" required />
                <label class="form-check-label" for="terms">
                  Saya setuju dengan <a href="javascript:void(0);">syarat dan ketentuan</a>
                </label>
              </div>
            </div>
            
            <!-- Recaptcha placeholder if needed, though hidden usually -->
            <div id="recaptcha-container"></div>

            <button class="btn btn-primary d-grid w-100" type="submit" :disabled="loading">
              <span v-if="loading">
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Loading...
              </span>
              <span v-else>Daftar Sekarang</span>
            </button>
          </form>

          <p class="text-center">
            <span>Sudah punya akun? </span>
            <router-link to="/login">
              <span>Login di sini</span>
            </router-link>
          </p>
          
           <div class="text-center mt-3">
             <router-link to="/">
                <small>Kembali ke Beranda</small>
             </router-link>
          </div>
        </div>
      </div>
      <!-- /Register Card -->
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { authAPI } from "@/services/api";
import { detectTenant, getTenantInfo } from '@/utils/tenant';

const router = useRouter();

const email = ref("");
const whatsapp = ref("");
const password = ref("");
const passwordConfirm = ref("");
const agreeTerms = ref(false);
const showPassword = ref(false);
const showPasswordConfirm = ref(false);
const loading = ref(false);
const errorMessage = ref("");
const successMessage = ref("");
const ispName = ref("");
const recaptchaToken = ref("");

// reCAPTCHA Site Key (using same as RegisterPage.vue)
const RECAPTCHA_SITE_KEY = import.meta.env.VITE_RECAPTCHA_SITE_KEY || "6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI";

onMounted(async () => {
   // Tenant detection for branding
    const tenant = detectTenant();
  if (tenant) {
      const info = getTenantInfo();
      if (info && info.name) {
          ispName.value = info.name;
      } else {
           try {
                const response = await authAPI.getTenantInfo();
                if (response.data && response.data.name) {
                    ispName.value = response.data.name;
                }
           } catch {
                ispName.value = tenant.subdomain;
           }
      }
  }
  
  // Load reCAPTCHA
  loadRecaptchaScript();
});

const loadRecaptchaScript = () => {
  if (document.getElementById('recaptcha-script')) return;
  const script = document.createElement('script');
  script.id = 'recaptcha-script';
  script.src = `https://www.google.com/recaptcha/api.js?render=${RECAPTCHA_SITE_KEY}`;
  script.async = true;
  script.defer = true;
  document.head.appendChild(script);
};

const formatWhatsApp = () => {
    let value = whatsapp.value.replace(/\D/g, '');
    if (value.startsWith('62')) {
        whatsapp.value = '+' + value;
    } else if (value.startsWith('0')) {
        whatsapp.value = value;
    }
};

const validateForm = () => {
    if (!agreeTerms.value) {
        errorMessage.value = "Anda harus menyetujui syarat dan ketentuan.";
        return false;
    }
    if (password.value !== passwordConfirm.value) {
        errorMessage.value = "Konfirmasi password tidak cocok.";
        return false;
    }
    if (password.value.length < 8) {
        errorMessage.value = "Password minimal 8 karakter.";
        return false;
    }
    return true;
};

const handleRegister = async () => {
    errorMessage.value = "";
    successMessage.value = "";
    
    if (!validateForm()) return;
    
    loading.value = true;
    
    try {
        await window.grecaptcha.ready(async () => {
             try {
                 recaptchaToken.value = await window.grecaptcha.execute(RECAPTCHA_SITE_KEY, { action: 'register' });
                 
                 const cleanWhatsapp = whatsapp.value.replace(/[\s-]/g, '');
                 
                 const response = await authAPI.register({
                     email: email.value,
                     password: password.value,
                     password_confirmation: passwordConfirm.value,
                     whatsapp: cleanWhatsapp,
                     recaptcha_token: recaptchaToken.value
                 });
                 
                 successMessage.value = "Registrasi berhasil! Silakan cek email dan login.";
                 setTimeout(() => {
                     router.push('/login');
                 }, 3000);
             } catch (error) {
                 console.error("Registration error:", error);
                 if (error.response?.data?.errors) {
                     errorMessage.value = Object.values(error.response.data.errors).flat().join(', ');
                 } else if (error.response?.data?.message) {
                     errorMessage.value = error.response.data.message;
                 } else {
                     errorMessage.value = "Registrasi gagal. Silakan coba lagi.";
                 }
             } finally {
                 loading.value = false;
             }
        });
    } catch(err) {
         errorMessage.value = "Gagal memuat reCAPTCHA.";
         loading.value = false;
    }
};
</script>

<style scoped>
.authentication-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  background-color: #f5f5f9; /* Sneat bg */
}
.cursor-pointer {
  cursor: pointer;
}
.authentication-inner {
    max-width: 450px;
    width: 100%;
}
.card {
    border: 0;
    box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.12);
    border-radius: 0.5rem;
}
.card-body {
    padding: 2rem 2.5rem;
}
@media (max-width: 576px) {
  .card-body {
    padding: 1.5rem;
  }
}
.form-check-input:checked {
  background-color: #696cff !important;
  border-color: #696cff !important;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M6 10l3 3l6-6'/%3e%3c/svg%3e") !important;
  background-repeat: no-repeat !important;
  background-position: center center !important;
  background-size: 1.1em !important;
  appearance: none !important;
}
.form-check-input:checked::after,
.form-check-input:checked::before {
  content: "" !important;
  display: none !important;
  background: none !important;
}
.form-check-input:focus {
  border-color: #696cff;
  box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.25);
}
.form-check-input {
  width: 1.2em;
  height: 1.2em;
  margin-top: 0.15em;
  appearance: none !important;
}
</style>
