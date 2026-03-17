<template>
  <div class="py-4 container-fluid">
    <LoadingOverlay :active="isLoading" />
    <div class="row">
      <div class="col-12">
        <div class="card mb-4">
          <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center">
              <h6>{{ $t('dashboard.invoices.title') }}</h6>
              <button class="btn btn-link text-primary p-0 mb-0" @click="manualRefresh" :disabled="isLoading">
                <i class="fas fa-sync-alt" :class="{ 'fa-spin': isLoading }"></i>
                <span class="ms-1 d-none d-sm-inline">{{ $t('dashboard.invoices.refresh') }}</span>
              </button>
            </div>
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
                      {{ $t('dashboard.invoices.total').toUpperCase() }}
                    </th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                      {{ $t('common.status').toUpperCase() }}
                    </th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                      {{ $t('dashboard.invoices.method').toUpperCase() }}
                    </th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                      {{ $t('dashboard.orders.date').toUpperCase() }}
                    </th>
                    <th class="text-secondary opacity-7"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="invoice in invoices" :key="invoice.id">
                    <td>
                      <div class="d-flex px-2 py-1">
                        <div class="d-flex flex-column justify-content-center">
                          <h6 class="mb-0 text-sm">{{ invoice.invoice_number || invoice.id }}</h6>
                          <p class="text-xs text-secondary mb-0">Unit: {{ invoice.billable?.company_name || '-' }}</p>
                        </div>
                      </div>
                    </td>
                    <td>
                      <p class="text-xs font-weight-bold mb-0">{{ formatCurrency(invoice.total) }}</p>
                    </td>
                    <td class="align-middle text-center text-sm">
                      <span
                        class="badge badge-sm"
                        :class="{
                          'bg-gradient-secondary': invoice.status === 'cancelled',
                          'bg-gradient-warning': invoice.status !== 'cancelled' && invoice.payment_status === 'unpaid',
                          'bg-gradient-info': invoice.payment_status === 'partial',
                          'bg-gradient-success': invoice.payment_status === 'paid',
                          'bg-gradient-danger': invoice.payment_status === 'overdue',
                        }"
                      >
                        {{ invoice.status === 'cancelled' ? 'CANCELLED' : (invoice.payment_status === 'paid' ? 'PAID' : (invoice.payment_status === 'unpaid' ? 'UNPAID' : (invoice.payment_status === 'overdue' ? 'OVERDUE' : invoice.payment_status.toUpperCase()))) }}
                      </span>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">
                        {{ 
                          (invoice.payment?.payment_type 
                            || invoice.payment?.payment_method 
                            || invoice.payment_method 
                            || '-')
                        }}
                      </span>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">{{ formatDate(invoice.created_at) }}</span>
                    </td>
                    <td class="align-middle">
                      <div class="d-flex gap-2 justify-content-center">
                        <button
                          v-if="invoice.payment_status === 'unpaid'"
                          class="btn btn-primary btn-sm mb-0"
                          @click="openPaymentModal(invoice)"
                          :disabled="processingPayment === invoice.id"
                        >
                          <span v-if="processingPayment === invoice.id">
                            <i class="fas fa-spinner fa-spin"></i>
                          </span>
                          <span v-else>
                            <i class="fas fa-credit-card me-1"></i>
                            {{ $t('dashboard.invoices.pay_now') }}
                          </span>
                        </button>
                        
                        <button
                          v-if="invoice.payment_status === 'unpaid'"
                          class="btn btn-warning btn-sm mb-0"
                          @click="cancelInvoice(invoice.id)"
                        >
                          <i class="fas fa-times me-1"></i>
                          {{ $t('common.cancel') }}
                        </button>
                        
                        <button
                          v-if="invoice.payment_status === 'paid' || invoice.payment_status === 'cancelled'"
                          class="btn btn-info btn-sm mb-0"
                          @click="viewInvoice(invoice.id)"
                        >
                          <i class="fas fa-eye me-1"></i>
                          {{ $t('dashboard.invoices.view') }}
                        </button>
                        
                        <button
                          v-if="invoice.payment_status === 'paid'"
                          class="btn btn-success btn-sm mb-0"
                          @click="downloadInvoice(invoice.id)"
                        >
                          <i class="fas fa-download me-1"></i>
                          {{ $t('dashboard.invoices.download_pdf') }}
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="invoices.length === 0">
                    <td colspan="6" class="text-center py-4">
                      <p class="text-muted mb-0">{{ $t('dashboard.invoices.no_invoices') }}</p>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

  <!-- Payment Method Modal -->
  <div
    v-if="showPaymentModal"
    class="modal fade show"
    style="display: block; background: rgba(0,0,0,0.5);"
    tabindex="-1"
  >
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header d-flex justify-content-between align-items-center">
          <h5 class="modal-title m-0">{{ $t('dashboard.invoices.select_payment') }}</h5>
          <button type="button" class="btn-close text-dark" @click="closePaymentModal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div v-if="selectedMethodInvoice" class="text-center mb-4">
            <p class="text-muted mb-1">{{ $t('dashboard.invoices.total_payment') }}</p>
            <h3 class="font-weight-bolder text-primary">{{ formatCurrency(selectedMethodInvoice.total) }}</h3>
          </div>

          <div class="row g-3">
            <!-- Balance Option -->
            <div class="col-12">
              <div 
                class="card border mb-3" 
                :class="canPayWithBalance ? 'border-success shadow-sm' : 'border-light opacity-75'"
                style="cursor: pointer; transition: all 0.2s;"
                @click="canPayWithBalance ? payWithBalance(selectedMethodInvoice) : null"
              >
                <div class="card-body p-3">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex align-items-center">
                      <div class="icon icon-shape icon-sm bg-gradient-success shadow text-center border-radius-md me-3 d-flex align-items-center justify-content-center">
                        <i class="fas fa-wallet text-white opacity-10" style="margin-top: -3px;"></i>
                      </div>
                      <div>
                        <h6 class="mb-0 text-dark">{{ $t('dashboard.invoices.pay_balance') }}</h6>
                        <span class="text-xs text-success font-weight-bold">
                          <i class="fas fa-check-circle me-1"></i> {{ $t('dashboard.invoices.no_fee') }}
                        </span>
                      </div>
                    </div>
                    <div class="text-end">
                      <span class="badge bg-success" v-if="canPayWithBalance">{{ $t('dashboard.invoices.recommended') }}</span>
                    </div>
                  </div>
                  <hr class="horizontal dark my-2">
                  <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">{{ $t('dashboard.invoices.your_balance') }}: <strong>{{ formatCurrency(balance) }}</strong></small>
                    <button 
                      class="btn btn-sm btn-success mb-0" 
                      :disabled="!canPayWithBalance"
                      @click.stop="canPayWithBalance ? payWithBalance(selectedMethodInvoice) : null"
                    >
                      {{ $t(canPayWithBalance ? 'dashboard.invoices.pay_now' : 'dashboard.invoices.insufficient') }}
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Gateway Option -->
            <div class="col-12">
              <div 
                class="card border border-light"
                style="cursor: pointer; transition: all 0.2s;"
                @click="confirmPayWithGateway()"
              >
                <div class="card-body p-3">
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                      <div class="icon icon-shape icon-sm bg-gradient-info shadow text-center border-radius-md me-3 d-flex align-items-center justify-content-center">
                        <i class="fas fa-globe text-white opacity-10" style="margin-top: -3px;"></i>
                      </div>
                      <div>
                        <h6 class="mb-0 text-dark">{{ $t('dashboard.invoices.payment_gateway') }}</h6>
                        <span class="text-xs text-muted">Bank Transfer, E-Wallet, QRIS</span>
                      </div>
                    </div>
                    <i class="fas fa-chevron-right text-muted"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Invoice Detail Modal -->
  <div
    v-if="selectedInvoice"
    class="modal fade show"
    style="display: block; background: rgba(0,0,0,0.5);"
    tabindex="-1"
  >
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header d-flex justify-content-between align-items-center">
          <h5 class="modal-title m-0">{{ $t('dashboard.invoices.invoice_details') }} - {{ selectedInvoice.invoice_number }}</h5>
          <button
            type="button"
            class="btn-close text-dark"
            @click="selectedInvoice = null"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
          <div v-if="loadingDetail" class="text-center py-3">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
          <div v-else-if="invoiceDetail">
            <div class="row mb-3">
              <div class="col-md-6">
                <p class="mb-1"><strong>{{ $t('dashboard.invoices.invoice_number') }}:</strong> {{ invoiceDetail.invoice_number }}</p>
                <p class="mb-1"><strong>{{ $t('dashboard.invoices.issue_date') }}:</strong> {{ formatDate(invoiceDetail.issue_date) }}</p>
                <p class="mb-1"><strong>{{ $t('dashboard.invoices.due_date') }}:</strong> {{ formatDate(invoiceDetail.due_date) }}</p>
              </div>
              <div class="col-md-6">
                <p class="mb-1"><strong>{{ $t('common.status') }}:</strong> 
                  <span
                    class="badge"
                    :class="{
                      'bg-warning': invoiceDetail.payment_status === 'unpaid',
                      'bg-info': invoiceDetail.payment_status === 'partial',
                      'bg-success': invoiceDetail.payment_status === 'paid',
                      'bg-danger': invoiceDetail.payment_status === 'overdue',
                    }"
                  >
                    {{ invoiceDetail.payment_status.toUpperCase() }}
                  </span>
                </p>
                <p class="mb-1"><strong>{{ $t('dashboard.invoices.payment_method') }}:</strong> {{ invoiceDetail.payment?.payment_method || '-' }}</p>
              </div>
            </div>

            <div class="table-responsive mb-3">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>{{ $t('dashboard.invoices.description') }}</th>
                    <th>{{ $t('dashboard.invoices.quantity') }}</th>
                    <th>{{ $t('dashboard.invoices.unit_price') }}</th>
                    <th>{{ $t('dashboard.invoices.total') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in invoiceDetail.items" :key="item.id">
                    <td>{{ item.description }}</td>
                    <td>{{ item.quantity }}</td>
                    <td>{{ formatCurrency(item.unit_price) }}</td>
                    <td>{{ formatCurrency(item.total) }}</td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="3" class="text-end"><strong>{{ $t('dashboard.invoices.subtotal') }}:</strong></td>
                    <td><strong>{{ formatCurrency(invoiceDetail.subtotal) }}</strong></td>
                  </tr>
                  <tr v-if="invoiceDetail.tax > 0">
                    <td colspan="3" class="text-end"><strong>{{ $t('dashboard.invoices.tax') }}:</strong></td>
                    <td><strong>{{ formatCurrency(invoiceDetail.tax) }}</strong></td>
                  </tr>
                  <tr v-if="invoiceDetail.discount > 0">
                    <td colspan="3" class="text-end"><strong>{{ $t('dashboard.invoices.discount') }}:</strong></td>
                    <td><strong>-{{ formatCurrency(invoiceDetail.discount) }}</strong></td>
                  </tr>
                  <tr>
                    <td colspan="3" class="text-end"><strong>{{ $t('dashboard.invoices.total') }}:</strong></td>
                    <td><strong>{{ formatCurrency(invoiceDetail.total) }}</strong></td>
                  </tr>
                </tfoot>
              </table>
            </div>

            <div v-if="invoiceDetail.payment" class="mb-3">
              <h6>{{ $t('dashboard.invoices.payment_details') }}</h6>
              <p class="mb-1"><strong>{{ $t('dashboard.invoices.payment_code') }}:</strong> {{ invoiceDetail.payment.payment_code }}</p>
              <p class="mb-1"><strong>Quantity:</strong> {{ formatCurrency(invoiceDetail.payment.amount) }}</p>
              <p class="mb-1"><strong>Status:</strong> {{ invoiceDetail.payment.status }}</p>
              <p class="mb-1"><strong>{{ $t('dashboard.invoices.payment_date') }}:</strong> {{ formatDate(invoiceDetail.payment.payment_date) }}</p>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-secondary"
            @click="selectedInvoice = null"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/api'
import LoadingOverlay from '@/components/LoadingOverlay.vue'
import notify, { confirm } from '@/utils/notify'

const route = useRoute()

const invoices = ref([])
const selectedInvoice = ref(null)
const invoiceDetail = ref(null)
const isLoading = ref(false)
const loadingDetail = ref(false)
const processingPayment = ref(null)

const balance = ref(0)
const showPaymentModal = ref(false)
const selectedMethodInvoice = ref(null)
const payingId = ref(null)

const canPayWithBalance = computed(() => {
  if (!selectedMethodInvoice.value) return false
  
  // Convert strings to numbers for proper comparison
  const balanceNum = parseFloat(balance.value) || 0
  const totalNum = parseFloat(selectedMethodInvoice.value.total) || 0
  
  const result = balanceNum >= totalNum
  
  console.log('canPayWithBalance check:', {
    hasInvoice: !!selectedMethodInvoice.value,
    balanceStr: balance.value,
    balanceNum: balanceNum,
    totalStr: selectedMethodInvoice.value?.total,
    totalNum: totalNum,
    canPay: result
  })
  
  return result
})


onMounted(async () => {
  await fetchInvoices()
  await fetchBalance()
  
  if (route.query.pay_invoice) {
    const invoiceToPay = invoices.value.find(inv => inv.id == route.query.pay_invoice && inv.payment_status === 'unpaid')
    if (invoiceToPay) {
      openPaymentModal(invoiceToPay)
    }
  }

  // Handle Payment Return (Xendit/Generic)
  if (route.query.status) {
      if (route.query.status === 'success') {
          if (route.query.external_id) {
               api.post('/isp-admin/client-area/invoices/verify-xendit', { external_id: route.query.external_id })
                   .then(response => {
                       if (response.data.success) {
                           notify('success', 'Payment Confirmed', 'Invoice updated successfully.');
                           fetchInvoices();
                           fetchBalance();
                       } else {
                           notify('error', 'Verification Failed', response.data.message);
                           fetchInvoices();
                       }
                   })
                   .catch(e => {
                       console.error('Verification error:', e);
                       // notify('error', 'Error', 'Verification error.');
                       fetchInvoices();
                   });
          } else {
               setTimeout(fetchInvoices, 3000);
          }

      } else if (route.query.status === 'failed') {
          notify('error', 'Payment Failed', 'Payment was failed or cancelled.');
      }
      
      const url = new URL(window.location.href);
      url.searchParams.delete('status');
      url.searchParams.delete('external_id');
      window.history.replaceState({}, document.title, url.toString());
  }
})

const fetchBalance = async () => {
  try {
    const response = await api.get('/isp-admin/client-area/balance')
    balance.value = response.data.balance || 0
  } catch (error) {
    console.error('Error fetching balance:', error)
  }
}

const fetchInvoices = async () => {
  isLoading.value = true
  try {
    const response = await api.get('/isp-admin/client-area/invoices')
    invoices.value = response.data
  } catch (error) {
    console.error('Error fetching invoices:', error)
    notify('error', 'Error', 'Failed to fetch invoices')
  } finally {
    isLoading.value = false
  }
}

const openPaymentModal = (invoice) => {
  selectedMethodInvoice.value = invoice
  showPaymentModal.value = true
}

const closePaymentModal = () => {
  showPaymentModal.value = false
  selectedMethodInvoice.value = null
}

const confirmPayWithBalance = () => {
  payWithBalance(selectedMethodInvoice.value)
  closePaymentModal()
}

const confirmPayWithGateway = () => {
  payInvoice(selectedMethodInvoice.value)
  closePaymentModal()
}

const viewInvoiceDetail = async (invoiceId) => {
  selectedInvoice.value = { id: invoiceId }
  loadingDetail.value = true
  try {
    const response = await api.get(`/isp-admin/client-area/invoices/${invoiceId}`)
    invoiceDetail.value = response.data
  } catch (error) {
    console.error('Error fetching invoice detail:', error)
    notify('error', 'Error', 'Failed to load invoice details')
  } finally {
    loadingDetail.value = false
  }
}

const payWithBalance = async (invoice) => {
  const isConfirmed = await confirm(
      'Pay with Balance?',
      `Pay invoice ${invoice.invoice_number} using your balance?`
  )
  if (!isConfirmed) return

  payingId.value = invoice.id
  try {
     const response = await api.post(`/isp-admin/client-area/invoices/${invoice.id}/pay`, {
       payment_method: 'balance'
     })
     
     if (response.data.success) {
       notify('success', 'Payment Successful', 'Invoice has been paid using balance!')
       showPaymentModal.value = false // Close modal
       await fetchInvoices()
       await fetchBalance()
       if (selectedInvoice.value) selectedInvoice.value = null
     }
  } catch (error) {
    console.error('Balance payment error:', error)
    notify('error', 'Payment Failed', error.response?.data?.message || 'Payment failed')
  } finally {
    payingId.value = null
  }
}

const payInvoice = async (invoice) => {
  console.log('Starting Pay Invoice...', invoice.id);
  payingId.value = invoice.id
  try {
    console.log('Sending POST request to:', `/isp-admin/client-area/invoices/${invoice.id}/pay`);
    const response = await api.post(`/isp-admin/client-area/invoices/${invoice.id}/pay`)
    console.log('Response received:', response);
    const data = response.data
    
    // Check for Midtrans Snap
    if (data.gateway === 'Midtrans' && data.payment_token) {
        
        // Ensure Snap is loaded with correct key
        if (data.client_key) {
             await loadSnap(data.client_key);
        } else if (!window.snap) {
             // Fallback to Env if no key returned (Legacy)
             await loadSnap(import.meta.env.VITE_MIDTRANS_CLIENT_KEY || 'SB-Mid-client-TestKey');
        }

        // Use Snap Popup
        window.snap.pay(data.payment_token, {
          onSuccess: async function(result) {
            // Updated: Call backend to confirm payment immediately (since localhost webhook fails)
            try {
                await api.post('/payment-callback', {
                    order_id: result.order_id,
                    transaction_id: result.transaction_id,
                    status: result.transaction_status || 'settlement',
                    amount: result.gross_amount, 
                    payment_type: result.payment_type
                });
                notify('success', 'Payment Successful', 'Invoice is being updated...');
                
                // Polling for status update
                let attempts = 0;
                const pollInterval = setInterval(async () => {
                    attempts++;
                    await fetchInvoices();
                    await fetchBalance();
                    
                    // Check if current payment is now marked as success in our local state
                    const updatedInvoice = invoices.value.find(inv => inv.id === invoice.id);
                    if (updatedInvoice && updatedInvoice.payment_status === 'paid') {
                        clearInterval(pollInterval);
                        notify('success', 'Status Updated', 'Payment has been verified.');
                    } else if (attempts > 5) {
                        clearInterval(pollInterval);
                    }
                }, 2000);

            } catch (e) {
                console.error('Failed to update status:', e);
                const errorMsg = e.response?.data?.message || e.message || 'Unknown error';
                notify('warning', 'Pembayaran Berhasil', 'Failed to update status: ' + errorMsg);
                await fetchInvoices();
                await fetchBalance();
            }
          },
          onPending: function(result) {
            notify('info', 'Payment Pending', 'Waiting for payment!');
            fetchInvoices();
          },
          onError: function(result) {
            notify('error', 'Payment Failed', 'Payment failed!');
            fetchInvoices();
          },
          onClose: function() {
            // Check status if closed without finishing?
          }
        });
    } else if (data.payment_url) {
      // Redirect to Payment Gateway (Xendit, Tripay, Duitku, etc.)
      console.log('Redirecting to payment URL:', data.payment_url);
      window.location.href = data.payment_url
    } else {
      notify('error', 'Error', data.message || 'Payment URL/Token could not be generated')
    }
  } catch (error) {
    console.error('Error initiating payment:', error)
    notify('error', 'Error', error.response?.data?.message || 'Failed to initiate payment')
  } finally {
    payingId.value = null
  }
}

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

const cancelInvoice = async (invoiceId) => {
  const isConfirmed = await confirm(
      'Cancel Invoice?',
      'Are you sure you want to cancel this invoice?'
  )
  if (!isConfirmed) return
  
  isLoading.value = true
  try {
    await api.post(`/isp-admin/client-area/invoices/${invoiceId}/cancel`)
    await fetchInvoices()
    if (selectedInvoice.value && selectedInvoice.value.id === invoiceId) {
       selectedInvoice.value = null
    }
    notify('success', 'Cancelled', 'Invoice cancelled successfully')
  } catch (error) {
    console.error('Error cancelling invoice:', error)
    notify('error', 'Error', error.response?.data?.message || 'Failed to cancel invoice')
  } finally {
    isLoading.value = false
  }
}

const viewInvoice = async (invoiceId) => {
  selectedInvoice.value = { id: invoiceId, invoice_number: `#${invoiceId}` }
  loadingDetail.value = true
  try {
    const response = await api.get(`/isp-admin/client-area/invoices/${invoiceId}`)
    invoiceDetail.value = response.data
    selectedInvoice.value.invoice_number = response.data.invoice_number || `#${invoiceId}`
  } catch (error) {
    console.error('Error fetching invoice detail:', error)
    notify('error', 'Error', 'Failed to load invoice details')
    selectedInvoice.value = null
  } finally {
    loadingDetail.value = false
  }
}

const downloadInvoice = async (invoiceId) => {
  try {
    const response = await api.get(`/isp-admin/client-area/invoices/${invoiceId}/download`, {
      responseType: 'blob'
    })
    
    // Create download link
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `invoice-${invoiceId}.pdf`)
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
  } catch (error) {
    if (error.message === 'Network Error' && !error.response) {
      console.log('Download intercepted by download manager (this is normal)')
      return
    }
    
    console.error('Error downloading invoice:', error)
    notify('error', 'Download Failed', 'Failed to download invoice. Please try again.')
  }
}

const manualRefresh = async () => {
  await fetchInvoices()
  await fetchBalance()
  notify('success', 'Sync Complete', 'Invoice data has been updated.')
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(amount)
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('en-US')
}
</script>
