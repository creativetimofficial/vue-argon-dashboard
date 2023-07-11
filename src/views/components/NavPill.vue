<script setup>
import { onMounted } from "vue";
import setNavPills from "@/assets/js/nav-pills.js";

onMounted(() => {
  setNavPills();
});
</script>
<template>
  <div class="nav-wrapper position-relative end-0">
    <ul class="nav nav-pills nav-fill p-1" role="tablist">
      <li class="nav-item">
        <a
          class="nav-link mb-0 px-0 py-1 active active"
          data-bs-toggle="tab"
          href="#"
          role="tab"
          aria-selected="true"
          >Messages</a
        >
      </li>
      <li class="nav-item">
        <a
          class="nav-link mb-0 px-0 py-1"
          data-bs-toggle="tab"
          href="#"
          role="tab"
          aria-selected="false"
          >Social</a
        >
      </li>
      <li class="nav-item">
        <a
          class="nav-link mb-0 px-0 py-1"
          data-bs-toggle="tab"
          href="#"
          role="tab"
          aria-selected="false"
          >Notifications</a
        >
      </li>
      <li class="nav-item">
        <a
          class="nav-link mb-0 px-0 py-1"
          data-bs-toggle="tab"
          href="#"
          role="tab"
          aria-selected="false"
          >Backup</a
        >
      </li>
    </ul>
  </div>
</template>
