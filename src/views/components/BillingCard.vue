<template>
  <div class="card">
    <div class="card-header pb-0 px-3 d-flex justify-content-between align-items-center">
      <h6 class="mb-0">Billing Information</h6>
      <button class="btn btn-primary btn-sm" @click="openAdd">Tambah ISP</button>
    </div>
    <div class="card-body pt-4 p-3">
      <ul class="list-group">
        <li
          v-for="isp in ispList"
          :key="isp.id"
          class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg"
        >
          <div class="d-flex flex-column">
            <h6 class="mb-3 text-sm">{{ isp.name }}</h6>
            <span class="mb-2 text-xs">
              Company Name:
              <span class="text-dark font-weight-bold ms-sm-2">{{ isp.company }}</span>
            </span>
            <span class="mb-2 text-xs">
              Email Address:
              <span class="text-dark ms-sm-2 font-weight-bold">{{ isp.email }}</span>
            </span>
            <span class="text-xs">
              VAT Number:
              <span class="text-dark ms-sm-2 font-weight-bold">{{ isp.vat }}</span>
            </span>
          </div>
          <div class="ms-auto text-end">
            <a
              class="btn btn-link text-danger text-gradient px-3 mb-0"
              href="javascript:;"
              @click="deleteISP(isp.id)"
            >
              <i class="far fa-trash-alt me-2" aria-hidden="true"></i>Delete
            </a>
            <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;" @click="openEdit(isp)">
              <i class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i>Edit
            </a>
          </div>
        </li>
      </ul>
      <!-- Modal Tambah/Edit ISP -->
      <div v-if="showModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.3);">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ editMode ? 'Edit ISP' : 'Tambah ISP' }}</h5>
              <button type="button" class="btn-close" @click="showModal = false"></button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="saveISP">
                <div class="mb-3">
                  <label class="form-label">Nama</label>
                  <input v-model="form.name" type="text" class="form-control" required />
                </div>
                <div class="mb-3">
                  <label class="form-label">Perusahaan</label>
                  <input v-model="form.company" type="text" class="form-control" required />
                </div>
                <div class="mb-3">
                  <label class="form-label">Email</label>
                  <input v-model="form.email" type="email" class="form-control" required />
                </div>
                <div class="mb-3">
                  <label class="form-label">VAT Number</label>
                  <input v-model="form.vat" type="text" class="form-control" required />
                </div>
                <div class="text-end">
                  <button type="button" class="btn btn-secondary me-2" @click="showModal = false">Batal</button>
                  <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

// Data ISP (dummy awal, bisa diganti API)
const ispList = ref([
  {
    id: 1,
    name: 'Oliver Liam',
    company: 'Viking Burrito',
    email: 'oliver@burrito.com',
    vat: 'FRB1235476',
  },
  {
    id: 2,
    name: 'Lucas Harper',
    company: 'Stone Tech Zone',
    email: 'lucas@stone-tech.com',
    vat: 'FRB1235476',
  },
  {
    id: 3,
    name: 'Ethan James',
    company: 'Fiber Notion',
    email: 'ethan@fiber.com',
    vat: 'FRB1235476',
  },
])

const form = ref({ id: null, name: '', company: '', email: '', vat: '' })
const showModal = ref(false)
const editMode = ref(false)

function openAdd() {
  form.value = { id: null, name: '', company: '', email: '', vat: '' }
  editMode.value = false
  showModal.value = true
}
function openEdit(isp) {
  form.value = { ...isp }
  editMode.value = true
  showModal.value = true
}
function saveISP() {
  if (editMode.value) {
    const idx = ispList.value.findIndex(i => i.id === form.value.id)
    if (idx !== -1) ispList.value[idx] = { ...form.value }
  } else {
    form.value.id = Date.now()
    ispList.value.push({ ...form.value })
  }
  showModal.value = false
}
function deleteISP(id) {
  if (confirm('Yakin ingin menghapus ISP ini?')) {
    ispList.value = ispList.value.filter(i => i.id !== id)
  }
}
</script>
