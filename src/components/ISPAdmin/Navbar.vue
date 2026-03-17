<template>
  <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
      <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)" @click="toggleMenu">
        <i class="bx bx-menu bx-md"></i>
      </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
      <!-- Search -->
      <div class="navbar-nav align-items-center me-auto">
        <div class="nav-item d-flex align-items-center">
          <i class="bx bx-search bx-md"></i>
          <input
            type="text"
            class="form-control border-0 shadow-none ps-1 ps-sm-2"
            placeholder="Cari..."
            aria-label="Cari..."
            v-model="searchQuery"
          />
        </div>
      </div>

      <!-- Right Side -->
      <ul class="navbar-nav flex-row align-items-center ms-auto">
        <!-- User Dropdown -->
        <li class="nav-item navbar-dropdown dropdown-user dropdown">
          <a
            class="nav-link dropdown-toggle hide-arrow p-0"
            href="javascript:void(0);"
            data-bs-toggle="dropdown"
          >
            <div class="avatar avatar-online">
              <img src="/sneat/assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" />
            </div>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <a class="dropdown-item" href="#">
                <div class="d-flex">
                  <div class="flex-shrink-0 me-3">
                    <div class="avatar avatar-online">
                      <img src="/sneat/assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" />
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    <h6 class="mb-0">{{ userName }}</h6>
                    <small class="text-muted">{{ ispName }}</small>
                  </div>
                </div>
              </a>
            </li>
            <li>
              <div class="dropdown-divider my-1"></div>
            </li>
            <li>
              <router-link class="dropdown-item" to="/isp-admin/settings">
                <i class="bx bx-user bx-md me-3"></i><span>Profil Saya</span>
              </router-link>
            </li>
            <li>
              <router-link class="dropdown-item" to="/isp-admin/settings">
                <i class="bx bx-cog bx-md me-3"></i><span>Pengaturan</span>
              </router-link>
            </li>
            <li>
              <div class="dropdown-divider my-1"></div>
            </li>
            <li>
              <a class="dropdown-item" href="javascript:void(0);" @click="handleLogout">
                <i class="bx bx-power-off bx-md me-3"></i><span>Keluar</span>
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useISPAdminStore } from '@/stores/ispAdmin';

const router = useRouter();
const ispAdminStore = useISPAdminStore();

const searchQuery = ref('');

const userName = computed(() => ispAdminStore.user?.name || 'ISP Admin');
const ispName = computed(() => ispAdminStore.ispName);

const toggleMenu = () => {
  document.body.classList.toggle('layout-menu-expanded');
};

const handleLogout = async () => {
  await ispAdminStore.logout();
  router.push('/login');
};
</script>

<style scoped>
/* Additional navbar styles if needed */
</style>
