import { createRouter, createWebHistory } from "vue-router";

// Import existing views
import Dashboard from "../views/Dashboard.vue";
import Profile from "../views/Profile.vue";
import Billing from "../views/Billing.vue";
import Tables from "../views/Tables.vue";

// Import new views
import LandingPage from "../views/LandingPage.vue";
import RegisterPage from "../views/RegisterPage.vue";
import LoginRole from "../views/LoginRole.vue";
import SuperAdminDashboard from "../views/SuperAdminDashboard.vue";
import VerificationSuccess from "../views/VerificationSuccess.vue";

// Import Super Admin views
import PaymentGateways from "../views/super-admin/PaymentGateways.vue";
import LandingPageEditor from "../views/super-admin/LandingPageEditor.vue";
import ISPThemeEditor from "../views/super-admin/ISPThemeEditor.vue";
import SubscriptionPackages from "../views/super-admin/SubscriptionPackages.vue";
import ISPManagement from "../views/super-admin/ISPManagement.vue";

const routes = [
  // ===== PUBLIC ROUTES =====
  {
    path: "/",
    name: "Home",
    component: LandingPage,
    meta: {
      guest: true,
      title: "ISP Billing System - Platform Manajemen ISP Terlengkap",
    },
  },
  {
    path: "/register",
    name: "Register",
    component: RegisterPage,
    meta: { guest: true, title: "Registrasi - ISP Billing System" },
  },
  {
    path: "/login",
    name: "Login",
    component: LoginRole,
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
    path: "/client-area-package",
    name: "ClientAreaPackage",
    component: () => import("../views/ClientArea.vue"),
    meta: { requiresAuth: true, role: "isp_admin", title: "Client Area - Pilih Paket" },
  },
  {
    path: "/client-area",
    redirect: "/client-area/dashboard",
    component: () => import("../views/client-area/LayoutWrapper.vue"),
    meta: { requiresAuth: true, role: "isp_admin" },
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
        path: "isp-theme-editor",
        name: "ISPThemeEditor",
        component: ISPThemeEditor,
        meta: { title: "ISP Theme Customizer" },
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
        path: "profile",
        name: "SuperAdminProfile",
        component: Profile,
        meta: { title: "Profile" },
      },
    ],
  },

  // ===== ISP ADMIN ROUTES =====
  {
    path: "/isp-admin",
    redirect: "/isp-admin/dashboard",
    meta: { requiresAuth: true, role: "isp_admin" },
    children: [
      {
        path: "dashboard",
        name: "ISPAdminDashboard",
        component: Dashboard,
        meta: { title: "ISP Admin Dashboard" },
      },
      {
        path: "customers",
        name: "CustomerManagement",
        component: Tables,
        meta: { title: "Customer Management" },
      },
      {
        path: "billing",
        name: "ISPAdminBilling",
        component: Billing,
        meta: { title: "Billing Management" },
      },
      {
        path: "profile",
        name: "ISPAdminProfile",
        component: Profile,
        meta: { title: "Profile" },
      },
    ],
  },

  // ===== TECHNICIAN ROUTES =====
  {
    path: "/technician",
    redirect: "/technician/dashboard",
    meta: { requiresAuth: true, role: "technician" },
    children: [
      {
        path: "dashboard",
        name: "TechnicianDashboard",
        component: Dashboard,
        meta: { title: "Technician Dashboard" },
      },
      {
        path: "profile",
        name: "TechnicianProfile",
        component: Profile,
        meta: { title: "Profile" },
      },
    ],
  },

  // ===== CUSTOMER ROUTES =====
  {
    path: "/customer",
    redirect: "/customer/dashboard",
    meta: { requiresAuth: true, role: "customer" },
    children: [
      {
        path: "dashboard",
        name: "CustomerDashboard",
        component: Dashboard,
        meta: { title: "Customer Dashboard" },
      },
      {
        path: "billing",
        name: "CustomerBilling",
        component: Billing,
        meta: { title: "My Billing" },
      },
      {
        path: "profile",
        name: "CustomerProfile",
        component: Profile,
        meta: { title: "My Profile" },
      },
    ],
  },

  // ===== COMMON ROUTES =====
  {
    path: "/profile",
    name: "Profile",
    component: Profile,
    meta: { requiresAuth: true, title: "Profile" },
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

  // For now, just allow all navigation
  // TODO: Implement authentication check with localStorage or session
  next();
});

export default router;
