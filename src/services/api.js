import axios from "axios";

// Create axios instance
const api = axios.create({
  baseURL: process.env.VUE_APP_API_URL || "http://localhost:8000/api",
  timeout: 30000,
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
});

// Request interceptor to add token
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem("auth_token");
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
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
      localStorage.removeItem("auth_token");
      localStorage.removeItem("user");
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
  getPaymentGateways: () => api.get("/super-admin/payment-gateways"),
};

// ISP Admin API
export const ispAdminAPI = {
  getDashboard: () => api.get("/isp-admin/dashboard"),
  getCustomers: (params) => api.get("/isp-admin/customers", { params }),
  getCustomer: (id) => api.get(`/isp-admin/customers/${id}`),
  createCustomer: (data) => api.post("/isp-admin/customers", data),
  updateCustomer: (id, data) => api.put(`/isp-admin/customers/${id}`, data),
  deleteCustomer: (id) => api.delete(`/isp-admin/customers/${id}`),
  
  // Client Area endpoints for ISP Admin
  getClientAreaStats: () => api.get("/isp-admin/client-area/stats"),
  getClientAreaServices: () => api.get("/isp-admin/client-area/services"),
  getClientAreaOrders: () => api.get("/isp-admin/client-area/orders"),
  createClientAreaOrder: (data) => api.post("/isp-admin/client-area/orders", data),
};

export default api;
