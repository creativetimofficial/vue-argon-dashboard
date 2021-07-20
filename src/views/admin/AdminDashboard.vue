<template>
  <div>
    <base-header type="gradient-success" class="pb-6 pb-8 pt-5 pt-md-8">
      <div class="row mb-2">
        <div class="col-lg-7 col-md-10">
          <h1 class="display-2 text-white">Reports</h1>
        </div>
      </div>

      <div class="row">
        <div class="col-xl-3 col-lg-6">
          <stats-card
            title="Total Order Today"
            type="gradient-red"
            sub-title="350,897"
            icon="ni ni-active-40"
            class="mb-4 mb-xl-0"
          >
          </stats-card>
        </div>
        <div class="col-xl-3 col-lg-6">
          <stats-card
            title="Total Order this Week"
            type="gradient-orange"
            sub-title="2,356"
            icon="ni ni-chart-pie-35"
            class="mb-4 mb-xl-0"
          >
          </stats-card>
        </div>
        <div class="col-xl-3 col-lg-6">
          <stats-card
            title="Total Order this Month"
            type="gradient-green"
            sub-title="924"
            icon="ni ni-money-coins"
            class="mb-4 mb-xl-0"
          >
          </stats-card>
        </div>
        <div class="col-xl-3 col-lg-6">
          <stats-card
            title="New Customer this Month"
            type="gradient-info"
            sub-title="49,65%"
            icon="ni ni-chart-bar-32"
            class="mb-4 mb-xl-0"
          >
          </stats-card>
        </div>
      </div>
    </base-header>

    <div class="container-fluid mt--7">
      <!--Charts-->
      <div class="row">
        <div class="col-xl-8 mb-5 mb-xl-0">
          <card header-classes="bg-transparent">
            <template v-slot:header>
              <div class="row align-items-center">
                <div class="col">
                  <h6 class="text-uppercase text-muted ls-1 mb-1">
                    Performance
                  </h6>
                  <h5 class="h3 mb-0">Total orders</h5>
                </div>
              </div>
            </template>
            <div class="chart-area">
              <canvas :height="350" :id="ordersChartID"></canvas>
            </div>
          </card>
        </div>
        <!--Tables-->
        <div class="col-xl-4">
          <social-traffic-table></social-traffic-table>
        </div>
      </div>
      <!-- End charts-->

      
    </div>
  </div>
</template>
<script>
// Charts
import { ordersChart } from "@/components/Charts/Chart";
// import Chart from "chart.js";

import SocialTrafficTable from "./Dashboard/SocialTrafficTable";
// let chart;

export default {
  components: {
    SocialTrafficTable,
  },
  data() {
    return {
      ordersChartID: "ordersChart"
    };
  },
  methods: {
  },
  mounted() {
    ordersChart.createChart(this.ordersChartID);
  },
};
</script>
<style></style>
