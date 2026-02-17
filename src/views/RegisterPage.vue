<script setup>
import { ref, onBeforeMount, onBeforeUnmount, onMounted } from "vue";
import { useStore } from "vuex";
import { useRouter } from "vue-router";
import ArgonInput from "@/components/ArgonInput.vue";
import ArgonButton from "@/components/ArgonButton.vue";
import ArgonCheckbox from "@/components/ArgonCheckbox.vue";
import { authAPI } from "@/services/api";
import Swal from "sweetalert2";

const body = document.getElementsByTagName("body")[0];
const store = useStore();
const router = useRouter();

const loading = ref(false);
const errorMessage = ref("");
const successMessage = ref("");

// Registration fields
const email = ref("");
const password = ref("");
const passwordConfirm = ref("");
const whatsapp = ref("");
const agreeTerms = ref(false);
const recaptchaToken = ref("");

// reCAPTCHA Site Key - GANTI DENGAN KEY ANDA
const RECAPTCHA_SITE_KEY = import.meta.env.VITE_RECAPTCHA_SITE_KEY || "6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"; // Test key

onBeforeMount(() => {
  store.state.hideConfigButton = true;
  store.state.showNavbar = false;
  store.state.showSidenav = false;
  store.state.showFooter = false;
  body.classList.remove("bg-gray-100");
});

onBeforeUnmount(() => {
  store.state.hideConfigButton = false;
  store.state.showNavbar = true;
  store.state.showSidenav = true;
  store.state.showFooter = true;
  body.classList.add("bg-gray-100");
});

onMounted(() => {
  // Load reCAPTCHA script
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

const validateForm = () => {
  if (!email.value || !password.value || !passwordConfirm.value || !whatsapp.value) {
    Swal.fire({
      icon: 'warning',
      title: 'Form Tidak Lengkap',
      text: 'Semua field harus diisi',
      confirmButtonColor: '#5e72e4'
    });
    return false;
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email.value)) {
    Swal.fire({
      icon: 'warning',
      title: 'Email Tidak Valid',
      text: 'Format email tidak valid. Contoh: nama@email.com',
      confirmButtonColor: '#5e72e4'
    });
    return false;
  }

  // Validate WhatsApp format
  const whatsappRegex = /^(\+62|62|0)[0-9]{9,12}$/;
  if (!whatsappRegex.test(whatsapp.value.replace(/[\s-]/g, ''))) {
    Swal.fire({
      icon: 'warning',
      title: 'WhatsApp Tidak Valid',
      text: 'Format nomor WhatsApp tidak valid. Contoh: 08123456789 atau +628123456789',
      confirmButtonColor: '#5e72e4'
    });
    return false;
  }

  if (password.value.length < 8) {
    Swal.fire({
      icon: 'warning',
      title: 'Password Terlalu Pendek',
      text: 'Password minimal 8 karakter',
      confirmButtonColor: '#5e72e4'
    });
    return false;
  }

  if (password.value !== passwordConfirm.value) {
    Swal.fire({
      icon: 'error',
      title: 'Password Tidak Cocok',
      text: 'Konfirmasi password tidak cocok dengan password yang Anda masukkan',
      confirmButtonColor: '#5e72e4'
    });
    return false;
  }

  if (!agreeTerms.value) {
    Swal.fire({
      icon: 'warning',
      title: 'Syarat dan Ketentuan',
      text: 'Anda harus menyetujui syarat dan ketentuan untuk melanjutkan',
      confirmButtonColor: '#5e72e4'
    });
    return false;
  }

  return true;
};

const handleSubmit = async () => {
  errorMessage.value = "";
  successMessage.value = "";

  if (!validateForm()) return;

  loading.value = true;

  try {
    // Get reCAPTCHA token
    await window.grecaptcha.ready(async () => {
      try {
        recaptchaToken.value = await window.grecaptcha.execute(RECAPTCHA_SITE_KEY, { action: 'register' });
        
        // Clean WhatsApp number (remove spaces and dashes)
        const cleanWhatsapp = whatsapp.value.replace(/[\s-]/g, '');
        
        const response = await authAPI.register({
          email: email.value,
          password: password.value,
          password_confirmation: passwordConfirm.value,
          whatsapp: cleanWhatsapp,
          recaptcha_token: recaptchaToken.value,
        });

        Swal.fire({
          icon: 'success',
          title: 'Registrasi Berhasil!',
          html: response.data.message || 'Silakan cek email Anda untuk verifikasi akun.<br><small>Anda akan diarahkan ke halaman login...</small>',
          timer: 3000,
          timerProgressBar: true,
          showConfirmButton: false
        });
        
        // Redirect to login after 3 seconds
        setTimeout(() => {
          router.push("/login");
        }, 3000);
      } catch (error) {
        console.error("Registration error:", error);
        
        let errorTitle = 'Registrasi Gagal';
        let errorText = 'Terjadi kesalahan. Silakan coba lagi.';
        
        if (error.response?.data?.errors) {
          const errors = error.response.data.errors;
          errorText = Object.values(errors).flat().join(', ');
        } else if (error.response?.data?.message) {
          errorText = error.response.data.message;
        }
        
        Swal.fire({
          icon: 'error',
          title: errorTitle,
          text: errorText,
          confirmButtonColor: '#5e72e4'
        });
      } finally {
        loading.value = false;
      }
    });
  } catch (error) {
    console.error("reCAPTCHA error:", error);
    Swal.fire({
      icon: 'error',
      title: 'Verifikasi Keamanan Gagal',
      text: 'Gagal memuat verifikasi keamanan. Silakan refresh halaman.',
      confirmButtonColor: '#5e72e4'
    });
    loading.value = false;
  }
};

const formatWhatsApp = () => {
  // Auto format WhatsApp number
  let value = whatsapp.value.replace(/\D/g, ''); // Remove non-digits
  
  if (value.startsWith('62')) {
    whatsapp.value = '+' + value;
  } else if (value.startsWith('0')) {
    whatsapp.value = value;
  }
};
</script>

<template>
  <main class="main-content mt-0">
    <div
      class="page-header align-items-start min-vh-50 pt-5 pb-11 m-3 border-radius-lg"
      style="
        background-image: url(&quot;https://raw.githubusercontent.com/creativetimofficial/public-assets/master/argon-dashboard-pro/assets/img/signup-cover.jpg&quot;);
        background-position: top;
      "
    >
      <span class="mask bg-gradient-dark opacity-6"></span>
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-6 text-center mx-auto">
            <h1 class="text-white mb-2 mt-5">Daftar Sekarang!</h1>
            <p class="text-lead text-white">
              Kelola ISP Anda dengan platform billing terlengkap. Gratis 14
              hari.
            </p>
          </div>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="row mt-lg-n10 mt-md-n11 mt-n10 justify-content-center">
        <div class="col-xl-5 col-lg-6 col-md-7 mx-auto">
          <div class="card z-index-0">
            <div class="card-header text-center pt-4 pb-3">
              <router-link to="/" class="text-dark d-inline-block mb-3">
                <i class="fas fa-arrow-left me-2"></i>
                <span class="font-weight-bold">Kembali ke Home</span>
              </router-link>
              <h5>Registrasi ISP</h5>
              <p class="text-sm mb-0">Buat akun baru untuk memulai</p>
            </div>

            <div class="card-body">
              <div
                v-if="errorMessage"
                class="alert alert-danger alert-dismissible fade show"
                role="alert"
              >
                <span class="alert-icon"
                  ><i class="fas fa-exclamation-triangle"></i
                ></span>
                <span class="alert-text">{{ errorMessage }}</span>
                <button
                  type="button"
                  class="btn-close"
                  @click="errorMessage = ''"
                  aria-label="Close"
                ></button>
              </div>

              <div
                v-if="successMessage"
                class="alert alert-success alert-dismissible fade show"
                role="alert"
              >
                <span class="alert-icon"
                  ><i class="fas fa-check-circle"></i
                ></span>
                <span class="alert-text">{{ successMessage }}</span>
              </div>

              <form role="form" @submit.prevent="handleSubmit">
                <div class="mb-3">
                  <argon-input
                    v-model="email"
                    type="email"
                    placeholder="Email"
                    size="lg"
                    :disabled="loading"
                  >
                    <template v-slot:icon>
                      <i class="fas fa-envelope"></i>
                    </template>
                  </argon-input>
                </div>

                <div class="mb-3">
                  <argon-input
                    v-model="whatsapp"
                    type="text"
                    placeholder="Nomor WhatsApp (08xxx atau +628xxx)"
                    size="lg"
                    :disabled="loading"
                    @blur="formatWhatsApp"
                  >
                    <template v-slot:icon>
                      <i class="fab fa-whatsapp"></i>
                    </template>
                  </argon-input>
                </div>

                <div class="mb-3">
                  <argon-input
                    v-model="password"
                    type="password"
                    placeholder="Password (min. 8 karakter)"
                    size="lg"
                    :disabled="loading"
                  >
                    <template v-slot:icon>
                      <i class="fas fa-lock"></i>
                    </template>
                  </argon-input>
                </div>
                <div class="mb-3">
                  <argon-input
                    v-model="passwordConfirm"
                    type="password"
                    placeholder="Konfirmasi Password"
                    size="lg"
                    :disabled="loading"
                  >
                    <template v-slot:icon>
                      <i class="fas fa-lock"></i>
                    </template>
                  </argon-input>
                </div>

                <div class="mb-3">
                  <argon-checkbox id="terms" v-model="agreeTerms">
                    <span class="text-sm">
                      Saya menyetujui
                      <a href="#" class="text-success font-weight-bold"
                        >Syarat & Ketentuan</a
                      >
                      dan
                      <a href="#" class="text-success font-weight-bold"
                        >Kebijakan Privasi</a
                      >
                    </span>
                  </argon-checkbox>
                </div>

                <!-- reCAPTCHA Badge Info -->
                <div class="mb-3 text-center">
                  <small class="text-muted" style="font-size: 0.7rem;">
                    <i class="fas fa-shield-alt me-1"></i>
                    Protected by reCAPTCHA •
                    <a href="https://policies.google.com/privacy" target="_blank" class="text-muted">Privacy</a> •
                    <a href="https://policies.google.com/terms" target="_blank" class="text-muted">Terms</a>
                  </small>
                </div>

                <div class="text-center">
                  <argon-button
                    type="submit"
                    class="mt-4"
                    variant="gradient"
                    color="success"
                    fullWidth
                    size="lg"
                    :disabled="loading"
                  >
                    <span v-if="loading">
                      <i class="fas fa-spinner fa-spin me-2"></i>
                      Memproses...
                    </span>
                    <span v-else>
                      <i class="fas fa-user-plus me-2"></i>
                      Daftar Sekarang
                    </span>
                  </argon-button>
                </div>
              </form>
            </div>

            <div class="card-footer text-center pt-0 px-lg-2 px-1">
              <p class="mb-4 text-sm mx-auto">
                Sudah punya akun?
                <router-link
                  to="/login"
                  class="text-success text-gradient font-weight-bold"
                >
                  Login disini
                </router-link>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<style scoped>
.cursor-pointer {
  cursor: pointer;
  transition: all 0.3s ease;
}

.cursor-pointer:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.main-content {
  overflow-x: hidden;
}

.card {
  overflow: visible;
}

/* Hide reCAPTCHA badge - we show our own info text instead */
/* Using display:none for complete removal from layout */
.grecaptcha-badge {
  display: none !important;
  visibility: hidden !important;
}
</style>
