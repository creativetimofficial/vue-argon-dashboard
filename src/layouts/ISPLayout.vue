<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useTheme } from 'vuetify'
import UserProfile from './components/UserProfile.vue'
import NotificationDropdown from './components/NotificationDropdown.vue'
import NavSearchBar from './components/NavSearchBar.vue'
import ThemeSwitcher from './components/ThemeSwitcher.vue'
import LanguageSelector from '@/components/LanguageSelector.vue'

const drawer = ref(true)
const route = useRoute()
const router = useRouter()
const { name: themeName } = useTheme()

const menuItems = [
  { title: 'nav.dashboard', icon: 'bx-home-smile', to: '/isp-admin/dashboard' },
  { type: 'header', title: 'nav.core_management' },
  { title: 'nav.customers', icon: 'bx-user', to: '/isp-admin/customers' },
  { title: 'nav.customer_maps', icon: 'bx-map-alt', to: '/isp-admin/maps' },
  { title: 'nav.internet_packages', icon: 'bx-package', to: '/isp-admin/packages' },
  { type: 'header', title: 'nav.operational' },
  { title: 'nav.monitoring_realtime', icon: 'bx-pulse', to: '/isp-admin/monitoring' },
  { title: 'nav.billing', icon: 'bx-file', to: '/isp-admin/invoices' },
  { title: 'nav.support_tickets', icon: 'bx-support', to: '/isp-admin/tickets' },
  { type: 'header', title: 'nav.infrastructure' },
  { title: 'nav.mikrotik_management', icon: 'bx-broadcast', to: '/isp-admin/mikrotik' },
  { title: 'nav.radius_server', icon: 'bx-server', to: '/isp-admin/radius' },
  { title: 'nav.acs', icon: 'bx-chip', to: '/isp-admin/acs' },
  { title: 'nav.remote_access', icon: 'bx-link-external', to: '/isp-admin/remote' },
  { title: 'nav.vpn_api', icon: 'bx-terminal', to: '/isp-admin/vpn-api' },
  { type: 'header', title: 'nav.marketing_apps' },
  { title: 'nav.reports', icon: 'bx-bar-chart-alt-2', to: '/isp-admin/reports' },
  { title: 'nav.customer_portal', icon: 'bx-desktop', to: '/isp-admin/portal' },
  { title: 'nav.android_app', icon: 'bx-mobile-vibration', to: '/isp-admin/android' },
  { type: 'header', title: 'nav.settings' },
  { title: 'nav.settings', icon: 'bx-cog', to: '/isp-admin/settings' },
  { title: 'nav.user_management', icon: 'bx-user-circle', to: '/isp-admin/users' },
]

const isActive = (to) => route.path.startsWith(to)
</script>

<template>
  <v-layout>
    <!-- Navigation Drawer (Sidebar) -->
    <v-navigation-drawer
      v-model="drawer"
      elevation="0"
      border="0"
      class="sidebar-nav"
      width="260"
    >
      <div class="sidebar-scroll-container">
        <div class="pa-4 d-flex align-center">
          <v-avatar color="primary" rounded class="me-3" size="32">
            <v-icon color="white" icon="bx-wifi"></v-icon>
          </v-avatar>
          <span class="text-h6 font-weight-bold text-uppercase" style="letter-spacing: 1px">ISP Admin</span>
        </div>

        <v-list density="compact" nav class="px-2">
          <template v-for="item in menuItems" :key="item.title">
            <v-list-subheader v-if="item.type === 'header'" class="text-uppercase font-weight-bold text-xs mt-4 mb-1">
              {{ $t(item.title) }}
            </v-list-subheader>
            
            <v-list-item
              v-else
              :to="item.to"
              :prepend-icon="item.icon"
              :title="$t(item.title)"
              :active="isActive(item.to)"
              rounded="lg"
              class="mb-1"
              color="primary"
            >
            </v-list-item>
          </template>
        </v-list>
      </div>
    </v-navigation-drawer>

    <!-- App Bar (Navbar) -->
    <v-app-bar elevation="0" class="px-4 border-b">
      <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>
      
      <NavSearchBar />
      
      <v-spacer></v-spacer>
      
      <ThemeSwitcher />
      <div class="mx-1"></div>
      <LanguageSelector />
      <div class="mx-1"></div>
      <NotificationDropdown />
      <div class="mx-1"></div>
      <UserProfile />
    </v-app-bar>

    <!-- Main Content -->
    <v-main>
      <div class="pa-0 h-100">
        <RouterView :key="$route.fullPath" />
      </div>
    </v-main>
  </v-layout>
</template>

<style scoped>
.sidebar-nav {
  background-color: rgb(var(--v-theme-surface)) !important;
}

.sidebar-scroll-container {
  height: 100%;
  overflow-y: auto;
  overflow-x: hidden;
  
  /* Hide scrollbar for Chrome, Safari and Opera */
  &::-webkit-scrollbar {
    display: none;
  }
  
  /* Hide scrollbar for IE, Edge and Firefox */
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}

.text-xs {
  font-size: 0.7rem !important;
  color: #a1acb8 !important;
}

/* Custom list item styles to match Sneat */
:deep(.v-list-item--active) {
  background: linear-gradient(270deg, rgba(var(--v-theme-primary), 0.9) 0%, rgba(var(--v-theme-primary), 0.7) 100%) !important;
  color: white !important;
  box-shadow: 0 2px 4px 0 rgba(var(--v-theme-primary), 0.4);
}

:deep(.v-list-item__prepend > .v-icon) {
  margin-inline-end: 12px !important;
}
</style>
