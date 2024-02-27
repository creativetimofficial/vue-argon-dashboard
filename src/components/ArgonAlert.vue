<script setup>
defineProps({
  color: {
    type: String,
    default: "success",
  },
  icon: {
    type: String,
    default: "",
  },
  dismissible: {
    type: Boolean,
    default: false,
  },
});
const getClasses = (color, dismissible) => {
  let colorValue, dismissibleValue;

  colorValue = color ? `alert-${color}` : null;
  dismissibleValue = dismissible ? "alert-dismissible fade show" : null;
  return `${colorValue} ${dismissibleValue}`;
};

const getIcon = (icon) => (icon ? icon : null);
</script>
<template>
  <div
    class="alert text-white font-weight-bold"
    role="alert"
    :class="getClasses(color, dismissible)"
  >
    <span class="alert-icon">
      <i :class="getIcon(icon)" />
    </span>
    <span class="alert-text">
      &nbsp;
      <slot />
    </span>
    <button
      v-if="dismissible"
      type="button"
      class="btn-close d-flex justify-content-center align-items-center"
      data-bs-dismiss="alert"
      aria-label="Close"
    >
      <span aria-hidden="true" class="text-lg font-weight-bold">&times;</span>
    </button>
  </div>
</template>
