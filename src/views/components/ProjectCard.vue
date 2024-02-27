<script setup>
defineProps({
  title: {
    type: String,
    default: "",
  },
  description: {
    type: String,
    default: "",
  },
  headings: {
    type: Array,
    default: () => [],
  },
  rows: {
    type: Array,
    logo: String,
    tool: String,
    teamMembers: Array,
    price: String,
    progress: Number,
    default: () => [],
  },
  action: {
    type: Array,
    route: String,
    label: String,
  },
});
</script>
<template>
  <div class="card">
    <div class="card-header pb-0">
      <div class="row">
        <div class="col-lg-6 col-7">
          <h6>{{ title }}</h6>
          <p class="text-sm mb-0" v-html="description" />
        </div>
        <div class="col-lg-6 col-5 my-auto text-end" v-if="action">
          <div class="dropdown float-lg-start pe-4">
            <a
              class="cursor-pointer"
              :id="title"
              data-bs-toggle="dropdown"
              aria-expanded="false"
            >
              <i class="fa fa-ellipsis-v text-secondary" aria-hidden="true"></i>
            </a>
            <ul
              class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5 text-end"
              :aria-labelledby="title"
              style
            >
              <li v-for="{ route, label } in action" :key="label">
                <a class="dropdown-item border-radius-md" :href="route">{{
                  label
                }}</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="card-body px-0 pb-2">
      <div class="table-responsive">
        <table class="table align-items-center mb-0">
          <thead>
            <tr>
              <th
                v-for="heading in headings"
                :key="heading"
                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
              >
                {{ heading }}
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="{ logo, tool, teamMembers, price, progress } in rows"
              :key="tool"
            >
              <td>
                <div class="d-flex py-1">
                  <div>
                    <img :src="logo" class="avatar avatar-sm me-3" alt="xd" />
                  </div>
                  <div class="d-flex flex-column justify-content-center">
                    <h6 class="mb-0 text-sm me-3">{{ tool }}</h6>
                  </div>
                </div>
              </td>
              <td>
                <div class="avatar-group mt-2">
                  <a
                    v-for="(image, index) in teamMembers"
                    :key="image + index"
                    href="javascript:;"
                    class="avatar avatar-xs rounded-circle"
                    data-bs-toggle="tooltip"
                    data-bs-placement="bottom"
                    title
                    data-bs-original-title="Ryan Tompson"
                  >
                    <img :src="image" alt="team1" />
                  </a>
                </div>
              </td>
              <td class="align-middle text-center text-sm">
                <span class="text-xs font-weight-bold">{{ price }}</span>
              </td>
              <td class="align-middle">
                <div class="progress-wrapper w-75 mx-auto">
                  <div class="progress-info">
                    <div class="progress-percentage">
                      <span class="text-xs font-weight-bold"
                        >{{ progress }}%</span
                      >
                    </div>
                  </div>
                  <div class="progress">
                    <div
                      class="progress-bar bg-gradient-info"
                      :class="` w-${progress}`"
                      role="progressbar"
                      :aria-valuenow="progress"
                      aria-valuemin="0"
                      aria-valuemax="100"
                    ></div>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
