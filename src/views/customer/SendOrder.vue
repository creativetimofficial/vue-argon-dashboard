<template>
  <div>
    <base-header
      class="header pb-8 pt-5 pt-lg-7 d-flex align-items-center"
      style="
        min-height: 300px;
        background-size: cover;
        background-position: center top;
      "
    >
      <!-- Mask -->
      <span class="mask bg-gradient-success opacity-8"></span>
      <!-- Header container -->
      <div class="container-fluid d-flex align-items-center">
        <div class="row">
          <div class="col-lg-10 col-md-30">
            <h1 class="display-2 text-white">Antar Barang</h1>
          </div>
        </div>
      </div>
    </base-header>

    <div class="container-fluid mt--7">
      <div class="row">
        <div class="col-xl-8 order-xl-1">
          <card shadow type="secondary">

             <form>
              <h6 class="heading-small text-muted mb-4">Antar Barang</h6>
              <div class="pl-lg-4">
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
                  <p>Where do you want to send the package?</p>
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
                <br>
                <hr class="my-4" />
               <h6 class="heading-small text-muted mb-4">Recepient Details</h6>
                <div class="pl-lg-4">
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label for="verify" class="col-sm-50 ">
                            Recepient's Name
                            </label>

                            <input
                            type="text"
                            class="form-control"
                            placeholder="Recepient's name"
                            v-model="model.Name"
                            />
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label for="verify" class="col-sm-50 ">
                            Recepient's Phone Number
                            </label>
                            <input
                            type="text"
                            class="form-control"
                            placeholder="08XXXXXXXXX"
                            v-model="model.phone_number"
                            />
                        </div>
                    </div>
              </div>
              <br>
                <hr class="my-4" />
               <h6 class="heading-small text-muted mb-4">Item Details</h6>
                <div class="pl-lg-4">
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label for="verify" class="col-sm-50 ">
                            Weight
                            </label>

                            <input
                            type="text"
                            class="form-control"
                            placeholder="xx gram"
                            v-model="model.weight"
                            />
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label for="verify" class="col-sm-50 ">
                            Type
                            </label>
                            <input
                            type="text"
                            class="form-control"
                            placeholder="food,stuff,etc"
                            v-model="model.type"
                            />
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label for="verify" class="col-sm-50 ">
                            Delivery Instruction
                            </label>
                            <input
                            type="text"
                            class="form-control"
                            placeholder="be careful with package"
                            v-model="model.delivery_instruction"
                            />
                        </div>
                    </div>
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
//import http from "../../http.js";

export default {
  name: "user-order",
  data() {
    return {
      model: {

      },
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
    };
  },
  methods: {
    submitAction() {
     

      
    }
  }
};
</script>
<style></style>