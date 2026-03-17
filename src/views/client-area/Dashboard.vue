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
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">{{ $t('dashboard.balance') }}</p>
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
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">{{ $t('dashboard.active_services') }}</p>
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
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">{{ $t('dashboard.pending_invoices') }}</p>
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
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">{{ $t('dashboard.pending_orders') }}</p>
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

    <!-- My ISP Units (Multi-Instance Management) -->
    <div class="row mt-4">
      <div class="col-12">
        <div class="card">
          <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h6 class="mb-0">{{ $t('dashboard.my_isp_units') }}</h6>
                <p class="text-xs text-muted">{{ $t('dashboard.manage_units_desc') }}</p>
              </div>
              <button class="btn btn-primary btn-sm" @click="openCreateISPModal">
                <i class="fas fa-plus me-1"></i> {{ $t('dashboard.add_isp_unit') }}
              </button>
            </div>
          </div>
          <div class="card-body">
            <div v-if="ownedIsps.length === 0" class="text-center py-5">
              <div class="mb-3">
                <i class="fas fa-network-wired text-lighter fa-3x"></i>
              </div>
              <h6 class="text-secondary">{{ $t('dashboard.no_units_found') }}</h6>
              <p class="text-sm text-muted mb-4">{{ $t('dashboard.no_units_desc') }}</p>
              <button class="btn btn-primary btn-sm px-4" @click="openCreateISPModal">
                <i class="fas fa-plus me-1"></i> {{ $t('dashboard.setup_unit_now') }}
              </button>
            </div>
            <div v-else class="table-responsive">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ $t('dashboard.unit_name') }}</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">{{ $t('dashboard.domain_subdomain') }}</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ $t('dashboard.subscription') }}</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ $t('common.status') }}</th>
                    <th class="text-secondary opacity-7"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="unit in ownedIsps" :key="unit.id">
                    <td>
                      <div class="d-flex px-2 py-1">
                        <div class="d-flex flex-column justify-content-center">
                          <h6 class="mb-0 text-sm">{{ unit.company_name }}</h6>
                          <p class="text-xs text-secondary mb-0">ID: #{{ unit.id }}</p>
                        </div>
                      </div>
                    </td>
                    <td>
                      <p v-if="unit.subdomain || unit.custom_domain" class="text-xs font-weight-bold mb-0">
                        {{ unit.custom_domain || getDisplayDomain(unit.subdomain) }}
                      </p>
                      <span v-else class="badge badge-dot me-4">
                        <i class="bg-warning"></i>
                        <span class="text-dark text-xs">{{ $t('dashboard.config_required') }}</span>
                      </span>
                    </td>
                    <td class="align-middle text-center text-sm">
                      <span class="text-xs font-weight-bold">{{ unit.subscription_package?.name || $t('dashboard.no_package') }}</span>
                      <p class="text-xxs text-muted mb-0">{{ $t('dashboard.expires') }}: {{ formatDate(unit.subscription_end_date) }}</p>
                    </td>
                    <td class="align-middle text-center text-sm">
                      <span :class="getStatusBadgeClass(unit.subscription_status)">
                        {{ unit.subscription_status.toUpperCase() }}
                      </span>
                    </td>
                    <td class="align-middle text-end">
                      <div class="d-flex gap-2 justify-content-end pr-3">
                         <a v-if="unit.subdomain" :href="'http://' + getDisplayDomain(unit.subdomain)" target="_blank" class="btn btn-link text-primary text-xs font-weight-bold mb-0">
                           <i class="fas fa-external-link-alt me-1"></i> {{ $t('dashboard.admin_isp') }}
                         </a>
                         <span v-else class="text-xs text-muted my-auto">{{ $t('dashboard.configuring') }}</span>
                         <button class="btn btn-outline-primary btn-xs mb-0" @click="renewISP(unit)">
                           <i class="fas fa-sync me-1"></i> {{ $t('dashboard.renew') }}
                         </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- My System Subscriptions (Old active services) -->
    <div class="row mt-4">
      <div class="col-12">
        <div class="card">
          <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center">
               <div>
                  <h6 class="mb-0">{{ $t('dashboard.subs_active_services') }}</h6>
                  <p class="text-xs text-muted">{{ $t('dashboard.subs_desc') }}</p>
               </div>
               <button class="btn btn-sm btn-outline-primary mb-0" @click="$router.push({ name: 'ClientAreaOrders' })">
                  <i class="fas fa-plus me-1"></i> {{ $t('dashboard.new_order') }}
               </button>
            </div>
          </div>
          <div class="card-body">
            <div v-if="services.length === 0" class="text-center py-5">
              <p class="text-muted">{{ $t('dashboard.no_services_found') }}</p>
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
                        <h5 class="card-title mb-1 text-truncate" style="max-width: 200px;">
                          {{ service.service?.name || service.subscription_package?.name || $t('dashboard.unknown_service') }}
                        </h5>
                         <span class="text-xs text-muted">{{ service.reference }}</span>
                         <div class="mt-1">
                            <span class="badge bg-secondary text-xxs px-2 py-1">
                              {{ $t('dashboard.owner') }}: {{ service.isp?.company_name || $t('dashboard.system') }}
                            </span>
                         </div>
                      </div>
                      <span :class="getStatusBadgeClass(service.status)">
                        {{ service.status.toUpperCase() }}
                      </span>
                    </div>
                    
                     <div v-if="service.status === 'active'" class="alert alert-light border mb-3 p-2">
                       <small class="d-block text-muted mb-1">{{ $t('dashboard.login_access') }}:</small>
                       <div class="d-flex justify-content-between align-items-center mb-1">
                          <span class="text-xs font-weight-bold">{{ $t('dashboard.account') }}:</span>
                          <span class="text-xs">{{ service.username || '-' }}</span>
                       </div>
                       <div class="d-flex justify-content-between align-items-center">
                          <span class="text-xs font-weight-bold">{{ $t('dashboard.password') }}:</span>
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
                       {{ $t('dashboard.expires') }}: {{ formatDate(service.expired_date) }}
                    </p>
                    
                    <div class="mb-3">
                      <h6 class="text-primary mb-0">
                        {{ formatCurrency(service.price) }}
                      </h6>
                      <small class="text-muted">/ {{ $t('dashboard.' + (service.billing_cycle || 'monthly')) }}</small>
                    </div>

                    <div class="row g-2">
                       <div class="col-6">
                          <button
                            class="btn btn-outline-primary btn-sm w-100"
                            @click="$router.push({ name: 'ClientAreaServiceDetail', params: { id: service.id } })"
                          >
                            {{ $t('common.details') }}
                          </button>
                       </div>
                       <div class="col-6">
                          <button
                            class="btn btn-primary btn-sm w-100"
                            @click="renewService(service)"
                          >
                            {{ $t('dashboard.renew') }}
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
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { ispAdminAPI } from '@/services/api'
import LoadingOverlay from '@/components/LoadingOverlay.vue'

const router = useRouter()

const balance = ref(0)
const activeServices = ref(0)
const unpaidInvoices = ref(0)
const pendingOrders = ref(0)
const services = ref([])
const ownedIsps = ref([])
const baseDomain = ref('localhost')
const isLoading = ref(false)

onMounted(async () => {
  isLoading.value = true
  await Promise.all([
    fetchDashboardStats(),
    fetchMyServices(),
    fetchOwnedIsps(),
    fetchMainDomain()
  ])
  isLoading.value = false
})

const fetchOwnedIsps = async () => {
  try {
    const response = await ispAdminAPI.getOwnedIsps()
    ownedIsps.value = response.data
  } catch (error) {
    console.error('Error fetching owned ISPs:', error)
  }
}

const fetchMainDomain = async () => {
  try {
    const res = await api.get('/system/main-domain')
    if (res.data.settings && res.data.settings.base_domain) {
      baseDomain.value = res.data.settings.base_domain
    }
  } catch (err) {
    console.error('Failed to fetch main domain', err)
  }
}

const getDisplayDomain = (subdomain) => {
  let base = baseDomain.value
  let port = ''
  
  // Local development override: if current host is localhost, force subdomains to use localhost:8080
  if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
    base = 'localhost'
    port = ':8080'
  } else {
    // Production logic
    port = (base === 'localhost') ? ':8080' : ''
  }
  
  return `${subdomain}.${base}${port}`
}

const openCreateISPModal = () => {
  // Navigate to order page with a flag for new ISP
  router.push({ path: '/client-area/orders', query: { action: 'new_unit' } })
}

const renewISP = (unit) => {
  // Navigate to order page with target ISP ID
  router.push({ path: '/client-area/orders', query: { isp_id: unit.id } })
}

const renewService = (service) => {
  // If it's a system subscription package, use the renew flow for the associated ISP
  if (service.subscription_package_id) {
    router.push({ path: '/client-area/orders', query: { isp_id: service.isp_id } })
  } else {
    // Fallback for other services
    router.push({ path: '/client-area/orders' })
  }
}

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
  return new Date(dateString).toLocaleDateString('en-US', {
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
