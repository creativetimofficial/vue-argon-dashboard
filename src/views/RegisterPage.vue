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
const ispName = ref(""); // New field
const password = ref("");
const passwordConfirm = ref("");
const whatsapp = ref("");
const agreeTerms = ref(false);
const recaptchaToken = ref("");

// reCAPTCHA Site Key
const RECAPTCHA_SITE_KEY = import.meta.env.VITE_RECAPTCHA_SITE_KEY || "6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI";

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
  if (!email.value || !ispName.value || !password.value || !passwordConfirm.value || !whatsapp.value) {
    Swal.fire({
      icon: 'warning',
      title: 'Formulir Tidak Lengkap',
      text: 'Semua bidang termasuk Nama ISP harus diisi',
      buttonsStyling: false,
      customClass: {
        popup: 'swal-custom-popup',
        title: 'swal-custom-title',
        confirmButton: 'swal-btn-confirm mt-3',
        icon: 'swal-custom-icon mt-0 mb-3',
        htmlContainer: 'swal-custom-text fs-6'
      }
    });
    return false;
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email.value)) {
    Swal.fire({
      icon: 'warning',
      title: 'Email Tidak Valid',
      text: 'Format email tidak valid.',
      buttonsStyling: false,
      customClass: {
        popup: 'swal-custom-popup',
        title: 'swal-custom-title',
        confirmButton: 'swal-btn-confirm mt-3',
        icon: 'swal-custom-icon mt-0 mb-3',
        htmlContainer: 'swal-custom-text fs-6'
      }
    });
    return false;
  }

  const whatsappRegex = /^(\+62|62|0)[0-9]{9,12}$/;
  if (!whatsappRegex.test(whatsapp.value.replace(/[\s-]/g, ''))) {
    Swal.fire({
      icon: 'warning',
      title: 'Nomor WhatsApp Tidak Valid',
      text: 'Format nomor WhatsApp tidak valid.',
      buttonsStyling: false,
      customClass: {
        popup: 'swal-custom-popup',
        title: 'swal-custom-title',
        confirmButton: 'swal-btn-confirm mt-3',
        icon: 'swal-custom-icon mt-0 mb-3',
        htmlContainer: 'swal-custom-text fs-6'
      }
    });
    return false;
  }

  if (password.value.length < 8) {
    Swal.fire({
      icon: 'warning',
      title: 'Password Terlalu Pendek',
      text: 'Password minimal harus 8 karakter',
      buttonsStyling: false,
      customClass: {
        popup: 'swal-custom-popup',
        title: 'swal-custom-title',
        confirmButton: 'swal-btn-confirm mt-3',
        icon: 'swal-custom-icon mt-0 mb-3',
        htmlContainer: 'swal-custom-text fs-6'
      }
    });
    return false;
  }

  if (password.value !== passwordConfirm.value) {
    Swal.fire({
      icon: 'error',
      title: 'Password Tidak Cocok',
      text: 'Konfirmasi password tidak cocok.',
      buttonsStyling: false,
      customClass: {
        popup: 'swal-custom-popup',
        title: 'swal-custom-title',
        confirmButton: 'swal-btn-confirm mt-3',
        icon: 'swal-custom-icon mt-0 mb-3',
        htmlContainer: 'swal-custom-text fs-6'
      }
    });
    return false;
  }

  if (!agreeTerms.value) {
    Swal.fire({
      icon: 'warning',
      title: 'Syarat dan Ketentuan',
      text: 'Anda harus menyetujui syarat dan ketentuan.',
      buttonsStyling: false,
      customClass: {
        popup: 'swal-custom-popup',
        title: 'swal-custom-title',
        confirmButton: 'swal-btn-confirm mt-3',
        icon: 'swal-custom-icon mt-0 mb-3',
        htmlContainer: 'swal-custom-text fs-6'
      }
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
    await window.grecaptcha.ready(async () => {
      try {
        recaptchaToken.value = await window.grecaptcha.execute(RECAPTCHA_SITE_KEY, { action: 'register' });
        
        const cleanWhatsapp = whatsapp.value.replace(/[\s-]/g, '');
        
        const response = await authAPI.register({
          email: email.value,
          isp_name: ispName.value, // Send isp_name
          password: password.value,
          password_confirmation: passwordConfirm.value,
          whatsapp: cleanWhatsapp,
          recaptcha_token: recaptchaToken.value,
        });

        Swal.fire({
          icon: 'success',
          title: 'Registrasi Berhasil!',
          html: 'Akun Admin ISP Anda telah dibuat. Silakan periksa email Anda untuk verifikasi.',
          timer: 3000,
          timerProgressBar: true,
          showConfirmButton: false,
          customClass: {
            popup: 'swal-custom-popup',
            title: 'swal-custom-title',
            icon: 'swal-custom-icon mt-0 mb-3',
            htmlContainer: 'swal-custom-text fs-6'
          }
        });
        
        setTimeout(() => {
          router.push("/login"); // Fixed path
        }, 3000);
      } catch (error) {
        console.error("Registration error:", error);
        let errorText = 'An error occurred. Please try again.';
        if (error.response?.data?.errors) {
          errorText = Object.values(error.response.data.errors).flat().join(', ');
        } else if (error.response?.data?.message) {
          errorText = error.response.data.message;
        }
        Swal.fire({
          icon: 'error',
          title: 'Registrasi Gagal',
          text: errorText,
          buttonsStyling: false,
          customClass: {
            popup: 'swal-custom-popup',
            title: 'swal-custom-title',
            confirmButton: 'swal-btn-confirm mt-3',
            icon: 'swal-custom-icon mt-0 mb-3',
            htmlContainer: 'swal-custom-text fs-6'
          }
        });
      } finally {
        loading.value = false;
      }
    });
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Gagal memuat reCAPTCHA.',
      buttonsStyling: false,
      customClass: {
        popup: 'swal-custom-popup',
        title: 'swal-custom-title',
        confirmButton: 'swal-btn-confirm mt-3',
        icon: 'swal-custom-icon mt-0 mb-3',
        htmlContainer: 'swal-custom-text fs-6'
      }
    });
    loading.value = false;
  }
};

const formatWhatsApp = () => {
  let value = whatsapp.value.replace(/\D/g, '');
  if (value.startsWith('62')) {
    whatsapp.value = '+' + value;
  } else if (value.startsWith('0')) {
    whatsapp.value = value;
  }
};

const showTerms = (e) => {
  e.preventDefault();
  Swal.fire({
    title: 'Syarat dan Ketentuan',
    html: `
      <div class="text-start fs-6" style="max-height: 400px; overflow-y: auto;">
        <p>Dengan mendaftar sebagai Kemitraan Jaringan di platform ini, Anda menyetujui syarat-syarat berikut:</p>
        <ol class="ps-3 mb-0">
          <li class="mb-2"><strong>Akurasi Data:</strong> Anda menjamin bahwa semua informasi yang diberikan saat registrasi (termasuk Nama ISP, Email, dan WhatsApp) adalah akurat dan sah.</li>
          <li class="mb-2"><strong>Penggunaan Layanan:</strong> Layanan pengelolaan ISP disediakan "sebagaimana adanya". Anda bertanggung jawab penuh atas segala aktivitas manajemen dan transaksi yang dilakukan melalui akun Anda.</li>
          <li class="mb-2"><strong>Kerahasiaan Akun:</strong> Anda bertanggung jawab menjaga kerahasiaan password dan tidak membagikan akses kepada pihak yang tidak berkepentingan.</li>
          <li class="mb-2"><strong>Kepatuhan Hukum:</strong> Anda setuju untuk mematuhi semua regulasi dan undang-undang yang berlaku terkait penyediaan layanan internet di wilayah Anda.</li>
          <li class="mb-2"><strong>Penghentian Akses:</strong> Kami berhak untuk menangguhkan atau menghentikan akun Anda jika ditemukan indikasi penyalahgunaan, aktivitas ilegal, atau pelanggaran terhadap syarat dan ketentuan ini.</li>
        </ol>
      </div>
    `,
    icon: 'info',
    confirmButtonText: 'Saya Mengerti',
    buttonsStyling: false,
    width: '600px',
    customClass: {
      popup: 'swal-custom-popup',
      title: 'swal-custom-title',
      confirmButton: 'swal-btn-confirm mt-3',
      icon: 'swal-custom-icon mt-0 mb-3',
      htmlContainer: 'swal-custom-text text-start fs-6'
    }
  });
};

const showPrivacyInfo = (e) => {
  e.preventDefault();
  Swal.fire({
    title: 'Kebijakan Privasi',
    html: `
      <div class="text-start fs-6" style="max-height: 400px; overflow-y: auto;">
        <p>Kami sangat menghargai privasi dan keamanan data Anda. Berikut adalah ringkasan kebijakan privasi kami:</p>
        <ul class="ps-3 mb-0" style="list-style-type: disc;">
          <li class="mb-2"><strong>Pengumpulan Data:</strong> Kami mengumpulkan data yang Anda berikan langsung (seperti nama, email, nomor telepon) dan data analitik otomatis saat Anda menggunakan aplikasi.</li>
          <li class="mb-2"><strong>Penggunaan Data:</strong> Data Anda digunakan secara eksklusif untuk menyediakan layanan manajemen ISP, autentikasi akun, komunikasi penting, dan perbaikan fitur.</li>
          <li class="mb-2"><strong>Keamanan Data:</strong> Kami menerapkan enkripsi standar industri untuk melindungi informasi sensitif Anda dari akses yang tidak sah.</li>
          <li class="mb-2"><strong>Berbagi Data:</strong> Kami tidak akan menjual atau menyewakan data pribadi Anda ke pihak ketiga. Data hanya dapat dibagikan dengan mitra penyedia infrastruktur yang terikat dalam perjanjian kerahasiaan, atau jika diwajibkan oleh hukum.</li>
          <li class="mb-2"><strong>Hak Pengguna:</strong> Anda memegang kendali atas data yang tersimpan dan dapat memodifikasi atau meminta penghapusan akun beserta data terkait melalui panel dashboard atau dengan menghubungi dukungan pelanggan kami.</li>
        </ul>
      </div>
    `,
    icon: 'info',
    confirmButtonText: 'Saya Mengerti',
    buttonsStyling: false,
    width: '600px',
    customClass: {
      popup: 'swal-custom-popup',
      title: 'swal-custom-title',
      confirmButton: 'swal-btn-confirm mt-3',
      icon: 'swal-custom-icon mt-0 mb-3',
      htmlContainer: 'swal-custom-text text-start fs-6'
    }
  });
};
</script>

<template>
  <main class="main-content mt-0 overflow-hidden">
    <div
      class="page-header align-items-start min-vh-50 pt-5 pb-11 m-3 border-radius-lg"
      style="
        background-image: linear-gradient(310deg, rgba(45, 206, 137, 0.6), rgba(26, 158, 101, 0.6)), url('https://images.unsplash.com/photo-1551434678-e076c223a692?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
        background-position: center;
        background-size: cover;
      "
    >
      <span class="mask bg-gradient-dark opacity-4"></span>
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-6 text-center mx-auto">
            <h1 class="text-white mb-2 mt-5 animate__animated animate__fadeInDown">Kemitraan Jaringan</h1>
            <p class="text-lead text-white animate__animated animate__fadeInUp animate__delay-1s">
              Bergabunglah dengan ekosistem kami dan kembangkan bisnis ISP Anda lebih cepat dengan alat manajemen yang canggih.
            </p>
          </div>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="row mt-lg-n10 mt-md-n11 mt-n10 justify-content-center">
        <div class="col-xl-6 col-lg-7 col-md-9 mx-auto">
          <div class="card z-index-0 shadow-xl border-0 animate__animated animate__zoomIn">
            <div class="card-header text-center pt-4 pb-1">
              <router-link to="/" class="text-success d-inline-block mb-3 no-color-hover">
                <i class="fas fa-arrow-left me-2"></i>
                <span class="font-weight-bold">Kembali ke Beranda</span>
              </router-link>
              <h4 class="font-weight-bolder">Daftar Akun Admin</h4>
              <p class="text-sm mb-0">Lengkapi data di bawah ini untuk memulai kemitraan Anda</p>
            </div>

            <div class="card-body px-lg-5">
              <form role="form" @submit.prevent="handleSubmit">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label ps-2 text-xs font-weight-bold text-uppercase">Nama ISP</label>
                    <argon-input
                      v-model="ispName"
                      type="text"
                      placeholder="Contoh: Media Net"
                      size="lg"
                      :disabled="loading"
                    >
                      <template v-slot:icon>
                        <i class="fas fa-building text-success"></i>
                      </template>
                    </argon-input>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label ps-2 text-xs font-weight-bold text-uppercase">Nomor WhatsApp</label>
                    <argon-input
                      v-model="whatsapp"
                      type="text"
                      placeholder="081234567890"
                      size="lg"
                      :disabled="loading"
                      @blur="formatWhatsApp"
                    >
                      <template v-slot:icon>
                        <i class="fab fa-whatsapp text-success"></i>
                      </template>
                    </argon-input>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label ps-2 text-xs font-weight-bold text-uppercase">Email Admin</label>
                  <argon-input
                    v-model="email"
                    type="email"
                    placeholder="email@isp.com"
                    size="lg"
                    :disabled="loading"
                  >
                    <template v-slot:icon>
                      <i class="fas fa-envelope text-info"></i>
                    </template>
                  </argon-input>
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label ps-2 text-xs font-weight-bold text-uppercase">Password</label>
                    <argon-input
                      v-model="password"
                      type="password"
                      placeholder="Min. 8 karakter"
                      size="lg"
                      :disabled="loading"
                    >
                      <template v-slot:icon>
                        <i class="fas fa-lock text-warning"></i>
                      </template>
                    </argon-input>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label ps-2 text-xs font-weight-bold text-uppercase">Konfirmasi Password</label>
                    <argon-input
                      v-model="passwordConfirm"
                      type="password"
                      placeholder="Ulangi password"
                      size="lg"
                      :disabled="loading"
                    >
                      <template v-slot:icon>
                        <i class="fas fa-check-double text-warning"></i>
                      </template>
                    </argon-input>
                  </div>
                </div>

                <div class="mb-3">
                  <argon-checkbox id="terms" v-model="agreeTerms">
                    <span class="text-sm">
                      Saya setuju dengan
                      <a href="#" class="text-success font-weight-bold" @click="showTerms">Syarat dan Ketentuan</a>
                      serta
                      <a href="#" class="text-success font-weight-bold" @click="showPrivacyInfo">Kebijakan Privasi</a>
                    </span>
                  </argon-checkbox>
                </div>

                <div class="text-center">
                  <argon-button
                    type="submit"
                    class="mt-3 mb-2"
                    variant="gradient"
                    color="success"
                    fullWidth
                    size="lg"
                    :disabled="loading"
                  >
                    <span v-if="loading">
                      <i class="fas fa-circle-notch fa-spin me-2"></i>
                      Mendaftarkan ISP...
                    </span>
                    <span v-else>
                      <i class="fas fa-rocket me-2"></i>
                      Daftar sebagai Mitra
                    </span>
                  </argon-button>
                </div>
              </form>
              
              <div class="text-center mt-3">
                 <small class="text-muted" style="font-size: 0.75rem;">
                   <i class="fas fa-shield-alt me-1 text-info"></i> Registrasi Aman & Terenkripsi
                 </small>
              </div>
            </div>

            <div class="card-footer text-center pt-0 px-lg-2 px-1 pb-4">
              <p class="mb-0 text-sm mx-auto">
                Sudah menjadi mitra?
                <router-link
                  to="/login"
                  class="text-success text-gradient font-weight-bold"
                >
                  Masuk ke Dashboard
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
.shadow-xl {
  box-shadow: 0 20px 27px 0 rgba(0, 0, 0, 0.05);
}

.hover-move {
  transition: all 0.3s ease;
}
.hover-move:hover {
  transform: translateX(-5px);
}
.no-color-hover {
  transition: transform 0.3s ease;
}
.no-color-hover:hover {
  transform: translateX(-5px);
  color: #212529 !important;
}

.card {
  transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.card:hover {
  transform: translateY(-5px);
}

.form-label {
  margin-bottom: 0.25rem;
}

/* Animations */
@import 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css';

/* Custom responsive tweaks */
@media (max-width: 768px) {
  .page-header {
    min-height: 40vh !important;
  }
}

.grecaptcha-badge {
  display: none !important;
}
</style>
