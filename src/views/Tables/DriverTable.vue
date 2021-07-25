<template>
  <div class="card shadow" :class="type === 'dark' ? 'bg-default' : ''">
    <div
      class="card-header border-0"
      :class="type === 'dark' ? 'bg-transparent' : ''"
    >
      <!-- Card stats -->
      <div class="row">
        <div class="col-xl-3 col-lg-6 mb-5">
          <stats-card
            title="Total Driver (Verificated)"
            type="gradient-info"
            :sub-title="number_of_driver"
            icon="ni ni-chart-bar-32"
            class="mb-4 mb-xl-0"
          >
            <!-- <template v-slot:footer>
              <span class="text-success mr-2"
                ><i class="fa fa-arrow-up"></i> 54.8%</span
              >
            </template> -->
          </stats-card>
        </div>
        <div class="col-xl-3 col-lg-6">
          <stats-card
            title="Car Driver"
            type="gradient-red"
            :sub-title="number_of_car_driver"
            icon="ni ni-active-40"
            class="mb-4 mb-xl-0"
          >
            <template v-slot:footer></template>
          </stats-card>
        </div>
        <div class="col-xl-3 col-lg-6">
          <stats-card
            title="Motorcycle Driver"
            type="gradient-orange"
            :sub-title="number_of_motor_driver"
            icon="ni ni-chart-pie-35"
            class="mb-4 mb-xl-0"
          >
            <template v-slot:footer></template>
          </stats-card>
        </div>
        <div class="col-xl-3 col-lg-6">
          <stats-card
            title="Active Driver"
            type="gradient-green"
            :sub-title="number_of_active_driver"
            icon="ni ni-money-coins"
            class="mb-4 mb-xl-0"
          >
            <template v-slot:footer></template>
          </stats-card>
        </div>
      </div>

      <!-- ------------------------------------------------------------------------------------------------------------------------------------------------ -->

      <div class="row align-items-center">
        <div class="col">
          <h3 class="mb-0" :class="type === 'dark' ? 'text-white' : ''">
            Driver List
          </h3>
        </div>

        <!-- BUAT SEARCH  -->
        <div class="form-group mb-0 mr-6">
          <div class="row">
            <div class="mr-1">
              <input
                type="text"
                class="form-control"
                placeholder="Search by NIK"
                v-model="nik_search"
              />
            </div>

            <div class="btn btn-info" @click="searchAction(nik_search)">
              Search
            </div>
          </div>

          <div class="btn btn-info mt-2" @click="seeAll()">See All</div>
        </div>
      </div>
    </div>

    <div class="table-responsive">
      <base-table
        class="table align-items-center table-flush"
        :class="type === 'dark' ? 'table-dark' : ''"
        :thead-classes="type === 'dark' ? 'thead-dark' : 'thead-light'"
        tbody-classes="list"
        :data="list_driver"
      >
        <template v-slot:columns>
          <th>Name</th>
          <th>Email</th>
          <th>NIK</th>
          <th>Vehicle Type</th>
          <th>Active Status</th>
          <th>Rating</th>
          <th>Action</th>
        </template>

        <template v-slot:default="row">
          <th scope="row">
            <div class="media align-items-center">
              <div class="media-body">
                <span class="name mb-0 text-sm">{{
                  row.item.profile.name
                }}</span>
              </div>
            </div>
          </th>

          <td>
            <div class="media-body">
              <span class="name mb-0 text-sm">{{ row.item.driver_email }}</span>
            </div>
          </td>

          <td>
            <div class="media-body">
              <span class="name mb-0 text-sm">{{ row.item.profile.NIK }}</span>
            </div>
          </td>

          <td>
            <div class="media-body">
              <span class="name mb-0 text-sm">{{
                row.item.vehicle_details.transportation_type
              }}</span>
            </div>
          </td>

          <td class="text-left">
            <div
              v-show="row.item.verification_status == true"
              :class="
                row.item.active_status === true
                  ? 'btn btn-success'
                  : 'btn btn-danger'
              "
            >
              {{ row.item.active_status }}
            </div>
          </td>

          <td>
            <div class="media-body">
              <span class="name mb-0 text-sm">{{ row.item.rating }}</span>
            </div>
          </td>

          <td class="text-left">
            <div class="btn btn-danger" @click="deleteAction(row.item._id)">
              Delete
            </div>
            <div class="btn btn-primary" @click="editAction(row.item._id)">
              Edit
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
  name: "driver-table",
  props: {
    type: {
      type: String,
    },
    title: String,
  },
  data() {
    return {
      list_driver: [],
      nik_search: "",
      number_of_driver: 0,
      number_of_car_driver: 0,
      number_of_motor_driver: 0,
      number_of_active_driver: 0,
    };
  },
  mounted() {
    const url = "admin/read/alldriver";
    http.get(url).then((response) => {
      this.list_driver = response.data;
      for (let i = 0; i < response.data.length; i++) {
        if (response.data[i].verification_status == true) {
          this.number_of_driver++;
          if (response.data[i].vehicle_details.transportation_type == "Car") {
            this.number_of_car_driver++;
          } else {
            this.number_of_motor_driver++;
          }

          if (response.data[i].active_status == true) {
            this.number_of_active_driver++;
          }
        }
      }
    });
  },
  methods: {
    deleteAction(i) {
      const url = "admin/delete/driver/" + i;

      http
        .delete(url)
        .then((response) => {
          if (response.status == 201) {
            alert("Succesfully delete driver");
            this.$router.push("/admin/driverList");
          }
        })
        .catch((error) => {
          alert("Failed to delete driver \n" + error);
        });
    },
    searchAction(nik) {
      const url = "/admin/search/driver/" + nik;
      http.get(url).then((response) => {
        this.list_driver = response.data;
      });
    },
    seeAll() {
      const url = "admin/read/alldriver";
      http.get(url).then((response) => {
        this.list_driver = response.data;
      });
    },

    editAction(_id) {
      this.$router.push({
        name: "Driver Data",
        params: { id: _id },
      });
    },
  },
};
</script>
<style></style>
