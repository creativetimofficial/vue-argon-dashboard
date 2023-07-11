<script setup>
defineProps({
  size: {
    type: String,
    default: "md",
  },
  color: {
    type: String,
    default: "success",
  },
  variant: {
    type: String,
    default: "fill",
  },
  circular: {
    type: Boolean,
    default: false,
  },
  floating: {
    type: Boolean,
    default: false,
  },
});

const getClasses = (variant, color, size, circular, floating) => {
  let colorValue, sizeValue, circleValue, floatingValue;

  if (variant === "gradient") {
    colorValue = `bg-gradient-${color}`;
  } else {
    colorValue = `badge-${color}`;
  }

  sizeValue = size ? `badge-${size}` : null;

  circleValue = circular ? "badge-circle" : null;

  floatingValue = floating ? "badge-floating" : null;

  return `${colorValue} ${sizeValue} ${circleValue} ${floatingValue}`;
};
</script>
<template>
  <span
    class="badge"
    :class="getClasses(variant, color, size, circular, floating)"
  >
    <slot />
  </span>
</template>
