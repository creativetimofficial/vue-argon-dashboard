<script setup>
import { ref, computed } from "vue";
import { useI18n } from "vue-i18n";

const { locale } = useI18n();
const show = ref(false);

const currentLanguage = computed(() => {
  return locale.value === "en" ? "English" : "Indonesia";
});

const currentFlag = computed(() => {
  return locale.value === "en" ? "🇺🇸" : "🇮🇩";
});

const toggleDropdown = () => {
  show.value = !show.value;
};

const closeDropdown = () => {
  setTimeout(() => {
    show.value = false;
  }, 200);
};

const setLanguage = (lang) => {
  locale.value = lang;
  localStorage.setItem("lang", lang);
  show.value = false;
};
</script>

<template>
  <div class="dropdown d-inline-block position-relative">
    <a
      href="javascript:;"
      class="p-0 nav-link text-white dropdown-toggle"
      :class="{ show: show }"
      id="languageDropdown"
      @click="toggleDropdown"
      @blur="closeDropdown"
    >
      <span class="me-1">{{ currentFlag }}</span>
      <span class="d-sm-inline d-none">{{ currentLanguage }}</span>
    </a>
    <ul
      class="dropdown-menu dropdown-menu-end px-2 py-3"
      :class="{ show: show }"
      aria-labelledby="languageDropdown"
      style="top: 100%; right: 0;"
    >
      <li>
        <a
          class="dropdown-item border-radius-md"
          href="javascript:;"
          @mousedown="setLanguage('en')"
        >
          <div class="d-flex align-items-center">
            <span class="me-2">🇺🇸</span>
            <span>English</span>
            <i v-if="locale === 'en'" class="fas fa-check ms-auto text-success text-xs"></i>
          </div>
        </a>
      </li>
      <li>
        <a
          class="dropdown-item border-radius-md"
          href="javascript:;"
          @mousedown="setLanguage('id')"
        >
          <div class="d-flex align-items-center">
            <span class="me-2">🇮🇩</span>
            <span>Indonesia</span>
            <i v-if="locale === 'id'" class="fas fa-check ms-auto text-success text-xs"></i>
          </div>
        </a>
      </li>
    </ul>
  </div>
</template>

<style scoped>
.dropdown-item {
  cursor: pointer;
}
.dropdown-toggle::after {
  display: none;
}
</style>
