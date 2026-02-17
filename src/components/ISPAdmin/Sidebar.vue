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
      <!-- Dashboard -->
      <li class="menu-item" :class="{ active: isActive('/isp-admin/dashboard') }">
        <router-link to="/isp-admin/dashboard" class="menu-link">
          <i class="menu-icon tf-icons bx bx-home-smile"></i>
          <div>Dashboard</div>
        </router-link>
      </li>

      <!-- Managemen Pelanggan -->
      <li class="menu-item" :class="{ active: isActive('/isp-admin/customers') }">
        <router-link to="/isp-admin/customers" class="menu-link">
          <i class="menu-icon tf-icons bx bx-user"></i>
          <div>Managemen Pelanggan</div>
        </router-link>
      </li>

      <!-- Paket Internet -->
      <li class="menu-item" :class="{ active: isActive('/isp-admin/packages') }">
        <router-link to="/isp-admin/packages" class="menu-link">
          <i class="menu-icon tf-icons bx bx-package"></i>
          <div>Paket Internet</div>
        </router-link>
      </li>

      <!-- Mikrotik Management -->
      <li class="menu-item" :class="{ active: isActive('/isp-admin/mikrotik') }">
        <router-link to="/isp-admin/mikrotik" class="menu-link">
          <i class="menu-icon tf-icons bx bx-server"></i>
          <div>Managemen Mikrotik</div>
        </router-link>
      </li>

      <!-- Divider -->
      <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Operasional</span>
      </li>

      <!-- Tiket -->
      <li class="menu-item" :class="{ active: isActive('/isp-admin/tickets') }">
        <router-link to="/isp-admin/tickets" class="menu-link">
          <i class="menu-icon tf-icons bx bx-support"></i>
          <div>Tiket Support</div>
        </router-link>
      </li>

      <!-- Divider -->
      <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Keuangan</span>
      </li>

      <!-- Invoice -->
      <li class="menu-item" :class="{ active: isActive('/isp-admin/invoices') }">
        <router-link to="/isp-admin/invoices" class="menu-link">
          <i class="menu-icon tf-icons bx bx-file"></i>
          <div>Invoice</div>
        </router-link>
      </li>

      <!-- Laporan -->
      <li class="menu-item" :class="{ active: isActive('/isp-admin/reports') }">
        <router-link to="/isp-admin/reports" class="menu-link">
          <i class="menu-icon tf-icons bx bx-bar-chart-alt-2"></i>
          <div>Laporan</div>
        </router-link>
      </li>

      <!-- Divider -->
      <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Pengaturan</span>
      </li>

      <!-- Settings -->
      <li class="menu-item" :class="{ active: isActive('/isp-admin/settings') }">
        <router-link to="/isp-admin/settings" class="menu-link">
          <i class="menu-icon tf-icons bx bx-cog"></i>
          <div>Pengaturan</div>
        </router-link>
      </li>
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

const isActive = (path) => {
  return route.path.startsWith(path);
};
</script>

<style scoped>
/* Additional sidebar styles if needed */
</style>
