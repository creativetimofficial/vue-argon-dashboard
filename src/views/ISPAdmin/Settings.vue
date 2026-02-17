<template>
  <div>
    <h4 class="py-3 mb-4">
      <span class="text-muted fw-light">ISP Admin /</span> Pengaturan
    </h4>

    <!-- Settings Tabs -->
    <div class="row">
      <div class="col-md-12">
        <div class="card mb-4">
          <div class="card-body">
            <ul class="nav nav-pills mb-4" role="tablist">
              <li class="nav-item">
                <button
                  type="button"
                  class="nav-link active"
                  role="tab"
                  data-bs-toggle="tab"
                  data-bs-target="#profile"
                >
                  <i class="bx bx-user me-1"></i>Profil
                </button>
              </li>
              <li class="nav-item">
                <button
                  type="button"
                  class="nav-link"
                  role="tab"
                  data-bs-toggle="tab"
                  data-bs-target="#company"
                >
                  <i class="bx bx-building me-1"></i>Perusahaan
                </button>
              </li>
              <li class="nav-item">
                <button
                  type="button"
                  class="nav-link"
                  role="tab"
                  data-bs-toggle="tab"
                  data-bs-target="#security"
                >
                  <i class="bx bx-lock me-1"></i>Keamanan
                </button>
              </li>
              <li class="nav-item">
                <button
                  type="button"
                  class="nav-link"
                  role="tab"
                  data-bs-toggle="tab"
                  data-bs-target="#theme"
                >
                  <i class="bx bx-palette me-1"></i>Tampilan
                </button>
              </li>
            </ul>

            <div class="tab-content">
              <!-- Profile Tab -->
              <div class="tab-pane fade show active" id="profile" role="tabpanel">
                <form @submit.prevent="updateProfile">
                  <div class="row">
                    <div class="col-md-6 mb-4">
                      <label class="form-label">Nama Lengkap</label>
                      <input type="text" class="form-control" v-model="profileData.name" placeholder="Nama lengkap" />
                    </div>
                    <div class="col-md-6 mb-4">
                      <label class="form-label">Email</label>
                      <input type="email" class="form-control" v-model="profileData.email" placeholder="Email" disabled />
                    </div>
                    <div class="col-md-6 mb-4">
                      <label class="form-label">No. Telepon</label>
                      <input type="tel" class="form-control" v-model="profileData.phone" placeholder="No. telepon" />
                    </div>
                    <div class="col-12">
                      <button type="submit" class="btn btn-primary" :disabled="loading">Simpan Perubahan</button>
                    </div>
                  </div>
                </form>
              </div>

              <!-- Company Tab -->
              <div class="tab-pane fade" id="company" role="tabpanel">
                <form @submit.prevent="updateCompany">
                  <div class="row">
                    <div class="col-md-6 mb-4">
                      <label class="form-label">Nama Perusahaan</label>
                      <input type="text" class="form-control" v-model="themeData.company_name" placeholder="Nama perusahaan" />
                    </div>
                    <div class="col-md-6 mb-4">
                    </div>
                    <div class="col-md-12 mb-4">
                      <label class="form-label">Alamat</label>
                      <textarea class="form-control" rows="3" v-model="companyAddress" placeholder="Alamat lengkap"></textarea>
                    </div>
                    <div class="col-12">
                      <button type="submit" class="btn btn-primary" :disabled="loading">Simpan Perubahan</button>
                    </div>
                  </div>
                </form>
              </div>

              <!-- Security Tab -->
              <div class="tab-pane fade" id="security" role="tabpanel">
                <form @submit.prevent="updatePassword">
                  <div class="row">
                    <div class="col-md-6 mb-4">
                      <label class="form-label">Password Lama</label>
                      <input type="password" class="form-control" v-model="passwordData.current_password" />
                    </div>
                    <div class="col-md-6 mb-4"></div>
                    <div class="col-md-6 mb-4">
                      <label class="form-label">Password Baru</label>
                      <input type="password" class="form-control" v-model="passwordData.password" />
                    </div>
                    <div class="col-md-6 mb-4">
                      <label class="form-label">Konfirmasi Password Baru</label>
                      <input type="password" class="form-control" v-model="passwordData.password_confirmation" />
                    </div>
                    <div class="col-12">
                      <button type="submit" class="btn btn-primary" :disabled="loading">Ubah Password</button>
                    </div>
                  </div>
                </form>
              </div>

               <!-- Theme Tab -->
              <div class="tab-pane fade" id="theme" role="tabpanel">
                 <form @submit.prevent="updateTheme">
                    <div class="row mb-4">
                        <div class="col-12 mb-4">
                            <h6 class="fw-semibold">Template Landing Page</h6>
                            <p class="text-muted small">Pilih tampilan halaman depan untuk pelanggan Anda.</p>
                        </div>
                        
                        <!-- Sneat Theme -->
                        <div class="col-md-4 mb-4">
                            <label class="form-check-label custom-option-content p-1" for="themeSneat">
                                <input 
                                    name="themeRadio" 
                                    class="form-check-input d-none" 
                                    type="radio" 
                                    value="sneat" 
                                    id="themeSneat" 
                                    v-model="themeData.landing_page_template"
                                />
                                <div class="card h-100 border-2" :class="themeData.landing_page_template == 'sneat' ? 'border-primary' : ''">
                                    <div class="card-body text-center">
                                        <div class="theme-preview bg-light rounded mb-3 d-flex align-items-center justify-content-center" style="height: 120px;">
                                            <i class='bx bx-layout fs-1 text-secondary'></i>
                                        </div>
                                        <h5 class="fw-bold mb-1">Sneat</h5>
                                        <small class="text-muted">Clean & Minimalist</small>
                                    </div>
                                    <div class="card-footer bg-transparent border-top-0 text-center" v-if="themeData.landing_page_template == 'sneat'">
                                        <span class="badge bg-primary">Terpilih</span>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- Modern Theme -->
                        <div class="col-md-4 mb-4">
                             <label class="form-check-label custom-option-content p-1" for="themeModern">
                                <input 
                                    name="themeRadio" 
                                    class="form-check-input d-none" 
                                    type="radio" 
                                    value="modern" 
                                    id="themeModern" 
                                    v-model="themeData.landing_page_template"
                                />
                                <div class="card h-100 border-2" :class="themeData.landing_page_template == 'modern' ? 'border-primary' : ''">
                                    <div class="card-body text-center">
                                         <div class="theme-preview bg-white border rounded mb-3 d-flex align-items-center justify-content-center" style="height: 120px;">
                                            <i class='bx bx-buildings fs-1 text-primary'></i>
                                        </div>
                                        <h5 class="fw-bold mb-1">Modern</h5>
                                        <small class="text-muted">Corporate & Professional</small>
                                    </div>
                                    <div class="card-footer bg-transparent border-top-0 text-center" v-if="themeData.landing_page_template == 'modern'">
                                        <span class="badge bg-primary">Terpilih</span>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- Creative Theme -->
                        <div class="col-md-4 mb-4">
                             <label class="form-check-label custom-option-content p-1" for="themeCreative">
                                <input 
                                    name="themeRadio" 
                                    class="form-check-input d-none" 
                                    type="radio" 
                                    value="creative" 
                                    id="themeCreative" 
                                    v-model="themeData.landing_page_template"
                                />
                                <div class="card h-100 border-2" :class="themeData.landing_page_template == 'creative' ? 'border-primary' : ''">
                                    <div class="card-body text-center">
                                         <div class="theme-preview bg-dark rounded mb-3 d-flex align-items-center justify-content-center" style="height: 120px;">
                                            <i class='bx bx-paint fs-1 text-info'></i>
                                        </div>
                                        <h5 class="fw-bold mb-1">Creative</h5>
                                        <small class="text-muted">Vibrant & Dynamic</small>
                                    </div>
                                    <div class="card-footer bg-transparent border-top-0 text-center" v-if="themeData.landing_page_template == 'creative'">
                                        <span class="badge bg-primary">Terpilih</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="row">
                         <div class="col-12">
                            <button type="submit" class="btn btn-primary" :disabled="loading">
                                <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                Simpan Tampilan
                            </button>
                        </div>
                    </div>
                 </form>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { ispAdminAPI } from '@/services/api';

const loading = ref(false);
const profileData = ref({
    name: '',
    email: '',
    phone: '',
});
const companyAddress = ref('');
const passwordData = ref({
    current_password: '',
    password: '',
    password_confirmation: '',
});
const themeData = ref({
    company_name: '',
    landing_page_template: 'sneat',
    // colors...
});

onMounted(async () => {
    fetchProfile();
    fetchTheme();
});

const fetchProfile = async () => {
    try {
        const response = await ispAdminAPI.getDashboard(); // user info is in dashboard response or we need a profile endpoint
        if (response.data && response.data.user) {
            profileData.value = {
                name: response.data.user.name,
                email: response.data.user.email,
                phone: response.data.user.phone, // Assuming phone is on user
            };
            if (response.data.isp) {
                themeData.value.company_name = response.data.isp.name;
                companyAddress.value = response.data.isp.address;
            }
        }
    } catch (error) {
        console.error("Failed to fetch profile", error);
    }
};

const fetchTheme = async () => {
    try {
        const response = await ispAdminAPI.getTheme();
        if (response.data) {
            themeData.value = {
                ...themeData.value,
                ...response.data, // Merge API data
            };
            // Ensure valid template
            if (!['sneat', 'modern', 'creative'].includes(themeData.value.landing_page_template)) {
                themeData.value.landing_page_template = 'sneat';
            }
        }
    } catch (error) {
        console.error("Failed to fetch theme", error);
    }
};

const updateProfile = async () => {
    // Implement API call
    alert("Update profile not implemented yet");
};

const updateCompany = async () => {
    // Implement API call for company details
     // Re-using theme update for company name for now as it's part of theme/settings
    updateTheme();
};

const updatePassword = async () => {
     // Implement API call
    alert("Update password not implemented yet");
};

const updateTheme = async () => {
    loading.value = true;
    try {
        await ispAdminAPI.updateTheme(themeData.value);
        alert("Pengaturan tampilan berhasil disimpan!");
    } catch (error) {
        console.error("Failed to update theme", error);
        alert("Gagal menyimpan pengaturan.");
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>
.cursor-pointer {
    cursor: pointer;
}
.theme-preview {
    transition: transform 0.2s;
}
.card:hover .theme-preview {
    transform: scale(1.05);
}
</style>
