<template>
  <div class="py-4 container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center">
              <h6 class="mb-0">Server Manager</h6>
              <button
                class="btn btn-primary btn-sm mb-0"
                @click="openModal()"
              >
                <i class="fas fa-plus me-2"></i>
                Add Server
              </button>
            </div>
          </div>
          <div class="card-body px-0 pt-0 pb-2">
            <div v-if="loading" class="text-center py-5">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
            <div v-else class="table-responsive p-0">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                      Name
                    </th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                      IP / Domain
                    </th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                      API Port
                    </th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                      VPN Local (Gateway)
                    </th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                      Status
                    </th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                      Created At
                    </th>
                    <th class="text-secondary opacity-7"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="server in servers" :key="server.id">
                    <td>
                      <div class="d-flex px-2 py-1">
                        <div class="d-flex flex-column justify-content-center">
                          <h6 class="mb-0 text-sm">{{ server.name }}</h6>
                          <p class="text-xs text-secondary mb-0">{{ server.location || 'Unknown Location' }}</p>
                        </div>
                      </div>
                    </td>
                    <td>
                      <p class="text-xs font-weight-bold mb-0">{{ server.domain || server.ip_address }}</p>
                      <p class="text-xs text-secondary mb-0" v-if="server.domain">{{ server.ip_address }}</p>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">{{ server.api_port }}</span>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">{{ server.vpn_local_address || '10.10.10.1' }}</span>
                    </td>
                    <td class="align-middle text-center text-sm">
                      <span
                        class="badge badge-sm"
                        :class="server.is_active ? 'bg-gradient-success' : 'bg-gradient-secondary'"
                      >
                        {{ server.is_active ? 'ACTIVE' : 'INACTIVE' }}
                      </span>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">{{ formatDate(server.created_at) }}</span>
                    </td>
                    <td class="align-middle">
                      <button
                        class="btn btn-link text-info mb-0"
                        @click="testConnection(server)"
                        :disabled="testingConnection === server.id"
                      >
                         <i v-if="testingConnection === server.id" class="fas fa-spinner fa-spin"></i>
                         <i v-else class="fas fa-network-wired"></i>
                      </button>
                      <button
                        class="btn btn-link text-warning mb-0"
                        @click="openVpnModal(server)"
                        title="Create Manual VPN"
                      >
                        <i class="fas fa-key"></i>
                      </button>
                      <button
                        class="btn btn-link text-secondary mb-0"
                        @click="openModal(server)"
                      >
                        <i class="fas fa-edit"></i>
                      </button>
                      <button
                        class="btn btn-link text-danger mb-0"
                        @click="deleteServer(server.id)"
                      >
                        <i class="fas fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                  <tr v-if="servers.length === 0">
                    <td colspan="6" class="text-center py-4">
                      <p class="text-muted mb-0">No servers found</p>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Server Modal -->
    <div
      v-if="showModal"
      class="modal fade show"
      style="display: block"
      tabindex="-1"
    >
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ form.id ? 'Edit Server' : 'Add New Server' }}</h5>
            <button
              type="button"
              class="btn-close"
              @click="showModal = false"
            ></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="submitForm">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Server Name</label>
                  <input
                    v-model="form.name"
                    type="text"
                    class="form-control"
                    placeholder="e.g. SG Server 1"
                    required
                  />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Location (Optional)</label>
                  <input
                    v-model="form.location"
                    type="text"
                    class="form-control"
                    placeholder="e.g. Singapore"
                  />
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">IP Address</label>
                  <input
                    v-model="form.ip_address"
                    type="text"
                    class="form-control"
                    placeholder="e.g. 103.x.x.x"
                    required
                  />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Domain (Optional)</label>
                  <input
                    v-model="form.domain"
                    type="text"
                    class="form-control"
                    placeholder="e.g. vpn.example.com"
                  />
                </div>
              </div>

              <div class="row">
                 <div class="col-md-3 mb-3">
                  <label class="form-label">API Port</label>
                  <input
                    v-model="form.api_port"
                    type="number"
                    class="form-control"
                    placeholder="8728"
                  />
                 </div>
                 <div class="col-md-3 mb-3">
                  <label class="form-label">VPN Local IP</label>
                  <input
                    v-model="form.vpn_local_address"
                    type="text"
                    class="form-control"
                    placeholder="10.10.10.1"
                  />
                 </div>
                 <div class="col-md-3 mb-3">
                  <label class="form-label">API Username</label>
                  <input
                    v-model="form.username"
                    type="text"
                    class="form-control"
                  />
                 </div>
                 <div class="col-md-3 mb-3">
                  <label class="form-label">API Password</label>
                  <input
                    v-model="form.password"
                    type="password"
                    class="form-control"
                  />
                 </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Capacity (Max Users)</label>
                <input
                  v-model="form.capacity"
                  type="number"
                  class="form-control"
                />
              </div>

              <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea
                  v-model="form.notes"
                  class="form-control"
                  rows="2"
                ></textarea>
              </div>

              <div class="form-check form-switch mb-3" v-if="form.id">
                <input
                  class="form-check-input"
                  type="checkbox"
                  v-model="form.is_active"
                />
                <label class="form-check-label">Is Active</label>
              </div>

              <div class="d-flex justify-content-end gap-2">
                <button
                  type="button"
                  class="btn btn-secondary"
                  @click="showModal = false"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  class="btn btn-primary"
                  :disabled="processing"
                >
                  <span v-if="processing">
                    <i class="fas fa-spinner fa-spin me-2"></i>
                    Saving...
                  </span>
                  <span v-else>Save Server</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div
      v-if="showModal"
      class="modal-backdrop fade show"
      @click="showModal = false"
    ></div>

    <!-- VPN Test Modal -->
    <div
      v-if="showVpnModal"
      class="modal fade show"
      style="display: block"
      tabindex="-1"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Manual VPN Test (L2TP/SSTP)</h5>
            <button
              type="button"
              class="btn-close"
              @click="showVpnModal = false"
            ></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="submitVpnTest">
              <div class="mb-3">
                <label class="form-label">Username</label>
                <input
                  v-model="vpnForm.username"
                  type="text"
                  class="form-control"
                  required
                />
              </div>
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input
                  v-model="vpnForm.password"
                  type="text"
                  class="form-control"
                  required
                />
              </div>
              <div class="row">
                  <div class="col-6 mb-3">
                    <label class="form-label">Local Address (Gateway)</label>
                    <input
                      v-model="vpnForm.local_address"
                      type="text"
                      class="form-control"
                      placeholder="e.g. 10.1.50.1"
                    />
                  </div>
                  <div class="col-6 mb-3">
                    <label class="form-label">Remote Address (Client)</label>
                    <input
                      v-model="vpnForm.remote_address"
                      type="text"
                      class="form-control"
                      placeholder="e.g. 10.1.50.2"
                    />
                  </div>
              </div>

              <div class="d-flex justify-content-end gap-2">
                <button
                  type="button"
                  class="btn btn-secondary"
                  @click="showVpnModal = false"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  class="btn btn-success"
                  :disabled="processing"
                >
                  <span v-if="processing">
                    <i class="fas fa-spinner fa-spin me-2"></i>
                    Creating...
                  </span>
                  <span v-else>Create Secret</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div
      v-if="showVpnModal"
      class="modal-backdrop fade show"
      @click="showVpnModal = false"
    ></div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const servers = ref([])
const loading = ref(false)
const showModal = ref(false)
const processing = ref(false)

const form = ref({
  id: null,
  name: '',
  ip_address: '',
  domain: '',
  location: '',
  api_port: 8728,
  vpn_local_address: '10.10.10.1',
  username: '',
  password: '',
  capacity: 1000,
  notes: '',
  is_active: true
})

onMounted(async () => {
  await fetchServers()
})

const fetchServers = async () => {
  loading.value = true
  try {
    const response = await api.get('/super-admin/servers')
    servers.value = response.data
  } catch (error) {
    console.error('Error fetching servers:', error)
    alert('Failed to fetch servers')
  } finally {
    loading.value = false
  }
}

const openModal = (server = null) => {
  if (server) {
    form.value = { ...server }
  } else {
    form.value = {
      id: null,
      name: '',
      ip_address: '',
      domain: '',
      location: '',
      api_port: 8728,
      vpn_local_address: '10.10.10.1',
      username: '',
      password: '',
      capacity: 1000,
      notes: '',
      is_active: true
    }
  }
  showModal.value = true
}

const submitForm = async () => {
  processing.value = true
  try {
    if (form.value.id) {
       await api.put(`/super-admin/servers/${form.value.id}`, form.value)
    } else {
       await api.post('/super-admin/servers', form.value)
    }
    
    await fetchServers()
    showModal.value = false
    alert('Server saved successfully!')
  } catch (error) {
    console.error('Error saving server:', error)
    alert('Failed to save server: ' + (error.response?.data?.message || error.message))
  } finally {
    processing.value = false
  }
}

const deleteServer = async (id) => {
  if (!confirm('Are you sure you want to delete this server?')) return
  
  try {
    await api.delete(`/super-admin/servers/${id}`)
    await fetchServers()
  } catch (error) {
    console.error('Error deleting server:', error)
     alert('Failed to delete server')
  }
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID')
}
const testingConnection = ref(null)
const showVpnModal = ref(false)
const vpnForm = ref({
  server_id: null,
  username: '',
  password: '',
  local_address: '',
  remote_address: ''
})

const testConnection = async (server) => {
  testingConnection.value = server.id
  try {
    const response = await api.post(`/super-admin/servers/${server.id}/test-connection`)
    alert(response.data.message)
  } catch (error) {
    console.error('Connection test failed:', error)
    alert('Connection Failed: ' + (error.response?.data?.message || error.message))
  } finally {
    testingConnection.value = null
  }
}

const openVpnModal = (server) => {
  vpnForm.value = {
    server_id: server.id,
    username: 'testvpn' + Math.floor(Math.random() * 1000),
    password: 'password123',
    local_address: '',
    remote_address: ''
  }
  showVpnModal.value = true
}

const submitVpnTest = async () => {
    processing.value = true
    try {
        const response = await api.post(`/super-admin/servers/${vpnForm.value.server_id}/create-vpn`, vpnForm.value)
        alert(response.data.message)
        showVpnModal.value = false
    } catch (error) {
        console.error('VPN Creation failed:', error)
        alert('Failed to create VPN: ' + (error.response?.data?.message || error.message))
    } finally {
        processing.value = false
    }
}
</script>
