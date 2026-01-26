<script setup>
import { ref, onBeforeMount, onBeforeUnmount } from "vue";
import { useStore } from "vuex";
import { useRouter } from "vue-router";
import ArgonInput from "@/components/ArgonInput.vue";
import ArgonButton from "@/components/ArgonButton.vue";
import ArgonCheckbox from "@/components/ArgonCheckbox.vue";
import { authAPI } from "@/services/api";

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
const agreeTerms = ref(false);

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

const validateForm = () => {
  if (!email.value || !password.value || !passwordConfirm.value) {
    errorMessage.value = "Semua field harus diisi";
    return false;
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email.value)) {
    errorMessage.value = "Format email tidak valid";
    return false;
  }

  if (password.value.length < 8) {
    errorMessage.value = "Password minimal 8 karakter";
    return false;
  }

  if (password.value !== passwordConfirm.value) {
    errorMessage.value = "Konfirmasi password tidak cocok";
    return false;
  }

  if (!agreeTerms.value) {
    errorMessage.value = "Anda harus menyetujui syarat dan ketentuan";
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
    const response = await authAPI.register({
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirm.value,
    });

    successMessage.value = response.data.message || "Registrasi berhasil! Silakan cek email Anda untuk verifikasi akun.";
    
    // Redirect to login after 3 seconds
    setTimeout(() => {
      router.push("/login");
    }, 3000);
  } catch (error) {
    console.error("Registration error:", error);
    if (error.response?.data?.errors) {
      const errors = error.response.data.errors;
      errorMessage.value = Object.values(errors).flat().join(", ");
    } else if (error.response?.data?.message) {
      errorMessage.value = error.response.data.message;
    } else {
      errorMessage.value = "Terjadi kesalahan. Silakan coba lagi.";
    }
  } finally {
    loading.value = false;
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
        <div class="col-xl-4 col-lg-5 col-md-7 mx-auto">
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
</style>
