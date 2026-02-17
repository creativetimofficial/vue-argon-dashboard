<template>
  <div class="container position-sticky z-index-sticky top-0">
    <div class="row">
      <div class="col-12">
        <!-- Navbar Placeholder -->
      </div>
    </div>
  </div>
  <main class="main-content mt-0">
    <section>
      <div class="page-header min-vh-100">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto">
              <div class="card card-plain">
                <div class="card-header pb-0 text-start">
                  <h4 class="font-weight-bolder">Magic Login</h4>
                  <p class="mb-0">Please wait while we log you in...</p>
                </div>
                <div class="card-body text-center">
                  <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                  <p class="mt-3 text-sm">{{ statusMessage }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
</template>

<script>
import { authAPI } from "@/services/api";

export default {
  name: "MagicLogin",
  data() {
    return {
      statusMessage: "Authenticating...",
    };
  },
  mounted() {
    this.processLogin();
  },
  methods: {
    async processLogin() {
      const urlParams = new URLSearchParams(window.location.search);
      const token = urlParams.get('token');
      const redirectPath = urlParams.get('redirect') || '/client-area/dashboard';

      if (token) {
        try {
          // 1. Store the token first so axios interceptor uses it
          localStorage.setItem('auth_token', token);
          
          this.statusMessage = "Verifying session...";

          // 2. Fetch User Details
          const response = await authAPI.getUser();
          const user = response.data.user;

          if (user) {
             // 3. Store user details
             localStorage.setItem('user', JSON.stringify(user));
             
             this.statusMessage = "Login successful! Redirecting...";
             
             setTimeout(() => {
               this.$router.push(redirectPath);
             }, 500);
          } else {
             throw new Error("User data not found");
          }

        } catch (error) {
          console.error("Magic Login Error:", error);
          this.statusMessage = "Login failed: " + (error.response?.data?.message || error.message);
          
          // Do not redirect automatically on error, let user see the message
          // localStorage.removeItem('auth_token'); // Keep token for debugging? Or remove? Remove to be safe.
          localStorage.removeItem('auth_token');
        }
      } else {
        this.statusMessage = "Invalid login link. Please try again.";
        setTimeout(() => {
          this.$router.push('/login');
        }, 2000);
      }
    },
  },
};
</script>
