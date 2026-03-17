<script setup>
import { computed } from "vue";
import { useStore } from "vuex";
const store = useStore();

const isRTL = computed(() => store.state.isRTL);
const layout = computed(() => store.state.layout);
const settings = computed(() => store.state.documentationSettings);

defineProps({
  card: {
    type: Object,
    required: true,
    title: String,
    description: String,
    links: {
      type: Array,
      label: String,
      route: String,
      color: String,
    },
  },
});
</script>
<template>
  <div
    v-show="layout !== 'landing'"
    class="card card-plain shadow-none"
    id="sidenavCard"
  >
    <div class="p-3 card-body text-center w-100 pt-0">
      <img
        class="w-50 mx-auto"
        src="@/assets/img/illustrations/icon-documentation.svg"
        alt="sidebar_illustration"
      />

      <h6 class="mb-0 text-dark up">{{ settings.help_title }}</h6>
      <p class="text-xs font-weight-bold">{{ settings.help_description }}</p>
    </div>

    <!-- Documentation Button -->
    <a
      v-if="settings.docs_link"
      :href="settings.docs_link"
      target="_blank"
      class="mb-3 btn btn-dark btn-sm w-100"
    >
      <i class="fas fa-book me-2"></i> Documentation
    </a>

    <!-- Youtube Button -->
    <a
      v-if="settings.youtube_link"
      :href="settings.youtube_link"
      target="_blank"
      class="btn btn- youtube-btn btn-sm w-100 mb-2"
    >
      <i class="fab fa-youtube me-2"></i> Youtube
    </a>
  </div>
</template>

<style scoped>
.youtube-btn {
  background-color: #ff0000;
  color: #ffffff;
  border: none;
}
.youtube-btn:hover {
  background-color: #cc0000;
  color: #ffffff;
}
</style>
