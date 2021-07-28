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
        :data="list_activity"
      >
        <template v-slot:columns>
          <th>Date and Time</th>
          <th>Type of Service</th>
          <th>Driver ID</th>
          <th>Customer ID</th>
          <th>Actiivty Status</th>
          <th>Price</th>
          <th>Action</th>
          <th></th>
        </template>

        <template v-slot:default="row">
          <th scope="row">
            <div class="media align-items-center">
              <div class="media-body">
                <span class="name mb-0 text-sm">{{ row.item.date }}</span>
              </div>
            </div>
          </th>

          <td>
            <div class="media-body">
              <span class="name mb-0 text-sm">{{
                row.item.type_of_service
              }}</span>
            </div>
          </td>

          <td>
            <div class="media-body">
              <span class="name mb-0 text-sm">{{ row.item.id_driver }}</span>
            </div>
          </td>
          <td>
            <div class="media-body">
              <span class="name mb-0 text-sm">{{ row.item.id_customer }}</span>
            </div>
          </td>

          <td>
            <div class="media-body">
              <span class="name mb-0 text-sm">{{
                row.item.activity_status
              }}</span>
            </div>
          </td>

          <td>
            <div class="media-body">
              <span class="name mb-0 text-sm">{{ row.item.price }}</span>
            </div>
          </td>

          <td class="text-left">
            <div class="btn btn-danger" @click="detailAction(row.item._id)">
              Detail Activity
            </div>
          </td>
        </template>
      </base-table>
    </div>
  </div>
</template>

<script>
import http from "../../http.js";

export default {
  name: "driver-new-order-table",
  props: {
    type: {
      type: String,
    },
    title: String,
  },
  data() {
    return {
      list_activity: [],
      date_search: "",
      service_type: "Type of Service" 
    };
  },
  mounted() {
    const url = "/driver/get/neworder/" + localStorage.getItem("driver_id");
    http.get(url).then((response) => {
      this.list_activity = response.data;
    });
  },
  methods: {
    allActivityAction() {
      const url =  "/driver/get/neworder/" + localStorage.getItem("driver_id");
      http.get(url).then((response) => {
        this.list_activity = response.data;
      });
      this.service_type = "All Activity";
    },
    detailAction(_id) {
      this.$router.push({
        name: "Driver Activity Detail",
        params: { id: _id },
      });
    }
  },
};
</script>
<style></style>
