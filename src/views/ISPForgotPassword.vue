<template>
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner">
      <!-- Forgot Password Card -->
      <div class="card px-sm-6 px-0">
        <div class="card-body">
          <!-- Logo -->
          <div class="app-brand justify-content-center mb-6">
            <span class="app-brand-text demo text-heading fw-bold text-center" style="font-size: 1.75rem;">{{ ispName || 'Forgot Password' }}</span>
          </div>
          <!-- /Logo -->

          <h4 class="mb-2 text-center">Lupa Password? 🔒</h4>
          <p class="mb-4 text-center text-muted">Masukkan email Anda dan kami akan mengirimkan instruksi untuk reset password</p>

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
            <button class="btn btn-primary d-grid w-100" type="submit" :disabled="loading">
              <span v-if="loading">
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Mengirim...
              </span>
              <span v-else>Kirim Link Reset</span>
            </button>
          </form>

          <div class="text-center">
            <router-link to="/login" class="d-flex align-items-center justify-content-center">
              <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i>
              Kembali ke Login
            </router-link>
          </div>
        </div>
      </div>
      <!-- /Forgot Password Card -->
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { authAPI } from "@/services/api";
import { detectTenant, getTenantInfo } from '@/utils/tenant';

const email = ref("");
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
    loading.value = true;
    error.value = null;
    success.value = null;

    try {
        await authAPI.forgotPassword(email.value);
        success.value = "Link reset password telah dikirim ke email Anda.";
        email.value = ""; // Clear email after success
    } catch (err) {
        if (err.response && err.response.data && err.response.data.message) {
            error.value = err.response.data.message;
        } else {
            error.value = "Gagal mengirim link reset. Silakan coba lagi.";
        }
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
.authentication-inner {
    max-width: 400px;
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
