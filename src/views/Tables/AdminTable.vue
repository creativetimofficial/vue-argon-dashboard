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
      </div>
    </div>

    <div class="table-responsive">
      <base-table
        class="table align-items-center table-flush"
        :class="type === 'dark' ? 'table-dark' : ''"
        :thead-classes="type === 'dark' ? 'thead-dark' : 'thead-light'"
        tbody-classes="list"
        :data="list_admin"
      >
        <template v-slot:columns>
          <th>Username</th>
          <th>Email</th>
          <th>Created At</th>
          <th>Action</th>
          <th></th>
        </template>

        <template v-slot:default="row">
          <th scope="row">
            <div class="media align-items-center">

              <div class="media-body">
                <span class="name mb-0 text-sm">{{ row.item.admin_name }}</span>
              </div>
            </div>
          </th>

          <td>
           
             <div class="media-body">
                <span class="name mb-0 text-sm">{{ row.item.admin_email }}</span>
              </div>
            
          </td>

         
          <td>
           
             <div class="media-body">
                <span class="name mb-0 text-sm">{{ row.item.created_at }}</span>
              </div>
            
          </td>

          <td class="text-left">
              <div class="btn btn-danger" @click= "deleteAction(row.item._id)" >Delete</div>
          </td>
        </template>
      </base-table>
    </div>
  </div>
<div class="pb-6 pb-8 pt-5 pt-md-4">
  <a href="/?#/admin/newAdmin" class="btn btn-info">Create New Admin Account</a>
</div>
</template>

<script>
import http from '../../http.js';

export default {
  name: "admin-table",
  props: {
    type: {
      type: String,
    },
    title: String
  },
  data() {
    return {
      list_admin:[]
    };
  },
  mounted() {
    this.loadAdmin();
  },
   methods: {
    deleteAction(i) {
        const url = "admin/delete/admin/"+i;

        http.delete(url).then((response) => {
          if (response.status == 201) {
            alert("Succesfully delete admin"); 
          }
        })
        .catch((error) => {
            alert("Failed to delete admin \n" + error);
          });;
      this.loadAdmin();
    },
    loadAdmin(){
      const url = "admin/read/alladmin";
    http.get(url).then(response => {
      this.list_admin = response.data;
    });
    }
   }

};
</script>
<style></style>
