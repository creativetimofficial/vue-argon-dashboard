<template>
  <v-container fluid class="pa-6">
    <div class="d-flex align-center mb-6">
      <h4 class="text-h4 font-weight-bold mb-0">
        <span class="text-medium-emphasis font-weight-light">ISP Admin /</span> System Settings
      </h4>
    </div>

    <!-- Settings Tabs -->
    <v-tabs v-model="activeTab" color="primary" class="mb-6 border-b" show-arrows>
      <v-tab value="account"><v-icon start>bx bx-user</v-icon> Account</v-tab>
      <v-tab value="branding"><v-icon start>bx bx-paint</v-icon> Branding</v-tab>
      <v-tab value="billing"><v-icon start>bx bx-credit-card</v-icon> Payment Gateway</v-tab>
      <v-tab value="landing"><v-icon start>bx bx-layout</v-icon> Landing Page</v-tab>
      <v-tab value="notifications"><v-icon start>bx bx-bell</v-icon> Notifications</v-tab>
      <v-tab value="security"><v-icon start>bx bx-lock-alt</v-icon> Security</v-tab>
    </v-tabs>

    <v-window v-model="activeTab">
      <!-- Account Tab -->
      <v-window-item value="account">
        <v-card>
          <v-card-text class="pa-6">
            <div class="d-flex align-center mb-6">
              <v-avatar size="100" rounded color="primary" class="mr-4">
                <v-img v-if="profileData.avatar" :src="profileData.avatar"></v-img>
                <span v-else class="text-h3 text-white">{{ profileData.name?.charAt(0) }}</span>
              </v-avatar>
              <div>
                <v-btn color="primary" class="mr-2 px-6" @click="$refs.avatarInput.click()">
                  Upload Profile Photo
                </v-btn>
                <v-btn variant="tonal" color="secondary" @click="resetAvatar">Reset</v-btn>
                <input type="file" ref="avatarInput" class="d-none" accept="image/*" @change="handleAvatarUpload" />
                <div class="text-caption text-medium-emphasis mt-2">JPG, GIF or PNG format. Max 800K</div>
              </div>
            </div>

            <v-divider class="mb-6"></v-divider>

            <v-form @submit.prevent="saveAccount">
              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field label="Full Name" v-model="profileData.name" variant="outlined" density="comfortable"></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field label="Email" v-model="profileData.email" variant="outlined" density="comfortable" disabled></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field label="Phone Number" v-model="profileData.phone" variant="outlined" density="comfortable" prefix="+62"></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field label="Company Name" v-model="themeData.company_name" variant="outlined" density="comfortable"></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-textarea label="Office Address" v-model="companyAddress" variant="outlined" density="comfortable" rows="2"></v-textarea>
                </v-col>
                <v-col cols="12">
                  <v-btn color="primary" type="submit" :loading="loading">Save Changes</v-btn>
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>
        </v-card>
      </v-window-item>

      <!-- Branding Tab -->
      <v-window-item value="branding">
        <v-card>
          <v-card-text class="pa-6">
            <div class="d-flex justify-space-between align-center mb-4">
              <div class="text-h6">ISP Identity & Branding</div>
              <v-chip v-if="!canUseBranding" color="warning" size="small" variant="tonal" prepend-icon="bx bx-lock-alt">Premium Feature</v-chip>
            </div>
            
            <div class="position-relative">
              <v-row :class="{ 'locked-content': !canUseBranding }">
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 mb-2">Logo Dashboard</div>
                    <v-card border flat class="pa-4 d-flex align-center justify-center" min-height="150" color="grey-lighten-4">
                       <v-img v-if="branding.logo" :src="branding.logo" max-height="80" contain></v-img>
                       <v-icon v-else size="48" color="medium-emphasis">bx bx-image-add</v-icon>
                    </v-card>
                    <v-btn block variant="tonal" size="small" class="mt-2" prepend-icon="bx bx-upload" :disabled="!canUseBranding">Upload Logo</v-btn>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 mb-2">Favicon (Ikon Browser)</div>
                    <v-card border flat class="pa-4 d-flex align-center justify-center" min-height="150" color="grey-lighten-4">
                       <v-img v-if="branding.favicon" :src="branding.favicon" width="32" height="32" contain></v-img>
                       <v-icon v-else size="48" color="medium-emphasis">bx bx-globe</v-icon>
                    </v-card>
                    <v-btn block variant="tonal" size="small" class="mt-2" prepend-icon="bx bx-upload" :disabled="!canUseBranding">Upload Favicon</v-btn>
                  </div>
                </v-col>
                <v-col cols="12">
                  <v-select label="Primary Color Theme" :items="['Sneat Blue (Default)', 'Forest Green', 'Royal Purple', 'Sunset Orange']" variant="outlined" density="comfortable" :disabled="!canUseBranding"></v-select>
                </v-col>
                <v-col cols="12">
                  <v-btn color="primary" :disabled="!canUseBranding">Save Branding</v-btn>
                </v-col>
              </v-row>

              <!-- Locked Overlay -->
              <div v-if="!canUseBranding" class="lock-overlay d-flex flex-column align-center justify-center">
                <v-icon size="48" color="primary" class="mb-2">bx bx-lock-alt</v-icon>
                <div class="text-h6 font-weight-bold">Branding Feature Locked</div>
                <p class="text-center text-body-2 mb-4 px-10">Use your own logo and brand colors in the dashboard. Upgrade to the <b>Enterprise</b> plan to unlock this feature.</p>
                <v-btn color="primary" prepend-icon="bx bx-up-arrow-alt" @click="goToUpgrade">Upgrade Plan Now</v-btn>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-window-item>

      <!-- Payment Gateway Tab -->
      <v-window-item value="billing">
        <v-card>
          <v-card-text class="pa-6">
            <div class="text-h6 mb-4">Automatic Payment Integration</div>
            <p class="text-medium-emphasis mb-6">Enable Tripay or Duitku integration so customers can pay bills automatically through Virtual Accounts, E-Wallets, or QRIS.</p>
            
            <v-row>
              <v-col cols="12" md="6">
                <v-card border flat class="pa-4 mb-4 h-100 d-flex flex-column">
                  <div class="d-flex align-center mb-6">
                    <v-img src="https://tripay.co.id/images/logo/logo-tripay.png" height="30" contain class="me-3" style="max-width: 120px"></v-img>
                    <v-spacer></v-spacer>
                    <v-switch color="primary" label="Active" hide-details density="compact"></v-switch>
                  </div>
                  <v-text-field label="Merchant Code" density="comfortable" variant="outlined" class="mb-2"></v-text-field>
                  <v-text-field label="API Key" density="comfortable" variant="outlined" type="password" class="mb-2"></v-text-field>
                  <v-text-field label="Private Key" density="comfortable" variant="outlined" type="password"></v-text-field>
                </v-card>
              </v-col>
              <v-col cols="12" md="6">
                <v-card border flat class="pa-4 h-100 d-flex flex-column">
                   <div class="d-flex align-center mb-6">
                    <v-img src="https://duitku.com/wp-content/uploads/2021/03/logo-duitku.png" height="30" contain class="me-3" style="max-width: 120px"></v-img>
                    <v-spacer></v-spacer>
                    <v-switch color="primary" label="Active" hide-details density="compact"></v-switch>
                  </div>
                  <v-text-field label="Merchant Code" density="comfortable" variant="outlined" class="mb-2"></v-text-field>
                  <v-text-field label="API Key" density="comfortable" variant="outlined" type="password"></v-text-field>
                </v-card>
              </v-col>
              <v-col cols="12">
                <v-btn color="primary" prepend-icon="bx bx-save">Save Configuration</v-btn>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-window-item>

      <!-- Landing Page Tab -->
      <v-window-item value="landing">
        <v-card>
          <v-card-text class="pa-6">
            <div class="text-h6 mb-4">Landing Page Customization</div>
            <p class="text-medium-emphasis mb-6">This page will appear when potential customers access your domain/subdomain address.</p>
            
            <v-row>
              <v-col cols="12" md="4">
                 <v-select label="Template" :items="['Modern Fiber (Default)', 'Dark Tech ISP', 'Minimalist Clean']" variant="outlined" density="comfortable"></v-select>
              </v-col>
              <v-col cols="12" md="8">
                 <v-file-input label="Main Banner (Hero Image)" prepend-inner-icon="bx bx-image" variant="outlined" density="comfortable"></v-file-input>
              </v-col>
              <v-col cols="12">
                 <v-textarea label="Company Description" rows="3" variant="outlined" placeholder="Example: We are the fastest internet provider in your city..."></v-textarea>
              </v-col>
              <v-col cols="12" class="d-flex ga-2">
                <v-btn color="primary" prepend-icon="bx bx-cloud-upload">Publish Page</v-btn>
                <v-btn variant="tonal" color="primary" prepend-icon="bx bx-show">Preview</v-btn>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-window-item>

      <!-- Notifications Tab -->
      <v-window-item value="notifications">
        <v-card>
          <v-card-text class="pa-6">
            <div class="text-h6 mb-6">Pengaturan Notifications Otomatis</div>
            
            <div class="mb-8">
              <div class="text-subtitle-1 font-weight-bold mb-4 d-flex align-center">
                <v-icon color="primary" class="mr-2">bx bx-envelope</v-icon> Email Notifications
              </div>
              <v-list class="pa-0 bg-transparent border rounded">
                <v-list-item>
                  <template v-slot:append><v-switch color="primary" v-model="notifSettings.new_order" hide-details></v-switch></template>
                  <v-list-item-title>New Registration</v-list-item-title>
                  <v-list-item-subtitle>Send email when a customer registers through the portal</v-list-item-subtitle>
                </v-list-item>
                <v-divider></v-divider>
                <v-list-item>
                  <template v-slot:append><v-switch color="primary" v-model="notifSettings.payment" hide-details></v-switch></template>
                  <v-list-item-title>Payment Successful</v-list-item-title>
                  <v-list-item-subtitle>Send payment proof automatically to customer email</v-list-item-subtitle>
                </v-list-item>
              </v-list>
            </div>

            <div class="position-relative mt-6">
              <div class="text-subtitle-1 font-weight-bold mb-4 d-flex align-center" :class="{ 'text-disabled': !canUseWA }">
                <v-icon :color="canUseWA ? 'success' : 'grey'" class="mr-2">bx bxl-whatsapp</v-icon> WhatsApp Gateway (Premium)
              </div>
              
              <div :class="{ 'locked-content': !canUseWA }">
                <v-card border flat class="pa-6">
                  <v-row>
                    <v-col cols="12" md="6">
                      <v-text-field label="WhatsApp API Gateway URL" placeholder="https://api.wa-gateway.com/send" variant="outlined" density="comfortable" :disabled="!canUseWA"></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field label="API Key / Token" type="password" variant="outlined" density="comfortable" :disabled="!canUseWA"></v-text-field>
                    </v-col>
                    <v-col cols="12">
                      <v-checkbox label="Send automatic bill reminder (3 days before due date)" density="compact" hide-details :disabled="!canUseWA"></v-checkbox>
                      <v-checkbox label="Send isolation notification if overdue" density="compact" hide-details :disabled="!canUseWA"></v-checkbox>
                    </v-col>
                  </v-row>
                </v-card>
              </div>

              <!-- Locked Overlay -->
              <div v-if="!canUseWA" class="lock-overlay d-flex flex-column align-center justify-center">
                <v-icon size="40" color="primary" class="mb-2">bx bx-lock-alt</v-icon>
                <div class="font-weight-bold">WhatsApp Gateway Belum Active</div>
                <p class="text-center text-caption mb-3">Send bills and automatic reminders directly to customers' WhatsApp.</p>
                <v-btn color="primary" size="small" @click="goToUpgrade">Unlock This Feature</v-btn>
              </div>
            </div>

            <v-btn color="primary" class="mt-8" @click="saveNotif" :loading="loading">Save Preferences</v-btn>
          </v-card-text>
        </v-card>
      </v-window-item>

      <!-- Security Tab -->
      <v-window-item value="security">
        <v-card>
          <v-card-title class="px-6 pt-6">Change Password</v-card-title>
          <v-card-text class="pa-6">
            <v-form @submit.prevent="savePassword">
              <v-row>
                <v-col cols="12" md="6">
                   <v-text-field label="Current Password" type="password" v-model="passwordData.current_password" variant="outlined" density="comfortable"></v-text-field>
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="12" md="6">
                   <v-text-field label="New Password" type="password" v-model="passwordData.password" variant="outlined" density="comfortable"></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                   <v-text-field label="Konfirmasi New Password" type="password" v-model="passwordData.password_confirmation" variant="outlined" density="comfortable"></v-text-field>
                </v-col>
              </v-row>
              <div class="mt-4">
                <v-btn color="primary" type="submit" :loading="loading">Update Password</v-btn>
              </div>
            </v-form>
          </v-card-text>
        </v-card>
      </v-window-item>
    </v-window>
  </v-container>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { ispAdminAPI } from '@/services/api';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';

const router = useRouter();
const activeTab = ref('account');
const loading = ref(false);

const profileData = ref({
    name: '',
    email: '',
    phone: '',
    avatar: null
});

const companyAddress = ref('');
const passwordData = ref({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const themeData = ref({
    company_name: '',
});

const branding = ref({
    logo: null,
    favicon: null
});

const notifSettings = ref({
  new_order: true,
  payment: true
});

const subscription = ref(null);

// 👉 Computed Feature Checks
const canUseWA = computed(() => subscription.value?.feature_whatsapp_gateway === true);
const canUseBranding = computed(() => subscription.value?.feature_whitelabel === true);

onMounted(async () => {
    fetchProfile();
});

const fetchProfile = async () => {
    try {
        const response = await ispAdminAPI.getDashboard();
        if (response.data) {
            const user = response.data.user;
            profileData.value = {
                name: user?.name || '',
                email: user?.email || '',
                phone: user?.phone || '',
                avatar: user?.avatar || null
            };
            if (response.data.isp) {
                const isp = response.data.isp;
                themeData.value.company_name = isp.company_name || isp.name;
                companyAddress.value = isp.address;
                
                // Load branding
                branding.value = {
                    logo: isp.logo,
                    favicon: isp.favicon
                };

                // Load notification settings
                if (isp.notification_settings) {
                    notifSettings.value = {
                        ...notifSettings.value,
                        ...isp.notification_settings
                    };
                }
            }
            // Load subscription features
            subscription.value = response.data.subscription;
        }
    } catch (error) {
        console.error("Failed to fetch profile", error);
    }
};

const handleAvatarUpload = (e) => {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (event) => {
      profileData.value.avatar = event.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const resetAvatar = () => {
  profileData.value.avatar = null;
};

const saveAccount = async () => {
    loading.value = true;
    try {
        const payload = {
            name: profileData.value.name,
            phone: profileData.value.phone,
            company_name: themeData.value.company_name,
            address: companyAddress.value
        };
        await ispAdminAPI.updateProfile(payload);
        Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: 'Account data has been updated.',
          showConfirmButton: false,
          timer: 1500
        });
    } catch (error) {
        Swal.fire({ title: 'Failed!', text: 'Failed to update account data.', icon: 'error' });
    } finally {
        loading.value = false;
    }
};

const savePassword = async () => {
    if (passwordData.value.password !== passwordData.value.password_confirmation) {
        return Swal.fire({ title: 'Error!', text: 'Password confirmation does not match!', icon: 'error' });
    }
    loading.value = true;
    try {
        await ispAdminAPI.updatePassword(passwordData.value);
        Swal.fire({ title: 'Success!', text: 'Password has been changed.', icon: 'success' });
        passwordData.value = { current_password: '', password: '', password_confirmation: '' };
    } catch (error) {
        Swal.fire({ title: 'Failed!', text: 'Failed to change password.', icon: 'error' });
    } finally {
        loading.value = false;
    }
};

const saveNotif = async () => {
    loading.value = true;
    try {
        await ispAdminAPI.updateNotificationSettings(notifSettings.value);
        Swal.fire({ icon: 'success', title: 'Success!', text: 'Notification preferences saved.', timer: 1500, showConfirmButton: false });
    } catch (err) {
        Swal.fire({ title: 'Failed!', text: 'Failed to save settings.', icon: 'error' });
    } finally {
        loading.value = false;
    }
};

const goToUpgrade = () => {
    router.push('/isp-admin/client-area/packages');
};
</script>

<style scoped>
.ga-2 { gap: 8px; }

/* 👉 Locked Feature Styles */
.locked-content {
  filter: blur(2px);
  opacity: 0.5;
  pointer-events: none;
  user-select: none;
}

.lock-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(var(--v-theme-surface), 0.6);
  backdrop-filter: blur(4px);
  z-index: 5;
  border-radius: 8px;
}

.text-disabled {
  color: rgba(var(--v-theme-on-surface), 0.38) !important;
}
</style>
