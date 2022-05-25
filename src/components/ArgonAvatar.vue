<template>
  <div class="avatar" :class="getSize(size)">
    <img :src="img" :alt="alt" :class="[getClasses(shadow, circular, borderRadius)]" />
  </div>
</template>

<script>
export default {
  name: "argon-avatar",
  props: {
    img: String,
    alt: String,
    size: String,
    shadow: String,
    borderRadius: String,
    circular: Boolean,
  },
  methods: {
    getSize: (size) => (size ? `avatar-${size}` : null),
    getClasses: (shadow, circular, borderRadius) => {
      let shadowValue, circularValue, borderRadiusValue;

      if (shadow) {
        shadowValue = shadow === "regular" ? "shadow" : `shadow-${shadow}`;
      } else {
        shadowValue = null;
      }

      circularValue = circular ? "rounded-circle" : null;

      borderRadiusValue = borderRadius ? `border-radius-${borderRadius}` : null;

      return `${shadowValue} ${circularValue} ${borderRadiusValue}`;
    },
  },
};
</script>
