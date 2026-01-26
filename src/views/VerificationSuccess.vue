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
                <p>Memverifikasi email Anda...</p>
              </div>
              <div v-else-if="success">
                <i class="fas fa-check-circle fa-4x text-success mb-4"></i>
                <h2 class="mb-3 text-success">Verifikasi Berhasil!</h2>
                <p class="mb-4">Email Anda telah berhasil diverifikasi.<br />Silakan login untuk mulai menggunakan layanan.</p>
                <router-link to="/login" class="btn btn-success px-4 py-2">Login Sekarang</router-link>
              </div>
              <div v-else-if="errorMessage">
                <i class="fas fa-times-circle fa-4x text-danger mb-4"></i>
                <h2 class="mb-3 text-danger">Verifikasi Gagal</h2>
                <p class="mb-4">{{ errorMessage }}</p>
                <router-link to="/login" class="btn btn-primary px-4 py-2">Kembali ke Login</router-link>
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
const loading = ref(false)
const errorMessage = ref('')
const success = ref(false)

onMounted(async () => {
  // If token is provided in route, verify email
  if (route.params.token) {
    await verifyEmail(route.params.token)
  } else {
    // Already verified, just show success
    success.value = true
    setTimeout(() => {
      router.push('/login')
    }, 4000)
  }
})

const verifyEmail = async (token) => {
  loading.value = true
  try {
    const response = await api.get(`/auth/verify-email/${token}`)
    
    if (response.data.success) {
      success.value = true
      setTimeout(() => {
        router.push('/login')
      }, 4000)
    } else {
      errorMessage.value = response.data.message || 'Token verifikasi tidak valid atau sudah kadaluarsa.'
      setTimeout(() => {
        router.push('/login?verify=failed')
      }, 3000)
    }
  } catch (error) {
    console.error('Verification error:', error)
    errorMessage.value = error.response?.data?.message || 'Token verifikasi tidak valid atau sudah kadaluarsa.'
    setTimeout(() => {
      router.push('/login?verify=failed')
    }, 3000)
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
