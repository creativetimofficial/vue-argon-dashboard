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
                    <h3 class="mb-0">Activity Details</h3>
                  </div>
                </div>
              </div>
            </template>

            <form>
              <h6 class="heading-small text-muted mb-4">Activity Info</h6>

              <div class="pl-lg-4">
                <div class="row">
                  <div class="col-lg-6 mb-3">
                    <!-- <p>Driver</p>
                    <input
                      type="text"
                      class="form-control"
                      v-model="data_act.id_driver"
                    /> -->
                    <div
                      class="btn btn-info mt-2"
                      @click="driverDetailAction(data_act.id_driver)"
                    >
                      Go to driver Profile
                    </div>
                  </div>
                  <!-- <div class="col-lg-6 mb-3">
                    <p>Customer</p>
                    <input
                      type="text"
                      class="form-control"
                      v-model="data_act.id_customer"
                    />
                    <div
                      class="btn btn-info mt-2"
                      @click="customerDetailAction(data_act.id_customer)"
                    >
                      Go to customer Profile
                    </div>
                  </div> -->
                </div>
                <div class="row">
                  <div class="col-lg-6 mb-3">
                    <p>Date</p>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="yyyy-mm-dd"
                      v-model="data_act.date"
                    />
                  </div>
                  <div class="col-lg-6 mb-3">
                    <p>Activity Status</p>
                    <div class="btn btn-primary mt-2" @click="openFile(item)">
                      {{ data_act.activity_status }}
                    </div>
                  </div>
                </div>
              </div>
              <div class="row ml-1">
                <div class="col-lg-6 mb-3">
                  <p>Type of Service</p>
                  <input
                    type="text"
                    class="form-control"
                    v-model="data_act.type_of_service"
                  />
                </div>
                <div class="col-lg-6 mb-3">
                  <p>Price</p>
                  <input
                    type="text"
                    class="form-control"
                    v-model="data_act.price"
                  />
                </div>
              </div>
              <div class="row ml-1">
                <div class="col-lg-6 mb-3">
                  <p>Start Location</p>
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
                  <p>End Location</p>
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
              <!-- <div class="row ml-1" v-show="data_act.id_feedback != null">
                <div class="col-lg-6">
                  <p>Feedback</p>
                  <div class="row">
                  <p>Rating</p>
                  <input
                    type="text"
                    class="form-control"
                    v-model="data_feedback.rating"
                  />
                  <p>Feedback</p>
                  <textarea
                    type="text"
                    class="form-control"
                    aria-label="Large"
                    v-model="data_feedback.review"
                  />
              </div> -->
                <!-- </div>
              </div>
              <hr class="my-4" /> --> 

              <h6
                class="heading-small text-muted mb-4"
                v-show="data_act.type_of_service == 'Antar Barang'"
              >
                Item Detail
              </h6>

              <div
                class="pl-lg-4"
                v-show="data_act.type_of_service == 'Antar Barang'"
              >
                <div class="row">
                  <div class="col-lg-6 mb-3">
                    <p>ID</p>
                    <input
                      type="text"
                      class="form-control"
                      v-model="item_detail._id"
                    />
                  </div>
                  <div class="col-lg-6 mb-3">
                    <p>Weight</p>
                    <input
                      type="text"
                      class="form-control"
                      v-model="item_detail.weight"
                    />
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-6 mb-3">
                    <p>Type</p>
                    <input
                      type="text"
                      class="form-control"
                      v-model="item_detail.type"
                    />
                  </div>
                  <div class="col-lg-6 mb-3">
                    <p>Delivery Instruction</p>
                    <input
                      type="text"
                      class="form-control"
                      v-model="item_detail.delivery_instruction"
                    />
                  </div>
                </div>
              </div>

              <hr
                class="my-4"
                v-show="data_act.type_of_service == 'Antar Barang'"
              />

              <h6
                class="heading-small text-muted mb-4"
                v-show="data_act.type_of_service == 'Antar Barang'"
              >
                Recipient Details
              </h6>

              <div
                class="pl-lg-4"
                v-show="data_act.type_of_service == 'Antar Barang'"
              >
                <div class="row">
                  <div class="col-lg-6 mb-3">
                    <p>Name</p>
                    <input
                      type="text"
                      class="form-control"
                      v-model="recipient_detail.recipient_name"
                    />
                  </div>
                  <div class="col-lg-6 mb-3">
                    <p>Phone Number</p>
                    <input
                      type="text"
                      class="form-control"
                      v-model="recipient_detail.recipient_phone_number"
                    />
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
      // data_feedback: {
      //   rating: "",
      // },
      data_act: {},
      start_loc: {},
      end_loc: {},
      item_detail: {},
      recipient_detail: {},
      blockedText: "Block",
    };
  },
  mounted() {
    const url = "/cust/read/order/" + this.$route.params.id;
    http.get(url).then((response) => {
      this.data_act = response.data[0];
      this.start_loc = response.data[0].start_loc;
      this.end_loc = response.data[0].end_loc;
      if (response.data[0].type_of_service == "Antar Barang") {
        this.item_detail = response.data[0].item_detail;
        this.recipient_detail = response.data[0].recipient_detail;
      }

    // const url = "/admin/read/feedback/" + this.$route.params.id;
    // http.get(url).then((response) => {
    //   this.data_feedback = response.data[0].data_feedback;
    // });
    });
  },
  methods: {
    driverDetailAction(_id) {
      this.$router.push({
        name: "Driver Profile",
        params: { id: _id },
      });
    },
    // customerDetailAction(_id) {
    //   this.$router.push({
    //     name: "Customer Profile",
    //     params: { id: _id },
    //   });
    // },
    // feedbackDetailAction(_id) {
    //   this.$router.push({
    //     name: "Feedback Detail",
    //     params: { id: _id },
    //   });
    // },
  },
};
</script>
<style></style>
