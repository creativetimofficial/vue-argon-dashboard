<template>
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner">
      <!-- Reset Password Card -->
      <div class="card px-sm-6 px-0">
        <div class="card-body">
          <!-- Logo -->
          <div class="app-brand justify-content-center mb-6">
            <span class="app-brand-text demo text-heading fw-bold text-center" style="font-size: 1.75rem;">{{ ispName || 'Reset Password' }}</span>
          </div>
          <!-- /Logo -->

          <h4 class="mb-2 text-center">Reset Password 🔒</h4>
          <p class="mb-4 text-center text-muted">
            <span v-if="step === 1">Masukkan email Anda untuk menerima kode OTP</span>
            <span v-else-if="step === 2">Masukkan kode OTP yang telah dikirim ke email Anda</span>
            <span v-else>Masukkan password baru Anda</span>
          </p>

          <!-- Alerts -->
          <div v-if="success" class="alert alert-success alert-dismissible mb-4" role="alert">
            {{ success }}
            <button type="button" class="btn-close" @click="success = null"></button>
          </div>

          <div v-if="error" class="alert alert-danger alert-dismissible mb-4" role="alert">
            {{ error }}
            <button type="button" class="btn-close" @click="error = null"></button>
          </div>

          <form id="formAuthentication" class="mb-3" @submit.prevent="handleSubmit">
            <!-- Step 1: Input Email -->
            <div v-if="step === 1">
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                  type="email"
                  class="form-control"
                  id="email"
                  name="email"
                  placeholder="Masukkan email Anda"
                  v-model="email"
                  autofocus
                  required
                />
              </div>
            </div>

            <!-- Step 2: Input OTP -->
            <div v-else-if="step === 2">
              <div class="alert alert-info mb-3">
                <i class="bx bx-envelope me-2"></i> Kode OTP telah dikirim ke <strong>{{ email }}</strong>
              </div>
              <div class="mb-3">
                <label for="otp" class="form-label">Kode OTP</label>
                <input
                  type="text"
                  class="form-control"
                  id="otp"
                  name="otp"
                  placeholder="Masukkan 6 digit kode OTP"
                  v-model="otp"
                  maxlength="6"
                  autofocus
                  required
                />
              </div>
            </div>

            <!-- Step 3: Input New Password -->
            <div v-else>
              <div class="alert alert-success mb-3">
                <i class="bx bx-check-circle me-2"></i> Kode OTP valid! Silakan buat password baru.
              </div>
              <div class="mb-3 form-password-toggle">
                <label class="form-label" for="password">Password Baru</label>
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
                <label class="form-label" for="passwordConfirmation">Konfirmasi Password</label>
                <div class="input-group input-group-merge">
                  <input
                    :type="showPasswordConfirm ? 'text' : 'password'"
                    id="passwordConfirmation"
                    class="form-control"
                    name="passwordConfirmation"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    v-model="passwordConfirmation"
                    required
                  />
                  <span class="input-group-text cursor-pointer" @click="showPasswordConfirm = !showPasswordConfirm" style="z-index: 10;">
                    <i class="bx" :class="showPasswordConfirm ? 'bx-show' : 'bx-hide'"></i>
                  </span>
                </div>
              </div>
            </div>

            <button class="btn btn-primary d-grid w-100" type="submit" :disabled="loading">
              <span v-if="loading">
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Memproses...
              </span>
              <span v-else-if="step === 1">Kirim Kode OTP</span>
              <span v-else-if="step === 2">Verifikasi Kode</span>
              <span v-else>Reset Password</span>
            </button>
          </form>

          <div class="text-center">
            <p v-if="step === 2" class="mb-2">
              Tidak menerima kode? 
              <a href="javascript:;" @click="resendCode" class="text-primary">Kirim Ulang</a>
            </p>
            <router-link to="/login" class="d-flex align-items-center justify-content-center">
              <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i>
              Kembali ke Login
            </router-link>
          </div>
        </div>
      </div>
      <!-- /Reset Password Card -->
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { authAPI } from "@/services/api";
import { detectTenant, getTenantInfo } from '@/utils/tenant';
import Swal from "sweetalert2";

const router = useRouter();

const step = ref(1);
const email = ref("");
const otp = ref("");
const password = ref("");
const passwordConfirmation = ref("");
const showPassword = ref(false);
const showPasswordConfirm = ref(false);
const loading = ref(false);
const error = ref(null);
const success = ref(null);
const ispName = ref("");

onMounted(async () => {
  // Get ISP Name for branding
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
        } else {
          ispName.value = tenant.subdomain;
        }
      } catch {
        ispName.value = tenant.subdomain;
      }
    }
  }
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
  error.value = null;
  success.value = null;

  try {
    await authAPI.forgotPassword(email.value);
    success.value = "Kode OTP telah dikirim ke email Anda.";
    step.value = 2;
  } catch (err) {
    error.value = err.response?.data?.message || 'Gagal mengirim kode. Cek kembali email Anda.';
  } finally {
    loading.value = false;
  }
};

const verifyOtp = async () => {
  loading.value = true;
  error.value = null;
  success.value = null;

  try {
    await authAPI.verifyResetCode({
      email: email.value,
      code: otp.value
    });
    success.value = "Kode valid! Silakan masukkan password baru.";
    step.value = 3;
  } catch (err) {
    error.value = err.response?.data?.message || 'Kode OTP salah atau kadaluwarsa.';
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
    error.value = 'Konfirmasi password tidak cocok.';
    return;
  }

  loading.value = true;
  error.value = null;
  success.value = null;

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
      confirmButtonColor: '#696cff'
    }).then(() => {
      router.push('/login');
    });
  } catch (err) {
    error.value = err.response?.data?.message || 'Gagal mereset password. Silakan coba lagi.';
  } finally {
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
</style>
