<template>
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <!-- Sidebar -->
      <ISPAdminSidebar />

      <!-- Layout page -->
      <div class="layout-page">
        <!-- Navbar -->
        <ISPAdminNavbar />

        <!-- Content wrapper -->
        <div class="content-wrapper">
          <!-- Content -->
          <div class="container-xxl flex-grow-1 container-p-y">
            <router-view />
          </div>
          <!-- / Content -->

          <!-- Footer -->
          <footer class="content-footer footer bg-footer-theme">
            <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
              <div class="mb-2 mb-md-0">
                © {{ currentYear }}, {{ ispName }}
              </div>
            </div>
          </footer>
          <!-- / Footer -->
        </div>
      </div>
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useISPAdminStore } from '@/stores/ispAdmin';
import ISPAdminSidebar from '@/components/ISPAdmin/Sidebar.vue';
import ISPAdminNavbar from '@/components/ISPAdmin/Navbar.vue';

const ispAdminStore = useISPAdminStore();

const currentYear = computed(() => new Date().getFullYear());
const ispName = computed(() => ispAdminStore.ispName);
</script>


