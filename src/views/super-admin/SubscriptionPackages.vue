<template>
  <div class="py-4 container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div
            class="card-header d-flex justify-content-between align-items-center"
          >
            <div>
              <h5 class="mb-0">{{ $t('dashboard.packages.title') }}</h5>
              <p class="text-sm mb-0">{{ $t('dashboard.packages.subtitle') }}</p>
            </div>
            <button class="btn btn-primary btn-sm" @click="showAddModal = true">
              <i class="fas fa-plus me-2"></i>{{ $t('dashboard.packages.add_package') }}
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
                      Package
                    </th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">{{ $t('dashboard.packages.price_duration') }}</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">{{ $t('dashboard.packages.main_features') }}</th>
                    <th
                      class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                    >
                      Status
                    </th>
                    <th
                      class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                    >
                      Action
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="packages.length === 0">
                    <td colspan="5" class="text-center text-secondary py-4">
                      {{ $t('dashboard.packages.no_packages') }}
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
                          >{{ $t('dashboard.packages.popular') }}</span
                        >
                      </div>
                    </td>
                    <td>
                      <p class="text-sm font-weight-bold mb-0">
                        Rp {{ formatCurrency(pkg.price) }}
                      </p>
                      <p class="text-xs text-secondary mb-0">
                        Duration: {{ pkg.active_days ? pkg.active_days + ' Days' : 'Lifetime (Unlimited)' }}
                      </p>
                      <p class="text-xs text-info mb-0" v-if="pkg.trial_days > 0">
                        Trial: {{ pkg.trial_days }} Days
                      </p>
                    </td>
                    <td>
                      <p class="text-xs mb-0">
                        <strong>{{ $t('dashboard.packages.max_customers') }}:</strong>
                        {{
                          pkg.max_customers === -1
                            ? "Unlimited"
                            : pkg.max_customers
                        }}
                      </p>
                      <p class="text-xs mb-0">
                        <strong>{{ $t('dashboard.packages.max_users') }}:</strong> {{ pkg.max_users }}
                      </p>
                      <p class="text-xs mb-0">
                        <strong>{{ $t('dashboard.packages.max_locations') }}:</strong> {{ pkg.max_locations }}
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
                        {{ pkg.is_active ? $t('common.active') : $t('common.inactive') }}
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
            <h5 class="modal-title">{{ $t('dashboard.packages.add_subscription') }}</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <div class="modal-body" style="overflow-x: auto; max-width: 100vw;">
            <form @submit.prevent="savePackage">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">{{ $t('dashboard.packages.package_name') }}</label>
                  <input v-model="form.name" type="text" class="form-control" required />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">{{ $t('dashboard.packages.slug') }}</label>
                  <input v-model="form.slug" type="text" class="form-control" required />
                  <small class="text-muted">{{ $t('dashboard.packages.slug_hint') }}</small>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">{{ $t('dashboard.packages.description') }}</label>
                <textarea v-model="form.description" class="form-control" rows="2"></textarea>
              </div>
              
              <h6 class="mt-3 mb-2">{{ $t('dashboard.packages.price_duration') }}</h6>
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label class="form-label">{{ $t('dashboard.packages.price_rp') }}</label>
                  <input v-model.number="form.price" type="number" step="0.01" class="form-control" required />
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">{{ $t('dashboard.packages.discount') }}</label>
                  <input v-model.number="form.discount_percent" type="number" min="0" max="100" class="form-control" />
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">{{ $t('dashboard.packages.duration_days') }}</label>
                  <div class="input-group">
                    <input 
                      :disabled="isDaysUnlimited"
                      v-model.number="form.active_days" 
                      type="number" 
                      min="1" 
                      class="form-control" 
                      :placeholder="isDaysUnlimited ? 'Unlimited' : '30'"
                    />
                    <div class="input-group-text bg-light">
                      <input 
                        class="form-check-input mt-0 me-2" 
                        type="checkbox" 
                        id="unlimitedCheck" 
                        v-model="isDaysUnlimited"
                      >
                      <label class="mb-0 text-xs cursor-pointer" for="unlimitedCheck">{{ $t('dashboard.packages.unlimited') }}</label>
                    </div>
                  </div>
                  <small class="text-muted" v-if="!isDaysUnlimited">Misal: 30, 90, 365</small>
                  <small class="text-success font-weight-bold" v-else>{{ $t('dashboard.packages.unlimited_hint') }}</small>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label class="form-label">{{ $t('dashboard.packages.trial_period') }}</label>
                  <input v-model.number="form.trial_days" type="number" min="0" class="form-control" />
                  <small class="text-muted">{{ $t('dashboard.packages.no_trial') }}</small>
                </div>
              </div>

              <h6 class="mt-3 mb-2">{{ $t('dashboard.packages.quota_limits') }}</h6>
              <div class="row">
                <div class="col-md-3 mb-3">
                  <label class="form-label">{{ $t('dashboard.packages.max_customers') }}</label>
                  <input v-model.number="form.max_customers" type="number" class="form-control" />
                  <small class="text-muted">-1 = Unlimited</small>
                </div>
                <div class="col-md-3 mb-3">
                  <label class="form-label">{{ $t('dashboard.packages.max_users') }}</label>
                  <input v-model.number="form.max_users" type="number" class="form-control" />
                </div>
                <div class="col-md-3 mb-3">
                  <label class="form-label">{{ $t('dashboard.packages.max_locations') }}</label>
                  <input v-model.number="form.max_locations" type="number" class="form-control" />
                </div>
                <div class="col-md-3 mb-3">
                  <label class="form-label">{{ $t('dashboard.packages.max_invoices') }}</label>
                  <input v-model.number="form.max_invoices" type="number" class="form-control" />
                </div>
              </div>

              <h6 class="mt-3 mb-2">{{ $t('dashboard.packages.features_support') }}</h6>
              <div class="row">
                <div class="col-md-3 mb-2">
                  <div class="form-check">
                    <input v-model="form.email_support" class="form-check-input" type="checkbox" id="emailSupport" />
                    <label class="form-check-label" for="emailSupport">{{ $t('dashboard.packages.email_support') }}</label>
                    <small class="text-muted d-block mt-1">{{ $t('dashboard.packages.email_support_desc') }}</small>
                  </div>
                </div>
                <div class="col-md-3 mb-2">
                  <div class="form-check">
                    <input v-model="form.whatsapp_support" class="form-check-input" type="checkbox" id="whatsappSupport" />
                    <label class="form-check-label" for="whatsappSupport">{{ $t('dashboard.packages.whatsapp_support') }}</label>
                    <small class="text-muted d-block mt-1">{{ $t('dashboard.packages.whatsapp_support_desc') }}</small>
                  </div>
                </div>
                <div class="col-md-3 mb-2">
                  <div class="form-check">
                    <input v-model="form.custom_branding" class="form-check-input" type="checkbox" id="customBranding" />
                    <label class="form-check-label" for="customBranding">{{ $t('dashboard.packages.custom_branding') }}</label>
                    <small class="text-muted d-block mt-1">{{ $t('dashboard.packages.custom_branding_desc') }}</small>
                  </div>
                </div>
                <div class="col-md-3 mb-2">
                  <div class="form-check">
                    <input v-model="form.feature_api_access" class="form-check-input" type="checkbox" id="apiAccess" />
                    <label class="form-check-label" for="apiAccess">{{ $t('dashboard.packages.api_access') }}</label>
                    <small class="text-muted d-block mt-1">{{ $t('dashboard.packages.api_access_desc') }}</small>
                  </div>
                </div>
                <div class="col-md-3 mb-2">
                  <div class="form-check">
                    <input v-model="form.multi_user_access" class="form-check-input" type="checkbox" id="multiUserAccess" />
                    <label class="form-check-label" for="multiUserAccess">{{ $t('dashboard.packages.multi_user_access') }}</label>
                    <small class="text-muted d-block mt-1">{{ $t('dashboard.packages.multi_user_access_desc') }}</small>
                  </div>
                </div>
                <div class="col-md-3 mb-2">
                  <div class="form-check">
                    <input v-model="form.feature_free_subdomain" class="form-check-input" type="checkbox" id="freeSubdomain" />
                    <label class="form-check-label" for="freeSubdomain">{{ $t('dashboard.packages.free_subdomain') }}</label>
                    <small class="text-muted d-block mt-1">{{ $t('dashboard.packages.free_subdomain_desc') }}</small>
                  </div>
                </div>
                <div class="col-md-3 mb-2">
                  <div class="form-check">
                    <input v-model="form.feature_custom_domain" class="form-check-input" type="checkbox" id="customDomain" />
                    <label class="form-check-label" for="customDomain">{{ $t('dashboard.packages.custom_domain') }}</label>
                    <small class="text-muted d-block mt-1">{{ $t('dashboard.packages.custom_domain_desc') }}</small>
                  </div>
                </div>
                <div class="col-md-3 mb-2">
                  <div class="form-check">
                    <input v-model="form.feature_maps_interaktif" class="form-check-input" type="checkbox" id="mapsInteraktif" />
                    <label class="form-check-label" for="mapsInteraktif">{{ $t('dashboard.packages.maps_interactive') }}</label>
                    <small class="text-muted d-block mt-1">{{ $t('dashboard.packages.maps_interactive_desc') }}</small>
                  </div>
                </div>
              </div>

              <h6 class="mt-3 mb-2">{{ $t('dashboard.packages.technical_modules') }}</h6>
              <div class="row">
                <div class="col-md-4 mb-2">
                  <div class="form-check">
                    <input v-model="form.feature_dynamic_forwarding" class="form-check-input" type="checkbox" id="dynamicForwarding" />
                    <label class="form-check-label" for="dynamicForwarding">{{ $t('dashboard.packages.dynamic_forwarding') }}</label>
                    <small class="text-muted d-block mt-1">{{ $t('dashboard.packages.dynamic_forwarding_desc') }}</small>
                  </div>
                </div>
                <div class="col-md-4 mb-2">
                  <div class="form-check">
                    <input v-model="form.feature_whatsapp_gateway" class="form-check-input" type="checkbox" id="whatsappGateway" />
                    <label class="form-check-label" for="whatsappGateway">{{ $t('dashboard.packages.whatsapp_gateway') }}</label>
                    <small class="text-muted d-block mt-1">{{ $t('dashboard.packages.whatsapp_gateway_desc') }}</small>
                  </div>
                </div>
                <div class="col-md-4 mb-2">
                  <div class="form-check">
                    <input v-model="form.feature_payment_gateways" class="form-check-input" type="checkbox" id="paymentGateways" />
                    <label class="form-check-label" for="paymentGateways">{{ $t('dashboard.packages.payment_gateways_feat') }}</label>
                    <small class="text-muted d-block mt-1">{{ $t('dashboard.packages.payment_gateways_feat_desc') }}</small>
                  </div>
                </div>
                <div class="col-md-4 mb-2">
                  <div class="form-check">
                    <input v-model="form.feature_custom_landing_page" class="form-check-input" type="checkbox" id="customLandingPage" />
                    <label class="form-check-label" for="customLandingPage">{{ $t('dashboard.packages.custom_landing_page') }}</label>
                    <small class="text-muted d-block mt-1">{{ $t('dashboard.packages.custom_landing_page_desc') }}</small>
                  </div>
                </div>
                <div class="col-md-4 mb-2">
                  <div class="form-check">
                    <input v-model="form.feature_realtime_monitoring" class="form-check-input" type="checkbox" id="realtimeMonitoring" />
                    <label class="form-check-label" for="realtimeMonitoring">{{ $t('dashboard.packages.realtime_monitoring') }}</label>
                    <small class="text-muted d-block mt-1">{{ $t('dashboard.packages.realtime_monitoring_desc') }}</small>
                  </div>
                </div>
                <div class="col-md-4 mb-2">
                  <div class="form-check">
                    <input v-model="form.feature_radius" class="form-check-input" type="checkbox" id="radius" />
                    <label class="form-check-label" for="radius">{{ $t('dashboard.packages.radius') }}</label>
                    <small class="text-muted d-block mt-1">{{ $t('dashboard.packages.radius_desc') }}</small>
                  </div>
                </div>
                <div class="col-md-4 mb-2">
                  <div class="form-check">
                    <input v-model="form.feature_acs" class="form-check-input" type="checkbox" id="acs" />
                    <label class="form-check-label" for="acs">{{ $t('dashboard.packages.acs_management') }}</label>
                    <small class="text-muted d-block mt-1">{{ $t('dashboard.packages.acs_management_desc') }}</small>
                  </div>
                </div>
                <div class="col-md-4 mb-2">
                  <div class="form-check">
                    <input v-model="form.feature_customer_portal" class="form-check-input" type="checkbox" id="customerPortal" />
                    <label class="form-check-label" for="customerPortal">{{ $t('dashboard.packages.customer_portal') }}</label>
                    <small class="text-muted d-block mt-1">{{ $t('dashboard.packages.customer_portal_desc') }}</small>
                  </div>
                </div>
                <div class="col-md-4 mb-2">
                  <div class="form-check">
                    <input v-model="form.feature_android_app" class="form-check-input" type="checkbox" id="androidApp" />
                    <label class="form-check-label" for="androidApp">{{ $t('dashboard.packages.android_app') }}</label>
                    <small class="text-muted d-block mt-1">{{ $t('dashboard.packages.android_app_desc') }}</small>
                  </div>
                </div>
              </div>

              <h6 class="mt-3 mb-2">{{ $t('dashboard.packages.admin_whitelabel') }}</h6>
              <div class="row">
                <div class="col-md-6 mb-2">
                  <div class="form-check">
                    <input v-model="form.feature_automated_billing" class="form-check-input" type="checkbox" id="automatedBilling" />
                    <label class="form-check-label" for="automatedBilling">{{ $t('dashboard.packages.automated_billing') }}</label>
                    <small class="text-muted d-block mt-1">{{ $t('dashboard.packages.automated_billing_desc') }}</small>
                  </div>
                </div>
                <div class="col-md-6 mb-2">
                  <div class="form-check">
                    <input v-model="form.feature_whitelabel" class="form-check-input" type="checkbox" id="whiteLabel" />
                    <label class="form-check-label" for="whiteLabel">{{ $t('dashboard.packages.white_label') }}</label>
                    <small class="text-muted d-block mt-1">{{ $t('dashboard.packages.white_label_desc') }}</small>
                  </div>
                </div>
              </div>
              
              <div class="mb-3 mt-3">
                <label class="form-label">{{ $t('dashboard.packages.extra_features') }}</label>
                <textarea v-model="form.features_text" class="form-control" rows="3" placeholder="Other additional features..."></textarea>
              </div>

              <div class="row">
                <div class="col-md-4 mb-3">
                  <div class="form-check form-switch">
                    <input v-model="form.is_active" class="form-check-input" type="checkbox" id="isActive" />
                    <label class="form-check-label" for="isActive">{{ $t('dashboard.packages.package_active') }}</label>
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <div class="form-check form-switch">
                    <input 
                      v-model="form.requires_manual_approval" 
                      class="form-check-input" 
                      type="checkbox" 
                      id="requiresManualApproval"
                    />
                    <label class="form-check-label" for="requiresManualApproval">
                      Manual Approval 
                      <small class="text-xs text-muted">(Hanya Package Gratis/Trial)</small>
                    </label>
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <div class="form-check form-switch">
                    <input v-model="form.is_popular" class="form-check-input" type="checkbox" id="isPopular" />
                    <label class="form-check-label" for="isPopular">{{ $t('dashboard.packages.popular') }}</label>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeModal">{{ $t('common.cancel') }}</button>
            <button type="button" class="btn btn-primary" @click="savePackage"><i class="fas fa-save me-2"></i>{{ $t('dashboard.packages.save_package') }}</button>
          </div>
        </div>
      </div>
    </div>
  <!-- End main container -->
</template>

<script setup>
import { ref, reactive, onMounted } from "vue";
import { superAdminAPI } from "@/services/api";
import notify, { confirm } from '@/utils/notify';
const packages = ref([]);
const loading = ref(false);
const isDaysUnlimited = ref(false);

const showAddModal = ref(false);
const editMode = ref(false);
const form = reactive({
  id: null,
  name: "",
  slug: "",
  description: "",
  price: 0,
  discount_percent: 0,
  max_customers: 100,
  max_users: 5,
  max_locations: 1,
  max_invoices: 0,
  active_days: 30,
  trial_days: 0,
  email_support: true,
  whatsapp_support: false,
  custom_branding: false,
  multi_user_access: false,
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
  feature_payment_gateways: false,
  feature_custom_landing_page: false,
  feature_realtime_monitoring: false,
  feature_radius: false,
  feature_acs: false,
  feature_customer_portal: false,
  feature_android_app: false,
  feature_whatsapp_gateway: false,
  features_text: "",
  is_active: true,
  is_popular: false,
  requires_manual_approval: false,
});

const formatCurrency = (value) => {
  return new Intl.NumberFormat("en-US").format(value);
};

const editPackage = (pkg) => {
  editMode.value = true;
  showAddModal.value = true;
  Object.assign(form, pkg);
  form.features_text = pkg.features ? pkg.features.join("\n") : "";
  
  // Handle Unlimited Logic
  if (pkg.active_days === null) {
    isDaysUnlimited.value = true;
    form.active_days = null;
  } else {
    isDaysUnlimited.value = false;
  }
};

const fetchPackages = async () => {
  try {
    loading.value = true;
    const response = await superAdminAPI.getSubscriptionPackages();
    console.log("Fetched packages:", response.data);
    packages.value = Array.isArray(response.data) ? response.data : [];
  } catch (error) {
    console.error("Error fetching packages:", error);
    notify(
      "error",
      "Error",
      "Failed to load packages: " + (error.response?.data?.message || error.message)
    );
  } finally {
    loading.value = false;
  }
};

const savePackage = async () => {
  try {
    loading.value = true;

    // Convert features text to array
    const packageData = {
      ...form,
      features: form.features_text
        ? form.features_text.split("\n").filter((f) => f.trim())
        : [],
      active_days: isDaysUnlimited.value ? null : form.active_days,
    };
    delete packageData.features_text;

    if (editMode.value) {
      await superAdminAPI.updateSubscriptionPackage(form.id, packageData);
      notify("success", "Success", "Package updated successfully!");
    } else {
      await superAdminAPI.createSubscriptionPackage(packageData);
      notify("success", "Success", "Package created successfully!");
    }

    await fetchPackages();
    closeModal();
  } catch (error) {
    console.error("Error saving package:", error);
    notify(
      "error",
      "Error",
      "Failed to save package: " + (error.response?.data?.message || error.message)
    );
  } finally {
    loading.value = false;
  }
};

const togglePackage = async (pkg) => {
  try {
    await superAdminAPI.updateSubscriptionPackage(pkg.id, {
      is_active: !pkg.is_active,
    });
    pkg.is_active = !pkg.is_active;
  } catch (error) {
    console.error("Error toggling package:", error);
    notify("error", "Error", "Failed to toggle package status");
  }
};

const deletePackage = async (id) => {
  if (await confirm('Konfirmasi', "Are you sure you want to delete this package?", 'warning')) {
    try {
      await superAdminAPI.deleteSubscriptionPackage(id);
      await fetchPackages();
      notify("success", "Success", "Package deleted successfully!");
    } catch (error) {
      console.error("Error deleting package:", error);
      notify("error", "Error", "Failed to delete package");
    }
  }
};

const closeModal = () => {
  showAddModal.value = false;
  editMode.value = false;
  
  // Reset to default
  isDaysUnlimited.value = false;
  
  Object.assign(form, {
    id: null,
    name: "",
    slug: "",
    description: "",
    price: 0,
    discount_percent: 0,
    max_customers: 100,
    max_users: 5,
    max_locations: 1,
    max_invoices: 0,
    active_days: 30,
    trial_days: 0,
    email_support: true,
    whatsapp_support: false,
    custom_branding: false,
    multi_user_access: false,
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
    feature_payment_gateways: false,
    feature_custom_landing_page: false,
    feature_realtime_monitoring: false,
    feature_radius: false,
    feature_acs: false,
    feature_customer_portal: false,
    feature_android_app: false,
    feature_whatsapp_gateway: false,
    features_text: "",
    is_active: true,
    is_popular: false,
    requires_manual_approval: false,
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
