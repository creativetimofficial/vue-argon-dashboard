<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { authAPI } from '@/services/api';
import { detectTenant, getTenantInfo } from '@/utils/tenant';

const router = useRouter();

const form = ref({
  email: '',
  password: '',
  remember: false,
});

const isPasswordVisible = ref(false);
const loading = ref(false);
const errorMessage = ref('');
const ispName = ref('');

// Load tenant name
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
      } catch {
        ispName.value = tenant.subdomain;
      }
    }
  }
  // Auto-fill remembered email
  const saved = localStorage.getItem('remembered_email');
  if (saved) { form.value.email = saved; form.value.remember = true; }
});

const handleLogin = async () => {
  errorMessage.value = '';
  loading.value = true;
  try {
    const res = await authAPI.login({ email: form.value.email, password: form.value.password });
    let data = res.data;
    if (typeof data === 'string') {
      const m = data.match(/\{[\s\S]*\}/);
      data = m ? JSON.parse(m[0]) : (() => { throw new Error('Invalid response'); })();
    }
    const { user, token } = data;
    if (!user || !token) throw new Error('Login failed');

    if (form.value.remember) {
      localStorage.setItem('auth_token', token);
      localStorage.setItem('user', JSON.stringify(user));
      localStorage.setItem('remembered_email', form.value.email);
    } else {
      sessionStorage.setItem('auth_token', token);
      sessionStorage.setItem('user', JSON.stringify(user));
      localStorage.removeItem('remembered_email');
      localStorage.removeItem('auth_token');
    }

    if (user.role === 'isp_admin') {
      localStorage.setItem('isp_admin_token', token);
      localStorage.setItem('isp_admin_user', JSON.stringify(user));
    }

    router.push('/isp-admin/dashboard');
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Login gagal. Silakan periksa kembali email dan password Anda.';
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="auth-wrapper d-flex align-center justify-center pa-4">
    <div class="position-relative my-sm-16">

      <!-- 👉 Auth Card -->
      <VCard
        class="auth-card"
        max-width="460"
        :class="$vuetify.display.smAndUp ? 'pa-6' : 'pa-0'"
      >
        <!-- Brand Header -->
        <VCardItem class="justify-center">
          <div class="d-flex align-center gap-3 app-logo">
            <VAvatar color="primary" rounded="lg" size="40">
              <VIcon color="white" size="22">bx-wifi</VIcon>
            </VAvatar>
            <h1 class="app-logo-title">
              {{ ispName || 'ISP Admin' }}
            </h1>
          </div>
        </VCardItem>

        <VCardText>
          <h4 class="text-h4 mb-1">Selamat Datang! 👋🏻</h4>
          <p class="mb-0">Silakan masuk ke akun ISP Anda</p>
        </VCardText>

        <VCardText>
          <!-- Error Alert -->
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

          <VForm @submit.prevent="handleLogin">
            <VRow>
              <!-- Email -->
              <VCol cols="12">
                <VTextField
                  v-model="form.email"
                  autofocus
                  label="Email"
                  type="email"
                  placeholder="admin@isp.com"
                />
              </VCol>

              <!-- Password -->
              <VCol cols="12">
                <VTextField
                  v-model="form.password"
                  label="Password"
                  placeholder="············"
                  :type="isPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isPasswordVisible ? 'bx-hide' : 'bx-show'"
                  @click:append-inner="isPasswordVisible = !isPasswordVisible"
                />

                <div class="d-flex align-center justify-space-between flex-wrap my-6">
                  <VCheckbox
                    v-model="form.remember"
                    label="Ingat Saya"
                  />
                  <RouterLink
                    class="text-primary"
                    to="/forgot-password"
                  >
                    Lupa Password?
                  </RouterLink>
                </div>

                <!-- Login Button -->
                <VBtn
                  block
                  type="submit"
                  :loading="loading"
                >
                  Masuk
                </VBtn>
              </VCol>

              <!-- Register link -->
              <VCol
                cols="12"
                class="text-body-1 text-center"
              >
                <span class="d-inline-block">Belum punya akun?</span>
                <RouterLink
                  class="text-primary ms-1 d-inline-block text-body-1"
                  to="/register"
                >
                  Daftar sekarang
                </RouterLink>
              </VCol>

              <VCol cols="12" class="d-flex align-center">
                <VDivider />
                <span class="mx-4 text-high-emphasis">atau</span>
                <VDivider />
              </VCol>

              <!-- Back to Homepage -->
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
/* Sneat auth page layout — replaces @core/scss/template/pages/page-auth */
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
</style>
