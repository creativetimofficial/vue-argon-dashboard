<template>
  <div class="py-4 container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header pb-0">
            <div class="d-flex justify-content-between">
              <h6>{{ $t('dashboard.withdrawals.title') }}</h6>
              <div>
                 <!-- Filter placeholder -->
              </div>
            </div>
          </div>
          <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                      ISP
                    </th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                      Bank Details
                    </th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                      Amount
                    </th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                      Status
                    </th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                      Date
                    </th>
                    <th class="text-secondary opacity-7"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="wd in withdrawals" :key="wd.id">
                    <td>
                      <div class="d-flex px-2 py-1">
                        <div class="d-flex flex-column justify-content-center">
                          <h6 class="mb-0 text-sm">{{ wd.isp?.company_name || 'Unknown' }}</h6>
                          <p class="text-xs text-secondary mb-0">{{ wd.isp?.email }}</p>
                        </div>
                      </div>
                    </td>
                    <td>
                      <p class="text-xs font-weight-bold mb-0">{{ wd.bank_name }} - {{ wd.account_number }}</p>
                      <p class="text-xs text-secondary mb-0">{{ wd.account_name }}</p>
                    </td>
                    <td class="align-middle text-center text-sm">
                        <h6 class="mb-0 text-sm font-weight-bold">{{ formatCurrency(wd.amount) }}</h6>
                    </td>
                    <td class="align-middle text-center text-sm">
                      <span
                        class="badge badge-sm"
                        :class="{
                          'bg-gradient-success': wd.status === 'approved',
                          'bg-gradient-warning': wd.status === 'pending',
                          'bg-gradient-danger': wd.status === 'rejected',
                        }"
                      >
                        {{ wd.status.toUpperCase() }}
                      </span>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">{{ formatDate(wd.created_at) }}</span>
                    </td>
                    <td class="align-middle">
                      <div v-if="wd.status === 'pending'" class="d-flex justify-content-center gap-2">
                        <button
                          class="btn btn-sm btn-success mb-0"
                          @click="approveWithdrawal(wd)"
                        >
                          Approve
                        </button>
                        <button
                          class="btn btn-sm btn-danger mb-0"
                          @click="openRejectModal(wd)"
                        >
                          Reject
                        </button>
                      </div>
                      <div v-else class="text-center">
                          <span class="text-xs text-muted">
                              {{ wd.status === 'rejected' ? $t('dashboard.withdrawals.rejected') : $t('dashboard.withdrawals.completed') }}
                              {{ wd.status === 'rejected' ? (wd.rejected_at ? 'on ' + formatDate(wd.rejected_at) : '') : (wd.approved_at ? 'on ' + formatDate(wd.approved_at) : '') }}
                          </span>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="withdrawals.length === 0">
                      <td colspan="6" class="text-center py-4">
                          <p class="text-muted mb-0">{{ $t('dashboard.withdrawals.no_withdrawals') }}</p>
                      </td>
                  </tr>
                </tbody>
              </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center px-3 mt-3" v-if="pagination.total > 0">
              <div class="text-sm text-muted">
                Showing {{ ((pagination.current_page - 1) * pagination.per_page) + 1 }} 
                to {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }} 
                of {{ pagination.total }} entries
              </div>
              <nav>
                <ul class="pagination pagination-sm mb-0">
                  <!-- Previous Button -->
                  <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                    <a class="page-link" href="#" @click.prevent="pagination.current_page > 1 && fetchWithdrawals(pagination.current_page - 1)">
                      <i class="fas fa-angle-left"></i>
                    </a>
                  </li>

                  <!-- First Page -->
                  <li v-if="pagination.current_page > 2" class="page-item">
                    <a class="page-link" href="#" @click.prevent="fetchWithdrawals(1)">1</a>
                  </li>

                  <!-- Ellipsis before current -->
                  <li v-if="pagination.current_page > 3" class="page-item disabled">
                    <span class="page-link">...</span>
                  </li>

                  <!-- Previous Page -->
                  <li v-if="pagination.current_page > 1" class="page-item">
                    <a class="page-link" href="#" @click.prevent="fetchWithdrawals(pagination.current_page - 1)">
                      {{ pagination.current_page - 1 }}
                    </a>
                  </li>

                  <!-- Current Page -->
                  <li class="page-item active">
                    <a class="page-link" href="#">{{ pagination.current_page }}</a>
                  </li>

                  <!-- Next Page -->
                  <li v-if="pagination.current_page < pagination.last_page" class="page-item">
                    <a class="page-link" href="#" @click.prevent="fetchWithdrawals(pagination.current_page + 1)">
                      {{ pagination.current_page + 1 }}
                    </a>
                  </li>

                  <!-- Ellipsis after current -->
                  <li v-if="pagination.current_page < pagination.last_page - 2" class="page-item disabled">
                    <span class="page-link">...</span>
                  </li>

                  <!-- Last Page -->
                  <li v-if="pagination.current_page < pagination.last_page - 1" class="page-item">
                    <a class="page-link" href="#" @click.prevent="fetchWithdrawals(pagination.last_page)">
                      {{ pagination.last_page }}
                    </a>
                  </li>

                  <!-- Next Button -->
                  <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                    <a class="page-link" href="#" @click.prevent="pagination.current_page < pagination.last_page && fetchWithdrawals(pagination.current_page + 1)">
                      <i class="fas fa-angle-right"></i>
                    </a>
                  </li>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Reject Modal -->
    <div v-if="showRejectModal" class="modal fade show" style="display: block; background: rgba(0,0,0,0.5)">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $t('dashboard.withdrawals.reject_withdrawal') }}</h5>
                    <button type="button" class="btn-close" @click="showRejectModal = false"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ $t('dashboard.withdrawals.rejection_reason') }}</label>
                        <textarea id="reject_reason" name="reject_reason" v-model="rejectReason" class="form-control" rows="3" placeholder="Incorrect bank details..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="showRejectModal = false">{{ $t('common.cancel') }}</button>
                    <button type="button" class="btn btn-danger" @click="confirmReject">{{ $t('dashboard.withdrawals.confirm_reject') }}</button>
                </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'
import notify, { confirm } from '@/utils/notify'

const withdrawals = ref([])
const pagination = ref({})
const showRejectModal = ref(false)
const selectedWithdrawal = ref(null)
const rejectReason = ref('')

onMounted(() => {
  fetchWithdrawals()
})

const fetchWithdrawals = async (page = 1) => {
  try {
    const response = await api.get(`/super-admin/withdrawals?page=${page}`)
    withdrawals.value = response.data.data
    // extract pagination info
    pagination.value = {
        current_page: response.data.current_page,
        last_page: response.data.last_page,
        prev_page_url: response.data.prev_page_url,
        next_page_url: response.data.next_page_url,
        total: response.data.total,
        per_page: response.data.per_page || 10
    }
  } catch (error) {
    console.error('Error fetching withdrawals:', error)
  }
}

const approveWithdrawal = async (wd) => {
    if(!await confirm('Konfirmasi', `Approve withdrawal of ${formatCurrency(wd.amount)} for ${wd.isp?.name}? ensure you have transferred the funds manually.`, 'question')) return;
    
    try {
        await api.post(`/super-admin/withdrawals/${wd.id}/approve`);
        notify('success', 'Success', 'Withdrawal approved!');
        fetchWithdrawals(pagination.value.current_page);
    } catch (error) {
        console.error("Approve error", error);
        notify('error', 'Error', error.response?.data?.message || 'Failed to approve');
    }
}

const openRejectModal = (wd) => {
    selectedWithdrawal.value = wd;
    rejectReason.value = '';
    showRejectModal.value = true;
}

const confirmReject = async () => {
    if (!rejectReason.value) {
        notify('warning', 'Warning', "Please provide a reason");
        return;
    }
    
    try {
        await api.post(`/super-admin/withdrawals/${selectedWithdrawal.value.id}/reject`, {
            reason: rejectReason.value
        });
        notify('success', 'Success', 'Withdrawal rejected and refunded!');
        showRejectModal.value = false;
        fetchWithdrawals(pagination.value.current_page);
    } catch (error) {
        console.error("Reject error", error);
         notify('error', 'Error', error.response?.data?.message || 'Failed to reject');
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
  return new Date(date).toLocaleString('en-US')
}
</script>
