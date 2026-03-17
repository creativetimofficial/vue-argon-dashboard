<template>
  <div class="py-4 container-fluid">
    <LoadingOverlay :active="isLoading" />
    <div class="row">
      <div class="col-12">
        <div class="card mb-4 min-vh-75">
          <div class="card-header pb-0 d-flex justify-content-between align-items-center">
            <h6>{{ $t('dashboard.orders.title') }}</h6>
            <button
              class="btn btn-primary btn-sm"
              @click="openCreateOrderModal"
            >
              <i class="fas fa-plus me-2"></i>
              {{ $t('dashboard.orders.new_order') }}
            </button>
          </div>
          <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                      ID
                    </th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                      {{ $t('dashboard.orders.reference').toUpperCase() }}
                    </th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                      {{ $t('dashboard.orders.product').toUpperCase() }}
                    </th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                      {{ $t('dashboard.orders.price').toUpperCase() }}
                    </th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                      {{ $t('dashboard.orders.date').toUpperCase() }}
                    </th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                      {{ $t('dashboard.orders.service_id').toUpperCase() }}
                    </th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                      {{ $t('common.status').toUpperCase() }}
                    </th>
                    <th class="text-secondary opacity-7"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="order in orders" :key="order.id">
                    <td>
                      <div class="d-flex px-2 py-1">
                        <div class="d-flex flex-column justify-content-center">
                          <h6 class="mb-0 text-sm">{{ order.id }}</h6>
                        </div>
                      </div>
                    </td>
                    <td>
                      <p class="text-xs font-weight-bold mb-0">{{ order.reference }}</p>
                    </td>
                    <td class="align-middle text-center text-sm">
                      <span class="text-secondary text-xs font-weight-bold">
                        {{ order.service?.name || order.subscription_package?.name || 'N/A' }}
                      </span>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">{{ formatCurrency(order.price) }}</span>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">{{ formatDate(order.created_at) }}</span>
                    </td>
                    <td class="align-middle text-center">
                      <router-link v-if="order.service_id" :to="'/client-area/services/' + order.id" class="text-xs font-weight-bold mb-0">#{{ order.service_id }}</router-link>
                      <router-link v-else-if="order.subscription_package_id && (order.status === 'active' || order.status === 'paid')" :to="'/client-area/services/' + order.id" class="text-xs font-weight-bold mb-0">#{{ order.id }}</router-link>
                      <span v-else class="text-secondary text-xs">-</span>
                    </td>
                    <td class="align-middle text-center text-sm">
                      <span
                        class="badge badge-sm"
                        :class="{
                          'bg-gradient-warning': order.status === 'pending' || order.status === 'pending_payment',
                          'bg-gradient-info': order.status === 'processing',
                          'bg-gradient-success': order.status === 'approved' || order.status === 'active',
                          'bg-gradient-danger': order.status === 'cancelled' || order.status === 'terminated' || order.status === 'failed',
                          'bg-gradient-secondary': order.status === 'expired',
                        }"
                      >
                        {{ order.status.toUpperCase().replace('_', ' ') }}
                      </span>
                    </td>
                    <td class="align-middle">
                      <div class="d-flex gap-2 justify-content-center">
                        <!-- Payment Button for Pending Payment Orders -->
                        <button
                          v-if="order.status === 'pending_payment'"
                          class="btn btn-success btn-sm mb-0"
                          @click="payOrder(order.id)"
                          :disabled="processingPayment === order.id"
                        >
                           <span v-if="processingPayment === order.id">
                            <i class="fas fa-spinner fa-spin"></i>
                          </span>
                          <span v-else>
                            {{ $t('common.pay') }}
                          </span>
                        </button>
                        
                        <!-- Retry Payment Button for Expired/Failed Orders -->
                        <button
                          v-if="order.status === 'expired' || order.status === 'failed'"
                          class="btn btn-warning btn-sm mb-0"
                          @click="retryPayment(order.id)"
                          :disabled="processingPayment === order.id"
                        >
                          <span v-if="processingPayment === order.id">
                            <i class="fas fa-spinner fa-spin"></i>
                          </span>
                          <span v-else>
                            <i class="fas fa-redo"></i> {{ $t('dashboard.orders.retry') }}
                          </span>
                        </button>
                        
                        <button
                          v-if="order.status === 'pending_payment'"
                          class="btn btn-secondary btn-sm mb-0"
                          @click="cancelOrder(order.id)"
                        >
                          {{ $t('common.cancel') }}
                        </button>
                        <button
                          v-if="order.status === 'pending_payment' || order.status === 'cancelled' || order.status === 'expired'"
                          class="btn btn-danger btn-sm mb-0"
                          @click="deleteOrder(order.id)"
                        >
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="orders.length === 0">
                    <td colspan="8" class="text-center py-4">
                      <p class="text-muted mb-0">{{ $t('dashboard.orders.no_orders') }}</p>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Order Modal -->
    <div
      v-if="showOrderModal"
      class="modal fade show"
      style="display: block; background: rgba(0,0,0,0.5);"
      tabindex="-1"
    >
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header d-flex justify-content-between align-items-center">
            <h5 class="modal-title m-0">{{ $t('dashboard.orders.create_order_title') }}</h5>
            <button
              type="button"
              class="btn-close"
              @click="showOrderModal = false"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="submitOrder">
              <!-- ISP Unit Selection -->
              <div class="mb-4">
                <label class="form-label font-weight-bold">{{ $t('dashboard.orders.target_isp') }} <span class="text-danger">*</span></label>
                <div class="d-flex gap-2 mb-2">
                  <div 
                    class="flex-fill p-3 border border-radius-lg cursor-pointer transition-all"
                    :class="orderForm.is_new_unit ? 'border-primary bg-light-primary' : 'bg-gray-100'"
                    @click="orderForm.is_new_unit = true"
                  >
                    <div class="form-check p-0 m-0">
                      <input class="form-check-input ms-0" type="radio" :value="true" v-model="orderForm.is_new_unit">
                      <label class="form-check-label ms-4 font-weight-bold mb-0">{{ $t('dashboard.orders.new_unit') }}</label>
                    </div>
                    <p class="text-xs text-secondary mb-0 ms-4">{{ $t('dashboard.orders.new_unit_desc') }}</p>
                  </div>
                  <div 
                    class="flex-fill p-3 border border-radius-lg cursor-pointer transition-all"
                    :class="!orderForm.is_new_unit ? 'border-primary bg-light-primary' : 'bg-gray-100'"
                    @click="orderForm.is_new_unit = false"
                    v-if="ownedIsps.length > 0"
                  >
                    <div class="form-check p-0 m-0">
                      <input class="form-check-input ms-0" type="radio" :value="false" v-model="orderForm.is_new_unit">
                      <label class="form-check-label ms-4 font-weight-bold mb-0">{{ $t('dashboard.orders.registered_unit') }}</label>
                    </div>
                    <p class="text-xs text-secondary mb-0 ms-4">{{ $t('dashboard.orders.registered_unit_desc') }}</p>
                  </div>
                </div>

                <!-- SELECT ACTION (RENEW vs CHANGE) -->
                <div v-if="!orderForm.is_new_unit" class="mb-3 animate__animated animate__fadeIn">
                  <label class="form-label text-xs font-weight-bold">{{ $t('dashboard.orders.what_to_do') }}</label>
                  <div class="d-flex gap-2">
                    <button 
                      type="button" 
                      class="btn btn-sm flex-fill" 
                      :class="orderForm.order_action === 'renew' ? 'btn-primary' : 'btn-outline-primary'"
                      @click="orderForm.order_action = 'renew'"
                    >
                      <i class="fas fa-sync-alt me-1"></i> {{ $t('dashboard.orders.renew_package') }}
                    </button>
                    <button 
                      type="button" 
                      class="btn btn-sm flex-fill" 
                      :class="orderForm.order_action === 'change' ? 'btn-primary' : 'btn-outline-primary'"
                      @click="orderForm.order_action = 'change'"
                    >
                      <i class="fas fa-exchange-alt me-1"></i> {{ $t('dashboard.orders.change_upgrade') }}
                    </button>
                  </div>
                </div>

                <div v-if="orderForm.is_new_unit" class="bg-gray-100 p-3 border-radius-lg mt-2">
                  <div class="mb-2">
                    <label class="form-label text-xs">{{ $t('dashboard.orders.company_name') }} <span class="text-danger">*</span></label>
                    <input
                      v-model="orderForm.company_name"
                      type="text"
                      class="form-control"
                      placeholder="e.g., Example ISP"
                      :required="orderForm.is_new_unit"
                    />
                  </div>
                </div>

                <div v-else class="mt-2">
                  <label class="form-label text-xs">{{ $t('dashboard.orders.select_unit') }} <span class="text-danger">*</span></label>
                  <select
                    v-model="orderForm.isp_id"
                    class="form-control"
                    :required="!orderForm.is_new_unit"
                  >
                    <option value="">-- {{ $t('dashboard.orders.select_unit') }} --</option>
                    <option
                      v-for="isp in ownedIsps"
                      :key="isp.id"
                      :value="isp.id"
                    >
                      {{ isp.company_name }} ({{ isp.subdomain || isp.custom_domain }})
                    </option>
                  </select>
                </div>
              </div>

              <hr class="horizontal dark my-4">

              <!-- Server Selection -->
              <div v-if="orderForm.is_new_unit" class="mb-3">
                <label class="form-label">{{ $t('dashboard.orders.server') }} <span class="text-danger">*</span></label>
                <select
                  v-model="orderForm.server_id"
                  class="form-control"
                  :required="orderForm.is_new_unit"
                >
                  <option value="">{{ $t('dashboard.orders.select_server') }}</option>
                  <option
                    v-for="server in availableServers"
                    :key="server.id"
                    :value="server.id"
                    :disabled="!server.is_available"
                  >
                    {{ server.name }} - {{ server.location || 'N/A' }} 
                    {{ server.is_available ? '✓' : '✗ Full' }}
                  </option>
                </select>
                <small class="text-muted">{{ $t('dashboard.orders.server_hint') }}</small>
              </div>

              <div class="mb-3">
                <label class="form-label">{{ $t('dashboard.orders.service') }} <span class="text-danger">*</span></label>
                <!-- Display Only for Renewal -->
                <div v-if="!orderForm.is_new_unit && orderForm.order_action === 'renew'" class="p-3 bg-gray-100 border-radius-lg border d-flex align-items-center">
                  <div class="flex-grow-1">
                    <h6 class="mb-0 text-sm">{{ orderForm.service_id ? (availableServices.find(s => s.id === orderForm.service_id)?.label || 'Loading...') : 'Loading...' }}</h6>
                    <p class="text-xs text-secondary mb-0">{{ $t('dashboard.orders.renew_extend_hint') }}</p>
                  </div>
                  <span class="badge badge-sm bg-gradient-info">{{ $t('dashboard.orders.renew_package') }}</span>
                </div>
                
                <!-- Select for New Unit or Change Package -->
                <select
                  v-else
                  v-model="orderForm.service_id"
                  class="form-control"
                  required
                >
                  <option value="">{{ $t('dashboard.orders.select_service') }}</option>
                  <option
                    v-for="service in filteredServices"
                    :key="service.id + '_' + (service.type || 'service')"
                    :value="service.id"
                  >
                    {{ service.label || service.name }}
                  </option>
                </select>
              </div>

              <!-- Domain & Referral (Only for New Units) -->
              <div v-if="orderForm.is_new_unit">
                <div class="mb-3">
                  <label class="form-label">{{ $t('dashboard.orders.domain_type') }}</label>
                  <select
                    v-model="orderForm.domain_type"
                    class="form-control"
                    :required="orderForm.is_new_unit"
                    @change="onDomainTypeChange"
                  >
                    <option value="subdomain">{{ $t('dashboard.orders.subdomain') }}</option>
                    <option value="custom">{{ $t('dashboard.orders.custom_domain') }}</option>
                  </select>
                </div>

                <div v-if="orderForm.domain_type === 'subdomain'" class="mb-3">
                  <label class="form-label">{{ $t('dashboard.orders.subdomain') }} <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <input
                      v-model="orderForm.subdomain"
                      type="text"
                      class="form-control"
                      placeholder="example"
                      :required="orderForm.domain_type === 'subdomain' && orderForm.is_new_unit"
                      :readonly="isExistingUnitForPackage"
                    />
                    <span class="input-group-text">{{ domainSuffix }}</span>
                  </div>
                  <small class="text-muted" v-if="!isExistingUnitForPackage">{{ $t('dashboard.orders.your_domain') }} {{ orderForm.subdomain }}{{ domainSuffix }}</small>
                  <small class="text-primary font-weight-bold" v-else>{{ $t('dashboard.orders.using_active_domain') }}</small>
                </div>

                <div v-if="orderForm.domain_type === 'custom'" class="mb-3">
                  <label class="form-label">{{ $t('dashboard.orders.custom_domain') }} <span class="text-danger">*</span></label>
                  <input
                    v-model="orderForm.domain"
                    type="text"
                    class="form-control"
                    placeholder="example.com"
                    :required="orderForm.domain_type === 'custom' && orderForm.is_new_unit"
                    :readonly="isExistingUnitForPackage"
                  />
                  <small class="text-muted" v-if="!isExistingUnitForPackage">{{ $t('dashboard.orders.custom_domain') }} (e.g., example.com)</small>
                  <small class="text-primary font-weight-bold" v-else>{{ $t('dashboard.orders.using_active_domain') }}</small>
                </div>

                <div class="mb-3">
                  <label class="form-label">{{ $t('dashboard.orders.referral_code') }}</label>
                  <input
                    v-model="orderForm.referral_code"
                    type="text"
                    class="form-control"
                    placeholder="Enter referral code for 10% discount"
                  />
                  <small class="text-muted">{{ $t('dashboard.orders.referral_hint') }}</small>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">{{ $t('dashboard.orders.notes') }}</label>
                <textarea
                  v-model="orderForm.notes"
                  class="form-control"
                  rows="3"
                  placeholder="Additional notes..."
                ></textarea>
              </div>

              <div class="d-flex justify-content-end gap-2">
                <button
                  type="button"
                  class="btn btn-secondary"
                  @click="showOrderModal = false"
                >
                  {{ $t('common.cancel') }}
                </button>
                <button
                  type="submit"
                  class="btn btn-primary"
                  :disabled="processing"
                >
                  <span v-if="processing">
                    <i class="fas fa-spinner fa-spin me-2"></i>
                    {{ $t('dashboard.orders.processing') }}
                  </span>
                  <span v-else>
                    <i class="fas fa-check me-2"></i>
                    {{ $t('dashboard.orders.submit_order') }}
                  </span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/api'
import LoadingOverlay from '@/components/LoadingOverlay.vue'
import notify, { confirm as confirmAction } from '@/utils/notify'

const route = useRoute()
const router = useRouter()

const orders = ref([])
const availableServices = ref([])
const availableServers = ref([])
const loading = ref(false)
const isLoading = ref(false) // Global loading
const processing = ref(false)
const processingPayment = ref(null)
const showOrderModal = ref(false)
const ownedIsps = ref([])
const baseDomain = ref('localhost')

// Domain suffix is dynamic based on system settings
const domainSuffix = computed(() => {
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
  
  return `.${base}${port}`
})

const orderForm = ref({
  is_new_unit: true,
  order_action: 'renew', // 'renew' or 'change'
  isp_id: '',
  company_name: '',
  server_id: '',
  service_id: '',
  billing_cycle: 'monthly',
  domain_type: 'subdomain',
  subdomain: '',
  domain: '',
  notes: '',
  referral_code: '',
})

const isExistingUnitForPackage = computed(() => {
  if (orderForm.value.is_new_unit) return false
  const selectedItem = availableServices.value.find(s => s.id === orderForm.value.service_id)
  return selectedItem && selectedItem.type === 'package'
})

const filteredServices = computed(() => {
  let services = availableServices.value;
  
  if (orderForm.value.is_new_unit) return services;
  
  // Filter out trials for existing units
  services = services.filter(s => {
    // Robust checks for trial status
    const priceValue = parseFloat(s.price || 0);
    const slug = (s.slug || '').toLowerCase();
    const name = (s.name || '').toLowerCase();
    const isTrial = priceValue === 0 || 
                    slug === 'trial' || 
                    name.includes('trial') || 
                    (s.trial_days && parseInt(s.trial_days) > 0);
    
    return !isTrial;
  });

  // If in 'change' action, filter out the current package for clarity
  if (orderForm.value.order_action === 'change' && orderForm.value.isp_id) {
    const isp = ownedIsps.value.find(i => i.id === orderForm.value.isp_id);
    if (isp && isp.subscription_package_id) {
      services = services.filter(s => s.id !== isp.subscription_package_id);
    }
  }

  return services;
})

watch(() => orderForm.value.isp_id, (newIspId) => {
  if (!orderForm.value.is_new_unit && newIspId) {
    const isp = ownedIsps.value.find(i => i.id === newIspId)
    if (isp) {
      if (isp.custom_domain) {
        orderForm.value.domain_type = 'custom'
        orderForm.value.domain = isp.custom_domain
      } else {
        orderForm.value.domain_type = 'subdomain'
        orderForm.value.subdomain = isp.subdomain
      }

      // Auto-select current package if in 'renew' mode
      if (orderForm.value.order_action === 'renew' && isp.subscription_package_id) {
        orderForm.value.service_id = isp.subscription_package_id;
      }
    }
  }
})

// Watch for order action changes to update service_id automatically
watch(() => orderForm.value.order_action, (newAction) => {
  if (!orderForm.value.is_new_unit && orderForm.value.isp_id) {
    const isp = ownedIsps.value.find(i => i.id === orderForm.value.isp_id);
    if (newAction === 'renew') {
      if (isp && isp.subscription_package_id) {
        orderForm.value.service_id = isp.subscription_package_id;
      }
    } else {
      // Clear selection when switching to 'change' to force user to pick a new package
      orderForm.value.service_id = '';
    }
  }
})

watch(() => orderForm.value.is_new_unit, (isNew) => {
  if (isNew) {
    orderForm.value.isp_id = ''
    orderForm.value.subdomain = ''
    orderForm.value.domain = ''
  } else if (ownedIsps.value.length > 0) {
    orderForm.value.isp_id = ownedIsps.value[0].id
  }
  
  // Reset service selection if it's no longer valid for the current unit type
  const isValidService = filteredServices.value.some(s => s.id === orderForm.value.service_id);
  if (!isValidService) {
    orderForm.value.service_id = '';
  }
})

onMounted(async () => {
  isLoading.value = true
  await Promise.all([
    fetchOrders(),
    fetchAvailableServices(),
    fetchServers(),
    fetchOwnedIsps(),
    fetchMainDomain()
  ])
  
  // If service_id in query, open modal
  if (route.query.service_id) {
    orderForm.value.service_id = route.query.service_id
    showOrderModal.value = true
  }

  // Handle flow from dashboard
  if (route.query.action === 'new_unit') {
    orderForm.value.is_new_unit = true
    showOrderModal.value = true
  } else if (route.query.isp_id) {
    orderForm.value.is_new_unit = false
    orderForm.value.isp_id = parseInt(route.query.isp_id)
    showOrderModal.value = true
  }
  
  isLoading.value = false
})

const fetchOwnedIsps = async () => {
  try {
    const response = await api.get('/isp-admin/client-area/owned-isps')
    ownedIsps.value = response.data
    
    if (ownedIsps.value.length > 0) {
      orderForm.value.is_new_unit = false
      orderForm.value.isp_id = ownedIsps.value[0].id
    } else {
      orderForm.value.is_new_unit = true
    }
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

watch(() => route.query.service_id, (newVal) => {
  if (newVal) {
    orderForm.value.service_id = newVal
    showOrderModal.value = true
  }
})

const fetchOrders = async () => {
  isLoading.value = true
  try {
    const response = await api.get('/isp-admin/client-area/orders')
    orders.value = response.data
  } catch (error) {
    console.error('Error fetching orders:', error)
    notify('error', 'Error', 'Failed to fetch orders')
  } finally {
    isLoading.value = false
  }
}

const fetchAvailableServices = async () => {
  try {
    const [servicesRes, packagesRes] = await Promise.all([
      api.get('/isp-admin/client-area/services'),
      api.get('/isp-admin/client-area/packages')
    ])
    
    // Combine both Services and Subscription Packages for selection
    const services = servicesRes.data.filter(s => s.is_active).map(s => ({ ...s, type: 'service', label: s.name }))
    const packages = packagesRes.data.map(p => ({ ...p, type: 'package', label: `[Package] ${p.name}` }))
    
    availableServices.value = [...packages, ...services]
  } catch (error) {
    console.error('Error fetching services/packages:', error)
  }
}

const fetchServers = async () => {
  try {
    const response = await api.get('/isp-admin/client-area/servers')
    availableServers.value = response.data.map(server => ({
      ...server,
      is_available: server.is_active && (!server.capacity || server.current_users < server.capacity)
    }))
  } catch (error) {
    console.error('Error fetching servers:', error)
  }
}

const onDomainTypeChange = () => {
  if (orderForm.value.domain_type === 'subdomain') {
    orderForm.value.domain = ''
  } else {
    orderForm.value.subdomain = ''
  }
}

const openCreateOrderModal = () => {
  showOrderModal.value = true
}

const submitOrder = async () => {
  processing.value = true
  isLoading.value = true
  try {
    const selectedItem = availableServices.value.find(s => s.id === orderForm.value.service_id)
    const payload = { ...orderForm.value }
    
    // Determine if it's a package or service
    if (selectedItem && selectedItem.type === 'package') {
      payload.package_id = selectedItem.id
      delete payload.service_id
    }
    
    // Remove individual domain parts if using the other type
    if (payload.domain_type === 'subdomain') {
       delete payload.domain
    } else {
       delete payload.subdomain
    }

    // Prune payload for existing units to avoid validation noise
    if (!payload.is_new_unit) {
      delete payload.company_name
      delete payload.referral_code
      delete payload.server_id
      delete payload.domain_type
    }

    const response = await api.post('/isp-admin/client-area/orders', payload)
    
    if (response.data.success) {
      showOrderModal.value = false;
      await Promise.all([
        fetchOrders(),
        fetchOwnedIsps()
      ]);
      
      const order = response.data.order;
      if (order && (order.status === 'pending_payment' || order.status === 'pending')) {
           if (await confirmAction('Order Successful', 'Your order is being processed. Proceed to payment?', 'success')) {
               router.push({ path: '/client-area/invoices', query: { pay_recent: 'true' } });
           }
      } else {
        // Trial or requires approval or active
        notify('success', 'Order Successful', response.data.message || 'Order submitted successfully!');
      }
      
      // Reset form
      orderForm.value = {
        is_new_unit: ownedIsps.value.length === 0,
        isp_id: ownedIsps.value.length > 0 ? ownedIsps.value[0].id : '',
        company_name: '',
        server_id: '',
        service_id: '',
        billing_cycle: 'monthly',
        domain_type: 'subdomain',
        subdomain: '',
        domain: '',
        notes: '',
        referral_code: '',
      }
    }
  } catch (error) {
    console.error('Error submitting order:', error)
    notify('error', 'Failed', error.response?.data?.message || 'Failed to submit order')
  } finally {
    processing.value = false
    isLoading.value = false
  }
}

const cancelOrder = async (orderId) => {
  if (!await confirmAction('Cancel Order', 'Are you sure you want to cancel this order?', 'warning')) return
  
  isLoading.value = true
  try {
    await api.post(`/isp-admin/client-area/orders/${orderId}/cancel`)
    await fetchOrders()
    notify('success', 'Cancelled', 'Order cancelled successfully')
  } catch (error) {
    console.error('Error cancelling order:', error)
    notify('error', 'Error', 'Failed to cancel order')
  } finally {
    isLoading.value = false
  }
}

const deleteOrder = async (orderId) => {
  if (!await confirmAction('Delete Order', 'Are you sure you want to delete this order? This action cannot be undone.', 'warning')) return
  
  isLoading.value = true
  try {
    await api.delete(`/isp-admin/client-area/orders/${orderId}`)
    await fetchOrders()
    notify('success', 'Deleted', 'Order deleted successfully')
  } catch (error) {
    console.error('Error deleting order:', error)
    notify('error', 'Error', 'Failed to delete order')
  } finally {
    isLoading.value = false
  }
}

const payOrder = async (orderId) => {
  processingPayment.value = orderId
  try {
    // Find unpaid invoice for this order
    const invoicesRes = await api.get('/isp-admin/client-area/invoices')
    const unpaidInvoice = invoicesRes.data.find(inv => 
      inv.subscription_id === orderId && inv.payment_status === 'unpaid'
    )

    if (unpaidInvoice) {
        // Redirect to invoices page to pay (centralized logic)
        // Pass query param to auto-open the payment modal
        router.push({ path: '/client-area/invoices', query: { pay_invoice: unpaidInvoice.id } });
    } else {
        notify('warning', 'Notice', 'No unpaid invoice found for this order. It might be already paid or not generated yet.')
    }
  } catch (error) {
    console.error('Payment error:', error)
    notify('error', 'Error', 'Failed to find invoice: ' + (error.response?.data?.message || error.message))
  } finally {
    processingPayment.value = null
  }
}

const retryPayment = async (orderId) => {
  processingPayment.value = orderId
  try {
    const response = await api.post(`/isp-admin/orders/${orderId}/retry-payment`)
    
    if (response.data.success) {
      await fetchOrders()
      
      // If payment URL is provided, redirect to payment
      if (response.data.data.invoice && response.data.data.invoice.id) {
         router.push({ path: '/client-area/invoices', query: { pay_invoice: response.data.data.invoice.id } });
      } else {
        notify('success', 'Payment Retry', 'Payment retry initiated. Please check your invoices to complete payment.')
        router.push('/client-area/invoices')
      }
    }
  } catch (error) {
    console.error('Retry payment error:', error)
    notify('error', 'Failed', error.response?.data?.message || 'Failed to retry payment')
  } finally {
    processingPayment.value = null
  }
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(amount)
}

const formatDate = (date) => {
  return new Date(date).toLocaleString('en-US')
}
</script>
