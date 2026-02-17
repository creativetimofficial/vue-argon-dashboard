<template>
  <div class="py-4 container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h5 class="mb-0">ISP Admin Theme Customizer</h5>
                <p class="text-sm mb-0">
                  Customize tampilan panel untuk ISP Admin clients
                </p>
              </div>
              <div>
                <button
                  class="btn btn-outline-primary btn-sm me-2"
                  @click="resetToDefault"
                >
                  <i class="fas fa-undo me-2"></i>Reset Default
                </button>
                <button
                  class="btn btn-outline-info btn-sm me-2"
                  @click="applySneatPreset"
                >
                  <i class="fas fa-magic me-2"></i>Sneat Preset
                </button>
                <button class="btn btn-primary btn-sm" @click="saveTheme">
                  <i class="fas fa-save me-2"></i>Simpan Theme
                </button>
              </div>
            </div>
          </div>

          <div class="card-body">
            <div class="row">
              <!-- Left Panel: Settings -->
              <div class="col-md-5">
                <!-- Branding -->
                <div class="card mb-3">
                  <div class="card-header pb-0">
                    <h6>Branding & Logo</h6>
                  </div>
                  <div class="card-body">
                    <div class="mb-3">
                      <label class="form-label">System Name</label>
                      <input
                        v-model="theme.system_name"
                        type="text"
                        class="form-control"
                      />
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Logo (Light Mode)</label>
                      <div class="d-flex align-items-center gap-3">
                        <img
                          v-if="theme.logo_light"
                          :src="theme.logo_light"
                          class="img-thumbnail"
                          style="max-height: 80px; max-width: 150px"
                        />
                        <input
                          type="file"
                          class="form-control"
                          accept="image/*"
                          @change="uploadLogo('light', $event)"
                        />
                      </div>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Logo (Dark Mode)</label>
                      <div class="d-flex align-items-center gap-3">
                        <img
                          v-if="theme.logo_dark"
                          :src="theme.logo_dark"
                          class="img-thumbnail bg-dark"
                          style="max-height: 80px; max-width: 150px"
                        />
                        <input
                          type="file"
                          class="form-control"
                          accept="image/*"
                          @change="uploadLogo('dark', $event)"
                        />
                      </div>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Favicon</label>
                      <div class="d-flex align-items-center gap-3">
                        <img
                          v-if="theme.favicon"
                          :src="theme.favicon"
                          class="img-thumbnail"
                          style="max-height: 32px; max-width: 32px"
                        />
                        <input
                          type="file"
                          class="form-control"
                          accept="image/*"
                          @change="uploadFavicon"
                        />
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Color Scheme -->
                <div class="card mb-3">
                  <div class="card-header pb-0">
                    <h6>Color Scheme</h6>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-6 mb-3">
                        <label class="form-label">Primary Color</label>
                        <input
                          v-model="theme.color_primary"
                          type="color"
                          class="form-control form-control-color"
                        />
                        <small class="text-muted">{{ theme.color_primary }}</small>
                      </div>
                      <div class="col-6 mb-3">
                        <label class="form-label">Secondary Color</label>
                        <input
                          v-model="theme.color_secondary"
                          type="color"
                          class="form-control form-control-color"
                        />
                        <small class="text-muted">{{ theme.color_secondary }}</small>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-6 mb-3">
                        <label class="form-label">Success Color</label>
                        <input
                          v-model="theme.color_success"
                          type="color"
                          class="form-control form-control-color"
                        />
                        <small class="text-muted">{{ theme.color_success }}</small>
                      </div>
                      <div class="col-6 mb-3">
                        <label class="form-label">Danger Color</label>
                        <input
                          v-model="theme.color_danger"
                          type="color"
                          class="form-control form-control-color"
                        />
                        <small class="text-muted">{{ theme.color_danger }}</small>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Button & Link Colors -->
                <div class="card mb-3">
                  <div class="card-header pb-0">
                    <h6>Button & Link Colors</h6>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-6 mb-3">
                        <label class="form-label">Button Primary</label>
                        <input
                          v-model="theme.button_primary_color"
                          type="color"
                          class="form-control form-control-color"
                        />
                        <small class="text-muted">{{ theme.button_primary_color }}</small>
                      </div>
                      <div class="col-6 mb-3">
                        <label class="form-label">Button Secondary</label>
                        <input
                          v-model="theme.button_secondary_color"
                          type="color"
                          class="form-control form-control-color"
                        />
                        <small class="text-muted">{{ theme.button_secondary_color }}</small>
                      </div>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Link Color</label>
                      <input
                        v-model="theme.link_color"
                        type="color"
                        class="form-control form-control-color"
                      />
                      <small class="text-muted">{{ theme.link_color }}</small>
                    </div>
                  </div>
                </div>

                <!-- Text & Background Colors -->
                <div class="card mb-3">
                  <div class="card-header pb-0">
                    <h6>Text & Background Colors</h6>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-6 mb-3">
                        <label class="form-label">Text Primary</label>
                        <input
                          v-model="theme.text_primary_color"
                          type="color"
                          class="form-control form-control-color"
                        />
                        <small class="text-muted">{{ theme.text_primary_color }}</small>
                      </div>
                      <div class="col-6 mb-3">
                        <label class="form-label">Text Secondary</label>
                        <input
                          v-model="theme.text_secondary_color"
                          type="color"
                          class="form-control form-control-color"
                        />
                        <small class="text-muted">{{ theme.text_secondary_color }}</small>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-6 mb-3">
                        <label class="form-label">Navbar Background</label>
                        <input
                          v-model="theme.navbar_bg_color"
                          type="color"
                          class="form-control form-control-color"
                        />
                        <small class="text-muted">{{ theme.navbar_bg_color }}</small>
                      </div>
                      <div class="col-6 mb-3">
                        <label class="form-label">Dashboard Background</label>
                        <input
                          v-model="theme.dashboard_bg_color"
                          type="color"
                          class="form-control form-control-color"
                        />
                        <small class="text-muted">{{ theme.dashboard_bg_color }}</small>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Sidebar -->
                <div class="card mb-3">
                  <div class="card-header pb-0">
                    <h6>Sidebar Settings</h6>
                  </div>
                  <div class="card-body">
                    <div class="mb-3">
                      <label class="form-label">Background Color</label>
                      <input
                        v-model="theme.sidebar_bg_color"
                        type="color"
                        class="form-control form-control-color"
                      />
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Text Color</label>
                      <input
                        v-model="theme.sidebar_text_color"
                        type="color"
                        class="form-control form-control-color"
                      />
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Active Item Color</label>
                      <input
                        v-model="theme.sidebar_active_color"
                        type="color"
                        class="form-control form-control-color"
                      />
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Sidebar Type</label>
                      <select v-model="theme.sidebar_type" class="form-control">
                        <option value="transparent">Transparent</option>
                        <option value="white">White</option>
                        <option value="dark">Dark</option>
                      </select>
                    </div>
                  </div>
                </div>

                <!-- Layout & UI -->
                <div class="card mb-3">
                  <div class="card-header pb-0">
                    <h6>Layout & UI</h6>
                  </div>
                  <div class="card-body">
                    <div class="mb-3">
                      <label class="form-label">Font Family</label>
                      <input v-model="theme.font_family" type="text" class="form-control" />
                    </div>
                    <div class="row">
                       <div class="col-6 mb-3">
                          <label class="form-label">Border Radius</label>
                          <input v-model="theme.border_radius" type="text" class="form-control" placeholder="8px" />
                       </div>
                       <div class="col-6 mb-3">
                          <label class="form-label">Font Size</label>
                          <input v-model="theme.font_size" type="text" class="form-control" placeholder="16px" />
                       </div>
                    </div>
                    <div class="form-check form-switch mb-2">
                       <input v-model="theme.dark_mode_default" class="form-check-input" type="checkbox" id="darkModeDefault" />
                       <label class="form-check-label" for="darkModeDefault">Dark Mode Default</label>
                    </div>
                    <div class="form-check form-switch mb-2">
                       <input v-model="theme.sidebar_mini" class="form-check-input" type="checkbox" id="sidebarMini" />
                       <label class="form-check-label" for="sidebarMini">Sidebar Mini</label>
                    </div>
                  </div>
                </div>

                <!-- Custom CSS -->
                <div class="card mb-3">
                  <div class="card-header pb-0">
                    <h6>Custom CSS</h6>
                  </div>
                  <div class="card-body">
                    <div class="mb-3">
                      <label class="form-label">Additional CSS</label>
                      <textarea
                        v-model="theme.customCSS"
                        class="form-control font-monospace"
                        rows="6"
                        placeholder="/* Custom CSS here */"
                      ></textarea>
                      <small class="text-muted"
                        >Advanced: Add custom CSS rules</small
                      >
                    </div>
                  </div>
                </div>
              </div>

              <!-- Right Panel: Live Preview -->
              <div class="col-md-7">
                <div class="card bg-light" style="position: sticky; top: 20px">
                  <div class="card-header">
                    <h6>Live Preview</h6>
                  </div>
                  <div class="card-body p-0">
                    <!-- Mini Dashboard Preview -->
                    <div class="preview-container" :style="previewStyles">
                      <!-- Sidebar Preview -->
                      <div class="preview-sidebar" :style="sidebarStyles">
                        <div class="px-3 py-2">
                          <img
                            v-if="theme.logo_light && theme.use_custom_logo"
                            :src="theme.logo_light"
                            style="max-height: 40px; max-width: 100%; object-fit: contain"
                            alt="Logo"
                          />
                          <div v-else class="fw-bold" :style="{ color: theme.sidebar_text_color || '#fff' }">
                            {{ theme.system_name || 'ISP Billing Pro' }}
                          </div>
                        </div>
                        <div class="nav flex-column px-2">
                          <div
                            class="nav-item px-3 py-2 mb-1"
                            :style="sidebarItemStyles"
                          >
                            <i class="fas fa-home me-2"></i>Dashboard
                          </div>
                          <div
                            class="nav-item px-3 py-2 mb-1 active"
                            :style="sidebarActiveItemStyles"
                          >
                            <i class="fas fa-users me-2"></i>Customers
                          </div>
                          <div
                            class="nav-item px-3 py-2 mb-1"
                            :style="sidebarItemStyles"
                          >
                            <i class="fas fa-file-invoice me-2"></i>Billing
                          </div>
                        </div>
                      </div>

                      <!-- Main Content Preview -->
                      <div class="preview-content">
                        <!-- Navbar Preview -->
                        <div
                          class="preview-navbar"
                          style="
                            background: white;
                            padding: 1rem;
                            border-bottom: 1px solid #e9ecef;
                          "
                        >
                          <div
                            class="d-flex justify-content-between align-items-center"
                          >
                            <h6 class="mb-0">Dashboard</h6>
                            <div class="d-flex gap-2">
                              <span
                                class="badge"
                                :style="{
                                  backgroundColor: theme.color_primary,
                                }"
                                >Primary</span
                              >
                              <span
                                class="badge"
                                :style="{
                                  backgroundColor: theme.color_success,
                                }"
                                >Success</span
                              >
                            </div>
                          </div>
                        </div>

                        <!-- Cards Preview -->
                        <div class="p-3">
                          <div class="row">
                            <div class="col-6 mb-3">
                              <div class="card">
                                <div class="card-body">
                                  <div class="d-flex justify-content-between">
                                    <div>
                                      <p
                                        class="text-sm mb-0 text-uppercase font-weight-bold"
                                      >
                                        Total Users
                                      </p>
                                      <h5 class="font-weight-bolder mb-0">
                                        2,300
                                      </h5>
                                    </div>
                                    <div
                                      class="icon icon-shape"
                                      :style="{
                                        backgroundColor: theme.color_primary,
                                        width: '48px',
                                        height: '48px',
                                        borderRadius: theme.border_radius,
                                        display: 'flex',
                                        alignItems: 'center',
                                        justifyContent: 'center',
                                      }"
                                    >
                                      <i class="fas fa-users text-white"></i>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="col-6 mb-3">
                              <div class="card">
                                <div class="card-body">
                                  <div class="d-flex justify-content-between">
                                    <div>
                                      <p
                                        class="text-sm mb-0 text-uppercase font-weight-bold"
                                      >
                                        Revenue
                                      </p>
                                      <h5 class="font-weight-bolder mb-0">
                                        $45k
                                      </h5>
                                    </div>
                                    <div
                                      class="icon icon-shape"
                                      :style="{
                                        backgroundColor: theme.color_success,
                                        width: '48px',
                                        height: '48px',
                                        borderRadius: theme.border_radius,
                                        display: 'flex',
                                        alignItems: 'center',
                                        justifyContent: 'center',
                                      }"
                                    >
                                      <i
                                        class="fas fa-dollar-sign text-white"
                                      ></i>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>

                          <!-- Table Preview -->
                          <div class="card">
                            <div class="card-header pb-0">
                              <h6>Recent Customers</h6>
                            </div>
                            <div class="card-body px-0 pt-0 pb-2">
                              <table class="table align-items-center mb-0">
                                <thead>
                                  <tr>
                                    <th
                                      class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                    >
                                      Name
                                    </th>
                                    <th
                                      class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                    >
                                      Status
                                    </th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <td class="ps-4">
                                      <span class="text-xs">John Doe</span>
                                    </td>
                                    <td>
                                      <span
                                        class="badge badge-sm"
                                        :style="{
                                          backgroundColor: theme.color_success,
                                        }"
                                        >Active</span
                                      >
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                          </div>

                          <!-- Buttons Preview -->
                          <div class="card mt-3">
                            <div class="card-body">
                              <h6 class="mb-3">Button Styles</h6>
                              <div class="d-flex gap-2 flex-wrap">
                                <button
                                  class="btn btn-sm"
                                  :style="{
                                    backgroundColor: theme.color_primary,
                                    color: 'white',
                                    borderRadius: theme.border_radius,
                                  }"
                                >
                                  Primary
                                </button>
                                <button
                                  class="btn btn-sm"
                                  :style="{
                                    backgroundColor: theme.color_success,
                                    color: 'white',
                                    borderRadius: theme.border_radius,
                                  }"
                                >
                                  Success
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
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from "vue";
import axios from "axios";

const loading = ref(false);
const API_URL = "http://localhost:8000/api";
const themeId = ref(null);

const theme = reactive({
  system_name: "ISP Billing Pro",
  name: "Default Theme",
  logo_light: "https://demos.creative-tim.com/argon-dashboard-pro/assets/img/logo-ct.png",
  logo_dark: "https://demos.creative-tim.com/argon-dashboard-pro/assets/img/logo-ct-dark.png",
  favicon: "",
  color_primary: "#5e72e4",
  color_secondary: "#8392ab",
  color_success: "#2dce89",
  color_danger: "#f5365c",
  color_warning: "#fb6340",
  color_info: "#11cdef",
  sidebar_bg_color: "#172b4d",
  sidebar_text_color: "#8898aa",
  sidebar_active_color: "#5e72e4",
  sidebar_type: "dark",
  font_family: "Open Sans",
  font_size: "16px",
  border_radius: "8px",
  button_style: "rounded",
  card_shadow: "shadow-sm",
  dark_mode_default: false,
  sidebar_mini: false,
  fixed_navbar: true,
  custom_css: "",
  custom_js: "",
  is_active: true,
  is_default: true,
  // New extended theme fields
  button_primary_color: "#2dce89",
  button_secondary_color: "#11cdef",
  link_color: "#5e72e4",
  text_primary_color: "#344767",
  text_secondary_color: "#8392ab",
  navbar_bg_color: "#ffffff",
  dashboard_bg_color: "#f8f9fa",
  use_custom_logo: false,
  company_name: "",
});

const previewStyles = computed(() => ({
  fontFamily: theme.font_family,
  fontSize: theme.font_size,
  height: "600px",
  overflow: "hidden",
  display: "flex",
}));

const sidebarStyles = computed(() => ({
  backgroundColor: theme.sidebar_bg_color,
  color: theme.sidebar_text_color,
  width: "240px",
  height: "100%",
  paddingTop: "1rem",
}));

const sidebarItemStyles = computed(() => ({
  color: theme.sidebar_text_color,
  borderRadius: theme.border_radius,
  cursor: "pointer",
}));

const sidebarActiveItemStyles = computed(() => ({
  backgroundColor: theme.sidebar_active_color,
  color: "white",
  borderRadius: theme.border_radius,
  cursor: "pointer",
}));

const fetchTheme = async () => {
  try {
    loading.value = true;
    const token = localStorage.getItem("auth_token") || sessionStorage.getItem("auth_token");
    const response = await axios.get(`${API_URL}/super-admin/isp-themes/default`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    
    if (response.data) {
      themeId.value = response.data.id;
      Object.assign(theme, response.data);
    }
  } catch (error) {
    console.error("Error fetching theme:", error);
    alert("Failed to load theme: " + (error.response?.data?.message || error.message));
  } finally {
    loading.value = false;
  }
};

const saveTheme = async () => {
  try {
    loading.value = true;
    const token = localStorage.getItem("auth_token") || sessionStorage.getItem("auth_token");
    
    if (!token) {
      alert("You are not authenticated. Please login again.");
      return;
    }
    
    if (themeId.value) {
      await axios.put(`${API_URL}/super-admin/isp-themes/${themeId.value}`, theme, {
        headers: { Authorization: `Bearer ${token}` },
      });
    } else {
      const response = await axios.post(`${API_URL}/super-admin/isp-themes`, theme, {
        headers: { Authorization: `Bearer ${token}` },
      });
      themeId.value = response.data.id;
    }
    
    alert("Theme saved successfully!");
    // Reload theme to update preview
    await fetchTheme();
  } catch (error) {
    console.error("Error saving theme:", error);
    alert("Failed to save theme: " + (error.response?.data?.message || error.message));
  } finally {
    loading.value = false;
  }
};

const uploadLogo = (mode, event) => {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      if (mode === "light") {
        theme.logo_light = e.target.result;
      } else {
        theme.logo_dark = e.target.result;
      }
    };
    reader.readAsDataURL(file);
  }
};

const uploadFavicon = (event) => {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      theme.favicon = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const applySneatPreset = () => {
    Object.assign(theme, {
      color_primary: "#696cff",
      color_secondary: "#8592a3",
      color_success: "#71dd37",
      color_danger: "#ff3e1d",
      color_warning: "#ffab00",
      color_info: "#03c3ec",
      sidebar_bg_color: "#ffffff",
      sidebar_text_color: "#697a8d",
      sidebar_active_color: "#696cff",
      sidebar_type: "white",
      font_family: "'Public Sans', sans-serif",
      font_size: "15px",
      border_radius: "6px",
      button_style: "rounded",
      card_shadow: "0 2px 6px 0 rgba(67, 89, 113, 0.12)",
      dark_mode_default: false,
      sidebar_mini: false,
      custom_css: `
/* Sneat style shadows and spacing */
.card { box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.12); border: none; }
.btn { box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.12); font-weight: 500; text-transform: none; }
      `
    });
};

const resetToDefault = () => {
  if (confirm("Reset semua settings ke default?")) {
    Object.assign(theme, {
      color_primary: "#5e72e4",
      color_secondary: "#8392ab",
      color_success: "#2dce89",
      sidebar_bg_color: "#172b4d",
      sidebar_text_color: "#8898aa",
      sidebar_active_color: "#5e72e4",
      sidebar_type: "dark",
      font_family: "Open Sans",
      font_size: "16px",
      border_radius: "8px",
      button_style: "rounded",
    });
  }
};

onMounted(() => {
  fetchTheme();
});
</script>

<style scoped>
.preview-container {
  position: relative;
  background: #f8f9fa;
}

.preview-sidebar {
  flex-shrink: 0;
  overflow-y: auto;
}

.preview-content {
  flex: 1;
  overflow-y: auto;
  background: #f8f9fa;
}

.form-control-color {
  width: 100%;
  height: 38px;
}

.font-monospace {
  font-family: "Courier New", monospace;
  font-size: 13px;
}

.nav-item {
  font-size: 14px;
  transition: all 0.2s;
}

.nav-item:hover {
  opacity: 0.8;
}
</style>
