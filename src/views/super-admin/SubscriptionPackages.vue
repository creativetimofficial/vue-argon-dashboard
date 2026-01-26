<template>
  <div class="py-4 container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div
            class="card-header d-flex justify-content-between align-items-center"
          >
            <div>
              <h5 class="mb-0">Manajemen Paket Berlangganan</h5>
              <p class="text-sm mb-0">Kelola paket berlangganan untuk ISP</p>
            </div>
            <button class="btn btn-primary btn-sm" @click="showAddModal = true">
              <i class="fas fa-plus me-2"></i>Tambah Paket
            </button>
          </div>

          <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th
                      class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                    >
                      Paket
                    </th>
                    <th
                      class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"
                    >
                      Harga
                    </th>
                    <th
                      class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"
                    >
                      Batasan
                    </th>
                    <th
                      class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                    >
                      Status
                    </th>
                    <th
                      class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                    >
                      Aksi
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="packages.length === 0">
                    <td colspan="5" class="text-center text-secondary py-4">
                      Tidak ada paket berlangganan yang tersedia.
                    </td>
                  </tr>
                  <tr v-for="pkg in packages" :key="pkg.id">
                    <td>
                      <div class="d-flex px-3 py-1 flex-column">
                        <h6 class="mb-0 text-sm">{{ pkg.name }}</h6>
                        <p class="text-xs text-secondary mb-0">
                          {{ pkg.description }}
                        </p>
                        <span
                          v-if="pkg.is_popular"
                          class="badge badge-sm bg-gradient-warning mt-1"
                          style="width: fit-content"
                          >Populer</span
                        >
                      </div>
                    </td>
                    <td>
                      <p class="text-sm font-weight-bold mb-0">
                        Rp {{ formatCurrency(pkg.price_monthly) }}/bulan
                      </p>
                      <p
                        class="text-xs text-secondary mb-0"
                        v-if="pkg.price_yearly"
                      >
                        Rp {{ formatCurrency(pkg.price_yearly) }}/tahun ({{
                          pkg.discount_yearly_percent
                        }}% diskon)
                      </p>
                    </td>
                    <td>
                      <p class="text-xs mb-0">
                        <strong>Pelanggan:</strong>
                        {{
                          pkg.max_customers === -1
                            ? "Tak terbatas"
                            : pkg.max_customers
                        }}
                      </p>
                      <p class="text-xs mb-0">
                        <strong>Pengguna:</strong> {{ pkg.max_users }}
                      </p>
                      <p class="text-xs mb-0">
                        <strong>Lokasi:</strong> {{ pkg.max_locations }}
                      </p>
                    </td>
                    <td class="align-middle text-center">
                      <span
                        :class="
                          pkg.is_active
                            ? 'badge badge-sm bg-gradient-success'
                            : 'badge badge-sm bg-gradient-secondary'
                        "
                      >
                        {{ pkg.is_active ? "Aktif" : "Nonaktif" }}
                      </span>
                    </td>
                    <td class="align-middle text-center">
                      <button
                        class="btn btn-link text-secondary mb-0"
                        @click="editPackage(pkg)"
                      >
                        <i class="fas fa-edit text-xs"></i>
                      </button>
                      <button
                        class="btn btn-link text-secondary mb-0"
                        @click="togglePackage(pkg)"
                      >
                        <i
                          :class="pkg.is_active ? 'fas fa-toggle-on text-success' : 'fas fa-toggle-off text-secondary'"
                          style="font-size: 18px"
                        ></i>
                      </button>
                      <button
                        class="btn btn-link text-danger mb-0"
                        @click="deletePackage(pkg.id)"
                      >
                        <i class="fas fa-trash text-xs"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal Section -->
    <div v-if="showAddModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.3);">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Tambah Paket Berlangganan</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <div class="modal-body" style="overflow-x: auto; max-width: 100vw;">
            <form @submit.prevent="savePackage">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Nama Paket *</label>
                  <input v-model="form.name" type="text" class="form-control" required />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Slug *</label>
                  <input v-model="form.slug" type="text" class="form-control" required />
                  <small class="text-muted">Nama URL-friendly (misal: starter, profesional)</small>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea v-model="form.description" class="form-control" rows="2"></textarea>
              </div>
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label class="form-label">Harga Bulanan (Rp) *</label>
                  <input v-model.number="form.price_monthly" type="number" step="0.01" class="form-control" required />
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">Harga Tahunan (Rp)</label>
                  <input v-model.number="form.price_yearly" type="number" step="0.01" class="form-control" />
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">Diskon Tahunan (%)</label>
                  <input v-model.number="form.discount_yearly_percent" type="number" min="0" max="100" class="form-control" />
                </div>
              </div>
              <h6 class="mt-3 mb-2">Batasan Paket</h6>
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label class="form-label">Maksimal Pelanggan</label>
                  <input v-model.number="form.max_customers" type="number" class="form-control" />
                  <small class="text-muted">-1 untuk tak terbatas</small>
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">Maksimal Pengguna/Staff</label>
                  <input v-model.number="form.max_users" type="number" class="form-control" />
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">Maksimal Lokasi</label>
                  <input v-model.number="form.max_locations" type="number" class="form-control" />
                </div>
              </div>
              <h6 class="mt-3 mb-2">Fitur & Izin (Utama)</h6>
              <div class="row">
                <div class="col-md-6 mb-2">
                  <div class="form-check">
                    <input v-model="form.feature_free_subdomain" class="form-check-input" type="checkbox" id="freeSubdomain" />
                    <label class="form-check-label" for="freeSubdomain">Free Subdomain</label>
                  </div>
                </div>
                <div class="col-md-6 mb-2">
                  <div class="form-check">
                    <input v-model="form.feature_custom_domain" class="form-check-input" type="checkbox" id="customDomain" />
                    <label class="form-check-label" for="customDomain">Bisa Pakai Domain Sendiri</label>
                  </div>
                </div>
                <div class="col-md-6 mb-2">
                  <div class="form-check">
                    <input v-model="form.feature_maps_interaktif" class="form-check-input" type="checkbox" id="mapsInteraktif" />
                    <label class="form-check-label" for="mapsInteraktif">Maps Interaktif</label>
                  </div>
                </div>
                <div class="col-md-6 mb-2">
                  <div class="form-check">
                    <input v-model="form.feature_dynamic_forwarding" class="form-check-input" type="checkbox" id="dynamicForwarding" />
                    <label class="form-check-label" for="dynamicForwarding">Dynamic Forwarding Remote ONT</label>
                  </div>
                </div>
                <div class="col-md-6 mb-2">
                  <div class="form-check">
                    <input v-model="form.feature_vpn_api" class="form-check-input" type="checkbox" id="vpnApi" />
                    <label class="form-check-label" for="vpnApi">Free VPN API</label>
                  </div>
                </div>
                <div class="col-md-6 mb-2">
                  <div class="form-check">
                    <input v-model="form.feature_payment_gateways" class="form-check-input" type="checkbox" id="paymentGateways" />
                    <label class="form-check-label" for="paymentGateways">Payment Gateway (Tripay & Duitku)</label>
                  </div>
                </div>
              </div>

              <h6 class="mt-3 mb-2">Fitur Tambahan</h6>
              <div class="row">
                <div class="col-md-6 mb-2">
                  <div class="form-check">
                    <input v-model="form.feature_custom_landing_page" class="form-check-input" type="checkbox" id="customLandingPage" />
                    <label class="form-check-label" for="customLandingPage">Free Custom Landing Page</label>
                  </div>
                </div>
                <div class="col-md-6 mb-2">
                  <div class="form-check">
                    <input v-model="form.feature_realtime_monitoring" class="form-check-input" type="checkbox" id="realtimeMonitoring" />
                    <label class="form-check-label" for="realtimeMonitoring">Monitoring Realtime</label>
                  </div>
                </div>
                <div class="col-md-6 mb-2">
                  <div class="form-check">
                    <input v-model="form.feature_radius" class="form-check-input" type="checkbox" id="radius" />
                    <label class="form-check-label" for="radius">Radius</label>
                  </div>
                </div>
                <div class="col-md-6 mb-2">
                  <div class="form-check">
                    <input v-model="form.feature_acs" class="form-check-input" type="checkbox" id="acs" />
                    <label class="form-check-label" for="acs">ACS</label>
                  </div>
                </div>
                <div class="col-md-6 mb-2">
                  <div class="form-check">
                    <input v-model="form.feature_customer_portal" class="form-check-input" type="checkbox" id="customerPortal" />
                    <label class="form-check-label" for="customerPortal">Portal Pelanggan</label>
                  </div>
                </div>
                <div class="col-md-6 mb-2">
                  <div class="form-check">
                    <input v-model="form.feature_android_app" class="form-check-input" type="checkbox" id="androidApp" />
                    <label class="form-check-label" for="androidApp">Android App</label>
                  </div>
                </div>
              </div>

              <h6 class="mt-3 mb-2">Administrasi</h6>
              <div class="row">
                <div class="col-md-6 mb-2">
                  <div class="form-check">
                    <input v-model="form.feature_automated_billing" class="form-check-input" type="checkbox" id="automatedBilling" />
                    <label class="form-check-label" for="automatedBilling">Pencatatan Invoice (Otomatis)</label>
                  </div>
                </div>
                <div class="col-md-6 mb-2">
                  <div class="form-check">
                    <input v-model="form.feature_whitelabel" class="form-check-input" type="checkbox" id="whiteLabel" />
                    <label class="form-check-label" for="whiteLabel">White Label</label>
                  </div>
                </div>
              </div>
              <div class="mb-3 mt-3">
                <label class="form-label">Daftar Fitur (satu per baris)</label>
                <textarea v-model="form.features_text" class="form-control" rows="5" placeholder="Analitik lanjutan&#10;Laporan kustom&#10;Dukungan 24/7"></textarea>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <div class="form-check form-switch">
                    <input v-model="form.is_active" class="form-check-input" type="checkbox" id="isActive" />
                    <label class="form-check-label" for="isActive">Paket Aktif</label>
                  </div>
                </div>
                <div class="col-md-6 mb-3">
                  <div class="form-check form-switch">
                    <input v-model="form.is_popular" class="form-check-input" type="checkbox" id="isPopular" />
                    <label class="form-check-label" for="isPopular">Tandai Populer</label>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeModal">Batal</button>
            <button type="button" class="btn btn-primary" @click="savePackage"><i class="fas fa-save me-2"></i>Simpan</button>
          </div>
        </div>
      </div>
    </div>
  <!-- End main container -->
</template>

<script setup>
import { ref, reactive, onMounted } from "vue";
import axios from "axios";

const API_URL = "http://localhost:8000/api";
const packages = ref([]);
const loading = ref(false);

const showAddModal = ref(false);
const editMode = ref(false);
const form = reactive({
  name: "",
  slug: "",
  description: "",
  price_monthly: 0,
  price_yearly: 0,
  discount_yearly_percent: 0,
  max_customers: 100,
  max_users: 5,
  max_locations: 1,
  feature_whitelabel: false,
  feature_api_access: false,
  feature_priority_support: false,
  feature_analytics: false,
  feature_multi_currency: false,
  feature_automated_billing: false,
  feature_free_subdomain: false,
  feature_custom_domain: false,
  feature_maps_interaktif: false,
  feature_dynamic_forwarding: false,
  feature_vpn_api: false,
  feature_payment_gateways: false,
  feature_custom_landing_page: false,
  feature_realtime_monitoring: false,
  feature_radius: false,
  feature_acs: false,
  feature_customer_portal: false,
  feature_android_app: false,
  features_text: "",
  is_active: true,
  is_popular: false,
});

const formatCurrency = (value) => {
  return new Intl.NumberFormat("id-ID").format(value);
};

const editPackage = (pkg) => {
  editMode.value = true;
  showAddModal.value = true;
  Object.assign(form, pkg);
  form.features_text = pkg.features ? pkg.features.join("\n") : "";
};

const fetchPackages = async () => {
  try {
    loading.value = true;
    const token = localStorage.getItem("auth_token");
    if (!token) {
      alert("Please login first");
      window.location.href = "/";
      return;
    }
    const response = await axios.get(
      `${API_URL}/super-admin/subscription-packages`,
      {
        headers: { Authorization: `Bearer ${token}` },
      },
    );
    console.log("Fetched packages:", response.data);
    packages.value = Array.isArray(response.data) ? response.data : [];
  } catch (error) {
    console.error("Error fetching packages:", error);
    if (error.response?.status === 401 || error.response?.status === 403) {
      alert("Session expired. Please login again.");
      localStorage.removeItem("auth_token");
      window.location.href = "/";
    } else {
      alert(
        "Failed to load packages: " +
          (error.response?.data?.message || error.message),
      );
    }
  } finally {
    loading.value = false;
  }
};

const savePackage = async () => {
  try {
    loading.value = true;
    const token = localStorage.getItem("auth_token");

    // Convert features text to array
    const packageData = {
      ...form,
      features: form.features_text
        ? form.features_text.split("\n").filter((f) => f.trim())
        : [],
    };
    delete packageData.features_text;

    if (editMode.value) {
      await axios.put(
        `${API_URL}/super-admin/subscription-packages/${form.id}`,
        packageData,
        {
          headers: { Authorization: `Bearer ${token}` },
        },
      );
      alert("Package updated successfully!");
    } else {
      await axios.post(
        `${API_URL}/super-admin/subscription-packages`,
        packageData,
        {
          headers: { Authorization: `Bearer ${token}` },
        },
      );
      alert("Package created successfully!");
    }

    await fetchPackages();
    closeModal();
  } catch (error) {
    console.error("Error saving package:", error);
    alert(
      "Failed to save package: " +
        (error.response?.data?.message || error.message),
    );
  } finally {
    loading.value = false;
  }
};

const togglePackage = async (pkg) => {
  try {
    const token = localStorage.getItem("auth_token");
    await axios.put(
      `${API_URL}/super-admin/subscription-packages/${pkg.id}`,
      {
        is_active: !pkg.is_active,
      },
      {
        headers: { Authorization: `Bearer ${token}` },
      },
    );
    pkg.is_active = !pkg.is_active;
  } catch (error) {
    console.error("Error toggling package:", error);
    alert("Failed to toggle package status");
  }
};

const deletePackage = async (id) => {
  if (confirm("Yakin ingin menghapus package ini?")) {
    try {
      const token = localStorage.getItem("auth_token");
      await axios.delete(`${API_URL}/super-admin/subscription-packages/${id}`, {
        headers: { Authorization: `Bearer ${token}` },
      });
      await fetchPackages();
      alert("Package deleted successfully!");
    } catch (error) {
      console.error("Error deleting package:", error);
      alert("Failed to delete package");
    }
  }
};

const closeModal = () => {
  showAddModal.value = false;
  editMode.value = false;
  Object.keys(form).forEach((key) => {
    if (typeof form[key] === "boolean") form[key] = key === "is_active";
    else if (typeof form[key] === "number") form[key] = 0;
    else form[key] = "";
  });
};

onMounted(() => {
  fetchPackages();
});
</script>

<style scoped>
.modal.show {
  display: block;
}
</style>
