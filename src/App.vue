<!--
=========================================================
* Vue Argon Dashboard 2 - v4.0.0
=========================================================

* Product Page: https://creative-tim.com/product/vue-argon-dashboard
* Copyright 2024 Creative Tim (https://www.creative-tim.com)

Coded by www.creative-tim.com

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<script setup>
import { computed, watch } from "vue";
import { useStore } from "vuex";
import { useRoute } from "vue-router";
import Sidenav from "./examples/Sidenav";
import Configurator from "@/examples/Configurator.vue";
import Navbar from "@/examples/Navbars/Navbar.vue";
import AppFooter from "@/examples/Footer.vue";

const store = useStore();
const route = useRoute();

// Check if current route is a public page (no sidebar/navbar needed)
const isPublicPage = computed(() => {
  const publicPaths = ["/", "/login", "/register", "/verify-email", "/verify-success", "/payment-callback", "/client-area-package"];
  const publicRoutes = ["Home", "Login", "Register", "VerifyEmail", "VerificationSuccess", "PaymentCallback", "ClientAreaPackage"];

  return publicPaths.includes(route.path) || publicRoutes.includes(route.name);
});

// Check if current route uses ClientAreaLayout
const isClientArea = computed(() => {
  return route.path.startsWith('/client-area') && route.path !== '/client-area-package';
});

// Watch for route changes and update store accordingly
watch(
  () => route.path,
  (newPath) => {
    const publicPaths = ["/", "/login", "/register"];
    const isPublic = publicPaths.includes(newPath);

    // Hide sidebar/navbar/footer for public pages
    store.state.showSidenav = !isPublic;
    store.state.showNavbar = !isPublic;
    store.state.showFooter = !isPublic;

    // IMPORTANT: Force 'default' layout for Client Area to show the green header background
    if (newPath.startsWith('/client-area')) {
      store.state.layout = 'default';
    }
  },
  { immediate: true },
);

const isNavFixed = computed(() => store.state.isNavFixed);
const darkMode = computed(() => store.state.darkMode);
const isAbsolute = computed(() => store.state.isAbsolute);
const showSidenav = computed(() => store.state.showSidenav);
const layout = computed(() => store.state.layout);
const showNavbar = computed(() => store.state.showNavbar);
const showFooter = computed(() => store.state.showFooter);
const showConfig = computed(() => store.state.showConfig);
const hideConfigButton = computed(() => store.state.hideConfigButton);
const toggleConfigurator = () => store.commit("toggleConfigurator");

const navClasses = computed(() => {
  return {
    "position-sticky bg-white left-auto top-2 z-index-sticky":
      isNavFixed.value && !darkMode.value,
    "position-sticky bg-default left-auto top-2 z-index-sticky":
      isNavFixed.value && darkMode.value,
    "position-absolute px-4 mx-0 w-100 z-index-2": isAbsolute.value,
    "px-0 mx-4": !isAbsolute.value,
  };
});
</script>
<template>
  <!-- Public Pages Layout (No Sidebar/Navbar) -->
  <div v-if="isPublicPage" class="public-layout">
    <router-view />
  </div>

  <!-- Authenticated Layout (With Sidebar & Navbar) -->
  <div v-else>
    <div
      v-show="layout === 'landing'"
      class="landing-bg h-100 bg-gradient-primary position-fixed w-100"
    ></div>

    <sidenav v-if="showSidenav" />

    <main
      class="main-content position-relative max-height-vh-100 h-100 border-radius-lg"
    >
      <navbar :class="[navClasses]" v-if="showNavbar" />

      <router-view />

      <app-footer v-show="showFooter" />

      <configurator
        :toggle="toggleConfigurator"
        :class="[showConfig ? 'show' : '', hideConfigButton ? 'd-none' : '']"
      />
    </main>
  </div>
</template>

<style scoped>
.public-layout {
  min-height: 100vh;
  width: 100%;
  overflow-x: hidden;
  overflow-y: auto;
}
</style>
