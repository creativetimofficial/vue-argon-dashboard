<script setup>
import { onMounted, watch, ref } from 'vue'
import { useTheme } from 'vuetify'

const { global: globalTheme } = useTheme()
const route = useRoute()

const resolveTheme = () => {
  const saved = localStorage.getItem('isp_admin_theme') || 'light'
  
  if (saved === 'system') {
    const isDark = window.matchMedia('(prefers-color-scheme: dark)').matches
    globalTheme.name.value = isDark ? 'dark' : 'light'
  } else {
    globalTheme.name.value = saved
  }
}

onMounted(() => {
  // Only resolve if not on the landing page (which has its own logic)
  if (window.location.pathname !== '/') {
    resolveTheme()
  }
  
  // Listen for system theme changes if in 'system' mode
  const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
  mediaQuery.addEventListener('change', () => {
    if (localStorage.getItem('isp_admin_theme') === 'system') {
      resolveTheme()
    }
  })

  // Listen for manual theme changes from ThemeSwitcher
  window.addEventListener('storage', (e) => {
    if (e.key === 'isp_admin_theme') {
      resolveTheme()
    }
  })
})
</script>

<template>
  <v-app>
    <router-view />
  </v-app>
</template>

<style>
/* 
  Global Overrides to ensure Dark Mode works correctly everywhere.
*/

/* Force component backgrounds in dark mode */
.v-theme--dark .v-card,
.v-theme--dark .v-list,
.v-theme--dark .v-sheet,
.v-theme--dark .v-table,
.v-theme--dark .v-data-table {
  background-color: #2B2C40 !important;
  color: #E6E6F1 !important;
}

/* Ensure table headers also follow the theme */
.v-theme--dark .v-table .v-table__wrapper > table > thead > tr > th {
  background-color: #2B2C40 !important;
  color: #B6BEE3 !important;
}

/* Force dark background for the entire page */
.v-theme--dark.v-application,
.v-theme--dark .v-application__wrap {
  background-color: #232333 !important;
}

/* Ensure common text classes adapt to surface color */
.v-theme--dark .text-medium-emphasis {
  color: #B6BEE3 !important;
}

/* Fix for Search Dialog specifically */
.v-theme--dark .v-overlay__content .v-card {
  background-color: #2B2C40 !important;
  border: 1px solid rgba(230, 230, 241, 0.12) !important;
}
</style>
