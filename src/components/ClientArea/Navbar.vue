<template>
  <nav
    class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl"
    id="navbarBlur"
    navbar-scroll="true"
  >
    <div class="container-fluid py-1 px-3">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm">
            <a class="opacity-5 text-dark" href="javascript:;">Home</a>
          </li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">
            {{ currentPageTitle }}
          </li>
        </ol>
        <h6 class="font-weight-bolder mb-0">{{ currentPageTitle }}</h6>
      </nav>
      <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
        <div class="ms-md-auto pe-md-3 d-flex align-items-center">
          <div class="input-group">
            <span class="input-group-text text-body">
              <i class="fas fa-search" aria-hidden="true"></i>
            </span>
            <input
              type="text"
              class="form-control"
              placeholder="Type here..."
            />
          </div>
        </div>
        <ul class="navbar-nav justify-content-end">
          <li class="nav-item d-flex align-items-center">
            <a class="btn btn-outline-primary btn-sm mb-0 me-3" href="#">
              <i class="fas fa-user me-2"></i>
              Sign In
            </a>
          </li>
          <li class="nav-item d-flex align-items-center">
            <a class="nav-link text-body font-weight-bold px-0" href="#">
              <i class="fa fa-cog me-sm-1"></i>
            </a>
          </li>
          <li class="nav-item d-flex align-items-center">
            <a class="nav-link text-body font-weight-bold px-0" href="#">
              <i class="fas fa-bell me-sm-1"></i>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

const currentPageTitle = computed(() => {
  return route.meta.title || 'Dashboard'
})
</script>
