<script setup>
defineProps({
  icon: {
    type: [String, Object],
    required: true,
    component: {
      type: String,
    },
    background: {
      type: String,
    },
    default: () => ({
      background: "bg-gradient-success",
    }),
  },
  title: {
    type: String,
    required: true,
  },
  description: {
    type: String,
    default: "",
  },
  value: {
    type: [String, Number],
    default: "",
  },
});
</script>
<template>
  <div class="card">
    <div class="p-3 mx-4 text-center card-header d-flex justify-content-center">
      <div
        :class="`icon icon-shape icon-lg shadow text-center border-radius-lg ${icon.background}`"
      >
        <i
          class="opacity-10"
          :class="typeof icon === 'string' ? icon : icon.component"
          aria-hidden="true"
        ></i>
      </div>
    </div>
    <div class="p-3 pt-0 text-center card-body">
      <h6 class="mb-0 text-center">{{ title }}</h6>
      <span class="text-xs">{{ description }}</span>
      <hr class="my-3 horizontal dark" />
      <h5 class="mb-0">{{ value }}</h5>
    </div>
  </div>
</template>
