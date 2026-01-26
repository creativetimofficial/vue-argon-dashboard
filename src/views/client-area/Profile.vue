<template>
  <div class="py-4 container-fluid">
    <div class="row">
    <!-- Left Column -->
    <div class="col-lg-8">
      <!-- Referral Code Card -->
      <div class="card mb-4">
        <div class="card-body">
          <h6 class="mb-3">Kode Referral Anda</h6>
          <div class="input-group mb-3">
            <input
              type="text"
              class="form-control"
              :value="referralCode"
              readonly
            />
            <button
              class="btn btn-outline-secondary"
              type="button"
              @click="copyReferralCode"
            >
              <i class="fas fa-copy me-2"></i>
              Salin Kode
            </button>
          </div>
          <button
            class="btn btn-primary btn-sm"
            @click="updateReferralCode"
          >
            <i class="fas fa-user-plus me-2"></i>
            Update Referral
          </button>
          <p class="text-sm text-muted mt-3 mb-0">
            Sebarkan kode ini untuk mendapatkan komisi 20%. Pelanggan yang menggunakan kode ini juga akan menerima diskon 10%.
          </p>
        </div>
      </div>

      <!-- Edit Profile Card -->
      <div class="card">
        <div class="card-body">
          <h6 class="mb-4">Edit Profile</h6>
          
          <h6 class="text-uppercase text-xs font-weight-bolder mb-3">USER INFORMATION</h6>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Fullname</label>
              <input
                v-model="profileForm.name"
                type="text"
                class="form-control"
                placeholder="Fullname"
              />
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Email Address</label>
              <input
                v-model="profileForm.email"
                type="email"
                class="form-control"
                placeholder="Email"
                readonly
              />
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">New Password</label>
              <input
                v-model="profileForm.password"
                type="password"
                class="form-control"
                placeholder="New Password"
              />
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Confirm Password</label>
              <input
                v-model="profileForm.password_confirmation"
                type="password"
                class="form-control"
                placeholder="Confirm Password"
              />
            </div>
          </div>

          <h6 class="text-uppercase text-xs font-weight-bolder mb-3 mt-4">CONTACT INFORMATION</h6>
          <div class="row">
            <div class="col-12 mb-3">
              <label class="form-label">Address</label>
              <textarea
                v-model="profileForm.address"
                class="form-control"
                rows="3"
                placeholder="Enter your full address"
              ></textarea>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">WhatsApp</label>
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
              class="btn btn-success"
              @click="updateProfile"
              :disabled="saving"
            >
              <span v-if="saving">
                <i class="fas fa-spinner fa-spin me-2"></i>
                Saving...
              </span>
              <span v-else>
                <i class="fas fa-user me-2"></i>
                Update Profile
              </span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Column -->
    <div class="col-lg-4">
      <!-- Your Transactions Card -->
      <div class="card mb-4">
        <div class="card-body">
          <h6 class="mb-3">Your Transactions</h6>
          <p class="text-sm text-muted mb-3">Last 5 transactions</p>
          <div class="mb-3">
            <h4 class="text-success mb-0">CURRENT BALANCE</h4>
            <h3 class="text-success font-weight-bolder">{{ formatCurrency(balance) }}</h3>
          </div>
          <p class="text-sm text-muted mb-0">Withdraw minimal Rp 1.000.000</p>
        </div>
      </div>

      <!-- Topup History Card -->
      <div class="card">
        <div class="card-body">
          <h6 class="mb-3">Topup History</h6>
          <p class="text-sm text-muted mb-3">last 5 topups</p>
          <div v-if="topupHistory.length === 0" class="text-center py-3">
            <p class="text-muted text-sm mb-0">No topup history</p>
          </div>
          <div v-else>
            <div
              v-for="topup in topupHistory"
              :key="topup.id"
              class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom"
            >
              <div>
                <p class="text-sm font-weight-bold mb-0">{{ formatCurrency(topup.amount) }}</p>
                <p class="text-xs text-muted mb-0">{{ formatDate(topup.created_at) }}</p>
              </div>
              <span
                class="badge badge-sm"
                :class="{
                  'bg-gradient-success': topup.status === 'success',
                  'bg-gradient-warning': topup.status === 'pending',
                }"
              >
                {{ topup.status }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const referralCode = ref('BBHFG')
const balance = ref(0)
const topupHistory = ref([])
const saving = ref(false)

const profileForm = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  address: '',
  whatsapp: '',
})

onMounted(async () => {
  await Promise.all([
    fetchProfile(),
    fetchBalance(),
    fetchTopupHistory()
  ])
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

const fetchTopupHistory = async () => {
  try {
    const response = await api.get('/isp-admin/client-area/topup-history')
    topupHistory.value = response.data
  } catch (error) {
    console.error('Error fetching topup history:', error)
  }
}

const updateProfile = async () => {
  if (profileForm.value.password && profileForm.value.password !== profileForm.value.password_confirmation) {
    alert('Password confirmation does not match')
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
      alert('Profile updated successfully!')
      await fetchProfile()
    }
  } catch (error) {
    console.error('Error updating profile:', error)
    alert(error.response?.data?.message || 'Failed to update profile')
  } finally {
    saving.value = false
  }
}

const copyReferralCode = async () => {
  try {
    await navigator.clipboard.writeText(referralCode.value)
    alert('Referral code copied to clipboard!')
  } catch (error) {
    console.error('Failed to copy:', error)
  }
}

const updateReferralCode = async () => {
  const newCode = prompt('Enter new referral code:', referralCode.value)
  if (newCode) {
    try {
      const response = await api.put('/isp-admin/client-area/referral-code', {
        referral_code: newCode
      })
      if (response.data.success) {
        referralCode.value = newCode
        alert('Referral code updated!')
      }
    } catch (error) {
      console.error('Error updating referral code:', error)
      alert('Failed to update referral code')
    }
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
