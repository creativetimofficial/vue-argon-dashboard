<template>
  <div>
    <base-header type="gradient-success" class="pb-6 pb-8 pt-5 pt-md-4">
    </base-header>

    <div class="container-fluid mt--7">
      <div class="row mt-5">
        <div class="col">
          <activity-table type="dark" title="Daftar Admin"></activity-table>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import ActivityTable from "../Tables/ActivityTable";
export default {
  name: "tables",
  components: {
    ActivityTable,
  },
  mounted(){
    alert("MASUK2");
  }
};
</script>
<style></style>
