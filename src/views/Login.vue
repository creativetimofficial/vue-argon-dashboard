<template>
  <div class="row justify-content-center">
    <div class="col-lg-5 col-md-7">
      <div class="card bg-secondary shadow border-0">
        <div class="card-body px-lg-5 py-lg-5">
          <div class="text-center text-muted mb-4">
            <small>Log In Form</small>
          </div>
          <form role="form">
            <base-input
              formClasses="input-group-alternative mb-3"
              placeholder="Email"
              addon-left-icon="ni ni-email-83"
              v-model= "model.email"
            >
            </base-input>

            <base-input
              formClasses="input-group-alternative mb-3"
              placeholder="Password"
              type="password"
              addon-left-icon="ni ni-lock-circle-open"
              v-model= "model.password"
            >
            </base-input>
            <base-input
              formClasses="input-group-alternative mb-3"
              placeholder="Password Verification"
              type="password"
              addon-left-icon="ni ni-lock-circle-open"
              v-model= "model.passwordVerification"
            >
            </base-input>


            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <button class="btn btn-outline-primary" type="button">
                  Login as
                </button>
              </div>
              <select class="custom-select" id="inputGroupSelect03"  v-model= "model.role">
                <option value="Customer">Customer</option>
                <option value="Driver">Driver</option>
                <option value="Admin">Admin</option>
              </select>
            </div>

            <base-checkbox class="custom-control-alternative">
              <span class="text-muted">Remember me</span>
            </base-checkbox>
            <div class="text-center">
              <base-button type="primary" class="my-4" v-on:click= "doLogin" >Log in</base-button>
            </div>
          </form>
        </div>
      </div>
      <div class="row mt-3">
        <div class="col-6">
          <a href="#" class="text-light"><small>Forgot password?</small></a>
        </div>
        <div class="col-6 text-right">
          <router-link to="/register" class="text-light"
            ><small>Create new account</small></router-link
          >
        </div>
      </div>
    </div>
  </div>
</template>
<script>

// import router from "../routes/routes";

export default {
  components: {},
  name: "login",
  data() {
    return {
      model: {
        email: "aaa",
        password: "",
        role: "Customer"
      },
    };
  },
    methods: {
    doLogin: function() {
      if(this.model.role == "Admin"){
        this.$router.push('/admin')
      }else if(this.model.role == "Customer"){
        this.$router.push('/cust')
      }else{
        this.$router.push('/driver')
      }
    }
  }
};
</script>
<style></style>
