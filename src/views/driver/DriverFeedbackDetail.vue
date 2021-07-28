<template>
  <div>
    <base-header
      class="header pb-8 pt-5 pt-lg-8 d-flex align-items-center"
      style="
        min-height: 200px;
        background-size: cover;
        background-position: center top;
      "
    >
      <!-- Mask -->
      <span class="mask bg-gradient-success opacity-8"></span>
    </base-header>
    <div class="container-fluid mt--7">
      <div class="row">
        <div class="col-xl-4 order-xl-1">
          <card shadow type="secondary">
            <template v-slot:header>
              <div class="bg-white border-0">
                <div class="row align-items-center">
                  <div class="col-8">
                    <h3 class="mb-0">Activity Details</h3>
                  </div>
                </div>
              </div>
            </template>

            <form>
              <h6 class="heading-small text-muted mb-4">Feedback</h6>

              <div class="pl-lg-4">
                <div class="row">
                  <p>Rating</p>
                  <input
                    type="text"
                    class="form-control"
                    v-model="data_feedback.rating"
                  />
                </div>
              </div>
              <div class="pl-lg-4">
                <div class="row">
                  <p>Feedback</p>
                  <textarea
                    type="text"
                    class="form-control"
                    aria-label="Large"
                    v-model="data_feedback.review"
                  />
                </div>
              </div>
            </form>
          </card>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import http from "../../http.js";

export default {
  name: "user-profile",
  data() {
    return {
      data: "",
      data_feedback: {},
      profile: {},
      blockedText: "Block",
    };
  },
  mounted() {
    const url = "/admin/read/feedback/" + this.$route.params.id;
    http.get(url).then((response) => {
      this.data_feedback = response.data[0];
    });
  },
};
</script>
<style></style>
