import "@/assets/vendor/nucleo/css/nucleo.css";
import "@/assets/vendor/@fortawesome/fontawesome-free/css/all.min.css";
import "@/assets/scss/argon.scss";
import globalComponents from "./globalComponents";
import globalDirectives from "./globalDirectives";

export default {
  install(Vue) {
    Vue.use(globalComponents);
    Vue.use(globalDirectives);
  }
};
