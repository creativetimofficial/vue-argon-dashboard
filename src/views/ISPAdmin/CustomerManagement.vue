<template>
  <div>
    <h4 class="py-3 mb-4">
      <span class="text-muted fw-light">ISP Admin /</span> Managemen Pelanggan
    </h4>

    <!-- Filter and Search -->
    <div class="card mb-4">
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <input
              type="text"
              class="form-control"
              placeholder="Cari pelanggan..."
              v-model="searchQuery"
            />
          </div>
          <div class="col-md-3">
            <select class="form-select" v-model="filterStatus">
              <option value="">Semua Status</option>
              <option value="active">Aktif</option>
              <option value="inactive">Tidak Aktif</option>
              <option value="suspended">Suspended</option>
            </select>
          </div>
          <div class="col-md-3">
            <select class="form-select" v-model="filterPackage">
              <option value="">Semua Paket</option>
              <option value="10mbps">10 Mbps</option>
              <option value="20mbps">20 Mbps</option>
              <option value="50mbps">50 Mbps</option>
            </select>
          </div>
          <div class="col-md-2">
            <button class="btn btn-primary w-100" @click="addCustomer">
              <i class="bx bx-plus me-1"></i>Tambah
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Customers Table -->
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Daftar Pelanggan</h5>
      </div>
      <div class="table-responsive text-nowrap">
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nama</th>
              <th>Email</th>
              <th>Paket</th>
              <th>Status</th>
              <th>Tagihan Terakhir</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            <tr v-if="customers.length === 0">
              <td colspan="7" class="text-center py-4">
                <div class="text-muted">
                  <i class="bx bx-info-circle bx-lg mb-2"></i>
                  <p>Belum ada data pelanggan</p>
                  <button class="btn btn-sm btn-primary" @click="addCustomer">
                    <i class="bx bx-plus me-1"></i>Tambah Pelanggan Pertama
                  </button>
                </div>
              </td>
            </tr>
            <tr v-for="customer in filteredCustomers" :key="customer.id">
              <td>{{ customer.id }}</td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar avatar-sm me-3">
                    <span class="avatar-initial rounded-circle bg-label-primary">
                      {{ customer.name.charAt(0) }}
                    </span>
                  </div>
                  <div>
                    <strong>{{ customer.name }}</strong>
                  </div>
                </div>
              </td>
              <td>{{ customer.email }}</td>
              <td>{{ customer.package }}</td>
              <td>
                <span
                  class="badge"
                  :class="{
                    'bg-label-success': customer.status === 'active',
                    'bg-label-danger': customer.status === 'inactive',
                    'bg-label-warning': customer.status === 'suspended'
                  }"
                >
                  {{ getStatusLabel(customer.status) }}
                </span>
              </td>
              <td>{{ customer.lastBill }}</td>
              <td>
                <div class="dropdown">
                  <button
                    type="button"
                    class="btn p-0 dropdown-toggle hide-arrow"
                    data-bs-toggle="dropdown"
                  >
                    <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="javascript:void(0);" @click="viewCustomer(customer.id)">
                      <i class="bx bx-show me-1"></i> Lihat Detail
                    </a>
                    <a class="dropdown-item" href="javascript:void(0);" @click="editCustomer(customer.id)">
                      <i class="bx bx-edit-alt me-1"></i> Edit
                    </a>
                    <a class="dropdown-item text-danger" href="javascript:void(0);" @click="deleteCustomer(customer.id)">
                      <i class="bx bx-trash me-1"></i> Hapus
                    </a>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
          <small class="text-muted">Menampilkan {{ customers.length }} pelanggan</small>
          <nav aria-label="Page navigation">
            <ul class="pagination mb-0">
              <li class="page-item disabled">
                <a class="page-link" href="javascript:void(0);">Previous</a>
              </li>
              <li class="page-item active">
                <a class="page-link" href="javascript:void(0);">1</a>
              </li>
              <li class="page-item">
                <a class="page-link" href="javascript:void(0);">2</a>
              </li>
              <li class="page-item">
                <a class="page-link" href="javascript:void(0);">3</a>
              </li>
              <li class="page-item">
                <a class="page-link" href="javascript:void(0);">Next</a>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

// Search and filter
const searchQuery = ref('');
const filterStatus = ref('');
const filterPackage = ref('');

// Mock data - replace with actual API calls
const customers = ref([
  // Empty for now - will be populated from API
]);

const filteredCustomers = computed(() => {
  return customers.value.filter(customer => {
    const matchesSearch = customer.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         customer.email.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesStatus = !filterStatus.value || customer.status === filterStatus.value;
    const matchesPackage = !filterPackage.value || customer.package === filterPackage.value;
    
    return matchesSearch && matchesStatus && matchesPackage;
  });
});

const getStatusLabel = (status) => {
  const labels = {
    active: 'Aktif',
    inactive: 'Tidak Aktif',
    suspended: 'Suspended'
  };
  return labels[status] || status;
};

const addCustomer = () => {
  // TODO: Open add customer modal or navigate to add customer page
  console.log('Add customer');
};

const viewCustomer = (id) => {
  router.push(`/isp-admin/customers/${id}`);
};

const editCustomer = (id) => {
  // TODO: Open edit customer modal
  console.log('Edit customer:', id);
};

const deleteCustomer = (id) => {
  // TODO: Show confirmation dialog and delete
  console.log('Delete customer:', id);
};

// TODO: Fetch customers from API
// onMounted(async () => {
//   const response = await fetch('/api/isp-admin/customers');
//   customers.value = await response.json();
// });
</script>

<style scoped>
/* Additional custom styles if needed */
</style>
