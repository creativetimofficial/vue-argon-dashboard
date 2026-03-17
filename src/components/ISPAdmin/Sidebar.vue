<template>
  <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
      <router-link to="/isp-admin/dashboard" class="app-brand-link">
        <span class="app-brand-text demo menu-text fw-bold ms-2">{{ ispName }}</span>
      </router-link>

      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
        <i class="bx bx-chevron-left bx-md d-block align-middle"></i>
      </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
      <!-- Dynamic Menus -->
      <template v-for="(item, index) in filteredMenu" :key="index">
        <!-- Divider -->
        <li v-if="item.isHeader" class="menu-header small text-uppercase">
          <span class="menu-header-text">{{ item.text }}</span>
        </li>
        
        <!-- Menu Item -->
        <li v-else class="menu-item" :class="{ active: isActive(item.to) }">
          <router-link :to="item.to" class="menu-link">
            <i class="menu-icon tf-icons" :class="item.icon"></i>
            <div>{{ item.text }}</div>
          </router-link>
        </li>
      </template>
    </ul>
  </aside>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import { useISPAdminStore } from '@/stores/ispAdmin';

const route = useRoute();
const ispAdminStore = useISPAdminStore();

const ispName = computed(() => ispAdminStore.ispName);
const ispPackage = computed(() => {
   // Try to get package from user object in storage if store doesn't have it
   const userStr = localStorage.getItem("user") || sessionStorage.getItem("user");
   if (userStr) {
      const user = JSON.parse(userStr);
      return user?.isp?.package;
   }
   return null;
});

const menuItems = [
  { to: "/isp-admin/dashboard", icon: "bx bx-home-smile", text: "Dashboard" },
  { to: "/isp-admin/customers", icon: "bx bx-user", text: "Managemen Pelanggan" },
  { to: "/isp-admin/maps", icon: "bx bx-map-alt", text: "Peta Pelanggan" },
  { to: "/isp-admin/packages", icon: "bx bx-package", text: "Paket Internet" },
  
  { isHeader: true, text: "Operasional" },
  { to: "/isp-admin/monitoring", icon: "bx bx-pulse", text: "Monitoring Realtime" },
  { to: "/isp-admin/tickets", icon: "bx bx-support", text: "Tiket Support" },
  { to: "/isp-admin/invoices", icon: "bx bx-file", text: "Invoice & Tagihan" },

  { isHeader: true, text: "Infrastruktur" },
  { to: "/isp-admin/mikrotik", icon: "bx bx-broadcast", text: "Mikrotik Management" },
  { to: "/isp-admin/radius", icon: "bx bx-server", text: "Radius Server" },
  { to: "/isp-admin/acs", icon: "bx bx-chip", text: "ACS (TR-069)" },
  { to: "/isp-admin/remote", icon: "bx bx-link-external", text: "Remote ONT / Forwarding" },
  { to: "/isp-admin/vpn-api", icon: "bx bx-terminal", text: "VPN API" },
  
  { isHeader: true, text: "Layanan & Aplikasi" },
  { to: "/isp-admin/reports", icon: "bx bx-bar-chart-alt-2", text: "Laporan" },
  { to: "/isp-admin/portal", icon: "bx bx-desktop", text: "Portal Pelanggan" },
  { to: "/isp-admin/android", icon: "bx bxl-android", text: "Android App" },
  
  { isHeader: true, text: "Sistem & User" },
  { to: "/isp-admin/settings", icon: "bx bx-cog", text: "Pengaturan" },
  { to: "/isp-admin/users", icon: "bx bx-user-circle", text: "Manajemen User", requiredFeature: 'multi_user_access' },
];

const filteredMenu = computed(() => {
  return menuItems.filter(item => {
    if (item.isHeader) return true;
    if (!item.requiredFeature) return true;
    
    const pkg = ispPackage.value;
    if (!pkg) return false;
    
    return pkg[item.requiredFeature] === true || pkg[item.requiredFeature] === 1;
  });
});

const isActive = (path) => {
  return route.path.startsWith(path);
};
</script>

<style scoped>
/* Additional sidebar styles if needed */
</style>
