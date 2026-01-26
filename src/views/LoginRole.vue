<script setup>
import { ref, onBeforeUnmount, onBeforeMount } from "vue";
import { useStore } from "vuex";
import { useRouter } from "vue-router";
import { authAPI } from "@/services/api";
import ArgonInput from "@/components/ArgonInput.vue";
import ArgonSwitch from "@/components/ArgonSwitch.vue";
import ArgonButton from "@/components/ArgonButton.vue";

const body = document.getElementsByTagName("body")[0];
const store = useStore();
const router = useRouter();

const email = ref("");
const password = ref("");
const rememberMe = ref(false);
const loading = ref(false);
const errorMessage = ref("");

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

const handleLogin = async () => {
  errorMessage.value = "";

  if (!email.value || !password.value) {
    errorMessage.value = "Email dan password harus diisi";
    return;
  }

  loading.value = true;

  try {
    const response = await authAPI.login({
      email: email.value,
      password: password.value,
    });

    // Debug: Log the full response
    console.log("Full API Response:", response);
    console.log("Response Data:", response.data);

    let responseData = response.data;

    // If response.data is a string (mixed with HTML warnings), extract JSON
    if (typeof responseData === "string") {
      console.log("Response is string, extracting JSON...");
      // Find the JSON part (starts with { and ends with })
      const jsonMatch = responseData.match(/\{[\s\S]*\}/);
      if (jsonMatch) {
        responseData = JSON.parse(jsonMatch[0]);
        console.log("Extracted JSON:", responseData);
      } else {
        throw new Error("Could not extract JSON from response");
      }
    }

    // Extract user and token from the response
    const user = responseData.user;
    const token = responseData.token;

    // Validate user and token
    if (!user || !token) {
      console.error("Invalid response structure:", responseData);
      throw new Error("Invalid login response from server");
    }

    console.log("User:", user);
    console.log("Token:", token);

    // Save token and user to localStorage
    localStorage.setItem("auth_token", token);
    localStorage.setItem("user", JSON.stringify(user));

    // Redirect based on role
    switch (user.role) {
      case "super_admin":
        router.push("/super-admin/dashboard");
        break;
      case "isp_admin":
        // Check if ISP has active subscription package
        if (user.isp && !user.isp.subscription_package_id) {
          // No package selected, redirect to subscription package selection
          router.push("/client-area/dashboard");
        } else if (user.isp && user.isp.approval_status === 'pending') {
          // Package pending approval, show message and redirect to client area dashboard
          alert("Paket Anda sedang menunggu persetujuan dari Super Admin.");
          router.push("/client-area/dashboard");
        } else if (user.isp && user.isp.subscription_package_id) {
          // Has package, redirect to client area dashboard
          router.push("/client-area/dashboard");
        } else {
          router.push("/isp-admin/dashboard");
        }
        break;
      case "technician":
        router.push("/technician/dashboard");
        break;
      case "customer":
        router.push("/customer/dashboard");
        break;
      default:
        router.push("/dashboard");
    }
  } catch (error) {
    console.error("Login error:", error);
    console.error("Error response:", error.response);

    if (error.response?.data?.errors) {
      errorMessage.value = Object.values(error.response.data.errors)
        .flat()
        .join(", ");
    } else if (error.response?.data?.message) {
      errorMessage.value = error.response.data.message;
    } else if (error.message) {
      errorMessage.value = error.message;
    } else {
      errorMessage.value = "Terjadi kesalahan. Silakan coba lagi.";
    }
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
            <div
              class="mx-auto col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0"
            >
              <div class="card card-plain">
                <div class="pb-0 card-header text-start">
                  <div class="mb-3">
                    <router-link to="/" class="text-dark">
                      <i class="fas fa-arrow-left me-2"></i>
                      <span class="font-weight-bold">Kembali ke Home</span>
                    </router-link>
                  </div>
                  <h4 class="font-weight-bolder">Login</h4>
                  <p class="mb-0">Masukkan email dan password Anda</p>
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

                  <form role="form" @submit.prevent="handleLogin">
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
                    <div class="mb-3">
                      <argon-input
                        id="password"
                        v-model="password"
                        type="password"
                        placeholder="Password"
                        name="password"
                        size="lg"
                        :disabled="loading"
                      >
                        <template v-slot:icon>
                          <i class="fas fa-lock"></i>
                        </template>
                      </argon-input>
                    </div>
                    <argon-switch
                      id="rememberMe"
                      v-model="rememberMe"
                      name="remember-me"
                    >
                      Ingat saya
                    </argon-switch>

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
                          <i class="fas fa-sign-in-alt me-2"></i>
                          Login
                        </span>
                      </argon-button>
                    </div>
                  </form>
                </div>
                <div class="px-1 pt-0 text-center card-footer px-lg-2">
                  <p class="mx-auto mb-2 text-sm">
                    Lupa password?
                    <a
                      href="javascript:;"
                      class="text-success text-gradient font-weight-bold"
                    >
                      Reset Password
                    </a>
                  </p>
                  <p class="mx-auto mb-4 text-sm">
                    Belum punya akun?
                    <router-link
                      to="/register"
                      class="text-success text-gradient font-weight-bold"
                    >
                      Daftar Sekarang
                    </router-link>
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
                <h4
                  class="mt-5 text-white font-weight-bolder position-relative"
                >
                  Selamat Datang Kembali!
                </h4>
                <p class="text-white position-relative">
                  Kelola bisnis ISP Anda dengan mudah menggunakan platform
                  terlengkap di Indonesia.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
</template>
