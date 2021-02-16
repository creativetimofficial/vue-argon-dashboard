import BaseHeader from "../components/BaseHeader";
import Card from "../components/Card";
import StatsCard from "../components/StatsCard";

const GlobalComponents = {
  install(app) {
    app.component("base-header", BaseHeader);
    app.component("card", Card);
    app.component("stats-card", StatsCard);
  },
};

export default GlobalComponents;
