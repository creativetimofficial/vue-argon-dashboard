<template>
  <li class="page-item" :class="getClasses(active, disabled)">
    <a class="page-link" href="javascript:;">
      {{ prev || next ? null : label }}
      <i v-if="prev" class="fa fa-angle-left"></i>
      <i v-if="next" class="fa fa-angle-right"></i>
    </a>
  </li>
</template>

<script>
export default {
  name: "argon-pagination-item",
  props: {
    label: String,
    active: {
      type: Boolean,
      default: false,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    prev: {
      type: Boolean,
      default: false,
    },
    next: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    getClasses: (active, disabled) => {
      let activeValue, disabledValue;

      activeValue = active ? "active" : null;
      disabledValue = disabled ? "disabled" : null;

      return `${activeValue} ${disabledValue}`;
    },
  },
};
</script>
