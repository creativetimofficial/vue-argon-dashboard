<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { authAPI } from '@/services/api';
import { detectTenant, getTenantInfo } from '@/utils/tenant';

const router = useRouter();

const form = ref({
  email: '',
  whatsapp: '',
  password: '',
  passwordConfirm: '',
  agreeTerms: false,
});

const isPasswordVisible = ref(false);
const isConfirmVisible = ref(false);
const loading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');
const ispName = ref('');
const packages = ref([]);
const selectedPackage = ref(null);
const recaptchaToken = ref('');

const RECAPTCHA_SITE_KEY = import.meta.env.VITE_RECAPTCHA_SITE_KEY || '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI';

onMounted(async () => {
  const tenant = detectTenant();
  if (tenant) {
    const info = getTenantInfo();
    if (info?.name) {
      ispName.value = info.name;
    } else {
      try {
        const res = await authAPI.getTenantInfo();
        ispName.value = res.data?.name || tenant.subdomain;
      } catch { ispName.value = tenant.subdomain; }
    }
    fetchPackages();
  }
  loadRecaptchaScript();
});

const fetchPackages = async () => {
  try {
    const res = await authAPI.getPublicTenantPackages();
    if (res.data?.success) {
      packages.value = res.data.packages;
      if (packages.value.length > 0) selectedPackage.value = packages.value[0].id;
    }
  } catch (e) { console.error('Failed to fetch packages:', e); }
};

const formatPrice = (price) => new Intl.NumberFormat('en-US').format(price);

const formatWhatsApp = () => {
  let v = form.value.whatsapp.replace(/\D/g, '');
  form.value.whatsapp = v.startsWith('62') ? '+' + v : v;
};

const loadRecaptchaScript = () => {
  if (document.getElementById('recaptcha-script')) return;
  const s = document.createElement('script');
  s.id = 'recaptcha-script';
  s.src = `https://www.google.com/recaptcha/api.js?render=${RECAPTCHA_SITE_KEY}`;
  s.async = true; s.defer = true;
  document.head.appendChild(s);
};

const handleRegister = async () => {
  errorMessage.value = '';
  successMessage.value = '';

  if (!form.value.agreeTerms) { errorMessage.value = 'Anda harus menyetujui syarat dan ketentuan.'; return; }
  if (form.value.password !== form.value.passwordConfirm) { errorMessage.value = 'Konfirmasi password tidak cocok.'; return; }
  if (form.value.password.length < 8) { errorMessage.value = 'Password minimal 8 karakter.'; return; }

  loading.value = true;
  try {
    await window.grecaptcha.ready(async () => {
      try {
        recaptchaToken.value = await window.grecaptcha.execute(RECAPTCHA_SITE_KEY, { action: 'register' });
        await authAPI.register({
          email: form.value.email,
          password: form.value.password,
          password_confirmation: form.value.passwordConfirm,
          whatsapp: form.value.whatsapp.replace(/[\s-]/g, ''),
          package_id: selectedPackage.value,
          recaptcha_token: recaptchaToken.value,
        });
        successMessage.value = 'Registrasi berhasil! Silakan periksa email Anda untuk verifikasi.';
        setTimeout(() => router.push('/login'), 3000);
      } catch (err) {
        errorMessage.value = err.response?.data?.errors
          ? Object.values(err.response.data.errors).flat().join(', ')
          : (err.response?.data?.message || 'Registrasi gagal.');
      } finally { loading.value = false; }
    });
  } catch { errorMessage.value = 'Gagal memuat reCAPTCHA.'; loading.value = false; }
};
</script>

<template>
  <div class="auth-wrapper d-flex align-center justify-center pa-4">
    <div class="position-relative my-sm-16">

      <!-- Auth Card -->
      <VCard
        class="auth-card"
        max-width="520"
        :class="$vuetify.display.smAndUp ? 'pa-6' : 'pa-0'"
      >
        <!-- Brand Header -->
        <VCardItem class="justify-center">
          <div class="d-flex align-center gap-3 app-logo">
            <VAvatar color="primary" rounded="lg" size="40">
              <VIcon color="white" size="22">bx-wifi</VIcon>
            </VAvatar>
            <h1 class="app-logo-title">{{ ispName || 'ISP Admin' }}</h1>
          </div>
        </VCardItem>

        <VCardText>
          <h4 class="text-h4 mb-1">Daftar Sekarang 🚀</h4>
          <p class="mb-0">Buat akun untuk mengelola koneksi internet Anda!</p>
        </VCardText>

        <VCardText>
          <!-- Alerts -->
          <VAlert v-if="successMessage" type="success" variant="tonal" class="mb-4">
            {{ successMessage }}
          </VAlert>
          <VAlert
            v-if="errorMessage"
            type="error"
            variant="tonal"
            closable
            class="mb-4"
            @click:close="errorMessage = ''"
          >
            {{ errorMessage }}
          </VAlert>

          <VForm @submit.prevent="handleRegister">
            <VRow>
              <!-- Email -->
              <VCol cols="12" md="6">
                <VTextField
                  v-model="form.email"
                  autofocus
                  label="Email Aktif"
                  type="email"
                  placeholder="nama@email.com"
                />
              </VCol>

              <!-- WhatsApp -->
              <VCol cols="12" md="6">
                <VTextField
                  v-model="form.whatsapp"
                  label="Nomor WhatsApp"
                  placeholder="08xxxxxxxxxx"
                  @blur="formatWhatsApp"
                />
              </VCol>

              <!-- Package Selection -->
              <VCol v-if="packages.length > 0" cols="12">
                <p class="text-body-2 font-weight-medium mb-3">Pilih Paket Internet</p>
                <VRow dense>
                  <VCol
                    v-for="pkg in packages"
                    :key="pkg.id"
                    cols="6"
                    sm="4"
                  >
                    <VCard
                      :variant="selectedPackage === pkg.id ? 'tonal' : 'outlined'"
                      :color="selectedPackage === pkg.id ? 'primary' : undefined"
                      class="cursor-pointer pa-3 text-center"
                      @click="selectedPackage = pkg.id"
                    >
                      <div class="font-weight-bold text-truncate">{{ pkg.name }}</div>
                      <div class="text-primary text-body-2">Rp{{ formatPrice(pkg.price) }}</div>
                      <div class="text-medium-emphasis text-caption text-capitalize">{{ pkg.billing_cycle === 'monthly' ? 'bulanan' : pkg.billing_cycle || 'bulanan' }}</div>
                      <VIcon v-if="selectedPackage === pkg.id" color="primary" class="position-absolute" style="top:4px;right:4px" size="16">bx-check-circle</VIcon>
                    </VCard>
                  </VCol>
                </VRow>
              </VCol>

              <!-- Password -->
              <VCol cols="12" md="6">
                <VTextField
                  v-model="form.password"
                  label="Password"
                  placeholder="············"
                  :type="isPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isPasswordVisible ? 'bx-hide' : 'bx-show'"
                  @click:append-inner="isPasswordVisible = !isPasswordVisible"
                  autocomplete="new-password"
                />
              </VCol>

              <!-- Confirm Password -->
              <VCol cols="12" md="6">
                <VTextField
                  v-model="form.passwordConfirm"
                  label="Konfirmasi Password"
                  placeholder="············"
                  :type="isConfirmVisible ? 'text' : 'password'"
                  :append-inner-icon="isConfirmVisible ? 'bx-hide' : 'bx-show'"
                  @click:append-inner="isConfirmVisible = !isConfirmVisible"
                  autocomplete="new-password"
                />
              </VCol>

              <!-- Terms & Conditions -->
              <VCol cols="12">
                <div class="d-flex align-center my-2">
                  <VCheckbox
                    id="privacy-policy"
                    v-model="form.agreeTerms"
                    inline
                  />
                  <VLabel for="privacy-policy" style="opacity:1">
                    <span class="me-1">Saya setuju dengan</span>
                    <a href="javascript:void(0)" class="text-primary">Syarat &amp; Ketentuan</a>
                  </VLabel>
                </div>

                <!-- Register Button -->
                <VBtn
                  block
                  type="submit"
                  :loading="loading"
                >
                  Daftar Sekarang
                </VBtn>
              </VCol>

              <!-- Already have account -->
              <VCol cols="12" class="text-center text-base">
                <span>Sudah punya akun?</span>
                <RouterLink class="text-primary ms-1" to="/login">
                  Masuk di sini
                </RouterLink>
              </VCol>

              <!-- Divider -->
              <VCol cols="12" class="d-flex align-center">
                <VDivider />
                <span class="mx-4">atau</span>
                <VDivider />
              </VCol>

              <!-- Back to Home -->
              <VCol cols="12" class="text-center">
                <RouterLink
                  to="/"
                  class="text-medium-emphasis text-body-2 d-inline-flex align-center gap-1"
                >
                  <VIcon size="16">bx-arrow-back</VIcon>
                  Kembali ke Beranda
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
/* Sneat auth page layout */
.auth-wrapper {
  min-height: 100vh;
  background-color: rgb(var(--v-theme-background));
}

.auth-card {
  width: 100%;
  border-radius: 16px;
}

.app-logo {
  text-decoration: none;
}

.app-logo-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: rgb(var(--v-theme-primary));
  text-transform: capitalize;
}

.cursor-pointer {
  cursor: pointer;
}
</style>
