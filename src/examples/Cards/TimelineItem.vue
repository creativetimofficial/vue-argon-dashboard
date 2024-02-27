<script setup>
defineProps({
  color: {
    type: String,
    default: "",
  },
  icon: {
    type: Object,
    required: true,
    component: String,
    color: String,
  },
  title: {
    type: String,
    required: true,
  },
  dateTime: {
    type: String,
    default: "",
  },
  description: {
    type: String,
    default: "",
  },
  badges: {
    type: Array,
    default: () => [],
  },
});
</script>
<template>
  <div class="mb-3 timeline-block">
    <span
      class="timeline-step p-3"
      :class="`${$parent.dark ? 'bg-dark' : 'bg-'}${color}`"
    >
      <i :class="`text-${icon.color} text-gradient ${icon.component}`"></i>
    </span>
    <div class="timeline-content">
      <h6
        class="mb-0 text-sm font-weight-bold"
        :class="$parent.dark ? 'text-white' : 'text-dark'"
      >
        {{ title }}
      </h6>
      <p class="mt-1 mb-0 text-xs text-secondary font-weight-normal">
        {{ dateTime }}
      </p>
      <p
        v-if="description"
        class="mt-3 mb-2 text-sm"
        :class="$parent.dark ? 'text-white' : 'text-dark'"
      >
        {{ description }}
      </p>
      <span
        v-for="(badge, index) of badges"
        :key="index"
        :class="`badge badge-sm bg-gradient-${icon.color} me-1`"
        >{{ badge }}</span
      >
    </div>
  </div>
</template>
