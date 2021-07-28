<template>
  <div class="row justify-content-center">
    <div class="col-lg-5 col-md-7">
      <div class="card bg-secondary shadow border-0">
        <div class="card-body px-lg-5 py-lg-5">
          <div class="text-center text-muted mb-4">
            <small>Sign Up Form</small>
          </div>
          <form role="form">
            <input
              type="text"
              class="form-control mb-2"
              placeholder="Name"
              v-model="model.name"
            />

            <input
              type="email"
              class="form-control mb-2"
              placeholder="Email"
              v-model="model.email"
            />

            <input
              type="password"
              class="form-control mb-2"
              placeholder="Password"
              v-model="model.password"
            />

            <input
              type="email"
              class="form-control mb-2"
              placeholder="Password Verification"
              v-model="model.password_verification"
            />

            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <button class="btn btn-outline-primary" type="button">
                  Register as
                </button>
              </div>
              <select
                class="custom-select"
                id="inputGroupSelect03"
                v-model="role"
              >
                <option value="customer">Customer</option>
                <option value="driver">Driver</option>
              </select>
            </div>

            <div class="row my-4">
              <div class="col-12">
                <base-checkbox class="custom-control-alternative">
                  <span class="text-muted"
                    >I agree with the <a href="#!">Privacy Policy</a></span
                  >
                </base-checkbox>
              </div>
            </div>
            <div class="text-center">
              <base-button type="primary" class="my-4" v-on:click="submitAction"
                >Create account</base-button
              >
            </div>
          </form>
        </div>
      </div>
      <div class="row mt-3">
        <div class="col-6">
          <a href="#" class="text-light">
            <small>Forgot password?</small>
          </a>
        </div>
        <div class="col-6 text-right">
          <router-link to="/login" class="text-light">
            <small>Login into your account</small>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import http from "../http.js";

export default {
  name: "register",
  data() {
    return {
      model: {
        name: "",
        email: "",
        password: "",
        password_verification: "",
      },
      role: "customer"
    };
  },
  methods: {
    submitAction() {
      if (this.role == "driver") {
        if (
          this.model.username != "" &&
          this.model.email != "" &&
          this.model.password != "" &&
          this.model.password_verification != ""
        ) {
          let formData = {
            profile: { name: this.model.name },
            driver_email: this.model.email,
            driver_password: this.model.password,
            password_verification: this.model.password_verification,
          };

          const jsonData = JSON.stringify(formData);

          const url = "driver/signup";
          // alert(jsonData);
          http
            .post(url, jsonData)
            .then((response) => {
              if (response.status == 201) {
                alert("Succesfully add driver");
                this.$router.push("/login");
              }
            })
            .catch((error) => {
              alert("Failed to add driver \n" + error);
            });
        } else {
          alert("Failed to add driver");
        }
      } else {
        if (
          this.model.username != "" &&
          this.model.email != "" &&
          this.model.password != "" &&
          this.model.password_verification != ""
        ) {
          let formData = {
            profile: { name: this.model.name },
            customer_email: this.model.email,
            customer_password: this.model.password,
            password_verification: this.model.password_verification,
          };

          const jsonData = JSON.stringify(formData);

          const url = "cust/signup";
          // alert(jsonData);
          http
            .post(url, jsonData)
            .then((response) => {
              if (response.status == 201) {
                alert("Succesfully create account");
                this.$router.push("/login");
              }
            })
            .catch((error) => {
              alert("Failed to create account \n" + error);
            });
        } else {
          alert("Failed to create account");
        }
      }
    },
  },
};
</script>
<style></style>
