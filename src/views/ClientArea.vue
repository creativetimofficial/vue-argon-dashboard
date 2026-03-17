<template>
  <main class="main-content mt-0">
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-12">
          <div class="card shadow-lg border-0 rounded-lg">
            <div class="card-header bg-gradient-success text-white text-center py-4">
              <h3 class="mb-0">
                <i class="fas fa-shopping-cart me-2"></i>
                Client Area - Choose Subscription Package
              </h3>
              <p class="mb-0 mt-2">Choose the right package for your ISP needs</p>
            </div>

            <div class="card-body p-4">
              <div v-if="loading" class="text-center py-5">
                <div class="spinner-border text-success" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3">Loading packages...</p>
              </div>

              <div v-else-if="errorMessage" class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ errorMessage }}
              </div>

              <div v-else-if="packages.length === 0" class="text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <p class="text-muted">No packages are available at this time.</p>
              </div>

              <div v-else class="row g-4">
                <div
                  v-for="pkg in packages"
                  :key="pkg.id"
                  class="col-lg-4 col-md-6"
                >
                  <div
                    class="card h-100 package-card"
                    :class="{
                      'border-success border-3': selectedPackage?.id === pkg.id,
                      'border-primary': pkg.is_popular,
                    }"
                  >
                    <div
                      v-if="pkg.is_popular"
                      class="badge bg-primary position-absolute top-0 start-50 translate-middle px-3 py-2"
                      style="z-index: 1"
                    >
                      <i class="fas fa-star me-1"></i>
                      Most Popular
                    </div>
                    <div class="card-body p-4">
                      <h4 class="card-title text-center mb-3">{{ pkg.name }}</h4>
                      <div class="text-center mb-4">
                        <h2 class="text-success mb-0">
                          {{ formatCurrency(pkg.price) }}
                        </h2>
                        <small class="text-muted">/ {{ pkg.active_days }} Days</small>
                      </div>

                      <div v-if="pkg.description" class="mb-3">
                        <p class="text-muted small">{{ pkg.description }}</p>
                      </div>

                      <ul class="list-unstyled mb-4">
                        <li class="mb-2">
                          <i class="fas fa-check text-success me-2"></i>
                          <strong>{{ formatNumber(pkg.max_customers) }}</strong> Maximum Customers
                        </li>
                        <li class="mb-2">
                          <i class="fas fa-check text-success me-2"></i>
                          <strong>{{ formatNumber(pkg.max_users) }}</strong> Maximum Users
                        </li>
                        <li class="mb-2">
                          <i class="fas fa-check text-success me-2"></i>
                          <strong>{{ formatNumber(pkg.max_locations) }}</strong> Locations
                        </li>
                        <li
                          v-if="pkg.features && pkg.features.length > 0"
                          v-for="(feature, idx) in pkg.features"
                          :key="idx"
                          class="mb-2"
                        >
                          <i class="fas fa-check text-success me-2"></i>
                          {{ feature }}
                        </li>
                      </ul>

                      <button
                        class="btn w-100"
                        :class="
                          selectedPackage?.id === pkg.id
                            ? 'btn-success'
                            : 'btn-outline-success'
                        "
                        @click="selectPackage(pkg)"
                        :disabled="processing"
                      >
                        <span v-if="selectedPackage?.id === pkg.id">
                          <i class="fas fa-check me-2"></i>
                          Selected
                        </span>
                        <span v-else>
                          <i class="fas fa-shopping-cart me-2"></i>
                          Choose Package
                        </span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <div v-if="selectedPackage" class="mt-5">
                <div class="card bg-light">
                  <div class="card-body p-4">
                    <h5 class="mb-3">
                      <i class="fas fa-info-circle me-2"></i>
                      Selected Package: {{ selectedPackage.name }}
                    </h5>
                    <div class="row">
                      <div class="col-md-6">
                        <p class="mb-2">
                          <strong>Price:</strong>
                          {{ formatCurrency(selectedPackage.price) }} / {{ selectedPackage.active_days }} Hari
                        </p>
                        <p class="mb-2">
                          <strong>Maximum Customers:</strong>
                          {{ formatNumber(selectedPackage.max_customers) }}
                        </p>
                        <p class="mb-2">
                          <strong>Maximum Users:</strong>
                          {{ formatNumber(selectedPackage.max_users) }}
                        </p>
                      </div>
                    </div>

                    <!-- New ISP Fields -->
                    <div v-if="intent === 'new_isp'" class="mt-4 p-3 border rounded bg-white">
                      <h6 class="mb-3 text-primary">New ISP Unit Information</h6>
                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <label class="form-label">Company Name / ISP Unit</label>
                          <input v-model="newIspData.company_name" type="text" class="form-control" placeholder="Example: Media Network Unit B" required>
                        </div>
                        <div class="col-md-6 mb-3">
                          <label class="form-label">Subdomain</label>
                          <div class="input-group">
                            <input v-model="newIspData.subdomain" type="text" class="form-control" placeholder="unit-b" required>
                            <span class="input-group-text">.localhost</span>
                          </div>
                          <small class="text-muted">This will be your dashboard address later.</small>
                        </div>
                      </div>
                    </div>

                    <!-- Renewal Info -->
                    <div v-if="intent === 'renew' && targetIspId" class="mt-4 p-3 border rounded bg-white">
                       <h6 class="mb-1 text-primary">Subscription Renewal</h6>
                       <p class="text-sm text-muted mb-0">This package will be applied to ISP ID: <strong>#{{ targetIspId }}</strong></p>
                    </div>

                    <div class="mt-4">
                      <button
                        class="btn btn-success btn-lg w-100"
                        @click="proceedToPayment"
                        :disabled="processing || (intent === 'new_isp' && (!newIspData.company_name || !newIspData.subdomain))"
                      >
                        <span v-if="processing">
                          <i class="fas fa-spinner fa-spin me-2"></i>
                          Processing...
                        </span>
                        <span v-else>
                          <i class="fas fa-credit-card me-2"></i>
                          Continue to Payment
                        </span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import api from "@/services/api";
import notify from "@/utils/notify";

const router = useRouter();
const route = useRoute();

const packages = ref([]);
const selectedPackage = ref(null);
const loading = ref(false);
const processing = ref(false);
const errorMessage = ref("");

// Multi-ISP Handling
const intent = ref("");
const targetIspId = ref(null);
const newIspData = ref({
  company_name: "",
  subdomain: ""
});

onMounted(async () => {
  intent.value = route.query.intent || "";
  targetIspId.value = route.query.isp_id;
  
  await fetchPackages();
});

const fetchPackages = async () => {
  loading.value = true;
  errorMessage.value = "";
  try {
    const response = await api.get("/subscription-packages/public");
    let allPackages = response.data.filter((pkg) => pkg.is_active);
    
    // If renewing, hide trial packages
    if (intent.value === 'renew') {
      allPackages = allPackages.filter(pkg => pkg.price > 0 && !(pkg.trial_days > 0));
    }
    
    packages.value = allPackages;
  } catch (error) {
    console.error("Error fetching packages:", error);
    errorMessage.value =
      "Failed to load packages. Please refresh the page or contact the administrator.";
  } finally {
    loading.value = false;
  }
};

const selectPackage = (pkg) => {
  selectedPackage.value = pkg;
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "IDR",
    minimumFractionDigits: 0,
  }).format(amount);
};

const formatNumber = (num) => {
  if (num === -1 || num === null) return "Unlimited";
  return new Intl.NumberFormat("en-US").format(num);
};

const proceedToPayment = async () => {
  if (!selectedPackage.value) return;

  processing.value = true;
  try {
    // Check if package is trial (trial_days > 0 or price is 0)
    const isTrial =
      selectedPackage.value.trial_days > 0 ||
      selectedPackage.value.price === 0;

    if (isTrial) {
      // For trial packages, request approval
      await requestTrialApproval();
    } else {
      // For paid packages, proceed to payment
      await createPayment();
    }
  } catch (error) {
    console.error("Error proceeding to payment:", error);
    errorMessage.value =
      error.response?.data?.message ||
      "An error occurred. Please try again.";
  } finally {
    processing.value = false;
  }
};

const requestTrialApproval = async () => {
  try {
    const payload = {
      package_id: selectedPackage.value.id,
      is_new_isp: intent.value === 'new_isp',
      target_isp_id: targetIspId.value,
      ...newIspData.value
    };

    const response = await api.post("/isp-admin/subscribe", payload);

    if (response.data.success) {
      notify(
        "success",
        "Request Sent",
        "Trial package request has been sent. Please wait for Super Admin approval."
      );
      router.push("/client-area/dashboard");
    }
  } catch (error) {
    throw error;
  }
};

const createPayment = async () => {
  try {
    const payload = {
      package_id: selectedPackage.value.id,
      is_new_isp: intent.value === 'new_isp',
      target_isp_id: targetIspId.value,
      ...newIspData.value
    };

    const response = await api.post("/isp-admin/subscribe", payload);

    if (response.data.payment_url) {
      // Redirect to payment gateway
      window.location.href = response.data.payment_url;
    } else if (response.data.success) {
      notify("success", "Success", "Package activated successfully!");
      router.push("/client-area/dashboard");
    }
  } catch (error) {
    console.error("Payment error:", error);
    if (error.response?.data?.message) {
      errorMessage.value = error.response.data.message;
    } else {
      errorMessage.value = "Failed to create payment. Please try again.";
    }
    throw error;
  }
};
</script>

<style scoped>
.package-card {
  transition: all 0.3s ease;
  cursor: pointer;
}

.package-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.card {
  border-radius: 1rem;
}
</style>
