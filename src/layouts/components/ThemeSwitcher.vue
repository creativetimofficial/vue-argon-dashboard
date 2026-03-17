<script setup>
import { useTheme } from 'vuetify'
import { watch, onMounted } from 'vue'

const themes = [
  { name: 'light', icon: 'bx-sun', label: 'Terang' },
  { name: 'dark', icon: 'bx-moon', label: 'Gelap' },
  { name: 'system', icon: 'bx-desktop', label: 'Otomatis' },
]

const { global: globalTheme } = useTheme()

const currentThemeIndex = ref(0)
const currentTheme = computed(() => themes[currentThemeIndex.value])

onMounted(() => {
  const saved = localStorage.getItem('isp_admin_theme') || 'light'
  const idx = themes.findIndex(t => t.name === saved)
  currentThemeIndex.value = idx !== -1 ? idx : 0
})

const changeTheme = () => {
  currentThemeIndex.value = (currentThemeIndex.value + 1) % themes.length
  const newTheme = themes[currentThemeIndex.value].name
  
  localStorage.setItem('isp_admin_theme', newTheme)
  
  // Trigger global change
  if (newTheme !== 'system') {
    globalTheme.name.value = newTheme
  } else {
    // If system, AppISP.vue will handle the logic
    window.dispatchEvent(new Event('storage'))
  }
}

// Sync if changed elsewhere
watch(() => globalTheme.name.value, val => {
  const idx = themes.findIndex(t => t.name === val)
  if (idx !== -1 && themes[idx].name !== 'system') {
     currentThemeIndex.value = idx
  }
})
</script>

<script>
// Need this because we use ref/computed in setup but want them available
import { ref, computed } from 'vue'
export default {
  name: 'ThemeSwitcher'
}
</script>

<template>
  <v-btn icon variant="text" color="secondary" @click="changeTheme">
    <v-icon :icon="currentTheme.icon" />
    <v-tooltip activator="parent" location="bottom">
      <span class="text-capitalize">{{ currentTheme.label }}</span>
    </v-tooltip>
  </v-btn>
</template>
