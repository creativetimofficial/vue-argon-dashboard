<template>
  <div>
    <base-header
      class="header pb-8 pt-5 pt-lg-8 d-flex align-items-center"
      style="
        min-height: 300px;
        background-image: url(img/theme/profile-cover.jpg);
        background-size: cover;
        background-position: center top;
      "
    >
      <!-- Mask -->
      <span class="mask bg-gradient-success opacity-8"></span>
      <!-- Header container -->
      <div class="container-fluid d-flex align-items-center">
        <div class="row">
          <div class="col-lg-7 col-md-10">
            <h1 class="display-2 text-white">Profile</h1>
          </div>
        </div>
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
                  {{ model.username }}
                </h3>
                <div class="h5 font-weight-300">
                  <i class="ni location_pin mr-2"></i>{{ model.email }}
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
                    <h3 class="mb-0">My account</h3>
                  </div>
                </div>
              </div>
            </template>

            <form>
              <h6 class="heading-small text-muted mb-4">User information</h6>
              <div class="pl-lg-4">
                <div class="row">
                  <div class="col-lg-6 mb-3">
                    <p>Username</p>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="Username"
                      v-model="model.username"
                    />
                  </div>
                  <div class="col-lg-6 mb-3">
                    <p>Email</p>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="Email"
                      v-model="model.email"
                    />
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-6 mb-3">
                    <p>Current Password</p>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="******"
                      v-model="model.curr_password"
                    />
                  </div>
                  <div class="col-lg-6 mb-3">
                    <p>New Password</p>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="******"
                      v-model="model.new_password"
                    />
                  </div>
                </div>
              </div>

              <div class="btn btn-info mt-3" @click="updateAction">
                Edit Profile
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
      model: {
        username: "",
        email: "",
        curr_password: "",
        new_password: "",
      },
    };
  },

  mounted() {
    (this.model.username = localStorage.getItem("admin_name")),
      (this.model.email = localStorage.getItem("admin_email"));
  },

  methods: {
    updateAction() {
      if (
        this.model.username != "" &&
        this.model.email != "" &&
        this.model.curr_password != "" &&
        this.model.new_password != ""
      ) {
        let formData = {
          admin_name: this.model.username,
          admin_email: this.model.email,
          admin_curr_password: this.model.curr_password,
          admin_new_password: this.model.admin_new_password,
        };

        const jsonData = JSON.stringify(formData);

        const url = "admin/update/admin/" + localStorage.getItem("admin_id");

        http
          .post(url, jsonData)
          .then((response) => {
            if (response.status == 201) {
              alert("Succesfully update admin");
              this.post_status = true;
              this.$router.push("/admin/adminList");
            }
          })
          .catch((error) => {
            alert("Failed to add admin \n" + error);
          });
      } else {
        alert("Failed to add admin");
      }
    },
  },
};
</script>
<style></style>
