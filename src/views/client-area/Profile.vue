<template>
  <div class="py-4 container-fluid">
    <LoadingOverlay :active="isLoading" />
    <div class="row">
      <div class="col-lg-4 mx-auto">
        <div class="card shadow-sm border-radius-xl mb-4">
          <div class="card-body p-1 bg-gray-100 border-radius-lg">
            <ul class="nav nav-pills nav-fill p-1" role="tablist">
              <li class="nav-item">
                <a
                  class="nav-link mb-0 px-0 py-2 d-flex align-items-center justify-content-center cursor-pointer"
                  :class="{ 'active shadow': activeTab === 'profile', 'text-secondary': activeTab !== 'profile' }"
                  @click="activeTab = 'profile'"
                >
                  <i class="ni ni-circle-08 me-2 text-lg"></i>
                  <span class="font-weight-bold">{{ $t('dashboard.client_profile.title') }}</span>
                </a>
              </li>
              <li class="nav-item">
                <a
                  class="nav-link mb-0 px-0 py-2 d-flex align-items-center justify-content-center cursor-pointer"
                  :class="{ 'active shadow': activeTab === 'finance', 'text-secondary': activeTab !== 'finance' }"
                  @click="activeTab = 'finance'"
                >
                  <i class="ni ni-money-coins me-2 text-lg"></i>
                  <span class="font-weight-bold">{{ $t('dashboard.client_profile.wallet_finance') }}</span>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Profile Tab -->
     <!-- Profile Tab -->
    <div v-show="activeTab === 'profile'" class="row">
      <div class="col-lg-12">
        <!-- Referral Code Card -->
        <div class="card mb-4 border-0 shadow-sm">
          <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h6 class="mb-0 font-weight-bolder">{{ $t('dashboard.client_profile.your_referral') }}</h6>
            </div>
            
            <div class="bg-gray-100 border-radius-lg p-3 d-flex align-items-center justify-content-between mb-3">
              <span class="font-weight-bold text-lg ms-2">{{ referralCode }}</span>
              <button
                class="btn btn-outline-secondary btn-sm mb-0"
                type="button"
                @click="copyReferralCode"
              >
                <i class="fas fa-copy me-1"></i>
                {{ $t('dashboard.client_profile.copy') }}
              </button>
            </div>

            <p class="text-sm text-secondary mb-0">
              <i class="fas fa-info-circle me-1"></i>
              {{ $t('dashboard.client_profile.referral_info') }}
            </p>
          </div>
        </div>

        <!-- Edit Profile Card -->
        <div class="card border-0 shadow-sm">
          <div class="card-body p-4">
            <h6 class="mb-4 font-weight-bolder">{{ $t('dashboard.client_profile.edit_profile') }}</h6>
            
            <h6 class="text-uppercase text-xs font-weight-bolder text-secondary mb-3">{{ $t('dashboard.client_profile.user_info').toUpperCase() }}</h6>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label text-xs font-weight-bold text-secondary text-uppercase">{{ $t('dashboard.client_profile.full_name').toUpperCase() }}</label>
                <input
                  v-model="profileForm.name"
                  type="text"
                  class="form-control"
                  placeholder="Full Name"
                />
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label text-xs font-weight-bold text-secondary text-uppercase">{{ $t('dashboard.client_profile.email').toUpperCase() }}</label>
                <input
                  v-model="profileForm.email"
                  type="email"
                  class="form-control bg-gray-100"
                  placeholder="Email"
                  readonly
                />
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label text-xs font-weight-bold text-secondary text-uppercase">{{ $t('dashboard.client_profile.new_password').toUpperCase() }}</label>
                <input
                  v-model="profileForm.password"
                  type="password"
                  class="form-control"
                  :placeholder="$t('dashboard.client_profile.leave_blank_password')"
                />
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label text-xs font-weight-bold text-secondary text-uppercase">{{ $t('dashboard.client_profile.confirm_password').toUpperCase() }}</label>
                <input
                  v-model="profileForm.password_confirmation"
                  type="password"
                  class="form-control"
                  :placeholder="$t('dashboard.client_profile.leave_blank_password')"
                />
              </div>
            </div>

            <h6 class="text-uppercase text-xs font-weight-bolder text-secondary mb-3 mt-4">{{ $t('dashboard.client_profile.contact_info').toUpperCase() }}</h6>
            <div class="row">
              <div class="col-12 mb-3">
                <label class="form-label text-xs font-weight-bold text-secondary text-uppercase">{{ $t('dashboard.client_profile.address').toUpperCase() }}</label>
                <textarea
                  v-model="profileForm.address"
                  class="form-control"
                  rows="3"
                  :placeholder="$t('dashboard.client_profile.enter_address')"
                ></textarea>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label text-xs font-weight-bold text-secondary text-uppercase">WhatsApp</label>
                <input
                  v-model="profileForm.whatsapp"
                  type="text"
                  class="form-control"
                  placeholder="085159634244"
                />
              </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
              <button
                class="btn btn-primary px-5"
                @click="updateProfile"
                :disabled="saving"
              >
                <span v-if="saving">
                  <i class="fas fa-spinner fa-spin me-2"></i>
                  {{ $t('dashboard.client_profile.saving') }}
                </span>
                <span v-else>
                  {{ $t('dashboard.client_profile.save_changes') }}
                </span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Finance Tab -->
    <div v-show="activeTab === 'finance'" class="row">
      <div class="col-lg-4 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <h6 class="mb-3">{{ $t('dashboard.client_profile.my_wallet') }}</h6>
            <h3 class="text-success font-weight-bolder mb-0">{{ formatCurrency(balance) }}</h3>
            <p class="text-xs text-muted">{{ $t('dashboard.client_profile.current_balance') }}</p>
            <hr class="my-4" />
            
            <h6 class="mb-3">{{ $t('dashboard.client_profile.withdraw') }}</h6>
            <div v-if="balance < 1000000" class="alert alert-warning text-white text-xs">
              {{ $t('dashboard.client_profile.min_withdrawal') }}
            </div>
            
            <form @submit.prevent="submitWithdrawal">
              <div class="mb-3">
                <label class="form-label">{{ $t('dashboard.withdrawals.amount') }}</label>
                <div class="input-group">
                  <span class="input-group-text">Rp</span>
                  <input
                    v-model.number="withdrawForm.amount"
                    type="number"
                    class="form-control"
                    min="1000000"
                    :max="balance"
                    required
                  />
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">{{ $t('dashboard.client_profile.bank_name') }}</label>
                <select v-model="withdrawForm.bank_name" class="form-control" required>
                  <option value="">{{ $t('dashboard.client_profile.select_destination') }}</option>
                  
                  <optgroup label="National Banks">
                    <option value="BCA">BCA (Bank Central Asia)</option>
                    <option value="BRI">BRI (Bank Rakyat Indonesia)</option>
                    <option value="BNI">BNI (Bank Negara Indonesia)</option>
                    <option value="MANDIRI">Bank Mandiri</option>
                    <option value="CIMB">CIMB Niaga</option>
                    <option value="PERMATA">Bank Permata</option>
                    <option value="DANAMON">Bank Danamon</option>
                    <option value="MAYBANK">Maybank Indonesia</option>
                    <option value="MEGA">Bank Mega</option>
                    <option value="BSI">BSI (Bank Syariah Indonesia)</option>
                    <option value="BNC">BNC (Bank Neo Commerce)</option>
                    <option value="SAHABAT_SAMPOERNA">Bank Sahabat Sampoerna</option>
                    <option value="BJB">BJB (Bank Jabar Banten)</option>
                    <option value="MUAMALAT">Bank Muamalat</option>
                    <option value="ARTHA">Bank Artha Graha</option>
                    <option value="OCBC">OCBC NISP</option>
                    <option value="BTPN">BTPN (Jenius)</option>
                    <option value="JAGO">Bank Jago</option>
                    <option value="SEABANK">SeaBank</option>
                    <option value="BCA_SYR">BCA Syariah</option>
                  </optgroup>

                  <optgroup label="E-Wallet (Digital Wallet)">
                    <option value="DANA">DANA</option>
                    <option value="GOPAY">GoPay</option>
                    <option value="OVO">OVO</option>
                    <option value="LINKAJA">LinkAja</option>
                    <option value="SHOPEEPAY">ShopeePay</option>
                  </optgroup>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">{{ $t('dashboard.client_profile.account_number') }}</label>
                <div class="input-group">
                  <input
                    v-model="withdrawForm.account_number"
                    type="text"
                    class="form-control"
                    :placeholder="['DANA','GOPAY','OVO','LINKAJA','SHOPEEPAY'].includes(withdrawForm.bank_name) ? 'Example: 081234567890' : 'Example: 1234567890'"
                    required
                  />
                  <button 
                    class="btn btn-outline-primary mb-0" 
                    type="button" 
                    @click="verifyBankAccountName"
                    :disabled="isVerifyingBank || !withdrawForm.bank_name || !withdrawForm.account_number"
                  >
                    <i class="fas fa-spinner fa-spin me-1" v-if="isVerifyingBank"></i>
                    <i class="fas fa-search me-1" v-else></i>
                    {{ $t('dashboard.client_profile.check_account') }}
                  </button>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">{{ $t('dashboard.client_profile.account_name') }}</label>
                <input
                  v-model="withdrawForm.account_name"
                  type="text"
                  class="form-control"
                  :class="{'bg-gray-100': !isEwallet}"
                  :placeholder="isEwallet ? $t('dashboard.client_profile.ewallet_placeholder') : $t('dashboard.client_profile.check_account_verify')"
                  :readonly="!isEwallet"
                  required
                />
              </div>
              <p class="text-xs text-muted mt-2 mb-3">
                <i class="fas fa-info-circle me-1"></i>
                {{ $t('dashboard.client_profile.admin_fee_notice') }} <strong>Rp 5.000</strong> {{ $t('dashboard.client_profile.admin_fee_deducted') }}
              </p>

              <button
                type="submit"
                class="btn btn-primary w-100"
                :disabled="withdrawProcessing || balance < 1000000"
              >
                <span v-if="withdrawProcessing">{{ $t('dashboard.topup.processing') }}</span>
                <span v-else>{{ $t('dashboard.client_profile.submit_withdrawal') }}</span>
              </button>
            </form>
          </div>
        </div>
      </div>
      
      <div class="col-lg-8">
        <!-- Transaction History -->
        <div class="card mb-4">
          <div class="card-header pb-0">
            <h6>{{ $t('dashboard.client_profile.withdrawal_history') }}</h6>
          </div>
          <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
               <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ $t('dashboard.withdrawals.date') }}</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">{{ $t('common.details') }}</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ $t('dashboard.withdrawals.amount') }}</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="wd in withdrawHistory" :key="wd.id">
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">{{ formatDate(wd.created_at) }}</h6>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0">{{ wd.bank_name }} - {{ wd.account_number }}</p>
                        <p class="text-xs text-secondary mb-0">{{ wd.account_name }}</p>
                         <p v-if="wd.status === 'rejected'" class="text-xs text-danger mb-0">{{ wd.notes }}</p>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <span class="text-secondary text-xs font-weight-bold d-block">{{ formatCurrency(wd.amount) }}</span>
                        <span class="text-danger text-xxs d-block">- {{ formatCurrency(wd.admin_fee || 0) }}</span>
                        <span class="text-success text-xs font-weight-bolder d-block border-top mt-1 pt-1">{{ formatCurrency(wd.total_transfer || wd.amount) }}</span>
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
                          {{ wd.status === 'approved' ? 'SUCCESS' : (wd.status === 'rejected' ? 'FAILED' : 'PENDING') }}
                        </span>
                      </td>
                    </tr>
                     <tr v-if="!withdrawHistory || withdrawHistory.length === 0">
                      <td colspan="4" class="text-center py-4">
                        <span class="text-muted text-sm">{{ $t('dashboard.client_profile.no_withdrawal_history') }}</span>
                      </td>
                    </tr>
                  </tbody>
               </table>
            </div>
            <!-- Pagination Withdrawal -->
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
              <div class="text-xs text-secondary">
                Showing {{ withdrawPagination.current_page }} of {{ withdrawPagination.last_page }} pages
                (Total {{ withdrawPagination.total }})
              </div>
              <div class="d-flex align-items-center gap-2">
                <select v-model="withdrawLimit" class="form-select form-select-sm" style="width: 80px" @change="fetchWithdrawHistory(1)">
                  <option :value="10">10</option>
                  <option :value="25">25</option>
                  <option :value="50">50</option>
                   <option value="all">All</option>
                </select>
                <button 
                  class="btn btn-sm btn-outline-secondary mb-0" 
                  :disabled="withdrawPagination.current_page <= 1"
                  @click="fetchWithdrawHistory(withdrawPagination.current_page - 1)"
                >
                  <i class="fas fa-chevron-left"></i>
                </button>
                <button 
                  class="btn btn-sm btn-outline-secondary mb-0"
                  :disabled="withdrawPagination.current_page >= withdrawPagination.last_page"
                  @click="fetchWithdrawHistory(withdrawPagination.current_page + 1)"
                >
                  <i class="fas fa-chevron-right"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <div class="card">
          <div class="card-header pb-0">
            <h6>{{ $t('dashboard.client_profile.topup_history') }}</h6>
          </div>
           <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
               <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">{{ $t('dashboard.client_profile.reference') }}</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Amount</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="topup in topupHistory" :key="topup.id">
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">{{ formatDate(topup.created_at) }}</h6>
                          </div>
                        </div>
                      </td>
                       <td>
                        <p class="text-xs font-weight-bold mb-0">{{ topup.transaction_id }}</p>
                        <p class="text-xs text-secondary mb-0">{{ topup.payment_method }}</p>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <span class="text-success text-xs font-weight-bold">+ {{ formatCurrency(topup.amount) }}</span>
                      </td>
                      <td class="align-middle text-center text-sm">
                         <span
                          class="badge badge-sm"
                          :class="{
                            'bg-gradient-success': topup.status === 'success' || topup.status === 'paid',
                            'bg-gradient-warning': topup.status === 'pending',
                            'bg-gradient-danger': topup.status === 'failed' || topup.status === 'expired',
                          }"
                        >
                          {{ topup.status.toUpperCase() }}
                        </span>
                      </td>
                    </tr>
                    <tr v-if="!topupHistory || topupHistory.length === 0">
                      <td colspan="4" class="text-center py-4">
                        <span class="text-muted text-sm">{{ $t('dashboard.client_profile.no_topup_history') }}</span>
                      </td>
                    </tr>
                  </tbody>
               </table>
            </div>
            <!-- Pagination Topup -->
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
              <div class="text-xs text-secondary">
                Showing {{ topupPagination.current_page }} of {{ topupPagination.last_page }} pages
                (Total {{ topupPagination.total }})
              </div>
              <div class="d-flex align-items-center gap-2">
                <select v-model="topupLimit" class="form-select form-select-sm" style="width: 80px" @change="fetchTopupHistory(1)">
                  <option :value="10">10</option>
                  <option :value="25">25</option>
                  <option :value="50">50</option>
                   <option value="all">All</option>
                </select>
                <button 
                  class="btn btn-sm btn-outline-secondary mb-0" 
                  :disabled="topupPagination.current_page <= 1"
                  @click="fetchTopupHistory(topupPagination.current_page - 1)"
                >
                  <i class="fas fa-chevron-left"></i>
                </button>
                <button 
                  class="btn btn-sm btn-outline-secondary mb-0"
                  :disabled="topupPagination.current_page >= topupPagination.last_page"
                  @click="fetchTopupHistory(topupPagination.current_page + 1)"
                >
                  <i class="fas fa-chevron-right"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import api from '@/services/api'
import LoadingOverlay from '@/components/LoadingOverlay.vue'
import notify, { confirm as confirmAction } from '@/utils/notify'

const activeTab = ref('profile')
const referralCode = ref('BBHFG')
const balance = ref(0)
const topupHistory = ref([])
const withdrawHistory = ref([])
const topupPagination = ref({ current_page: 1, last_page: 1, per_page: 10, total: 0 })
const withdrawPagination = ref({ current_page: 1, last_page: 1, per_page: 10, total: 0 })
const topupLimit = ref(10)
const withdrawLimit = ref(10)

const isLoading = ref(false)
const saving = ref(false)
const withdrawProcessing = ref(false)
const refreshInterval = ref(null)
const isVerifyingBank = ref(false)
const isEwallet = ref(false)

const profileForm = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  address: '',
  whatsapp: '',
})

const withdrawForm = ref({
  amount: 1000000,
  bank_name: '',
  account_number: '',
  account_name: '',
})

const startPolling = () => {
  if (refreshInterval.value) return
  
  refreshInterval.value = setInterval(async () => {
    const hasPending = withdrawHistory.value?.some(w => w.status === 'pending')
    if (hasPending) {
      await fetchWithdrawHistory()
      await fetchBalance()
    } else {
      stopPolling()
    }
  }, 3000)
}

const stopPolling = () => {
  if (refreshInterval.value) {
    clearInterval(refreshInterval.value)
    refreshInterval.value = null
  }
}

onMounted(async () => {
  isLoading.value = true
  await Promise.all([
    fetchProfile(),
    fetchBalance(),
    fetchTopupHistory(),
    fetchWithdrawHistory()
  ])
  isLoading.value = false
  startPolling()
})

onUnmounted(() => {
  stopPolling()
})

const fetchProfile = async () => {
  try {
    const response = await api.get('/isp-admin/client-area/profile')
    const user = response.data
    profileForm.value = {
      name: user.name || '',
      email: user.email || '',
      password: '',
      password_confirmation: '',
      address: user.address || '',
      whatsapp: user.whatsapp || user.phone || '',
    }
    referralCode.value = user.referral_code || 'BBHFG'
  } catch (error) {
    console.error('Error fetching profile:', error)
  }
}

const fetchBalance = async () => {
  try {
    const response = await api.get('/isp-admin/client-area/balance')
    balance.value = response.data.balance || 0
  } catch (error) {
    console.error('Error fetching balance:', error)
  }
}

const fetchTopupHistory = async (page = 1) => {
  try {
    const response = await api.get('/isp-admin/client-area/topup-history', {
      params: { page, per_page: topupLimit.value }
    })
    topupHistory.value = response.data.data
    topupPagination.value = {
        current_page: response.data.current_page,
        last_page: response.data.last_page,
        per_page: response.data.per_page,
        total: response.data.total
    }
  } catch (error) {
    console.error('Error fetching topup history:', error)
  }
}

const fetchWithdrawHistory = async (page = 1) => {
  try {
    const response = await api.get('/isp-admin/client-area/withdrawal-history', {
      params: { page, per_page: withdrawLimit.value }
    })
    withdrawHistory.value = response.data.data
    withdrawPagination.value = {
        current_page: response.data.current_page,
        last_page: response.data.last_page,
        per_page: response.data.per_page,
        total: response.data.total
    }
  } catch (error) {
    console.error('Error fetching withdrawal history:', error)
  }
}

const updateProfile = async () => {
  if (profileForm.value.password && profileForm.value.password !== profileForm.value.password_confirmation) {
    notify('error', 'Error', 'Password confirmation does not match')
    return
  }

  saving.value = true
  try {
    const data = { ...profileForm.value }
    if (!data.password) {
      delete data.password
      delete data.password_confirmation
    }
    
    const response = await api.put('/isp-admin/client-area/profile', data)
    
    if (response.data.success) {
      notify('success', 'Success', 'Profile updated successfully!')
      await fetchProfile()
    }
  } catch (error) {
    console.error('Error updating profile:', error)
    notify('error', 'Failed', error.response?.data?.message || 'Failed to update profile')
  } finally {
    saving.value = false
  }
}

const verifyBankAccountName = async () => {
  if (!withdrawForm.value.bank_code && !withdrawForm.value.bank_name) {
      notify('warning', 'Warning', 'Please select a Bank first');
      return;
  }
  if (!withdrawForm.value.account_number) {
      notify('warning', 'Warning', 'Please enter account number');
      return;
  }

  isVerifyingBank.value = true;
  withdrawForm.value.account_name = ''; // Reset when retrying
  isEwallet.value = false;

  try {
    const response = await api.post('/isp-admin/client-area/verify-bank', {
       bank_code: withdrawForm.value.bank_name,
       bank_account_number: withdrawForm.value.account_number
    });

    if (response.data.is_ewallet) {
       isEwallet.value = true;
       notify('info', 'E-Wallet Detected', 'Auto name verification is not supported for E-Wallets. Please enter your name manually.');
    } else if (response.data.is_fallback) {
       isEwallet.value = true; // Unlock the input just like E-wallet
       notify('warning', 'Attention (Fallback)', 'Xendit name validation is currently unavailable. Please enter your name manually. ENSURE THE NAME AND ACCOUNT NUMBER ARE CORRECT to avoid failed disbursement!');
    } else if (response.data.success && response.data.account_name) {
       withdrawForm.value.account_name = response.data.account_name;
       notify('success', 'Success', `Account verified as: ${response.data.account_name}`);
    } else {
       notify('warning', 'Failed', 'Account number not found or incorrect');
    }
  } catch (error) {
    console.error('Bank Verify Error:', error);
    notify('error', 'Failed', error.response?.data?.message || 'Failed to verify bank with server/gateway.');
  } finally {
    isVerifyingBank.value = false;
  }
}

const submitWithdrawal = async () => {
    if(withdrawForm.value.amount < 1000000) {
        notify('warning', 'Minimum withdrawal is Rp 1.000.000');
        return;
    }
    
    if(!withdrawForm.value.account_name) {
        notify('error', 'Failed', 'Please click "Check Account" first to verify the account holder name.');
        return;
    }

    if(isEwallet.value) {
         const confirmManual = await confirmAction('Manual Name Confirmation', `You entered the name manually for E-Wallet/Fallback.\n\nAre you SURE the account holder name: "${withdrawForm.value.account_name}" is CORRECT?\n\nIf incorrect, funds may be lost or stuck.`);
         if(!confirmManual) return;
    }
    
    const adminFee = 5000;
    const netAmount = withdrawForm.value.amount - adminFee;
    
    const confirmed = await confirmAction('Confirm Withdrawal', `Are you sure you want to withdraw ${formatCurrency(withdrawForm.value.amount)}?\n\nAn admin fee of ${formatCurrency(adminFee)} will be deducted, so you will receive ${formatCurrency(netAmount)}.`);
    if (!confirmed) return;
  
    withdrawProcessing.value = true
    try {
      const response = await api.post('/isp-admin/client-area/withdraw', withdrawForm.value)
      
      if (response.data.success) {
        notify('success', 'Success', 'Withdrawal request submitted!');
        // Reset form
        withdrawForm.value = {
          amount: 1000000,
          bank_name: '',
          account_number: '',
          account_name: '',
        }
        // Refresh Data
        await fetchBalance()
        await fetchWithdrawHistory()
        startPolling()
      }
    } catch (error) {
      console.error('Withdrawal error:', error)
      notify('error', 'Failed', error.response?.data?.message || 'Failed to submit withdrawal request')
    } finally {
      withdrawProcessing.value = false
    }
  }
  
  const copyReferralCode = async () => {
    try {
      await navigator.clipboard.writeText(referralCode.value)
      notify('success', 'Copied!', 'Referral code copied to clipboard', 1500)
    } catch (error) {
      console.error('Failed to copy:', error)
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
  return new Date(date).toLocaleDateString('en-US')
}
</script>
