<template>
  <v-container fluid class="pa-6">
    <div class="d-flex align-center mb-6">
      <h4 class="text-h4 font-weight-bold mb-0">
        <span class="text-medium-emphasis font-weight-light">Layanan /</span> Portal Pelanggan
      </h4>
    </div>

    <v-row>
      <v-col cols="12" md="12">
        <v-card title="Management Portal Pelanggan Self-Service">
          <v-card-text>
             <v-alert type="success" variant="tonal" class="mb-6">
               URL Portal Pelanggan Anda: <strong>https://client.isp-name.com</strong>
             </v-alert>
             
             <v-row>
               <v-col cols="12" md="4" v-for="feature in portalFeatures" :key="feature.name">
                 <v-card border flat>
                   <v-card-text class="d-flex align-center">
                     <v-checkbox-btn v-model="feature.enabled" color="primary"></v-checkbox-btn>
                     <div class="ms-2">
                       <div class="font-weight-bold">{{ feature.name }}</div>
                       <div class="text-caption text-medium-emphasis">{{ feature.desc }}</div>
                     </div>
                   </v-card-text>
                 </v-card>
               </v-col>
             </v-row>

             <v-btn color="primary" class="mt-6">Simpan Pengaturan Portal</v-btn>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref } from 'vue';

const portalFeatures = ref([
  { name: 'Self-Topup', desc: 'Pelanggan bisa isi saldo mandiri', enabled: true },
  { name: 'Self-Upgrade', desc: 'Upgrade paket otomatis', enabled: true },
  { name: 'Support Ticket', desc: 'Buka tiket dari portal', enabled: true },
  { name: 'E-Invoice', desc: 'Download invoice PDF', enabled: true },
  { name: 'Usage History', desc: 'Lihat riwayat pemakaian BW', enabled: false },
]);
</script>
