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
                  {{ profile.name }}
                </h3>
                <div class="h5 font-weight-300">
                  <i class="ni location_pin mr-2"></i
                  >{{ data_cust.customer_email }}
                </div>
                <div class="h5 mt-4">
                  <i class="ni business_briefcase-24 mr-2"></i
                  >{{ profile.gender }}
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
                    <h3 class="mb-0">Customer's account</h3>
                  </div>
                </div>
              </div>
            </template>

            <form>
              <h6 class="heading-small text-muted mb-4">
                Customer information
              </h6>

              <div class="pl-lg-4">
                <div class="row">
                  <div class="col-lg-6 mb-3">
                    <p>Email</p>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="customer@gmail.com"
                      v-model="data_cust.customer_email"
                    />
                  </div>
                  <div class="col-lg-6 mb-3">
                    <p>Sign Up Date</p>
                    <p>{{ data_cust.signUp_date }}</p>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-6 mb-3">
                    <p>password</p>
                    <input
                      type="password"
                      class="form-control"
                      placeholder="dd-mm-yy"
                      v-model="data_cust.customer_password"
                    />
                  </div>
                  <div class="col-lg-6 mb-3">
                    <!-- <div
                      v-show="data_driver.blokir == true"
                      class="btn btn-danger">
                      Your Account Blocked By Admin
                    </div> -->
                  </div>
                </div>
              </div>
              <hr class="my-4" />

              <h6 class="heading-small text-muted mb-4">Customer Profile</h6>
              <div class="pl-lg-4">
                <div class="row">
                  <div class="col-lg-6 mb-3">
                    <p>Name</p>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="customer's name"
                      v-model="profile.name"
                    />
                  </div>
                  <div class="col-lg-6 mb-3">
                    <p>Phone Number</p>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="08XXXXXXXXX"
                      v-model="profile.phone_number"
                    />
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-6 mb-3">
                    <p>Gender</p>
                    <label for="verify" class="col-lg-6 mb-3">
                      <input
                        type="checkbox"
                        v-model="profile.male"
                        @click="checkBoxSelect('male')"
                      />
                      Male
                    </label>
                    <label for="verify" class="col-lg-6 mb-3">
                      <input
                        type="checkbox"
                        v-model="profile.female"
                        @click="checkBoxSelect('female')"
                      />
                      Female
                    </label>
                  </div>
                  <div class="col-lg-6 mb-3">
                    <p>Birth Date</p>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="yyyy-mm-dd"
                      v-model="profile.birth_of_date"
                    />
                  </div>
                  <div class="btn btn-info mt-3" @click="updateAction">
                    Edit Profile
                  </div>
                </div>
              </div>
              <hr class="my-4" />
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
      data_cust: {
        customer_email: "",
        customer_password: "",
        signUp_date: "",
      },
      profile: {
        name: "",
        phone_number: "",
        male: false,
        female: false,
        birth_of_date: "",
        gender: "",
      },
      blockedText: "Block",
    };
  },
  mounted() {
    
  },
  methods: {
    checkBoxSelect(data) {
      this.profile.gender = data;
      alert(this.profile.gender)
      //  if (data === 'male') {
      //    this.profile.female = false
      //  } else if (data === 'female') {
      //    this.profile.male = false
      //  }
    },

    updateAction() {
     alert(this.profile.gender)
      // if(this.profile.male == true){
      //    this.profile.gender = "pria"
      // }else if (this.profile.female == true){
      //   this.profile.gender = "wanita"
      // }else{
      //   this.profile.gender = ""
      // }

      if (
        this.data_cust.customer_email != "" &&
        this.data_cust.customer_password != "" &&
        this.profile.name != "" &&
        this.profile.phone_number != "" &&
        this.profile.birth_of_date != "" &&
        this.data_cust.signUp_date != ""
      ) {
        let formData = {
          customer_email: this.data_cust.customer_email,
          customer_password: this.data_cust.customer_password,
          profile: {
            name: this.profile.name,
            phone_number: this.profile.phone_number,
            birth_of_date: this.profile.birth_of_date,
            gender: this.profile.gender,
          },
          signUp_date: this.data_cust.signUp_date,
        };
        const jsonData = JSON.stringify(formData);

        const url =
          "/cust/update/account/" + localStorage.getItem("customer_id");

        http
          .post(url, jsonData)
          .then((response) => {
            if (response.status == 201) {
              alert("Succesfully update profil");
              this.post_status = true;
            }
          })
          .catch((error) => {
            alert("Failed to update \n" + error);
          });
      } else {
        alert("Failed to update");
      }
    },
  },
};
</script>
<style></style>
