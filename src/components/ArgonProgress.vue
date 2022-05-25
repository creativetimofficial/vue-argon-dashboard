<template>
  <div class="progress">
    <div
      class="progress-bar"
      :class="getClasses(color, variant)"
      :style="{width: percentage + '%'}"
    ></div>
  </div>
</template>

<script>
export default {
  name: "argon-progress",
  props: {
    color: {
      type: String,
      default: "primary",
    },
    variant: {
      type: String,
      default: "fill",
    },
    percentage: String,
  },
  methods: {
    getClasses: (color, variant) => {
      let colorValue;

      if (variant === "gradient") {
        colorValue = `bg-gradient-${color}`;
      } else {
        colorValue = `bg-${color}`;
      }

      return `${colorValue}`;
    },
  },
};
</script>