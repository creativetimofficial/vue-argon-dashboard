<template>
  <div>
    <h4 class="py-3 mb-4">
      <span class="text-muted fw-light">ISP Admin /</span> Invoice
    </h4>

    <!-- Filter -->
    <div class="card mb-4">
      <div class="card-body">
        <div class="row">
          <div class="col-md-3">
            <select class="form-select" v-model="filterStatus">
              <option value="">Semua Status</option>
              <option value="paid">Lunas</option>
              <option value="pending">Pending</option>
              <option value="overdue">Jatuh Tempo</option>
            </select>
          </div>
          <div class="col-md-3">
            <input type="month" class="form-control" v-model="filterMonth" />
          </div>
        </div>
      </div>
    </div>

    <!-- Invoices Table -->
    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <h5 class="card-title mb-0">Daftar Invoice</h5>
        <button class="btn btn-sm btn-primary">
          <i class="bx bx-download me-1"></i>Export
        </button>
      </div>
      <div class="table-responsive text-nowrap">
        <table class="table">
          <thead>
            <tr>
              <th>No. Invoice</th>
              <th>Pelanggan</th>
              <th>Tanggal</th>
              <th>Jatuh Tempo</th>
              <th>Total</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="invoices.length === 0">
              <td colspan="7" class="text-center py-4">
                <i class="bx bx-file bx-lg mb-2 text-muted"></i>
                <p class="text-muted">Belum ada invoice</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const filterStatus = ref('');
const filterMonth = ref('');
const invoices = ref([]);
</script>
