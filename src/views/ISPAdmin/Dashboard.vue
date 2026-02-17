<template>
  <div>
    <h4 class="py-3 mb-4">
      <span class="text-muted fw-light">ISP Admin /</span> Dashboard
    </h4>

    <!-- Statistics Cards -->
    <div class="row">
      <!-- Total Pelanggan -->
      <div class="col-lg-3 col-md-6 col-6 mb-6">
        <div class="card h-100">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between mb-4">
              <div class="avatar flex-shrink-0">
                <img src="/sneat/assets/img/icons/unicons/chart-success.png" alt="chart success" class="rounded" />
              </div>
            </div>
            <p class="mb-1">Total Pelanggan</p>
            <h4 class="card-title mb-3">{{ stats.totalCustomers }}</h4>
            <small class="text-success fw-medium">
              <i class="bx bx-up-arrow-alt"></i> {{ stats.customerGrowth }}%
            </small>
          </div>
        </div>
      </div>

      <!-- Pelanggan Aktif -->
      <div class="col-lg-3 col-md-6 col-6 mb-6">
        <div class="card h-100">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between mb-4">
              <div class="avatar flex-shrink-0">
                <img src="/sneat/assets/img/icons/unicons/wallet-info.png" alt="wallet info" class="rounded" />
              </div>
            </div>
            <p class="mb-1">Pelanggan Aktif</p>
            <h4 class="card-title mb-3">{{ stats.activeCustomers }}</h4>
            <small class="text-success fw-medium">
              <i class="bx bx-up-arrow-alt"></i> {{ stats.activeGrowth }}%
            </small>
          </div>
        </div>
      </div>

      <!-- Total Pendapatan -->
      <div class="col-lg-3 col-md-6 col-6 mb-6">
        <div class="card h-100">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between mb-4">
              <div class="avatar flex-shrink-0">
                <img src="/sneat/assets/img/icons/unicons/paypal.png" alt="paypal" class="rounded" />
              </div>
            </div>
            <p class="mb-1">Total Pendapatan</p>
            <h4 class="card-title mb-3">Rp {{ formatNumber(stats.totalRevenue) }}</h4>
            <small class="text-success fw-medium">
              <i class="bx bx-up-arrow-alt"></i> Bulan ini
            </small>
          </div>
        </div>
      </div>

      <!-- Tagihan Terbayar -->
      <div class="col-lg-3 col-md-6 col-6 mb-6">
        <div class="card h-100">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between mb-4">
              <div class="avatar flex-shrink-0">
                <img src="/sneat/assets/img/icons/unicons/cc-success.png" alt="cc success" class="rounded" />
              </div>
            </div>
            <p class="mb-1">Tagihan Terbayar</p>
            <h4 class="card-title mb-3">{{ stats.paidInvoices }}</h4>
            <small class="text-muted">Dari {{ stats.totalInvoices }} tagihan</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Activities -->
    <div class="row mt-4">
      <div class="col-md-6 col-lg-8 mb-6">
        <div class="card h-100">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">Aktivitas Terbaru</h5>
          </div>
          <div class="card-body">
            <ul class="p-0 m-0">
              <li v-for="(activity, index) in recentActivities" :key="index" class="d-flex mb-4 pb-1">
                <div class="avatar flex-shrink-0 me-3">
                  <span class="avatar-initial rounded bg-label-primary">
                    <i :class="activity.icon"></i>
                  </span>
                </div>
                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                  <div class="me-2">
                    <h6 class="mb-0">{{ activity.title }}</h6>
                    <small class="text-muted">{{ activity.description }}</small>
                  </div>
                  <div class="user-progress d-flex align-items-center gap-1">
                    <small class="fw-medium">{{ activity.time }}</small>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="col-md-6 col-lg-4 mb-6">
        <div class="card h-100">
          <div class="card-header">
            <h5 class="card-title m-0">Quick Actions</h5>
          </div>
          <div class="card-body">
            <div class="d-grid gap-3">
              <button class="btn btn-primary" @click="$router.push('/isp-admin/customers')">
                <i class="bx bx-user-plus me-2"></i>Tambah Pelanggan
              </button>
              <button class="btn btn-outline-primary" @click="$router.push('/isp-admin/invoices')">
                <i class="bx bx-file me-2"></i>Lihat Invoice
              </button>
              <button class="btn btn-outline-primary" @click="$router.push('/isp-admin/mikrotik')">
                <i class="bx bx-server me-2"></i>Managemen Router
              </button>
              <button class="btn btn-outline-primary" @click="$router.push('/isp-admin/reports')">
                <i class="bx bx-bar-chart-alt-2 me-2"></i>Lihat Laporan
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

// Mock data - replace with actual API calls
const stats = ref({
  totalCustomers: 0,
  activeCustomers: 0,
  totalRevenue: 0,
  paidInvoices: 0,
  totalInvoices: 0,
  customerGrowth: 0,
  activeGrowth: 0
});

const recentActivities = ref([
  {
    icon: 'bx bx-user-plus',
    title: 'Pelanggan Baru',
    description: 'Pelanggan baru telah terdaftar',
    time: '2 jam lalu'
  },
  {
    icon: 'bx bx-check-circle',
    title: 'Pembayaran Diterima',
    description: 'Pembayaran invoice #INV-001 telah dikonfirmasi',
    time: '5 jam lalu'
  },
  {
    icon: 'bx bx-wrench',
    title: 'Tiket Perbaikan',
    description: 'Tiket perbaikan baru dari pelanggan',
    time: '1 hari lalu'
  }
]);

const formatNumber = (num) => {
  return new Intl.NumberFormat('id-ID').format(num);
};

// TODO: Fetch actual data from API
// onMounted(async () => {
//   const response = await fetch('/api/isp-admin/dashboard-stats');
//   stats.value = await response.json();
// });
</script>

<style scoped>
/* Additional custom styles if needed */
</style>
