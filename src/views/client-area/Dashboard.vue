<template>
  <div class="py-4 container-fluid">
    <LoadingOverlay :active="isLoading" />
    <div class="row">
      <!-- Summary Cards -->
      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-body p-3">
            <div class="row">
              <div class="col-8">
                <div class="numbers">
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">BALANCE</p>
                  <h5 class="font-weight-bolder mb-0">{{ formatCurrency(balance) }}</h5>
                </div>
              </div>
              <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                  <i class="fas fa-wallet text-lg opacity-10" aria-hidden="true"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-body p-3">
            <div class="row">
              <div class="col-8">
                <div class="numbers">
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">ACTIVE SERVICES</p>
                  <h5 class="font-weight-bolder mb-0">{{ activeServices }}</h5>
                </div>
              </div>
              <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                  <i class="fas fa-leaf text-lg opacity-10" aria-hidden="true"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-body p-3">
            <div class="row">
              <div class="col-8">
                <div class="numbers">
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">UNPAID INVOICES</p>
                  <h5 class="font-weight-bolder mb-0">{{ unpaidInvoices }}</h5>
                </div>
              </div>
              <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                  <i class="fas fa-file-invoice-dollar text-lg opacity-10" aria-hidden="true"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-sm-6">
        <div class="card">
          <div class="card-body p-3">
            <div class="row">
              <div class="col-8">
                <div class="numbers">
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">PENDING ORDERS</p>
                  <h5 class="font-weight-bolder mb-0">{{ pendingOrders }}</h5>
                </div>
              </div>
              <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                  <i class="fas fa-shopping-cart text-lg opacity-10" aria-hidden="true"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- My Services (Replaces Available Services Catalog) -->
    <div class="row mt-4">
      <div class="col-12">
        <div class="card">
          <div class="card-header pb-0">
            <div class="d-flex justify-content-between">
               <h6 class="mb-0">Available Services (Layanan Tersedia)</h6>
               <button class="btn btn-sm btn-primary mb-0" @click="$router.push({ name: 'ClientAreaOrders' })">
                  <i class="fas fa-plus me-1"></i> Order Baru
               </button>
            </div>
          </div>
          <div class="card-body">
            <div v-if="services.length === 0" class="text-center py-5">
              <p class="text-muted">Belum ada layanan yang aktif atau pending.</p>
              <button class="btn btn-primary mt-3" @click="$router.push({ name: 'ClientAreaOrders' })">
                Mulai Berlangganan
              </button>
            </div>
            <div v-else class="row g-4">
              <div
                v-for="service in services"
                :key="service.id"
                class="col-lg-4 col-md-6"
              >
                <div class="card h-100 shadow-sm border">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                      <div>
                        <h5 class="card-title mb-1">{{ service.service?.name || service.subscription_package?.name || 'Unknown Service' }}</h5>
                         <span class="text-xs text-muted">{{ service.reference }}</span>
                      </div>
                      <span :class="getStatusBadgeClass(service.status)">
                        {{ service.status.toUpperCase() }}
                      </span>
                    </div>
                    
                    <div v-if="service.status === 'active'" class="alert alert-light border mb-3 p-2">
                       <small class="d-block text-muted mb-1">Credentials:</small>
                       <div class="d-flex justify-content-between align-items-center mb-1">
                          <span class="text-xs font-weight-bold">User:</span>
                          <span class="text-xs">{{ service.username || '-' }}</span>
                       </div>
                       <div class="d-flex justify-content-between align-items-center">
                          <span class="text-xs font-weight-bold">Pass:</span>
                          <div class="d-flex align-items-center">
                              <span class="text-xs me-2">
                                {{ visiblePasswords[service.id] ? service.password : '••••••' }}
                              </span>
                              <span class="cursor-pointer text-primary" @click="togglePassword(service.id)">
                                <i :class="visiblePasswords[service.id] ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                              </span>
                          </div>
                       </div>
                    </div>

                    <p class="text-sm text-muted mb-3">
                       <i class="fas fa-calendar-alt me-1"></i> 
                       Expires: {{ formatDate(service.expired_date) }}
                    </p>
                    
                    <div class="mb-3">
                      <h6 class="text-primary mb-0">
                        {{ formatCurrency(service.price) }}
                      </h6>
                      <small class="text-muted">/ {{ service.billing_cycle }}</small>
                    </div>

                    <div class="d-grid gap-2">
                       <button
                          class="btn btn-outline-primary btn-sm"
                          @click="$router.push({ name: 'ClientAreaServiceDetail', params: { id: service.id } })"
                        >
                          Lihat Detail
                        </button>
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

<script setup>
import { ref, onMounted } from 'vue'
import { ispAdminAPI } from '@/services/api'
import LoadingOverlay from '@/components/LoadingOverlay.vue'

const balance = ref(0)
const activeServices = ref(0)
const unpaidInvoices = ref(0)
const pendingOrders = ref(0)
const services = ref([])
const isLoading = ref(false)

onMounted(async () => {
  isLoading.value = true
  await Promise.all([
    fetchDashboardStats(),
    fetchMyServices()
  ])
  isLoading.value = false
})

const fetchDashboardStats = async () => {
  try {
    const response = await ispAdminAPI.getClientAreaStats()
    balance.value = response.data.balance || 0
    activeServices.value = response.data.active_services || 0
    unpaidInvoices.value = response.data.unpaid_invoices || 0
    pendingOrders.value = response.data.pending_orders || 0
  } catch (error) {
    console.error('Error fetching dashboard stats:', error)
  }
}

const fetchMyServices = async () => {
  try {
    const response = await ispAdminAPI.getMyServices()
    services.value = response.data
  } catch (error) {
    console.error('Error fetching services:', error)
  }
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  })
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(amount)
}

const visiblePasswords = ref({})

const togglePassword = (id) => {
  visiblePasswords.value[id] = !visiblePasswords.value[id]
}

const getStatusBadgeClass = (status) => {
  switch (status) {
    case 'active': return 'badge bg-success'
    case 'trial': return 'badge bg-warning text-dark'
    case 'suspended': return 'badge bg-danger'
    case 'pending': return 'badge bg-info'
    case 'cancelled': return 'badge bg-secondary'
    default: return 'badge bg-light text-dark'
  }
}
</script>
