<template>
  <div>
    <base-header type="gradient-success" class="pb-6 pb-8 pt-5 pt-md-4">
    </base-header>

    <div class="container-fluid mt--7">
      <div class="row mt-5">
        <div class="col">
          <customer-activity-table type="dark" title="Activity History"></customer-activity-table>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import CustomerActivityTable from "../Tables/CustomerActivityTable";
export default {
  name: "tables",
  components: {
    CustomerActivityTable,
  },
};
</script>
<style></style>
