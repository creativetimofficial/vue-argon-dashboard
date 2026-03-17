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
              <h5 class="mb-0">{{ $t('dashboard.payment_gateways.title') }}</h5>
              <p class="text-sm mb-0">
                {{ $t('dashboard.payment_gateways.subtitle') }}
              </p>
            </div>
            <button class="btn btn-primary btn-sm" @click="showAddModal = true">
              <i class="fas fa-plus me-2"></i>{{ $t('dashboard.payment_gateways.add_gateway') }}
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
                        <h6 class="text-secondary">{{ $t('dashboard.payment_gateways.no_gateways') }}</h6>
                        <p class="text-sm text-secondary mb-0">Klik tombol "Add Gateway" untuk memulai.</p>
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
                        {{ gateway.is_active ? $t('common.active') : $t('common.inactive') }}
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
              {{ editMode ? $t('common.edit') : $t('common.add') }} {{ $t('dashboard.payment_gateways.add_edit_gateway') }}
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
                  <label class="form-label">{{ $t('dashboard.payment_gateways.gateway_name') }}</label>
                  <select v-model="form.gateway_name" class="form-control" required>
                    <option value="">{{ $t('dashboard.payment_gateways.select_gateway') }}</option>
                    <option value="Midtrans">Midtrans</option>
                    <option value="Xendit">Xendit</option>
                    <option value="Tripay">Tripay</option>
                    <option value="Duitku">Duitku</option>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">{{ $t('dashboard.payment_gateways.type') }}</label>
                  <select v-model="form.gateway_type" class="form-control" required>
                    <option value="local">{{ $t('dashboard.payment_gateways.local') }}</option>
                    <option value="international">{{ $t('dashboard.payment_gateways.international') }}</option>
                    <option value="both">{{ $t('dashboard.payment_gateways.both') }}</option>
                  </select>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                   <label class="form-label">{{ $t('dashboard.payment_gateways.api_key') }}</label>
                  <input
                    v-model="form.api_key"
                    type="text"
                    class="form-control"
                    required
                  />
                  <small class="text-muted">{{ $t('dashboard.payment_gateways.api_key_hint') }}</small>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">{{ $t('dashboard.payment_gateways.secret_key') }}</label>
                  <input
                    v-model="form.secret_key"
                    type="password"
                    class="form-control"
                    :placeholder="editMode ? 'Leave blank if you don\'t want to change' : 'Enter Server/Private Key'"
                    :required="!editMode"
                  />
                  <small class="text-muted">{{ $t('dashboard.payment_gateways.secret_key_hint') }}</small>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">{{ $t('dashboard.payment_gateways.merchant_id') }}</label>
                  <input
                    v-model="form.merchant_id"
                    type="text"
                    class="form-control"
                    placeholder="Merchant Code (Tripay/Duitku) or ID"
                  />
                  <small class="text-muted">{{ $t('dashboard.payment_gateways.merchant_hint') }}</small>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">{{ $t('dashboard.payment_gateways.transaction_fee') }}</label>
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
                  >{{ $t('dashboard.payment_gateways.countries_label') }}</label
                >
                <input
                  v-model="countriesText"
                  type="text"
                  class="form-control"
                  placeholder="ID, MY, SG"
                />
              </div>

              <div class="mb-3">
                <label class="form-label">{{ $t('dashboard.payment_gateways.webhook_url') }}</label>
                <input
                  v-model="form.webhook_url"
                  type="url"
                  class="form-control"
                  readonly
                />
                <small class="text-muted"
                  >{{ $t('dashboard.payment_gateways.webhook_hint') }}</small
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
                  >{{ $t('dashboard.payment_gateways.enable_gateway') }}</label
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
                  >{{ $t('dashboard.payment_gateways.sandbox_mode') }}</label
                >
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeModal">
              {{ $t('common.cancel') }}
            </button>
            <button type="button" class="btn btn-primary" @click="saveGateway">
              <i class="fas fa-save me-2"></i>{{ $t('common.save') }}
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
import notify, { confirm } from '@/utils/notify';

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
    const token = localStorage.getItem("auth_token") || sessionStorage.getItem("auth_token");
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
    Midtrans: "https://nurosoft.id/blog/wp-content/uploads/2024/06/Midtrans.webp",
    Xendit: "https://dka575ofm4ao0.cloudfront.net/pages-transactional_logos/retina/69862/xenditlogo.png",
    Tripay: "https://tripay.co.id/assets/images/logo-black.png",
    Duitku: "https://docs.duitku.com/assets/img/duitku-logo.png",
  };
  // Fallback
  return logos[name] || "https://premium.creative-tim.com/vue-argon-dashboard-pro/img/logos/mastercard.png";
};

const editGateway = (gateway) => {
  editMode.value = true;
  showAddModal.value = true;
  
  // Copy all fields except secret_key (columns match DB)
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
    const token = localStorage.getItem("auth_token") || sessionStorage.getItem("auth_token");
    
    // Construct payload
    const payload = {
      ...form,
      supported_countries: countriesText.value.split(",").map(c => c.trim()).filter(c => c)
    };
    
    // If editing and secret_key is empty, don't send it (keep existing)
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
    notify("success", "Success", editMode.value ? 'Payment gateway updated successfully!' : 'Payment gateway added successfully!');
  } catch (error) {
    console.error("Error saving gateway:", error);
    notify("error", "Error", "Failed to save gateway: " + (error.response?.data?.message || error.message));
  } finally {
    loading.value = false;
  }
};

const toggleGateway = async (gateway) => {
  try {
    const token = localStorage.getItem("auth_token") || sessionStorage.getItem("auth_token");
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
  if (await confirm('Konfirmasi', "Are you sure you want to delete this gateway?", 'warning')) {
    try {
      const token = localStorage.getItem("auth_token") || sessionStorage.getItem("auth_token");
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
  
  // Reset Form
  form.id = null;
  form.gateway_name = "";
  form.gateway_type = "local";
  form.api_key = "";
  form.secret_key = "";
  form.merchant_id = "";
  form.transaction_fee = 0;
  form.is_active = true;
  form.sandbox_mode = true;
  countriesText.value = "";
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
