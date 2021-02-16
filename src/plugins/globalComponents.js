import Card from "../components/Card";
import StatsCard from "../components/StatsCard";

const GlobalComponents = {
  install(app) {
    app.component("card", Card);
    app.component("stats-card", StatsCard);
  },
};

export default GlobalComponents;
