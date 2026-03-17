import { createApp } from 'vue';
import AppISP from './AppISP.vue';
import { createPinia } from 'pinia';
import router from './router/isp';
import i18n from './i18n';

// Vuetify plugin
import vuetifyPlugin from './plugins/vuetify-isp.js';

// Sneat core styles
import '../sneat-vuetify-vuejs-admin-template-free/javascript-version/src/@core/scss/template/index.scss';
import '../sneat-vuetify-vuejs-admin-template-free/javascript-version/src/@layouts/styles/index.scss';
import '../sneat-vuetify-vuejs-admin-template-free/javascript-version/src/assets/styles/styles.scss';

// Create Vue application for ISP Admin
const app = createApp(AppISP);

// Register base Vue plugins
app.use(createPinia());
app.use(router);
app.use(i18n);

// Register Sneat Vuetify Plugin (which loads icons, themes, and defaults)
vuetifyPlugin(app);

// Mount the app to the isp-admin.html DOM
app.mount('#app');
