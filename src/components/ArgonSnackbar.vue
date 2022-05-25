<template>
  <div class="toast fade show p-2 mt-2" :class="getColor(color)">
    <div class="toast-header bg-transparent border-0">
      <i class="me-2" :class="getIcon(icon, iconColor)" />
      <span class="me-auto font-weight-bold" :class="getTextColor(color)">
        {{
        title
        }}
      </span>
      <small :class="getTextColor(color)">{{ date }}</small>
      <i
        class="fas fa-times text-md ms-3 cursor-pointer"
        :class="getTextColor(color)"
        @click="closeHandler"
      />
    </div>
    <hr class="horizontal dark m-0" :class="getHrColor(color)" />
    <div class="toast-body" :class="getTextColor(color)">{{ description }}</div>
  </div>
</template>

<script>
export default {
  name: "argon-snackbar",
  props: {
    title: String,
    date: String,
    description: String,
    icon: String,
    color: {
      type: String,
      default: "success",
    },
    iconColor: {
      type: String,
      default: "success",
    },
    closeHandler: Function,
  },
  methods: {
    getColor: (color) => {
      let colorValue;

      if (color === "white") {
        colorValue = "bg-white";
      } else {
        colorValue = `bg-gradient-${color}`;
      }

      return colorValue;
    },
    getIcon: (icon, iconColor) =>
      icon && iconColor ? `${icon} text-${iconColor}` : null,
    getTextColor: (color) => (color === "white" ? "text-dark" : "text-white"),
    getHrColor: (color) => (color === "white" ? "dark" : "light"),
  },
};
</script>
