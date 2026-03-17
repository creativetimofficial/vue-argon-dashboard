<template>
  <v-container fluid class="pa-6">
    <div class="d-flex align-center justify-space-between mb-6">
      <h4 class="text-h4 font-weight-bold mb-0">
        <span class="text-medium-emphasis font-weight-light">ISP Admin /</span> Mikrotik Management
      </h4>
      <v-btn color="primary" prepend-icon="bx bx-plus" @click="openModal('add')">
        Add Router
      </v-btn>
    </div>

    <!-- Routers Table -->
    <v-card >
      <v-card-title class="pa-4 d-flex align-center">
        Mikrotik Router List
      </v-card-title>
      <v-divider></v-divider>
      <v-table hover>
        <thead>
          <tr>
            <th class="text-left font-weight-bold">Router Name</th>
            <th class="text-left font-weight-bold">IP Address</th>
            <th class="text-left font-weight-bold">Location</th>
            <th class="text-left font-weight-bold">Status</th>
            <th class="text-left font-weight-bold">Capacity</th>
            <th class="text-left font-weight-bold" width="100">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="6" class="text-center py-10">
              <v-progress-circular indeterminate color="primary"></v-progress-circular>
            </td>
          </tr>
          <tr v-else-if="routers.length === 0">
            <td colspan="6" class="text-center py-10">
              <v-icon size="48" color="medium-emphasis" class="mb-2">bx bx-server</v-icon>
              <p class="text-medium-emphasis mb-0">No routers registered yet</p>
            </td>
          </tr>
          <tr v-for="router in routers" :key="router.id">
            <td>
              <div class="font-weight-medium text-primary">{{ router.name }}</div>
            </td>
            <td><code>{{ router.ip }}</code></td>
            <td>{{ router.location || '-' }}</td>
            <td>
              <v-chip size="small" :color="router.status === 'online' ? 'success' : 'error'" variant="tonal">
                {{ router.status === 'online' ? 'Online' : 'Offline' }}
              </v-chip>
            </td>
            <td>
              <span class="text-caption">{{ router.currentUsers || 0 }}/{{ router.maxUsers || 0 }}</span>
              <v-progress-linear :model-value="(router.currentUsers / router.maxUsers) * 100" color="primary" height="6" rounded class="mt-1"></v-progress-linear>
            </td>
            <td>
              <v-btn icon variant="text" size="small">
                <v-icon>bx bx-dots-vertical-rounded</v-icon>
                <v-menu activator="parent" offset-y>
                  <v-list density="compact" class="py-0">
                    <v-list-item @click="openModal('edit', router)">
                      <template v-slot:prepend><v-icon size="small" color="primary" class="mr-2">bx bx-edit-alt</v-icon></template>
                      <v-list-item-title>Edit</v-list-item-title>
                    </v-list-item>
                    <v-list-item @click="confirmDelete(router)">
                      <template v-slot:prepend><v-icon size="small" color="error" class="mr-2">bx bx-trash</v-icon></template>
                      <v-list-item-title class="text-error">Delete</v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </v-btn>
            </td>
          </tr>
        </tbody>
      </v-table>
    </v-card>

    <!-- Modal Form -->
    <v-dialog v-model="modalVisible" max-width="500" persistent :theme="themeName">
      <v-card>
        <v-card-title class="d-flex justify-space-between align-center px-6 py-4 border-b">
           <span class="text-h6">{{ modalMode === 'add' ? 'Add Router' : 'Edit Router' }}</span>
           <v-btn icon="bx bx-x" variant="text" size="small" @click="closeModal"></v-btn>
        </v-card-title>
        
        <v-form @submit.prevent="saveRouter" id="routerForm">
          <v-card-text class="pa-6">
            <v-row>
              <v-col cols="12">
                <v-text-field label="Router Name" v-model="form.name" variant="outlined" density="comfortable" required></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field label="IP Address" v-model="form.ip" placeholder="192.168.1.1" variant="outlined" density="comfortable" required></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field label="Location" v-model="form.location" placeholder="Building A, 2nd Fl" variant="outlined" density="comfortable"></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field label="Username" v-model="form.username" variant="outlined" density="comfortable" required></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field label="Password" type="password" v-model="form.password" variant="outlined" density="comfortable"></v-text-field>
              </v-col>
            </v-row>
          </v-card-text>
          
          <v-divider></v-divider>
          
          <v-card-actions class="pa-4 flex-row-reverse">
             <v-btn color="primary" type="submit" form="routerForm" :loading="saving" :disabled="saving" class="ml-2">
                {{ modalMode === 'add' ? 'Save' : 'Update' }}
             </v-btn>
             <v-btn variant="tonal" color="secondary" @click="closeModal">Cancel</v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useTheme } from 'vuetify';
import { ispAdminAPI } from '@/services/api';
import Swal from 'sweetalert2';

const { name: themeName } = useTheme();
const routers = ref([]);
const loading = ref(true);
const saving = ref(false);
const modalVisible = ref(false);
const modalMode = ref('add');
const selectedId = ref(null);

const form = ref({
  name: '',
  ip: '',
  location: '',
  username: '',
  password: '',
});

onMounted(() => {
  fetchRouters();
});

const fetchRouters = async () => {
  loading.value = true;
  try {
    const response = await ispAdminAPI.getMikrotik();
    routers.value = response.data;
  } catch (err) {
    console.error("Error fetching routers", err);
  } finally {
    loading.value = false;
  }
};

const openModal = (mode, router = null) => {
  modalMode.value = mode;
  if (mode === 'edit' && router) {
    selectedId.value = router.id;
    form.value = { ...router };
  } else {
    selectedId.value = null;
    form.value = { name: '', ip: '', location: '', username: '', password: '' };
  }
  modalVisible.value = true;
};

const closeModal = () => {
  modalVisible.value = false;
};

const saveRouter = async () => {
  saving.value = true;
  try {
    if (modalMode.value === 'add') {
      await ispAdminAPI.createMikrotik(form.value);
    } else {
      await ispAdminAPI.updateMikrotik(selectedId.value, form.value);
    }
    closeModal();
    fetchRouters();
    Swal.fire({ 
       icon: 'success', 
       title: 'Success!', 
       text: 'Router data has been updated.',
       showConfirmButton: false, 
       timer: 1500 
    });
  } catch (err) {
    Swal.fire({
       title: 'Failed!',
       text: 'Failed to save router data.',
       icon: 'error',
       confirmButtonText: 'OK'
    });
  } finally {
    saving.value = false;
  }
};

const confirmDelete = (router) => {
  Swal.fire({
    title: 'Delete Router?',
    text: `Are you sure you want to delete router "${router.name}"?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ff3e1d',
    confirmButtonText: 'Yes, Delete!',
  }).then(async (result) => {
    if (result.isConfirmed) {
      try {
        await ispAdminAPI.deleteMikrotik(router.id);
        fetchRouters();
        Swal.fire({
           title: 'Deleted!',
           text: 'Router has been deleted.',
           icon: 'success',
           timer: 1500,
           showConfirmButton: false
        });
      } catch (err) {
        Swal.fire({
           title: 'Failed!',
           text: 'Failed to delete router.',
           icon: 'error',
           confirmButtonText: 'OK'
        });
      }
    }
  });
};
</script>
