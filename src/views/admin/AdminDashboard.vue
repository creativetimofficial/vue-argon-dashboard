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
            :sub-title="totalorder_day"
            icon="ni ni-active-40"
            class="mb-4 mb-xl-0"
          >
          </stats-card>
        </div>
        <div class="col-xl-3 col-lg-6">
          <stats-card
            title="Total Order this Week"
            type="gradient-orange"
            :sub-title="totalorder_week"
            icon="ni ni-chart-pie-35"
            class="mb-4 mb-xl-0"
          >
          </stats-card>
        </div>
        <div class="col-xl-3 col-lg-6">
          <stats-card
            title="Total Order this Month"
            type="gradient-green"
            :sub-title="totalorder_month"
            icon="ni ni-money-coins"
            class="mb-4 mb-xl-0"
          >
          </stats-card>
        </div>
        <div class="col-xl-3 col-lg-6">
          <stats-card
            title="New Customer this Month"
            type="gradient-info"
            :sub-title="totalnewcust_month"
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

                <base-dropdown>
                  <template v-slot:title>
                    <base-button type="secondary" class="dropdown-toggle">
                      {{ year }}
                    </base-button>
                  </template>
                  <a class="dropdown-item" @click="allActivityAction"
                    >All Activity</a
                  >
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" @click="yearChangeAction('2021')"
                    >2021</a
                  >
                  <a class="dropdown-item" @click="yearChangeAction('2020')"
                    >2020</a
                  >

                  <a class="dropdown-item" @click="yearChangeAction('2019')"
                    >2019</a
                  >
                </base-dropdown>
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
import http from "../../http.js";
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
      ordersChartID: "ordersChart",
      totalorder_month: 0,
      totalorder_week: 0,
      totalorder_day: 0,
      totalnewcust_month: 0,
      data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
      year: 2021,
      order_permonth: [],
    };
  },
  methods: {
    yearChangeAction(year) {
      const url = "admin/get/totalorder/" + year;
      http.get(url).then((response) => {
        this.order_permonth = response.data;
        var i = 0;
        for (i > 0; i < this.order_permonth.length; i++) {
          switch (this.order_permonth[i]._id) {
            case "01":
              this.data[0] = this.order_permonth[i].totalorder;
              break;
            case "02":
              this.data[1] = this.order_permonth[i].totalorder;
              break;
            case "03":
              this.data[2] = this.order_permonth[i].totalorder;
              break;
            case "04":
              this.data[3] = this.order_permonth[i].totalorder;
              break;
            case "05":
              this.data[4] = this.order_permonth[i].totalorder;
              break;
            case "06":
              this.data[5] = this.order_permonth[i].totalorder;
              break;
            case "07":
              this.data[6] = this.order_permonth[i].totalorder;
              break;
            case "08":
              this.data[7] = this.order_permonth[i].totalorder;
              break;
            case "09":
              this.data[8] = this.order_permonth[i].totalorder;
              break;
            case "10":
              this.data[9] = this.order_permonth[i].totalorder;
              break;
            case "11":
              this.data[10] = this.order_permonth[i].totalorder;
              break;
            case "12":
              this.data[11] = this.order_permonth[i].totalorder;
              break;
          }
        }
      });

      ordersChart.createChart(this.ordersChartID, this.data);
    },
  },
  mounted() {
    this.yearChangeAction(this.year);

    const url2 = "/admin/get/totalorder/thismonth";
    http.get(url2).then((response) => {
      this.totalorder_month = response.data[0].totalorder;
    });

    const url3 = "/admin/get/totalorder/thisweek";
    http.get(url3).then((response) => {
      this.totalorder_week = response.data[0].totalorder;
    });

    const url4 = "/admin/get/totalorder/today";
    http.get(url4).then((response) => {
      this.totalorder_day = response.data[0].totalorder;
    });

    const url5 = "/admin/get/totalnewcust/thismonth";
    http.get(url5).then((response) => {
      this.totalnewcust_month = response.data[0].totaluser;
    });
  },
};
</script>
<style></style>
