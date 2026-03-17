<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { authAPI } from '@/services/api';
import { detectTenant, getTenantInfo } from '@/utils/tenant';

const router = useRouter();

const step = ref(1);
const form = ref({ email: '', otp: '', password: '', passwordConfirm: '' });
const loading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');
const ispName = ref('');

onMounted(async () => {
  const tenant = detectTenant();
  if (tenant) {
    const info = getTenantInfo();
    ispName.value = info?.name || tenant.subdomain;
  }
});

const handleSubmit = async () => {
  errorMessage.value = '';
  if (step.value === 1) await sendOtp();
  else if (step.value === 2) await verifyOtp();
  else await resetPassword();
};

const sendOtp = async () => {
  loading.value = true;
  try {
    await authAPI.forgotPassword(form.value.email);
    step.value = 2;
    successMessage.value = 'Kode OTP telah dikirim ke email Anda!';
  } catch (e) {
    errorMessage.value = e.response?.data?.message || 'Gagal mengirim kode. Silakan cek kembali email Anda.';
  } finally { loading.value = false; }
};

const verifyOtp = async () => {
  loading.value = true;
  try {
    await authAPI.verifyResetCode({ email: form.value.email, code: form.value.otp });
    step.value = 3;
    successMessage.value = 'Kode valid! Silakan masukkan password baru Anda.';
  } catch (e) {
    errorMessage.value = e.response?.data?.message || 'Kode OTP salah atau sudah kadaluwarsa.';
  } finally { loading.value = false; }
};

const resetPassword = async () => {
  if (form.value.password !== form.value.passwordConfirm) {
    errorMessage.value = 'Konfirmasi password tidak cocok.';
    return;
  }
  loading.value = true;
  try {
    await authAPI.resetPassword({
      email: form.value.email,
      code: form.value.otp,
      password: form.value.password,
      password_confirmation: form.value.passwordConfirm,
    });
    successMessage.value = 'Password berhasil direset. Silakan login.';
    setTimeout(() => router.push('/login'), 2000);
  } catch (e) {
    errorMessage.value = e.response?.data?.message || 'Gagal mereset password. Silakan coba lagi.';
  } finally { loading.value = false; }
};
</script>

<template>
  <div class="auth-wrapper d-flex align-center justify-center pa-4">
    <div class="position-relative my-sm-16">

      <VCard
        class="auth-card"
        max-width="460"
        :class="$vuetify.display.smAndUp ? 'pa-6' : 'pa-0'"
      >
        <!-- Brand -->
        <VCardItem class="justify-center">
          <div class="d-flex align-center gap-3 app-logo">
            <VAvatar color="primary" rounded="lg" size="40">
              <VIcon color="white" size="22">bx-wifi</VIcon>
            </VAvatar>
            <h1 class="app-logo-title">{{ ispName || 'ISP Admin' }}</h1>
          </div>
        </VCardItem>

        <!-- Step Header -->
        <VCardText>
          <template v-if="step === 1">
            <h4 class="text-h4 mb-1">Lupa Password? 🔒</h4>
            <p class="mb-0">Masukkan email Anda untuk menerima kode OTP</p>
          </template>
          <template v-else-if="step === 2">
            <h4 class="text-h4 mb-1">Verifikasi OTP ✉️</h4>
            <p class="mb-0">Masukkan kode yang dikirim ke <strong>{{ form.email }}</strong></p>
          </template>
          <template v-else>
            <h4 class="text-h4 mb-1">Password Baru 🔑</h4>
            <p class="mb-0">Buat password baru untuk akun Anda</p>
          </template>
        </VCardText>

        <VCardText>
          <!-- Step progress -->
          <div class="d-flex align-center justify-center gap-2 mb-4">
            <VChip :color="step >= 1 ? 'primary' : undefined" size="small">1</VChip>
            <VDivider style="max-width:32px" />
            <VChip :color="step >= 2 ? 'primary' : undefined" size="small">2</VChip>
            <VDivider style="max-width:32px" />
            <VChip :color="step >= 3 ? 'primary' : undefined" size="small">3</VChip>
          </div>

          <!-- Alerts -->
          <VAlert v-if="successMessage" type="success" variant="tonal" class="mb-4">{{ successMessage }}</VAlert>
          <VAlert v-if="errorMessage" type="error" variant="tonal" closable class="mb-4" @click:close="errorMessage = ''">{{ errorMessage }}</VAlert>

          <VForm @submit.prevent="handleSubmit">
            <VRow>
              <!-- Step 1: Email -->
              <VCol v-if="step === 1" cols="12">
                <VTextField
                  v-model="form.email"
                  autofocus
                  label="Email"
                  type="email"
                  placeholder="admin@isp.com"
                />
              </VCol>

              <!-- Step 2: OTP -->
              <VCol v-if="step === 2" cols="12">
                <VTextField
                  v-model="form.otp"
                  autofocus
                  label="Kode OTP"
                  placeholder="6-digit kode"
                  maxlength="6"
                />
                <p class="text-body-2 mt-2">
                  Tidak menerima kode?
                  <a href="javascript:void(0)" class="text-primary" @click="sendOtp">Kirim ulang</a>
                </p>
              </VCol>

              <!-- Step 3: New Password -->
              <template v-if="step === 3">
                <VCol cols="12">
                  <VTextField
                    v-model="form.password"
                    label="Password Baru"
                    placeholder="············"
                    type="password"
                  />
                </VCol>
                <VCol cols="12">
                  <VTextField
                    v-model="form.passwordConfirm"
                    label="Konfirmasi Password"
                    placeholder="············"
                    type="password"
                  />
                </VCol>
              </template>

              <!-- Submit -->
              <VCol cols="12">
                <VBtn block type="submit" :loading="loading">
                  <span v-if="step === 1">Kirim Kode OTP</span>
                  <span v-else-if="step === 2">Verifikasi Kode</span>
                  <span v-else>Reset Password</span>
                </VBtn>
              </VCol>

              <!-- Back to Login -->
              <VCol cols="12" class="text-center">
                <RouterLink to="/login" class="text-primary d-inline-flex align-center gap-1 text-body-2">
                  <VIcon size="16">bx-arrow-back</VIcon>
                  Kembali ke Login
                </RouterLink>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </div>
  </div>
</template>

<style scoped>
.auth-wrapper {
  min-height: 100vh;
  background-color: rgb(var(--v-theme-background));
}
.auth-card {
  width: 100%;
  border-radius: 16px;
}
.app-logo { text-decoration: none; }
.app-logo-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: rgb(var(--v-theme-primary));
  text-transform: capitalize;
}
</style>
