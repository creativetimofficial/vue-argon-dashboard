<template>
  <v-container fluid class="pa-6">
    <v-row>
      <!-- Welcome Card -->
      <v-col cols="12" md="8" class="order-0 order-md-0">
        <v-card color="surface" variant="flat" class="h-100">
          <v-row no-gutters class="h-100 align-end">
             <v-col cols="12" sm="7">
                <v-card-text>
                  <h5 class="text-h5 text-primary mb-2">{{ $t('dashboard.welcome', { name: ispName }) }}! 🎉</h5>
                  <p class="mb-4">
                    {{ $t('dashboard.processed_invoices', { count: stats.overdue_invoices }) }}
                  </p>
                  <v-btn  color="primary" to="/isp-admin/invoices">{{ $t('dashboard.view_invoices') }}</v-btn>
                </v-card-text>
             </v-col>
             <v-col cols="12" sm="5" class="text-center text-sm-start pb-0 px-0 px-md-4">
                <v-icon size="140" color="primary" style="opacity: 0.8;">bx bx-laptop</v-icon>
             </v-col>
          </v-row>
        </v-card>
      </v-col>

      <!-- Stats Cards -->
      <v-col cols="12" md="4" class="order-1">
        <v-row>
          <v-col cols="6" md="12" lg="6">
            <v-card color="surface" variant="flat" class="text-center px-1 py-3 h-100">
              <v-card-text class="d-flex flex-column align-center justify-center h-100 pa-2">
                <v-avatar color="rgba(113, 221, 55, 0.16)" rounded size="42" class="mb-3">
                  <v-icon color="success" size="24">bx bx-wallet</v-icon>
                </v-avatar>
                <div class="text-overline mb-1 text-uppercase">{{ $t('dashboard.revenue_month') }}</div>
                <h5 class="text-h5 font-weight-medium">Rp {{ formatNumber(stats.revenue_this_month) }}</h5>
              </v-card-text>
            </v-card>
          </v-col>
          
          <v-col cols="6" md="12" lg="6">
            <v-card color="surface" variant="flat" class="text-center px-1 py-3 h-100">
              <v-card-text class="d-flex flex-column align-center justify-center h-100 pa-2">
                <v-avatar color="rgba(255, 171, 0, 0.16)" rounded size="42" class="mb-3">
                  <v-icon color="warning" size="24">bx bx-time</v-icon>
                </v-avatar>
                <div class="text-overline mb-1 text-uppercase">{{ $t('dashboard.unpaid') }}</div>
                <h5 class="text-h5 font-weight-medium">Rp {{ formatNumber(stats.pending_amount) }}</h5>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-col>
    </v-row>

    <!-- Second Row: Mini Stats -->
    <v-row class="mt-4">
      <v-col cols="6" md="3">
        <v-card color="surface" variant="flat" class="text-center h-100 py-3">
          <v-card-text>
            <v-avatar color="rgba(105, 108, 255, 0.16)" rounded size="42" class="mb-2">
              <v-icon color="primary" size="24">bx bx-user</v-icon>
            </v-avatar>
            <h4 class="text-h4 font-weight-medium mb-0">{{ stats.total_customers }}</h4>
            <div class="text-caption text-medium-emphasis">{{ $t('dashboard.total_customers') }}</div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="6" md="3">
        <v-card color="surface" variant="flat" class="text-center h-100 py-3">
          <v-card-text>
            <v-avatar color="rgba(113, 221, 55, 0.16)" rounded size="42" class="mb-2">
              <v-icon color="success" size="24">bx bx-check-circle</v-icon>
            </v-avatar>
            <h4 class="text-h4 font-weight-medium mb-0">{{ stats.active_customers }}</h4>
            <div class="text-caption text-medium-emphasis">{{ $t('dashboard.active_customers') }}</div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="6" md="3">
        <v-card color="surface" variant="flat" class="text-center h-100 py-3">
           <v-card-text>
            <v-avatar color="rgba(255, 62, 29, 0.16)" rounded size="42" class="mb-2">
              <v-icon color="error" size="24">bx bx-pause-circle</v-icon>
            </v-avatar>
            <h4 class="text-h4 font-weight-medium mb-0">{{ stats.suspended_customers }}</h4>
            <div class="text-caption text-medium-emphasis">{{ $t('dashboard.suspended') }}</div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="6" md="3">
        <v-card color="surface" variant="flat" class="text-center h-100 py-3">
          <v-card-text>
            <v-avatar color="rgba(3, 195, 236, 0.16)" rounded size="42" class="mb-2">
              <v-icon color="info" size="24">bx bx-package</v-icon>
            </v-avatar>
            <h5 class="text-h5 font-weight-medium mb-0 text-truncate px-1">{{ subscription?.name || 'Starter' }}</h5>
            <div class="text-caption text-medium-emphasis">{{ $t('dashboard.your_package') }}</div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <v-row class="mt-4">
      <!-- Recent Customers -->
      <v-col cols="12" md="6">
        <v-card color="surface" variant="flat" class="h-100">
          <v-card-title class="d-flex align-center justify-space-between pb-4">
            <span>{{ $t('dashboard.recent_customers') }}</span>
            <v-btn  size="small" color="primary" to="/isp-admin/customers">{{ $t('common.all') }}</v-btn>
          </v-card-title>
          <v-card-text>
            <v-list lines="two" class="pa-0">
              <v-list-item v-for="customer in recentCustomers" :key="customer.id" class="px-0">
                <template v-slot:prepend>
                  <v-avatar color="rgba(105, 108, 255, 0.16)" rounded>
                    <span class="text-primary font-weight-medium">{{ customer.name.charAt(0) }}</span>
                  </v-avatar>
                </template>
                <v-list-item-title class="font-weight-medium mb-1">{{ customer.name }}</v-list-item-title>
                <v-list-item-subtitle>{{ customer.customer_code }}</v-list-item-subtitle>
                <template v-slot:append>
                  <div class="text-caption font-weight-medium">{{ formatDate(customer.created_at) }}</div>
                </template>
              </v-list-item>
            </v-list>
          </v-card-text>
        </v-card>
      </v-col>

      <!-- Recent Invoices -->
      <v-col cols="12" md="6">
        <v-card color="surface" variant="flat" class="h-100">
          <v-card-title class="d-flex align-center justify-space-between pb-4">
            <span>{{ $t('dashboard.recent_invoices') }}</span>
            <v-btn  size="small" color="primary" to="/isp-admin/invoices">{{ $t('common.all') }}</v-btn>
          </v-card-title>
          <v-card-text>
             <v-list lines="two" class="pa-0">
              <v-list-item v-for="invoice in recentInvoices" :key="invoice.id" class="px-0">
                <template v-slot:prepend>
                  <v-avatar size="10" :color="invoice.payment_status === 'paid' ? 'success' : 'warning'" class="mr-3" />
                </template>
                <v-list-item-title class="font-weight-medium mb-1">#{{ invoice.invoice_number }}</v-list-item-title>
                <v-list-item-subtitle>{{ invoice.billable?.name }}</v-list-item-subtitle>
                <template v-slot:append>
                  <div class="text-right">
                    <div class="font-weight-bold">Rp {{ formatNumber(invoice.total) }}</div>
                    <div class="text-caption font-weight-medium" :class="invoice.payment_status === 'paid' ? 'text-success' : 'text-warning'">
                      {{ invoice.payment_status === 'paid' ? $t('dashboard.status_paid') : $t('dashboard.status_unpaid') }}
                    </div>
                  </div>
                </template>
              </v-list-item>
            </v-list>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    
    <!-- Quick Actions -->
    <v-row class="mt-4" v-if="hasAddons">
       <v-col cols="12">
          <v-card color="surface" variant="flat">
             <v-card-title class="text-primary font-weight-bold d-flex align-center">
                <v-icon class="mr-2">bx bx-bolt-circle</v-icon> {{ $t('dashboard.quick_actions') }}
             </v-card-title>
             <v-card-text>
                <v-row>
                   <v-col cols="12" sm="6" md="3" v-if="ispPackage?.feature_realtime_monitoring">
                      <v-btn  height="100" class="w-100 d-flex flex-column align-center justify-center text-none" to="/isp-admin/mikrotik">
                         <v-icon size="32" color="primary" class="mb-2">bx bx-broadcast</v-icon>
                         <span>{{ $t('dashboard.mikrotik_mgt') }}</span>
                      </v-btn>
                   </v-col>
                   <v-col cols="12" sm="6" md="3" v-if="ispPackage?.feature_analytics">
                      <v-btn  height="100" class="w-100 d-flex flex-column align-center justify-center text-none" to="/isp-admin/reports">
                         <v-icon size="32" color="info" class="mb-2">bx bx-bar-chart-alt-2</v-icon>
                         <span>{{ $t('dashboard.analytics') }}</span>
                      </v-btn>
                   </v-col>
                   <v-col cols="12" sm="6" md="3">
                      <v-btn  height="100" class="w-100 d-flex flex-column align-center justify-center text-none" to="/isp-admin/invoices">
                         <v-icon size="32" color="success" class="mb-2">bx bx-receipt</v-icon>
                         <span>{{ $t('dashboard.invoices') }}</span>
                      </v-btn>
                   </v-col>
                   <v-col cols="12" sm="6" md="3">
                      <v-btn  height="100" class="w-100 d-flex flex-column align-center justify-center text-none" @click="triggerSync">
                         <v-icon size="32" color="warning" class="mb-2">bx bx-sync</v-icon>
                         <span>{{ $t('dashboard.sync_bandwidth') }}</span>
                      </v-btn>
                   </v-col>
                </v-row>
             </v-card-text>
          </v-card>
       </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { ispAdminAPI } from "@/services/api";
import Swal from 'sweetalert2';

const ispName = ref("");
const stats = ref({
  total_customers: 0,
  active_customers: 0,
  suspended_customers: 0,
  revenue_this_month: 0,
  pending_amount: 0,
  overdue_invoices: 0
});
const ispPackage = ref(null);
const subscription = ref(null);
const recentCustomers = ref([]);
const recentInvoices = ref([]);

const hasAddons = computed(() => {
    return ispPackage.value?.feature_realtime_monitoring || ispPackage.value?.feature_analytics;
});

const fetchDashboardData = async () => {
  try {
    const response = await ispAdminAPI.getDashboard();
    
    const data = response.data;
    ispName.value = data.isp?.company_name || "Admin ISP";
    stats.value = data.stats;
    ispPackage.value = data.isp?.subscriptionPackage;
    subscription.value = data.subscription;
    recentCustomers.value = data.recent_customers || [];
    recentInvoices.value = data.recent_invoices || [];
  } catch (err) {
    console.error("Dashboard error", err);
  }
};

const formatNumber = (num) => {
  if (!num) return "0";
  return parseFloat(num).toLocaleString("en-US");
};

const formatDate = (dateStr) => {
  if (!dateStr) return "-";
  const date = new Date(dateStr);
  return date.toLocaleDateString("en-US", { day: "2-digit", month: "short" });
};

const triggerSync = () => {
    Swal.fire({
        title: t('dashboard.sync_confirm_title'),
        text: t('dashboard.sync_confirm_text'),
        icon: 'info',
        confirmButtonText: t('dashboard.sync_confirm_btn')
    });
};

onMounted(() => {
  fetchDashboardData();
});
</script>

<style scoped>
.text-xs { font-size: 0.7rem; }
.avatar-initial {
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  font-weight: 500;
}
.badge-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-top: 5px;
}
</style>
