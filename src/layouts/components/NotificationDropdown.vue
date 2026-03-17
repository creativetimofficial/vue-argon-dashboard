<script setup>
import { ref, onMounted } from 'vue'
import { useTheme } from 'vuetify'
import axios from 'axios'

const notifications = ref([])
const unreadCount = ref(0)
const loading = ref(true)

const fetchNotifications = async () => {
  try {
    const token = localStorage.getItem('isp_admin_token')
    if (!token) return

    const response = await axios.get('/api/isp-admin/dashboard-stats', {
      headers: { Authorization: `Bearer ${token}` }
    })
    
    // Construct real system notifications based on statistics
    const stats = response.data.stats || {}
    const alerts = []

    if (stats.overdue_invoices > 0) {
      alerts.push({
        title: 'Tagihan Jatuh Tempo',
        subtitle: `Terdapat ${stats.overdue_invoices} tagihan yang belum dibayar`,
        time: 'Hari Ini',
        color: 'error',
        icon: 'bx-error-circle'
      })
    }

    if (stats.pending_amount > 0) {
      alerts.push({
        title: 'Pembayaran Tertunda',
        subtitle: `Total Rp ${new Intl.NumberFormat('id-ID').format(stats.pending_amount)}`,
        time: 'Bulan Ini',
        color: 'warning',
        icon: 'bx-wallet'
      })
    }

    // Example informational alert
    alerts.push({
      title: 'Selamat Datang!',
      subtitle: 'Sistem billing beroperasi normal.',
      time: 'Baru saja',
      color: 'primary',
      icon: 'bx-info-circle'
    })

    notifications.value = alerts
    unreadCount.value = alerts.length
  } catch (err) {
    console.error('Failed to fetch notifications', err)
  } finally {
    loading.value = false
  }
}

const clearAllNotifications = () => {
  notifications.value = []
  unreadCount.value = 0
}

const removeNotification = (index) => {
  notifications.value.splice(index, 1)
  unreadCount.value = notifications.value.length
}

onMounted(() => {
  fetchNotifications()
})

const { name: themeName } = useTheme()
</script>

<template>
  <VMenu :theme="themeName" offset-y :close-on-content-click="false" max-width="380">
    <template #activator="{ props }">
      <IconBtn v-bind="props" class="me-2">
        <VBadge :model-value="unreadCount > 0" dot color="error" offset-x="3" offset-y="3">
          <VIcon icon="bx-bell" />
        </VBadge>
      </IconBtn>
    </template>

    <VCard elevation="10" width="380">
      <VCardTitle class="d-flex align-center justify-space-between py-3 px-4">
        <h6 class="text-h6 mb-0 font-weight-medium">Notifications</h6>
        <VChip size="small" color="primary" variant="tonal">{{ notifications.length }} New</VChip>
      </VCardTitle>
      
      <v-divider></v-divider>

      <VList lines="two" class="pa-0">
        <VListItem
          v-for="(notification, index) in notifications"
          :key="index"
          class="px-4 py-3"
        >
          <template #prepend>
            <VAvatar size="40" :color="'rgba(var(--v-theme-' + notification.color + '), 0.16)'" class="me-3">
              <span v-if="notification.initials" :class="'text-' + notification.color" class="font-weight-medium">{{ notification.initials }}</span>
              <VIcon v-else :color="notification.color" size="24">{{ notification.color === 'success' ? 'bx-user-plus' : 'bx-envelope' }}</VIcon>
            </VAvatar>
          </template>

          <VListItemTitle class="font-weight-medium mb-1">{{ notification.title }}</VListItemTitle>
          <VListItemSubtitle class="text-caption mb-1">{{ notification.subtitle }}</VListItemSubtitle>
          <div class="text-xs text-disabled">{{ notification.time }}</div>

          <template #append>
            <IconBtn size="small" @click.stop="removeNotification(index)">
              <VIcon icon="bx-x" size="20" />
            </IconBtn>
          </template>
        </VListItem>
        <v-divider v-if="notifications.length > 0" class="mt-0"></v-divider>
      </VList>

      <v-divider></v-divider>

      <div class="pa-4">
        <VBtn @click="clearAllNotifications" variant="tonal" color="error" block>
          Hapus Semua Notifikasi
        </VBtn>
      </div>
    </VCard>
  </VMenu>
</template>
