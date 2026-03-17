<template>
  <v-container fluid class="pa-6">
    <v-row>
      <v-col cols="12">
        <h4 class="text-h4 mb-4">
          <span class="text-medium-emphasis">ISP Admin /</span> User & Staff Management
        </h4>
      </v-col>
    </v-row>

    <!-- Search and Add -->
    <v-card  class="mb-6">
      <v-card-text>
        <v-row align="center">
          <v-col cols="12" md="8">
            <v-text-field
              v-model="searchQuery"
              prepend-inner-icon="bx-search"
              placeholder="Search name, email, or role..."
              variant="outlined"
              hide-details
              density="comfortable"
            />
          </v-col>
          <v-col cols="12" md="4" class="text-md-end">
            <v-btn  color="primary" prepend-icon="bx-plus" @click="openAddModal">
              Add Staff
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <!-- Staff Table -->
    <v-card >
      <v-card-title class="pa-4">
        <h5 class="text-h5 mb-0">Staff & Admin List</h5>
      </v-card-title>
      
      <v-table hover>
        <thead>
          <tr>
            <th class="text-uppercase text-caption font-weight-bold">Member</th>
            <th class="text-uppercase text-caption font-weight-bold">Role</th>
            <th class="text-uppercase text-caption font-weight-bold">WhatsApp</th>
            <th class="text-uppercase text-caption font-weight-bold">Status</th>
            <th class="text-uppercase text-caption font-weight-bold">Last Login</th>
            <th class="text-uppercase text-caption font-weight-bold text-center">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="6" class="text-center py-10">
              <v-progress-circular indeterminate color="primary" />
            </td>
          </tr>
          <tr v-else-if="filteredStaff.length === 0">
            <td colspan="6" class="text-center py-10">
              <div class="text-medium-emphasis">
                <v-icon size="48" class="mb-2">bx-user-x</v-icon>
                <p>No staff found</p>
              </div>
            </td>
          </tr>
          <tr v-for="member in filteredStaff" :key="member.id">
            <td>
              <div class="d-flex align-items-center">
                <v-avatar color="primary" variant="tonal" size="32" class="me-3">
                  <span class="text-xs font-weight-bold">{{ member.name.charAt(0).toUpperCase() }}</span>
                </v-avatar>
                <div>
                  <div class="font-weight-bold">{{ member.name }}</div>
                  <div class="text-caption text-medium-emphasis">{{ member.email }}</div>
                </div>
              </div>
            </td>
            <td>
              <v-chip
                size="small"
                :color="getRoleColor(member.role)"
                label
                class="text-capitalize"
              >
                {{ getRoleLabel(member.role) }}
              </v-chip>
            </td>
            <td>{{ member.phone || '-' }}</td>
            <td>
              <v-chip
                size="small"
                :color="member.is_active ? 'success' : 'error'"
                variant="tonal"
                label
              >
                {{ member.is_active ? 'Active' : 'Inactive' }}
              </v-chip>
            </td>
            <td class="text-caption">{{ formatDate(member.updated_at) }}</td>
            <td>
              <div class="d-flex justify-center gap-2">
                <v-btn
                  icon
                  size="x-small"
                  color="primary"
                  variant="tonal"
                  @click="openEditModal(member)"
                >
                  <v-icon size="18">bx-edit-alt</v-icon>
                </v-btn>
                <v-btn
                  icon
                  size="x-small"
                  color="error"
                  variant="tonal"
                  @click="confirmDelete(member)"
                  :disabled="member.id === currentUserId"
                >
                  <v-icon size="18">bx-trash</v-icon>
                </v-btn>
              </div>
            </td>
          </tr>
        </tbody>
      </v-table>
    </v-card>

    <!-- Add/Edit Dialog -->
    <v-dialog v-model="dialog" max-width="500px" :theme="themeName">
      <v-card>
        <v-card-title class="pa-4 d-flex justify-space-between align-center">
          <span class="text-h5">{{ isEditing ? 'Edit Staff' : 'Add New Staff' }}</span>
          <v-btn icon="bx-x" variant="text" @click="dialog = false" />
        </v-card-title>
        
        <v-divider />

        <v-form @submit.prevent="saveStaff">
          <v-card-text class="pa-4">
            <v-row no-gutters>
              <v-col cols="12" class="mb-4">
                <v-text-field
                  v-model="form.name"
                  label="Full Name"
                  placeholder="Example: John Doe"
                  variant="outlined"
                  required
                />
              </v-col>
              <v-col cols="12" class="mb-4">
                <v-text-field
                  v-model="form.email"
                  label="Email"
                  placeholder="email@example.com"
                  variant="outlined"
                  type="email"
                  required
                  :readonly="isEditing"
                  :hint="isEditing ? 'Email cannot be changed after registration' : ''"
                  persistent-hint
                />
              </v-col>
              <v-col cols="12" sm="6" class="pe-sm-2 mb-4">
                <v-select
                  v-model="form.role"
                  :items="roleOptions"
                  label="Role"
                  variant="outlined"
                  required
                  :disabled="isEditingSelf"
                  :readonly="isEditingSelf"
                  :hint="isEditingSelf ? 'You cannot change your own role' : ''"
                  persistent-hint
                />
              </v-col>
              <v-col cols="12" sm="6" class="ps-sm-2 mb-4">
                <v-text-field
                  v-model="form.phone"
                  label="WhatsApp"
                  placeholder="081xxx"
                  variant="outlined"
                />
              </v-col>
              <v-col cols="12" class="mb-4">
                <v-text-field
                  v-model="form.password"
                  label="Password"
                  :placeholder="isEditing ? '(Leave blank if not changed)' : 'Min 8 characters'"
                  variant="outlined"
                  type="password"
                  :required="!isEditing"
                  hint="This password will also be used for Client Area login"
                  persistent-hint
                />
              </v-col>
              <v-col cols="12" v-if="isEditing">
                <v-switch
                  v-model="form.is_active"
                  label="Active Account Status"
                  color="primary"
                  hide-details
                  :disabled="isEditingSelf"
                  :readonly="isEditingSelf"
                />
                <div v-if="isEditingSelf" class="text-caption text-error ms-2 mt-n1">
                  You cannot deactivate your own account
                </div>
              </v-col>
            </v-row>
          </v-card-text>

          <v-divider />

          <v-card-actions class="pa-4">
            <v-spacer />
            <v-btn
              variant="tonal"
              color="secondary"
              @click="dialog = false"
            >
              Cancel
            </v-btn>
            <v-btn
              type="submit"
              color="primary"
              :loading="saving"
            >
              Save
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useTheme } from 'vuetify';
import { ispAdminAPI } from '@/services/api';
import { useISPAdminStore } from '@/stores/ispAdmin';
import notify, { confirm } from '@/utils/notify';

const { name: themeName } = useTheme();
const ispAdminStore = useISPAdminStore();
const currentUserId = computed(() => ispAdminStore.user?.id);
const currentUserEmail = computed(() => ispAdminStore.user?.email);

const isEditingSelf = computed(() => {
  if (!isEditing.value) return false;
  // Use both ID and Email for robustness
  return Number(form.value.id) === Number(currentUserId.value) || 
         (form.value.email && form.value.email === currentUserEmail.value);
});

const staff = ref([]);
const loading = ref(true);
const saving = ref(false);
const isEditing = ref(false);
const searchQuery = ref('');
const dialog = ref(false);

const roleOptions = [
  { title: 'Admin', value: 'admin' },
  { title: 'Technician', value: 'technician' },
  { title: 'Owner (Full Access)', value: 'isp_admin' }
];

const form = ref({
  id: null,
  name: '',
  email: '',
  password: '',
  role: 'admin',
  phone: '',
  is_active: true
});

const filteredStaff = computed(() => {
  if (!searchQuery.value) return staff.value;
  const q = searchQuery.value.toLowerCase();
  return staff.value.filter(s => 
    s.name.toLowerCase().includes(q) || 
    s.email.toLowerCase().includes(q) || 
    s.role.toLowerCase().includes(q)
  );
});

onMounted(() => {
  fetchStaff();
});

const fetchStaff = async () => {
  loading.value = true;
  try {
    const response = await ispAdminAPI.getStaff();
    if (response.data?.success) {
      staff.value = response.data.data;
    }
  } catch (err) {
    console.error('Failed to fetch staff:', err);
  } finally {
    loading.value = false;
  }
};

const openAddModal = () => {
  isEditing.value = false;
  form.value = {
    id: null,
    name: '',
    email: '',
    password: '',
    role: 'admin',
    phone: '',
    is_active: true
  };
  dialog.value = true;
};

const openEditModal = (member) => {
  isEditing.value = true;
  form.value = { ...member, password: '' };
  dialog.value = true;
};

const saveStaff = async () => {
  saving.value = true;
  try {
    let response;
    if (isEditing.value) {
      response = await ispAdminAPI.updateStaff(form.value.id, form.value);
    } else {
      response = await ispAdminAPI.createStaff(form.value);
    }

    if (response.data?.success) {
      await fetchStaff();
      dialog.value = false;
    }
  } catch (err) {
    console.error('Failed to save staff:', err);
    notify('error', 'Error', 'Failed to save staff. Ensure email is not already in use.');
  } finally {
    saving.value = false;
  }
};

const confirmDelete = async (member) => {
  if (await confirm('Konfirmasi', `Are you sure you want to delete staff ${member.name}?`, 'warning')) {
    try {
      await ispAdminAPI.deleteStaff(member.id);
      await fetchStaff();
    } catch (err) {
      console.error('Failed to delete staff:', err);
    }
  }
};

const getRoleLabel = (role) => {
  const labels = {
    isp_admin: 'Owner',
    admin: 'Admin',
    technician: 'Technician'
  };
  return labels[role] || role;
};

const getRoleColor = (role) => {
  const colors = {
    isp_admin: 'warning',
    admin: 'primary',
    technician: 'info'
  };
  return colors[role] || 'secondary';
};

const formatDate = (dateStr) => {
  if (!dateStr) return 'Never';
  const date = new Date(dateStr);
  return date.toLocaleDateString('en-US', { 
    day: '2-digit', 
    month: 'short', 
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};
</script>

<style scoped>
.text-xs { font-size: 0.75rem; }
.gap-2 { gap: 8px; }
</style>
