<script setup>
defineProps({
  color: {
    type: String,
    default: "success",
  },
  variant: {
    type: String,
    default: "fill",
  },
  percentage: {
    type: Number,
    required: true,
  },
});

const getClasses = (color, variant) => {
  let colorValue;

  if (variant === "gradient") {
    colorValue = `bg-gradient-${color}`;
  } else {
    colorValue = `bg-${color}`;
  }

  return `${colorValue}`;
};
</script>
<template>
  <div class="progress">
    <div
      class="progress-bar"
      :class="getClasses(color, variant)"
      :style="{ width: percentage + '%' }"
    ></div>
  </div>
</template>
