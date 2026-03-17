<template>
  <v-container fluid class="pa-6">
    <div class="d-flex align-center mb-6">
      <h4 class="text-h4 font-weight-bold mb-0">
        <span class="text-medium-emphasis font-weight-light">ISP Admin /</span> Reports
      </h4>
    </div>

    <!-- Date Range Filter -->
    <v-card class="mb-6">
      <v-card-text>
        <v-row align="end">
          <v-col cols="12" md="3">
            <v-text-field label="From Date" type="date" v-model="dateFrom" variant="outlined" density="comfortable" hide-details></v-text-field>
          </v-col>
          <v-col cols="12" md="3">
            <v-text-field label="To Date" type="date" v-model="dateTo" variant="outlined" density="comfortable" hide-details></v-text-field>
          </v-col>
          <v-col cols="12" md="3">
            <v-select
              label="Jenis Reports"
              v-model="reportType"
              :items="[
                { title: 'Revenue', value: 'revenue' },
                { title: 'Customers', value: 'customers' },
                { title: 'Invoice', value: 'invoices' }
              ]"
              variant="outlined"
              density="comfortable"
              hide-details
            ></v-select>
          </v-col>
          <v-col cols="12" md="3">
            <v-btn color="primary" block height="48" prepend-icon="bx bx-search" @click="generateReport">
              Generate
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <!-- Report Cards -->
    <v-row>
      <v-col cols="12" md="4" class="mb-4">
        <v-card elevation="2">
          <v-card-text>
            <div class="d-flex align-center mb-2">
              <v-avatar color="primary" variant="tonal" size="40" class="mr-3">
                <v-icon color="primary">bx bx-wallet</v-icon>
              </v-avatar>
              <span class="text-subtitle-2 font-weight-medium">Total Revenue</span>
            </div>
            <h3 class="text-h4 font-weight-bold mb-1">Rp {{ formatNumber(stats.revenue) }}</h3>
            <div class="text-caption text-medium-emphasis">This period</div>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" md="4" class="mb-4">
        <v-card elevation="2">
          <v-card-text>
            <div class="d-flex align-center mb-2">
              <v-avatar color="success" variant="tonal" size="40" class="mr-3">
                <v-icon color="success">bx bx-user-plus</v-icon>
              </v-avatar>
              <span class="text-subtitle-2 font-weight-medium">Customers Baru</span>
            </div>
            <h3 class="text-h4 font-weight-bold mb-1">{{ stats.newCustomers }}</h3>
            <div class="text-caption text-medium-emphasis">This period</div>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" md="4" class="mb-4">
        <v-card elevation="2">
          <v-card-text>
            <div class="d-flex align-center mb-2">
              <v-avatar color="info" variant="tonal" size="40" class="mr-3">
                <v-icon color="info">bx bx-file</v-icon>
              </v-avatar>
              <span class="text-subtitle-2 font-weight-medium">Paid Invoices</span>
            </div>
            <h3 class="text-h4 font-weight-bold mb-1">{{ stats.paidInvoices }}</h3>
            <div class="text-caption text-medium-emphasis">This period</div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Chart Placeholder -->
    <v-card >
      <v-card-title class="pa-4 d-flex justify-space-between align-center">
        <span class="text-h6">Grafik Revenue</span>
        <v-btn variant="outlined" color="primary" size="small" prepend-icon="bx bx-download">
          Export PDF
        </v-btn>
      </v-card-title>
      <v-divider></v-divider>
      <v-card-text class="py-10 text-center">
        <v-icon size="64" color="medium-emphasis" class="mb-4">bx bx-bar-chart-alt-2</v-icon>
        <p class="text-medium-emphasis mb-0">Chart will be displayed here once data is gathered</p>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref } from 'vue';

const dateFrom = ref('');
const dateTo = ref('');
const reportType = ref('revenue');

const stats = ref({
  revenue: 0,
  newCustomers: 0,
  paidInvoices: 0
});

const generateReport = () => {
  // Report generation logic
  console.log('Generating report:', reportType.value, 'from', dateFrom.value, 'to', dateTo.value);
};

const formatNumber = (num) => {
  return new Intl.NumberFormat('en-US').format(num);
};
</script>
