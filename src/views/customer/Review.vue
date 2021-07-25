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
                  <p>Rating</p>
                  <input
                    type="text"
                    class="form-control"
                    placeholder="1-5"
                    v-model="feedback.rating"
                  />
                </div>
              </div>
              <div class="pl-lg-4">
                <div class="row">
                  <p>Review</p>
                  <input
                    type="text"
                    class="form-control"
                    aria-label="Large"
                    v-model="feedback.review"
                  />
                </div>
                <div class="btn btn-info mt-3" @click="reviewAction">
                    Feedback Order
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
      feedback: {
          rating: "",
          review: "",
      },
    };
  },
 methods :{
     reviewAction(){
         let formData = {
             new_review: {
               
                 rating: this.feedback.rating,
                 review: this.feedback.review
             }
         }

        const jsonData = JSON.stringify(formData);

        const url = "/cust/create/review";

        http.post(url, jsonData).then((response) => {
          if (response.status == 201) {
            alert("Succesfully create feedback"); 
          }
        })
        .catch((error) => {
            alert("Failed to create feedback \n" + error);
          });
     }
 }
};
</script>
<style></style>
