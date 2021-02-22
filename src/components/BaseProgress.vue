<template>
  <div class="progress-wrapper">
    <div
      class="progress-info"
      v-if="$slots.label || label || $slots.percentage || showPercentage"
    >
      <div class="progress-label" v-if="$slots.label || label">
        <span>
          <slot name="label">
            {{ label }}
          </slot>
        </span>
      </div>
      <div
        class="progress-percentage"
        v-if="$slots.percentage || showPercentage"
      >
        <slot name="percentage"> {{ value }} % </slot>
      </div>
    </div>
    <div class="progress" :style="`height: ${height}px`">
      <div
        class="progress-bar"
        :class="computedClasses"
        role="progressbar"
        :aria-valuenow="value"
        aria-valuemin="0"
        aria-valuemax="100"
        :style="`width: ${value}%;`"
      ></div>
    </div>
  </div>
</template>
<script>
export default {
  name: "base-progress",
  props: {
    striped: {
      type: Boolean,
      description: "Whether progress is striped",
    },
    animated: {
      type: Boolean,
      description:
        "Whether progress is animated (works only with `striped` prop together)",
    },
    showPercentage: {
      type: Boolean,
      default: true,
      description: "Whether progress bar should show percentage value",
    },
    height: {
      type: Number,
      default: 3,
      description: "Progress line height",
    },
    label: {
      type: String,
      default: "",
      description: "Progress label",
    },
    type: {
      type: String,
      default: "default",
      description: "Progress type (e.g danger, primary etc)",
    },
    value: {
      type: Number,
      default: 0,
      validator: (value) => {
        return value >= 0 && value <= 100;
      },
      description: "Progress value",
    },
  },
  computed: {
    computedClasses() {
      return [
        { "progress-bar-striped": this.striped },
        { "progress-bar-animated": this.animated },
        { [`bg-${this.type}`]: this.type },
      ];
    },
  },
};
</script>
<style></style>
