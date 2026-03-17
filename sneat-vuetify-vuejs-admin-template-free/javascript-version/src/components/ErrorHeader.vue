<script setup>
const props = defineProps({
  statusCode: {
    type: [
      String,
      Number,
    ],
    required: false,
  },
  title: {
    type: String,
    required: false,
  },
  description: {
    type: String,
    required: false,
  },
})
</script>

<template>
  <div class="text-center">
    <!-- 👉 Title and subtitle -->
    <h1
      v-if="props.statusCode"
      class="header-title font-weight-medium mb-2"
    >
      {{ props.statusCode }}
    </h1>
    <h4
      v-if="props.title"
      class="text-h4 font-weight-medium mb-2"
    >
      {{ props.title }}
    </h4>
    <p
      v-if="props.description"
      class="text-body-1 mb-6"
    >
      {{ props.description }}
    </p>
  </div>
</template>

<style lang="scss" scoped>
.header-title {
  font-size: clamp(3rem, 5vw, 6rem);
  line-height: clamp(3rem, 5vw, 6rem);
}
</style>
