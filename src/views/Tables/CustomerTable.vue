<template>
  <div class="card shadow" :class="type === 'dark' ? 'bg-default' : ''">
    <div
      class="card-header border-0"
      :class="type === 'dark' ? 'bg-transparent' : ''"
    >
      <div class="row align-items-center">
        <div class="col">
          <h3 class="mb-0" :class="type === 'dark' ? 'text-white' : ''">
            {{ title }}
          </h3>
        </div>
       <!-- BUAT SEARCH  -->
           <div class="form-group mb-0 mr-6">
             <div class="row">
               <div class="mr-1">
                 <input
                type="text"
                class="form-control"
                placeholder="Search by Name"
                v-model="name_search"
              />
               </div>
               <div class="btn btn-info" @click= "searchAction(name_search)" >Search</div>
             </div>

              <div class="btn btn-info mt-2" @click= "seeAll()" >See All</div>
          

        </div>
      </div>
    </div>

    <div class="table-responsive">
      <base-table
        class="table align-items-center table-flush"
        :class="type === 'dark' ? 'table-dark' : ''"
        :thead-classes="type === 'dark' ? 'thead-dark' : 'thead-light'"
        tbody-classes="list"
        :data="list_cust"
      >
        <template v-slot:columns>
       
          <th>Email</th>
          <th>Name</th>
          <th>Phone Number</th>
          <th>Sign Up Date</th>
          <th>Action</th>
          <th></th>
        </template>

        <template v-slot:default="row">

          <th scope="row">
            <div class="media align-items-center">
              <a href="#" class="avatar rounded-circle mr-3">
                <img alt="Image placeholder" :src="row.item.img" />
              </a>
              <div class="media-body">
                <span class="name mb-0 text-sm">{{ row.item.customer_email }}</span>
              </div>
            </div>
          </th>

         
          <td>
           
             <div class="media-body">
                <span class="name mb-0 text-sm">{{ row.item.profile.name }}</span>
              </div>
            
          </td>

          <td>
           
             <div class="media-body">
                <span class="name mb-0 text-sm">{{ row.item.profile.phone_number }}</span>
              </div>
            
          </td>

          <td>
           
             <div class="media-body">
                <span class="name mb-0 text-sm">{{ row.item.signUp_date }}</span>
              </div>
            
          </td>

          <td class="text-left">
              <!-- <div class="">
                <a href="/?#/admin/customerDetail" class="btn btn-primary">Edit</a>
              </div> -->
              <div class="btn btn-danger" @click= "deleteAction(row.item._id)" >Delete</div>
              <div class="btn btn-primary" @click= "editAction(row.item._id)" >See</div>
          </td>

        </template>
      </base-table>
    </div>
  </div>

</template>

<script>
import http from '../../http.js';

export default {
  name: "customer-table",
  props: {
    type: {
      type: String,
    },
    title: String
  },
  data() {
    return {
      list_cust:[],
      name_search: ""
    };
  },
  mounted() {
    const url = "admin/read/allcust";
    http.get(url).then(response => {
      this.list_cust = response.data;
    });
  },
   methods: {
     deleteAction(i) {
        const url = "admin/delete/customer/"+i;

        http.delete(url).then((response) => {
          if (response.status == 201) {
            alert("Succesfully delete customer"); 
            this.$router.push('/admin/customerList');
          }
        })
        .catch((error) => {
            alert("Failed to delete customer \n" + error);
          });;
    },
    editAction(_id) {
        this.$router.push({
          name: 'Customer Profile',
          params: { id: _id}
        });
    },
    searchAction(name) {
        const url = "/admin/search/cust/"+name;
          http.get(url).then(response => {
            this.list_cust = response.data;
          });
    },
    seeAll() {
    const url = "admin/read/allcust";
    http.get(url).then(response => {
      this.list_cust = response.data;
    });
  }
    
   }

};
</script>
<style></style>
