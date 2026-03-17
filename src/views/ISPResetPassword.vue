<script setup>
import { ref, onBeforeMount, onBeforeUnmount } from "vue";
import { useStore } from "vuex";
import { useRouter } from "vue-router";
import { authAPI } from "@/services/api";
import ArgonInput from "@/components/ArgonInput.vue";
import ArgonButton from "@/components/ArgonButton.vue";
import { detectTenant, getTenantInfo } from "@/utils/tenant";
import Swal from "sweetalert2";

const body = document.getElementsByTagName("body")[0];
const store = useStore();
const router = useRouter();

const step = ref(1);
const email = ref("");
const otp = ref("");
const password = ref("");
const passwordConfirmation = ref("");
const loading = ref(false);
const ispName = ref("");
const ispLogo = ref("");

onBeforeMount(async () => {
  store.state.hideConfigButton = true;
  store.state.showNavbar = false;
  store.state.showSidenav = false;
  store.state.showFooter = false;
  body.classList.remove("bg-gray-100");

  const tenant = detectTenant();
  if (tenant) {
    const info = getTenantInfo();
    if (info) {
      ispName.value = info.name || "";
      ispLogo.value = info.logo || "";
    } else {
      try {
        const res = await authAPI.getTenantInfo();
        if (res.data) {
          ispName.value = res.data.name || "";
          ispLogo.value = res.data.logo || "";
        }
      } catch {}
    }
  }
});

onBeforeUnmount(() => {
  store.state.hideConfigButton = false;
  store.state.showNavbar = true;
  store.state.showSidenav = true;
  store.state.showFooter = true;
  body.classList.add("bg-gray-100");
});

const handleSubmit = async () => {
  if (step.value === 1) await sendOtp();
  else if (step.value === 2) await verifyOtp();
  else await resetPassword();
};

const sendOtp = async () => {
  loading.value = true;
  try {
    await authAPI.forgotPassword(email.value);
    step.value = 2;
    Swal.fire({
      icon: "success",
      title: "Kode Terkirim!",
      text: "Silakan periksa email Anda untuk kode OTP.",
      timer: 3000,
      showConfirmButton: false,
      customClass: {
        popup: 'swal-custom-popup',
        title: 'swal-custom-title',
        icon: 'swal-custom-icon mt-0 mb-3',
        htmlContainer: 'swal-custom-text fs-6'
      }
    });
  } catch (err) {
    Swal.fire({
      icon: "error",
      title: "Gagal",
      text: err.response?.data?.message || "Gagal mengirim kode. Cek kembali email Anda.",
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
};

const verifyOtp = async () => {
  loading.value = true;
  try {
    await authAPI.verifyResetCode({ email: email.value, code: otp.value });
    step.value = 3;
    Swal.fire({
      icon: "success",
      title: "Kode Valid!",
      text: "Silakan masukkan password baru Anda.",
      timer: 2000,
      showConfirmButton: false,
      customClass: {
        popup: 'swal-custom-popup',
        title: 'swal-custom-title',
        icon: 'swal-custom-icon mt-0 mb-3',
        htmlContainer: 'swal-custom-text fs-6'
      }
    });
  } catch (err) {
    Swal.fire({
      icon: "error",
      title: "Kode Salah",
      text: err.response?.data?.message || "Kode OTP salah atau sudah kadaluwarsa.",
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
};

const resendCode = async () => {
  step.value = 1;
  await sendOtp();
};

const resetPassword = async () => {
  if (password.value !== passwordConfirmation.value) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "Konfirmasi password tidak cocok.",
      buttonsStyling: false,
      customClass: {
        popup: 'swal-custom-popup',
        title: 'swal-custom-title',
        confirmButton: 'swal-btn-confirm mt-3',
        icon: 'swal-custom-icon mt-0 mb-3',
        htmlContainer: 'swal-custom-text fs-6'
      }
    });
    return;
  }
  loading.value = true;
  try {
    await authAPI.resetPassword({
      email: email.value,
      code: otp.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    });
    Swal.fire({
      icon: "success",
      title: "Berhasil!",
      text: "Password Anda telah diperbarui. Silakan login.",
      buttonsStyling: false,
      customClass: {
        popup: 'swal-custom-popup',
        title: 'swal-custom-title',
        confirmButton: 'swal-btn-confirm mt-3',
        icon: 'swal-custom-icon mt-0 mb-3',
        htmlContainer: 'swal-custom-text fs-6'
      }
    }).then(() => router.push("/login"));
  } catch (err) {
    Swal.fire({
      icon: "error",
      title: "Gagal",
      text: err.response?.data?.message || "Gagal mereset password. Silakan coba lagi.",
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
};
</script>

<template>
  <main class="mt-0 main-content">
    <section>
      <div class="page-header min-vh-100">
        <div class="container">
          <div class="row">
            <!-- LEFT: Form -->
            <div class="mx-auto col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0">
              <div class="card card-plain">
                <div class="pb-0 card-header text-start">
                  <div class="mb-3">
                    <router-link to="/login" class="text-dark">
                      <i class="fas fa-arrow-left me-2"></i>
                      <span class="font-weight-bold">Kembali ke Login</span>
                    </router-link>
                  </div>

                  <template v-if="step === 1">
                    <h4 class="font-weight-bolder">Lupa Password?</h4>
                    <p class="mb-0">Masukkan email Anda, kami akan mengirimkan kode OTP</p>
                  </template>
                  <template v-else-if="step === 2">
                    <h4 class="font-weight-bolder">Verifikasi OTP</h4>
                    <p class="mb-0">Masukkan kode yang dikirim ke <strong>{{ email }}</strong></p>
                  </template>
                  <template v-else>
                    <h4 class="font-weight-bolder">Password Baru</h4>
                    <p class="mb-0">Buat password baru untuk akun Anda</p>
                  </template>
                </div>

                <div class="card-body">
                  <form role="form" @submit.prevent="handleSubmit">

                    <!-- Step 1: Email -->
                    <div v-if="step === 1">
                      <div class="mb-3">
                        <argon-input
                          id="email"
                          v-model="email"
                          type="email"
                          placeholder="Masukkan email Anda"
                          name="email"
                          size="lg"
                          :disabled="loading"
                        >
                          <template v-slot:icon>
                            <i class="fas fa-envelope"></i>
                          </template>
                        </argon-input>
                      </div>
                    </div>

                    <!-- Step 2: OTP -->
                    <div v-else-if="step === 2">
                      <div class="mb-3">
                        <argon-input
                          id="otp"
                          v-model="otp"
                          type="text"
                          placeholder="Kode OTP (6 Digit)"
                          name="otp"
                          size="lg"
                          maxlength="6"
                          :disabled="loading"
                        >
                          <template v-slot:icon>
                            <i class="fas fa-key"></i>
                          </template>
                        </argon-input>
                      </div>
                      <p class="text-sm text-muted">
                        Tidak menerima kode?
                        <a href="javascript:;" @click="resendCode" class="text-success font-weight-bold">Kirim ulang</a>
                      </p>
                    </div>

                    <!-- Step 3: New Password -->
                    <div v-else>
                      <div class="mb-3">
                        <argon-input
                          id="password"
                          v-model="password"
                          type="password"
                          placeholder="Password Baru"
                          name="password"
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
                          id="passwordConfirmation"
                          v-model="passwordConfirmation"
                          type="password"
                          placeholder="Konfirmasi Password"
                          name="passwordConfirmation"
                          size="lg"
                          :disabled="loading"
                        >
                          <template v-slot:icon>
                            <i class="fas fa-lock"></i>
                          </template>
                        </argon-input>
                      </div>
                    </div>

                    <!-- Submit button -->
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
                          <i class="fas fa-spinner fa-spin me-2"></i>Memproses...
                        </span>
                        <span v-else-if="step === 1">
                          <i class="fas fa-paper-plane me-2"></i>Kirim Kode OTP
                        </span>
                        <span v-else-if="step === 2">
                          <i class="fas fa-check me-2"></i>Verifikasi Kode
                        </span>
                        <span v-else>
                          <i class="fas fa-lock me-2"></i>Reset Password
                        </span>
                      </argon-button>
                    </div>
                  </form>
                </div>

                <!-- Step indicator footer -->
                <div class="px-1 pt-0 text-center card-footer px-lg-2">
                  <p class="mx-auto mb-4 text-sm text-muted">
                    <span class="step-badge" :class="{ active: step >= 1 }">1</span>
                    <span class="step-sep">—</span>
                    <span class="step-badge" :class="{ active: step >= 2 }">2</span>
                    <span class="step-sep">—</span>
                    <span class="step-badge" :class="{ active: step >= 3 }">3</span>
                  </p>
                </div>
              </div>
            </div>

            <!-- RIGHT: Green panel -->
            <div class="top-0 my-auto text-center col-6 d-lg-flex d-none h-100 pe-0 position-absolute end-0 justify-content-center flex-column">
              <div
                class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center overflow-hidden"
                style="background-image: url(&quot;https://raw.githubusercontent.com/creativetimofficial/public-assets/master/argon-dashboard-pro/assets/img/signin-ill.jpg&quot;); background-size: cover;"
              >
                <span class="mask bg-gradient-success opacity-6"></span>
                <div class="position-relative text-center">
                  <img v-if="ispLogo" :src="ispLogo" class="mb-3" style="max-height: 70px;" alt="Logo" />
                  <h4 class="mt-5 text-white font-weight-bolder">
                    {{ ispName ? ispName : 'Reset Password' }}
                  </h4>
                  <p class="text-white">
                    Ikuti langkah-langkah untuk memulihkan akses akun Anda dengan aman.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
</template>

<style scoped>
.step-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: #e5e7eb;
  color: #9ca3af;
  font-size: 11px;
  font-weight: 700;
  transition: all 0.3s;
}
.step-badge.active {
  background: linear-gradient(135deg, #2dce89, #1a9e65);
  color: #fff;
}
.step-sep {
  margin: 0 4px;
  color: #d1d5db;
}
</style>
