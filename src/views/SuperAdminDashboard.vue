<template>
  <div class="py-4 container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <div class="row">
          <div class="col-lg-3 col-md-6 col-12">
            <mini-statistics-card
              title="Total ISP"
              :value="stats.total_isps?.toString() || '0'"
              description="<span class='text-sm font-weight-bolder text-success'>+5%</span> dari bulan lalu"
              :icon="{
                component: 'ni ni-building',
                background: 'bg-gradient-primary',
                shape: 'rounded-circle',
              }"
            />
          </div>
          <div class="col-lg-3 col-md-6 col-12">
            <mini-statistics-card
              title="Langganan Aktif"
              :value="stats.active_subscriptions?.toString() || '0'"
              description="<span class='text-sm font-weight-bolder text-success'>+3%</span> dari bulan lalu"
              :icon="{
                component: 'ni ni-world',
                background: 'bg-gradient-danger',
                shape: 'rounded-circle',
              }"
            />
          </div>
          <div class="col-lg-3 col-md-6 col-12">
            <mini-statistics-card
              title="Pesanan Baru"
              :value="stats.new_orders?.toString() || '0'"
              description="<span class='text-sm font-weight-bolder text-danger'>-2%</span> dari kemarin"
              :icon="{
                component: 'ni ni-paper-diploma',
                background: 'bg-gradient-success',
                shape: 'rounded-circle',
              }"
            />
          </div>
          <div class="col-lg-3 col-md-6 col-12">
            <mini-statistics-card
              title="Total Pendapatan"
              :value="formatCurrency(stats.total_revenue || 0)"
              description="<span class='text-sm font-weight-bolder text-success'>+5%</span> dari bulan lalu"
              :icon="{
                component: 'ni ni-cart',
                background: 'bg-gradient-warning',
                shape: 'rounded-circle',
              }"
            />
          </div>
        </div>
        
        <div class="row">
          <div class="col-lg-7 mb-4">
             <gradient-line-chart
              :key="chartKey"
              id="chart-line"
              title="Ringkasan Pendapatan"
              description="<i class='fa fa-arrow-up text-success'></i> <span class='font-weight-bold'>Tahun 2026</span>"
              :chart="{
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [
                  {
                    label: 'Pendapatan',
                    data: (stats.monthly_revenue && stats.monthly_revenue.length) ? stats.monthly_revenue : [0,0,0,0,0,0,0,0,0,0,0,0],
                  },
                ],
              }"
            />
          </div>
          <div class="col-lg-5 mb-4">
            <div class="card h-100">
              <div class="card-header pb-0 p-3">
                <div class="d-flex justify-content-between">
                  <h6 class="mb-0">ISP Terbaru</h6>
                </div>
              </div>
              <div class="card-body p-3 pb-0">
                <ul class="list-group">
                  <li v-if="!isps || isps.length === 0" class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                    <div class="d-flex align-items-center">
                       <div class="d-flex flex-column">
                        <h6 class="mb-1 text-dark text-sm">Belum ada ISP terbaru</h6>
                      </div>
                    </div>
                  </li>
                  <li v-for="isp in isps.slice(0, 5)" :key="isp.id" class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                    <div class="d-flex align-items-center">
                      <div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
                        <i class="ni ni-mobile-button text-white opacity-10"></i>
                      </div>
                      <div class="d-flex flex-column">
                        <h6 class="mb-1 text-dark text-sm">{{ isp.name }}</h6>
                        <span class="text-xs">{{ isp.email }} <span class="font-weight-bold text-primary ms-2">{{ isp.company_name }}</span></span>
                      </div>
                    </div>
                    <div class="d-flex">
                      <button class="btn btn-link btn-icon-only btn-rounded btn-sm text-dark icon-move-right my-auto" @click="$router.push('/super-admin/isp-management')"><i class="ni ni-bold-right" aria-hidden="true"></i></button>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent ISPs Table -->
        <div class="row mt-4">
          <div class="col-12">
            <div class="card mb-4">
              <div class="card-header pb-0">
                <h6>Perusahaan Terdaftar Terbaru</h6>
              </div>
              <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                   <table class="table align-items-center mb-0">
                    <thead>
                      <tr>
                        <th
                          class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                        >
                          Perusahaan ISP
                        </th>
                        <th
                          class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"
                        >
                          Status
                        </th>
                        <th
                          class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                        >
                          Pelanggan
                        </th>
                         <th
                          class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                        >
                          Terdaftar Pada
                        </th>
                        <th class="text-secondary opacity-7"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-if="loading">
                        <td colspan="5" class="text-center py-4">
                          <i class="fas fa-spinner fa-spin"></i> Memuat...
                        </td>
                      </tr>
                      <tr v-else-if="!isps || isps.length === 0">
                        <td colspan="5" class="text-center py-4">
                          Tidak ada data ISP
                        </td>
                      </tr>
                      <tr v-for="isp in isps" :key="isp.id">
                        <td>
                          <div class="d-flex px-2 py-1">
                             <div>
                                <div class="avatar avatar-sm me-3 bg-gradient-primary rounded-circle">
                                  <span class="text-white text-xs font-weight-bold">{{ isp.name.charAt(0) }}</span>
                                </div>
                            </div>
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
                          <span class="text-secondary text-xs font-weight-bold">{{ new Date(isp.created_at).toLocaleDateString('id-ID') }}</span>
                        </td>
                        <td class="align-middle">
                          <button class="btn btn-link text-secondary mb-0" @click="$router.push('/super-admin/isp-management')">
                            <i class="fa fa-pencil-square-o text-xs me-2"></i> Kelola
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
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
import GradientLineChart from "@/examples/Charts/GradientLineChart.vue";

export default {
  name: "SuperAdminDashboard",
  components: {
    MiniStatisticsCard,
    GradientLineChart
  },
  setup() {
    const loading = ref(false);
    const chartKey = ref(0);
    const stats = ref({
      total_isps: 0,
      total_customers: 0,
      total_revenue: 0,
      pending_approvals: 0,
      active_subscriptions: 0, // Mocked or real if available
      new_orders: 0, // Mocked or real if available
      monthly_revenue: [] 
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
          stats.value = { ...stats.value, ...statsResponse.data.data };
          // Fallback if some stats are missing from backend
          if(stats.value.active_subscriptions === undefined) stats.value.active_subscriptions = 120; // Mock
          if(stats.value.new_orders === undefined) stats.value.new_orders = 15; // Mock
          
          // Force chart re-render
          chartKey.value++;
        }

        // Fetch recent ISPs
        const ispsResponse = await superAdminAPI.getRecentISPs();
        if (ispsResponse.data.success) {
          isps.value = ispsResponse.data.data;
        }
      } catch (error) {
        console.error("Error fetching dashboard data:", error);
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
