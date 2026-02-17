<script setup>
import { ref, onBeforeMount, onBeforeUnmount } from "vue";
import { useStore } from "vuex";
import { useRouter } from "vue-router";
import { authAPI } from "@/services/api";
import ArgonInput from "@/components/ArgonInput.vue";
import ArgonButton from "@/components/ArgonButton.vue";
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

const handleSubmit = async () => {
  if (step.value === 1) {
    await sendOtp();
  } else if (step.value === 2) {
    await verifyOtp();
  } else {
    await resetPassword();
  }
};

const sendOtp = async () => {
  loading.value = true;
  try {
    await authAPI.forgotPassword(email.value);
    Swal.fire({
      icon: 'success',
      title: 'Kode Terkirim!',
      text: 'Silakan cek email Anda untuk mendapatkan kode OTP.',
      timer: 3000,
      showConfirmButton: false
    });
    step.value = 2;
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Gagal',
      text: error.response?.data?.message || 'Gagal mengirim kode. Cek kembali email Anda.',
    });
  } finally {
    loading.value = false;
  }
};

const verifyOtp = async () => {
  loading.value = true;
  try {
    await authAPI.verifyResetCode({
      email: email.value,
      code: otp.value
    });
    Swal.fire({
      icon: 'success',
      title: 'Kode Valid!',
      text: 'Silakan masukkan password baru Anda.',
      timer: 2000,
      showConfirmButton: false
    });
    step.value = 3;
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Gagal',
      text: error.response?.data?.message || 'Kode OTP salah atau kadaluwarsa.',
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
      icon: 'error',
      title: 'Error',
      text: 'Konfirmasi password tidak cocok.',
    });
    return;
  }

  loading.value = true;
  try {
    await authAPI.resetPassword({
      email: email.value,
      code: otp.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value
    });

    Swal.fire({
      icon: 'success',
      title: 'Berhasil!',
      text: 'Password Anda telah diperbarui. Silakan login.',
    }).then(() => {
      router.push('/login');
    });
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Gagal',
      text: error.response?.data?.message || 'Kode OTP salah atau kadaluwarsa.',
    });
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <main class="mt-0 main-content">
    <section>
      <div 
        class="page-header min-vh-100"
        style="
          background-image: url('https://images.unsplash.com/photo-1497294815431-9365093b7331?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1950&q=80');
          background-size: cover;
          background-position: center;
        "
      >
        <span class="mask bg-gradient-dark opacity-6"></span>
        <div class="container">
          <div class="row">
            <div class="mx-auto col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0">
              <div class="card" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-radius: 1rem;">
                <div class="pb-0 card-header text-start" style="background: transparent;">
                  <div class="mb-3">
                    <router-link to="/login" class="text-dark">
                      <i class="fas fa-arrow-left me-2"></i>
                      <span class="font-weight-bold">Kembali ke Login</span>
                    </router-link>
                  </div>
                  <h4 class="font-weight-bolder">Reset Password</h4>
                  <p class="mb-0">
                    <span v-if="step === 1">Masukkan email Anda untuk menerima kode OTP</span>
                    <span v-else-if="step === 2">Masukkan kode OTP yang telah dikirim ke email Anda</span>
                    <span v-else>Masukkan password baru Anda</span>
                  </p>
                </div>
                <div class="card-body">
                  <form role="form" @submit.prevent="handleSubmit">
                    
                    <!-- Step 1: Input Email -->
                    <div v-if="step === 1">
                      <div class="mb-3">
                        <argon-input
                          id="email"
                          v-model="email"
                          type="email"
                          placeholder="Email"
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

                    <!-- Step 2: Input OTP Only -->
                    <div v-else-if="step === 2">
                      <div class="alert alert-info text-sm mb-3">
                        <i class="fas fa-envelope me-2"></i> Kode OTP telah dikirim ke <strong>{{ email }}</strong>
                      </div>

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
                    </div>

                    <!-- Step 3: Input New Password -->
                    <div v-else>
                      <div class="alert alert-success text-sm mb-3">
                        <i class="fas fa-check-circle me-2"></i> Kode OTP valid! Silakan buat password baru.
                      </div>

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
                        <span v-else-if="step === 1">
                          <i class="fas fa-paper-plane me-2"></i>
                          Kirim Kode OTP
                        </span>
                        <span v-else-if="step === 2">
                          <i class="fas fa-check-circle me-2"></i>
                          Verifikasi Kode
                        </span>
                        <span v-else>
                          <i class="fas fa-lock me-2"></i>
                          Reset Password
                        </span>
                      </argon-button>
                    </div>
                  </form>
                </div>
                <div class="px-1 pt-0 text-center card-footer px-lg-2">
                  <p v-if="step === 2" class="mx-auto mb-2 text-sm">
                    Tidak menerima kode?
                    <a
                      href="javascript:;"
                      @click="resendCode"
                      class="text-success text-gradient font-weight-bold"
                    >
                      Kirim Ulang
                    </a>
                  </p>
                </div>
              </div>
            </div>
            <div
              class="top-0 my-auto text-center col-6 d-lg-flex d-none h-100 pe-0 position-absolute end-0 justify-content-center flex-column"
            >
              <div
                class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center overflow-hidden"
                style="
                  background-image: url(&quot;https://raw.githubusercontent.com/creativetimofficial/public-assets/master/argon-dashboard-pro/assets/img/signin-ill.jpg&quot;);
                  background-size: cover;
                "
              >
                <span class="mask bg-gradient-success opacity-6"></span>
                <div class="position-relative">
                  <h4 class="mt-5 text-white font-weight-bolder">Lupa Password?</h4>
                  <p class="text-white">Jangan khawatir! Kami akan mengirimkan kode verifikasi ke email Anda untuk mereset password.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
</template>
