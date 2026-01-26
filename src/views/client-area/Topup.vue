<template>
  <div class="py-4 container-fluid">
    <div class="row">
      <div class="col-lg-8">
        <div class="card">
          <div class="card-header pb-0">
            <h6>Topup Balance</h6>
          </div>
          <div class="card-body">
            <form @submit.prevent="handleTopup">
              <div class="mb-4">
                <label class="form-label">Select Amount</label>
                <div class="row g-2">
                  <div v-for="preset in presets" :key="preset" class="col-4 col-md-3">
                    <button
                      type="button"
                      class="btn w-100"
                      :class="amount === preset ? 'btn-primary' : 'btn-outline-primary'"
                      @click="amount = preset"
                    >
                      {{ formatCurrency(preset) }}
                    </button>
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Custom Amount</label>
                <div class="input-group">
                  <span class="input-group-text">Rp</span>
                  <input
                    v-model.number="amount"
                    type="number"
                    class="form-control"
                    placeholder="Enter amount"
                    min="10000"
                  />
                </div>
              </div>

              <div class="mb-4">
                <label class="form-label">Payment Method</label>
                <select v-model="paymentMethod" class="form-control" required>
                  <option value="bank_transfer">Bank Transfer (Manual Review)</option>
                  <option value="midtrans">Midtrans (Virtual Account / QRIS)</option>
                </select>
              </div>

              <div class="d-grid mt-4">
                <button
                  type="submit"
                  class="btn btn-primary btn-lg"
                  :disabled="processing || amount < 10000"
                >
                  <span v-if="processing">
                    <i class="fas fa-spinner fa-spin me-2"></i>
                    Processing...
                  </span>
                  <span v-else>
                    Confirm Topup
                  </span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="col-lg-4 mt-4 mt-lg-0">
        <div class="card h-100">
          <div class="card-header pb-0">
            <h6>Recent Activities</h6>
          </div>
          <div class="card-body p-3">
            <div v-if="history.length === 0" class="text-center py-4">
              <p class="text-muted small">No recent topup history</p>
            </div>
            <ul class="list-group">
              <li
                v-for="item in history"
                :key="item.id"
                class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg"
              >
                <div class="d-flex align-items-center">
                  <button
                    class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"
                  >
                    <i class="fas fa-arrow-up"></i>
                  </button>
                  <div class="d-flex flex-column">
                    <h6 class="mb-1 text-dark text-sm">Topup Balance</h6>
                    <span class="text-xs">{{ formatDate(item.created_at) }}</span>
                  </div>
                </div>
                <div class="d-flex align-items-center text-success text-gradient text-sm font-weight-bold">
                  + {{ formatCurrency(item.amount) }}
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const presets = [50000, 100000, 250000, 500000, 1000000, 2500000]
const amount = ref(100000)
const paymentMethod = ref('bank_transfer')
const processing = ref(false)
const history = ref([])

onMounted(async () => {
  // Fetch history (simulated for now or point to real endpoint if exists)
  fetchHistory()
})

const fetchHistory = async () => {
  try {
    // Assuming there's a transactions endpoint
    const response = await api.get('/isp-admin/client-area/stats')
    // Mock history from stats logic if available, or just empty
    history.value = response.data.recent_transactions || []
  } catch (error) {
    console.error('Error fetching history:', error)
  }
}

const handleTopup = async () => {
  processing.value = true
  try {
    // This endpoint should be implemented in the backend
    const response = await api.post('/isp-admin/client-area/topup', {
      amount: amount.value,
      method: paymentMethod.value
    })
    
    if (response.data.success) {
      alert(response.data.message || 'Topup request created!')
      if (response.data.payment_url) {
        window.open(response.data.payment_url, '_blank')
      }
      fetchHistory()
    }
  } catch (error) {
    console.error('Topup error:', error)
    alert(error.response?.data?.message || 'Failed to process topup. Please try again.')
  } finally {
    processing.value = false
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
  return new Date(date).toLocaleDateString('id-ID')
}
</script>
