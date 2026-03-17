<script setup>
import { computed, ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import { useStore } from "vuex";
import api from "@/services/api";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

import SidenavItem from "./SidenavItem.vue";
import SidenavCard from "./SidenavCard.vue";
import { confirm } from "@/utils/notify";

const store = useStore();
const route = useRoute();
const isRTL = computed(() => store.state.isRTL);
const user = ref(null);

const fetchDocumentationSettings = async () => {
  try {
    const res = await api.get('/system/documentation');
    if (res.data.settings) {
      store.commit('setDocumentationSettings', res.data.settings);
    }
  } catch (err) {
    console.error('Failed to fetch documentation settings', err);
  }
};

onMounted(() => {
  fetchDocumentationSettings();
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
    text: "nav.dashboard",
  },
  {
    to: "/super-admin/isp-management",
    icon: "ni ni-building text-success",
    text: "nav.isp_unit_management",
  },
  {
    to: "/super-admin/isp-orders",
    icon: "ni ni-cart text-info",
    text: "nav.incoming_orders",
  },
  {
    to: "/super-admin/servers",
    icon: "ni ni-world-2 text-primary",
    text: "nav.server_management",
  },
  {
    to: "/super-admin/subscription-packages",
    icon: "ni ni-box-2 text-info",
    text: "nav.subscription_packages",
  },
  {
    to: "/super-admin/payment-gateways",
    icon: "ni ni-credit-card text-warning",
    text: "nav.payment_gateways",
  },
  {
    to: "/super-admin/withdrawals",
    icon: "ni ni-money-coins text-success",
    text: "nav.withdrawal_requests",
  },
  {
    to: "/super-admin/landing-page-editor",
    icon: "ni ni-palette text-danger",
    text: "nav.main_page_editor",
  },
  {
    to: "/super-admin/system-settings",
    icon: "ni ni-settings-gear-65 text-secondary",
    text: "nav.system_settings",
  },
];

const ispAdminMenu = [
  {
    to: "/isp-admin/dashboard",
    icon: "bx bx-home-circle text-primary",
    text: "nav.home",
  },
  {
    to: "/isp-admin/customers",
    icon: "bx bx-user text-success",
    text: "nav.customers",
  },
  {
    to: "/isp-admin/mikrotik",
    icon: "bx bx-broadcast text-primary",
    text: "nav.mikrotik_management",
    requiredFeature: "feature_realtime_monitoring",
  },
  {
    to: "/isp-admin/packages",
    icon: "bx bx-package text-info",
    text: "nav.internet_packages",
  },
  {
    to: "/isp-admin/billing",
    icon: "bx bx-credit-card text-warning",
    text: "nav.billing",
  },
  {
    to: "/isp-admin/tickets",
    icon: "bx bx-chat text-danger",
    text: "nav.support_tickets",
  },
  {
    to: "/isp-admin/reports",
    icon: "bx bx-bar-chart-alt-2 text-success",
    text: "nav.reports",
    requiredFeature: "feature_analytics",
  },
  {
    to: "/isp-admin/users",
    icon: "bx bx-user-check text-dark",
    text: "nav.user_management",
    requiredFeature: "multi_user_access",
  },
  {
    to: "/isp-admin/settings",
    icon: "bx bx-cog text-secondary",
    text: "nav.settings",
  },
];

const technicianMenu = [
  {
    to: "/technician/dashboard",
    icon: "ni ni-tv-2 text-primary",
    text: "nav.dashboard",
  },
];

const customerMenu = [
  {
    to: "/customer/dashboard",
    icon: "ni ni-tv-2 text-primary",
    text: "nav.home",
  },
  {
    to: "/customer/billing",
    icon: "ni ni-credit-card text-success",
    text: "nav.my_invoices",
  },
];

const clientAreaMenu = [
  {
    to: "/client-area/dashboard",
    icon: "ni ni-tv-2 text-primary",
    text: "nav.dashboard",
  },
  {
    to: "/client-area/orders",
    icon: "ni ni-cart text-info",
    text: "nav.buy_add_unit",
  },
  {
    to: "/client-area/services",
    icon: "ni ni-app text-success",
    text: "nav.services",
  },
  {
    to: "/client-area/invoices",
    icon: "ni ni-paper-diploma text-warning",
    text: "nav.invoices_history",
  },
  {
    to: "/client-area/topup",
    icon: "ni ni-credit-card text-danger",
    text: "nav.balance",
  },
];

const menuItems = computed(() => {
  if (route.path.startsWith('/client-area')) {
    return clientAreaMenu;
  }

  if (!user.value) return [];

  let items = [];
  switch (user.value.role) {
    case "super_admin":
      items = superAdminMenu;
      break;
    case "isp_admin":
      items = ispAdminMenu;
      break;
    case "technician":
      items = technicianMenu;
      break;
    case "customer":
      items = customerMenu;
      break;
    default:
      return [];
  }

  // Filter based on features if ISP admin
  if (user.value.role === "isp_admin" && user.value.isp && user.value.isp.package) {
    const pkg = user.value.isp.package;
    return items.filter(item => {
      if (!item.requiredFeature) return true;
      return pkg[item.requiredFeature] === true || pkg[item.requiredFeature] === 1;
    });
  }

  return items;
});

const logout = async () => {
  if (await confirm(t("common.are_you_sure_logout"))) {
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
          :navText="$t(item.text)"
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
          {{ $t('nav.my_account') }}
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
          :navText="$t('nav.profile')"
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
          <span class="nav-link-text ms-1">{{ $t('nav.logout') }}</span>
        </a>
      </li>
    </ul>
  </div>

  <div
    class="pt-3 mx-3 mt-3 sidenav-footer"
    v-if="user"
  >
    <sidenav-card
      :card="{
        title: user?.role === 'super_admin' ? 'Super Admin' : 'Client Area',
        description: 'Need help or docs?',
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
