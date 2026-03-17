<script setup>
import { ref, watch } from 'vue'
import { useTheme } from 'vuetify'
import axios from 'axios'

const { name: themeName } = useTheme()
const isDialogVisible = ref(false)
const searchQuery = ref('')
const searchResults = ref([])
const loading = ref(false)
let searchTimeout = null

const performSearch = async (query) => {
  if (!query || query.length < 2) {
    searchResults.value = []
    return
  }
  
  loading.value = true
  try {
    const token = localStorage.getItem('isp_admin_token')
    const response = await axios.get('/api/isp-admin/search', {
      params: { q: query },
      headers: { Authorization: `Bearer ${token}` }
    })
    searchResults.value = response.data.results || []
  } catch (error) {
    console.error('Search failed', error)
  } finally {
    loading.value = false
  }
}

watch(searchQuery, (newVal) => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    performSearch(newVal)
  }, 300)
})

watch(isDialogVisible, (newVal) => {
  if (!newVal) {
    searchQuery.value = ''
    searchResults.value = []
  }
})
</script>

<template>
  <div class="d-flex align-center cursor-pointer ms-lg-n3" @click="isDialogVisible = true">
    <IconBtn>
      <VIcon icon="bx-search" />
    </IconBtn>

    <span class="d-none d-md-flex align-center text-disabled ms-2" style="user-select: none;">
      <span class="me-2">Search</span>
      <span class="meta-key">&#8984;K</span>
    </span>
  </div>

  <VDialog
    v-model="isDialogVisible"
    max-width="600"
    :theme="themeName"
  >
    <VCard class="pa-4">
      <VTextField
        v-model="searchQuery"
        autofocus
        placeholder="Cari pelanggan, tagihan..."
        prepend-inner-icon="bx-search"
        append-inner-icon="bx-x"
        @click:append-inner="isDialogVisible = false"
        variant="underlined"
        hide-details
        class="mb-4"
        :loading="loading"
      />
      <VCardText class="px-0 pt-2 pb-0">
        <div v-if="searchResults.length === 0 && searchQuery.length > 1 && !loading" class="text-caption text-center text-disabled my-4">
          Tidak ada hasil untuk "{{ searchQuery }}"
        </div>
        
        <div v-else-if="searchResults.length > 0" class="text-caption text-disabled mb-3">HASIL PENCARIAN</div>
        <div v-else class="text-caption text-disabled mb-3">CONTOH PENCARIAN</div>
        
        <VList lines="one" class="pa-0">
          <!-- Default Examples if no search -->
          <template v-if="searchResults.length === 0 && searchQuery.length < 2">
            <VListItem to="/isp-admin/customers" class="mb-1 rounded" @click="isDialogVisible = false">
              <template #prepend><VIcon icon="bx-user" class="me-3" /></template>
              <VListItemTitle>Semua Pelanggan</VListItemTitle>
            </VListItem>
            <VListItem to="/isp-admin/invoices" class="mb-1 rounded" @click="isDialogVisible = false">
              <template #prepend><VIcon icon="bx-receipt" class="me-3" /></template>
              <VListItemTitle>Tagihan Jatuh Tempo</VListItemTitle>
            </VListItem>
          </template>

          <!-- Live Results from DB -->
          <template v-else>
            <VListItem
              v-for="(item, i) in searchResults"
              :key="i"
              :to="item.to"
              class="mb-1 rounded"
              @click="isDialogVisible = false"
            >
              <template #prepend><VIcon :icon="item.icon" class="me-3 text-primary" /></template>
              <VListItemTitle>{{ item.title }}</VListItemTitle>
              <VListItemSubtitle>{{ item.subtitle }}</VListItemSubtitle>
            </VListItem>
          </template>
        </VList>
      </VCardText>
    </VCard>
  </VDialog>
</template>

<style scoped>
.meta-key {
  border: thin solid rgba(var(--v-border-color), var(--v-border-opacity));
  border-radius: 6px;
  block-size: 1.5625rem;
  line-height: 1.3125rem;
  padding-block: 0.125rem;
  padding-inline: 0.25rem;
}
</style>
