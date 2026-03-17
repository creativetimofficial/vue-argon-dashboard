<template>
  <v-container fluid class="pa-6">
    <div class="d-flex align-center mb-6">
      <h4 class="text-h4 font-weight-bold mb-0">
        <span class="text-medium-emphasis font-weight-light">Infrastruktur /</span> VPN API
      </h4>
    </div>

    <v-row>
      <v-col cols="12" md="4">
        <v-card title="VPN API Credentials">
          <v-card-text>
            <p class="text-caption mb-4">Gunakan kredensial ini untuk mengintegrasikan layanan VPN dengan aplikasi pihak ketiga Anda.</p>
            
            <v-text-field 
              label="API Key" 
              v-model="apiKey"
              readonly 
              persistent-placeholder
              variant="outlined"
              append-inner-icon="bx bx-copy" 
              density="comfortable" 
              class="mb-4"
              @click:append-inner="copyToClipboard(apiKey, 'API Key')"
            ></v-text-field>

            <v-text-field 
              label="API Secret" 
              v-model="apiSecret"
              :type="showSecret ? 'text' : 'password'" 
              readonly 
              persistent-placeholder
              variant="outlined"
              :append-inner-icon="showSecret ? 'bx bx-hide' : 'bx bx-show'" 
              density="comfortable"
              @click:append-inner="showSecret = !showSecret"
            ></v-text-field>

            <v-btn 
              color="primary" 
              block 
              class="mt-6" 
              prepend-icon="bx bx-refresh"
              :loading="regenerating"
              @click="regenerateKeys"
            >
              Regenerate Key
            </v-btn>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" md="8">
        <v-card title="API Usage Logs">
          <v-table hover>
            <thead>
              <tr>
                <th class="text-left font-weight-bold">Timestamp</th>
                <th class="text-left font-weight-bold">Endpoint</th>
                <th class="text-left font-weight-bold">Method</th>
                <th class="text-left font-weight-bold">Status</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="logs.length === 0">
                <td colspan="4" class="text-center py-4 text-medium-emphasis">Belum ada aktivitas API</td>
              </tr>
              <tr v-for="(log, index) in logs" :key="index">
                <td>{{ log.timestamp }}</td>
                <td><code>{{ log.endpoint }}</code></td>
                <td><span class="font-weight-bold" :class="getMethodColor(log.method)">{{ log.method }}</span></td>
                <td>
                  <v-chip size="x-small" :color="log.status === 200 ? 'success' : 'error'" variant="tonal">
                    {{ log.status }} {{ log.statusText }}
                  </v-chip>
                </td>
              </tr>
            </tbody>
          </v-table>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref } from 'vue';
import Swal from 'sweetalert2';

const apiKey = ref('');
const apiSecret = ref('');
const showSecret = ref(false);
const regenerating = ref(false);

const logs = ref([
  { timestamp: '2024-03-01 14:20:11', endpoint: '/vpn/user/create', method: 'POST', status: 200, statusText: 'OK' },
  { timestamp: '2024-03-01 13:05:45', endpoint: '/vpn/server/status', method: 'GET', status: 200, statusText: 'OK' },
  { timestamp: '2024-03-01 10:15:22', endpoint: '/vpn/user/delete', method: 'DELETE', status: 404, statusText: 'Not Found' }
]);

const copyToClipboard = (text, label) => {
  navigator.clipboard.writeText(text);
  Swal.fire({
    icon: 'success',
    title: 'Copied!',
    text: `${label} telah disalin ke clipboard.`,
    timer: 1500,
    showConfirmButton: false,
    toast: true,
    position: 'top-end'
  });
};

const regenerateKeys = () => {
  Swal.fire({
    title: 'Regenerate API Key?',
    text: 'Aplikasi yang menggunakan key lama tidak akan bisa terhubung lagi.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, Ganti Baru',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      regenerating.value = true;
      setTimeout(() => {
        apiKey.value = 'sk_live_' + Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
        apiSecret.value = 'sec_live_' + Math.random().toString(36).substring(2, 15);
        regenerating.value = false;
        
        Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: 'Kredensial baru telah digenerate.',
          timer: 2000,
          showConfirmButton: false
        });
      }, 1000);
    }
  });
};

const getMethodColor = (method) => {
  const colors = {
    'GET': 'text-info',
    'POST': 'text-success',
    'PUT': 'text-warning',
    'DELETE': 'text-error'
  };
  return colors[method] || '';
};
</script>

<style scoped>
code {
  background-color: rgba(var(--v-theme-on-surface), 0.05);
  padding: 2px 4px;
  border-radius: 4px;
  font-size: 0.85rem;
}
</style>
