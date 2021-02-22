<template>
  <div class="row justify-content-center">
    <div class="col-lg-5 col-md-7">
      <div class="card bg-secondary shadow border-0">
        <div class="card-header bg-transparent pb-5">
          <div class="text-muted text-center mt-2 mb-3">
            <small>Sign up with</small>
          </div>
          <div class="btn-wrapper text-center">
            <a href="#" class="btn btn-neutral btn-icon">
              <span class="btn-inner--icon"
                ><img src="img/icons/common/github.svg"
              /></span>
              <span class="btn-inner--text">Github</span>
            </a>
            <a href="#" class="btn btn-neutral btn-icon">
              <span class="btn-inner--icon"
                ><img src="img/icons/common/google.svg"
              /></span>
              <span class="btn-inner--text">Google</span>
            </a>
          </div>
        </div>
        <div class="card-body px-lg-5 py-lg-5">
          <div class="text-center text-muted mb-4">
            <small>Or sign up with credentials</small>
          </div>
          <form role="form">
            <base-input
              formClasses="input-group-alternative"
              placeholder="Name"
              addon-left-icon="ni ni-hat-3"
              v-model="model.name"
            >
            </base-input>

            <base-input
              formClasses="input-group-alternative"
              placeholder="Email"
              addon-left-icon="ni ni-email-83"
              v-model="model.email"
              focused
            >
            </base-input>

            <base-input
              formClasses="input-group-alternative"
              placeholder="Password"
              type="password"
              addon-left-icon="ni ni-lock-circle-open"
              v-model="model.password"
            >
            </base-input>

            <div class="text-muted font-italic">
              <small
                >password strength:
                <span class="text-success font-weight-700">strong</span></small
              >
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
              <base-button type="primary" class="my-4"
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
export default {
  name: "register",
  data() {
    return {
      model: {
        name: "",
        email: "",
        password: "",
      },
    };
  },
};
</script>
<style></style>
