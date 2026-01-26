import { defineStore } from "pinia";
import axios from "axios";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null,
    token: localStorage.getItem("token") || null,
    isAuthenticated: false,
    loading: false,
    error: null,
  }),

  getters: {
    // Check if user is authenticated
    isLoggedIn: (state) => !!state.token && !!state.user,

    // Get user role
    userRole: (state) => state.user?.role || null,

    // Check if super admin
    isSuperAdmin: (state) => state.user?.role === "super_admin",

    // Check if ISP admin
    isISPAdmin: (state) => state.user?.role === "isp_admin",

    // Check if technician
    isTechnician: (state) => state.user?.role === "technician",

    // Check if customer
    isCustomer: (state) => state.user?.role === "customer",

    // Get user's ISP (if applicable)
    userISP: (state) => state.user?.isp || null,
  },

  actions: {
    /**
     * Initialize auth from local storage
     */
    async init() {
      const token = localStorage.getItem("token");
      const userData = localStorage.getItem("user");

      if (token && userData) {
        this.token = token;
        this.user = JSON.parse(userData);
        this.isAuthenticated = true;

        // Set axios default header
        axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;

        // Verify token is still valid
        try {
          await this.fetchUser();
        } catch (error) {
          this.logout();
        }
      }
    },

    /**
     * Universal login
     */
    async login(credentials) {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.post("/api/auth/login", credentials);

        this.setAuthData(response.data.token, response.data.user);

        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || "Login failed";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    /**
     * Super Admin login
     */
    async loginSuperAdmin(credentials) {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.post(
          "/api/auth/super-admin/login",
          credentials,
        );

        this.setAuthData(response.data.token, response.data.user);

        return response.data;
      } catch (error) {
        this.error =
          error.response?.data?.message || "Super admin login failed";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    /**
     * ISP Admin login
     */
    async loginISPAdmin(credentials) {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.post(
          "/api/auth/isp-admin/login",
          credentials,
        );

        this.setAuthData(response.data.token, response.data.user);

        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || "ISP admin login failed";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    /**
     * Technician login
     */
    async loginTechnician(credentials) {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.post(
          "/api/auth/technician/login",
          credentials,
        );

        this.setAuthData(response.data.token, response.data.user);

        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || "Technician login failed";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    /**
     * Customer login
     */
    async loginCustomer(credentials) {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.post(
          "/api/auth/customer/login",
          credentials,
        );

        this.setAuthData(response.data.token, response.data.user);

        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || "Customer login failed";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    /**
     * Register ISP
     */
    async registerISP(data) {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.post("/api/auth/register/isp", data);

        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || "Registration failed";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    /**
     * Logout
     */
    async logout() {
      try {
        if (this.token) {
          await axios.post("/api/auth/logout");
        }
      } catch (error) {
        console.error("Logout error:", error);
      } finally {
        this.clearAuthData();
      }
    },

    /**
     * Fetch current user data
     */
    async fetchUser() {
      try {
        const response = await axios.get("/api/auth/user");
        this.user = response.data.user;
        localStorage.setItem("user", JSON.stringify(this.user));
        return this.user;
      } catch (error) {
        throw error;
      }
    },

    /**
     * Update profile
     */
    async updateProfile(data) {
      try {
        const response = await axios.put("/api/auth/profile", data, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        });

        this.user = response.data.user;
        localStorage.setItem("user", JSON.stringify(this.user));

        return response.data;
      } catch (error) {
        throw error;
      }
    },

    /**
     * Update password
     */
    async updatePassword(data) {
      try {
        const response = await axios.put("/api/auth/password", data);
        return response.data;
      } catch (error) {
        throw error;
      }
    },

    /**
     * Forgot password
     */
    async forgotPassword(email) {
      try {
        const response = await axios.post("/api/auth/forgot-password", {
          email,
        });
        return response.data;
      } catch (error) {
        throw error;
      }
    },

    /**
     * Reset password
     */
    async resetPassword(data) {
      try {
        const response = await axios.post("/api/auth/reset-password", data);
        return response.data;
      } catch (error) {
        throw error;
      }
    },

    /**
     * Verify email
     */
    async verifyEmail(id, hash) {
      try {
        const response = await axios.post(
          `/api/auth/email/verify/${id}/${hash}`,
        );
        return response.data;
      } catch (error) {
        throw error;
      }
    },

    /**
     * Resend verification email
     */
    async resendVerificationEmail() {
      try {
        const response = await axios.post("/api/auth/email/resend");
        return response.data;
      } catch (error) {
        throw error;
      }
    },

    /**
     * Set authentication data
     */
    setAuthData(token, user) {
      this.token = token;
      this.user = user;
      this.isAuthenticated = true;

      // Store in localStorage
      localStorage.setItem("token", token);
      localStorage.setItem("user", JSON.stringify(user));

      // Set axios default header
      axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
    },

    /**
     * Clear authentication data
     */
    clearAuthData() {
      this.token = null;
      this.user = null;
      this.isAuthenticated = false;

      // Remove from localStorage
      localStorage.removeItem("token");
      localStorage.removeItem("user");

      // Remove axios default header
      delete axios.defaults.headers.common["Authorization"];
    },

    /**
     * Check if user has permission
     */
    hasPermission(permission) {
      if (this.isSuperAdmin) return true;

      if (this.isISPAdmin && this.user.permissions) {
        return this.user.permissions.includes(permission);
      }

      return false;
    },

    /**
     * Get role-based dashboard route
     */
    getDashboardRoute() {
      const routes = {
        super_admin: "/super-admin/dashboard",
        isp_admin: "/isp-admin/dashboard",
        technician: "/technician/dashboard",
        customer: "/customer/dashboard",
      };

      return routes[this.userRole] || "/login";
    },
  },
});
