<template>
  <div class="py-4 container-fluid">
    <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header pb-0">
          <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Orders Table</h6>
            <button
              class="btn btn-primary btn-sm"
              @click="showOrderModal = true"
            >
              <i class="fas fa-plus me-2"></i>
              New Order
            </button>
          </div>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
          <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
          <div v-else class="table-responsive p-0">
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
                    <a v-if="order.service_id" href="#" class="text-xs font-weight-bold mb-0">#{{ order.service_id }}</a>
                    <span v-else class="text-secondary text-xs">-</span>
                  </td>
                  <td class="align-middle text-center text-sm">
                    <span
                      class="badge badge-sm"
                      :class="{
                        'bg-gradient-warning': order.status === 'pending',
                        'bg-gradient-success': order.status === 'approved' || order.status === 'active',
                        'bg-gradient-danger': order.status === 'cancelled' || order.status === 'terminated',
                      }"
                    >
                      {{ order.status.toUpperCase() }}
                    </span>
                  </td>
                  <td class="align-middle">
                    <div class="d-flex gap-2 justify-content-center">
                      <button
                        v-if="order.status === 'pending'"
                        class="btn btn-warning btn-sm"
                        @click="cancelOrder(order.id)"
                      >
                        Cancel
                      </button>
                      <button
                        class="btn btn-danger btn-sm"
                        @click="deleteOrder(order.id)"
                      >
                        <i class="fas fa-trash"></i>
                      </button>
                      <a
                        v-if="order.status === 'pending'"
                        href="#"
                        class="btn btn-info btn-sm"
                      >
                        Hubungi Admin
                      </a>
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
    style="display: block"
    tabindex="-1"
  >
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Order New Service</h5>
          <button
            type="button"
            class="btn-close"
            @click="showOrderModal = false"
          ></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="submitOrder">
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

            <!-- Billing cycle is defined by the package/service -->

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
  <div
    v-if="showOrderModal"
    class="modal-backdrop fade show"
    @click="showOrderModal = false"
  ></div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/api'

const route = useRoute()
const router = useRouter()

const orders = ref([])
const availableServices = ref([])
const loading = ref(false)
const processing = ref(false)
const showOrderModal = ref(false)

// Domain suffix is dynamic based on hosting environment
const domainSuffix = computed(() => {
  const host = window.location.hostname;
  return `.${host}`;
})

const orderForm = ref({
  service_id: '',
  billing_cycle: 'monthly',
  domain_type: 'subdomain',
  subdomain: '',
  domain: '',
  notes: '',
})

onMounted(async () => {
  await Promise.all([
    fetchOrders(),
    fetchAvailableServices()
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
  loading.value = true
  try {
    const response = await api.get('/isp-admin/client-area/orders')
    orders.value = response.data
  } catch (error) {
    console.error('Error fetching orders:', error)
  } finally {
    loading.value = false
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

const onDomainTypeChange = () => {
  if (orderForm.value.domain_type === 'subdomain') {
    orderForm.value.domain = ''
  } else {
    orderForm.value.subdomain = ''
  }
}

const submitOrder = async () => {
  processing.value = true
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
      if (response.data.payment_url) {
        window.location.href = response.data.payment_url
        return
      }
      alert(response.data.message || 'Order submitted successfully!')
      showOrderModal.value = false
      await fetchOrders()
      
      // Reset form
      orderForm.value = {
        service_id: '',
        billing_cycle: 'monthly',
        domain_type: 'subdomain',
        subdomain: '',
        domain: '',
        notes: '',
      }
    }
  } catch (error) {
    console.error('Error submitting order:', error)
    alert(error.response?.data?.message || 'Failed to submit order')
  } finally {
    processing.value = false
  }
}

const cancelOrder = async (orderId) => {
  if (!confirm('Are you sure you want to cancel this order?')) return
  
  try {
    await api.post(`/isp-admin/client-area/orders/${orderId}/cancel`)
    await fetchOrders()
  } catch (error) {
    console.error('Error cancelling order:', error)
    alert('Failed to cancel order')
  }
}

const deleteOrder = async (orderId) => {
  if (!confirm('Are you sure you want to delete this order?')) return
  
  try {
    await api.delete(`/isp-admin/client-area/orders/${orderId}`)
    await fetchOrders()
  } catch (error) {
    console.error('Error deleting order:', error)
    alert('Failed to delete order')
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
