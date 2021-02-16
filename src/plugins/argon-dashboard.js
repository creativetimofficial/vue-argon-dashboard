import GlobalComponents from "./globalComponents";

import "@/assets/vendor/nucleo/css/nucleo.css";
import "@/assets/scss/argon.scss";
// import SidebarPlugin from "@/components/SidebarPlugin/index"

export default {
  install(app) {
    app.use(GlobalComponents);
  },
};
