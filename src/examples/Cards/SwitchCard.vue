<template>
  <div class="card h-100">
    <div class="card-body">
      <div class="mb-4 d-flex">
        <p class="mb-0">{{ state }}</p>
        <div class="form-check form-switch ms-auto">
          <input
            class="form-check-input"
            type="checkbox"
            id="flexSwitchCheckHumidity"
            :checked="isChecked"
          />
        </div>
      </div>
      <slot name="icon" />
      <p class="mb-0 font-weight-bold" :class="classCustom">{{ stateText }}</p>
      <span class="text-xs"> {{ stateDescription }} </span>
    </div>
  </div>
</template>

<script>
export default {
  name: "switch-card",
  props: ["state", "stateText", "stateDescription", "isChecked", "classCustom"],
};
</script>
