import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';
import { getTenantInfo, setTenantInfo, clearTenantInfo } from '@/utils/tenant';

export const useISPAdminStore = defineStore('ispAdmin', () => {
  // State
  const user = ref(null);
  const token = ref(localStorage.getItem('isp_admin_token') || null);
  const tenant = ref(getTenantInfo());
  const isAuthenticated = ref(!!token.value);
  const loading = ref(false);
  const error = ref(null);

  // Getters
  const ispName = computed(() => tenant.value?.company_name || 'ISP Admin');
  const ispSubdomain = computed(() => tenant.value?.subdomain || '');

  // Actions
  async function register(data) {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.post('/api/isp-admin/register', data);
      
      if (response.data.success) {
        return { success: true, message: response.data.message };
      } else {
        error.value = response.data.message || 'Registrasi gagal';
        return { success: false, message: error.value };
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Terjadi kesalahan saat registrasi';
      return { success: false, message: error.value, errors: err.response?.data?.errors };
    } finally {
      loading.value = false;
    }
  }

  async function login(credentials) {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.post('/api/isp-admin/login', credentials);
      
      if (response.data.success) {
        token.value = response.data.token;
        user.value = response.data.user;
        
        // Map user data to tenant info since they contain company info
        const tenantInfo = {
          company_name: response.data.user.company_name,
          subdomain: response.data.user.subdomain,
          logo: null // TODO: Add logo support
        };
        tenant.value = tenantInfo;
        
        // Store in localStorage
        localStorage.setItem('isp_admin_token', response.data.token);
        localStorage.setItem('isp_admin_user', JSON.stringify(response.data.user));
        setTenantInfo(tenantInfo); // Update utility
        
        isAuthenticated.value = true;
        
        // Set axios default header
        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
        
        return { success: true };
      } else {
        error.value = response.data.message || 'Login failed';
        return { success: false, message: error.value };
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Network error';
      return { success: false, message: error.value };
    } finally {
      loading.value = false;
    }
  }

  async function logout() {
    try {
      await axios.post('/api/isp-admin/logout');
    } catch (err) {
      console.error('Logout error:', err);
    } finally {
      // Clear state
      token.value = null;
      user.value = null;
      tenant.value = null;
      isAuthenticated.value = false;
      
      // Clear localStorage
      clearTenantInfo();
      
      // Clear axios header
      delete axios.defaults.headers.common['Authorization'];
    }
  }

  async function fetchUser() {
    if (!token.value) return;

    try {
      const response = await axios.get('/api/isp-admin/user');
      user.value = response.data.user;
      tenant.value = response.data.tenant;
      setTenantInfo(response.data.tenant);
    } catch (err) {
      console.error('Failed to fetch user:', err);
      // If unauthorized, clear auth
      if (err.response?.status === 401) {
        await logout();
      }
    }
  }

  // Initialize axios header if token exists
  if (token.value) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`;
  }

  return {
    // State
    user,
    token,
    tenant,
    isAuthenticated,
    loading,
    error,
    
    // Getters
    ispName,
    ispSubdomain,
    
    // Actions
    register,
    login,
    logout,
    fetchUser
  };
});
