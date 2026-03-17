import { createRouter, createWebHistory } from "vue-router";
import { h } from "vue";

// Import new views
import LandingPage from "../views/LandingPage.vue";
import RegisterPage from "../views/RegisterPage.vue";
import LoginRole from "../views/LoginRole.vue";
import ISPLogin from "../views/ISPLogin.vue";
import ISPRegister from "../views/ISPRegister.vue";
import SuperAdminDashboard from "../views/SuperAdminDashboard.vue";
import VerificationSuccess from "../views/VerificationSuccess.vue";
import ISPLandingPage from "../views/ISPLandingPage.vue";
import ISPResetPassword from "../views/ISPResetPassword.vue";
import { detectTenant } from "@/utils/tenant";

// Import Super Admin views
import PaymentGateways from "../views/super-admin/PaymentGateways.vue";
import LandingPageEditor from "../views/super-admin/LandingPageEditor.vue";
import SubscriptionPackages from "../views/super-admin/SubscriptionPackages.vue";
import ISPManagement from "../views/super-admin/ISPManagement.vue";
import SuperAdminProfile from "../views/super-admin/Profile.vue";

// Helper component to switch between main landing and ISP landing
const HomeSwitch = {
  name: "HomeSwitch",
  render() {
    const tenant = detectTenant();
    return tenant ? h(ISPLandingPage) : h(LandingPage);
  }
};

const routes = [
  // ===== PUBLIC ROUTES =====
  {
    path: "/",
    name: "Home",
    component: HomeSwitch,
    meta: {
      guest: true,
      title: "Payneto - Platform Manajemen ISP Terlengkap",
    },
  },
  {
    path: "/register",
    name: "Register",
    component: RegisterPage,
    meta: { guest: true, title: "Registrasi - Payneto" },
  },

  {
    path: "/login",
    name: "Login",
    component: LoginRole,
    meta: { guest: true, title: "Login - Payneto" },
  },
  {
    path: "/verify-email/:token",
    name: "VerifyEmail",
    component: VerificationSuccess,
    meta: { guest: true, title: "Verifikasi Email" },
  },
  {
    path: "/verify-success",
    name: "VerificationSuccess",
    component: VerificationSuccess,
    meta: { guest: true, title: "Verifikasi Berhasil" },
  },
  {
    path: "/magic-login",
    name: "MagicLogin",
    component: () => import("../views/MagicLogin.vue"),
    meta: { guest: true, title: "Magic Login" },
  },
  {
    path: "/client-area",
    redirect: "/client-area/dashboard",
    component: () => import("../views/client-area/LayoutWrapper.vue"),
    meta: { requiresAuth: true, role: "client" },
    children: [
      {
        path: "dashboard",
        name: "ClientAreaDashboard",
        component: () => import("../views/client-area/Dashboard.vue"),
        meta: { title: "Dashboard" },
      },
      {
        path: "orders",
        name: "ClientAreaOrders",
        component: () => import("../views/client-area/Orders.vue"),
        meta: { title: "Orders" },
      },
      {
        path: "services",
        name: "ClientAreaServices",
        component: () => import("../views/client-area/Services.vue"),
        meta: { title: "Services" },
      },
      {
        path: "services/:id",
        name: "ClientAreaServiceDetail",
        component: () => import("../views/client-area/ServiceDetail.vue"),
        meta: { title: "Detail Service" },
      },
      {
        path: "invoices",
        name: "ClientAreaInvoices",
        component: () => import("../views/client-area/Invoices.vue"),
        meta: { title: "Invoices" },
      },
      {
        path: "profile",
        name: "ClientAreaProfile",
        component: () => import("../views/client-area/Profile.vue"),
        meta: { title: "Profile" },
      },
      {
        path: "topup",
        name: "ClientAreaTopup",
        component: () => import("../views/client-area/Topup.vue"),
        meta: { title: "Topup Balance" },
      },
    ],
  },
  {
    path: "/payment-callback",
    name: "PaymentCallback",
    component: () => import("../views/PaymentCallback.vue"),
    meta: { guest: true, title: "Payment Callback" },
  },

  // ===== SUPER ADMIN ROUTES =====
  {
    path: "/super-admin",
    redirect: "/super-admin/dashboard",
    meta: { requiresAuth: true, role: "super_admin" },
    children: [
      {
        path: "dashboard",
        name: "SuperAdminDashboard",
        component: SuperAdminDashboard,
        meta: { title: "Super Admin Dashboard" },
      },
      {
        path: "payment-gateways",
        name: "PaymentGateways",
        component: PaymentGateways,
        meta: { title: "Payment Gateways Management" },
      },
      {
        path: "landing-page-editor",
        name: "LandingPageEditor",
        component: LandingPageEditor,
        meta: { title: "Landing Page Editor" },
      },
      {
        path: "subscription-packages",
        name: "SubscriptionPackages",
        component: SubscriptionPackages,
        meta: { title: "Subscription Packages" },
      },
      {
        path: "isp-management",
        name: "ISPManagement",
        component: ISPManagement,
        meta: { title: "ISP Management" },
      },
      {
        path: "isp-orders",
        name: "ISPOrders",
        component: () => import("../views/super-admin/ISPOrders.vue"),
        meta: { title: "ISP Orders Management" },
      },
      {
        path: "servers",
        name: "ServerManager",
        component: () => import("../views/super-admin/ServerManager.vue"),
        meta: { title: "Server Management" },
      },
      {
        path: "withdrawals",
        name: "Withdrawals",
        component: () => import("../views/super-admin/Withdrawals.vue"),
        meta: { title: "Withdrawal Requests" },
      },
      {
        path: "profile",
        name: "SuperAdminProfile",
        component: SuperAdminProfile,
        meta: { title: "Profile Settings" },
      },
      {
        path: "system-settings",
        name: "SystemSettings",
        component: () => import("../views/super-admin/SystemSettings.vue"),
        meta: { title: "System Settings" },
      },
    ],
  },

  // ===== ISP ADMIN ROUTES =====
  // ISP Admin now uses Client Area routes (/client-area/*)
  // Old routes removed - components (Dashboard, Tables, Billing, Profile) have been deleted


  // ===== TECHNICIAN ROUTES =====
  // Removed: Technician routes - not implemented yet
  // Components (Dashboard, Profile) have been deleted

  // ===== CUSTOMER ROUTES =====
  // Removed: Customer routes - not implemented yet
  // Components (Dashboard, Billing, Profile) have been deleted


  // ===== COMMON ROUTES =====
  // Removed: Common Profile route - Profile component has been deleted

  {
    path: "/forgot-password",
    name: "ForgotPassword",
    component: ISPResetPassword,
    meta: { guest: true, title: "Lupa Password" },
  },

  {
    path: "/:pathMatch(.*)*",
    name: "NotFound",
    redirect: "/login",
  },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
  linkActiveClass: "active",
});

// Navigation Guards
router.beforeEach((to, from, next) => {
  // Set page title
  document.title = to.meta.title || "Payneto";

  // Get authentication tokens
  const ispAdminToken = localStorage.getItem("isp_admin_token");
  const clientToken = localStorage.getItem("auth_token") || sessionStorage.getItem("auth_token"); // Client Area token (matches LoginRole.vue)

  // ISP Admin Routes Protection
  if (to.meta.requiresAuth && to.meta.role === "isp_admin") {
    if (!ispAdminToken) {
      // No ISP Admin token, redirect to generic login
      next("/login");
      return;
    }
    // Has ISP Admin token, allow access
    next();
    return;
  }



  // Client Area Routes Protection
  if (to.path.startsWith('/client-area') && to.path !== '/client-area-package') {
    if (!clientToken) {
      // No client token, redirect to main login
      next("/login");
      return;
    }
    // Has client token, allow access
    next();
    return;
  }

  // Super Admin Routes Protection (if any)
  if (to.path.startsWith('/super-admin')) {
    // Check for super admin token/role
    // TODO: Implement super admin authentication check
    next();
    return;
  }

  // Allow all other routes
  next();
});

export default router;
