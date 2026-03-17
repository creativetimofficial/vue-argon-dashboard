<template>
  <div class="py-4 container-fluid">
    <LoadingOverlay :active="isLoading" />
    <div class="row">
    <div class="col-12">
      <div class="card mb-4 min-vh-75">
        <div class="card-header pb-0">
          <h6 class="mb-0">{{ $t('dashboard.services.title') }}</h6>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                   <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">{{ $t('dashboard.services.product') }}</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ $t('dashboard.services.account_user') }}</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ $t('dashboard.services.address_ip') }}</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ $t('dashboard.services.expiry') }}</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ $t('dashboard.services.price') }}</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ $t('dashboard.services.domain') }}</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ $t('dashboard.services.notes') }}</th>
                  <th class="text-secondary opacity-7"></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="service in services" :key="service.id">
                  <td>
                    <div class="d-flex px-2 py-1">
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm">{{ service.id }}</h6>
                      </div>
                    </div>
                  </td>
                  <td>
                    <p class="text-xs font-weight-bold mb-0">{{ service.service?.name || service.subscription_package?.name || 'N/A' }}</p>
                  </td>
                  <td class="align-middle text-center text-sm">
                    <span class="text-secondary text-xs font-weight-bold">{{ service.username || '-' }}</span>
                  </td>
                  <td class="align-middle text-center">
                    <span class="text-secondary text-xs font-weight-bold">{{ service.server_address || '-' }}</span>
                  </td>
                  <td class="align-middle text-center">
                    <span
                      class="text-secondary text-xs font-weight-bold"
                      :class="{ 'text-danger': isExpired(service.expired_date) }"
                    >
                      {{ formatDate(service.expired_date) }}
                      <span v-if="isExpired(service.expired_date)" class="text-danger">({{ $t('dashboard.services.expired') }})</span>
                    </span>
                  </td>
                  <td class="align-middle text-center">
                    <span class="text-secondary text-xs font-weight-bold">{{ formatCurrency(service.price) }}</span>
                  </td>
                  <td class="align-middle text-center text-sm">
                    <span
                      class="badge badge-sm"
                      :class="{
                        'bg-gradient-warning': service.status === 'pending',
                        'bg-gradient-success': service.status === 'active',
                        'bg-gradient-danger': service.status === 'terminated' || service.status === 'cancelled',
                      }"
                    >
                      {{ service.status.toUpperCase() }}
                    </span>
                  </td>
                  <td class="align-middle text-center">
                    <a
                      v-if="service.domain"
                      :href="getDomainUrl(service.domain)"
                      target="_blank"
                      class="text-xs font-weight-bold mb-0"
                    >
                      {{ service.domain }}
                      <i class="fas fa-external-link-alt ms-1"></i>
                    </a>
                    <span v-else class="text-secondary text-xs">-</span>
                  </td>
                  <td class="align-middle text-center">
                    <span class="text-secondary text-xs">{{ service.notes || '-' }}</span>
                  </td>
                  <td class="align-middle">
                    <router-link
                      :to="{ name: 'ClientAreaServiceDetail', params: { id: service.id } }"
                      class="btn btn-info btn-sm"
                    >
                      <i class="fas fa-eye me-1"></i>
                      Details
                    </router-link>
                  </td>
                </tr>
                <tr v-if="services.length === 0">
                  <td colspan="10" class="text-center py-4">
                    <p class="text-muted mb-0">{{ $t('dashboard.services.no_services') }}</p>
                  </td>
                </tr>
              </tbody>
            </table>
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
import LoadingOverlay from '@/components/LoadingOverlay.vue'

const services = ref([])
const isLoading = ref(false)

onMounted(async () => {
  await fetchServices()
})

const fetchServices = async () => {
  isLoading.value = true
  try {
    const response = await api.get('/isp-admin/client-area/my-services')
    services.value = response.data
  } catch (error) {
    console.error('Error fetching services:', error)
  } finally {
    isLoading.value = false
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
  if (!date) return '-'
  return new Date(date).toLocaleDateString('en-US')
}

const isExpired = (date) => {
  if (!date) return false
  return new Date(date) < new Date()
}

const getDomainUrl = (domain) => {
  if (domain.startsWith('http')) return domain
  const protocol = window.location.protocol
  const port = window.location.port ? `:${window.location.port}` : ''
  return `${protocol}//${domain}${port}`
}
</script>
