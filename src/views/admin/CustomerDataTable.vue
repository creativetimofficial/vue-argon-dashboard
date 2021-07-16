<template>
  <div>
    <base-header type="gradient-success" class="pb-6 pb-8 pt-5 pt-md-4">
    </base-header>

    <div class="container-fluid mt--7">
      <div class="row mt-5">
        <div class="col">
          <customer-table type="dark" title="Daftar Customer"></customer-table>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import CustomerTable from "../Tables/CustomerTable";
export default {
  name: "tables",
  components: {
    CustomerTable
  },
};
</script>
<style></style>
