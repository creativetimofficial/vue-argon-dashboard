/**
 * Tenant Detection Utility
 * Detects ISP tenant from subdomain or custom domain
 */

export function detectTenant() {
  const hostname = window.location.hostname;
  const parts = hostname.split('.');
  
  // Check if subdomain (e.g., irvan.localhost or isp1.yourdomain.com)
  if (parts.length >= 2) {
    const subdomain = parts[0];
    
    // Skip main domains
    const mainDomains = ['localhost', '127', 'www', 'app'];
    if (mainDomains.includes(subdomain)) {
      return null;
    }
    
    return {
      subdomain,
      isCustomDomain: parts.length === 2, // e.g., customdomain.com
      hostname
    };
  }
  
  return null;
}

export function getApiBaseUrl() {
  const tenant = detectTenant();
  
  if (tenant) {
    // ISP Admin API calls go to main backend with tenant context
    return 'http://localhost:8000/api/isp-admin';
  }
  
  // Super Admin / Client Area
  return 'http://localhost:8000/api';
}

export function isISPAdminRoute() {
  return window.location.pathname.startsWith('/isp-admin');
}

export function getTenantInfo() {
  const tenant = detectTenant();
  
  if (!tenant) {
    return null;
  }
  
  // Get tenant info from localStorage (set during login)
  const tenantData = localStorage.getItem('isp_tenant_info');
  
  if (tenantData) {
    try {
      return JSON.parse(tenantData);
    } catch (e) {
      console.error('Failed to parse tenant info:', e);
      return null;
    }
  }
  
  return {
    subdomain: tenant.subdomain,
    hostname: tenant.hostname
  };
}

export function setTenantInfo(info) {
  localStorage.setItem('isp_tenant_info', JSON.stringify(info));
}

export function clearTenantInfo() {
  localStorage.removeItem('isp_tenant_info');
  localStorage.removeItem('isp_admin_token');
  localStorage.removeItem('isp_admin_user');
}
