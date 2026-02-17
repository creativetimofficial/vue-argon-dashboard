<template>
  <div class="isp-landing-container">
    <div v-if="loading" class="d-flex justify-content-center align-items-center vh-100">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
    
    <component 
      v-else 
      :is="activeThemeComponent" 
      :isp-name="ispName" 
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed, defineAsyncComponent } from "vue";
import { getTenantInfo } from "@/utils/tenant";
import { authAPI } from "@/services/api";

// Async imports for better performance
const SneatLanding = defineAsyncComponent(() => import("@/components/landing-pages/SneatLanding.vue"));
const ModernLanding = defineAsyncComponent(() => import("@/components/landing-pages/ModernLanding.vue"));
const CreativeLanding = defineAsyncComponent(() => import("@/components/landing-pages/CreativeLanding.vue"));

const ispName = ref("");
const loading = ref(true);
const currentTemplate = ref("sneat");

const activeThemeComponent = computed(() => {
  switch (currentTemplate.value) {
    case "modern":
      return ModernLanding;
    case "creative":
      return CreativeLanding;
    case "sneat":
    default:
      return SneatLanding;
  }
});

onMounted(async () => {
    loading.value = true;
    try {
        let tenant = getTenantInfo();
        
        let themeData = null;
        
        // Always try to fetch fresh theme data to get the template
        try {
            const response = await authAPI.getPublicTheme(); // Use getPublicTheme as it handles domain resolution logic on backend usually, or we can use getTenantInfo if it returns theme
            if (response.data) {
                themeData = response.data;
                 // If the response is the theme directly
                if (themeData.landing_page_template) {
                     currentTemplate.value = themeData.landing_page_template;
                } 
                // If the response is wrapped or is tenant info containing theme
                else if (themeData.theme && themeData.theme.landing_page_template) {
                    currentTemplate.value = themeData.theme.landing_page_template;
                }
                
                // Set ISP Name
                if (themeData.company_name) {
                    ispName.value = themeData.company_name;
                } else if (themeData.isp && themeData.isp.name) {
                    ispName.value = themeData.isp.name;
                } else if (tenant && tenant.name) {
                     ispName.value = tenant.name;
                } else {
                    ispName.value = "ISP Name";
                }
            }
        } catch (e) {
            console.warn("Failed to fetch theme from API, using default/local", e);
             // Fallback to local
             if (tenant && tenant.name) {
                ispName.value = tenant.name || tenant.subdomain;
            } else {
                ispName.value = "ISP Name";
            }
        }

        document.title = ispName.value + " - Home";
        
    } catch (e) {
        console.error("Error in landing page init", e);
    } finally {
        loading.value = false;
    }
});
</script>

<style scoped>
.isp-landing-container {
  min-height: 100vh;
}
</style>
