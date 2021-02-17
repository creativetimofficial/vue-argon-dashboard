import BaseHeader from "../components/BaseHeader";
import BaseNav from "../components/BaseNav";
import BaseTable from "../components/BaseTable";
import Card from "../components/Card";
import StatsCard from "../components/StatsCard";

const GlobalComponents = {
  install(app) {
    app.component("base-header", BaseHeader);
    app.component("base-nav", BaseNav);
    app.component("base-table", BaseTable);
    app.component("card", Card);
    app.component("stats-card", StatsCard);
  }
};

export default GlobalComponents;
