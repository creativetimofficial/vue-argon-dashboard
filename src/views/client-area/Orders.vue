<template>
  <div class="py-4 container-fluid">
    <LoadingOverlay :active="isLoading" />
    <div class="row">
      <div class="col-12">
        <div class="card mb-4 min-vh-75">
          <div class="card-header pb-0 d-flex justify-content-between align-items-center">
            <h6>My Orders</h6>
            <button
              class="btn btn-primary btn-sm"
              @click="openCreateOrderModal"
            >
              <i class="fas fa-plus me-2"></i>
              New Order
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
                      REFERENCE
                    </th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                      PRODUCT
                    </th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                      PRICE
                    </th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                      CREATED AT
                    </th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                      SERVICE ID
                    </th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                      STATUS
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
                            Pay
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
                            <i class="fas fa-redo"></i> Retry
                          </span>
                        </button>
                        
                        <button
                          v-if="order.status === 'pending_payment'"
                          class="btn btn-secondary btn-sm mb-0"
                          @click="cancelOrder(order.id)"
                        >
                          Cancel
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
                      <p class="text-muted mb-0">No orders found</p>
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
            <h5 class="modal-title m-0">Order New Service</h5>
            <button
              type="button"
              class="btn-close text-dark"
              @click="showOrderModal = false"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="submitOrder">
              <!-- Server Selection -->
              <div class="mb-3">
                <label class="form-label">Server <span class="text-danger">*</span></label>
                <select
                  v-model="orderForm.server_id"
                  class="form-control"
                  required
                >
                  <option value="">Select Server</option>
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
                <small class="text-muted">Choose server for your VPN/PPPoE account</small>
              </div>

              <div class="mb-3">
                <label class="form-label">Service</label>
                <select
                  v-model="orderForm.service_id"
                  class="form-control"
                  required
                >
                  <option value="">Select Service</option>
                  <option
                    v-for="service in availableServices"
                    :key="service.id + '_' + (service.type || 'service')"
                    :value="service.id"
                  >
                    {{ service.label || service.name }}
                  </option>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label">Domain Type</label>
                <select
                  v-model="orderForm.domain_type"
                  class="form-control"
                  required
                  @change="onDomainTypeChange"
                >
                  <option value="subdomain">Subdomain</option>
                  <option value="custom">Custom Domain</option>
                </select>
              </div>

              <div v-if="orderForm.domain_type === 'subdomain'" class="mb-3">
                <label class="form-label">Subdomain</label>
                <div class="input-group">
                  <input
                    v-model="orderForm.subdomain"
                    type="text"
                    class="form-control"
                    placeholder="irvan"
                    required
                  />
                  <span class="input-group-text">{{ domainSuffix }}</span>
                </div>
                <small class="text-muted">Your domain will be: {{ orderForm.subdomain }}{{ domainSuffix }}</small>
              </div>

              <div v-if="orderForm.domain_type === 'custom'" class="mb-3">
                <label class="form-label">Custom Domain</label>
                <input
                  v-model="orderForm.domain"
                  type="text"
                  class="form-control"
                  placeholder="example.com"
                  required
                />
                <small class="text-muted">Enter your custom domain (e.g., example.com)</small>
              </div>

              <div class="mb-3">
                <label class="form-label">Referral Code (Optional)</label>
                <input
                  v-model="orderForm.referral_code"
                  type="text"
                  class="form-control"
                  placeholder="Enter referral code for 10% discount"
                />
                <small class="text-muted">Get 10% discount by entering a valid referral code.</small>
              </div>

              <div class="mb-3">
                <label class="form-label">Notes (Optional)</label>
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
                  Cancel
                </button>
                <button
                  type="submit"
                  class="btn btn-primary"
                  :disabled="processing"
                >
                  <span v-if="processing">
                    <i class="fas fa-spinner fa-spin me-2"></i>
                    Processing...
                  </span>
                  <span v-else>
                    <i class="fas fa-check me-2"></i>
                    Submit Order
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

// Domain suffix is dynamic based on hosting environment
const domainSuffix = computed(() => {
  const host = window.location.hostname;
  return `.${host}`;
})

const orderForm = ref({
  server_id: '',
  service_id: '',
  billing_cycle: 'monthly',
  domain_type: 'subdomain',
  subdomain: '',
  domain: '',
  notes: '',
  referral_code: '',
})

onMounted(async () => {
  await Promise.all([
    fetchOrders(),
    fetchAvailableServices(),
    fetchServers()
  ])
  
  // If service_id in query, open modal
  if (route.query.service_id) {
    orderForm.value.service_id = route.query.service_id
    showOrderModal.value = true
  }
})

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

    const response = await api.post('/isp-admin/client-area/orders', payload)
    
    if (response.data.success) {
      showOrderModal.value = false;
      await fetchOrders();
      
      const order = response.data.order;
      if (order && (order.status === 'pending_payment' || order.status === 'pending')) {
           if (await confirmAction('Order Created', 'Order created successfully! Do you want to pay now?', 'success')) {
               router.push({ path: '/client-area/invoices', query: { pay_recent: 'true' } });
           }
      } else {
        // Trial or requires approval or active
        notify('success', 'Success', response.data.message || 'Order submitted successfully!');
      }
      
      // Reset form
      orderForm.value = {
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
  return new Date(date).toLocaleString('id-ID')
}
</script>
