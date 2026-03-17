<template>
  <div class="py-4 container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header pb-0">
            <div class="d-flex align-items-center">
              <h6 class="mb-0">{{ $t('dashboard.profile.title') }}</h6>
            </div>
          </div>
          <div class="card-body">
            <p class="text-uppercase text-sm">{{ $t('dashboard.profile.user_info') }}</p>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="name" class="form-control-label">{{ $t('dashboard.profile.full_name') }}</label>
                  <input
                    id="name"
                    v-model="profile.name"
                    class="form-control"
                    type="text"
                    placeholder="Full Name"
                  />
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="email" class="form-control-label">{{ $t('dashboard.email') }}</label>
                  <input
                    id="email"
                    v-model="profile.email"
                    class="form-control"
                    type="email"
                    placeholder="Email"
                    disabled
                  />
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="phone" class="form-control-label">{{ $t('dashboard.phone') }}</label>
                  <input
                    id="phone"
                    v-model="profile.phone"
                    class="form-control"
                    type="text"
                    placeholder="Phone Number"
                  />
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="role" class="form-control-label">{{ $t('dashboard.profile.role') }}</label>
                  <input
                    id="role"
                    v-model="profile.role"
                    class="form-control"
                    type="text"
                    disabled
                  />
                </div>
              </div>
            </div>

            <hr class="horizontal dark" />

            <p class="text-uppercase text-sm">{{ $t('dashboard.profile.change_password') }}</p>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="current_password" class="form-control-label">{{ $t('dashboard.profile.current_password') }}</label>
                  <input
                    id="current_password"
                    v-model="passwordForm.current_password"
                    class="form-control"
                    type="password"
                    placeholder="Current Password"
                  />
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="new_password" class="form-control-label">{{ $t('dashboard.profile.new_password') }}</label>
                  <input
                    id="new_password"
                    v-model="passwordForm.new_password"
                    class="form-control"
                    type="password"
                    placeholder="New Password"
                  />
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="confirm_password" class="form-control-label">{{ $t('dashboard.profile.confirm_password') }}</label>
                  <input
                    id="confirm_password"
                    v-model="passwordForm.confirm_password"
                    class="form-control"
                    type="password"
                    placeholder="Confirm Password"
                  />
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
              <button
                type="button"
                class="btn btn-sm btn-primary me-2"
                @click="updateProfile"
                :disabled="loading"
              >
                <span v-if="loading" class="spinner-border spinner-border-sm me-1"></span>
                {{ $t('dashboard.profile.update_profile') }}
              </button>
              <button
                type="button"
                class="btn btn-sm btn-secondary"
                @click="updatePassword"
                :disabled="loading || !passwordForm.current_password"
              >
                <span v-if="loading" class="spinner-border spinner-border-sm me-1"></span>
                {{ $t('dashboard.profile.change_password') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import notify from '@/utils/notify';

export default {
  name: 'SuperAdminProfile',
  setup() {
    const profile = ref({
      name: '',
      email: '',
      phone: '',
      role: 'Super Admin',
    });

    const passwordForm = ref({
      current_password: '',
      new_password: '',
      confirm_password: '',
    });

    const loading = ref(false);

    onMounted(() => {
      loadProfile();
    });

    const loadProfile = () => {
      const userStr = localStorage.getItem('user');
      if (userStr) {
        const user = JSON.parse(userStr);
        profile.value = {
          name: user.name || '',
          email: user.email || '',
          phone: user.phone || '',
          role: user.role === 'super_admin' ? 'Super Admin' : user.role,
        };
      }
    };

    const updateProfile = async () => {
      loading.value = true;
      try {
        const token = localStorage.getItem('auth_token');
        const response = await axios.put(
          `${import.meta.env.VITE_API_URL}/auth/profile`,
          {
            name: profile.value.name,
            phone: profile.value.phone,
          },
          {
            headers: { Authorization: `Bearer ${token}` },
          }
        );

        // Update localStorage
        const user = JSON.parse(localStorage.getItem('user'));
        user.name = profile.value.name;
        user.phone = profile.value.phone;
        localStorage.setItem('user', JSON.stringify(user));

        notify('success', 'Profile Updated', 'Profile updated successfully!');
      } catch (error) {
        console.error('Error updating profile:', error);
        notify('error', 'Error', error.response?.data?.message || 'Failed to update profile');
      } finally {
        loading.value = false;
      }
    };

    const updatePassword = async () => {
      if (passwordForm.value.new_password !== passwordForm.value.confirm_password) {
        notify('warning', 'Mismatch', 'New password and confirm password do not match!');
        return;
      }

      if (passwordForm.value.new_password.length < 8) {
        notify('warning', 'Invalid Password', 'Password must be at least 8 characters!');
        return;
      }

      loading.value = true;
      try {
        const token = localStorage.getItem('auth_token');
        await axios.put(
          `${import.meta.env.VITE_API_URL}/auth/password`,
          {
            current_password: passwordForm.value.current_password,
            new_password: passwordForm.value.new_password,
          },
          {
            headers: { Authorization: `Bearer ${token}` },
          }
        );

        // Clear password form
        passwordForm.value = {
          current_password: '',
          new_password: '',
          confirm_password: '',
        };

        notify('success', 'Success', 'Password changed successfully!');
      } catch (error) {
        console.error('Error changing password:', error);
        notify('error', 'Error', error.response?.data?.message || 'Failed to change password');
      } finally {
        loading.value = false;
      }
    };

    return {
      profile,
      passwordForm,
      loading,
      updateProfile,
      updatePassword,
    };
  },
};
</script>
