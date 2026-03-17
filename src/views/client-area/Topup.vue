<template>
  <div class="py-4 container-fluid">
    <LoadingOverlay :active="isLoading" />
    <div class="row">
      <div class="col-lg-8">
        <div class="card">
          <div class="card-header pb-0">
            <h6>{{ $t('dashboard.client_profile.topup') }}</h6>
          </div>
          <div class="card-body">
            <div class="alert alert-info text-white mb-4 d-flex align-items-center justify-content-between">
              <div>
                <span class="text-sm opacity-8">{{ $t('dashboard.client_profile.balance') }}</span>
                <h4 class="text-white mb-0">{{ formatCurrency(balance) }}</h4>
              </div>
              <i class="fas fa-wallet fa-2x opacity-5"></i>
            </div>
            <form @submit.prevent="handleTopup">
              <div class="mb-4">
                <label class="form-label">{{ $t('dashboard.topup.select_amount') }}</label>
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
                <label class="form-label">{{ $t('dashboard.topup.custom_amount') }}</label>
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

              <div class="d-grid mt-4">
                <button
                  type="submit"
                  class="btn btn-primary btn-lg"
                  :disabled="processing || amount < 10000"
                >
                  <span v-if="processing">
                    <i class="fas fa-spinner fa-spin me-2"></i>
                    {{ $t('dashboard.topup.processing') }}
                  </span>
                  <span v-else>
                    {{ $t('dashboard.topup.confirm_topup') }}
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
            <h6>{{ $t('dashboard.topup.recent_activity') }}</h6>
          </div>
          <div class="card-body p-3">
            <div v-if="!history || history.length === 0" class="text-center py-4">
              <p class="text-muted small">{{ $t('dashboard.topup.no_history') }}</p>
            </div>
            <ul class="list-group">
              <li
                v-for="item in history"
                :key="item.id"
                class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg"
              >
                <div class="d-flex align-items-center">
                  <button
                    class="btn btn-icon-only btn-rounded mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"
                    :class="getStatusIconClass(item.status)"
                  >
                    <i :class="getStatusIcon(item.status)"></i>
                  </button>
                  <div class="d-flex flex-column">
                    <h6 class="mb-1 text-dark text-sm">{{ $t('dashboard.client_profile.topup') }}</h6>
                    <span class="text-xs">{{ formatDate(item.created_at) }}</span>
                    <span class="text-xs mt-1" :class="getStatusTextClass(item.status)">
                      {{ item.status ? item.status.toUpperCase() : 'PENDING' }}
                    </span>
                  </div>
                </div>
                <div class="d-flex align-items-center text-sm font-weight-bold" :class="getStatusTextClass(item.status)">
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
import LoadingOverlay from '@/components/LoadingOverlay.vue'
import notify from '@/utils/notify'

const presets = [50000, 100000, 250000, 500000, 1000000, 2500000]
const amount = ref(100000)
const processing = ref(false)
const isLoading = ref(false)
const history = ref([])
const balance = ref(0)

onMounted(async () => {
  isLoading.value = true
    await Promise.all([fetchHistory(), fetchBalance()])
    isLoading.value = false
})

const loadSnap = (clientKey) => {
  return new Promise((resolve, reject) => {
    if (window.snap && document.querySelector(`script[data-client-key="${clientKey}"]`)) {
        resolve();
        return;
    }
    
    // Remove existing snap script if any (to update key)
    const oldScript = document.querySelector('script[src*="snap.js"]');
    if (oldScript) {
        oldScript.remove();
    }

    const scriptUrl = clientKey && clientKey.startsWith('SB-') 
        ? 'https://app.sandbox.midtrans.com/snap/snap.js' 
        : 'https://app.midtrans.com/snap/snap.js';

    const script = document.createElement('script');
    script.src = scriptUrl;
    script.setAttribute('data-client-key', clientKey);
    script.onload = () => resolve();
    script.onerror = () => reject(new Error('Failed to load Snap JS'));
    document.head.appendChild(script);
  });
}


const fetchHistory = async () => {
  try {
    const response = await api.get('/isp-admin/client-area/topup-history', {
        params: { per_page: 5 }
    })
    history.value = response.data.data
  } catch (error) {
    console.error('Error fetching history:', error)
  }
}

const fetchBalance = async () => {
  try {
     const response = await api.get('/isp-admin/client-area/balance')
     balance.value = response.data.balance || 0
  } catch (error) {
      console.error('Error fetching balance', error)
  }
}

const handleTopup = async () => {
  processing.value = true
  try {
    const response = await api.post('/isp-admin/client-area/topup', {
      amount: amount.value,
    })
    
    const data = response.data
    
    if (data.success) {
      if (data.gateway === 'Midtrans' && data.payment_token) {
          try {
              if (data.client_key) {
                  await loadSnap(data.client_key);
              } else {
                  await loadSnap(import.meta.env.VITE_MIDTRANS_CLIENT_KEY || 'SB-Mid-client-TestKey');
              }
              
              window.snap.pay(data.payment_token, {
                  onSuccess: async function(result) {
                      notify('success', 'Success', "Payment Successful! Your balance will be updated soon.");
                      await fetchBalance();
                      await fetchHistory();
                  },
                  onPending: function(result) {
                      notify('info', 'Pending', "Waiting for payment!");
                  },
                  onError: function(result) {
                      notify('error', 'Failed', "Payment failed!");
                  },
                  onClose: function() {
                      console.log('Customer closed the popup without finishing the payment');
                  }
              });
          } catch(e) {
              notify('error', 'Error', 'Failed to load Midtrans payment system.');
          }
      } else if (data.payment_url) {
         window.location.href = data.payment_url;
      } else {
         notify('success', 'Initiated', data.message || 'Topup initiated!');
         fetchHistory();
      }
    }
  } catch (error) {
    console.error('Topup error:', error)
    notify('error', 'Error', error.response?.data?.message || 'Failed to process topup. Please try again.')
  } finally {
    processing.value = false
  }
}

const getStatusIconClass = (status) => {
  switch (status) {
    case 'success': return 'btn-outline-success'
    case 'failed': return 'btn-outline-danger'
    case 'pending': return 'btn-outline-warning'
    default: return 'btn-outline-secondary'
  }
}

const getStatusIcon = (status) => {
  switch (status) {
    case 'success': return 'fas fa-arrow-up'
    case 'failed': return 'fas fa-times'
    case 'pending': return 'fas fa-clock'
    default: return 'fas fa-question'
  }
}

const getStatusTextClass = (status) => {
  switch (status) {
    case 'success': return 'text-success'
    case 'failed': return 'text-danger'
    case 'pending': return 'text-warning'
    default: return 'text-secondary'
  }
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(amount)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US')
}
</script>
