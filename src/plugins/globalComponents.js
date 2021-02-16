import Card from "../components/Card";
import StatsCard from "../components/StatsCard";

const GlobalComponents = {
  install(app) {
    app.use(Card);
    app.use(StatsCard);
  },
};

export default GlobalComponents;
