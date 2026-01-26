<template>
  <aside
    class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3"
    id="sidenav-main"
  >
    <div class="sidenav-header">
      <i
        class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
        aria-hidden="true"
        id="iconSidenav"
      ></i>
      <a class="navbar-brand m-0" href="#" target="_blank">
        <span class="ms-1 font-weight-bold">CLIENTAREA</span>
      </a>
    </div>
    <hr class="horizontal dark mt-0" />
    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item">
          <router-link
            :to="{ name: 'ClientAreaDashboard' }"
            class="nav-link"
            :class="{ active: $route.name === 'ClientAreaDashboard' }"
          >
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="fas fa-desktop text-dark text-sm"></i>
            </div>
            <span class="nav-link-text ms-1">Dashboard</span>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link
            :to="{ name: 'ClientAreaOrders' }"
            class="nav-link"
            :class="{ active: $route.name === 'ClientAreaOrders' }"
          >
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="fas fa-shopping-cart text-dark text-sm"></i>
            </div>
            <span class="nav-link-text ms-1">Orders</span>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link
            :to="{ name: 'ClientAreaServices' }"
            class="nav-link"
            :class="{ active: $route.name === 'ClientAreaServices' || $route.name === 'ClientAreaServiceDetail' }"
          >
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="fas fa-cog text-dark text-sm"></i>
            </div>
            <span class="nav-link-text ms-1">Services</span>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link
            :to="{ name: 'ClientAreaInvoices' }"
            class="nav-link"
            :class="{ active: $route.name === 'ClientAreaInvoices' }"
          >
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="fas fa-file-invoice-dollar text-dark text-sm"></i>
            </div>
            <span class="nav-link-text ms-1">Invoices</span>
          </router-link>
        </li>
        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">
            ACCOUNT PAGES
          </h6>
        </li>
        <li class="nav-item">
          <router-link
            :to="{ name: 'ClientAreaProfile' }"
            class="nav-link"
            :class="{ active: $route.name === 'ClientAreaProfile' }"
          >
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="fas fa-user text-dark text-sm"></i>
            </div>
            <span class="nav-link-text ms-1">Profile</span>
          </router-link>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="fas fa-wallet text-dark text-sm"></i>
            </div>
            <span class="nav-link-text ms-1">Top Up</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" @click.prevent="handleLogout">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="fas fa-sign-out-alt text-danger text-sm"></i>
            </div>
            <span class="nav-link-text ms-1">Logout</span>
          </a>
        </li>
      </ul>
    </div>
    <div class="sidenav-footer mx-3">
      <div class="card card-background shadow-none card-background-mask-secondary" id="sidenavCard">
        <div class="full-background" style="background-image: url('/assets/img/curved-images/white-curved.jpg')"></div>
        <div class="card-body text-start p-3 w-100">
          <div class="d-block px-2 py-1">
            <div class="d-flex align-items-center">
              <div class="icon icon-shape icon-sm bg-white shadow text-center mb-0 d-flex align-items-center justify-content-center me-2">
                <i class="fas fa-folder text-dark text-sm"></i>
              </div>
              <div class="ms-2">
                <p class="text-sm font-weight-bold mb-0">Need help?</p>
                <p class="text-xs text-secondary mb-0">Please check our docs</p>
              </div>
            </div>
          </div>
          <a class="btn btn-white btn-sm w-100 mb-0" href="#" target="_blank">Documentation</a>
          <a class="btn btn-danger btn-sm w-100 mb-0" href="#" target="_blank">Youtube</a>
        </div>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { authAPI } from '@/services/api'

const router = useRouter()

const handleLogout = async () => {
  try {
    await authAPI.logout()
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user')
    router.push('/login')
  } catch (error) {
    console.error('Logout error:', error)
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user')
    router.push('/login')
  }
}
</script>

<style scoped>
.sidenav {
  z-index: 1000;
}
</style>
