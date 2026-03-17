<script setup>
import { computed, ref, onMounted } from "vue";
import { useStore } from "vuex";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";
import api from "@/services/api";
import Breadcrumbs from "../Breadcrumbs.vue";
import LanguageSelector from "@/components/LanguageSelector.vue";

const showMenu = ref(false);
const store = useStore();
const isRTL = computed(() => store.state.isRTL);
const route = useRoute();
const router = useRouter();

const currentRouteName = computed(() => route.name);
const currentDirectory = computed(() => {
  let dir = route.path.split("/")[1];
  return dir.charAt(0).toUpperCase() + dir.slice(1);
});

const minimizeSidebar = () => store.commit("sidebarMinimize");
const toggleConfigurator = () => store.commit("toggleConfigurator");
const closeMenu = () => {
  setTimeout(() => {
    showMenu.value = false;
  }, 100);
};

const searchQuery = ref("");
const searchResults = ref([]);
const searching = ref(false);
const showSearchResults = ref(false);
let searchTimeout = null;

const handleSearch = async (e) => {
  // If Enter is pressed, search immediately
  if (e && e.key === "Enter") {
    clearTimeout(searchTimeout);
    performSearch();
    return;
  }

  // Otherwise, debounce search as user types
  clearTimeout(searchTimeout);
  if (searchQuery.value.trim().length >= 2) {
    searchTimeout = setTimeout(() => {
      performSearch();
    }, 500);
  } else {
    searchResults.value = [];
    showSearchResults.value = false;
  }
};

const performSearch = async () => {
  if (!searchQuery.value.trim()) return;
  
  searching.value = true;
  showSearchResults.value = true;
  try {
    const role = user.value?.role;
    const endpoint = role === 'super_admin' ? '/super-admin/search' : '/isp-admin/search';
    const res = await api.get(`${endpoint}?q=${encodeURIComponent(searchQuery.value)}`);
    searchResults.value = res.data.results || [];
  } catch (err) {
    searchResults.value = [{ name: "Tidak ada hasil", description: "Terjadi kesalahan saat mencari" }];
  } finally {
    searching.value = false;
  }
};

const closeSearchResults = () => {
  showSearchResults.value = false;
};
const goToResult = (path) => {
  if (path) {
    router.push(path);
    showSearchResults.value = false;
    searchQuery.value = "";
  }
};
const clearNotifications = () => {
  notifications.value = [];
  localStorage.setItem('dismissed_notifications', 'true');
};

// --- USER ---
const user = ref(null);
const userMenuOpen = ref(false);
onMounted(() => {
  const userData = localStorage.getItem("user") || sessionStorage.getItem("user");
  if (userData) user.value = JSON.parse(userData);
});
const handleSignInClick = () => {
  if (!user.value) {
    router.push({ name: "Login" });
  } else {
    userMenuOpen.value = !userMenuOpen.value;
  }
};
const handleLogout = () => {
  localStorage.removeItem("auth_token");
  localStorage.removeItem("user");
  sessionStorage.removeItem("auth_token");
  sessionStorage.removeItem("user");
  user.value = null;
  router.push({ name: "Login" });
};

// --- NOTIFICATIONS ---
const notifications = ref([]);
const notificationCount = computed(() => notifications.value.length);
const fetchNotifications = async () => {
  if (localStorage.getItem('dismissed_notifications') === 'true') {
    return;
  }
  
  try {
    const res = await api.get("/notifications");
    notifications.value = res.data.notifications || [];
  } catch (err) {
    notifications.value = [];
  }
};
onMounted(fetchNotifications);
</script>
<template>
  <nav
    class="navbar navbar-main navbar-expand-lg px-0 shadow-none border-radius-xl"
    :class="isRTL ? 'top-0 position-sticky z-index-sticky' : ''"
    v-bind="$attrs"
    id="navbarBlur"
    data-scroll="true"
  >
    <div class="px-3 py-1 container-fluid">
      <breadcrumbs
        :current-page="currentRouteName"
        :current-directory="currentDirectory"
      />

      <div
        class="mt-2 collapse navbar-collapse mt-sm-0 me-md-0 me-sm-4"
        :class="isRTL ? 'px-0' : 'me-sm-4'"
        id="navbar"
      >
        <div
          class="pe-md-3 d-flex align-items-center"
          :class="isRTL ? 'me-md-auto' : 'ms-md-auto'"
        >
          <div class="input-group position-relative">
            <span class="input-group-text text-body">
              <i class="fas fa-search" aria-hidden="true"></i>
            </span>
            <input
              id="navbar_search"
              name="search"
              type="text"
              class="form-control"
              placeholder="Cari menu, pesanan, atau data (Contoh: Pesanan, Tagihan)..."
              v-model="searchQuery"
              @input="handleSearch"
              @keydown="handleSearch"
              @focus="showSearchResults = false"
            />
            <div v-if="showSearchResults" class="search-results position-absolute border rounded shadow p-0 overflow-hidden" style="top: 40px; left: 0; right: 0; z-index: 1000;">
              <div v-if="searching" class="text-center py-2">Searching...</div>
              <div v-else>
                <div v-if="searchResults.length === 0" class="text-center py-2 text-muted">No results found</div>
                <div 
                  v-for="result in searchResults" 
                  :key="result.id || result.name" 
                  class="search-item p-2 border-bottom cursor-pointer transition-all"
                  @click="goToResult(result.to)"
                >
                  <div class="d-flex align-items-center">
                    <i v-if="result.icon" :class="['bx', result.icon, 'me-2']"></i>
                    <div>
                      <div class="fw-bold text-sm">{{ result.name }}</div>
                      <div class="text-xs text-secondary">{{ result.description }}</div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="text-end p-1 bg-light">
                <button class="btn btn-sm btn-link mb-0 py-1" @click="closeSearchResults">Close</button>
              </div>
            </div>
          </div>
        </div>
        <ul class="navbar-nav justify-content-end">
          <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
            <a
              href="#"
              @click="minimizeSidebar"
              class="p-0 nav-link text-white hamburger-menu"
              id="iconNavbarSidenav"
            >
              <div class="sidenav-toggler-inner">
                <i class="sidenav-toggler-line bg-white"></i>
                <i class="sidenav-toggler-line bg-white"></i>
                <i class="sidenav-toggler-line bg-white"></i>
              </div>
            </a>
          </li>
          <li class="px-3 nav-item d-flex align-items-center">
            <a class="p-0 nav-link text-white" @click="toggleConfigurator">
              <i class="cursor-pointer fa fa-cog fixed-plugin-button-nav"></i>
            </a>
          </li>
          <li class="nav-item px-3 d-flex align-items-center">
            <language-selector />
          </li>
          <li class="nav-item dropdown d-flex align-items-center position-relative" :class="isRTL ? 'ps-2' : 'pe-2'">
            <a
              href="javascript:;"
              class="p-0 nav-link text-white position-relative"
              :class="[showMenu ? 'show' : '']"
              id="dropdownMenuButton"
              @click="showMenu = !showMenu; fetchNotifications();"
              @blur="closeMenu"
            >
              <i class="cursor-pointer fa fa-bell"></i>
              <span v-if="notificationCount > 0" class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill">{{ notificationCount }}</span>
            </a>
            <ul
              class="px-2 py-3 dropdown-menu dropdown-menu-end me-sm-n4 notification-dropdown"
              :class="showMenu ? 'show' : ''"
              aria-labelledby="dropdownMenuButton"
              style="min-width: 250px; max-height: 400px; overflow-y: auto;"
            >
              <li class="mb-2 d-flex justify-content-between align-items-center px-2 border-bottom pb-2">
                <span class="text-xs font-weight-bold text-uppercase">Notifikasi</span>
                <a href="javascript:;" class="text-xs text-info" @click="clearNotifications" v-if="notifications.length > 0">Bersihkan Semua</a>
              </li>
              <li v-if="notifications.length === 0" class="text-center py-2 text-muted">No notifications</li>
              <li v-for="notif in notifications" :key="notif.id" class="mb-2">
                <a class="dropdown-item border-radius-md" href="javascript:;">
                  <div class="py-1 d-flex">
                    <div class="my-auto">
                      <i class="fa fa-info-circle text-primary me-2"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-center">
                      <h6 class="mb-1 text-sm font-weight-normal">{{ notif.title }}</h6>
                      <p class="mb-0 text-xs text-secondary">{{ notif.message }}</p>
                      <p class="mb-0 text-xs text-secondary"><i class="fa fa-clock me-1"></i> {{ notif.time }}</p>
                    </div>
                  </div>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<style scoped>
/* Hamburger Menu Visibility Fix */
.hamburger-menu .sidenav-toggler-line,
#iconNavbarSidenav .sidenav-toggler-line,
.sidenav-toggler-inner .sidenav-toggler-line,
.nav-link.hamburger-menu .sidenav-toggler-line {
    background-color: #ffffff !important;
    opacity: 1 !important;
}

/* User menu dropdown styling */
.user-menu {
  background-color: var(--bs-body-bg, #fff);
  color: var(--bs-body-color, #000);
}

.user-menu a {
  color: var(--bs-body-color, #000);
  text-decoration: none;
}

.user-menu a:hover {
  color: var(--bs-primary, #5e72e4);
}

/* Search results dropdown styling */
.search-results {
  background-color: var(--bs-body-bg, #fff);
  color: var(--bs-body-color, #000);
}

.search-item {
  cursor: pointer;
  transition: all 0.2s ease;
}

.search-item:hover {
  background-color: rgba(94, 114, 228, 0.1);
}

.search-item:last-child {
  border-bottom: none !important;
}

/* Dark theme support */
body.dark-version .user-menu,
body.dark-version .search-results,
body.dark-version .notification-dropdown {
  background-color: #111c44 !important;
  color: #ffffff !important;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

body.dark-version .search-item:hover,
body.dark-version .dropdown-item:hover {
  background-color: rgba(255, 255, 255, 0.1) !important;
}

body.dark-version .text-secondary {
  color: #cbd5e0 !important;
}

body.dark-version .text-sm, 
body.dark-version .text-xs,
body.dark-version h6 {
  color: #ffffff !important;
}

body.dark-version .bg-light {
  background-color: #1a233a !important;
}
</style>
