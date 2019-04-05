<template>
  <div class="navbar-minimize-fixed" style="opacity: 1;">
    <fade-transition>
      <sidebar-toggle-button v-if="showButton" class="text-muted" />
    </fade-transition>
  </div>
</template>
<script>
import { FadeTransition } from 'vue2-transitions';
import SidebarToggleButton from './SidebarToggleButton';

export default {
  name: 'sidebar-fixed-toggle-button',
  components: {
    SidebarToggleButton,
    FadeTransition
  },
  data() {
    return {
      showScrollThreshold: 50,
      currentScroll: 0,
      scrollTicking: false
    };
  },
  computed: {
    showButton() {
      return this.currentScroll > this.showScrollThreshold;
    }
  },
  methods: {
    handleScroll() {
      this.currentScroll = window.scrollY;

      if (!this.scrollTicking) {
        window.requestAnimationFrame(() => {
          this.scrollTicking = false;
        });

        this.scrollTicking = true;
      }
    }
  },
  mounted() {
    window.addEventListener('scroll', this.handleScroll);
  }
};
</script>
<style></style>
