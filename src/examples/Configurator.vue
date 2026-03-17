<script setup>
import { computed } from "vue";
import { useStore } from "vuex";
import { activateDarkMode, deactivateDarkMode } from "@/assets/js/dark-mode";

const store = useStore();
// state
const isRTL = computed(() => store.state.isRTL);
const isNavFixed = computed(() => store.state.isNavFixed);
const sidebarType = computed(() => store.state.sidebarType);
const toggleConfigurator = () => store.commit("toggleConfigurator");

// mutations
const navbarFixed = () => {
  store.commit("navbarFixed");
  localStorage.setItem("navbarFixed", store.state.isNavFixed ? "true" : "false");
};
const setSidebarType = (type) => store.commit("sidebarType", type);

const sidebarColor = (color = "success") => {
  const sidenav = document.querySelector("#sidenav-main");
  if (sidenav) {
    sidenav.setAttribute("data-color", color);
    localStorage.setItem("sidebarColor", color);
  }
};

const setSidebarTypePersist = (type) => {
  setSidebarType(type);
  store.commit("sidebarType", type);
  localStorage.setItem("sidebarType", type);
  // Terapkan langsung ke elemen sidenav jika ada
  const sidenav = document.querySelector("#sidenav-main");
  if (sidenav) {
    sidenav.classList.remove("bg-white", "bg-default");
    sidenav.classList.add(type);
  }
};

const darkMode = () => {
  if (store.state.darkMode) {
    store.state.darkMode = false;
    setSidebarTypePersist("bg-white");
    deactivateDarkMode();
    localStorage.setItem("darkMode", "false");
    return;
  } else {
    store.state.darkMode = true;
    setSidebarTypePersist("bg-default");
    activateDarkMode();
    localStorage.setItem("darkMode", "true");
  }
};

import { onMounted } from "vue";
onMounted(() => {
  // Restore sidebar color
  const color = localStorage.getItem("sidebarColor");
  if (color) sidebarColor(color);
  // Restore sidebar type
  const type = localStorage.getItem("sidebarType");
  if (type) {
    setSidebarType(type);
    store.commit("sidebarType", type);
    const sidenav = document.querySelector("#sidenav-main");
    if (sidenav) {
      sidenav.classList.remove("bg-white", "bg-default");
      sidenav.classList.add(type);
    }
  }
  // Restore dark mode
  const dark = localStorage.getItem("darkMode");
  if (dark === "true") {
    store.state.darkMode = true;
    setSidebarType("bg-default");
    activateDarkMode();
  } else if (dark === "false") {
    store.state.darkMode = false;
    setSidebarType("bg-white");
    deactivateDarkMode();
  }
  // Restore navbar fixed
  const navFixed = localStorage.getItem("navbarFixed");
  if (navFixed === "true") {
    store.commit("navbarFixed");
  } else if (navFixed === "false") {
    if (store.state.isNavFixed) store.commit("navbarFixed");
  }
});

</script>
<template>
  <div class="fixed-plugin">
    <div class="shadow-lg card">
      <div class="pt-3 pb-0 bg-transparent card-header">
        <div class="" :class="isRTL ? 'float-end' : 'float-start'">
          <h5 class="mt-3 mb-0">Tema Konfigurasi</h5>
          <p>See our dashboard options.</p>
        </div>
        <div
          class="mt-4"
          @click="toggleConfigurator"
          :class="isRTL ? 'float-start' : 'float-end'"
        >
          <button class="p-0 btn btn-link text-dark fixed-plugin-close-button">
            <i class="fa fa-close"></i>
          </button>
        </div>
        <!-- End Toggle Button -->
      </div>
      <hr class="my-1 horizontal dark" />
      <div class="pt-0 card-body pt-sm-3">
        <!-- Sidebar Backgrounds -->
        <div>
          <h6 class="mb-0">Sidebar Colors</h6>
        </div>
        <a href="#" class="switch-trigger background-color">
          <div
            class="my-2 badge-colors"
            :class="isRTL ? 'text-end' : ' text-start'"
          >
            <span
              class="badge filter bg-gradient-primary active"
              data-color="primary"
              @click="sidebarColor('primary')"
            ></span>
            <span
              class="badge filter bg-gradient-dark"
              data-color="dark"
              @click="sidebarColor('dark')"
            ></span>
            <span
              class="badge filter bg-gradient-info"
              data-color="info"
              @click="sidebarColor('info')"
            ></span>
            <span
              class="badge filter bg-gradient-success"
              data-color="success"
              @click="sidebarColor('success')"
            ></span>
            <span
              class="badge filter bg-gradient-warning"
              data-color="warning"
              @click="sidebarColor('warning')"
            ></span>
            <span
              class="badge filter bg-gradient-danger"
              data-color="danger"
              @click="sidebarColor('danger')"
            ></span>
          </div>
        </a>
        <!-- Sidenav Type -->
        <div class="mt-3">
          <h6 class="mb-0">Sidenav Type</h6>
          <p class="text-sm">Choose between 2 different sidenav types.</p>
        </div>
        <div class="d-flex gap-2">
          <button
            id="btn-white"
            class="btn w-100 px-3 mb-2"
            :class="sidebarType === 'bg-white' ? 'btn-success text-white' : 'btn-outline-success'"
            @click="setSidebarTypePersist('bg-white')"
          >
            White
          </button>
          <button
            id="btn-dark"
            class="btn w-100 px-3 mb-2"
            :class="sidebarType === 'bg-default' ? 'btn-success text-white' : 'btn-outline-success'"
            @click="setSidebarTypePersist('bg-default')"
          >
            Dark
          </button>
        </div>
        <p class="mt-2 text-sm d-xl-none d-block">
          You can change the sidenav type just on desktop view.
        </p>


        <hr class="horizontal dark my-4" />
        <div class="mt-2 mb-5 d-flex">
          <h6 class="mb-0" :class="isRTL ? 'ms-2' : ''">Light / Dark</h6>
          <div class="form-check form-switch ps-0 ms-auto my-auto">
            <input
              class="form-check-input mt-1 ms-auto"
              type="checkbox"
              :checked="store.state.darkMode"
              @click="darkMode"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
