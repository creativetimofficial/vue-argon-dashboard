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
        <div class="col-xl-4 order-xl-2 mb-5 mb-xl-0">
          <div class="card card-profile shadow">
            <div class="row justify-content-center">
              <div class="col-lg-3 order-lg-2">
                <div class="card-profile-image">
                  <a href="#">
                    <img
                      src="img/theme/team-4-800x800.jpg"
                      class="rounded-circle"
                    />
                  </a>
                </div>
              </div>
            </div>
            <div
              class="card-header text-center border-0 pt-8 pt-md-4 pb-0 pb-md-4"
            ></div>
            <div class="card-body pt-0 pt-md-4">
              <div class="row">
                <div class="col">
                  <div
                    class="card-profile-stats d-flex justify-content-center mt-md-5"
                  ></div>
                </div>
              </div>
              <div class="text-center">
                <h3>
                  Jessica Jones<span class="font-weight-light">, 27</span>
                </h3>
                <div class="h5 font-weight-300">
                  <i class="ni location_pin mr-2"></i>Bucharest, Romania
                </div>
                <div class="h5 mt-4">
                  <i class="ni business_briefcase-24 mr-2"></i>Solution Manager
                  - Creative Tim Officer
                </div>
                <div>
                  <i class="ni education_hat mr-2"></i>University of Computer
                  Science
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-8 order-xl-1">
          <card shadow type="secondary">
            <template v-slot:header>
              <div class="bg-white border-0">
                <div class="row align-items-center">
                  <div class="col-8">
                    <h3 class="mb-0">Driver's account</h3>
                  </div>
                </div>
              </div>
            </template>

            <form>
              <h6 class="heading-small text-muted mb-4">Driver information</h6>
              <div class="pl-lg-4">
                <div class="row">
                  <div class="col-lg-6 mb-3">
                    <p>Rating</p>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="Rating"
                      v-model="data_driver.rating"
                    />
                  </div>
                </div>
              </div>
              <hr class="my-4" />
              <!-- Address -->
              <h6 class="heading-small text-muted mb-4">Profile</h6>
              <div class="pl-lg-4">
            
                <div class="row">
                  <div class="col-lg-6 mb-3">
                    <p>Name</p>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="Name"
                      v-model="profile.name"
                    />
                  </div>
                  <div class="col-lg-6 mb-3">
                    <p>Phone Number</p>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="Phone Number"
                      v-model="profile.phone_number"
                    />
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-6 mb-3">
                    <p>Gender</p>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="Gender"
                      v-model="profile.gender"
                    />
                  </div>
                </div>
              </div>
              <hr class="my-4" />

              <h6 class="heading-small text-muted mb-4">Vehicle Details</h6>
              <div class="pl-lg-4">
                <div class="row">
                  <div class="col-lg-6 mb-3">
                    <p>Vehicle Type</p>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="Car/Motorcycle"
                      v-model="vehicle_details.transportation_type"
                    />
                  </div>
                  <div class="col-lg-6 mb-3">
                    <p>Plat Number</p>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="Plat Number"
                      v-model="vehicle_details.plat_number"
                    />
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-6 mb-3">
                    <p>Capacity</p>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="Capacity"
                      v-model="vehicle_details.capacity"
                    />
                  </div>
                  <div class="col-lg-6 mb-3">
                    <p>Merk and Type</p>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="Merk and Type"
                      v-model="vehicle_details.merk_and_type"
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
      data_driver: {
        rating:"",
      },
      profile: {
        name:"",
        phone_number:"",
        gender:"",
      },
      vehicle_details: {
        transportation_type:"",
        plat_number:"",
        capacity:"",
        merk_and_type:"",
      },
      status: "",
    };
  },

 mounted() {
    const url = "/cust/get/driver_profile/" + this.$route.params.id;
    http.get(url).then((response) => {
      this.data_driver = response.data[0];
      this.profile = response.data[0].profile;
      this.vehicle_details = response.data[0].vehicle_details;
      this.address = response.data[0].address;
    });
  },
};
</script>
<style></style>
