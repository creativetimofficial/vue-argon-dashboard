<script setup>
import { useStore } from "vuex";
const store = useStore();
</script>
<template>
  <footer class="py-3 footer">
    <div class="container-fluid">
      <div class="row align-items-center justify-content-lg-between">
        <div class="mb-4 col-lg-12 mb-lg-0">
          <div
            class="text-sm text-center copyright text-muted"
          >
            ©
            {{ new Date().getFullYear() }}, made with
            <i class="fa fa-heart"></i> by
            <a
              href="https://www.payneto.id"
              class="font-weight-bold"
              target="_blank"
              >Payneto Group</a
            >
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>
