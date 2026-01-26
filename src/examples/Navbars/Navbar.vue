<script setup>
import { computed, ref, onMounted } from "vue";
import { useStore } from "vuex";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";
import Breadcrumbs from "../Breadcrumbs.vue";

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

// --- SEARCH ---
const searchQuery = ref("");
const searchResults = ref([]);
const searching = ref(false);
const showSearchResults = ref(false);
const handleSearch = async (e) => {
  if (e.key === "Enter" && searchQuery.value.trim()) {
    searching.value = true;
    try {
      const token = localStorage.getItem("auth_token");
      const res = await axios.get(`http://localhost:8000/api/search?q=${encodeURIComponent(searchQuery.value)}`, {
        headers: { Authorization: `Bearer ${token}` },
      });
      searchResults.value = res.data.results || [];
      showSearchResults.value = true;
    } catch (err) {
      searchResults.value = [{ name: "Tidak ada hasil", description: "" }];
      showSearchResults.value = true;
    } finally {
      searching.value = false;
    }
  }
};
const closeSearchResults = () => {
  showSearchResults.value = false;
};

// --- USER ---
const user = ref(null);
const userMenuOpen = ref(false);
onMounted(() => {
  const userData = localStorage.getItem("user");
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
  user.value = null;
  router.push({ name: "Login" });
};

// --- NOTIFICATIONS ---
const notifications = ref([]);
const notificationCount = computed(() => notifications.value.length);
const fetchNotifications = async () => {
  try {
    const token = localStorage.getItem("auth_token");
    const res = await axios.get("http://localhost:8000/api/notifications", {
      headers: { Authorization: `Bearer ${token}` },
    });
    notifications.value = res.data.notifications || [];
  } catch (err) {
    notifications.value = [];
  }
};
onMounted(fetchNotifications);
</script>
<template>
  <nav
    class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl"
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
              type="text"
              class="form-control"
              :placeholder="isRTL ? 'أكتب هنا...' : 'Cari data...'"
              v-model="searchQuery"
              @keydown="handleSearch"
              @focus="showSearchResults = false"
            />
            <div v-if="showSearchResults" class="search-results position-absolute bg-white border rounded shadow p-2" style="top: 40px; left: 0; right: 0; z-index: 1000;">
              <div v-if="searching" class="text-center py-2">Mencari...</div>
              <div v-else>
                <div v-if="searchResults.length === 0" class="text-center py-2 text-muted">Tidak ada hasil</div>
                <div v-for="result in searchResults" :key="result.id || result.name" class="py-1 border-bottom">
                  <div><strong>{{ result.name }}</strong></div>
                  <div class="text-xs text-secondary">{{ result.description }}</div>
                </div>
              </div>
              <div class="text-end pt-1">
                <button class="btn btn-sm btn-link" @click="closeSearchResults">Tutup</button>
              </div>
            </div>
          </div>
        </div>
        <ul class="navbar-nav justify-content-end">
          <li class="nav-item d-flex align-items-center position-relative">
            <a
              href="javascript:;"
              class="px-0 nav-link font-weight-bold text-white"
              @click="handleSignInClick"
            >
              <i class="fa fa-user" :class="isRTL ? 'ms-sm-2' : 'me-sm-2'"></i>
              <span v-if="!user">Sign In</span>
              <span v-else>{{ user.name }}</span>
            </a>
            <div v-if="user && userMenuOpen" class="user-menu position-absolute bg-white border rounded shadow p-2" style="top: 40px; right: 0; z-index: 1000; min-width: 150px;">
              <div class="py-1"><strong>{{ user.name }}</strong></div>
              <div class="py-1"><a href="javascript:;" @click="router.push({ name: 'Profile' })">Profil</a></div>
              <div class="py-1"><a href="javascript:;" @click="handleLogout">Logout</a></div>
            </div>
          </li>
          <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
            <a
              href="#"
              @click="minimizeSidebar"
              class="p-0 nav-link text-white"
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
              class="px-2 py-3 dropdown-menu dropdown-menu-end me-sm-n4"
              :class="showMenu ? 'show' : ''"
              aria-labelledby="dropdownMenuButton"
              style="min-width: 250px; max-height: 300px; overflow-y: auto;"
            >
              <li v-if="notifications.length === 0" class="text-center py-2 text-muted">Tidak ada notifikasi</li>
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
