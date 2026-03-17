<script setup>
import { computed, ref, onMounted } from "vue";
import { useStore } from "vuex";
import SidenavList from "./SidenavList.vue";
import logo from "@/assets/img/logo-ct-dark.png";
import logoWhite from "@/assets/img/logo-ct.png";

const store = useStore();
const isRTL = computed(() => store.state.isRTL);
const layout = computed(() => store.state.layout);
const sidebarType = computed(() => store.state.sidebarType);
const darkMode = computed(() => store.state.darkMode);

const user = ref(null);
onMounted(() => {
  const userData = localStorage.getItem("user") || sessionStorage.getItem("user");
  if (userData) user.value = JSON.parse(userData);
});
</script>
<template>
  <div
    v-show="layout === 'default'"
    class="min-height-300 position-absolute w-100"
    :class="`${darkMode ? 'bg-transparent' : 'bg-success'}`"
  />

  <aside
    class="my-3 overflow-auto border-0 sidenav navbar navbar-vertical navbar-expand-xs border-radius-xl"
    :class="`${isRTL ? 'me-3 rotate-caret fixed-end' : 'fixed-start ms-3'}    
      ${
        layout === 'landing' ? 'bg-transparent shadow-none' : ' '
      } ${sidebarType}`"
    id="sidenav-main"
  >
    <div class="sidenav-header">
      <i
        class="top-0 p-3 cursor-pointer fas fa-times text-secondary opacity-5 position-absolute end-0 d-none d-xl-none"
        aria-hidden="true"
        id="iconSidenav"
      ></i>

      <router-link 
        class="m-0 navbar-brand d-flex align-items-center justify-content-center" 
        :to="user?.role === 'super_admin' ? '/super-admin/dashboard' : '/client-area/dashboard'"
      >
        <img
          src="/favicon.png"
          class="navbar-brand-img h-100"
          alt="main_logo"
          style="margin-right: -8px;"
        />
        <span class="ms-1 font-weight-bold" style="font-size: 1.3rem; color: #2dce89;">ayneto</span>
      </router-link>
    </div>

    <hr class="mt-0 horizontal dark" />

    <sidenav-list />
  </aside>
</template>

<style scoped>
#sidenav-main::-webkit-scrollbar {
  display: none;
}
#sidenav-main {
  -ms-overflow-style: none; /* IE and Edge */
  scrollbar-width: none; /* Firefox */
}
</style>

