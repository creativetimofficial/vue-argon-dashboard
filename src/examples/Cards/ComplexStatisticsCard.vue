<script setup>
import { ref } from "vue";

const showMenu = ref(false);
defineProps({
  img: {
    type: [String, Object],
    url: String,
    overlay: String,
    default: () => ({
      overlay: "dark",
    }),
  },
  icon: {
    type: [String, Object],
    required: true,
    component: {
      type: String,
    },
    background: {
      type: String,
    },
    default: () => ({
      background: "bg-white",
    }),
  },
  count: {
    type: Object,
    default: null,
    number: {
      type: Number,
    },
    label: {
      type: String,
    },
  },
  percentage: {
    type: [String, Object],
    default: "",
    label: {
      type: String,
    },
    color: {
      type: String,
    },
  },
});
</script>
<template>
  <div
    class="card overflow-hidden shadow-lg"
    :style="`background-image: url(${typeof img === 'object' ? img.url : img});
  background-size: cover`"
  >
    <div class="colored-shadow"></div>
    <span
      :class="`mask bg-gradient-${
        typeof img === 'object' ? img.overlay : 'dark'
      }`"
    ></span>
    <div class="card-body p-3 position-relative">
      <div class="row">
        <div class="col-8 text-start">
          <div
            class="icon icon-shape shadow text-center border-radius-md"
            :class="typeof icon === 'object' ? icon.background : 'bg-white'"
          >
            <i
              class="text-dark text-gradient text-lg opacity-10"
              :class="typeof icon === 'string' ? icon : icon.component"
              aria-hidden="true"
            ></i>
          </div>
          <h5 class="text-white font-weight-bolder mb-0 mt-3">
            {{ count.number }}
          </h5>
          <span class="text-white text-sm">{{ count.label }}</span>
        </div>
        <div class="col-4">
          <div class="dropdown text-end mb-6">
            <a
              id="dropdownUsers1"
              href="javascript:;"
              class="cursor-pointer"
              :class="{ show: showMenu }"
              data-bs-toggle="dropdown"
              aria-expanded="false"
              @click="showMenu = !showMenu"
            >
              <i class="fa fa-ellipsis-h text-white" aria-hidden="true"></i>
            </a>
            <ul
              class="dropdown-menu px-2 py-3"
              aria-labelledby="dropdownUsers1"
              :class="{ show: showMenu }"
            >
              <li>
                <a class="dropdown-item border-radius-md" href="javascript:;"
                  >Action</a
                >
              </li>
              <li>
                <a class="dropdown-item border-radius-md" href="javascript:;"
                  >Another action</a
                >
              </li>
              <li>
                <a class="dropdown-item border-radius-md" href="javascript:;"
                  >Something else here</a
                >
              </li>
            </ul>
          </div>
          <p
            class="text-sm text-end font-weight-bolder mt-auto mb-0"
            :class="percentage.color ? percentage.color : 'text-white'"
          >
            {{
              typeof percentage === "string"
                ? `${percentage}`
                : `${percentage.label}`
            }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
