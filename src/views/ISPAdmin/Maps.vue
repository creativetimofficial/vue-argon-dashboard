<template>
  <v-container fluid class="pa-6">
    <div class="d-flex align-center mb-6">
      <h4 class="text-h4 font-weight-bold mb-0">
        <span class="text-medium-emphasis font-weight-light">ISP Admin /</span> Peta Pelanggan
      </h4>
    </div>

    <!-- Filter Cards -->
    <v-card  class="mb-6">
      <v-card-text class="d-flex flex-wrap align-center">
        <div class="text-subtitle-1 font-weight-medium mr-4">Filter Peringatan:</div>
        
        <v-checkbox
          v-model="filters.active"
          hide-details
          color="success"
          density="compact"
          class="mr-4"
          @update:modelValue="renderMarkers"
        >
          <template v-slot:label>
            <div class="text-success font-weight-bold d-flex align-center">
              <v-icon size="small" class="mr-1">bx bxs-circle</v-icon> Aktif ({{ summary.active }})
            </div>
          </template>
        </v-checkbox>
        
        <v-checkbox
          v-model="filters.suspended"
          hide-details
          color="warning"
          density="compact"
          class="mr-4"
          @update:modelValue="renderMarkers"
        >
          <template v-slot:label>
            <div class="text-warning font-weight-bold d-flex align-center">
              <v-icon size="small" class="mr-1">bx bxs-circle</v-icon> Suspended ({{ summary.suspended }})
            </div>
          </template>
        </v-checkbox>

        <v-checkbox
          v-model="filters.terminated"
          hide-details
          color="error"
          density="compact"
          @update:modelValue="renderMarkers"
        >
          <template v-slot:label>
            <div class="text-error font-weight-bold d-flex align-center">
              <v-icon size="small" class="mr-1">bx bxs-circle</v-icon> Terminated ({{ summary.terminated }})
            </div>
          </template>
        </v-checkbox>

        <v-spacer></v-spacer>
        
        <v-btn  color="primary" @click="fetchCustomers" size="small" prepend-icon="bx bx-refresh">
          Refresh Data
        </v-btn>
      </v-card-text>
    </v-card>

    <!-- Map Container -->
    <v-card  class="position-relative">
      <div id="map" style="height: 600px; width: 100%; border-radius: inherit;"></div>
      
      <v-overlay :model-value="loading" contained class="align-center justify-center">
        <v-card class="pa-4 text-center d-flex flex-column align-center">
          <v-progress-circular indeterminate color="primary" class="mb-2"></v-progress-circular>
          <div>Memuat Peta...</div>
        </v-card>
      </v-overlay>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import { ispAdminAPI } from '@/services/api';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';
import Swal from 'sweetalert2';

const map = ref(null);
const markersGroup = ref(null);
const loading = ref(true);
const customersData = ref([]);

const summary = ref({
   active: 0,
   suspended: 0,
   terminated: 0
});

const filters = ref({
   active: true,
   suspended: true,
   terminated: true
});

// Custom Node Icons using pure CSS/HTML markers to emulate Google Maps style pins
const createCustomIcon = (color) => {
   const svgIcon = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="36" height="36" fill="${color}"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>`;
   
   return L.divIcon({
      className: 'custom-div-icon',
      html: `<div style="margin-top:-36px; margin-left:-18px;">${svgIcon}</div>`,
      iconSize: [36, 36],
      iconAnchor: [18, 36],
      popupAnchor: [0, -36]
   });
};

const icons = {
   active: createCustomIcon('#71dd37'), // Success (Sneat)
   suspended: createCustomIcon('#ffab00'), // Warning (Sneat)
   terminated: createCustomIcon('#ff3e1d') // Danger (Sneat)
};

const initMap = async () => {
    // Prevent re-initialization
    if (map.value !== null) return;
    
    // Default center (Indonesia roughly) if no customers
    map.value = L.map('map').setView([-2.5489, 118.0149], 5);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map.value);

    markersGroup.value = L.layerGroup().addTo(map.value);
};

const fetchCustomers = async () => {
   loading.value = true;
   try {
      const response = await ispAdminAPI.getCustomersMap();
      
      // Defensive check: Ensure response.data is an array
      // If the API is wrapped in { data: [...] }, handle it.
      const rawData = response.data?.data || response.data || [];
      customersData.value = Array.isArray(rawData) ? rawData : [];
      
      // Calculate Summary
      summary.value = {
         active: customersData.value.filter(c => c.status === 'active').length,
         suspended: customersData.value.filter(c => c.status === 'suspended').length,
         terminated: customersData.value.filter(c => c.status === 'terminated').length
      };
      
      renderMarkers();
   } catch (error) {
       console.error("Gagal memuat data pelanggan", error);
       Swal.fire({
          title: 'Error!',
          text: 'Gagal memuat data pelanggan pada peta.',
          icon: 'error',
          confirmButtonText: 'OK'
       });
   } finally {
      loading.value = false;
   }
};

const renderMarkers = () => {
   if (!markersGroup.value || !map.value) return;
   
   // Clear existing markers
   markersGroup.value.clearLayers();
   
   let bounds = L.latLngBounds();
   let hasValidCoords = false;

   const data = Array.isArray(customersData.value) ? customersData.value : [];
   data.forEach(customer => {
      // Check filtering
      if (!filters.value[customer.status]) return;
      
      if (customer.latitude && customer.longitude) {
         const lat = parseFloat(customer.latitude);
         const lng = parseFloat(customer.longitude);
         
         if (isNaN(lat) || isNaN(lng)) return;

         const icon = icons[customer.status] || icons['terminated'];
         const marker = L.marker([lat, lng], { icon });
         
         // Build Popup Content
         const popupContent = `
            <div style="min-width: 200px;">
               <h6 style="margin: 0; font-weight: bold; color: #696cff;">${customer.name}</h6>
               <p style="margin: 5px 0 10px; font-size: 12px; color: #666;">${customer.customer_code || 'No Code'}</p>
               
               <table style="width: 100%; font-size: 12px; margin-bottom: 15px;">
                  <tr>
                     <td style="color: #999;">Status:</td>
                     <td style="text-align: right; font-weight: bold; color: ${customer.status === 'active' ? '#71dd37' : customer.status === 'suspended' ? '#ffab00' : '#ff3e1d'}">
                        ${customer.status.toUpperCase()}
                     </td>
                  </tr>
                  <tr>
                     <td style="color: #999;">Layanan:</td>
                     <td style="text-align: right;">${customer.service_plan?.name || 'Belum Ada'}</td>
                  </tr>
               </table>
               
               <div style="text-align: center;">
                  <button type="button" class="btn btn-xs btn-primary text-white" style="padding: 2px 8px; font-size: 11px; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; background-color: #696cff;" onclick="window.dispatchEvent(new CustomEvent('nav-customers'))">Lihat Profil</button>
               </div>
            </div>
         `;
         
         marker.bindPopup(popupContent);
         markersGroup.value.addLayer(marker);
         
         bounds.extend([lat, lng]);
         hasValidCoords = true;
      }
   });
   
   // Auto-fit bounds if we have points
   if (hasValidCoords) {
      map.value.fitBounds(bounds, { padding: [50, 50], maxZoom: 16 });
   }
};

onMounted(async () => {
   await nextTick();
   await initMap();
   fetchCustomers();
   window.addEventListener('nav-customers', navigateToCustomers);
});

onUnmounted(() => {
   window.removeEventListener('nav-customers', navigateToCustomers);
   if (map.value) {
      map.value.remove();
      map.value = null;
   }
});

import { useRouter } from 'vue-router';
const router = useRouter();
const navigateToCustomers = () => {
   router.push('/isp-admin/customers');
};
</script>

<style scoped>
/* Ensure custom icon is positioned correctly and overrides any default Leaflet borders */
:deep(.custom-div-icon) {
   background: transparent;
   border: none;
}
:deep(.leaflet-popup-content-wrapper) {
   border-radius: 0.375rem;
   box-shadow: 0 0.25rem 1rem rgba(161, 172, 184, 0.45);
}
:deep(.leaflet-popup-content) {
   margin: 15px;
}
</style>
