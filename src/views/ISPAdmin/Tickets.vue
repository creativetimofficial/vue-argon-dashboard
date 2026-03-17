<template>
  <v-container fluid class="pa-6">
    <div class="d-flex align-center justify-space-between mb-6">
      <h4 class="text-h4 font-weight-bold mb-0">
        <span class="text-medium-emphasis font-weight-light">ISP Admin /</span> Support Tickets
      </h4>
      <v-btn color="primary" prepend-icon="bx bx-plus" @click="openModal('add')">
        Create Ticket
      </v-btn>
    </div>

    <!-- Tickets Table -->
    <v-card >
      <v-card-title class="pa-4">Ticket List</v-card-title>
      <v-divider></v-divider>
      <v-table hover>
        <thead>
          <tr>
            <th class="text-left font-weight-bold">ID</th>
            <th class="text-left font-weight-bold">Customer</th>
            <th class="text-left font-weight-bold">Subject</th>
            <th class="text-left font-weight-bold">Priority</th>
            <th class="text-left font-weight-bold">Status</th>
            <th class="text-left font-weight-bold">Date</th>
            <th class="text-left font-weight-bold" width="100">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="7" class="text-center py-10">
              <v-progress-circular indeterminate color="primary"></v-progress-circular>
            </td>
          </tr>
          <tr v-else-if="tickets.length === 0">
            <td colspan="7" class="text-center py-10">
              <v-icon size="48" color="medium-emphasis" class="mb-2">bx bx-support</v-icon>
              <p class="text-medium-emphasis mb-0">No support tickets yet</p>
            </td>
          </tr>
          <tr v-for="ticket in tickets" :key="ticket.id">
            <td><span class="font-weight-bold text-primary">#{{ ticket.ticket_id || ticket.id }}</span></td>
            <td>
              <div class="font-weight-medium">{{ ticket.customer?.name || 'Unknown' }}</div>
              <div class="text-caption text-medium-emphasis">{{ ticket.customer?.customer_code }}</div>
            </td>
            <td>{{ ticket.subject }}</td>
            <td>
              <v-chip size="x-small" :color="getPriorityColor(ticket.priority)" variant="tonal" class="text-capitalize">
                {{ ticket.priority }}
              </v-chip>
            </td>
            <td>
              <v-chip size="x-small" :color="getStatusColor(ticket.status)" variant="tonal" class="text-capitalize">
                {{ ticket.status }}
              </v-chip>
            </td>
            <td>{{ formatDate(ticket.created_at) }}</td>
            <td>
              <v-btn icon variant="text" size="small">
                <v-icon>bx bx-dots-vertical-rounded</v-icon>
                <v-menu activator="parent" offset-y>
                  <v-list density="compact" class="py-0">
                    <v-list-item @click="viewTicket(ticket)">
                      <template v-slot:prepend><v-icon size="small" color="primary" class="mr-2">bx bx-show</v-icon></template>
                      <v-list-item-title>Detail</v-list-item-title>
                    </v-list-item>
                    <v-list-item @click="confirmDelete(ticket)">
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

    <!-- Modal Ticket (Simplified for now) -->
    <v-dialog v-model="modalVisible" max-width="600" persistent :theme="themeName">
      <v-card>
        <v-card-title class="d-flex justify-space-between align-center px-6 py-4 border-b">
           <span class="text-h6">Create Ticket Baru</span>
           <v-btn icon="bx bx-x" variant="text" size="small" @click="closeModal"></v-btn>
        </v-card-title>
        
        <v-form @submit.prevent="saveTicket" id="ticketForm">
          <v-card-text class="pa-6">
            <v-row>
              <v-col cols="12">
                <v-select label="Customer" :items="customers" item-title="name" item-value="id" v-model="form.customer_id" variant="outlined" density="comfortable" required></v-select>
              </v-col>
              <v-col cols="12">
                <v-text-field label="Subject" v-model="form.subject" variant="outlined" density="comfortable" required></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-select label="Priority" :items="['low', 'medium', 'high', 'urgent']" v-model="form.priority" variant="outlined" density="comfortable" required></v-select>
              </v-col>
              <v-col cols="12">
                <v-textarea label="Message" v-model="form.message" rows="4" variant="outlined" density="comfortable" required></v-textarea>
              </v-col>
            </v-row>
          </v-card-text>
          
          <v-divider></v-divider>
          
          <v-card-actions class="pa-4 flex-row-reverse">
             <v-btn color="primary" type="submit" form="ticketForm" :loading="saving" :disabled="saving" class="ml-2">Save</v-btn>
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
const tickets = ref([]);
const customers = ref([]);
const loading = ref(true);
const saving = ref(false);
const modalVisible = ref(false);

const form = ref({
  customer_id: null,
  subject: '',
  priority: 'low',
  message: '',
});

onMounted(() => {
  fetchTickets();
  fetchCustomers();
});

const fetchTickets = async () => {
  loading.value = true;
  try {
    const response = await ispAdminAPI.getTickets();
    tickets.value = response.data;
  } catch (err) {
    console.error("Error fetching tickets", err);
  } finally {
    loading.value = false;
  }
};

const fetchCustomers = async () => {
  try {
    const response = await ispAdminAPI.getCustomers();
    customers.value = response.data;
  } catch (err) {
    console.error("Error fetching customers", err);
  }
};

const openModal = () => {
  form.value = { customer_id: null, subject: '', priority: 'low', message: '' };
  modalVisible.value = true;
};

const closeModal = () => {
  modalVisible.value = false;
};

const saveTicket = async () => {
  saving.value = true;
  try {
    await ispAdminAPI.createTicket(form.value);
    closeModal();
    fetchTickets();
    Swal.fire({ icon: 'success', title: 'Success', text: 'Ticket created successfully', showConfirmButton: false, timer: 1500 });
  } catch (err) {
    Swal.fire('Failed', 'Failed membuat tiket', 'error');
  } finally {
    saving.value = false;
  }
};

const confirmDelete = (ticket) => {
  Swal.fire({
    title: 'Delete Tiket?',
    text: `Are you sure you want to delete ticket #${ticket.id}?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ff3e1d',
    confirmButtonText: 'Ya, Delete!',
  }).then(async (result) => {
    if (result.isConfirmed) {
      try {
        await ispAdminAPI.deleteTicket(ticket.id);
        fetchTickets();
        Swal.fire('Deleted!', 'Ticket has been deleted', 'success');
      } catch (err) {
        Swal.fire('Failed', 'Failed menghapus tiket', 'error');
      }
    }
  });
};

const getPriorityColor = (priority) => {
  const colors = { low: 'info', medium: 'warning', high: 'error', urgent: 'error' };
  return colors[priority] || 'secondary';
};

const getStatusColor = (status) => {
  const colors = { open: 'primary', closed: 'success', pending: 'warning' };
  return colors[status] || 'secondary';
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', { day: '2-digit', month: 'short', year: 'numeric' });
};

const viewTicket = (ticket) => {
  // view detail implementation
};
</script>
