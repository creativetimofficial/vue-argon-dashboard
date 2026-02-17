<template>
  <div>
    <h4 class="py-3 mb-4">
      <span class="text-muted fw-light">ISP Admin /</span> Laporan
    </h4>

    <!-- Date Range Filter -->
    <div class="card mb-4">
      <div class="card-body">
        <div class="row">
          <div class="col-md-3">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" class="form-control" v-model="dateFrom" />
          </div>
          <div class="col-md-3">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" class="form-control" v-model="dateTo" />
          </div>
          <div class="col-md-3">
            <label class="form-label">Jenis Laporan</label>
            <select class="form-select" v-model="reportType">
              <option value="revenue">Pendapatan</option>
              <option value="customers">Pelanggan</option>
              <option value="invoices">Invoice</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">&nbsp;</label>
            <button class="btn btn-primary w-100">
              <i class="bx bx-search me-1"></i>Generate
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Report Cards -->
    <div class="row">
      <div class="col-md-4 mb-4">
        <div class="card">
          <div class="card-body">
            <h6 class="card-title">Total Pendapatan</h6>
            <h3 class="mb-0">Rp 0</h3>
            <small class="text-muted">Periode ini</small>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="card">
          <div class="card-body">
            <h6 class="card-title">Pelanggan Baru</h6>
            <h3 class="mb-0">0</h3>
            <small class="text-muted">Periode ini</small>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="card">
          <div class="card-body">
            <h6 class="card-title">Invoice Lunas</h6>
            <h3 class="mb-0">0</h3>
            <small class="text-muted">Periode ini</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Chart Placeholder -->
    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <h5 class="card-title mb-0">Grafik Pendapatan</h5>
        <button class="btn btn-sm btn-outline-primary">
          <i class="bx bx-download me-1"></i>Export PDF
        </button>
      </div>
      <div class="card-body">
        <div class="text-center py-5">
          <i class="bx bx-bar-chart-alt-2 bx-lg mb-2 text-muted"></i>
          <p class="text-muted">Grafik akan ditampilkan di sini</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const dateFrom = ref('');
const dateTo = ref('');
const reportType = ref('revenue');
</script>
