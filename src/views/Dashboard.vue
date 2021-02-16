<template>
  <div>
    <base-header type="gradient-success" class="pb-6 pb-8 pt-5 pt-md-8">
      <div class="row">
        das
        <div class="col-xl-3 col-lg-6">
          <stats-card title="Total traffic"
                      type="gradient-red"
                      sub-title="350,897"
                      icon="ni ni-active-40"
                      class="mb-4 mb-xl-0"
          >
              <template v-slot:footer>
                  <span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 3.48%</span>
                  <span class="text-nowrap">Since last month</span>
              </template>
          </stats-card>
        </div>
      </div>
    </base-header>
  </div>
</template>
<script>
</script>
<style></style>
