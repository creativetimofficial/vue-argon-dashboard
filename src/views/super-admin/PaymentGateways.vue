<template>
  <div class="py-4 container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card mb-4 mt-4">
          <!-- Card header -->
          <div
            class="card-header d-flex justify-content-between align-items-center"
          >
            <div>
              <h5 class="mb-0">Payment Gateway Management</h5>
              <p class="text-sm mb-0">
                Kelola payment gateway untuk pembayaran subscription ISP
              </p>
            </div>
            <button class="btn btn-primary btn-sm" @click="showAddModal = true">
              <i class="fas fa-plus me-2"></i>Tambah Gateway
            </button>
          </div>

          <!-- Card body -->
          <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th
                      class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                    >
                      Gateway
                    </th>
                    <th
                      class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"
                    >
                      Status
                    </th>
                    <th
                      class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"
                    >
                      Supported Countries
                    </th>
                    <th
                      class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"
                    >
                      Transaction Fee
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
                    <td colspan="5" class="text-center py-5">
                      <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="!loading && gateways.length === 0">
                    <td colspan="5" class="text-center py-5">
                      <div class="d-flex flex-column align-items-center">
                        <i class="fas fa-wallet fa-3x text-secondary mb-3 opacity-3"></i>
                        <h6 class="text-secondary">Belum ada Payment Gateway</h6>
                        <p class="text-sm text-secondary mb-0">Klik tombol "Tambah Gateway" untuk memulai.</p>
                      </div>
                    </td>
                  </tr>
                  <tr v-for="gateway in gateways" :key="gateway.id">
                    <td>
                      <div class="d-flex px-3 py-1">
                        <div>
                          <img
                            :src="gateway.logo"
                            class="avatar avatar-sm me-3"
                            :alt="gateway.name"
                          />
                        </div>
                        <div class="d-flex flex-column justify-content-center">
                          <h6 class="mb-0 text-sm">{{ gateway.gateway_name }}</h6>
                          <p class="text-xs text-secondary mb-0">
                            {{ gateway.gateway_type }}
                          </p>
                        </div>
                      </div>
                    </td>
                    <td class="px-2">
                      <span
                        :class="
                          gateway.is_active
                            ? 'badge badge-sm bg-gradient-success'
                            : 'badge badge-sm bg-gradient-secondary'
                        "
                      >
                        {{ gateway.is_active ? "Active" : "Inactive" }}
                      </span>
                    </td>
                    <td class="px-2">
                      <p class="text-xs font-weight-bold mb-0">
                        {{ Array.isArray(gateway.countries) ? gateway.countries.join(", ") : gateway.countries }}
                      </p>
                    </td>
                    <td class="px-2">
                      <p class="text-xs font-weight-bold mb-0">
                        {{ gateway.transaction_fee }}%
                      </p>
                    </td>
                    <td class="align-middle text-center">
                      <button
                        class="btn btn-link text-secondary mb-0"
                        @click="editGateway(gateway)"
                      >
                        <i class="fas fa-edit text-xs"></i>
                      </button>
                      <button
                        class="btn btn-link text-secondary mb-0"
                        @click="toggleGateway(gateway)"
                      >
                        <i
                          :class="
                            gateway.is_active
                              ? 'fas fa-toggle-on text-success text-xs'
                              : 'fas fa-toggle-off text-xs'
                          "
                        ></i>
                      </button>
                      <button
                        class="btn btn-link text-danger mb-0"
                        @click="deleteGateway(gateway.id)"
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

    <!-- Add/Edit Gateway Modal -->
    <div
      v-if="showAddModal"
      class="modal fade show d-block"
      style="background: rgba(0, 0, 0, 0.5)"
    >
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              {{ editMode ? "Edit" : "Tambah" }} Payment Gateway
            </h5>
            <button
              type="button"
              class="btn-close"
              @click="closeModal"
            ></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveGateway">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Gateway Name</label>
                  <select v-model="form.gateway_name" class="form-control" required>
                    <option value="">Pilih Gateway</option>
                    <option value="Xendit">Xendit</option>
                    <option value="Midtrans">Midtrans</option>
                    <option value="PayPal">PayPal</option>
                    <option value="Stripe">Stripe</option>
                    <option value="2Checkout">2Checkout</option>
                    <option value="Razorpay">Razorpay</option>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Type</label>
                  <select v-model="form.gateway_type" class="form-control" required>
                    <option value="local">Local (Indonesia)</option>
                    <option value="international">International</option>
                    <option value="both">Both</option>
                  </select>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                   <label class="form-label">API Key / Public Key (Client Key)</label>
                  <input
                    v-model="form.api_key"
                    type="text"
                    class="form-control"
                    required
                  />
                  <small class="text-muted">Public key atau Client Key untuk browser</small>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Secret Key / Private Key (Server Key)</label>
                  <input
                    v-model="form.secret_key"
                    type="password"
                    class="form-control"
                    :placeholder="editMode ? 'Kosongkan jika tidak ingin mengubah' : 'Masukkan Server Key'"
                    :required="!editMode"
                  />
                  <small class="text-muted">Secret key atau Server Key untuk otentikasi di server</small>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Merchant ID (Optional)</label>
                  <input
                    v-model="form.merchant_id"
                    type="text"
                    class="form-control"
                  />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Transaction Fee (%)</label>
                  <input
                    v-model="form.transaction_fee"
                    type="number"
                    step="0.01"
                    class="form-control"
                    required
                  />
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label"
                  >Supported Countries (comma separated)</label
                >
                <input
                  v-model="countriesText"
                  type="text"
                  class="form-control"
                  placeholder="ID, MY, SG, US, UK"
                />
              </div>

              <div class="mb-3">
                <label class="form-label">Webhook URL</label>
                <input
                  v-model="form.webhook_url"
                  type="url"
                  class="form-control"
                  readonly
                />
                <small class="text-muted"
                  >Copy URL ini ke dashboard payment gateway</small
                >
              </div>

              <div class="form-check form-switch mb-3">
                <input
                  v-model="form.is_active"
                  class="form-check-input"
                  type="checkbox"
                  id="isActive"
                />
                <label class="form-check-label" for="isActive"
                  >Aktifkan Gateway</label
                >
              </div>

              <div class="form-check form-switch mb-3">
                <input
                  v-model="form.sandbox_mode"
                  class="form-check-input"
                  type="checkbox"
                  id="sandboxMode"
                />
                <label class="form-check-label" for="sandboxMode"
                  >Sandbox Mode (Testing)</label
                >
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeModal">
              Batal
            </button>
            <button type="button" class="btn btn-primary" @click="saveGateway">
              <i class="fas fa-save me-2"></i>Simpan
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from "vue";
import MiniStatisticsCard from "@/examples/Cards/MiniStatisticsCard.vue";
import axios from "axios";

const gateways = ref([]);
const loading = ref(false);
const API_URL = "http://localhost:8000/api";

const showAddModal = ref(false);
const editMode = ref(false);
const form = reactive({
  id: null,
  gateway_name: "",
  gateway_type: "local",
  api_key: "",
  secret_key: "",
  merchant_id: "",
  transaction_fee: 0,
  supported_countries: [],
  webhook_url: "",
  is_active: true,
  sandbox_mode: true,
});

const countriesText = ref("");

const fetchGateways = async () => {
  try {
    loading.value = true;
    const token = localStorage.getItem("auth_token");
    const response = await axios.get(`${API_URL}/super-admin/payment-gateways`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    gateways.value = response.data.map(g => ({
      ...g,
      countries: Array.isArray(g.supported_countries) ? g.supported_countries : (g.supported_countries ? JSON.parse(g.supported_countries) : []),
      logo: getLogo(g.gateway_name)
    }));
  } catch (error) {
    console.error("Error fetching gateways:", error);
  } finally {
    loading.value = false;
  }
};

const getLogo = (name) => {
  const logos = {
    Midtrans: "https://midtrans.com/assets/images/midtrans-logo.svg",
    Xendit: "https://upload.wikimedia.org/wikipedia/commons/thumb/9/9e/Xendit_logo.svg/1200px-Xendit_logo.svg.png",
    Stripe: "https://upload.wikimedia.org/wikipedia/commons/thumb/b/ba/Stripe_Logo%2C_revised_2016.svg/1200px-Stripe_Logo%2C_revised_2016.svg.png",
    PayPal: "https://upload.wikimedia.org/wikipedia/commons/thumb/b/b5/PayPal.svg/1200px-PayPal.svg.png",
    "2Checkout": "https://www.2checkout.com/images/branding/logo-2checkout-blue.svg",
    Razorpay: "https://upload.wikimedia.org/wikipedia/commons/thumb/8/89/Razorpay_logo.svg/1200px-Razorpay_logo.svg.png",
    Bank_Transfer: "https://premium.creative-tim.com/vue-argon-dashboard-pro/img/logos/mastercard.png"
  };
  return logos[name] || "https://premium.creative-tim.com/vue-argon-dashboard-pro/img/logos/mastercard.png";
};

const editGateway = (gateway) => {
  editMode.value = true;
  showAddModal.value = true;
  
  // Copy all fields except secret_key (it's hidden by API)
  Object.keys(form).forEach(key => {
    if (key !== 'secret_key' && gateway[key] !== undefined) {
      form[key] = gateway[key];
    }
  });
  
  // Clear secret_key so user can optionally enter a new one
  form.secret_key = '';
  
  countriesText.value = gateway.countries ? gateway.countries.join(", ") : '';
};

const saveGateway = async () => {
  try {
    loading.value = true;
    const token = localStorage.getItem("auth_token");
    
    const payload = {
      ...form,
      supported_countries: countriesText.value.split(",").map(c => c.trim()).filter(c => c)
    };
    
    // If editing and secret_key is empty, don't send it (keep existing value)
    if (editMode.value && !payload.secret_key) {
      delete payload.secret_key;
    }

    if (editMode.value) {
      await axios.put(`${API_URL}/super-admin/payment-gateways/${form.id}`, payload, {
        headers: { Authorization: `Bearer ${token}` },
      });
    } else {
      await axios.post(`${API_URL}/super-admin/payment-gateways`, payload, {
        headers: { Authorization: `Bearer ${token}` },
      });
    }
    
    await fetchGateways();
    closeModal();
    alert(editMode.value ? 'Payment gateway berhasil diupdate!' : 'Payment gateway berhasil ditambahkan!');
  } catch (error) {
    console.error("Error saving gateway:", error);
    alert("Failed to save gateway settings: " + (error.response?.data?.message || error.message));
  } finally {
    loading.value = false;
  }
};

const toggleGateway = async (gateway) => {
  try {
    const token = localStorage.getItem("auth_token");
    await axios.put(`${API_URL}/super-admin/payment-gateways/${gateway.id}`, {
      is_active: !gateway.is_active
    }, {
      headers: { Authorization: `Bearer ${token}` },
    });
    gateway.is_active = !gateway.is_active;
  } catch (error) {
    console.error("Error toggling gateway:", error);
  }
};

const deleteGateway = async (id) => {
  if (confirm("Yakin ingin menghapus gateway ini?")) {
    try {
      const token = localStorage.getItem("auth_token");
      await axios.delete(`${API_URL}/super-admin/payment-gateways/${id}`, {
        headers: { Authorization: `Bearer ${token}` },
      });
      await fetchGateways();
    } catch (error) {
      console.error("Error deleting gateway:", error);
    }
  }
};

const closeModal = () => {
  showAddModal.value = false;
  editMode.value = false;
  Object.keys(form).forEach((key) => {
    if (typeof form[key] === "boolean")
      form[key] = key === "is_active" || key === "sandbox_mode";
    else if (typeof form[key] === "number") form[key] = 2.9;
    else form[key] = "";
  });
};

onMounted(() => {
  fetchGateways();
});
</script>

<style scoped>
.avatar {
  object-fit: contain;
  background: white;
  padding: 4px;
}

.modal.show {
  display: block;
}
</style>
