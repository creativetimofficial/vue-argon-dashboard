<template>
  <!-- Navbar -->
  <nav
    class="navbar navbar-expand-lg top-0 z-index-3 position-absolute mt-4"
    :class="isBlur ? isBlur : 'shadow-none my-2 navbar-transparent w-100'"
  >
    <div class="container ps-2 pe-0">
      <router-link
        class="navbar-brand font-weight-bolder ms-lg-0 ms-3"
        :class="darkMode ? 'text-black' : 'text-white'"
        to="/"
        >Argon Dashboard 2</router-link
      >
      <button
        class="shadow-none navbar-toggler ms-2"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navigation"
        aria-controls="navigation"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="mt-2 navbar-toggler-icon">
          <span class="navbar-toggler-bar bar1"></span>
          <span class="navbar-toggler-bar bar2"></span>
          <span class="navbar-toggler-bar bar3"></span>
        </span>
      </button>
      <div class="collapse navbar-collapse" id="navigation">
        <ul class="navbar-nav mx-auto">
          <li class="nav-item">
            <router-link
              class="nav-link d-flex align-items-center me-2 active"
              aria-current="page"
              to="/dashboard-default"
            >
              <i
                class="fa fa-chart-pie opacity-6 me-1"
                aria-hidden="true"
                :class="isBlur ? 'text-dark' : 'text-white'"
              ></i>
              Dashboard
            </router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link me-2" to="/profile">
              <i
                class="fa fa-user opacity-6 me-1"
                aria-hidden="true"
                :class="isBlur ? 'text-dark' : 'text-white'"
              ></i>
              Profile
            </router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link me-2" to="/signup">
              <i
                class="fas fa-user-circle opacity-6 me-1"
                aria-hidden="true"
                :class="isBlur ? 'text-dark' : 'text-white'"
              ></i>
              Sign Up
            </router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link me-2" to="/signin">
              <i
                class="fas fa-key opacity-6 me-1"
                aria-hidden="true"
                :class="isBlur ? 'text-dark' : 'text-white'"
              ></i>
              Sign In
            </router-link>
          </li>
        </ul>
        <ul class="navbar-nav d-lg-block d-none">
          <li class="nav-item">
            <a
              href="https://www.creative-tim.com/product/vue-argon-dashboard"
              class="btn btn-sm mb-0 me-1"
              :class="isBtn"
              >Free download</a
            >
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <!-- End Navbar -->
</template>

<script>
import downArrWhite from "@/assets/img/down-arrow-white.svg";
import downArrBlack from "@/assets/img/down-arrow-dark.svg";

export default {
  name: "navbar",
  data() {
    return {
      downArrWhite,
      downArrBlack,
    };
  },
  props: {
    btnBackground: String,
    isBlur: String,
    isBtn: { type: String, default: "bg-gradient-light" },
    darkMode: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    darkModes() {
      return {
        "text-dark": this.darkMode,
      };
    },
  },
};
</script>
