<template>
  <div>
    <base-header type="gradient-success" class="pb-6 pb-8 pt-5 pt-md-4">
    </base-header>

    <div class="container-fluid mt--7">
      <div class="row mt-5">
        <div class="col">
          <admin-table type="dark" title="Daftar Admin"></admin-table>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import AdminTable from "../Tables/AdminTable";
export default {
  name: "tables",
  components: {
    AdminTable
  },
};
</script>
<style></style>
