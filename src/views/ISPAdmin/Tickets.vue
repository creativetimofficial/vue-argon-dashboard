<template>
  <div>
    <h4 class="py-3 mb-4">
      <span class="text-muted fw-light">ISP Admin /</span> Tiket Support
    </h4>

    <!-- Add Ticket Button -->
    <div class="mb-4">
      <button class="btn btn-primary">
        <i class="bx bx-plus me-1"></i>Buat Tiket
      </button>
    </div>

    <!-- Tickets Table -->
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Daftar Tiket</h5>
      </div>
      <div class="table-responsive text-nowrap">
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Pelanggan</th>
              <th>Subjek</th>
              <th>Prioritas</th>
              <th>Status</th>
              <th>Tanggal</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="tickets.length === 0">
              <td colspan="7" class="text-center py-4">
                <i class="bx bx-support bx-lg mb-2 text-muted"></i>
                <p class="text-muted">Belum ada tiket support</p>
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

const tickets = ref([]);
</script>
