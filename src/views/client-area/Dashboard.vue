<template>
  <div class="py-4 container-fluid">
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

    <!-- Service Cards for Ordering -->
    <div class="row mt-4">
      <div class="col-12">
        <div class="card">
          <div class="card-header pb-0">
            <h6 class="mb-0">Available Services</h6>
          </div>
          <div class="card-body">
            <div v-if="loadingServices" class="text-center py-5">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
            <div v-else-if="services.length === 0" class="text-center py-5">
              <p class="text-muted">No services available at the moment.</p>
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
                      <h5 class="card-title mb-0">{{ service.name }}</h5>
                      <span
                        v-if="service.trial_days > 0"
                        class="badge bg-warning text-dark"
                      >
                        Trial
                      </span>
                    </div>
                    <p class="text-sm text-muted mb-3">
                      {{ service.description || 'No description available' }}
                    </p>
                    <div class="mb-3">
                      <h6 class="text-success mb-0">
                        Mulai dari {{ formatCurrency(service.price) }}
                      </h6>
                      <small class="text-muted">/ {{ service.billing_cycle }}</small>
                    </div>
                    <button
                      class="btn btn-primary btn-sm w-100"
                      @click="goToOrder(service)"
                    >
                      <span v-if="service.trial_days > 0">
                        Trial {{ service.trial_days }} Hari
                      </span>
                      <span v-else>Pesan Sekarang</span>
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
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { ispAdminAPI } from '@/services/api'

const router = useRouter()

const balance = ref(0)
const activeServices = ref(0)
const unpaidInvoices = ref(0)
const pendingOrders = ref(0)
const services = ref([])
const loadingServices = ref(false)

onMounted(async () => {
  await Promise.all([
    fetchDashboardStats(),
    fetchServices()
  ])
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

const fetchServices = async () => {
  loadingServices.value = true
  try {
    const response = await ispAdminAPI.getClientAreaServices()
    services.value = response.data.filter(s => s.is_active)
  } catch (error) {
    console.error('Error fetching services:', error)
  } finally {
    loadingServices.value = false
  }
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(amount)
}

const goToOrder = (service) => {
  router.push({
    name: 'ClientAreaOrders',
    query: { service_id: service.id }
  })
}
</script>
