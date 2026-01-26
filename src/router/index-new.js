import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/store/auth";

// ===== AUTH VIEWS =====
import Login from "../views/Auth/Login.vue";
import Register from "../views/Auth/Register.vue";
import EmailVerification from "../views/Auth/EmailVerification.vue";

// ===== SUPER ADMIN VIEWS =====
import SuperAdminDashboard from "../views/SuperAdmin/Dashboard.vue";
import ISPManagement from "../views/SuperAdmin/ISPManagement.vue";
import ISPDetail from "../views/SuperAdmin/ISPDetail.vue";
import PackageManagement from "../views/SuperAdmin/PackageManagement.vue";
import PaymentGatewayConfig from "../views/SuperAdmin/PaymentGatewayConfig.vue";
import WebCustomization from "../views/SuperAdmin/WebCustomization.vue";
import SuperAdminReports from "../views/SuperAdmin/Reports.vue";
import SuperAdminSettings from "../views/SuperAdmin/Settings.vue";

// ===== ISP ADMIN VIEWS =====
import ISPAdminDashboard from "../views/ISPAdmin/Dashboard.vue";
import CustomerManagement from "../views/ISPAdmin/CustomerManagement.vue";
import CustomerDetail from "../views/ISPAdmin/CustomerDetail.vue";
import CustomerPackages from "../views/ISPAdmin/CustomerPackages.vue";
import MikrotikManagement from "../views/ISPAdmin/MikrotikManagement.vue";
import TechnicianManagement from "../views/ISPAdmin/TechnicianManagement.vue";
import InstallationRequests from "../views/ISPAdmin/InstallationRequests.vue";
import RepairTickets from "../views/ISPAdmin/RepairTickets.vue";
import PaymentApproval from "../views/ISPAdmin/PaymentApproval.vue";
import ISPAdminInvoices from "../views/ISPAdmin/Invoices.vue";
import ISPAdminReports from "../views/ISPAdmin/Reports.vue";
import ISPAdminSettings from "../views/ISPAdmin/Settings.vue";

// ===== TECHNICIAN VIEWS =====
import TechnicianDashboard from "../views/Technician/Dashboard.vue";
import TechnicianInstallations from "../views/Technician/Installations.vue";
import TechnicianRepairs from "../views/Technician/Repairs.vue";
import TechnicianProfile from "../views/Technician/Profile.vue";

// ===== CUSTOMER VIEWS =====
import CustomerDashboard from "../views/Customer/Dashboard.vue";
import CustomerBilling from "../views/Customer/Billing.vue";
import CustomerPayment from "../views/Customer/Payment.vue";
import CustomerInvoices from "../views/Customer/Invoices.vue";
import CustomerComplaints from "../views/Customer/Complaints.vue";
import CustomerProfile from "../views/Customer/Profile.vue";

// ===== COMMON VIEWS =====
import Profile from "../views/Profile.vue";
import NotFound from "../views/NotFound.vue";
import Unauthorized from "../views/Unauthorized.vue";

const routes = [
  // ===== PUBLIC ROUTES =====
  {
    path: "/",
    name: "Home",
    redirect: "/login",
  },
  {
    path: "/login",
    name: "Login",
    component: Login,
    meta: { guest: true },
  },
  {
    path: "/register",
    name: "Register",
    component: Register,
    meta: { guest: true },
  },
  {
    path: "/email-verification",
    name: "EmailVerification",
    component: EmailVerification,
    meta: { guest: true },
  },

  // ===== SUPER ADMIN ROUTES =====
  {
    path: "/super-admin",
    name: "SuperAdmin",
    redirect: "/super-admin/dashboard",
    meta: { requiresAuth: true, role: "super_admin" },
    children: [
      {
        path: "dashboard",
        name: "SuperAdminDashboard",
        component: SuperAdminDashboard,
        meta: { title: "Dashboard" },
      },
      {
        path: "isps",
        name: "ISPManagement",
        component: ISPManagement,
        meta: { title: "ISP Management" },
      },
      {
        path: "isps/:id",
        name: "ISPDetail",
        component: ISPDetail,
        meta: { title: "ISP Detail" },
      },
      {
        path: "packages",
        name: "PackageManagement",
        component: PackageManagement,
        meta: { title: "Package Management" },
      },
      {
        path: "payment-gateways",
        name: "PaymentGatewayConfig",
        component: PaymentGatewayConfig,
        meta: { title: "Payment Gateway Configuration" },
      },
      {
        path: "customization",
        name: "WebCustomization",
        component: WebCustomization,
        meta: { title: "Web Customization" },
      },
      {
        path: "reports",
        name: "SuperAdminReports",
        component: SuperAdminReports,
        meta: { title: "Reports & Analytics" },
      },
      {
        path: "settings",
        name: "SuperAdminSettings",
        component: SuperAdminSettings,
        meta: { title: "System Settings" },
      },
    ],
  },

  // ===== ISP ADMIN ROUTES =====
  {
    path: "/isp-admin",
    name: "ISPAdmin",
    redirect: "/isp-admin/dashboard",
    meta: { requiresAuth: true, role: "isp_admin" },
    children: [
      {
        path: "dashboard",
        name: "ISPAdminDashboard",
        component: ISPAdminDashboard,
        meta: { title: "Dashboard" },
      },
      {
        path: "customers",
        name: "CustomerManagement",
        component: CustomerManagement,
        meta: { title: "Customer Management" },
      },
      {
        path: "customers/:id",
        name: "CustomerDetail",
        component: CustomerDetail,
        meta: { title: "Customer Detail" },
      },
      {
        path: "packages",
        name: "CustomerPackages",
        component: CustomerPackages,
        meta: { title: "Internet Packages" },
      },
      {
        path: "mikrotik",
        name: "MikrotikManagement",
        component: MikrotikManagement,
        meta: { title: "Mikrotik Management" },
      },
      {
        path: "technicians",
        name: "TechnicianManagement",
        component: TechnicianManagement,
        meta: { title: "Technician Management" },
      },
      {
        path: "installations",
        name: "InstallationRequests",
        component: InstallationRequests,
        meta: { title: "Installation Requests" },
      },
      {
        path: "repairs",
        name: "RepairTickets",
        component: RepairTickets,
        meta: { title: "Repair Tickets" },
      },
      {
        path: "payment-approval",
        name: "PaymentApproval",
        component: PaymentApproval,
        meta: { title: "Payment Approval" },
      },
      {
        path: "invoices",
        name: "ISPAdminInvoices",
        component: ISPAdminInvoices,
        meta: { title: "Invoices" },
      },
      {
        path: "reports",
        name: "ISPAdminReports",
        component: ISPAdminReports,
        meta: { title: "Reports" },
      },
      {
        path: "settings",
        name: "ISPAdminSettings",
        component: ISPAdminSettings,
        meta: { title: "Settings" },
      },
    ],
  },

  // ===== TECHNICIAN ROUTES =====
  {
    path: "/technician",
    name: "Technician",
    redirect: "/technician/dashboard",
    meta: { requiresAuth: true, role: "technician" },
    children: [
      {
        path: "dashboard",
        name: "TechnicianDashboard",
        component: TechnicianDashboard,
        meta: { title: "Dashboard" },
      },
      {
        path: "installations",
        name: "TechnicianInstallations",
        component: TechnicianInstallations,
        meta: { title: "Installation Requests" },
      },
      {
        path: "repairs",
        name: "TechnicianRepairs",
        component: TechnicianRepairs,
        meta: { title: "Repair Tickets" },
      },
      {
        path: "profile",
        name: "TechnicianProfile",
        component: TechnicianProfile,
        meta: { title: "My Profile" },
      },
    ],
  },

  // ===== CUSTOMER ROUTES =====
  {
    path: "/customer",
    name: "Customer",
    redirect: "/customer/dashboard",
    meta: { requiresAuth: true, role: "customer" },
    children: [
      {
        path: "dashboard",
        name: "CustomerDashboard",
        component: CustomerDashboard,
        meta: { title: "Dashboard" },
      },
      {
        path: "billing",
        name: "CustomerBilling",
        component: CustomerBilling,
        meta: { title: "Billing" },
      },
      {
        path: "payment",
        name: "CustomerPayment",
        component: CustomerPayment,
        meta: { title: "Make Payment" },
      },
      {
        path: "invoices",
        name: "CustomerInvoices",
        component: CustomerInvoices,
        meta: { title: "Invoice History" },
      },
      {
        path: "complaints",
        name: "CustomerComplaints",
        component: CustomerComplaints,
        meta: { title: "Complaints" },
      },
      {
        path: "profile",
        name: "CustomerProfile",
        component: CustomerProfile,
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

  // ===== ERROR ROUTES =====
  {
    path: "/unauthorized",
    name: "Unauthorized",
    component: Unauthorized,
  },
  {
    path: "/:pathMatch(.*)*",
    name: "NotFound",
    component: NotFound,
  },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
  linkActiveClass: "active",
});

// Navigation Guards
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  const isAuthenticated = authStore.isAuthenticated;
  const userRole = authStore.user?.role;

  // Set page title
  if (to.meta.title) {
    document.title = `${to.meta.title} - ISP Billing System`;
  } else {
    document.title = "ISP Billing System";
  }

  // Guest routes (login, register, etc)
  if (to.meta.guest) {
    if (isAuthenticated) {
      // Redirect to dashboard based on role
      return next(getRoleDashboard(userRole));
    }
    return next();
  }

  // Routes that require authentication
  if (to.meta.requiresAuth) {
    if (!isAuthenticated) {
      return next({ name: "Login", query: { redirect: to.fullPath } });
    }

    // Check role-based access
    if (to.meta.role && to.meta.role !== userRole) {
      return next({ name: "Unauthorized" });
    }

    return next();
  }

  next();
});

// Helper function to get role-based dashboard
function getRoleDashboard(role) {
  const dashboards = {
    super_admin: "/super-admin/dashboard",
    isp_admin: "/isp-admin/dashboard",
    technician: "/technician/dashboard",
    customer: "/customer/dashboard",
  };

  return dashboards[role] || "/login";
}

export default router;
export { getRoleDashboard };
