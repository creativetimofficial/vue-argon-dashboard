<template>
  <div>
    <h4 class="py-3 mb-4">
      <span class="text-muted fw-light">ISP Admin /</span> Paket Internet
    </h4>

    <!-- Add Package Button -->
    <div class="mb-4">
      <button class="btn btn-primary" @click="showAddModal = true">
        <i class="bx bx-plus me-1"></i>Tambah Paket
      </button>
    </div>

    <!-- Packages Grid -->
    <div class="row">
      <div v-for="pkg in packages" :key="pkg.id" class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div class="badge bg-label-primary">{{ pkg.speed }}</div>
              <div class="dropdown">
                <button class="btn p-0" type="button" data-bs-toggle="dropdown">
                  <i class="bx bx-dots-vertical-rounded"></i>
                </button>
                <div class="dropdown-menu">
                  <a class="dropdown-item" href="javascript:void(0);" @click="editPackage(pkg.id)">
                    <i class="bx bx-edit-alt me-1"></i> Edit
                  </a>
                  <a class="dropdown-item text-danger" href="javascript:void(0);" @click="deletePackage(pkg.id)">
                    <i class="bx bx-trash me-1"></i> Hapus
                  </a>
                </div>
              </div>
            </div>
            <h5 class="card-title">{{ pkg.name }}</h5>
            <p class="card-text text-muted">{{ pkg.description }}</p>
            <div class="mb-3">
              <h3 class="mb-0">Rp {{ formatNumber(pkg.price) }}</h3>
              <small class="text-muted">per bulan</small>
            </div>
            <ul class="list-unstyled mb-0">
              <li class="mb-2"><i class="bx bx-check text-success me-2"></i>{{ pkg.speed }} Mbps</li>
              <li class="mb-2"><i class="bx bx-check text-success me-2"></i>{{ pkg.quota }}</li>
              <li class="mb-2"><i class="bx bx-check text-success me-2"></i>{{ pkg.support }}</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="packages.length === 0" class="col-12">
        <div class="card">
          <div class="card-body text-center py-5">
            <i class="bx bx-package bx-lg mb-3 text-muted"></i>
            <p class="text-muted">Belum ada paket internet</p>
            <button class="btn btn-primary" @click="showAddModal = true">
              <i class="bx bx-plus me-1"></i>Tambah Paket Pertama
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const showAddModal = ref(false);

const packages = ref([
  // Empty for now - will be populated from API
]);

const formatNumber = (num) => {
  return new Intl.NumberFormat('id-ID').format(num);
};

const editPackage = (id) => {
  console.log('Edit package:', id);
};

const deletePackage = (id) => {
  console.log('Delete package:', id);
};
</script>
