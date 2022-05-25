<template>
  <div class="card bg-gradient-dark">
    <div class="card-body">
      <div class="mb-2">
        <sup class="text-white">$</sup>
        <span class="h2 text-white">3,300</span>
        <div class="text-white opacity-8 mt-2 text-sm">Your current balance</div>
        <div>
          <span class="text-success font-weight-600">+ 15%</span>
          <span class="text-white opacity-8">($250)</span>
        </div>
      </div>
      <button class="btn btn-sm btn-white mb-0 w-100">Add credit</button>
    </div>
    <div class="card-footer pt-0">
      <div class="row">
        <div class="col">
          <small class="text-white opacity-8">Orders: 60%</small>
          <div class="progress progress-xs my-2">
            <div class="progress-bar bg-success" style="width: 60%"></div>
          </div>
        </div>
        <div class="col">
          <small class="text-white opacity-8">Sales: 40%</small>
          <div class="progress progress-xs my-2">
            <div class="progress-bar bg-warning" style="width: 40%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "credit-card",
};
</script>