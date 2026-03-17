<template>
  <v-container fluid class="pa-6">
    <div class="d-flex align-center mb-6">
      <h4 class="text-h4 font-weight-bold mb-0">
        <span class="text-medium-emphasis font-weight-light">ISP Admin /</span> Billing & Invoices
      </h4>
    </div>

    <!-- Filter & Search -->
    <v-card  class="mb-6">
      <v-card-text>
        <v-row>
          <v-col cols="12" md="4">
            <v-text-field
              v-model="search"
              prepend-inner-icon="bx bx-search"
              placeholder="Search invoice no. or name..."
              hide-details
              density="compact"
              
              @input="fetchInvoices"
            ></v-text-field>
          </v-col>
          <v-col cols="12" md="3">
            <v-select
              v-model="status"
              :items="[{title:'All Status', value:''}, {title:'Paid', value:'paid'}, {title:'Unpaid', value:'unpaid'}, {title:'Overdue', value:'overdue'}]"
              hide-details
              density="compact"
              
              @update:modelValue="fetchInvoices"
            ></v-select>
          </v-col>
          <v-col cols="12" md="5" class="d-flex justify-end">
             <v-btn color="primary" @click="runBillingJob" :loading="runningJob" :disabled="runningJob">
                <v-icon start>bx bx-repost</v-icon> 
                {{ runningJob ? 'Memproses...' : 'Generate Tagihan' }}
             </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <!-- Invoices Table -->
    <v-card >
      <v-card-title class="pa-4 d-flex align-center">Invoice List</v-card-title>
      <v-divider></v-divider>
      <v-table hover>
        <thead>
          <tr>
            <th class="text-left font-weight-bold">No. Invoice</th>
            <th class="text-left font-weight-bold">Pelanggan</th>
            <th class="text-left font-weight-bold">Total</th>
            <th class="text-left font-weight-bold">Status</th>
            <th class="text-left font-weight-bold">Jatuh Tempo</th>
            <th class="text-left font-weight-bold" width="100">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
             <td colspan="6" class="text-center py-10">
                <v-progress-circular indeterminate color="primary"></v-progress-circular>
             </td>
          </tr>
          <tr v-else-if="invoices.length === 0">
             <td colspan="6" class="text-center py-10 text-medium-emphasis">No invoice data found</td>
          </tr>
          <tr v-for="invoice in invoices" :key="invoice.id">
            <td>
              <div class="font-weight-bold text-primary">#{{ invoice.invoice_number }}</div>
              <div class="text-caption text-medium-emphasis">{{ formatDate(invoice.issue_date) }}</div>
            </td>
            <td>
              <div class="d-flex flex-column">
                <div class="font-weight-medium">{{ invoice.billable?.name }}</div>
                <div class="text-caption text-medium-emphasis">{{ invoice.billable?.customer_code }}</div>
              </div>
            </td>
            <td>
              <div class="font-weight-bold">Rp {{ formatNumber(invoice.total) }}</div>
              <div class="text-caption text-medium-emphasis" v-if="invoice.tax > 0">Inc. 11% VAT</div>
            </td>
            <td>
              <v-chip size="small" :color="getStatusClass(invoice.payment_status).replace('bg-label-', '') + ' bg-opacity-10 text-' + getStatusClass(invoice.payment_status).replace('bg-label-', '')" class="font-weight-medium">
                {{ getStatusLabel(invoice.payment_status) }}
              </v-chip>
            </td>
            <td>
              <span :class="isOverdue(invoice) ? 'text-error font-weight-bold' : ''">
                {{ formatDate(invoice.due_date) }}
              </span>
            </td>
            <td>
              <v-btn icon variant="text" size="small" color="secondary">
                <v-icon>bx bx-dots-vertical-rounded</v-icon>
                <v-menu activator="parent" offset-y>
                  <v-list density="compact" class="py-0">
                    <v-list-item @click="downloadPDF(invoice)">
                      <template v-slot:prepend><v-icon class="mr-2">bx bx-download</v-icon></template>
                      <v-list-item-title>Download PDF</v-list-item-title>
                    </v-list-item>
                    <v-list-item v-if="invoice.payment_status !== 'paid'" @click="markAsPaid(invoice)">
                      <template v-slot:prepend><v-icon color="success" class="mr-2">bx bx-check-circle</v-icon></template>
                      <v-list-item-title>Tandai Lunas</v-list-item-title>
                    </v-list-item>
                    <v-list-item @click="viewDetail(invoice)">
                      <template v-slot:prepend><v-icon color="primary" class="mr-2">bx bx-show</v-icon></template>
                      <v-list-item-title>Detail</v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </v-btn>
            </td>
          </tr>
        </tbody>
      </v-table>
      <v-divider></v-divider>
      <!-- Pagination -->
      <v-card-actions class="pa-4 justify-space-between" v-if="pagination.total > 0">
        <div class="text-caption text-medium-emphasis">Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }}</div>
        <v-pagination
          v-model="pagination.current"
          :length="pagination.last_page"
          density="compact"
          @update:modelValue="goToPage"
          color="primary"
        ></v-pagination>
      </v-card-actions>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { ispAdminAPI } from '@/services/api';
import Swal from 'sweetalert2';

const invoices = ref([]);
const loading = ref(false);
const runningJob = ref(false);
const search = ref('');
const status = ref('');

const pagination = ref({
   current: 1,
   last_page: 1,
   total: 0,
   from: 0,
   to: 0,
   prev: null,
   next: null
});

const fetchInvoices = async (page = 1) => {
  loading.value = true;
  try {
     const response = await ispAdminAPI.getInvoices({
        page,
        search: search.value,
        status: status.value
     });
     invoices.value = response.data.data;
     pagination.value = {
        current: response.data.current_page,
        last_page: response.data.last_page,
        total: response.data.total,
        from: response.data.from || 0,
        to: response.data.to || 0,
        prev: response.data.prev_page_url,
        next: response.data.next_page_url
     };
  } catch (err) {
     console.error("Error fetching invoices", err);
  } finally {
     loading.value = false;
  }
};

const goToPage = (page) => {
   if (page >= 1 && page <= pagination.value.last_page) {
      fetchInvoices(page);
   }
};

const runBillingJob = async () => {
   const { isConfirmed } = await Swal.fire({
      title: 'Generate Invoices?',
      text: 'The system will process automatic invoice generation for all customers in the billing cycle.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Yes, Process!',
      cancelButtonText: 'Cancel'
   });

   if (!isConfirmed) return;

   runningJob.value = true;
   try {
      await ispAdminAPI.runBillingJob();
      Swal.fire({
         title: 'Success!',
         text: 'Invoice generation process completed.',
         icon: 'success',
         timer: 2000,
         showConfirmButton: false
      });
      fetchInvoices();
   } catch (err) {
      Swal.fire({
         title: 'Information',
         text: 'Background automation process is running.',
         icon: 'info',
         confirmButtonText: 'OK'
      });
      fetchInvoices();
   } finally {
      runningJob.value = false;
   }
};

const downloadPDF = async (invoice) => {
  try {
    const response = await ispAdminAPI.downloadInvoice(invoice.id);
    
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `Invoice-${invoice.invoice_number}.pdf`);
    document.body.appendChild(link);
    link.click();
  } catch (err) {
    Swal.fire({
       title: 'Failed!',
       text: 'Failed to download PDF.',
       icon: 'error',
       confirmButtonText: 'OK'
    });
  }
};

const markAsPaid = async (invoice) => {
  const { isConfirmed } = await Swal.fire({
    title: 'Mark as Paid?',
    text: `Confirm manual payment for #${invoice.invoice_number}`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, Mark as Paid!',
  });

  if (isConfirmed) {
    try {
      await ispAdminAPI.markInvoicePaid(invoice.id);
      Swal.fire({
         title: 'Success!',
         text: 'Invoice has been marked as paid.',
         icon: 'success',
         timer: 2000,
         showConfirmButton: false
      });
      fetchInvoices(pagination.value.current);
    } catch (err) {
      Swal.fire({
         title: 'Failed!',
         text: 'Failed to update status.',
         icon: 'error',
         confirmButtonText: 'OK'
      });
    }
  }
};

const getStatusLabel = (status) => {
  const labels = { paid: 'Paid', unpaid: 'Unpaid', overdue: 'Late', partial: 'Partial' };
  return labels[status] || status;
};

const getStatusClass = (status) => {
  const classes = { paid: 'bg-label-success', unpaid: 'bg-label-warning', overdue: 'bg-label-danger' };
  return classes[status] || 'bg-label-secondary';
};

const formatNumber = (num) => {
  if (!num) return '0';
  return parseFloat(num).toLocaleString('en-US');
};

const formatDate = (dateStr) => {
  if (!dateStr) return '-';
  const date = new Date(dateStr);
  return date.toLocaleDateString('en-US', { day: '2-digit', month: 'short', year: 'numeric' });
};

const isOverdue = (invoice) => {
  if (invoice.payment_status === 'paid') return false;
  return new Date(invoice.due_date) < new Date();
};

onMounted(() => {
  fetchInvoices();
});
</script>
