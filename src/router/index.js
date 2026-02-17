import { createRouter, createWebHistory } from "vue-router";

// Import new views
import LandingPage from "../views/LandingPage.vue";
import RegisterPage from "../views/RegisterPage.vue";
import LoginRole from "../views/LoginRole.vue";
import ISPLogin from "../views/ISPLogin.vue";
import ISPRegister from "../views/ISPRegister.vue";
import SuperAdminDashboard from "../views/SuperAdminDashboard.vue";
import VerificationSuccess from "../views/VerificationSuccess.vue";
import ISPLandingPage from "../views/ISPLandingPage.vue";
import { detectTenant } from "@/utils/tenant";

// Import Super Admin views
import PaymentGateways from "../views/super-admin/PaymentGateways.vue";
import LandingPageEditor from "../views/super-admin/LandingPageEditor.vue";
import SubscriptionPackages from "../views/super-admin/SubscriptionPackages.vue";
import ISPManagement from "../views/super-admin/ISPManagement.vue";
import SuperAdminProfile from "../views/super-admin/Profile.vue";

const routes = [
  // ===== PUBLIC ROUTES =====
  {
    path: "/",
    name: "Home",
    component: () => {
      const tenant = detectTenant();
      if (tenant) {
        return ISPLandingPage;
      }
      return LandingPage;
    },
    meta: {
      guest: true,
      title: "ISP Billing System - Platform Manajemen ISP Terlengkap",
    },
  },
  {
    path: "/register",
    name: "Register",
    component: () => {
      const tenant = detectTenant();
      if (tenant) {
        return ISPRegister;
      }
      return RegisterPage;
    },
    meta: { guest: true, title: "Registrasi - ISP Billing System" },
  },
  {
    path: "/reset-password",
    name: "ResetPassword",
    component: () => import("../views/ISPResetPassword.vue"),
    meta: { guest: true, title: "Reset Password" },
  },
  {
    path: "/login",
    name: "Login",
    component: () => {
       const tenant = detectTenant();
      if (tenant) {
        return ISPLogin;
      }
      return LoginRole;
    },
    meta: { guest: true, title: "Login - ISP Billing System" },
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
    path: "/client-area-package",
    name: "ClientAreaPackage",
    component: () => import("../views/ClientArea.vue"),
    meta: { requiresAuth: true, role: "isp_admin", title: "Client Area - Pilih Paket" },
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
    component: () => import("../views/ISPForgotPassword.vue"),
    meta: { guest: true, title: "Lupa Password" },
  },
  {
    path: "/isp-admin",
    component: () => import("../layouts/ISPAdminLayout.vue"),
    meta: { requiresAuth: true, role: "isp_admin" },
    children: [
      {
        path: "",
        redirect: "/isp-admin/dashboard",
      },
      {
        path: "dashboard",
        name: "ISPAdminDashboard",
        component: () => import("../views/ISPAdmin/Dashboard.vue"),
        meta: { title: "Dashboard - ISP Admin" },
      },
      {
        path: "customers",
        name: "ISPAdminCustomers",
        component: () => import("../views/ISPAdmin/CustomerManagement.vue"),
        meta: { title: "Managemen Pelanggan - ISP Admin" },
      },
      {
        path: "packages",
        name: "ISPAdminPackages",
        component: () => import("../views/ISPAdmin/Packages.vue"),
        meta: { title: "Paket Internet - ISP Admin" },
      },
      {
        path: "mikrotik",
        name: "ISPAdminMikrotik",
        component: () => import("../views/ISPAdmin/Mikrotik.vue"),
        meta: { title: "Managemen Mikrotik - ISP Admin" },
      },
      {
        path: "tickets",
        name: "ISPAdminTickets",
        component: () => import("../views/ISPAdmin/Tickets.vue"),
        meta: { title: "Tiket Support - ISP Admin" },
      },
      {
        path: "invoices",
        name: "ISPAdminInvoices",
        component: () => import("../views/ISPAdmin/Invoices.vue"),
        meta: { title: "Invoice - ISP Admin" },
      },
      {
        path: "reports",
        name: "ISPAdminReports",
        component: () => import("../views/ISPAdmin/Reports.vue"),
        meta: { title: "Laporan - ISP Admin" },
      },
      {
        path: "settings",
        name: "ISPAdminSettings",
        component: () => import("../views/ISPAdmin/Settings.vue"),
        meta: { title: "Pengaturan - ISP Admin" },
      },
    ],
  },

  {
    path: "/:pathMatch(.*)*",
    name: "NotFound",
    redirect: "/login",
  },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
  linkActiveClass: "active",
});

// Navigation Guards
router.beforeEach((to, from, next) => {
  // Set page title
  document.title = to.meta.title || "ISP Billing System";

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
