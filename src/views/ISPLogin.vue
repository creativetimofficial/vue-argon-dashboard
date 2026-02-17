<template>
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner">
      <!-- Login Card -->
      <div class="card px-sm-6 px-0">
        <div class="card-body">
          <!-- Logo -->
          <div class="app-brand justify-content-center mb-4">
            <span class="app-brand-text demo text-heading fw-bold text-center" style="font-size: 1.75rem;">{{ ispName || 'Login' }}</span>
          </div>
          <!-- /Logo -->

          <h4 class="mb-2 text-center">Selamat Datang! 👋</h4>
          <p class="mb-4 text-center text-muted">Silakan login ke akun Anda</p>

          <!-- Error Alert -->
          <div v-if="errorMessage" class="alert alert-danger alert-dismissible mb-4" role="alert">
            {{ errorMessage }}
            <button type="button" class="btn-close" @click="errorMessage = ''"></button>
          </div>

          <!-- Login Form -->
          <form id="formAuthentication" class="mb-3" @submit.prevent="handleLogin">
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
            <div class="mb-3 form-password-toggle">
              <label class="form-label" for="password">Password</label>
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
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center">
                <div class="form-check mb-0">
                  <input class="form-check-input" type="checkbox" id="remember-me" v-model="rememberMe" />
                  <label class="form-check-label" for="remember-me"> Ingat Saya </label>
                </div>
                <router-link to="/forgot-password" class="small">
                  <p class="mb-0">Lupa Password?</p>
                </router-link>
              </div>
            </div>
            <div class="mb-3">
              <button class="btn btn-primary d-grid w-100" type="submit" :disabled="loading">
                <span v-if="loading">
                  <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                  Loading...
                </span>
                <span v-else>Login</span>
              </button>
            </div>
          </form>

          <p class="text-center">
            <span>Belum punya akun? </span>
            <router-link to="/register">
              <span>Daftar sekarang</span>
            </router-link>
          </p>
          
          <div class="text-center mt-3">
             <router-link to="/">
                <small>Kembali ke Beranda</small>
             </router-link>
          </div>
        </div>
      </div>
      <!-- /Login Card -->
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
// Note: We use the general authAPI, not the ISPAdmin store, because this login is for ALL roles (Customers, etc.)
import { authAPI } from "@/services/api";
import { detectTenant, getTenantInfo } from '@/utils/tenant';

const router = useRouter();

const email = ref("");
const password = ref("");
const rememberMe = ref(false);
const showPassword = ref(false);
const loading = ref(false);
const errorMessage = ref("");
const ispName = ref("");

onMounted(async () => {
  // Logic to get ISP Name specific to this view
  const tenant = detectTenant();
  if (tenant) {
      // Try local storage first
      const info = getTenantInfo();
      if (info && info.name) {
          ispName.value = info.name;
      } else {
          // Fetch if needed, though usually LandingPage has already set it or we can fallback
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

  // Pre-fill email if remembered
  const savedEmail = localStorage.getItem("remembered_email");
  if (savedEmail) {
    email.value = savedEmail;
    rememberMe.value = true;
  }
});

const handleLogin = async () => {
    errorMessage.value = "";
    loading.value = true;

    try {
        const response = await authAPI.login({
            email: email.value,
            password: password.value,
        });

        // Parse response similar to LoginRole.vue
        let responseData = response.data;
        if (typeof responseData === "string") {
            const jsonMatch = responseData.match(/\{[\s\S]*\}/);
            if (jsonMatch) {
                responseData = JSON.parse(jsonMatch[0]);
            } else {
                throw new Error("Invalid server response");
            }
        }

        const user = responseData.user;
        const token = responseData.token;

        if (!user || !token) throw new Error("Login failed: missing credentials");

        // Save tokens
        if (rememberMe.value) {
            localStorage.setItem("auth_token", token);
            localStorage.setItem("user", JSON.stringify(user));
            localStorage.setItem("remembered_email", email.value);
        } else {
            sessionStorage.setItem("auth_token", token);
            sessionStorage.setItem("user", JSON.stringify(user));
            localStorage.removeItem("remembered_email");
            localStorage.removeItem("auth_token");
        }

        // Redirect logic
        switch (user.role) {
            case "super_admin":
                router.push("/super-admin/dashboard");
                break;
            case "isp_admin":
                 if (user.isp && !user.isp.subscription_package_id) {
                    router.push("/client-area/dashboard");
                 } else {
                    // Check approval?
                     router.push("/isp-admin/dashboard");
                 }
                break;
            case "technician":
                router.push("/technician/dashboard");
                break;
            case "customer":
                router.push("/client-area/dashboard"); // Assuming customer dashboard is here or /customer/dashboard
                break;
            default:
                router.push("/client-area/dashboard");
        }

    } catch (error) {
        console.error("Login error:", error);
         if (error.response?.data?.message) {
            errorMessage.value = error.response.data.message;
        } else {
            errorMessage.value = "Login gagal. Periksa email dan password Anda.";
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
.cursor-pointer {
  cursor: pointer;
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
.form-check-input:checked {
  background-color: #696cff !important;
  border-color: #696cff !important;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M6 10l3 3l6-6'/%3e%3c/svg%3e") !important;
  background-repeat: no-repeat !important;
  background-position: center center !important;
  background-size: 1.1em !important;
  appearance: none !important;
}
.form-check-input:checked::after,
.form-check-input:checked::before {
  content: "" !important;
  display: none !important;
  background: none !important;
}
.form-check-input:focus {
  border-color: #696cff;
  box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.25);
}
.form-check-input {
  width: 1.2em;
  height: 1.2em;
  margin-top: 0.15em;
  appearance: none !important;
}
</style>
