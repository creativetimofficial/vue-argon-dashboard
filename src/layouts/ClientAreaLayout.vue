<template>
  <div class="g-sidenav-show bg-gray-100">
    <Sidebar />
    <main class="main-content position-relative border-radius-lg">
      <Navbar />
      <div class="container-fluid py-4">
        <router-view />
      </div>
    </main>
  </div>
</template>

<script setup>
import Sidebar from "@/components/ClientArea/Sidebar.vue";
import Navbar from "@/components/ClientArea/Navbar.vue";
</script>
