<template>
  <v-container fluid class="pa-6">
    <div class="d-flex align-center justify-space-between mb-6">
      <h4 class="text-h4 font-weight-bold mb-0">
        <span class="text-medium-emphasis font-weight-light">ISP Admin /</span> Internet Packages
      </h4>
      <v-btn color="primary" prepend-icon="bx bx-plus" @click="openModal('add')">
        Add Package
      </v-btn>
    </div>

    <!-- Packages Grid -->
    <v-row v-if="loading">
      <v-col cols="12" class="text-center py-10">
        <v-progress-circular indeterminate color="primary"></v-progress-circular>
      </v-col>
    </v-row>
    <v-row v-else>
      <v-col v-for="pkg in packages" :key="pkg.id" cols="12" md="6" lg="4" class="mb-4">
        <v-card class="h-100" elevation="2">
          <v-card-text>
            <div class="d-flex justify-space-between align-start mb-3">
              <v-chip color="primary" variant="tonal" size="small">{{ pkg.speed }} Mbps</v-chip>
              <v-btn icon variant="text" size="small">
                <v-icon>bx bx-dots-vertical-rounded</v-icon>
                <v-menu activator="parent" offset-y>
                  <v-list density="compact" class="py-0">
                    <v-list-item @click="openModal('edit', pkg)">
                      <template v-slot:prepend><v-icon color="primary" size="small" class="mr-2">bx bx-edit-alt</v-icon></template>
                      <v-list-item-title>Edit</v-list-item-title>
                    </v-list-item>
                    <v-list-item @click="confirmDelete(pkg)">
                      <template v-slot:prepend><v-icon color="error" size="small" class="mr-2">bx bx-trash</v-icon></template>
                      <v-list-item-title class="text-error">Delete</v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </v-btn>
            </div>
            <div class="text-h6 mb-2">{{ pkg.name }}</div>
            <p class="text-body-2 text-medium-emphasis line-clamp-2" style="height: 40px;">{{ pkg.description || 'No description' }}</p>
            <div class="my-4 d-flex align-end">
              <h3 class="text-h4 font-weight-bold mb-0 mr-2">Rp {{ formatNumber(pkg.price) }}</h3>
              <span class="text-caption text-medium-emphasis pb-1">/ month</span>
            </div>
            
            <v-divider class="mb-4"></v-divider>
            
            <v-list density="compact" class="pa-0 bg-transparent">
              <v-list-item class="px-0 min-height-0">
                <template v-slot:prepend><v-icon color="success" size="18" class="mr-2">bx bx-check</v-icon></template>
                <v-list-item-title class="text-body-2">Capacity: {{ pkg.speed }} Mbps</v-list-item-title>
              </v-list-item>
               <v-list-item class="px-0 min-height-0">
                <template v-slot:prepend><v-icon color="success" size="18" class="mr-2">bx bx-check</v-icon></template>
                <v-list-item-title class="text-body-2">Type: {{ pkg.is_unlimited ? 'Unlimited' : 'FUP' }}</v-list-item-title>
              </v-list-item>
               <v-list-item class="px-0 min-height-0">
                <template v-slot:prepend><v-icon color="success" size="18" class="mr-2">bx bx-check</v-icon></template>
                <v-list-item-title class="text-body-2">Shared: {{ pkg.is_shared ? 'Up-to' : 'Dedicated' }}</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-card-text>
        </v-card>
      </v-col>

      <!-- Empty State -->
      <v-col v-if="packages.length === 0" cols="12">
        <v-card  class="text-center py-10" border dashed>
          <v-card-text>
            <v-icon size="64" color="medium-emphasis" class="mb-4">bx bx-package</v-icon>
            <div class="text-h6 text-medium-emphasis mb-4">No internet packages registered yet</div>
            <v-btn color="primary" prepend-icon="bx bx-plus" @click="openModal('add')">
              Add First Package
            </v-btn>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Add/Edit Modal -->
    <v-dialog v-model="modalVisible" max-width="600" persistent :theme="themeName">
      <v-card>
        <v-card-title class="d-flex justify-space-between align-center px-6 py-4 border-b">
           <span class="text-h6">{{ modalMode === 'add' ? 'Add New Package' : 'Edit Package' }}</span>
           <v-btn icon="bx bx-x" variant="text" size="small" @click="closeModal"></v-btn>
        </v-card-title>
        
        <v-form @submit.prevent="savePackage" id="packageForm">
          <v-card-text class="pa-6">
            <v-row>
              <v-col cols="12">
                <v-text-field label="Package Name" v-model="form.name" placeholder="Example: Home Fiber 20Mbps" variant="outlined" density="comfortable" required></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field label="Speed (Mbps)" type="number" v-model="form.speed" variant="outlined" density="comfortable" required></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field label="Monthly Price" type="number" v-model="form.price" prefix="Rp" variant="outlined" density="comfortable" required></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-textarea label="Description" v-model="form.description" rows="3" variant="outlined" density="comfortable"></v-textarea>
              </v-col>
              
              <v-col cols="12" sm="6">
                 <v-switch label="Unlimited Quota" v-model="form.is_unlimited" color="primary" hide-details></v-switch>
              </v-col>
              <v-col cols="12" sm="6">
                 <v-switch label="Shared Bandwidth" v-model="form.is_shared" color="primary" hide-details></v-switch>
              </v-col>
            </v-row>
          </v-card-text>
          
          <v-divider></v-divider>
          
          <v-card-actions class="pa-4 flex-row-reverse">
             <v-btn color="primary" type="submit" form="packageForm" :loading="saving" :disabled="saving" class="ml-2">
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
const packages = ref([]);
const loading = ref(true);
const saving = ref(false);
const modalVisible = ref(false);
const modalMode = ref('add');
const selectedId = ref(null);

const form = ref({
  name: '',
  description: '',
  price: 0,
  speed: 0,
  is_unlimited: true,
  is_shared: true,
});

onMounted(() => {
  fetchPackages();
});

const fetchPackages = async () => {
  loading.value = true;
  try {
    const response = await ispAdminAPI.getPackages();
    packages.value = response.data;
  } catch (err) {
    console.error("Error fetching packages", err);
  } finally {
    loading.value = false;
  }
};

const openModal = (mode, pkg = null) => {
  modalMode.value = mode;
  if (mode === 'edit' && pkg) {
    selectedId.value = pkg.id;
    form.value = { ...pkg };
  } else {
    selectedId.value = null;
    form.value = {
      name: '',
      description: '',
      price: 0,
      speed: 0,
      is_unlimited: true,
      is_shared: true,
    };
  }
  modalVisible.value = true;
};

const closeModal = () => {
  modalVisible.value = false;
};

const savePackage = async () => {
  saving.value = true;
  try {
    if (modalMode.value === 'add') {
      await ispAdminAPI.createPackage(form.value);
    } else {
      await ispAdminAPI.updatePackage(selectedId.value, form.value);
    }
    
    Swal.fire({
      icon: 'success',
      title: 'Success!',
      text: 'Package data saved successfully.',
      timer: 1500,
      showConfirmButton: false
    });
    
    closeModal();
    fetchPackages();
  } catch (err) {
    console.error("Error saving package", err);
    Swal.fire({
       title: 'Failed!',
       text: 'Failed to save package data.',
       icon: 'error',
       confirmButtonText: 'OK'
    });
  } finally {
    saving.value = false;
  }
};

const confirmDelete = (pkg) => {
  Swal.fire({
    title: 'Delete Package?',
    text: `Are you sure you want to delete package "${pkg.name}"?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ff3e1d',
    confirmButtonText: 'Yes, Delete!',
  }).then(async (result) => {
    if (result.isConfirmed) {
      try {
        await ispAdminAPI.deletePackage(pkg.id);
        Swal.fire({
           title: 'Deleted!',
           text: 'Package has been deleted.',
           icon: 'success',
           timer: 1500,
           showConfirmButton: false
        });
        fetchPackages();
      } catch (err) {
        Swal.fire({
           title: 'Failed!',
           text: 'Failed to delete package.',
           icon: 'error',
           confirmButtonText: 'OK'
        });
      }
    }
  });
};

const formatNumber = (num) => {
  return new Intl.NumberFormat('en-US').format(num);
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.min-height-0 {
    min-height: auto !important;
}
</style>
