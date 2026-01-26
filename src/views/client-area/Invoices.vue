<template>
  <div class="py-4 container-fluid">
    <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header pb-0">
          <h6 class="mb-0">Invoices Table</h6>
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
                    TOTAL
                  </th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                    STATUS
                  </th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                    METHOD
                  </th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                    CREATED AT
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
                        'bg-gradient-warning': invoice.payment_status === 'unpaid',
                        'bg-gradient-info': invoice.payment_status === 'partial',
                        'bg-gradient-success': invoice.payment_status === 'paid',
                        'bg-gradient-danger': invoice.payment_status === 'overdue',
                      }"
                    >
                      {{ invoice.payment_status.toUpperCase() }}
                    </span>
                  </td>
                  <td class="align-middle text-center">
                    <span class="text-secondary text-xs font-weight-bold">
                      {{ invoice.payment?.payment_method || '-' }}
                    </span>
                  </td>
                  <td class="align-middle text-center">
                    <span class="text-secondary text-xs font-weight-bold">{{ formatDate(invoice.created_at) }}</span>
                  </td>
                  <td class="align-middle">
                    <button
                      class="btn btn-info btn-sm"
                      @click="viewInvoiceDetail(invoice.id)"
                    >
                      <i class="fas fa-eye me-1"></i>
                      Detail
                    </button>
                  </td>
                </tr>
                <tr v-if="invoices.length === 0">
                  <td colspan="6" class="text-center py-4">
                    <p class="text-muted mb-0">No data available in table</p>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Invoice Detail Modal -->
  <div
    v-if="selectedInvoice"
    class="modal fade show"
    style="display: block"
    tabindex="-1"
  >
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Invoice Detail - {{ selectedInvoice.invoice_number }}</h5>
          <button
            type="button"
            class="btn-close"
            @click="selectedInvoice = null"
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
                <p class="mb-1"><strong>Invoice Number:</strong> {{ invoiceDetail.invoice_number }}</p>
                <p class="mb-1"><strong>Issue Date:</strong> {{ formatDate(invoiceDetail.issue_date) }}</p>
                <p class="mb-1"><strong>Due Date:</strong> {{ formatDate(invoiceDetail.due_date) }}</p>
              </div>
              <div class="col-md-6">
                <p class="mb-1"><strong>Status:</strong> 
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
                <p class="mb-1"><strong>Payment Method:</strong> {{ invoiceDetail.payment?.payment_method || '-' }}</p>
              </div>
            </div>

            <div class="table-responsive mb-3">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
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
                    <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                    <td><strong>{{ formatCurrency(invoiceDetail.subtotal) }}</strong></td>
                  </tr>
                  <tr v-if="invoiceDetail.tax > 0">
                    <td colspan="3" class="text-end"><strong>Tax:</strong></td>
                    <td><strong>{{ formatCurrency(invoiceDetail.tax) }}</strong></td>
                  </tr>
                  <tr v-if="invoiceDetail.discount > 0">
                    <td colspan="3" class="text-end"><strong>Discount:</strong></td>
                    <td><strong>-{{ formatCurrency(invoiceDetail.discount) }}</strong></td>
                  </tr>
                  <tr>
                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                    <td><strong>{{ formatCurrency(invoiceDetail.total) }}</strong></td>
                  </tr>
                </tfoot>
              </table>
            </div>

            <div v-if="invoiceDetail.payment" class="mb-3">
              <h6>Payment Details</h6>
              <p class="mb-1"><strong>Payment Code:</strong> {{ invoiceDetail.payment.payment_code }}</p>
              <p class="mb-1"><strong>Amount:</strong> {{ formatCurrency(invoiceDetail.payment.amount) }}</p>
              <p class="mb-1"><strong>Status:</strong> {{ invoiceDetail.payment.status }}</p>
              <p class="mb-1"><strong>Payment Date:</strong> {{ formatDate(invoiceDetail.payment.payment_date) }}</p>
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
  <div
    v-if="selectedInvoice"
    class="modal-backdrop fade show"
    @click="selectedInvoice = null"
  ></div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const invoices = ref([])
const selectedInvoice = ref(null)
const invoiceDetail = ref(null)
const loading = ref(false)
const loadingDetail = ref(false)

onMounted(async () => {
  await fetchInvoices()
})

const fetchInvoices = async () => {
  loading.value = true
  try {
    const response = await api.get('/isp-admin/client-area/invoices')
    invoices.value = response.data
  } catch (error) {
    console.error('Error fetching invoices:', error)
  } finally {
    loading.value = false
  }
}

const viewInvoiceDetail = async (invoiceId) => {
  selectedInvoice.value = { id: invoiceId }
  loadingDetail.value = true
  try {
    const response = await api.get(`/isp-admin/client-area/invoices/${invoiceId}`)
    invoiceDetail.value = response.data
  } catch (error) {
    console.error('Error fetching invoice detail:', error)
    alert('Failed to load invoice details')
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
  return new Date(date).toLocaleDateString('id-ID')
}
</script>
