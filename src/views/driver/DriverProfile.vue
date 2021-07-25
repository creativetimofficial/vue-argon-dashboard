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

      <div v-show="data_driver.submitted == false">
        <base-alert type="danger" dismissible>
          <span class="alert-inner--icon"><i class="ni ni-like-2"></i></span>
          <span class="alert-inner--text"
            ><strong>   Danger!</strong>  Submit data hanya dapat dilakukan satu
            kali !</span
          >
          <button
            type="button"
            class="close"
            data-dismiss="alert"
            aria-label="Close"
          >
            <span aria-hidden="true">&times;</span>
          </button>
        </base-alert>
      </div>
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
                  {{ profile.name }}
                </h3>
                <div class="h5 font-weight-300">
                  <i class="ni location_pin mr-2"></i
                  >{{ vehicle_details.transportation_type }}
                </div>
                <div class="h5 mt-4">
                  <i class="ni business_briefcase-24 mr-2"></i
                  >{{ data_driver.driver_email }}
                </div>
                <div>
                  <i class="ni education_hat mr-2"></i>{{ address.city }}
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
                    <p>Email</p>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="Email"
                      v-model="data_driver.driver_email"
                    />
                  </div>
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
                <div class="row">
                  <div class="col-lg-6 mb-3">
                    <p>Active/Non Active Account</p>
                    <div
                      :class="
                        data_driver.active_status == true
                          ? 'btn btn-danger'
                          : 'btn btn-success'
                      "
                      @click="updActiveStatusAction()"
                    >
                      {{ activeText }}
                    </div>
                  </div>
                  <div class="col-lg-6 mb-3">
                    <div
                      v-show="data_driver.blokir == true"
                      class="btn btn-danger"
                    >
                      Your Account Blocked By Admin
                    </div>
                  </div>
                </div>
              </div>
              <hr class="my-4" />
              <!-- Address -->
              <h6 class="heading-small text-muted mb-4">Profile</h6>
              <div class="pl-lg-4">
                <div class="row">
                  <div class="col-lg-6 mb-3">
                    <p>NIK</p>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="NIK"
                      v-model="profile.nik"
                    />
                  </div>
                  <div class="col-lg-6 mb-3">
                    <p>Nomor SIM</p>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="SIM NO"
                      v-model="profile.sim_no"
                    />
                  </div>
                </div>
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
                  <div class="col-lg-6 mb-3">
                    <p>Birth Date</p>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="yyyy/mm/dd"
                      v-model="profile.birth_of_date"
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

                <div class="row">
                  <div class="col-lg-6 mb-3">
                    <p>STNK Registration Number</p>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="STNK No Regis"
                      v-model="vehicle_details.stnk_no_registration"
                    />
                  </div>
                </div>
              </div>
              <hr class="my-4" />

              <h6 class="heading-small text-muted mb-4">Address</h6>
              <div class="pl-lg-4">
                <div class="row">
                  <div class="col-lg-6 mb-3">
                    <p>Province</p>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="Province"
                      v-model="address.province"
                    />
                  </div>
                  <div class="col-lg-6 mb-3">
                    <p>City</p>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="City"
                      v-model="address.city"
                    />
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-6 mb-3">
                    <p>Sub District</p>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="Sub District"
                      v-model="address.sub_district"
                    />
                  </div>
                  <div class="col-lg-6 mb-3">
                    <p>Zip Code</p>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="Zip Code"
                      v-model="address.zip_code"
                    />
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-6 mb-3">
                    <p>Street</p>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="Steet"
                      v-model="address.street"
                    />
                  </div>
                </div>
              </div>
              <hr class="my-4" />

              <h6 class="heading-small text-muted mb-4">Document</h6>
              <div class="pl-lg-4">
                <div class="row">
                  <div class="col-lg-6 mb-3">
                    <p>SKCK</p>
                    <!-- <button id="fileInputButton" onclick="document.getElementById('fileInput').click()">Open File</button> -->
                    <div class="btn btn-primary mb-2" @click="openFile(item)">
                      Open File
                    </div>
                    <input
                      type="file"
                      class="form-control-file ml-1"
                      @change="change"
                    />
                  </div>
                  <div class="col-lg-6 mb-3">
                    <p>KTP</p>
                    <!-- <button id="fileInputButton" onclick="document.getElementById('fileInput').click()">Open File</button> -->
                    <div class="btn btn-primary mb-2" @click="openFile(item)">
                      Open File
                    </div>
                    <input
                      type="file"
                      class="form-control-file ml-1"
                      @change="change"
                    />
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-6 mb-3">
                    <p>SIM</p>
                    <!-- <button id="fileInputButton" onclick="document.getElementById('fileInput').click()">Open File</button> -->
                    <div class="btn btn-primary mb-2" @click="openFile(item)">
                      Open File
                    </div>
                    <input
                      type="file"
                      class="form-control-file ml-1"
                      @change="change"
                    />
                  </div>
                  <div class="col-lg-6 mb-3">
                    <p>STNK</p>
                    <!-- <button id="fileInputButton" onclick="document.getElementById('fileInput').click()">Open File</button> -->
                    <div class="btn btn-primary mb-2" @click="openFile(item)">
                      Open File
                    </div>
                    <input
                      type="file"
                      class="form-control-file ml-1"
                      @change="change"
                    />
                  </div>
                </div>
              </div>
              <hr class="my-4" />

              <div
                class="btn btn-info mt-3"
                v-show="data_driver.submitted == false"
                @click="updateProfileAction()"
              >
                Submit
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
      data_driver: {},
      profile: {},
      vehicle_details: {},
      address: {},
      documents: {},
      model: { username: "aaaa" },
      form: { sertifikat: null },
      activeText: "",
      blockedText: "",
    };
  },
  mounted() {
    const url = "/admin/read/driver/" + localStorage.getItem("driver_id");
    http.get(url).then((response) => {
      this.data_driver = response.data[0];
      this.profile = response.data[0].profile;
      this.vehicle_details = response.data[0].vehicle_details;
      this.address = response.data[0].address;
      this.documents = response.data[0].documents;

      if (this.data_driver.active_status == true) {
        this.activeText = "Non Activate";
      } else {
        this.activeText = "Activate";
      }
    });
  },

  methods: {
    updActiveStatusAction() {
      let jsonData = {
        active_status: true,
      };

      if (this.data_driver.active_status == true) {
        jsonData = {
          active_status: false,
        };
      }

      const url = "/driver/update/active/" + localStorage.getItem("driver_id");

      http
        .post(url, jsonData)
        .then((response) => {
          if (response.status == 201) {
            alert("Succesfully edit Active Status");
            this.data_driver.active_status = !this.data_driver.active_status;
            if (this.data_driver.active_status == true) {
              this.activeText = "Non Activate";
            } else {
              this.activeText = "Activate";
            }
          }
        })
        .catch((error) => {
          alert("Failed to change active status\n" + error);
        });
    },
    updateProfileAction() {
      let new_profile = {
        nik: this.profile.nik,
        sim_no: this.profile.sim_no,
        name: this.profile.name,
        profpict: "",
        phone_number: this.profile.phone_number,
        gender: this.profile.gender,
        birth_of_date: new Date(this.profile.birth_of_date),
      };

      let new_vehicle_details = {
        transportation_type: this.vehicle_details.transportation_type,
        plat_number: this.vehicle_details.plat_number,
        capacity: this.vehicle_details.capacity,
        merk_and_type: this.vehicle_details.merk_and_type,
        stnk_no_registration: this.vehicle_details.stnk_no_registration,
      };

      let newAddress = {
        province: this.address.province,
        city: this.address.city,
        sub_district: this.address.sub_district,
        zip_code: this.address.zip_code,
        street: this.address.street,
      };

      let newDocuments = {
        skck: "",
        ktp: "",
        sim: "",
        stnk: "",
      };

      let jsonData = {
        driver_email: this.data_driver.driver_email,
        profile: new_profile,
        vehicle_details: new_vehicle_details,
        address: newAddress,
        documents: newDocuments,
        rating: null,
        submitted: true,
      };

      const url = "/driver/update/profile/" + localStorage.getItem("driver_id");
      http
        .post(url, jsonData)
        .then((response) => {
          if (response.status == 201) {
            alert("Succesfully edit profile");
          }
        })
        .catch((error) => {
          alert("Failed to change active status\n" + error);
        });
    },
  },
};
</script>
<style></style>
