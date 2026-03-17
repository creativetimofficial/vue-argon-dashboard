import { createApp } from "vue";
import { createPinia } from "pinia";
import App from "./App.vue";
import store from "./store";
import router from "./router";
import "./assets/css/nucleo-icons.css";
import "./assets/css/nucleo-svg.css";
import "./assets/css/checkbox-fix.css";
import "./assets/css/swal-custom.css";
import ArgonDashboard from "./argon-dashboard";
import i18n from "./i18n";

// Load Sneat Bootstrap JS for ISP Admin
// This will be loaded globally but only used in ISP Admin routes
if (typeof window !== 'undefined') {
  // Bootstrap is loaded from Sneat assets in ISPAdminLayout.vue
  // We just need to ensure it's available globally
}

const pinia = createPinia();
const appInstance = createApp(App);
appInstance.use(pinia);
appInstance.use(store);
appInstance.use(router);
appInstance.use(i18n);
appInstance.use(ArgonDashboard);
appInstance.mount("#app");
