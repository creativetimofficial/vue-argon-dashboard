<template>
  <div class="loading-overlay" v-if="active">
    <div class="spinner-container">
      <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="mt-2 text-white font-weight-bold">{{ text }}</p>
    </div>
  </div>
</template>

<script setup>
defineProps({
  active: {
    type: Boolean,
    default: false
  },
  text: {
    type: String,
    default: 'Please wait...'
  }
})
</script>

<style scoped>
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent black */
  z-index: 9999; /* High z-index to cover everything */
  display: flex;
  justify-content: center;
  align-items: center;
  backdrop-filter: blur(2px); /* Blur effect for "coolness" */
}

.spinner-container {
  text-align: center;
  padding: 20px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 10px;
}
</style>
