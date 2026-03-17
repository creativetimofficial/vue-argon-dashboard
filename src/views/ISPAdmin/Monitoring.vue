<template>
  <v-container fluid class="pa-6">
    <div class="d-flex align-center mb-6">
      <h4 class="text-h4 font-weight-bold mb-0">
        <span class="text-medium-emphasis font-weight-light">Infrastruktur /</span> Monitoring Realtime
      </h4>
      <v-spacer></v-spacer>
      <v-chip color="success" prepend-icon="bx bx-pulse" class="font-weight-bold">
        Live Status: Online
      </v-chip>
    </div>

    <!-- Real-time Stats -->
    <v-row class="mb-4">
      <v-col cols="12" sm="6" md="3" v-for="stat in stats" :key="stat.title">
        <v-card elevation="2" border>
          <v-card-text class="d-flex align-center">
            <v-avatar :color="stat.color" rounded size="42" variant="tonal" class="me-3">
              <v-icon size="24">{{ stat.icon }}</v-icon>
            </v-avatar>
            <div>
              <div class="text-caption text-medium-emphasis">{{ stat.title }}</div>
              <div class="text-h6 font-weight-bold">{{ stat.value }}</div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <v-row>
      <!-- Traffic Charts -->
      <v-col cols="12" md="8">
        <v-row>
          <v-col cols="12">
            <v-card border>
              <v-card-item>
                <v-card-title>
                  <div class="d-flex align-center">
                    <v-icon color="primary" class="me-2">bx bx-down-arrow-circle</v-icon>
                    Traffic Download (Realtime)
                    <v-spacer></v-spacer>
                    <span class="text-primary text-h6">{{ currentDown }} Mbps</span>
                  </div>
                </v-card-title>
              </v-card-item>
              <v-card-text>
                <div class="chart-container" style="height: 250px;">
                  <canvas id="downChart"></canvas>
                </div>
              </v-card-text>
            </v-card>
          </v-col>
          
          <v-col cols="12">
            <v-card border>
              <v-card-item>
                <v-card-title>
                  <div class="d-flex align-center">
                    <v-icon color="success" class="me-2">bx bx-up-arrow-circle</v-icon>
                    Traffic Upload (Realtime)
                    <v-spacer></v-spacer>
                    <span class="text-success text-h6">{{ currentUp }} Mbps</span>
                  </div>
                </v-card-title>
              </v-card-item>
              <v-card-text>
                <div class="chart-container" style="height: 250px;">
                  <canvas id="upChart"></canvas>
                </div>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-col>

      <!-- Connection Status & Users -->
      <v-col cols="12" md="4">
        <v-card border class="mb-6">
          <v-card-item title="System Health">
            <template v-slot:append>
              <v-btn icon="bx bx-dots-vertical-rounded" variant="text" density="comfortable"></v-btn>
            </template>
          </v-card-item>
          <v-card-text>
            <div class="mb-4">
              <div class="d-flex justify-space-between mb-1">
                <span class="text-body-2">CPU Usage</span>
                <span class="text-body-2 font-weight-bold">12%</span>
              </div>
              <v-progress-linear color="info" model-value="12" rounded height="6"></v-progress-linear>
            </div>
            <div class="mb-4">
              <div class="d-flex justify-space-between mb-1">
                <span class="text-body-2">Memory Usage</span>
                <span class="text-body-2 font-weight-bold">45%</span>
              </div>
              <v-progress-linear color="primary" model-value="45" rounded height="6"></v-progress-linear>
            </div>
             <div class="mb-0">
              <div class="d-flex justify-space-between mb-1">
                <span class="text-body-2">Temperature</span>
                <span class="text-body-2 font-weight-bold">42°C</span>
              </div>
              <v-progress-linear color="warning" model-value="42" rounded height="6"></v-progress-linear>
            </div>
          </v-card-text>
        </v-card>

        <v-card border title="Pelanggan Online">
          <v-list density="compact">
            <v-list-item v-for="user in onlineUsers" :key="user.name" :subtitle="user.ip">
              <template v-slot:prepend>
                <v-badge dot color="success" offset-x="3" offset-y="3">
                  <v-avatar size="36" color="primary" variant="tonal">
                    <span class="text-primary text-caption font-weight-bold">{{ user.name.charAt(0) }}</span>
                  </v-avatar>
                </v-badge>
              </template>
              <v-list-item-title class="font-weight-bold">{{ user.name }}</v-list-item-title>
              <template v-slot:append>
                <v-chip size="x-small" variant="tonal" color="primary">{{ user.type }}</v-chip>
              </template>
            </v-list-item>
          </v-list>
          <v-divider></v-divider>
          <v-card-actions class="justify-center pa-2">
            <v-btn variant="text" color="primary" size="small" block>Lihat Semua Koneksi</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import Chart from 'chart.js/auto';

const stats = ref([
  { title: 'Total BW Usage', value: '450 Mbps', icon: 'bx bx-trending-up', color: 'primary' },
  { title: 'Active Connections', value: '1,240', icon: 'bx bx-plug', color: 'success' },
  { title: 'CPU Load', value: '12%', icon: 'bx bx-chip', color: 'info' },
  { title: 'Uptime', value: '14 Hari', icon: 'bx bx-time', color: 'warning' },
]);

const onlineUsers = ref([
  { name: 'Budiman Santoso', ip: '10.20.30.45', type: 'PPPoE' },
  { name: 'Siti Aminah', ip: '10.20.30.12', type: 'Hotspot' },
  { name: 'Joko Widodo', ip: '10.20.30.88', type: 'PPPoE' },
  { name: 'Ani Yudhoyono', ip: '10.20.30.101', type: 'PPPoE' },
]);

const currentDown = ref(450.2);
const currentUp = ref(125.5);

let downChart = null;
let upChart = null;
let simulatorInterval = null;

const createChart = (id, label, color, initialData) => {
  const ctx = document.getElementById(id).getContext('2d');
  
  // Create gradient
  const gradient = ctx.createLinearGradient(0, 0, 0, 250);
  gradient.addColorStop(0, `rgba(${color}, 0.2)`);
  gradient.addColorStop(1, `rgba(${color}, 0)`);

  return new Chart(ctx, {
    type: 'line',
    data: {
      labels: Array(20).fill(''),
      datasets: [{
        label,
        data: initialData,
        borderColor: `rgb(${color})`,
        backgroundColor: gradient,
        fill: true,
        tension: 0.4, // Smooth curves
        pointRadius: 0,
        borderWidth: 2
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false }
      },
      scales: {
        x: { display: false },
        y: {
          beginAtZero: true,
          grid: { color: 'rgba(0,0,0,0.05)' },
          ticks: { color: '#a1acb8', font: { size: 10 } }
        }
      }
    }
  });
};

onMounted(() => {
  // Initialize charts with some fake history
  const downHistory = Array.from({length: 20}, () => Math.floor(Math.random() * 200) + 300);
  const upHistory = Array.from({length: 20}, () => Math.floor(Math.random() * 50) + 100);

  downChart = createChart('downChart', 'Download', '105, 108, 255', downHistory);
  upChart = createChart('upChart', 'Upload', '113, 221, 55', upHistory);

  // Simulator
  simulatorInterval = setInterval(() => {
    // Simulate data change
    currentDown.value = parseFloat((currentDown.value + (Math.random() * 20 - 10)).toFixed(1));
    currentUp.value = parseFloat((currentUp.value + (Math.random() * 10 - 5)).toFixed(1));
    
    // Bounds check
    if (currentDown.value < 100) currentDown.value = 150;
    if (currentUp.value < 50) currentUp.value = 80;

    // Update Download Chart
    downChart.data.datasets[0].data.shift();
    downChart.data.datasets[0].data.push(currentDown.value);
    downChart.update('none'); // Update without animation for performance

    // Update Upload Chart
    upChart.data.datasets[0].data.shift();
    upChart.data.datasets[0].data.push(currentUp.value);
    upChart.update('none');
  }, 1000);
});

onBeforeUnmount(() => {
  if (simulatorInterval) clearInterval(simulatorInterval);
  if (downChart) downChart.destroy();
  if (upChart) upChart.destroy();
});
</script>

<style scoped>
.chart-container {
  position: relative;
  width: 100%;
}
</style>
