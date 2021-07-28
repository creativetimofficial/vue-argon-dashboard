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
                    <h3 class="mb-0">Feedback</h3>
                  </div>
                </div>
              </div>
            </template>

            <form>

              <div class="pl-lg-4">
                <div class="row">
                  <p>Rating:</p>
                  <br>
                   <div class="col-8">
                  <p> {{ new_review.rating }} </p>
                </div>
                </div>
              </div>
              <div class="pl-lg-4">
                <div class="row">
                  <p>Review:</p>
                  <br>
                  <div class="col-8">
                  <p> {{ new_review.review }} </p>
                </div>
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
      new_review: {
          rating: "",
          review: "",
      },
    };
  },
 mounted() {
    const url = "/cust/read/feedback/" + this.$route.params.id;
    http.get(url).then((response) => {
      this.new_review = response.data[0];
    });
    console.log("hahah"+this.$route.params.id);
  },
};
</script>
<style></style>
