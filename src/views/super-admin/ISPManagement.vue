<template>
  <div class="py-4 container-fluid">
    <!-- Stats Cards -->
    <div class="row mb-4">
      <div class="col-lg-3 col-md-6">
        <div class="card">
          <div class="card-body p-3">
            <div class="d-flex justify-content-between">
              <div>
                <p class="text-sm mb-0 text-uppercase font-weight-bold">
                  Total ISPs
                </p>
                <h5 class="font-weight-bolder mb-0">{{ stats.total }}</h5>
              </div>
              <div
                class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md"
              >
                <i
                  class="ni ni-building text-lg opacity-10"
                  aria-hidden="true"
                ></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="card">
          <div class="card-body p-3">
            <div class="d-flex justify-content-between">
              <div>
                <p class="text-sm mb-0 text-uppercase font-weight-bold">
                  Pending Approval
                </p>
                <h5 class="font-weight-bolder mb-0 text-warning">
                  {{ stats.pending }}
                </h5>
              </div>
              <div
                class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md"
              >
                <i
                  class="ni ni-time-alarm text-lg opacity-10"
                  aria-hidden="true"
                ></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="card">
          <div class="card-body p-3">
            <div class="d-flex justify-content-between">
              <div>
                <p class="text-sm mb-0 text-uppercase font-weight-bold">
                  Active ISPs
                </p>
                <h5 class="font-weight-bolder mb-0 text-success">
                  {{ stats.active }}
                </h5>
              </div>
              <div
                class="icon icon-shape bg-gradient-success shadow text-center border-radius-md"
              >
                <i
                  class="ni ni-check-bold text-lg opacity-10"
                  aria-hidden="true"
                ></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="card">
          <div class="card-body p-3">
            <div class="d-flex justify-content-between">
              <div>
                <p class="text-sm mb-0 text-uppercase font-weight-bold">
                  Monthly Revenue
                </p>
                <h5 class="font-weight-bolder mb-0">
                  Rp {{ formatCurrency(stats.revenue) }}
                </h5>
              </div>
              <div
                class="icon icon-shape bg-gradient-info shadow text-center border-radius-md"
              >
                <i
                  class="ni ni-money-coins text-lg opacity-10"
                  aria-hidden="true"
                ></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ISP List -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center">
              <h6>ISP Companies Management</h6>
              <div>
                <button class="btn btn-primary btn-sm me-3" @click="openAddISP">Add ISP</button>
                <button
                  class="btn btn-outline-primary btn-sm me-2"
                  @click="filterStatus = 'all'"
                  :class="filterStatus === 'all' ? 'active' : ''"
                >
                  All
                </button>
                <button
                  class="btn btn-outline-warning btn-sm me-2"
                  @click="filterStatus = 'pending'"
                  :class="filterStatus === 'pending' ? 'active' : ''"
                >
                  Pending
                </button>
                <button
                  class="btn btn-outline-success btn-sm"
                  @click="filterStatus = 'approved'"
                  :class="filterStatus === 'approved' ? 'active' : ''"
                >
                  Approved
                </button>
              </div>
            </div>
          </div>
          <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th
                      class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                    >
                      ISP Company
                    </th>
                    <th
                      class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"
                    >
                      Subscription
                    </th>
                    <th
                      class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"
                    >
                      Usage
                    </th>
                    <th
                      class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                    >
                      Status
                    </th>
                    <th
                      class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                    >
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="loading">
                    <td colspan="5" class="text-center py-4">
                      <i class="fas fa-spinner fa-spin"></i> Loading...
                    </td>
                  </tr>
                  <tr v-else-if="filteredISPs.length === 0">
                    <td colspan="5" class="text-center py-4">
                      Tidak ada data ISP
                    </td>
                  </tr>
                  <tr v-else v-for="isp in filteredISPs" :key="isp.id">
                    <td>
                      <div class="d-flex px-3 py-1 flex-column">
                        <h6 class="mb-0 text-sm">{{ isp.name }}</h6>
                        <p class="text-xs text-secondary mb-0">{{ isp.email }}</p>
                        <p class="text-xs text-secondary mb-0">{{ isp.phone }}</p>
                      </div>
                    </td>
                    <td>
                      <p class="text-sm font-weight-bold mb-0">{{ isp.package_name }}</p>
                      <p class="text-xs text-secondary mb-0">Valid until: {{ isp.subscription_end_date }}</p>
                      <span :class="getSubscriptionStatusClass(isp.subscription_status)">{{ isp.subscription_status }}</span>
                    </td>
                    <td>
                      <p class="text-xs mb-0"><strong>Customers:</strong> {{ isp.current_customers }}/{{ isp.max_customers === -1 ? "∞" : isp.max_customers }}</p>
                      <p class="text-xs mb-0"><strong>Users:</strong> {{ isp.current_users }}/{{ isp.max_users }}</p>
                      <p class="text-xs mb-0"><strong>Locations:</strong> {{ isp.current_locations }}/{{ isp.max_locations }}</p>
                    </td>
                    <td class="align-middle text-center">
                      <span :class="getApprovalStatusClass(isp.approval_status)">{{ isp.approval_status }}</span>
                    </td>
                    <td class="align-middle text-center">
                      <button class="btn btn-info btn-sm mb-0 me-1" @click="viewDetails(isp)"><i class="fas fa-eye"></i> View</button>
                      <button class="btn btn-dark btn-sm mb-0 me-1" @click="openEditISP(isp)"><i class="fas fa-pencil-alt"></i> Edit</button>
                      <button class="btn btn-danger btn-sm mb-0" @click="deleteISP(isp)"><i class="fas fa-trash"></i> Delete</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ISP Details Modal -->
    <div
      v-if="showDetailsModal"
      class="modal fade show d-block"
      style="background: rgba(0, 0, 0, 0.5)"
    >
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">ISP Details - {{ selectedISP.name }}</h5>
            <button
              type="button"
              class="btn-close"
              @click="showDetailsModal = false"
            ></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Company Name</label>
                <p>{{ selectedISP.name }}</p>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Email</label>
                <p>{{ selectedISP.email }}</p>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Phone</label>
                <p>{{ selectedISP.phone }}</p>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Address</label>
                <p>{{ selectedISP.address }}</p>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Subscription Package</label>
                <p>{{ selectedISP.package_name }}</p>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Registration Date</label>
                <p>{{ selectedISP.created_at }}</p>
              </div>
            </div>
            <div class="mt-4">
              <h6 class="mb-3">Riwayat Langganan & Pesanan</h6>
              <div class="table-responsive">
                <table class="table table-sm align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Service/Package</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Price</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Cycle</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Expires</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="!selectedISP.orders || selectedISP.orders.length === 0">
                      <td colspan="5" class="text-center text-secondary text-xs py-3">No subscriptions found</td>
                    </tr>
                    <tr v-else v-for="order in selectedISP.orders" :key="order.id">
                      <td>
                        <div class="d-flex flex-column px-2">
                          <h6 class="mb-0 text-xs">{{ order.service_name || order.subscription_package?.name || 'Unknown' }}</h6>
                          <span class="text-secondary text-xxs">{{ order.reference }}</span>
                        </div>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0">Rp {{ formatCurrency(order.price) }}</p>
                      </td>
                       <td>
                        <span class="text-xs text-secondary">{{ order.billing_cycle || 'N/A' }}</span>
                      </td>
                      <td>
                        <span :class="getSubscriptionStatusClass(order.status)">{{ order.status }}</span>
                      </td>
                      <td>
                        <span class="text-xs font-weight-bold">{{ order.expired_date }}</span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              @click="showDetailsModal = false"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Add/Edit ISP -->
    <div v-if="showISPModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.3);">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editMode ? 'Edit ISP' : 'Add ISP' }}</h5>
            <button type="button" class="btn-close" @click="showISPModal = false"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveISP">
              <div class="mb-3">
                <label class="form-label">Company Name</label>
                <input v-model="ispForm.name" type="text" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input v-model="ispForm.email" type="email" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Phone</label>
                <input v-model="ispForm.phone" type="text" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Address</label>
                <input v-model="ispForm.address" type="text" class="form-control" required />
              </div>
              <div class="text-end">
                <button type="button" class="btn btn-secondary me-2" @click="showISPModal = false">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import axios from "axios";

const API_URL = "http://localhost:8000/api";
const loading = ref(false);

const stats = ref({
  total: 0,
  pending: 0,
  active: 0,
  approved: 0,
});

const filterStatus = ref("all");
const showDetailsModal = ref(false);
const selectedISP = ref({});
const packages = ref([]);
const isps = ref([]);

// Tambah state/modal untuk CRUD ISP
const showISPModal = ref(false);
const ispForm = ref({
  id: null,
  name: '',
  email: '',
  phone: '',
  address: '',
});
const editMode = ref(false);

const filteredISPs = computed(() => {
  if (filterStatus.value === "all") return isps.value;
  return isps.value.filter((isp) => isp.approval_status === filterStatus.value);
});

const formatCurrency = (value) => {
  return new Intl.NumberFormat("id-ID").format(value);
};

const getSubscriptionStatusClass = (status) => {
  const classes = {
    active: "badge badge-sm bg-gradient-success",
    trial: "badge badge-sm bg-gradient-info",
    suspended: "badge badge-sm bg-gradient-warning",
    expired: "badge badge-sm bg-gradient-danger",
  };
  return classes[status] || "badge badge-sm bg-gradient-secondary";
};

const getApprovalStatusClass = (status) => {
  const classes = {
    approved: "badge badge-sm bg-gradient-success",
    pending: "badge badge-sm bg-gradient-warning",
    rejected: "badge badge-sm bg-gradient-danger",
  };
  return classes[status] || "badge badge-sm bg-gradient-secondary";
};

const fetchStats = async () => {
  try {
    const token = localStorage.getItem("auth_token") || sessionStorage.getItem("auth_token");
    if (!token) {
      stats.value = { total: 0, pending: 0, active: 0, approved: 0, revenue: 0 };
      return;
    }
    const response = await axios.get(
      `${API_URL}/super-admin/isp-management/stats`,
      {
        headers: { Authorization: `Bearer ${token}` },
      },
    );
    if (response.data && typeof response.data === 'object') {
      stats.value = {
        total: response.data.total ?? 0,
        pending: response.data.pending ?? 0,
        active: response.data.active ?? 0,
        approved: response.data.approved ?? 0,
        revenue: response.data.revenue ?? 0,
      };
    } else {
      stats.value = { total: 0, pending: 0, active: 0, approved: 0, revenue: 0 };
    }
  } catch (error) {
    stats.value = { total: 0, pending: 0, active: 0, approved: 0, revenue: 0 };
    console.error("Error fetching stats:", error);
    // Don't alert here, will be handled by fetchISPs
  }
};

const fetchISPs = async () => {
  try {
    loading.value = true;
    const token = localStorage.getItem("auth_token") || sessionStorage.getItem("auth_token");
    if (!token) {
      alert("Please login first");
      window.location.href = "/";
      return;
    }
    const params =
      filterStatus.value !== "all"
        ? { approval_status: filterStatus.value }
        : {};
    const response = await axios.get(`${API_URL}/super-admin/isp-management`, {
      headers: { Authorization: `Bearer ${token}` },
      params,
    });
    // Check if response is array
    const data = Array.isArray(response.data) ? response.data : [];
    isps.value = data.map((isp) => ({
      id: isp.id,
      name: isp.company_name,
      email: isp.email,
      phone: isp.phone,
      address: isp.address,
      package_name: isp.subscription_package?.name || "N/A",
      subscription_status: isp.subscription_status,
      subscription_end_date: isp.subscription_end_date,
      current_customers: isp.current_customers_count,
      max_customers: isp.subscription_package?.max_customers || 0,
      current_users: isp.current_users_count,
      max_users: isp.subscription_package?.max_users || 0,
      current_locations: isp.current_locations_count,
      max_locations: isp.subscription_package?.max_locations || 0,
      approval_status: isp.approval_status,
      created_at: isp.created_at,
      subscription_package_id: isp.subscription_package_id,
    }));
  } catch (error) {
    console.error("Error fetching ISPs:", error);
    if (error.response?.status === 401 || error.response?.status === 403) {
      alert("Session expired. Please login again.");
      localStorage.removeItem("auth_token");
      window.location.href = "/";
    } else {
      alert(
        "Failed to load ISPs: " +
          (error.response?.data?.message || error.message),
      );
    }
  } finally {
    loading.value = false;
  }
};

const fetchPackages = async () => {
  try {
    const token = localStorage.getItem("auth_token") || sessionStorage.getItem("auth_token");
    if (!token) {
      return; // Silent fail, will redirect from fetchISPs
    }
    const response = await axios.get(
      `${API_URL}/super-admin/subscription-packages`,
      {
        headers: { Authorization: `Bearer ${token}` },
      },
    );
    packages.value = Array.isArray(response.data) ? response.data : [];
  } catch (error) {
    console.error("Error fetching packages:", error);
    // Don't alert here, will be handled by fetchISPs
  }
};

const approveISP = async (isp) => {
  if (confirm(`Approve ISP: ${isp.name}?`)) {
    try {
      const token = localStorage.getItem("auth_token") || sessionStorage.getItem("auth_token");
      await axios.post(
        `${API_URL}/super-admin/isp-management/${isp.id}/approve`,
        {},
        {
          headers: { Authorization: `Bearer ${token}` },
        },
      );
      alert(`ISP ${isp.name} has been approved!`);
      await fetchISPs();
      await fetchStats();
    } catch (error) {
      console.error("Error approving ISP:", error);
      alert("Failed to approve ISP");
    }
  }
};

const rejectISP = async (isp) => {
  const reason = prompt(`Rejection reason for ${isp.name}:`);
  if (reason) {
    try {
      const token = localStorage.getItem("auth_token") || sessionStorage.getItem("auth_token");
      await axios.post(
        `${API_URL}/super-admin/isp-management/${isp.id}/reject`,
        {
          rejection_reason: reason,
        },
        {
          headers: { Authorization: `Bearer ${token}` },
        },
      );
      alert(`ISP ${isp.name} has been rejected.`);
      await fetchISPs();
      await fetchStats();
    } catch (error) {
      console.error("Error rejecting ISP:", error);
      alert("Failed to reject ISP");
    }
  }
};

const viewDetails = async (isp) => {
  try {
    const token = localStorage.getItem("auth_token") || sessionStorage.getItem("auth_token");
    const response = await axios.get(`${API_URL}/super-admin/isp-management/${isp.id}`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    selectedISP.value = response.data;
    showDetailsModal.value = true;
  } catch (error) {
    console.error("Error fetching ISP details:", error);
    alert("Failed to load details");
  }
};

const updateSubscription = async () => {
  try {
    const token = localStorage.getItem("auth_token") || sessionStorage.getItem("auth_token");
    await axios.post(
      `${API_URL}/super-admin/isp-management/${selectedISP.value.id}/update-subscription`,
      {
        subscription_package_id: selectedISP.value.subscription_package_id,
      },
      {
        headers: { Authorization: `Bearer ${token}` },
      },
    );
    alert("Subscription updated successfully!");
    showDetailsModal.value = false;
    await fetchISPs();
  } catch (error) {
    console.error("Error updating subscription:", error);
    alert("Failed to update subscription");
  }
};

function openAddISP() {
  ispForm.value = { id: null, name: '', email: '', phone: '', address: '' };
  editMode.value = false;
  showISPModal.value = true;
}
function openEditISP(isp) {
  ispForm.value = {
    id: isp.id,
    name: isp.name,
    email: isp.email,
    phone: isp.phone,
    address: isp.address,
  };
  editMode.value = true;
  showISPModal.value = true;
}
async function saveISP() {
  const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
  try {
    if (editMode.value) {
      await axios.put(`${API_URL}/super-admin/isp-management/${ispForm.value.id}`, {
        company_name: ispForm.value.name,
        email: ispForm.value.email,
        phone: ispForm.value.phone,
        address: ispForm.value.address,
      }, {
        headers: { Authorization: `Bearer ${token}` },
      });
      alert('ISP updated successfully!');
    } else {
      await axios.post(`${API_URL}/super-admin/isp-management`, {
        company_name: ispForm.value.name,
        email: ispForm.value.email,
        phone: ispForm.value.phone,
        address: ispForm.value.address,
      }, {
        headers: { Authorization: `Bearer ${token}` },
      });
      alert('ISP created successfully!');
    }
    showISPModal.value = false;
    await fetchISPs();
    await fetchStats();
  } catch (error) {
    alert('Failed to save ISP: ' + (error.response?.data?.message || error.message));
  }
}
async function deleteISP(isp) {
  if (!confirm(`Yakin ingin menghapus ISP: ${isp.name}?`)) return;
  const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
  try {
    await axios.delete(`${API_URL}/super-admin/isp-management/${isp.id}`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    alert('ISP deleted successfully!');
    await fetchISPs();
    await fetchStats();
  } catch (error) {
    alert('Failed to delete ISP: ' + (error.response?.data?.message || error.message));
  }
}

onMounted(async () => {
  await Promise.all([fetchStats(), fetchISPs(), fetchPackages()]);
});
</script>

<style scoped>
.modal.show {
  display: block;
}
</style>
