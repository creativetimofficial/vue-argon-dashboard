import axios from "axios";

// Create axios instance
const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || "http://localhost:8000/api",
  timeout: 30000,
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
});

// Request interceptor to add token
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem("auth_token") || sessionStorage.getItem("auth_token");
    
    // List of public routes that don't require an auth token
    const publicRoutes = [
      '/auth/login',
      '/auth/register',
      '/auth/login-role',
      '/landing-page',
      '/theme/public',
      '/subscription-packages/public',
      '/public-tenant-packages',
      '/tenant-info'
    ];

    // Check if the current request URL is a public route
    const isPublicRoute = publicRoutes.some(route => config.url && config.url.includes(route));

    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    } else if (!isPublicRoute) {
      // Only log "no token" for protected routes
      console.warn("no token");
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  },
);

// Response interceptor to handle errors
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Unauthorized - clear token and redirect to login
      // Unauthorized - clear token and redirect to login
      localStorage.removeItem("auth_token");
      localStorage.removeItem("user");
      sessionStorage.removeItem("auth_token");
      sessionStorage.removeItem("user");
      window.location.href = "/login";
    }
    return Promise.reject(error);
  },
);

// Auth API
export const authAPI = {
  login: (credentials) => api.post("/auth/login", credentials),
  register: (data) => api.post("/auth/register", data),
  logout: () => api.post("/auth/logout"),
  getUser: () => api.get("/auth/user"),
  forgotPassword: (email) => api.post("/auth/forgot-password", { email }),
  verifyResetCode: (data) => api.post("/auth/verify-reset-code", data),
  resetPassword: (data) => api.post("/auth/reset-password", data),
  getTenantInfo: () => api.get("/tenant-info"),
  getPublicTheme: () => api.get("/theme/public"),
  getPublicTenantPackages: () => api.get("/public-tenant-packages"),
};

// Super Admin API
export const superAdminAPI = {
  getStats: () => api.get("/super-admin/dashboard/stats"),
  getRecentISPs: () => api.get("/super-admin/dashboard/recent-isps"),
  getISPs: (params) => api.get("/super-admin/isp-management", { params }),
  getISP: (id) => api.get(`/super-admin/isp-management/${id}`),
  approveISP: (id) => api.post(`/super-admin/isp-management/${id}/approve`),
  rejectISP: (id) => api.post(`/super-admin/isp-management/${id}/reject`),
  getSubscriptionPackages: () => api.get("/super-admin/subscription-packages"),
  createSubscriptionPackage: (data) => api.post("/super-admin/subscription-packages", data),
  updateSubscriptionPackage: (id, data) => api.put(`/super-admin/subscription-packages/${id}`, data),
  deleteSubscriptionPackage: (id) => api.delete(`/super-admin/subscription-packages/${id}`),
  getPaymentGateways: () => api.get("/super-admin/payment-gateways"),
};

// ISP Admin API
export const ispAdminAPI = {
  getDashboard: () => api.get("/isp-admin/dashboard"),
  getCustomers: (params) => api.get("/isp-admin/customers", { params }),
  getCustomersMap: () => api.get("/isp-admin/customers-map"),
  getCustomer: (id) => api.get(`/isp-admin/customers/${id}`),
  createCustomer: (data) => api.post("/isp-admin/customers", data),
  updateCustomer: (id, data) => api.put(`/isp-admin/customers/${id}`, data),
  deleteCustomer: (id) => api.delete(`/isp-admin/customers/${id}`),
  
  // Client Area endpoints for ISP Admin
  getClientAreaStats: () => api.get("/isp-admin/client-area/stats"),
  getMyServices: () => api.get("/isp-admin/client-area/my-services"),
  getOwnedIsps: () => api.get("/isp-admin/client-area/owned-isps"),
  getClientAreaServices: () => api.get("/isp-admin/client-area/services"),
  getClientAreaOrders: () => api.get("/isp-admin/client-area/orders"),
  createClientAreaOrder: (data) => api.post("/isp-admin/client-area/orders", data),
  
  // Theme Management
  getTheme: () => api.get("/isp-admin/theme"),
  updateTheme: (data) => api.post("/isp-admin/theme", data),

  // Staff Management
  getStaff: () => api.get("/isp-admin/staff"),
  createStaff: (data) => api.post("/isp-admin/staff", data),
  updateStaff: (id, data) => api.put(`/isp-admin/staff/${id}`, data),
  deleteStaff: (id) => api.delete(`/isp-admin/staff/${id}`),

  // Packages & Services
  getPackages: () => api.get("/isp-admin/packages"),
  createPackage: (data) => api.post("/isp-admin/packages", data),
  updatePackage: (id, data) => api.put(`/isp-admin/packages/${id}`, data),
  deletePackage: (id) => api.delete(`/isp-admin/packages/${id}`),
  getServices: () => api.get("/isp-admin/services"),

  // Invoices & Billing
  getInvoices: (params) => api.get("/isp-admin/invoices", { params }),
  downloadInvoice: (id) => api.get(`/isp-admin/invoices/${id}/download`, { responseType: 'blob' }),
  markInvoicePaid: (id) => api.post(`/isp-admin/invoices/${id}/mark-as-paid`),
  runBillingJob: () => api.post('/isp-admin/billing/trigger'),

  // Other Management
  getReports: () => api.get("/isp-admin/reports"),
  getMikrotik: () => api.get("/isp-admin/mikrotik"),
  createMikrotik: (data) => api.post("/isp-admin/mikrotik", data),
  updateMikrotik: (id, data) => api.put(`/isp-admin/mikrotik/${id}`, data),
  deleteMikrotik: (id) => api.delete(`/isp-admin/mikrotik/${id}`),
  getTickets: () => api.get("/isp-admin/tickets"),
  createTicket: (data) => api.post("/isp-admin/tickets", data),
  updateTicket: (id, data) => api.put(`/isp-admin/tickets/${id}`, data),
  deleteTicket: (id) => api.delete(`/isp-admin/tickets/${id}`),

  // Settings & Profile
  updateProfile: (data) => api.post("/isp-admin/settings/profile", data),
  updatePassword: (data) => api.post("/isp-admin/settings/password", data),
  getNotificationSettings: () => api.get("/isp-admin/settings/notifications"),
  updateNotificationSettings: (data) => api.post("/isp-admin/settings/notifications", data),
};

export default api;
