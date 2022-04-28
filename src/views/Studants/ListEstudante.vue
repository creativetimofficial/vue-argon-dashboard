<template>
  <div>
    <base-header
      class="header pb-8 pt-5 pt-lg-8 d-flex align-items-center"
      style="
        min-height: 60px;
        
        background-size: cover;
        background-position: center top;
      "
    >
      <!-- Mask -->
      <span class="mask bg-gradient-success opacity-8"></span>
      <!-- Header container -->
    </base-header>

    <div class="container-fluid mt--7">
      <div class="row justify-content-center">
        
         <div class="row">
        <div class="col">
          <projects-table title="Lista de Estudantes"></projects-table>
        </div>
      </div>
      </div>
    </div>
  </div>
</template>
<script>
import ProjectsTable from "../Tables/ProjectsTable";
export default {
  name: "estudantes",
  components: {
    ProjectsTable,
  },
  data() {
    return {
     
    };
  },
};
</script>
<style></style>
