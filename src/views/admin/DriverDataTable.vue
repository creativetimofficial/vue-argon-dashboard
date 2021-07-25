<template>
  <div>
    <base-header type="gradient-success" class="pb-6 pb-8 pt-5 pt-md-8">
      
    </base-header>

    <div class="container-fluid mt--7">
      <div class="row mt-5">
        <div class="col">
          <driver-table type="dark" title="List Driver"></driver-table>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
// import http from '../../http.js';
import DriverTable from "../Tables/DriverTable";
export default {
  name: "tables",
  components: {
    DriverTable
  },
};
</script>
<style></style>
