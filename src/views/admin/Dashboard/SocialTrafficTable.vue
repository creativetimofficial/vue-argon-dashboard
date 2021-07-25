<template>
  <div class="card">
    <div class="card-header border-0">
      <div class="row align-items-center">
        <div class="col">
          <h3 class="mb-0">All Order Traffic</h3>
        </div>
      </div>
    </div>

    <div class="table-responsive">
      <base-table thead-classes="thead-light" :data="tableData">
        <template v-slot:columns>
          <th>Type of Service</th>
          <th>Order Total</th>
        </template>

        <template v-slot:default="row">
          <th scope="row">
            {{ row.item._id }}
          </th>
          <td>
            {{ row.item.totalorder }}
          </td>
        </template>
      </base-table>
    </div>
  </div>
</template>
<script>
import http from "../../../http.js";
export default {
  name: "social-traffic-table",
  data() {
    return {
      tableData: [],
    };
  },
  mounted(){
    const url = "/admin/read/report/pertype";
    http.get(url).then((response) => {
      this.tableData = response.data;
    });
  }
};
</script>
<style></style>
