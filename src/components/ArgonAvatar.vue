<script setup>
defineProps({
  image: {
    type: String,
    required: true,
  },
  alt: {
    type: String,
    default: "",
  },
  size: {
    type: String,
    default: null,
  },
  shadow: {
    type: String,
    default: null,
  },
  borderRadius: {
    type: String,
    default: null,
  },
  circular: {
    type: Boolean,
    default: false,
  },
});
const getSize = (size) => (size ? `avatar-${size}` : null);
const getClasses = (shadow, circular, borderRadius) => {
  let shadowValue, circularValue, borderRadiusValue;

  if (shadow) {
    shadowValue = shadow === "regular" ? "shadow" : `shadow-${shadow}`;
  } else {
    shadowValue = null;
  }

  circularValue = circular ? "rounded-circle" : null;

  borderRadiusValue = borderRadius ? `border-radius-${borderRadius}` : null;

  return `${shadowValue} ${circularValue} ${borderRadiusValue}`;
};
</script>
<template>
  <div class="avatar" :class="getSize(size)">
    <img
      :src="image"
      :alt="alt"
      :class="[getClasses(shadow, circular, borderRadius)]"
    />
  </div>
</template>
