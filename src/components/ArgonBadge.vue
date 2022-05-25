<template>
  <span class="badge" :class="getClasses(variant, color, size, circle, floating)">
    <slot />
  </span>
</template>

<script>
export default {
  name: "argon-badge",
  props: {
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
    circle: {
      type: Boolean,
      default: false,
    },
    floating: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    getClasses: (variant, color, size, circle, floating) => {
      let colorValue, sizeValue, circleValue, floatingValue;

      if (variant === "gradient") {
        colorValue = `bg-gradient-${color}`;
      } else {
        colorValue = `badge-${color}`;
      }

      sizeValue = size ? `badge-${size}` : null;

      circleValue = circle ? "badge-circle" : null;

      floatingValue = floating ? "badge-floating" : null;

      return `${colorValue} ${sizeValue} ${circleValue} ${floatingValue}`;
    },
  },
};
</script>
