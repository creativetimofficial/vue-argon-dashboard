<template>
  <div class="py-4 container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <div class="row">
          <!-- Total ISPs -->
          <div class="col-lg-3 col-md-6 col-12">
            <mini-statistics-card
              title="Total ISP"
              :value="stats.total_isps?.toString() || '0'"
              description="<span class='text-sm font-weight-bolder text-success'>Active ISPs</span>"
              :icon="{
                component: 'ni ni-building',
                background: 'bg-gradient-primary',
                shape: 'rounded-circle',
              }"
            />
          </div>

          <!-- Total Customers -->
          <div class="col-lg-3 col-md-6 col-12">
            <mini-statistics-card
              title="Total Pelanggan"
              :value="stats.total_customers?.toString() || '0'"
              description="<span class='text-sm font-weight-bolder text-info'>All Customers</span>"
              :icon="{
                component: 'ni ni-single-02',
                background: 'bg-gradient-success',
                shape: 'rounded-circle',
              }"
            />
          </div>

          <!-- Monthly Revenue -->
          <div class="col-lg-3 col-md-6 col-12">
            <mini-statistics-card
              title="Total Pendapatan"
              :value="formatCurrency(stats.total_revenue || 0)"
              description="<span class='text-sm font-weight-bolder text-warning'>This Month</span>"
              :icon="{
                component: 'ni ni-money-coins',
                background: 'bg-gradient-info',
                shape: 'rounded-circle',
              }"
            />
          </div>

          <!-- Pending Approvals -->
          <div class="col-lg-3 col-md-6 col-12">
            <mini-statistics-card
              title="Pending Actions"
              :value="stats.pending_approvals?.toString() || '0'"
              description="<span class='text-sm font-weight-bolder text-danger'>Needs Review</span>"
              :icon="{
                component: 'ni ni-notification-70',
                background: 'bg-gradient-warning',
                shape: 'rounded-circle',
              }"
            />
          </div>
        </div>

        <!-- Recent ISPs Table -->
        <div class="row mt-4">
          <div class="col-lg-8 mb-4">
            <div class="card">
              <div class="card-header pb-0">
                <div class="d-flex justify-content-between">
                  <h6>ISP Companies</h6>
                  <button class="btn btn-sm btn-primary mb-0">
                    <i class="fas fa-plus me-2"></i>Add ISP
                  </button>
                </div>
              </div>
              <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                  <table class="table align-items-center mb-0">
                    <thead>
                      <tr>
                        <th
                          class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                        >
                          ISP
                        </th>
                        <th
                          class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"
                        >
                          Status
                        </th>
                        <th
                          class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                        >
                          Customers
                        </th>
                        <th
                          class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                        >
                          Package
                        </th>
                        <th class="text-secondary opacity-7"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-if="loading">
                        <td colspan="5" class="text-center py-4">
                          <i class="fas fa-spinner fa-spin"></i> Loading...
                        </td>
                      </tr>
                      <tr v-else-if="!isps || isps.length === 0">
                        <td colspan="5" class="text-center py-4">
                          No ISP data available
                        </td>
                      </tr>
                      <tr v-for="isp in isps" :key="isp.id">
                        <td>
                          <div class="d-flex px-2 py-1">
                            <div
                              class="d-flex flex-column justify-content-center"
                            >
                              <h6 class="mb-0 text-sm">{{ isp.name }}</h6>
                              <p class="text-xs text-secondary mb-0">
                                {{ isp.email }}
                              </p>
                            </div>
                          </div>
                        </td>
                        <td>
                          <span
                            :class="`badge badge-sm bg-gradient-${isp.subscription_status === 'active' ? 'success' : 'secondary'}`"
                          >
                            {{ isp.subscription_status || "inactive" }}
                          </span>
                        </td>
                        <td class="align-middle text-center text-sm">
                          <span class="text-xs font-weight-bold">{{
                            isp.customers_count || 0
                          }}</span>
                        </td>
                        <td class="align-middle text-center">
                          <span
                            class="text-secondary text-xs font-weight-bold"
                            >{{ isp.package_name || "N/A" }}</span
                          >
                        </td>
                        <td class="align-middle">
                          <button class="btn btn-link text-secondary mb-0">
                            <i class="fa fa-ellipsis-v text-xs"></i>
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <!-- System Status -->
          <div class="col-lg-4 mb-4">
            <div class="card h-100">
              <div class="card-header pb-0">
                <h6>System Status</h6>
              </div>
              <div class="card-body p-3">
                <div class="timeline timeline-one-side">
                  <div class="timeline-block mb-3">
                    <span class="timeline-step">
                      <i class="ni ni-check-bold text-success"></i>
                    </span>
                    <div class="timeline-content">
                      <h6 class="text-dark text-sm font-weight-bold mb-0">
                        Server Status
                      </h6>
                      <p
                        class="text-secondary font-weight-bold text-xs mt-1 mb-0"
                      >
                        Online & Running
                      </p>
                    </div>
                  </div>
                  <div class="timeline-block mb-3">
                    <span class="timeline-step">
                      <i class="ni ni-check-bold text-success"></i>
                    </span>
                    <div class="timeline-content">
                      <h6 class="text-dark text-sm font-weight-bold mb-0">
                        Database
                      </h6>
                      <p
                        class="text-secondary font-weight-bold text-xs mt-1 mb-0"
                      >
                        Connected
                      </p>
                    </div>
                  </div>
                  <div class="timeline-block mb-3">
                    <span class="timeline-step">
                      <i class="ni ni-check-bold text-warning"></i>
                    </span>
                    <div class="timeline-content">
                      <h6 class="text-dark text-sm font-weight-bold mb-0">
                        Payment Gateway
                      </h6>
                      <p
                        class="text-secondary font-weight-bold text-xs mt-1 mb-0"
                      >
                        Pending Setup
                      </p>
                    </div>
                  </div>
                  <div class="timeline-block">
                    <span class="timeline-step">
                      <i class="ni ni-button-play text-info"></i>
                    </span>
                    <div class="timeline-content">
                      <h6 class="text-dark text-sm font-weight-bold mb-0">
                        Active Users
                      </h6>
                      <p
                        class="text-secondary font-weight-bold text-xs mt-1 mb-0"
                      >
                        {{ stats.active_users || 1 }} online
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from "vue";
import { superAdminAPI } from "@/services/api";
import MiniStatisticsCard from "@/examples/Cards/MiniStatisticsCard.vue";

export default {
  name: "SuperAdminDashboard",
  components: {
    MiniStatisticsCard,
  },
  setup() {
    const loading = ref(false);
    const stats = ref({
      total_isps: 0,
      total_customers: 0,
      total_revenue: 0,
      pending_approvals: 0,
      active_users: 1,
    });
    const isps = ref([]);

    const formatCurrency = (value) => {
      return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
      }).format(value);
    };

    const fetchDashboardData = async () => {
      loading.value = true;
      try {
        // Fetch dashboard stats
        const statsResponse = await superAdminAPI.getStats();
        if (statsResponse.data.success) {
          stats.value = statsResponse.data.data;
        }

        // Fetch recent ISPs
        const ispsResponse = await superAdminAPI.getRecentISPs();
        if (ispsResponse.data.success) {
          isps.value = ispsResponse.data.data;
        }
      } catch (error) {
        console.error("Error fetching dashboard data:", error);
        // Errors are already handled in api interceptors for 401
      } finally {
        loading.value = false;
      }
    };

    onMounted(() => {
      fetchDashboardData();
    });

    return {
      loading,
      stats,
      isps,
      formatCurrency,
    };
  },
};
</script>
