<template>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header pb-0 bg-gradient-primary">
          <h6 class="text-white mb-0">Detail Services</h6>
        </div>
        <div class="card-body">
          <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
          <div v-else-if="service">
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label text-sm font-weight-bold">Status</label>
                  <div>
                    <span
                      class="badge badge-lg"
                      :class="{
                        'bg-gradient-warning': service.status === 'pending',
                        'bg-gradient-success': service.status === 'active',
                        'bg-gradient-danger': service.status === 'terminated' || service.status === 'cancelled',
                      }"
                    >
                      {{ service.status.toUpperCase() }}
                    </span>
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label text-sm font-weight-bold">Product</label>
                  <p class="mb-0">{{ service.service?.name || 'N/A' }}</p>
                </div>
                <div class="mb-3">
                  <label class="form-label text-sm font-weight-bold">Price</label>
                  <p class="mb-0">{{ formatCurrency(service.price) }}</p>
                </div>
                <div class="mb-3">
                  <label class="form-label text-sm font-weight-bold">Server Address</label>
                  <p class="mb-0">{{ service.server_address || 'N/A' }}</p>
                </div>
                <div class="mb-3">
                  <label class="form-label text-sm font-weight-bold">Username</label>
                  <p class="mb-0">{{ service.username || 'N/A' }}</p>
                </div>
                <div class="mb-3">
                  <label class="form-label text-sm font-weight-bold">Password</label>
                  <div class="input-group">
                    <input
                      :type="showPassword ? 'text' : 'password'"
                      :value="service.password || 'N/A'"
                      class="form-control"
                      readonly
                    />
                    <button
                      class="btn btn-outline-secondary"
                      type="button"
                      @click="showPassword = !showPassword"
                    >
                      <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </button>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label text-sm font-weight-bold">Expired Date</label>
                  <p
                    class="mb-0"
                    :class="{ 'text-danger': isExpired(service.expired_date) }"
                  >
                    {{ formatDate(service.expired_date) }}
                    <span v-if="isExpired(service.expired_date)" class="text-danger">(Expired)</span>
                  </p>
                </div>
                <div class="mb-3">
                  <label class="form-label text-sm font-weight-bold">IP Address</label>
                  <p class="mb-0">{{ service.ip_address || 'N/A' }}</p>
                </div>
                <div class="mb-3">
                  <label class="form-label text-sm font-weight-bold">Domain</label>
                  <p class="mb-0">
                    <a
                      v-if="service.domain"
                      :href="service.domain.startsWith('http') ? service.domain : 'https://' + service.domain"
                      target="_blank"
                    >
                      {{ service.domain }}
                      <i class="fas fa-external-link-alt ms-1"></i>
                    </a>
                    <span v-else>N/A</span>
                  </p>
                </div>
                <div class="mb-3">
                  <label class="form-label text-sm font-weight-bold">Catatan</label>
                  <textarea
                    v-model="service.notes"
                    class="form-control"
                    rows="3"
                    placeholder="N/A"
                    maxlength="50"
                  ></textarea>
                  <small class="text-muted">maximum 50 karakter</small>
                </div>
                <div class="mb-3">
                  <label class="form-label text-sm font-weight-bold">Auto Renew</label>
                  <div class="form-check form-switch">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      v-model="service.auto_renew"
                      :disabled="!canEdit"
                    />
                    <label class="form-check-label">
                      {{ service.auto_renew ? 'Enabled' : 'Disabled' }}
                      <span v-if="!service.auto_renew" class="text-muted">(pastikan saldo cukup)</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="service.l2tp_config" class="mb-3">
              <label class="form-label text-sm font-weight-bold">L2TP Config</label>
              <div class="input-group">
                <input
                  type="text"
                  :value="service.l2tp_config"
                  class="form-control"
                  readonly
                />
                <button
                  class="btn btn-outline-secondary"
                  type="button"
                  @click="copyToClipboard(service.l2tp_config)"
                >
                  <i class="fas fa-copy"></i>
                </button>
              </div>
            </div>

            <div v-if="service.sstp_config" class="mb-3">
              <label class="form-label text-sm font-weight-bold">SSTP Config</label>
              <div class="input-group">
                <input
                  type="text"
                  :value="service.sstp_config"
                  class="form-control"
                  readonly
                />
                <button
                  class="btn btn-outline-secondary"
                  type="button"
                  @click="copyToClipboard(service.sstp_config)"
                >
                  <i class="fas fa-copy"></i>
                </button>
              </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
              <button
                class="btn btn-dark"
                @click="saveChanges"
                :disabled="!canEdit || saving"
              >
                <span v-if="saving">
                  <i class="fas fa-spinner fa-spin me-2"></i>
                  Saving...
                </span>
                <span v-else>Save Changes</span>
              </button>
              <router-link
                to="/client-area/services"
                class="btn btn-info"
              >
                <i class="fas fa-arrow-left me-2"></i>
                Back to List
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/api'

const route = useRoute()
const router = useRouter()

const service = ref(null)
const loading = ref(false)
const saving = ref(false)
const showPassword = ref(false)

const canEdit = computed(() => {
  return service.value && (service.value.status === 'active' || service.value.status === 'pending')
})

onMounted(async () => {
  await fetchServiceDetail()
})

const fetchServiceDetail = async () => {
  loading.value = true
  try {
    const response = await api.get(`/isp-admin/client-area/services/${route.params.id}`)
    service.value = response.data
  } catch (error) {
    console.error('Error fetching service detail:', error)
    alert('Failed to load service details')
    router.push('/client-area/services')
  } finally {
    loading.value = false
  }
}

const saveChanges = async () => {
  saving.value = true
  try {
    const response = await api.put(`/isp-admin/client-area/services/${route.params.id}`, {
      notes: service.value.notes,
      auto_renew: service.value.auto_renew,
    })
    
    if (response.data.success) {
      alert('Changes saved successfully!')
    }
  } catch (error) {
    console.error('Error saving changes:', error)
    alert('Failed to save changes')
  } finally {
    saving.value = false
  }
}

const copyToClipboard = async (text) => {
  try {
    await navigator.clipboard.writeText(text)
    alert('Copied to clipboard!')
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
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('id-ID')
}

const isExpired = (date) => {
  if (!date) return false
  return new Date(date) < new Date()
}
</script>
