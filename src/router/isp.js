import { createRouter, createWebHistory } from 'vue-router';

// Layouts
// ISPLayout: self-contained Vuetify layout (VNavigationDrawer + VAppBar + VMain)
// Replaces Sneat's DefaultLayout which required broken @/layouts/components/ imports
import DefaultLayout from '@/layouts/ISPLayout.vue';
import BlankLayout from '../../sneat-vuetify-vuejs-admin-template-free/javascript-version/src/layouts/blank.vue';

const routes = [
  // ===== PUBLIC LANDING PAGE (accessible to all) =====
  {
    path: '/',
    component: BlankLayout,
    children: [{
      path: '',
      name: 'ISPLanding',
      component: () => import('../views/ISPLandingPage.vue'),
      meta: { title: 'Selamat Datang' },
    }]
  },

  // ===== PUBLIC AUTH ROUTES (No sidebar) =====
  // These work because tenant subdomains are now routed to isp-admin.html (Vuetify app)
  {
    path: '/login',
    component: BlankLayout,
    children: [{
      path: '',
      name: 'ISPLogin',
      component: () => import('../views/ISPLogin.vue'),
      meta: { guest: true, title: 'Login - ISP Admin' },
    }]
  },
  {
    path: '/register',
    component: BlankLayout,
    children: [{
      path: '',
      name: 'ISPRegister',
      component: () => import('../views/ISPRegister.vue'),
      meta: { guest: true, title: 'Daftar - ISP Admin' },
    }]
  },
  {
    path: '/forgot-password',
    component: BlankLayout,
    children: [{
      path: '',
      name: 'ISPForgotPassword',
      component: () => import('../views/ISPForgotPassword.vue'),
      meta: { guest: true, title: 'Lupa Password - ISP Admin' },
    }]
  },

  // ===== PROTECTED ISP ADMIN ROUTES (With Sneat sidebar/navbar) =====
  {
    path: '/isp-admin',
    component: DefaultLayout,
    meta: { requiresAuth: true, role: 'isp_admin' },
    children: [
      { path: '', redirect: '/isp-admin/dashboard' },
      {
        path: 'dashboard',
        name: 'ISPAdminDashboard',
        component: () => import('../views/ISPAdmin/Dashboard.vue'),
        meta: { title: 'Dashboard - ISP Admin' },
      },
      {
        path: 'customers',
        name: 'ISPAdminCustomers',
        component: () => import('../views/ISPAdmin/CustomerManagement.vue'),
        meta: { title: 'Manajemen Pelanggan' },
      },
      {
        path: 'maps',
        name: 'ISPAdminMaps',
        component: () => import('../views/ISPAdmin/Maps.vue'),
        meta: { title: 'Peta Pelanggan' },
      },
      {
        path: 'users',
        name: 'ISPAdminUsers',
        component: () => import('../views/ISPAdmin/UserManagement.vue'),
        meta: { title: 'Manajemen User' },
      },
      {
        path: 'packages',
        name: 'ISPAdminPackages',
        component: () => import('../views/ISPAdmin/Packages.vue'),
        meta: { title: 'Paket Layanan' },
      },
      {
        path: 'mikrotik',
        name: 'ISPAdminMikrotik',
        component: () => import('../views/ISPAdmin/Mikrotik.vue'),
        meta: { title: 'Mikrotik' },
      },
      {
        path: 'tickets',
        name: 'ISPAdminTickets',
        component: () => import('../views/ISPAdmin/Tickets.vue'),
        meta: { title: 'Tiket Dukungan' },
      },
      {
        path: 'invoices',
        name: 'ISPAdminInvoices',
        component: () => import('../views/ISPAdmin/Invoices.vue'),
        meta: { title: 'Tagihan' },
      },
      {
        path: 'reports',
        name: 'ISPAdminReports',
        component: () => import('../views/ISPAdmin/Reports.vue'),
        meta: { title: 'Laporan' },
      },
      {
        path: 'monitoring',
        name: 'ISPAdminMonitoring',
        component: () => import('../views/ISPAdmin/Monitoring.vue'),
        meta: { title: 'Monitoring Realtime' },
      },
      {
        path: 'radius',
        name: 'ISPAdminRadius',
        component: () => import('../views/ISPAdmin/Radius.vue'),
        meta: { title: 'Radius Server' },
      },
      {
        path: 'acs',
        name: 'ISPAdminACS',
        component: () => import('../views/ISPAdmin/ACS.vue'),
        meta: { title: 'ACS (TR-069)' },
      },
      {
        path: 'remote',
        name: 'ISPAdminRemote',
        component: () => import('../views/ISPAdmin/RemoteAccess.vue'),
        meta: { title: 'Remote ONT / Forwarding' },
      },
      {
        path: 'vpn-api',
        name: 'ISPAdminVPN',
        component: () => import('../views/ISPAdmin/VPN.vue'),
        meta: { title: 'VPN API' },
      },
      {
        path: 'portal',
        name: 'ISPAdminPortal',
        component: () => import('../views/ISPAdmin/CustomerPortal.vue'),
        meta: { title: 'Portal Pelanggan' },
      },
      {
        path: 'android',
        name: 'ISPAdminAndroid',
        component: () => import('../views/ISPAdmin/AndroidApp.vue'),
        meta: { title: 'Android App' },
      },
      {
        path: 'settings',
        name: 'ISPAdminSettings',
        component: () => import('../views/ISPAdmin/Settings.vue'),
        meta: { title: 'Pengaturan' },
      },
    ]
  },

  // Catch-all route
  { path: '/isp-admin', redirect: '/isp-admin/dashboard' },
  { path: '/:pathMatch(.*)*', redirect: '/' },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});

// Navigation Guard
router.beforeEach((to, from, next) => {
  document.title = to.meta.title || 'ISP Admin - Sneat';

  const ispAdminToken =
    localStorage.getItem('isp_admin_token') ||
    sessionStorage.getItem('auth_token');

  // Redirect to login if accessing protected route without token
  if (to.meta.requiresAuth && !ispAdminToken) {
    return next('/login');
  }

  // Redirect logged-in users away from guest pages (login/register/forgot-password)
  if (to.meta.guest && ispAdminToken) {
    return next('/isp-admin/dashboard');
  }

  // Redirect logged-in users from root / to dashboard
  if (to.path === '/login' && ispAdminToken) {
    return next('/isp-admin/dashboard');
  }

  next();
});

export default router;
