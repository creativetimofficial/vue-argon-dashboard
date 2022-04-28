<template>
  <div class="main-content bg-default">
    <!-- Navbar -->
   
    <!-- Header -->
    <div class="header bg-gradient-success py-7 py-lg-8">
      <div class="container">
        <div class="header-body text-center mb-7">
          <div class="row justify-content-center">
            <div class="col-lg-5 col-md-6">
              <h1 class="text-white">SIGISA_ISPB</h1>
              <p class="text-lead text-white">
                Use these awesome forms to login or create new account in your
                project for free.
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class="separator separator-bottom separator-skew zindex-100">
        <svg
          x="0"
          y="0"
          viewBox="0 0 2560 100"
          preserveAspectRatio="none"
          version="1.1"
          xmlns="http://www.w3.org/2000/svg"
        >
          <polygon
            class="fill-default"
            points="2560 0 2560 100 0 100"
          ></polygon>
        </svg>
      </div>
    </div>
    <!-- Page content -->
    <div class="container mt--9 pb-5">
      <router-view></router-view>
    </div>
    
  </div>
</template>
<script>
export default {
  name: "auth-layout",
  data() {
    return {
      year: new Date().getFullYear(),
      showMenu: false,
    };
  },
};
</script>
<style></style>
