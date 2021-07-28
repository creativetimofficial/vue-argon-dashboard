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
        <div class="col-xl-8 order-xl-1">
          <card shadow type="secondary">
            <template v-slot:header>
              <div class="bg-white border-0">
                <div class="row align-items-center">
                  <div class="col-8">
                    <h3 class="mb-0">Transportasi Mobil</h3>
                  </div>
                </div>
              </div>
            </template>

            <form>

              <div class="pl-lg-4">
                <div class="row">
                </div>
              </div>
              <div class="row ml-1">
                <div class="col-lg-6 mb-3">
                  <p>Where are you now?</p>
                  <label for="">Address</label>
                  <input
                    type="text"
                    class="form-control mb-1"
                    v-model="start_loc.address"
                  />
                  <label for="">Latitude</label>
                  <input
                    type="text"
                    class="form-control mb-1"
                    v-model="start_loc.latitude"
                  />
                  <label for="">Longitude</label>
                  <input
                    type="text"
                    class="form-control mb-1"
                    v-model="start_loc.longitude"
                  />
                </div>
                <div class="col-lg-6 mb-3">
                  <p>Where do you want to go?</p>
                  <label for="">Address</label>
                  <input
                    type="text"
                    class="form-control mb-1"
                    v-model="end_loc.address"
                  />
                  <label for="">Latitude</label>
                  <input
                    type="text"
                    class="form-control mb-1"
                    v-model="end_loc.latitude"
                  />
                  <label for="">Longitude</label>
                  <input
                    type="text"
                    class="form-control mb-1"
                    v-model="end_loc.longitude"
                  />
                </div>
              </div>
              <div class="row ml-1">
                <div class="col-lg-6 mb-3">
                  <p>Price</p>
                  <input
                    type="text"
                    class="form-control"
                    v-model="price"
                  />
                </div>
              </div>
              <div class="btn btn-info mt-3" @click="submitAction">Order Now!</div>
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
      start_loc: {
        address: "",
        latitude: "",
        longtitude: "",
      },
      end_loc: {
        address: "",
        latitude: "",
        longtitude: "",
      },
      price : "",
    };
  },
  
  methods: {
     submitAction() {
         let formData = {
            start_loc: {
              latitude: this.start_loc.latitude,
              longtitude: this.start_loc.longtitude,
            },
            end_loc: {
              latitude: this.end_loc.latitude,
              longtitude: this.end_loc.longtitude,
            },
         };

         const jsonData = JSON.stringify(formData);
         const url = "/cust/create/order";
         http.post(url, jsonData).then((response) => {
          if (response.status == 201) {
            alert("Succesfully create order"); 
          }
        })
        .catch((error) => {
            alert("Failed to create order \n" + error);
          });
     }
  },
};
</script>
<style></style>
