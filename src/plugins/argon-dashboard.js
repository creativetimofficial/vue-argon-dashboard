import GlobalComponents from "./globalComponents";
import GlobalDirectives from "./globalDirectives";

import "../assets/vendor/nucleo/css/nucleo.css";
import "../assets/vendor/@fortawesome/fontawesome-free/css/all.min.css";
import "../assets/scss/argon.scss";
import SidebarPlugin from "../components/SidebarPlugin";

export default {
  install(app) {
    app.use(SidebarPlugin);
    app.use(GlobalComponents);
    app.use(GlobalDirectives);
  },
};
