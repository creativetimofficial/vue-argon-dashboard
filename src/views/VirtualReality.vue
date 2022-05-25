<template>
  <div class="mt-3">
    <navbar
      :minNav="navbarMinimize"
      :toggle="toggleConfigurator"
      :class="`${
        this.$store.state.isNavFixed ? this.$store.state.navbarFixed_class : ''} ${this.$store.state.isNavFixed ? 'bg-white' : 'bg-success'}`
      "
    />
  </div>
  <div
    class="mx-3 mt-4 border-radius-xl position-relative"
    :style="{
      backgroundImage: 'url(' + require('@/assets/img/vr-bg.jpg') + ')',
      backgroundSize: 'cover'
    }"
  >
    <sidenav :custom_class="this.$store.state.mcolor" :class="isTransparent" class="fixed-start" />
    <main class="mt-1 main-content border-radius-lg">
      <div class="section min-vh-85 position-relative transform-scale-0 transform-scale-md-7">
        <div class="container-fluid">
          <div class="pt-10 row">
            <div class="pt-5 text-center col-lg-1 col-md-1 pt-lg-0 ms-lg-5">
              <a
                href="javascript:;"
                class="border-0 avatar avatar-md d-block"
                data-bs-toggle="tooltip"
                data-bs-placement="left"
                title="My Profile"
              >
                <img class="border-radius-lg" alt="Image placeholder" src="@/assets/img/team-1.jpg" />
              </a>
              <button
                class="p-2 mt-2 btn btn-white border-radius-lg d-block"
                type="button"
                data-bs-toggle="tooltip"
                data-bs-placement="left"
                title="Home"
              >
                <i class="p-2 fas fa-home"></i>
              </button>
              <button
                class="p-2 btn btn-white border-radius-lg d-block"
                type="button"
                data-bs-toggle="tooltip"
                data-bs-placement="left"
                title="Search"
              >
                <i class="p-2 fas fa-search"></i>
              </button>
              <button
                class="p-2 btn btn-white border-radius-lg d-block"
                type="button"
                data-bs-toggle="tooltip"
                data-bs-placement="left"
                title="Minimize"
              >
                <i class="p-2 fas fa-ellipsis-h"></i>
              </button>
            </div>
            <div class="col-lg-8 col-md-11">
              <div class="d-flex">
                <div class="me-auto">
                  <h1 class="mb-0 display-1 font-weight-bold mt-n4">12°C</h1>
                  <h6 class="mb-0 text-uppercase ms-1">Cloudy</h6>
                </div>
                <div class="ms-auto">
                  <img
                    class="w-50 float-end mt-lg-n4"
                    src="@/assets/img/small-logos/icon-sun-cloud.png"
                    alt="image sun"
                  />
                </div>
              </div>
              <div class="mt-4 row">
                <div class="col-lg-4 col-md-4">
                  <card-calendar />
                </div>
                <div class="mt-4 col-lg-4 col-md-4 mt-sm-0">
                  <card-to-do />
                  <card-email />
                </div>
                <div class="mt-4 col-lg-4 col-md-4 mt-sm-0">
                  <card-player />
                  <card-message />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
  <app-footer class="py-3 bg-white border-radius-lg" />
</template>

<script>
import Sidenav from "@/examples/Sidenav";
import AppFooter from "@/examples/Footer.vue";
import Navbar from "@/examples/Navbars/Navbar.vue";
import CardCalendar from "./components/CardCalendar.vue";
import CardEmail from "./components/CardEmail.vue";
import CardToDo from "./components/CardToDo.vue";
import CardPlayer from "./components/CardPlayer.vue";
import CardMessage from "./components/CardMessage.vue";
import setTooltip from "@/assets/js/tooltip.js";

const body = document.getElementsByTagName("body")[0];

import { mapMutations } from "vuex";

export default {
  name: "virtual-reality",
  components: {
    AppFooter,
    Sidenav,
    Navbar,
    CardCalendar,
    CardEmail,
    CardToDo,
    CardPlayer,
    CardMessage,
  },
  mounted() {
    setTooltip();
  },
  methods: {
    ...mapMutations(["navbarMinimize", "toggleConfigurator"]),
  },
  beforeMount() {
    this.$store.state.layout = "vr";
    this.$store.state.showNavbar = false;
    this.$store.state.showSidenav = false;
    this.$store.state.showFooter = false;
    body.classList.add("virtual-reality");
    this.$store.state.isTransparent = "bg-white";
  },
  beforeUnmount() {
    this.$store.state.layout = "default";
    this.$store.state.showNavbar = true;
    this.$store.state.showSidenav = true;
    this.$store.state.showFooter = true;
    body.classList.remove("virtual-reality");

    if (this.$store.state.isPinned === false) {
      const sidenav_show = document.querySelector(".g-sidenav-show");
      sidenav_show.classList.remove("g-sidenav-hidden");
      sidenav_show.classList.add("g-sidenav-pinned");
      this.$store.state.isPinned = true;
    }
    this.$store.state.isTransparent = "bg-transparent";
  },
  computed: {
    isTransparent() {
      return this.$store.state.isTransparent;
    },
  },
};
</script>
