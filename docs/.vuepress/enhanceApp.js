import DemoBlock from './demo-block/demo-block'
import lang from 'element-ui/lib/locale/lang/en';
import locale from 'element-ui/lib/locale';
import getGlobalComponents from './globals'
import ArgonDashboard from '@/plugins/argon-dashboard'
import './doc_styles.scss'
import './argon-docs.css'
import "../../node_modules/flatpickr/dist/flatpickr.css";
import flatPicker from "../../node_modules/vue-flatpickr-component";
import getElements from './utils/get-sidebar-elements';
import BootstrapVue from '../../node_modules/bootstrap-vue';

let Components = getGlobalComponents()
export default ({
                  Vue, // the version of Vue being used in the VuePress app
                  options, // the options for the root Vue instance
                  router, // the router instance for the app
                  siteData
                }) => {
  locale.use(lang);
  Vue.use(ArgonDashboard)
  Vue.use(BootstrapVue)
  Vue.component('flat-picker', flatPicker);
  Vue.component('demo-block', DemoBlock);
  let componentEntries = Object.entries(Components);
  for(let [name, component] of componentEntries) {
    Vue.component(component.name || name, component)
  }
  Vue.prototype.$docs = Object.values(Components);
  let docComponents = getElements(componentEntries);
  siteData.themeConfig.sidebar = siteData.themeConfig.sidebar.concat(docComponents);
}
