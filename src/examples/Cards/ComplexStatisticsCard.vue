<template>
  <div
    class="card overflow-hidden shadow-lg"
    :style="`background-image: url(${img});
  background-size: cover`"
  >
    <div class="colored-shadow"></div>
    <span class="mask bg-gradient-dark"></span>
    <div class="card-body p-3 position-relative">
      <div class="row">
        <div class="col-8 text-start">
          <div
            class="icon icon-shape bg-white shadow text-center border-radius-md"
            :class="icon_bg"
          >
            <i
              class="text-dark text-gradient text-lg opacity-10"
              :class="classIcon"
              aria-hidden="true"
            ></i>
          </div>
          <h5 class="text-white font-weight-bolder mb-0 mt-3">{{ caption }}</h5>
          <span class="text-white text-sm">{{ activeUsers }}</span>
        </div>
        <div class="col-4">
          <div class="dropdown text-end mb-6">
            <a
              href="javascript:;"
              class="cursor-pointer"
              :class="{ show: showMenu }"
              id="dropdownUsers1"
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
                <a class="dropdown-item border-radius-md" href="javascript:;">Action</a>
              </li>
              <li>
                <a class="dropdown-item border-radius-md" href="javascript:;">Another action</a>
              </li>
              <li>
                <a class="dropdown-item border-radius-md" href="javascript:;">Something else here</a>
              </li>
            </ul>
          </div>
          <p
            class="text-white text-sm text-end font-weight-bolder mt-auto mb-0"
            :class="class_percentage"
          >{{ percentage }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "complex-statistics-card",
  data() {
    return {
      showMenu: false,
    };
  },
  props: {
    classIcon: {
      type: String,
      required: true,
    },
    activeUsers: String,
    percentage: String,
    class_percentage: {
      type: String,
      default: "text-success",
    },
    icon_bg: {
      type: String,
      default: "bg-white",
    },
    caption: String,
    img: String,
  },
};
</script>
