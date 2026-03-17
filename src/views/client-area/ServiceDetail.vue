<template>
  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header pb-0">
            <div class="d-flex justify-content-between">
              <h6 class="mb-0">{{ $t('dashboard.services.title') }}</h6>
            </div>
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
                    <label class="form-label text-sm font-weight-bold">{{ $t('common.status') }}</label>
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
                    <label class="form-label text-sm font-weight-bold">{{ $t('dashboard.services.product') }}</label>
                    <p class="mb-0">{{ service.service?.name || service.subscription_package?.name || 'N/A' }}</p>
                  </div>
                  <div class="mb-3">
                    <label class="form-label text-sm font-weight-bold">{{ $t('dashboard.services.price') }}</label>
                    <p class="mb-0">{{ formatCurrency(service.price) }}</p>
                  </div>
                  
                  <!-- Credentials for Services AND Packages -->
                  <div>
                    <div class="mb-3">
                      <label class="form-label text-sm font-weight-bold">{{ $t('dashboard.server.ip_address') }}</label>
                      <p class="mb-0">{{ service.server_address || 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                      <label class="form-label text-sm font-weight-bold">{{ $t('dashboard.server.username') }}</label>
                      <p class="mb-0">{{ service.username || 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                      <label class="form-label text-sm font-weight-bold">{{ $t('dashboard.server.password') }}</label>
                      <div class="position-relative">
                        <input
                          :type="showPassword ? 'text' : 'password'"
                          :value="service.password || 'N/A'"
                          class="form-control"
                          readonly
                          style="height: 40px; padding-right: 40px;"
                        />
                        <button
                          class="btn btn-link text-secondary mb-0 border-0"
                          type="button"
                          style="height: 40px; position: absolute; right: 0; top: 0; z-index: 5;"
                          @click="showPassword = !showPassword"
                        >
                          <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label text-sm font-weight-bold">{{ $t('dashboard.services.expiry') }}</label>
                    <p
                      class="mb-0"
                      :class="{ 'text-danger': isExpired(service.expired_date) }"
                    >
                      {{ formatDate(service.expired_date) }}
                      <span v-if="isExpired(service.expired_date)" class="text-danger">({{ $t('dashboard.services.expired') }})</span>
                    </p>
                  </div>
                  
                  <div class="mb-3">
                    <label class="form-label text-sm font-weight-bold">{{ $t('dashboard.server.ip_address') }}</label>
                    <p class="mb-0">{{ service.ip_address || 'N/A' }}</p>
                  </div>

                  <div class="mb-3">
                    <label class="form-label text-sm font-weight-bold">{{ $t('dashboard.service_detail.isp_url') }}</label>
                    <p class="mb-0">
                      <a
                        v-if="service.domain"
                        :href="getDomainUrl(service.domain)"
                        target="_blank"
                        class="btn btn-sm btn-outline-primary mb-0 mt-1"
                      >
                        <i class="fas fa-external-link-alt me-2"></i>
                        Visit ISP Website
                      </a>
                      <span v-else>N/A</span>
                    </p>
                    <small v-if="service.domain" class="text-muted d-block mt-1">
                      Manage this ISP system at: {{ getDisplayDomain(service.domain) }}
                    </small>
                  </div>
                  <div class="mb-3">
                    <label class="form-label text-sm font-weight-bold">{{ $t('dashboard.server.notes') }}</label>
                    <textarea
                      v-model="service.notes"
                      class="form-control"
                      rows="3"
                      placeholder="N/A"
                      maxlength="50"
                    ></textarea>
                    <small class="text-muted">maximum 50 characters</small>
                  </div>
                  
                  <div class="mb-3">
                    <label class="form-label text-sm font-weight-bold">{{ $t('dashboard.service_detail.auto_renew') }}</label>
                    <div class="form-check form-switch ps-0 mt-2">
                      <input
                        class="form-check-input ms-0"
                        type="checkbox"
                        v-model="service.auto_renew"
                        :disabled="!canEdit"
                        id="autoRenewSwitch"
                      />
                      <label class="form-check-label text-body ms-2 text-truncate w-80 mb-0" for="autoRenewSwitch">
                        {{ service.auto_renew ? 'Enabled' : 'Disabled' }}
                        <span v-if="!service.auto_renew" class="text-muted">(ensure sufficient balance)</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <div v-if="service.l2tp_config" class="mb-3">
                <label class="form-label text-sm font-weight-bold">{{ $t('dashboard.service_detail.l2tp_config') }}</label>
                <div class="position-relative">
                  <input
                    type="text"
                    :value="service.l2tp_config"
                    class="form-control"
                    readonly
                    style="height: 40px; padding-right: 40px;"
                  />
                  <button
                    class="btn btn-link text-secondary mb-0 border-0"
                    type="button"
                    style="height: 40px; position: absolute; right: 0; top: 0; z-index: 5;"
                    @click="copyToClipboard(service.l2tp_config)"
                  >
                    <i class="fas fa-copy"></i>
                  </button>
                </div>
              </div>

              <div v-if="service.sstp_config" class="mb-3">
                <label class="form-label text-sm font-weight-bold">{{ $t('dashboard.service_detail.sstp_config') }}</label>
                <div class="position-relative">
                  <input
                    type="text"
                    :value="service.sstp_config"
                    class="form-control"
                    readonly
                    style="height: 40px; padding-right: 40px;"
                  />
                  <button
                    class="btn btn-link text-secondary mb-0 border-0"
                    type="button"
                    style="height: 40px; position: absolute; right: 0; top: 0; z-index: 5;"
                    @click="copyToClipboard(service.sstp_config)"
                  >
                    <i class="fas fa-copy"></i>
                  </button>
                </div>
              </div>

            <div class="d-flex justify-content-end mt-4 gap-2">
              <router-link
                to="/client-area/services"
                class="btn btn-secondary mb-0"
              >
                <i class="fas fa-arrow-left me-2"></i>
                {{ $t('common.back') }}
              </router-link>
              
              <button
                class="btn btn-primary mb-0"
                @click="saveChanges"
                :disabled="!canEdit || saving"
              >
                <span v-if="saving">
                  <i class="fas fa-spinner fa-spin me-2"></i>
                  Saving...
                </span>
                <span v-else>{{ $t('common.save') }}</span>
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
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/api'
import notify from '@/utils/notify'

const route = useRoute()
const router = useRouter()

const service = ref(null)
const baseDomain = ref('localhost')
const loading = ref(false)
const saving = ref(false)
const showPassword = ref(false)

const canEdit = computed(() => {
  return service.value && (service.value.status === 'active' || service.value.status === 'pending')
})

onMounted(async () => {
  await Promise.all([
    fetchServiceDetail(),
    fetchMainDomain()
  ])
})

const fetchServiceDetail = async () => {
  loading.value = true
  try {
    const response = await api.get(`/isp-admin/client-area/services/${route.params.id}`)
    service.value = response.data
  } catch (error) {
    console.error('Error fetching service detail:', error)
    notify('error', 'Error', 'Failed to load service details')
    router.push('/client-area/services')
  } finally {
    loading.value = false
  }
}

const fetchMainDomain = async () => {
  try {
    const res = await api.get('/system/main-domain')
    if (res.data.settings && res.data.settings.base_domain) {
      baseDomain.value = res.data.settings.base_domain
    }
  } catch (err) {
    console.error('Failed to fetch main domain', err)
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
      notify('success', 'Success', 'Changes saved successfully!')
    }
  } catch (error) {
    console.error('Error saving changes:', error)
    notify('error', 'Error', 'Failed to save changes')
  } finally {
    saving.value = false
  }
}

const copyToClipboard = async (text) => {
  try {
    await navigator.clipboard.writeText(text)
    notify('success', 'Copied', 'Copied to clipboard!')
  } catch (error) {
    console.error('Failed to copy:', error)
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
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US')
}

const isExpired = (date) => {
  if (!date) return false
  return new Date(date) < new Date()
}

const getDomainUrl = (domain) => {
  if (domain.startsWith('http')) return domain
  
  const finalDomain = getDisplayDomain(domain)
  const protocol = window.location.protocol
  return `${protocol}//${finalDomain}`
}

const getDisplayDomain = (domain) => {
  if (!domain) return 'N/A'
  if (domain.startsWith('http')) return domain.replace(/^https?:\/\//, '')
  
  let subdomain = domain
  if (service.value && service.value.domain_type === 'subdomain' && service.value.subdomain) {
    subdomain = service.value.subdomain
  }
  
  // If it's already a full domain with more than one dot and not ending in .localhost, 
  // we might want to be careful, but the logic below is generally safe.
  
  let base = baseDomain.value
  let port = ''
  
  // Local development override: if current host is localhost, force subdomains to use localhost:8080
  if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
    base = 'localhost'
    port = ':8080'
  } else {
    // Production logic
    port = (base === 'localhost') ? ':8080' : ''
  }
  
  return `${subdomain}.${base}${port}`
}
</script>
