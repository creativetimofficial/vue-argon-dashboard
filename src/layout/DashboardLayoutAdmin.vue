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
            name: 'Dashboard',
            icon: 'ni ni-tv-2 text-primary',
            path: '/admin/dashboardAdmin',
          }"
        />
        <sidebar-item
          :link="{
            name: 'User Profile',
            icon: 'ni ni-single-02 text-yellow',
            path: '/profile',
          }"
        />
        <sidebar-item
          :link="{
            name: 'Data Activities',
            icon: 'ni ni-delivery-fast text-primary',
            path: '/tables',
          }"
        />
        <sidebar-item
          :link="{
            name: 'Data Admin',
            icon: 'ni ni-app text-red',
            path: '/admin/adminList',
          }"
        />
        <sidebar-item
          :link="{
            name: 'Data Driver',
            icon: 'ni ni-badge text-blue',
            path: '/admin/driverList',
          }"
        />
        <sidebar-item
          :link="{
            name: 'Data Customer',
            icon: 'ni ni-mobile-button text-red',
            path: '/admin/tables',
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
      localStorage.removeItem("admin_id");
      localStorage.removeItem("admin_email");

     }
  },
};
</script>
<style lang="scss"></style>
