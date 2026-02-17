<template>
  <div class="py-4 container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center">
              <h6 class="mb-0">ISP Orders Management</h6>
              <div class="d-flex gap-2">
                <select v-model="filterStatus" class="form-select form-select-sm" style="width: auto;">
                  <option value="">All Status</option>
                  <option value="pending">Pending</option>
                  <option value="approved">Approved</option>
                  <option value="active">Active</option>
                  <option value="cancelled">Cancelled</option>
                </select>
              </div>
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
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">ISP</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Service</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Reference</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Domain</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Price</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Created At</th>
                    <th class="text-secondary opacity-7"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="order in filteredOrders" :key="order.id">
                    <td>
                      <div class="d-flex px-2 py-1">
                        <h6 class="mb-0 text-sm">{{ order.id }}</h6>
                      </div>
                    </td>
                    <td>
                      <p class="text-xs font-weight-bold mb-0">{{ order.isp?.company_name || order.isp?.email || 'N/A' }}</p>
                    </td>
                    <td class="align-middle text-center text-sm">
                      <span class="text-secondary text-xs font-weight-bold">{{ order.service?.name || order.subscription_package?.name || 'N/A' }}</span>
                      <span v-if="order.service?.trial_days > 0" class="badge badge-sm bg-warning ms-1">Trial</span>
                    </td>
                    <td class="align-middle text-center">
                      <p class="text-xs font-weight-bold mb-0">{{ order.reference }}</p>
                    </td>
                    <td class="align-middle text-center">
                      <p class="text-xs font-weight-bold mb-0">{{ order.domain || '-' }}</p>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">{{ formatCurrency(order.price) }}</span>
                    </td>
                    <td class="align-middle text-center text-sm">
                      <span
                        class="badge badge-sm"
                        :class="{
                          'bg-gradient-warning': order.status === 'pending',
                          'bg-gradient-info': order.status === 'approved',
                          'bg-gradient-success': order.status === 'active',
                          'bg-gradient-danger': order.status === 'cancelled' || order.status === 'terminated',
                        }"
                      >
                        {{ order.status.toUpperCase() }}
                      </span>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">{{ formatDate(order.created_at) }}</span>
                    </td>
                    <td class="align-middle">
                      <div class="d-flex gap-2 justify-content-center">
                        <button
                          v-if="order.status === 'pending'"
                          class="btn btn-success btn-sm"
                          @click="approveOrder(order.id)"
                          :disabled="processing"
                        >
                          <i class="fas fa-check me-1"></i>
                          Approve
                        </button>
                        <button
                          v-if="order.status === 'pending'"
                          class="btn btn-danger btn-sm"
                          @click="rejectOrder(order.id)"
                          :disabled="processing"
                        >
                          <i class="fas fa-times me-1"></i>
                          Reject
                        </button>
                        <button
                          class="btn btn-info btn-sm"
                          @click="viewOrderDetail(order.id)"
                        >
                          <i class="fas fa-eye"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="filteredOrders.length === 0">
                    <td colspan="9" class="text-center py-4">
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

    <!-- Order Detail Modal -->
    <div
      v-if="selectedOrder"
      class="modal fade show"
      style="display: block"
      tabindex="-1"
    >
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Order Detail - {{ selectedOrder.reference }}</h5>
            <button
              type="button"
              class="btn-close"
              @click="selectedOrder = null"
            ></button>
          </div>
          <div class="modal-body">
            <div v-if="loadingDetail" class="text-center py-3">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
            <div v-else-if="orderDetail">
              <div class="row mb-3">
                <div class="col-md-6">
                  <p class="mb-1"><strong>ISP:</strong> {{ orderDetail.isp?.company_name || orderDetail.isp?.email }}</p>
                  <p class="mb-1"><strong>Service:</strong> {{ orderDetail.service?.name || orderDetail.subscription_package?.name || 'N/A' }}</p>
                  <p class="mb-1"><strong>Price:</strong> {{ formatCurrency(orderDetail.price) }}</p>
                  <p class="mb-1"><strong>Billing Cycle:</strong> {{ orderDetail.billing_cycle }}</p>
                </div>
                <div class="col-md-6">
                  <p class="mb-1"><strong>Domain Type:</strong> {{ orderDetail.domain_type }}</p>
                  <p class="mb-1"><strong>Domain:</strong> {{ orderDetail.domain || '-' }}</p>
                  <p class="mb-1"><strong>Status:</strong> 
                    <span
                      class="badge"
                      :class="{
                        'bg-warning': orderDetail.status === 'pending',
                        'bg-info': orderDetail.status === 'approved',
                        'bg-success': orderDetail.status === 'active',
                      }"
                    >
                      {{ orderDetail.status.toUpperCase() }}
                    </span>
                  </p>
                  <p class="mb-1"><strong>Created At:</strong> {{ formatDate(orderDetail.created_at) }}</p>
                </div>
              </div>
              <div v-if="orderDetail.notes" class="mb-3">
                <strong>Notes:</strong>
                <p class="mb-0">{{ orderDetail.notes }}</p>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              @click="selectedOrder = null"
            >
              Close
            </button>
            <button
              v-if="orderDetail && orderDetail.status === 'pending'"
              type="button"
              class="btn btn-success"
              @click="approveOrder(orderDetail.id)"
              :disabled="processing"
            >
              Approve Order
            </button>
          </div>
        </div>
      </div>
    </div>
    <div
      v-if="selectedOrder"
      class="modal-backdrop fade show"
      @click="selectedOrder = null"
    ></div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '@/services/api'

const orders = ref([])
const selectedOrder = ref(null)
const orderDetail = ref(null)
const loading = ref(false)
const loadingDetail = ref(false)
const processing = ref(false)
const filterStatus = ref('')

const filteredOrders = computed(() => {
  if (!filterStatus.value) return orders.value
  return orders.value.filter(o => o.status === filterStatus.value)
})

onMounted(async () => {
  await fetchOrders()
})

const fetchOrders = async () => {
  loading.value = true
  try {
    const response = await api.get('/super-admin/isp-orders')
    orders.value = response.data
  } catch (error) {
    console.error('Error fetching orders:', error)
    alert('Failed to load orders')
  } finally {
    loading.value = false
  }
}

const approveOrder = async (orderId) => {
  if (!confirm('Approve this order?')) return

  processing.value = true
  try {
    const response = await api.post(`/super-admin/isp-orders/${orderId}/approve`)
    
    if (response.data.success) {
      alert('Order approved successfully!')
      await fetchOrders()
      if (selectedOrder.value && selectedOrder.value.id === orderId) {
        selectedOrder.value = null
        orderDetail.value = null
      }
    }
  } catch (error) {
    console.error('Error approving order:', error)
    alert(error.response?.data?.message || 'Failed to approve order')
  } finally {
    processing.value = false
  }
}

const rejectOrder = async (orderId) => {
  const reason = prompt('Rejection reason:')
  if (!reason) return

  processing.value = true
  try {
    const response = await api.post(`/super-admin/isp-orders/${orderId}/reject`, {
      reason: reason
    })
    
    if (response.data.success) {
      alert('Order rejected successfully!')
      await fetchOrders()
      if (selectedOrder.value && selectedOrder.value.id === orderId) {
        selectedOrder.value = null
        orderDetail.value = null
      }
    }
  } catch (error) {
    console.error('Error rejecting order:', error)
    alert(error.response?.data?.message || 'Failed to reject order')
  } finally {
    processing.value = false
  }
}

const viewOrderDetail = async (orderId) => {
  selectedOrder.value = { id: orderId }
  loadingDetail.value = true
  try {
    const response = await api.get(`/super-admin/isp-orders/${orderId}`)
    orderDetail.value = response.data
  } catch (error) {
    console.error('Error fetching order detail:', error)
    alert('Failed to load order details')
  } finally {
    loadingDetail.value = false
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
  if (!date) return '-'
  return new Date(date).toLocaleString('id-ID')
}
</script>
