<script setup>
defineProps({
  title: {
    type: String,
    default: "",
  },
  description: {
    type: String,
    default: "",
  },
  dark: {
    type: Boolean,
    default: false,
  },
});
</script>
<template>
  <div class="card" :class="dark ? 'bg-gradient-dark' : ''">
    <div class="pb-0 card-header" :class="dark ? 'bg-transparent' : ''">
      <h6 :class="dark ? 'text-white' : ''">{{ title }}</h6>
      <!--  eslint-disable-next-line vue/no-v-html -->
      <p v-if="description" class="text-sm" v-html="description"></p>
    </div>
    <div class="p-3 card-body">
      <div
        class="timeline timeline-one-side"
        :data-timeline-axis-style="dark ? 'dashed' : 'dotted'"
      >
        <slot />
      </div>
    </div>
  </div>
</template>
