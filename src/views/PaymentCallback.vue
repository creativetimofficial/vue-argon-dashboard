<template>
  <main class="main-content mt-0">
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8 col-12">
          <div class="card shadow-lg border-0 rounded-lg mt-5">
            <div class="card-body text-center p-5">
              <div v-if="loading" class="py-5">
                <div class="spinner-border text-success mb-4" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <p>Memproses pembayaran...</p>
              </div>
              <div v-else-if="success">
                <i class="fas fa-check-circle fa-4x text-success mb-4"></i>
                <h2 class="mb-3 text-success">Pembayaran Berhasil!</h2>
                <p class="mb-4">Paket Anda telah berhasil diaktifkan.<br />Anda akan diarahkan ke dashboard.</p>
              </div>
              <div v-else-if="errorMessage">
                <i class="fas fa-times-circle fa-4x text-danger mb-4"></i>
                <h2 class="mb-3 text-danger">Pembayaran Gagal</h2>
                <p class="mb-4">{{ errorMessage }}</p>
                <router-link to="/client-area" class="btn btn-primary px-4 py-2">Kembali ke Client Area</router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/services/api'

const router = useRouter()
const route = useRoute()
const loading = ref(true)
const success = ref(false)
const errorMessage = ref('')

onMounted(async () => {
  await processPayment()
})

const processPayment = async () => {
  try {
    const params = route.query
    
    if (!params.order_id && (!params.isp_id || !params.package_id)) {
      errorMessage.value = 'Parameter pembayaran tidak valid.'
      loading.value = false
      return
    }

    const payload = {
      status: params.status || 'success',
      transaction_id: params.transaction_id,
    }

    if (params.order_id) {
      payload.order_id = params.order_id
    } else {
      payload.isp_id = params.isp_id
      payload.package_id = params.package_id
      payload.amount = params.amount
      payload.billing_cycle = params.billing_cycle || 'monthly'
    }

    const response = await api.post('/payment-callback', payload)

    if (response.data.success) {
      success.value = true
      
      // Short delay for visual confirmation, then redirect
      setTimeout(() => {
         const token = localStorage.getItem('auth_token')
         if (token) {
            router.push('/client-area/services')
         } else {
            // User not logged in, send to login
            router.push('/login')
         }
      }, 1500)
    } else {
      errorMessage.value = response.data.message || 'Pembayaran gagal.'
    }
  } catch (error) {
    console.error('Payment callback error:', error)
    errorMessage.value = error.response?.data?.message || 'Terjadi kesalahan saat memproses pembayaran.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.card {
  border-radius: 1rem;
}
</style>
