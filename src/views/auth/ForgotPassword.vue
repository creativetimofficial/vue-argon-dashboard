<script setup>
import { ref, onBeforeMount, onBeforeUnmount } from "vue";
import { useStore } from "vuex";
import { useRouter } from "vue-router";
import { authAPI } from "@/services/api";
import Swal from "sweetalert2";

const body = document.getElementsByTagName("body")[0];
const store = useStore();
const router = useRouter();

const step = ref(1);
const email = ref("");
const otp = ref("");
const password = ref("");
const passwordConfirmation = ref("");
const showPassword = ref(false);
const showPasswordConfirm = ref(false);
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
    step.value = 2;
    Swal.fire({
      icon: 'success',
      title: 'Code Sent!',
      text: 'Please check your email for the OTP code.',
      timer: 3000,
      showConfirmButton: false
    });
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Failed',
      text: error.response?.data?.message || 'Failed mengirim kode. Cek kembali email Anda.',
      confirmButtonColor: '#2dce89'
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
    step.value = 3;
    Swal.fire({
      icon: 'success',
      title: 'Code Valid!',
      text: 'Please enter your new password.',
      timer: 2000,
      showConfirmButton: false
    });
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Failed',
      text: error.response?.data?.message || 'OTP code is incorrect or expired.',
      confirmButtonColor: '#2dce89'
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
      text: 'Password confirmation does not match.',
      confirmButtonColor: '#2dce89'
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
      title: 'Success!',
      text: 'Your password has been updated. Please log in.',
      confirmButtonColor: '#2dce89'
    }).then(() => {
      router.push('/login');
    });
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Failed',
      text: error.response?.data?.message || 'Failed mereset password. Silakan coba lagi.',
      confirmButtonColor: '#2dce89'
    });
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="auth-page-wrapper">
    <div class="auth-card animate__animated animate__zoomIn">
      <!-- Green Header -->
      <div class="auth-header">
        <div class="shape-blob"></div>
        <div class="shape-blob secondary"></div>
        <div class="position-relative z-index-2 text-center">
          <i class="fas fa-lock text-white mb-2" style="font-size: 2rem;"></i>
          <h3 class="text-white fw-bold mb-0">Reset Password</h3>
          <p class="text-white opacity-8 mb-0 small mt-1">
            <span v-if="step === 1">Admin & Customer Portal</span>
            <span v-else-if="step === 2">Enter the OTP code from your email</span>
            <span v-else>Create your new password</span>
          </p>
        </div>
      </div>

      <!-- Card Body -->
      <div class="auth-body">
        <!-- Step indicator -->
        <div class="step-indicator mb-4">
          <div class="step-dot" :class="{ active: step >= 1, done: step > 1 }">
            <i class="fas fa-envelope" v-if="step <= 1"></i>
            <i class="fas fa-check" v-else></i>
          </div>
          <div class="step-line" :class="{ active: step > 1 }"></div>
          <div class="step-dot" :class="{ active: step >= 2, done: step > 2 }">
            <i class="fas fa-key" v-if="step <= 2"></i>
            <i class="fas fa-check" v-else></i>
          </div>
          <div class="step-line" :class="{ active: step > 2 }"></div>
          <div class="step-dot" :class="{ active: step >= 3 }">
            <i class="fas fa-lock"></i>
          </div>
        </div>

        <form @submit.prevent="handleSubmit">
          <!-- Step 1: Email -->
          <div v-if="step === 1">
            <div class="mb-3">
              <label class="form-label text-xs fw-bold text-uppercase">Email</label>
              <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0">
                  <i class="fas fa-envelope text-success"></i>
                </span>
                <input
                  type="email"
                  class="form-control form-control-lg border-start-0 ps-0"
                  placeholder="Enter your email"
                  v-model="email"
                  autofocus
                  required
                />
              </div>
            </div>
          </div>

          <!-- Step 2: OTP -->
          <div v-else-if="step === 2">
            <div class="alert-soft-info d-flex align-items-center p-3 rounded mb-3">
              <i class="fas fa-envelope me-2" style="color: #2563eb;"></i>
              <span class="small">OTP code sent to <strong>{{ email }}</strong></span>
            </div>
            <div class="mb-3">
              <label class="form-label text-xs fw-bold text-uppercase">Kode OTP</label>
              <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0">
                  <i class="fas fa-key text-success"></i>
                </span>
                <input
                  type="text"
                  class="form-control form-control-lg border-start-0 ps-0 text-center fw-bold"
                  style="letter-spacing: 0.4em; font-size: 1.2rem;"
                  placeholder="• • • • • •"
                  v-model="otp"
                  maxlength="6"
                  autofocus
                  required
                />
              </div>
            </div>
          </div>

          <!-- Step 3: New Password -->
          <div v-else>
            <div class="alert-soft-success d-flex align-items-center p-3 rounded mb-3">
              <i class="fas fa-check-circle me-2" style="color: #059669;"></i>
              <span class="small">Code valid! Please create a new password.</span>
            </div>
            <div class="mb-3">
              <label class="form-label text-xs fw-bold text-uppercase">New Password</label>
              <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0">
                  <i class="fas fa-lock text-success"></i>
                </span>
                <input
                  :type="showPassword ? 'text' : 'password'"
                  class="form-control form-control-lg border-start-0 border-end-0 ps-0"
                  placeholder="••••••••"
                  v-model="password"
                  required
                />
                <span class="input-group-text bg-transparent border-start-0" style="cursor:pointer;" @click="showPassword = !showPassword">
                  <i class="fas" :class="showPassword ? 'fa-eye' : 'fa-eye-slash'"></i>
                </span>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label text-xs fw-bold text-uppercase">Confirm Password</label>
              <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0">
                  <i class="fas fa-lock text-success"></i>
                </span>
                <input
                  :type="showPasswordConfirm ? 'text' : 'password'"
                  class="form-control form-control-lg border-start-0 border-end-0 ps-0"
                  placeholder="••••••••"
                  v-model="passwordConfirmation"
                  required
                />
                <span class="input-group-text bg-transparent border-start-0" style="cursor:pointer;" @click="showPasswordConfirm = !showPasswordConfirm">
                  <i class="fas" :class="showPasswordConfirm ? 'fa-eye' : 'fa-eye-slash'"></i>
                </span>
              </div>
            </div>
          </div>

          <div class="mb-3 mt-2">
            <button class="btn btn-success-custom d-grid w-100 btn-lg py-3" type="submit" :disabled="loading">
              <span v-if="loading">
                <i class="fas fa-circle-notch fa-spin me-2"></i>Processing...
              </span>
              <span v-else-if="step === 1">
                <i class="fas fa-paper-plane me-2"></i>Send OTP Code
              </span>
              <span v-else-if="step === 2">
                <i class="fas fa-check me-2"></i>Verify Code
              </span>
              <span v-else>
                <i class="fas fa-lock me-2"></i>Reset Password
              </span>
            </button>
          </div>
        </form>

        <div class="text-center">
          <p v-if="step === 2" class="mb-2 small text-muted">
            Didn't receive the code?
            <a href="javascript:;" @click="resendCode" class="text-success fw-bold">Resend</a>
          </p>
          <router-link to="/login" class="d-flex align-items-center justify-content-center text-success small fw-bold">
            <i class="fas fa-arrow-left me-1"></i>
            Back to Login
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@import 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css';

.auth-page-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  background: #f4f7f6;
  padding: 1rem;
}

.auth-card {
  background: #fff;
  border-radius: 1rem;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  width: 100%;
  max-width: 440px;
}

.auth-header {
  background: linear-gradient(135deg, #2dce89 0%, #1aae6d 100%);
  padding: 2rem 2.5rem;
  position: relative;
  overflow: hidden;
}

.auth-body {
  padding: 2rem 2.5rem;
}

/* Blob decorations */
.shape-blob {
  position: absolute;
  height: 120px;
  width: 120px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  top: -40px;
  right: -40px;
  z-index: 1;
}
.shape-blob.secondary {
  height: 80px;
  width: 80px;
  bottom: -20px;
  left: -20px;
  top: auto;
  right: auto;
}

/* Step indicator */
.step-indicator {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0;
}
.step-dot {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #e5e7eb;
  color: #9ca3af;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  transition: all 0.3s;
  flex-shrink: 0;
}
.step-dot.active {
  background: linear-gradient(135deg, #2dce89 0%, #1aae6d 100%);
  color: #fff;
  box-shadow: 0 4px 10px rgba(45, 206, 137, 0.35);
}
.step-dot.done {
  background: #1aae6d;
  color: #fff;
}
.step-line {
  flex: 1;
  height: 3px;
  background: #e5e7eb;
  max-width: 60px;
  transition: background 0.3s;
}
.step-line.active {
  background: linear-gradient(90deg, #2dce89, #1aae6d);
}

/* Alerts */
.alert-soft-info {
  background: #dbeafe;
  color: #1e40af;
}
.alert-soft-success {
  background: #d1fae5;
  color: #065f46;
}

/* Input */
.form-control:focus {
  border-color: #2dce89;
  box-shadow: 0 0 0 0.25rem rgba(45, 206, 137, 0.15);
}
.input-group-text {
  border-color: #dee2e6;
}

/* Button */
.btn-success-custom {
  background: linear-gradient(135deg, #2dce89 0%, #1aae6d 100%);
  border: 0;
  color: #fff;
  font-weight: 600;
  border-radius: 0.5rem;
  transition: all 0.2s;
}
.btn-success-custom:hover:not(:disabled) {
  background: linear-gradient(135deg, #26b87a 0%, #178a58 100%);
  transform: translateY(-1px);
  box-shadow: 0 6px 15px rgba(45, 206, 137, 0.35);
}
.btn-success-custom:disabled {
  opacity: 0.7;
}

@media (max-width: 576px) {
  .auth-body { padding: 1.5rem; }
  .auth-header { padding: 1.5rem; }
}
</style>
