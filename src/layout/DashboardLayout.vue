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
            name: 'Painel Inicial',
            icon: 'ni ni-tv-2 text-primary',
            path: '/dashboard',
          }"
        />

        <sidebar-item
          :link="{
            name: 'Icones',
            icon: 'ni ni-planet text-blue',
            path: '/icons',
          }"
        />
        <sidebar-item
          :link="{
            name: 'Anos Académicos',
            icon: 'ni ni-hat-3 text-blue',
            path: '/icons',
          }"
        />
        <sidebar-item
          :link="{
            name: 'Estudantes',
            icon: 'ni ni-badge text-blue',
            path: '/estudantes',
          }"
        />
        <sidebar-item
          :link="{
            name: 'Docentes',
            icon: 'ni ni-badge text-blue',
            path: '/icons',
          }"
        />
        <sidebar-item
          :link="{
            name: 'Anúncios',
            icon: 'ni ni-notification-70 text-orange',
            path: '/icons',
          }"
        />

        <sidebar-item
          :link="{
            name: 'Maps',
            icon: 'ni ni-pin-3 text-orange',
            path: '/maps',
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
            name: 'Tables',
            icon: 'ni ni-bullet-list-67 text-red',
            path: '/tables',
          }"
        />
        <sidebar-item
          :link="{
            name: 'Login',
            icon: 'ni ni-key-25 text-info',
            path: '/login',
          }"
        />
        <sidebar-item
          :link="{
            name: 'Register',
            icon: 'ni ni-circle-08 text-pink',
            path: '/register',
          }"
        />

        <sidebar-item
          :link="{
            name: 'Área Académica',
            icon: 'ni ni-collection text-blue',
            path: '/register',
          }"
        />
        <sidebar-item
          :link="{
            name: 'Reconfirmação de Matrícula',
            icon: 'ni ni-ruler-pencil text-blue',
            path: '/register',
          }"
        />
        <sidebar-item
          :link="{
            name: 'Eventos',
            icon: 'ni ni-calendar-grid-58 text-red',
            path: '/register',
          }"
        />
        <sidebar-item
          :link="{
            name: 'Programas',
            icon: 'ni ni-single-copy-04 text-info',
            path: '/register',
          }"
        />
        <sidebar-item
          :link="{
            name: 'Horários',
            icon: 'ni ni-calendar-grid-58 text-red',
            path: '/register',
          }"
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
  },
};
</script>
<style lang="scss"></style>
