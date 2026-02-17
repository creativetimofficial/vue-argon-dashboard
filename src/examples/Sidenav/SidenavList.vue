<script setup>
import { computed, ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import { useStore } from "vuex";

import SidenavItem from "./SidenavItem.vue";
import SidenavCard from "./SidenavCard.vue";

const store = useStore();
const route = useRoute();
const isRTL = computed(() => store.state.isRTL);
const user = ref(null);

onMounted(() => {
  // Check localStorage first (Remember Me = true)
  let userStr = localStorage.getItem("user");
  
  // If not in localStorage, check sessionStorage (Remember Me = false)
  if (!userStr) {
    userStr = sessionStorage.getItem("user");
  }
  
  if (userStr) {
    user.value = JSON.parse(userStr);
    console.log("Sidebar - User loaded:", user.value); // Debug log
  } else {
    console.warn("Sidebar - No user found in storage"); // Debug log
  }
});

const getRoute = () => {
  const routeArr = route.path.split("/");
  return routeArr[1];
};

// Menu items based on role
const superAdminMenu = [
  {
    to: "/super-admin/dashboard",
    icon: "ni ni-tv-2 text-primary",
    text: "Dashboard",
  },
  {
    to: "/super-admin/isp-management",
    icon: "ni ni-building text-success",
    text: "ISP Management",
  },
  {
    to: "/super-admin/isp-orders",
    icon: "ni ni-cart text-info",
    text: "ISP Orders",
  },
  {
    to: "/super-admin/servers",
    icon: "ni ni-world-2 text-primary",
    text: "Server Manager",
  },
  {
    to: "/super-admin/subscription-packages",
    icon: "ni ni-box-2 text-info",
    text: "Subscription Packages",
  },
  {
    to: "/super-admin/payment-gateways",
    icon: "ni ni-credit-card text-warning",
    text: "Payment Gateways",
  },
  {
    to: "/super-admin/withdrawals",
    icon: "ni ni-money-coins text-success",
    text: "Withdrawals",
  },
  {
    to: "/super-admin/landing-page-editor",
    icon: "ni ni-palette text-danger",
    text: "Landing Page Editor",
  },
];

const ispAdminMenu = [
  {
    to: "/isp-admin/dashboard",
    icon: "ni ni-tv-2 text-primary",
    text: "Dashboard",
  },
  {
    to: "/isp-admin/customers",
    icon: "ni ni-single-02 text-success",
    text: "Customers",
  },
  {
    to: "/isp-admin/billing",
    icon: "ni ni-credit-card text-warning",
    text: "Billing",
  },
];

const technicianMenu = [
  {
    to: "/technician/dashboard",
    icon: "ni ni-tv-2 text-primary",
    text: "Dashboard",
  },
];

const customerMenu = [
  {
    to: "/customer/dashboard",
    icon: "ni ni-tv-2 text-primary",
    text: "Dashboard",
  },
  {
    to: "/customer/billing",
    icon: "ni ni-credit-card text-success",
    text: "My Billing",
  },
];

const clientAreaMenu = [
  {
    to: "/client-area/dashboard",
    icon: "ni ni-tv-2 text-primary",
    text: "Dashboard",
  },
  {
    to: "/client-area/orders",
    icon: "ni ni-cart text-info",
    text: "Orders",
  },
  {
    to: "/client-area/services",
    icon: "ni ni-app text-success",
    text: "Services",
  },
  {
    to: "/client-area/invoices",
    icon: "ni ni-paper-diploma text-warning",
    text: "Invoices",
  },
  {
    to: "/client-area/topup",
    icon: "ni ni-credit-card text-danger",
    text: "Topup Balance",
  },
];

const menuItems = computed(() => {
  if (route.path.startsWith('/client-area')) {
    return clientAreaMenu;
  }

  if (!user.value) return [];

  switch (user.value.role) {
    case "super_admin":
      return superAdminMenu;
    case "isp_admin":
      return ispAdminMenu;
    case "technician":
      return technicianMenu;
    case "customer":
      return customerMenu;
    default:
      return [];
  }
});

const logout = () => {
  if (confirm("Yakin ingin logout?")) {
    localStorage.removeItem("auth_token");
    localStorage.removeItem("user");
    window.location.href = "/login";
  }
};
</script>
<template>
  <div
    class="collapse navbar-collapse w-auto h-auto h-100"
    id="sidenav-collapse-main"
  >
    <ul class="navbar-nav">
      <!-- Dynamic menu items based on user role -->
      <li v-for="item in menuItems" :key="item.to" class="nav-item">
        <sidenav-item
          :to="item.to"
          :class="$route.path === item.to ? 'active' : ''"
          :navText="item.text"
        >
          <template v-slot:icon>
            <i :class="item.icon + ' text-sm opacity-10'"></i>
          </template>
        </sidenav-item>
      </li>

      <!-- Account Pages Section -->
      <li class="mt-3 nav-item">
        <h6
          class="text-xs ps-4 text-uppercase font-weight-bolder opacity-6"
          :class="isRTL ? 'me-4' : 'ms-2'"
        >
          {{ isRTL ? "صفحات المرافق" : "ACCOUNT" }}
        </h6>
      </li>

      <li class="nav-item">
        <sidenav-item
          :to="
            user?.role === 'super_admin'
              ? '/super-admin/profile'
              : route.path.startsWith('/client-area')
                ? '/client-area/profile'
                : user?.role === 'isp_admin'
                  ? '/isp-admin/profile'
                  : user?.role === 'technician'
                    ? '/technician/profile'
                    : '/customer/profile'
          "
          :class="getRoute() === 'profile' ? 'active' : ''"
          :navText="isRTL ? 'حساب تعريفي' : 'Profile'"
        >
          <template v-slot:icon>
            <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
          </template>
        </sidenav-item>
      </li>

      <li class="nav-item">
        <a href="#" class="nav-link" @click.prevent="logout">
          <div
            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center"
          >
            <i class="ni ni-button-power text-danger text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Logout</span>
        </a>
      </li>
    </ul>
  </div>

  <div
    class="pt-3 mx-3 mt-3 sidenav-footer"
    v-if="user?.role === 'super_admin'"
  >
    <sidenav-card
      :card="{
        title: 'Super Admin',
        description: 'Manage ISP Billing Platform',
        links: [],
      }"
    />
  </div>
</template>

<style scoped>
#sidenav-collapse-main::-webkit-scrollbar {
  display: none;
}
#sidenav-collapse-main {
  -ms-overflow-style: none; /* IE and Edge */
  scrollbar-width: none; /* Firefox */
}
</style>
