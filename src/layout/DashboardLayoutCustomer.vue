<template>
  <div class="wrapper" :class="{ 'nav-open': $sidebar.showSidebar }">
    <side-bar
      :background-color="sidebarBackground"
      short-title="Argon"
      title="Argon"
    >
      <template v-slot:links>
        <sidebar-item
          :link="{
            name: 'User Profile',
            icon: 'ni ni-single-02 text-yellow',
            path: '/Customerprofile',
          }"
        />
        <sidebar-item
          :link="{
            name: 'Order',
            icon: 'ni ni-tv-2 text-primary',
            path: '/cust/Order',
          }"
        />

        <sidebar-item
          :link="{
            name: 'Activity History',
            icon: 'ni ni-planet text-blue',
              path: '/cust/ActivityHistory',
          }"
        />
        <sidebar-item
          :link="{
            name: 'Logout',
            icon: 'ni ni-user-run text-black',
            path: '/login'
          }"
          @click= "doLogout"

        />

      </template>
    </side-bar>
    <div class="main-content" :data="sidebarBackground">
      <dashboard-navbar></dashboard-navbar>

      <div @click="toggleSidebar">
        <!-- your content here -->
        <router-view></router-view>
        <content-footer v-if="!$route.meta.hideFooter"></content-footer>
      </div>
    </div>
  </div>
</template>
<script>
import DashboardNavbar from "./DashboardNavbar.vue";
import ContentFooter from "./ContentFooter.vue";

export default {
  components: {
    DashboardNavbar,
    ContentFooter,
  },
  data() {
    return {
      sidebarBackground: "vue", //vue|blue|orange|green|red|primary
    };
  },
  methods: {
    toggleSidebar() {
      if (this.$sidebar.showSidebar) {
        this.$sidebar.displaySidebar(false);
      }
    },
    doLogout: function(){
      localStorage.removeItem("customer_id");
      localStorage.removeItem("customer_email");

     }
  },
};
</script>
<style lang="scss"></style>
