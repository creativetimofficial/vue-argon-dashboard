<template>
  <v-container fluid class="pa-6">
    <div class="d-flex align-center mb-6">
      <h4 class="text-h4 font-weight-bold mb-0">
        <span class="text-medium-emphasis font-weight-light">ISP Admin /</span> Customer Management
      </h4>
    </div>

    <!-- Filter and Search -->
    <v-card  class="mb-6">
      <v-card-text>
        <v-row>
          <v-col cols="12" md="4">
            <v-text-field
              v-model="search"
              prepend-inner-icon="bx bx-search"
              placeholder="Search customers..."
              hide-details
              density="compact"
              
              @input="fetchCustomers"
            ></v-text-field>
          </v-col>
          <v-col cols="6" md="3">
            <v-select
              v-model="status"
              :items="[{title:'All Status', value:''}, {title:'Active', value:'active'}, {title:'Suspended', value:'suspended'}, {title:'Terminated', value:'terminated'}]"
              hide-details
              density="compact"
              
              @update:modelValue="fetchCustomers"
            ></v-select>
          </v-col>
          <v-col cols="6" md="5" class="d-flex justify-end">
             <v-btn color="primary" @click="openModal('add')">
                <v-icon start>bx bx-plus</v-icon> Add
             </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <!-- Customers Table -->
    <v-card >
      <v-card-title class="pa-4 d-flex align-center">Customer List</v-card-title>
      <v-divider></v-divider>
      <v-table hover>
        <thead>
          <tr>
            <th class="text-left font-weight-bold">ID</th>
            <th class="text-left font-weight-bold">Name & Service</th>
            <th class="text-left font-weight-bold">Contact</th>
            <th class="text-left font-weight-bold">Status</th>
            <th class="text-left font-weight-bold">Balance</th>
            <th class="text-left font-weight-bold" width="100">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
             <td colspan="6" class="text-center py-10">
                <v-progress-circular indeterminate color="primary"></v-progress-circular>
             </td>
          </tr>
          <tr v-else-if="customers.length === 0">
             <td colspan="6" class="text-center py-10 text-medium-emphasis">No customer data found</td>
          </tr>
          <tr v-for="customer in customers" :key="customer.id">
            <td>{{ customer.id }}</td>
            <td>
              <div class="d-flex align-center">
                <v-avatar :color="isOnline(customer) ? 'success-opacity-10' : 'secondary-opacity-10'" class="mr-3">
                  <span :class="isOnline(customer) ? 'text-success' : 'text-secondary'" class="text-button">{{ customer.name.charAt(0) }}</span>
                </v-avatar>
                <div>
                  <div class="font-weight-medium text-truncate">{{ customer.name }}</div>
                  <div class="text-caption text-primary font-weight-bold" v-if="customer.service_plan_id">
                    {{ getServiceName(customer.service_plan_id) }}
                  </div>
                  <div class="text-caption text-medium-emphasis" v-else>No Package</div>
                </div>
              </div>
            </td>
            <td>
              <div class="d-flex flex-column">
                <span class="text-truncate">{{ customer.email || '-' }}</span>
                <span class="text-caption text-medium-emphasis">{{ customer.phone }}</span>
              </div>
            </td>
            <td>
              <v-chip size="small" :color="getStatusClass(customer.status).replace('bg-label-', '') + ' bg-opacity-10 text-' + getStatusClass(customer.status).replace('bg-label-', '')" class="font-weight-medium">
                {{ getStatusLabel(customer.status) }}
              </v-chip>
            </td>
            <td>
              <span class="font-weight-medium">Rp {{ formatNumber(customer.balance) }}</span><br>
              <div class="text-caption text-error" v-if="customer.balance < 0">Overdue</div>
            </td>
            <td>
              <v-btn icon variant="text" size="small" color="secondary">
                <v-icon>bx bx-dots-vertical-rounded</v-icon>
                <v-menu activator="parent" offset-y>
                  <v-list density="compact" class="py-0">
                    <v-list-item @click="openModal('edit', customer)">
                      <template v-slot:prepend><v-icon color="primary" class="mr-2">bx bx-edit-alt</v-icon></template>
                      <v-list-item-title>Edit</v-list-item-title>
                    </v-list-item>
                    <v-list-item @click="confirmDelete(customer.id)">
                      <template v-slot:prepend><v-icon color="error" class="mr-2">bx bx-trash</v-icon></template>
                      <v-list-item-title class="text-error">Delete</v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </v-btn>
            </td>
          </tr>
        </tbody>
      </v-table>
      <v-divider></v-divider>
      <!-- Pagination -->
      <v-card-actions class="pa-4 justify-space-between" v-if="pagination.total > 0">
        <div class="text-caption text-medium-emphasis">Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }}</div>
        <v-pagination
          v-model="pagination.current"
          :length="pagination.last_page"
          density="compact"
          @update:modelValue="goToPage"
          color="primary"
        ></v-pagination>
      </v-card-actions>
    </v-card>

    <!-- Modal Add/Edit (Enterprise Version) -->
    <v-dialog v-model="modalVisible" max-width="1100" persistent scrollable :theme="themeName">
      <v-card>
        <v-card-title class="d-flex justify-space-between align-center px-6 py-4 border-b">
          <span class="text-h6">{{ modalMode === 'add' ? 'Add New Customer' : 'Edit Customer Data' }}</span>
          <v-btn icon="bx bx-x" variant="text" size="small" @click="closeModal"></v-btn>
        </v-card-title>
        
        <v-card-text class="pa-6">
          <v-form @submit.prevent="saveCustomer" id="customerForm">
            <v-row>
              <!-- Main Info -->
              <v-col cols="12">
                 <div class="text-subtitle-1 font-weight-bold text-primary mb-2">PERSONAL DATA</div>
                 <v-divider class="mb-4"></v-divider>
              </v-col>
              <v-col cols="12" md="4">
                <v-text-field label="Full Name" v-model="form.name"  density="compact" required></v-text-field>
              </v-col>
              <v-col cols="12" md="4">
                <v-text-field label="ID Number" v-model="form.id_number" placeholder="ID Card No."  density="compact"></v-text-field>
              </v-col>
              <v-col cols="12" md="4">
                <v-select label="Identity Type" v-model="form.id_type" :items="['KTP', 'SIM', 'PASSPORT']"  density="compact"></v-select>
              </v-col>
              <v-col cols="12" md="4">
                <v-text-field label="Email" type="email" v-model="form.email"  density="compact"></v-text-field>
              </v-col>
              <v-col cols="12" md="4">
                <v-text-field label="Phone / WA" v-model="form.phone"  density="compact" required></v-text-field>
              </v-col>
              <v-col cols="12" md="4">
                <v-text-field label="Customer Code" v-model="form.customer_code" placeholder="Automatic if empty"  density="compact"></v-text-field>
              </v-col>

              <!-- Technical Specs -->
              <v-col cols="12">
                 <div class="text-subtitle-1 font-weight-bold text-primary mt-2 mb-2">NETWORK CONFIGURATION</div>
                 <v-divider class="mb-4"></v-divider>
              </v-col>
              <v-col cols="12" md="4">
                <v-select label="Service Plan" v-model="form.service_plan_id" :items="[ { title: 'Select Service Plan', value: '' }, ...servicePlans.map(p => ({ title: `${p.name} - Rp ${formatNumber(p.price)}`, value: p.id })) ]"  density="compact"></v-select>
              </v-col>
              <v-col cols="12" md="4">
                <v-text-field label="ONT Serial Number (SN)" v-model="form.ont_serial_number" placeholder="e.g., SN: ZTEGC12345"  density="compact"></v-text-field>
              </v-col>
              <v-col cols="12" md="4">
                <v-text-field label="IP Address (Statik/DHCP)" v-model="form.ip_address" placeholder="192.168.x.x"  density="compact"></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field label="PPPoE Username" v-model="form.mikrotik_username"  density="compact"></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <!-- Change to password field type? In mikrotik management it might be useful to see it -->
                <v-text-field label="PPPoE Password" v-model="form.mikrotik_password"  density="compact"></v-text-field>
              </v-col>

              <!-- Location -->
              <v-col cols="12">
                 <div class="text-subtitle-1 font-weight-bold text-primary mt-2 mb-2">LOCATION & INSTALLATION</div>
                 <v-divider class="mb-4"></v-divider>
              </v-col>
              <v-col cols="12" md="3">
                <v-text-field label="Latitude" v-model="form.latitude" placeholder="-6.xxx"  density="compact"></v-text-field>
              </v-col>
              <v-col cols="12" md="3">
                <v-text-field label="Longitude" v-model="form.longitude" placeholder="106.xxx"  density="compact"></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field type="date" label="Installation Date" v-model="form.installation_date"  density="compact"></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-textarea label="Full Address" v-model="form.address" rows="2"  density="compact" required></v-textarea>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        
        <v-card-actions class="px-6 py-4 border-t px-4 d-flex justify-end gap-2">
           <v-btn  color="secondary" @click="closeModal">Cancel</v-btn>
           <v-btn color="primary" type="submit" form="customerForm" :loading="saving" :disabled="saving">
              {{ modalMode === 'add' ? 'Add' : 'Save Changes' }}
           </v-btn>
        </v-card-actions>
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
const customers = ref([]);
const servicePlans = ref([]);
const loading = ref(false);
const saving = ref(false);
const search = ref('');
const status = ref('');
const modalMode = ref('add');
const selectedId = ref(null);
const modalVisible = ref(false);

const pagination = ref({
   current: 1,
   last_page: 1,
   total: 0,
   from: 0,
   to: 0,
   prev: null,
   next: null
});

const form = ref({
  name: '',
  email: '',
  phone: '',
  address: '',
  customer_code: '',
  status: 'active',
  id_number: '',
  id_type: 'KTP',
  service_plan_id: '',
  ont_serial_number: '',
  ip_address: '',
  mac_address: '',
  mikrotik_username: '',
  mikrotik_password: '',
  latitude: '',
  longitude: '',
  installation_date: ''
});

const fetchCustomers = async (page = 1) => {
  loading.value = true;
  try {
     const response = await ispAdminAPI.getCustomers({
        page,
        search: search.value,
        status: status.value
     });
     customers.value = response.data.data;
     pagination.value = {
        current: response.data.current_page,
        last_page: response.data.last_page,
        total: response.data.total,
        from: response.data.from || 0,
        to: response.data.to || 0,
        prev: response.data.prev_page_url,
        next: response.data.next_page_url
     };
  } catch (err) {
     console.error("Error fetching customers", err);
  } finally {
     loading.value = false;
  }
};

const fetchServices = async () => {
    try {
        const response = await ispAdminAPI.getServices();
        servicePlans.value = response.data;
    } catch (err) {
        console.error("Error fetching services", err);
    }
};

const goToPage = (page) => {
   if (page >= 1 && page <= pagination.value.last_page) {
      fetchCustomers(page);
   }
};

const openModal = (mode, customer = null) => {
  modalMode.value = mode;
  if (mode === 'edit' && customer) {
    selectedId.value = customer.id;
    form.value = { ...customer };
  } else {
    selectedId.value = null;
    form.value = {
      name: '',
      email: '',
      phone: '',
      address: '',
      customer_code: '',
      status: 'active',
      id_number: '',
      id_type: 'KTP',
      service_plan_id: '',
      ont_serial_number: '',
      ip_address: '',
      mac_address: '',
      mikrotik_username: '',
      mikrotik_password: '',
      latitude: '',
      longitude: '',
      installation_date: ''
    };
  }
  modalVisible.value = true;
};

const closeModal = () => {
  modalVisible.value = false;
};

const saveCustomer = async () => {
  saving.value = true;
  try {
    const response = modalMode.value === 'add' 
      ? await ispAdminAPI.createCustomer(form.value)
      : await ispAdminAPI.updateCustomer(selectedId.value, form.value);

    Swal.fire({
      icon: 'success',
      title: 'Success!',
      text: response.data.message,
      timer: 2000,
      showConfirmButton: false
    });

    closeModal();
    fetchCustomers(pagination.value.current);
  } catch (err) {
    console.error("Error saving customer", err);
    const msg = err.response?.data?.message || 'An error occurred while saving data.';
    Swal.fire({
       title: 'Failed!',
       text: msg,
       icon: 'error',
       confirmButtonText: 'OK'
    });
  } finally {
    saving.value = false;
  }
};

const confirmDelete = (id) => {
  Swal.fire({
    title: 'Delete Customer?',
    text: "All service data and history will be deleted!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ff3e1d',
    cancelButtonColor: '#8592a3',
    confirmButtonText: 'Yes, Delete!',
    cancelButtonText: 'Cancel'
  }).then(async (result) => {
    if (result.isConfirmed) {
      try {
        await ispAdminAPI.deleteCustomer(id);
        Swal.fire({
           title: 'Deleted!',
           text: 'Customer data has been deleted.',
           icon: 'success',
           timer: 2000,
           showConfirmButton: false
        });
        fetchCustomers(pagination.value.current);
      } catch (err) {
        Swal.fire({
           title: 'Failed!',
           text: 'Failed to delete data.',
           icon: 'error',
           confirmButtonText: 'OK'
        });
      }
    }
  });
};

const getServiceName = (id) => {
    const plan = servicePlans.value.find(p => p.id === id);
    return plan ? plan.name : 'Unknown';
};

const getStatusLabel = (status) => {
  const labels = { active: 'Active', suspended: 'Suspended', terminated: 'Diputus' };
  return labels[status] || status;
};

const getStatusClass = (status) => {
  const classes = { active: 'bg-label-success', suspended: 'bg-label-warning', terminated: 'bg-label-danger' };
  return classes[status] || 'bg-label-secondary';
};

const formatNumber = (num) => {
  if (!num) return '0';
  return parseFloat(num).toLocaleString('en-US');
};

const isOnline = (customer) => {
    // Mock logic for online status indicator
    return customer.status === 'active' && customer.service_plan_id !== null;
};

onMounted(() => {
  fetchCustomers();
  fetchServices();
});
</script>

<style scoped>
.bg-label-primary {
  background-color: #e7e7ff !important;
  color: #696cff !important;
}
.text-xs { font-size: 0.75rem; }
.avatar-initial {
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 500;
}
</style>
