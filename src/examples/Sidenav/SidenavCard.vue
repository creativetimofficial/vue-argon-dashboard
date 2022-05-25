<template>
  <div
    v-show="this.$store.state.layout !== 'landing'"
    class="card card-plain shadow-none"
    id="sidenavCard"
  >
    <div class="p-3 card-body text-center w-100 pt-0">
      <img
        class="w-50 mx-auto"
        src="@/assets/img/illustrations/icon-documentation.svg"
        alt="sidebar_illustration"
      />
      <h6 v-if="this.$store.state.isRTL" class="mb-0 text-dark up">
        تحتاج مساعدة ؟
      </h6>
      <h6 v-else class="mb-0 text-dark up">Need Help ?</h6>
      <p v-if="this.$store.state.isRTL" class="text-xs font-weight-bold">
        يرجى التحقق من مستنداتنا
      </p>
      <p v-else class="text-xs font-weight-bold">Please check our docs</p>
    </div>
    <a
      v-if="this.$store.state.isRTL"
      href="https://www.creative-tim.com/learning-lab/vue/overview/argon-dashboard/"
      target="_blank"
      class="mb-3 btn btn-dark btn-sm w-100"
      >توثيق</a
    >
    <a
      v-else
      href="https://www.creative-tim.com/learning-lab/vue/overview/argon-dashboard/"
      target="_blank"
      class="mb-3 btn btn-dark btn-sm w-100"
      >Documentation</a
    >
    <a
      v-if="this.$store.state.isRTL"
      href="https://www.creative-tim.com/product/vue-argon-dashboard-pro"
      target="_blank"
      class="mb-3 btn btn-success btn-sm w-100"
      >التطور للاحترافية</a
    >

    <a
      v-else
      href="https://www.creative-tim.com/product/vue-argon-dashboard-pro"
      target="_blank"
      class="mb-3 btn btn-success btn-sm w-100"
      >Upgrade to pro</a
    >
  </div>
</template>
<script>
import img from "../../assets/img/illustrations/icon-documentation.svg";

export default {
  name: "sidenav-card",
  props: ["textPrimary", "textSecondary", "iconClass"],
  data() {
    return {
      img
    };
  }
};
</script>
