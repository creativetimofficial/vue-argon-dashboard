import Chart from 'chart.js';
import { initGlobalOptions } from "./config";
export default {
  mounted() {
    initGlobalOptions(Chart);
  }
}
