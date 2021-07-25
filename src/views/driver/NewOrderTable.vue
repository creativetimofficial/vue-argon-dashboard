<template>
  <div>
    <base-header type="gradient-success" class="pb-6 pb-8 pt-5 pt-md-4">
    </base-header>

    <div class="container-fluid mt--7">
      <div class="row mt-5">
        <div class="col">
          <driver-new-order-table
            type="dark"
            title="Order Baru"
          ></driver-new-order-table>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import DriverNewOrderTable from "../Tables/DriverNewOrderTable";
export default {
  name: "tables",
  components: {
    DriverNewOrderTable,
  },
};
</script>
<style></style>
