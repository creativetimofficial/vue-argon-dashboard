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
                        v-model="theme.branding.systemName"
                        type="text"
                        class="form-control"
                      />
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Logo (Light Mode)</label>
                      <div class="d-flex align-items-center gap-3">
                        <img
                          v-if="theme.branding.logoLight"
                          :src="theme.branding.logoLight"
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
                          v-if="theme.branding.logoDark"
                          :src="theme.branding.logoDark"
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
                          v-if="theme.branding.favicon"
                          :src="theme.branding.favicon"
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
                          v-model="theme.colors.primary"
                          type="color"
                          class="form-control form-control-color"
                        />
                        <small class="text-muted">{{
                          theme.colors.primary
                        }}</small>
                      </div>
                      <div class="col-6 mb-3">
                        <label class="form-label">Secondary Color</label>
                        <input
                          v-model="theme.colors.secondary"
                          type="color"
                          class="form-control form-control-color"
                        />
                        <small class="text-muted">{{
                          theme.colors.secondary
                        }}</small>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-6 mb-3">
                        <label class="form-label">Success Color</label>
                        <input
                          v-model="theme.colors.success"
                          type="color"
                          class="form-control form-control-color"
                        />
                        <small class="text-muted">{{
                          theme.colors.success
                        }}</small>
                      </div>
                      <div class="col-6 mb-3">
                        <label class="form-label">Danger Color</label>
                        <input
                          v-model="theme.colors.danger"
                          type="color"
                          class="form-control form-control-color"
                        />
                        <small class="text-muted">{{
                          theme.colors.danger
                        }}</small>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-6 mb-3">
                        <label class="form-label">Warning Color</label>
                        <input
                          v-model="theme.colors.warning"
                          type="color"
                          class="form-control form-control-color"
                        />
                        <small class="text-muted">{{
                          theme.colors.warning
                        }}</small>
                      </div>
                      <div class="col-6 mb-3">
                        <label class="form-label">Info Color</label>
                        <input
                          v-model="theme.colors.info"
                          type="color"
                          class="form-control form-control-color"
                        />
                        <small class="text-muted">{{
                          theme.colors.info
                        }}</small>
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
                        v-model="theme.sidebar.backgroundColor"
                        type="color"
                        class="form-control form-control-color"
                      />
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Text Color</label>
                      <input
                        v-model="theme.sidebar.textColor"
                        type="color"
                        class="form-control form-control-color"
                      />
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Active Item Color</label>
                      <input
                        v-model="theme.sidebar.activeColor"
                        type="color"
                        class="form-control form-control-color"
                      />
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Sidebar Type</label>
                      <select v-model="theme.sidebar.type" class="form-control">
                        <option value="transparent">Transparent</option>
                        <option value="white">White</option>
                        <option value="dark">Dark</option>
                      </select>
                    </div>
                  </div>
                </div>

                <!-- Typography -->
                <div class="card mb-3">
                  <div class="card-header pb-0">
                    <h6>Typography</h6>
                  </div>
                  <div class="card-body">
                    <div class="mb-3">
                      <label class="form-label">Font Family</label>
                      <select
                        v-model="theme.typography.fontFamily"
                        class="form-control"
                      >
                        <option value="Open Sans">Open Sans (Default)</option>
                        <option value="Roboto">Roboto</option>
                        <option value="Poppins">Poppins</option>
                        <option value="Inter">Inter</option>
                        <option value="Montserrat">Montserrat</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Base Font Size</label>
                      <select
                        v-model="theme.typography.fontSize"
                        class="form-control"
                      >
                        <option value="14px">Small (14px)</option>
                        <option value="16px">Medium (16px)</option>
                        <option value="18px">Large (18px)</option>
                      </select>
                    </div>
                  </div>
                </div>

                <!-- Layout -->
                <div class="card mb-3">
                  <div class="card-header pb-0">
                    <h6>Layout Options</h6>
                  </div>
                  <div class="card-body">
                    <div class="form-check form-switch mb-3">
                      <input
                        v-model="theme.layout.darkModeDefault"
                        class="form-check-input"
                        type="checkbox"
                        id="darkMode"
                      />
                      <label class="form-check-label" for="darkMode"
                        >Dark Mode by Default</label
                      >
                    </div>
                    <div class="form-check form-switch mb-3">
                      <input
                        v-model="theme.layout.sidebarMini"
                        class="form-check-input"
                        type="checkbox"
                        id="sidebarMini"
                      />
                      <label class="form-check-label" for="sidebarMini"
                        >Mini Sidebar</label
                      >
                    </div>
                    <div class="form-check form-switch mb-3">
                      <input
                        v-model="theme.layout.fixedNavbar"
                        class="form-check-input"
                        type="checkbox"
                        id="fixedNavbar"
                      />
                      <label class="form-check-label" for="fixedNavbar"
                        >Fixed Navbar</label
                      >
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
                            v-if="theme.branding.logoLight"
                            :src="theme.branding.logoLight"
                            style="max-height: 40px; max-width: 100%"
                          />
                          <div v-else class="text-white fw-bold">
                            {{ theme.branding.systemName }}
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
                          <div
                            class="nav-item px-3 py-2 mb-1"
                            :style="sidebarItemStyles"
                          >
                            <i class="fas fa-chart-line me-2"></i>Reports
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
                                  backgroundColor: theme.colors.primary,
                                }"
                                >Primary</span
                              >
                              <span
                                class="badge"
                                :style="{
                                  backgroundColor: theme.colors.success,
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
                                        backgroundColor: theme.colors.primary,
                                        width: '48px',
                                        height: '48px',
                                        borderRadius: '12px',
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
                                        backgroundColor: theme.colors.success,
                                        width: '48px',
                                        height: '48px',
                                        borderRadius: '12px',
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
                                          backgroundColor: theme.colors.success,
                                        }"
                                        >Active</span
                                      >
                                    </td>
                                  </tr>
                                  <tr>
                                    <td class="ps-4">
                                      <span class="text-xs">Jane Smith</span>
                                    </td>
                                    <td>
                                      <span
                                        class="badge badge-sm"
                                        :style="{
                                          backgroundColor: theme.colors.warning,
                                        }"
                                        >Pending</span
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
                                    backgroundColor: theme.colors.primary,
                                    color: 'white',
                                  }"
                                >
                                  Primary
                                </button>
                                <button
                                  class="btn btn-sm"
                                  :style="{
                                    backgroundColor: theme.colors.success,
                                    color: 'white',
                                  }"
                                >
                                  Success
                                </button>
                                <button
                                  class="btn btn-sm"
                                  :style="{
                                    backgroundColor: theme.colors.danger,
                                    color: 'white',
                                  }"
                                >
                                  Danger
                                </button>
                                <button
                                  class="btn btn-sm"
                                  :style="{
                                    backgroundColor: theme.colors.warning,
                                    color: 'white',
                                  }"
                                >
                                  Warning
                                </button>
                                <button
                                  class="btn btn-sm"
                                  :style="{
                                    backgroundColor: theme.colors.info,
                                    color: 'white',
                                  }"
                                >
                                  Info
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
import { ref, reactive, computed } from "vue";

const theme = reactive({
  branding: {
    systemName: "ISP Billing Pro",
    logoLight:
      "https://demos.creative-tim.com/argon-dashboard-pro/assets/img/logo-ct.png",
    logoDark:
      "https://demos.creative-tim.com/argon-dashboard-pro/assets/img/logo-ct-dark.png",
    favicon: "",
  },
  colors: {
    primary: "#5e72e4",
    secondary: "#8392ab",
    success: "#2dce89",
    danger: "#f5365c",
    warning: "#fb6340",
    info: "#11cdef",
  },
  sidebar: {
    backgroundColor: "#172b4d",
    textColor: "#8898aa",
    activeColor: "#5e72e4",
    type: "dark",
  },
  typography: {
    fontFamily: "Open Sans",
    fontSize: "16px",
  },
  layout: {
    darkModeDefault: false,
    sidebarMini: false,
    fixedNavbar: true,
  },
  customCSS: "",
});

const previewStyles = computed(() => ({
  fontFamily: theme.typography.fontFamily,
  fontSize: theme.typography.fontSize,
  height: "600px",
  overflow: "hidden",
  display: "flex",
}));

const sidebarStyles = computed(() => ({
  backgroundColor: theme.sidebar.backgroundColor,
  color: theme.sidebar.textColor,
  width: "240px",
  height: "100%",
  paddingTop: "1rem",
}));

const sidebarItemStyles = computed(() => ({
  color: theme.sidebar.textColor,
  borderRadius: "0.5rem",
  cursor: "pointer",
}));

const sidebarActiveItemStyles = computed(() => ({
  backgroundColor: theme.sidebar.activeColor,
  color: "white",
  borderRadius: "0.5rem",
  cursor: "pointer",
}));

const uploadLogo = (mode, event) => {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      if (mode === "light") {
        theme.branding.logoLight = e.target.result;
      } else {
        theme.branding.logoDark = e.target.result;
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
      theme.branding.favicon = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const resetToDefault = () => {
  if (confirm("Reset semua settings ke default?")) {
    Object.assign(theme, {
      branding: {
        systemName: "ISP Billing Pro",
        logoLight:
          "https://demos.creative-tim.com/argon-dashboard-pro/assets/img/logo-ct.png",
        logoDark:
          "https://demos.creative-tim.com/argon-dashboard-pro/assets/img/logo-ct-dark.png",
        favicon: "",
      },
      colors: {
        primary: "#5e72e4",
        secondary: "#8392ab",
        success: "#2dce89",
        danger: "#f5365c",
        warning: "#fb6340",
        info: "#11cdef",
      },
      sidebar: {
        backgroundColor: "#172b4d",
        textColor: "#8898aa",
        activeColor: "#5e72e4",
        type: "dark",
      },
      typography: {
        fontFamily: "Open Sans",
        fontSize: "16px",
      },
      layout: {
        darkModeDefault: false,
        sidebarMini: false,
        fixedNavbar: true,
      },
      customCSS: "",
    });
  }
};

const saveTheme = () => {
  // TODO: API call to save theme
  console.log("Saving theme:", theme);
  alert("Theme saved successfully! ISP Admin panels will use this theme.");
};
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
