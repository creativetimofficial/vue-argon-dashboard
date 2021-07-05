<template>
  <div class="row justify-content-center">
    <div class="col-lg-5 col-md-7">
      <div class="card bg-secondary shadow border-0">
        <div class="card-body px-lg-5 py-lg-5">
          <div class="text-center text-muted mb-4">
            <small>Log In Form</small>
          </div>
          <form role="form">
            <div class="col-lg-14 mt-2">
                    <input
                      type="email"
                      class="form-control"
                      placeholder="Email"
                      v-model="model.email"
                    />
                  </div>
         

            <div class="col-lg-14 mt-2">
                    <input
                      type="password"
                      class="form-control"
                      placeholder="Password"
                      v-model="model.password"
                    />
                  </div>


            <div class="input-group mb-3 mt-2">
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
import http from "../http.js";

export default {
  components: {},
  name: "login",
  data() {
    return {
      model: {
        email: "",
        password: "",
        role: "Customer"
      },
    };
  },
    methods: {
    doLogin: function(){

      if(this.model.role == "Admin"){
        let formData = {
          admin_email: this.model.email,
          admin_password: this.model.password
        };
      
        const jsonData = JSON.stringify(formData);
        const url = "auth/admin";
        http.post(url, jsonData)
          .then((response) => {
            if (response.status == 201) {
              const email = JSON.stringify(response.data.result[0].admin_email);
              const id = JSON.stringify(response.data.result[0]._id);
              localStorage.setItem("admin_email",email);
              localStorage.setItem("admin_id",id);
              this.$router.push('/admin')
            }
          })
          .catch((error) => {
            alert("Login Err \n" + error);
          });

        

      }else if(this.model.role == "Customer"){
        let formData = {
          customer_email: this.model.email,
          customer_password: this.model.password
        };
      
        const jsonData = JSON.stringify(formData);
        const url = "auth/cust";
        http.post(url, jsonData)
          .then((response) => {
            if (response.status == 201) {
              const email = JSON.stringify(response.data.result[0].customer_email);
              const id = JSON.stringify(response.data.result[0]._id);
              localStorage.setItem("customer_email",email);
              localStorage.setItem("customer_id",id);

              this.$router.push('/customer')
            }
            
          })
          .catch((error) => {
            alert("Login Err \n" + error);
          });

      }else{
        let formData = {
          admin_email: this.model.email,
          admin_password: this.model.password
        };
      
        const jsonData = JSON.stringify(formData);
        const url = "auth/driver";
        http.post(url, jsonData)
          .then((response) => {
            if (response.status == 201) {
              const email = JSON.stringify(response.data.result[0].driver_email);
              const id = JSON.stringify(response.data.result[0]._id);
              localStorage.setItem("driver_email",email);
              localStorage.setItem("driver_id",id);

              this.$router.push('/driver')
            }
            
          })
          .catch((error) => {
            alert("Login Err \n" + error);
          });

      }

    }
  }
};
</script>
<style></style>
