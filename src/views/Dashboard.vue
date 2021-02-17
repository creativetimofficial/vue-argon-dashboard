<template>
  <div>
    <base-header type="gradient-success" class="pb-6 pb-8 pt-5 pt-md-8">
      <div class="row">
        <div class="col-xl-3 col-lg-6">
          <stats-card
            title="Total traffic"
            type="gradient-red"
            sub-title="350,897"
            icon="ni ni-active-40"
            class="mb-4 mb-xl-0"
          >
            <template v-slot:footer>
              <span class="text-success mr-2">
                <i class="fa fa-arrow-up"></i> 3.48%
              </span>
              <span class="text-nowrap">Since last month</span>
            </template>
          </stats-card>
        </div>
        <div class="col-xl-3 col-lg-6">
          <stats-card
            title="Total traffic"
            type="gradient-orange"
            sub-title="2,356"
            icon="ni ni-chart-pie-35"
            class="mb-4 mb-xl-0"
          >
            <template v-slot:footer>
              <span class="text-success mr-2">
                <i class="fa fa-arrow-up"></i> 12.18%
              </span>
              <span class="text-nowrap">Since last month</span>
            </template>
          </stats-card>
        </div>
        <div class="col-xl-3 col-lg-6">
          <stats-card
            title="Sales"
            type="gradient-green"
            sub-title="924"
            icon="ni ni-money-coins"
            class="mb-4 mb-xl-0"
          >
            <template v-slot:footer>
              <span class="text-danger mr-2">
                <i class="fa fa-arrow-down"></i> 5.72%
              </span>
              <span class="text-nowrap">Since last month</span>
            </template>
          </stats-card>
        </div>
        <div class="col-xl-3 col-lg-6">
          <stats-card
            title="Performance"
            type="gradient-info"
            sub-title="49,65%"
            icon="ni ni-chart-bar-32"
            class="mb-4 mb-xl-0"
          >
            <template v-slot:footer>
              <span class="text-success mr-2">
                <i class="fa fa-arrow-up"></i> 54.8%
              </span>
              <span class="text-nowrap">Since last month</span>
            </template>
          </stats-card>
        </div>
      </div>
    </base-header>

    <div class="container-fluid mt--7">
      <!--Charts-->
      <div class="row">
        <div class="col-xl-8 mb-5 mb-xl-0">
          <card type="default" header-classes="bg-transparent">
            <template v-slot:header>
              <div class="row align-items-center">
                <div class="col">
                  <h6 class="text-light text-uppercase ls-1 mb-1">Overview</h6>
                  <h5 class="h3 text-white mb-0">Sales value</h5>
                </div>
                <div class="col">
                    <ul class="nav nav-pills justify-content-end">
                        <li class="nav-item mr-2 mr-md-0">
                            <a class="nav-link py-2 px-3"
                               href="#"
                               :class="{active: bigLineChart.activeIndex === 0}"
                               @click.prevent="initBigChart(0)">
                                <span class="d-none d-md-block">Month</span>
                                <span class="d-md-none">M</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-2 px-3"
                               href="#"
                               :class="{active: bigLineChart.activeIndex === 1}"
                               @click.prevent="initBigChart(1)">
                                <span class="d-none d-md-block">Week</span>
                                <span class="d-md-none">W</span>
                            </a>
                        </li>
                    </ul>
                </div>
              </div>
            </template>
            <div class="chart-area">
              <canvas :id="activeUsersChartID"></canvas>
            </div>
          </card>
        </div>
      </div>
      <!-- End charts-->

      <!--Tables-->
      <div class="row mt-5">
        <div class="col-xl-8 mb-5 mb-xl-0">
          <page-visits-table></page-visits-table>
        </div>
        <div class="col-xl-4">
          <social-traffic-table></social-traffic-table>
        </div>
      </div>
      <!--End tables-->
    </div>
  </div>
</template>
<script>
// Charts
import * as chartConfigs from '@/components/Charts/config';
import {
  activeUsersChart
} from "@/components/Charts/LineChart";

import PageVisitsTable from './Dashboard/PageVisitsTable';
import SocialTrafficTable from './Dashboard/SocialTrafficTable';


export default {
  components: {
    PageVisitsTable,
    SocialTrafficTable
  },
  data() {
    return {
      activeUsersChartID: "activeUsersChart",
      bigLineChart: {
        allData: [
          [0, 20, 10, 30, 15, 40, 20, 60, 60],
          [0, 20, 5, 25, 10, 30, 15, 40, 40]
        ],
        activeIndex: 0,
        chartData: {
          datasets: [],
          labels: [],
        },
        extraOptions: chartConfigs.blueChartOptions,
      },
      redBarChart: {
        chartData: {
          labels: ['Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
          datasets: [{
            label: 'Sales',
            data: [25, 20, 30, 22, 17, 29]
          }]
        }
      }
    };
  },
  mounted() {
    activeUsersChart.createChart(this.activeUsersChartID);
  },
}
</script>
<style></style>
