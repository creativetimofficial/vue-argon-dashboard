<template>
  <div>
    <h4 class="py-3 mb-4">
      <span class="text-muted fw-light">ISP Admin /</span> Managemen Mikrotik
    </h4>

    <!-- Add Router Button -->
    <div class="mb-4">
      <button class="btn btn-primary">
        <i class="bx bx-plus me-1"></i>Tambah Router
      </button>
    </div>

    <!-- Routers Table -->
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Daftar Router Mikrotik</h5>
      </div>
      <div class="table-responsive text-nowrap">
        <table class="table">
          <thead>
            <tr>
              <th>Nama Router</th>
              <th>IP Address</th>
              <th>Lokasi</th>
              <th>Status</th>
              <th>Kapasitas</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="routers.length === 0">
              <td colspan="6" class="text-center py-4">
                <i class="bx bx-server bx-lg mb-2 text-muted"></i>
                <p class="text-muted">Belum ada router terdaftar</p>
              </td>
            </tr>
            <tr v-for="router in routers" :key="router.id">
              <td>{{ router.name }}</td>
              <td><code>{{ router.ip }}</code></td>
              <td>{{ router.location }}</td>
              <td>
                <span class="badge" :class="router.status === 'online' ? 'bg-label-success' : 'bg-label-danger'">
                  {{ router.status === 'online' ? 'Online' : 'Offline' }}
                </span>
              </td>
              <td>{{ router.currentUsers }}/{{ router.maxUsers }}</td>
              <td>
                <div class="dropdown">
                  <button class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                    <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="javascript:void(0);">
                      <i class="bx bx-show me-1"></i> Detail
                    </a>
                    <a class="dropdown-item" href="javascript:void(0);">
                      <i class="bx bx-edit-alt me-1"></i> Edit
                    </a>
                    <a class="dropdown-item text-danger" href="javascript:void(0);">
                      <i class="bx bx-trash me-1"></i> Hapus
                    </a>
                  </div>
                </div>
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

const routers = ref([
  // Empty for now - will be populated from API
]);
</script>
