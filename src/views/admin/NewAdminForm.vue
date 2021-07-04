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
          <div class="col-lg-7 col-md-10">
            <h1 class="display-2 text-white">New Admin</h1>
            <p class="text-white mt-0 mb-5">
              You can create New Admin Account here. Be wise in managing Admin
              Data.
            </p>
            <!-- <a href="#!" class="btn btn-info">Edit profile</a> -->
          </div>
        </div>
      </div>
    </base-header>

    <div class="container-fluid mt--7">
      <div class="row">
        <div class="col-xl-8 order-xl-1">
          <card shadow type="secondary">
            <template v-slot:header>
              <div class="bg-white border-0">
                <div class="row align-items-center">
                  <div class="col-8">
                    <h3 class="mb-0">Create New Admin Account</h3>
                  </div>
                </div>
              </div>
            </template>

            <form>
              <h6 class="heading-small text-muted mb-4">Admin Account</h6>
              <div class="pl-lg-4">
                <div class="row">
                  <div class="col-lg-6">
                      <label for="verify" class="col-sm-8 col-form-label">
                  Username
                </label>

                    <input
                      type="email"
                      class="form-control"
                      placeholder="jess"
                      v-model="model.username"
                    />
                  </div>
                  <div class="col-lg-6">
                      <label for="verify" class="col-sm-8 col-form-label">
                  Email
                </label>

                    <input
                      type="text"
                      class="form-control"
                      placeholder="jess@gmail.com"
                      v-model="model.email"
                    />
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-6">
                    <label for="verify" class="col-sm-8 col-form-label">
                  Password
                </label>
                    <input
                      type="password"
                      class="form-control"
                      placeholder="******"
                      v-model="model.password"
                    />
                  </div>
                  <div class="col-lg-6">
                    <label for="verify" class="col-sm-8 col-form-label">
                  Password Verification
                </label>
                    <input
                      type="password"
                      class="form-control"
                      placeholder="******"
                      v-model="model.password_verification"
                    />
                  </div>
                </div>
              </div>
              <div class="btn btn-info mt-3" @click="submitAction">Submit</div>
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
        password: "",
        passwordVerification: "",
      },
    };
  },
  methods: {
    submitAction() {
      console.log(this.model.username);
      if (
        this.model.username != "" &&
        this.model.email != "" &&
        this.model.password != "" &&
        this.model.password_verification != ""
      ) {
        console.log("masuk");
        let formData = {
          admin_name: this.model.username, 
          admin_email: this.model.email, 
          admin_password: this.model.password, 
          password_verification: this.model.password_verification
        };

        const jsonData = JSON.stringify(formData);

        const url = "admin/add/newadmin";
    
        http.post(url, jsonData).then((response) => {
          // const res = JSON.stringify(response);
          console.log(response);
          if (response.status == 201) {
            alert("Succesfully add admin");
          } else {
            alert("Failed to add admin");
          }
        });
      }
    },
  },
};
</script>
<style></style>
