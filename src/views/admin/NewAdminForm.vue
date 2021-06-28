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
              You can create New Admin Account here. Be wise in managing Admin Data.
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
                    <base-input
                      alternative=""
                      label="Username"
                      placeholder="Username"
                      input-classes="form-control-alternative"
                      v-model="model.username"
                    />
                  </div>
                  <div class="col-lg-6">
                    <base-input
                      alternative=""
                      label="Email address"
                      placeholder="jesse@example.com"
                      input-classes="form-control-alternative"
                      v-model="model.email"
                    />
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-6">
                    <base-input
                      alternative=""
                      label="Password"
                      placeholder="******"
                      input-classes="form-control-alternative"
                      type="password"
                      v-model="model.password"
                    />
                  </div>
                  <div class="col-lg-6">
                    <base-input
                      alternative=""
                      label="Password Verification"
                      placeholder="******"
                      input-classes="form-control-alternative"
                      type="password"
                      v-model="model.passwordVerification"
                    />
                  </div>
                </div>
              </div>
              <div class="btn btn-info" @click= "submitAction" >Submit</div>
            </form>
          </card>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "user-profile",
  data() {
    return {
      model: {
        username: "",
        email: "",
        password: "",
        passwordVerification: ""
      },
    };
  },
  methods: {
    submitAction(evt) {
      
      console.log("delete" + evt);
    }
   }
};
</script>
<style></style>
