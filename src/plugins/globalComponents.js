import BaseButton from "../components/BaseButton";
import BaseHeader from "../components/BaseHeader";
import BaseInput from "../components/BaseInput";
import BaseNav from "../components/BaseNav";
import BaseProgress from "../components/BaseProgress";
import BaseTable from "../components/BaseTable";
import Card from "../components/Card";
import StatsCard from "../components/StatsCard";

const GlobalComponents = {
  install(app) {
    app.component("base-button", BaseButton);
    app.component("base-header", BaseHeader);
    app.component("base-input", BaseInput);
    app.component("base-nav", BaseNav);
    app.component("base-progress", BaseProgress);
    app.component("base-table", BaseTable);
    app.component("card", Card);
    app.component("stats-card", StatsCard);
  }
};

export default GlobalComponents;
