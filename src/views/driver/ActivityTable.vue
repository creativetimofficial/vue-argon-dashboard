<template>
  <div>
    <base-header type="gradient-success" class="pb-6 pb-8 pt-5 pt-md-4">
    </base-header>

    <div class="container-fluid mt--7">
      <div class="row mt-5">
        <div class="col">
          <driver-activity-table type="dark" title="Riwayat Order"></driver-activity-table>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import DriverActivityTable from "../Tables/DriverActivityTable";
export default {
  name: "tables",
  components: {
    DriverActivityTable,
  },
};
</script>
<style></style>
