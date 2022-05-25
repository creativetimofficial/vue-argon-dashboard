<template>
  <div class="card h-100">
    <div class="card-header pb-0">
      <h6>{{ title }}</h6>
      <p class="text-sm">
        <i class="fa fa-arrow-up text-success" aria-hidden="true"></i>
        <span class="font-weight-bold">24%</span> {{ text }}
      </p>
    </div>
    <div class="card-body p-3">
      <div class="timeline timeline-one-side">
        <div class="timeline-block mb-3">
          <span class="timeline-step">
            <i class="ni ni-bell-55 text-success text-gradient"></i>
          </span>
          <div class="timeline-content">
            <h6 class="text-dark text-sm font-weight-bold mb-0">{{ order }}</h6>
            <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
              {{ date }}
            </p>
          </div>
        </div>
        <div class="timeline-block mb-3">
          <span class="timeline-step">
            <i class="ni ni-html5 text-danger text-gradient"></i>
          </span>
          <div class="timeline-content">
            <h6 class="text-dark text-sm font-weight-bold mb-0">
              {{ newOrder }}
            </h6>
            <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
              {{ orderDate }}
            </p>
          </div>
        </div>
        <div class="timeline-block mb-3">
          <span class="timeline-step">
            <i class="ni ni-cart text-info text-gradient"></i>
          </span>
          <div class="timeline-content">
            <h6 class="text-dark text-sm font-weight-bold mb-0">
              {{ payment }}
            </h6>
            <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
              {{ paymentDate }}
            </p>
          </div>
        </div>
        <div class="timeline-block mb-3">
          <span class="timeline-step">
            <i class="ni ni-credit-card text-warning text-gradient"></i>
          </span>
          <div class="timeline-content">
            <h6 class="text-dark text-sm font-weight-bold mb-0">
              {{ newCard }}
            </h6>
            <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
              {{ newCardDate }}
            </p>
          </div>
        </div>
        <div class="timeline-block mb-3">
          <span class="timeline-step">
            <i class="ni ni-key-25 text-primary text-gradient"></i>
          </span>
          <div class="timeline-content">
            <h6 class="text-dark text-sm font-weight-bold mb-0">
              {{ unlockPackages }}
            </h6>
            <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
              {{ unlockPackagesDate }}
            </p>
          </div>
        </div>
        <div class="timeline-block" v-if="newOrder2">
          <span class="timeline-step">
            <i class="ni ni-money-coins text-dark text-gradient"></i>
          </span>
          <div class="timeline-content">
            <h6 class="text-dark text-sm font-weight-bold mb-0">
              {{ newOrder2 }}
            </h6>
            <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
              {{ newOrder2Date }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "orders-card",
  props: {
    title: {
      type: String,
      default: "Orders overview",
    },
    text: {
      type: String,
      default: "this month",
    },
    order: {
      type: String,
      default: "$2400, Design changes",
    },
    date: {
      type: String,
      default: "22 DEC 7:20 PM",
    },
    newOrder: {
      type: String,
      default: "New order #1832412",
    },
    orderDate: {
      type: String,
      default: "21 DEC 11 PM",
    },
    payment: {
      type: String,
      default: "Server payments for April",
    },
    paymentDate: {
      type: String,
      default: "21 DEC 9:34 PM",
    },
    newCard: {
      type: String,
      default: "New card added for order #4395133",
    },
    newCardDate: {
      type: String,
      default: "20 DEC 2:20 AM",
    },
    unlockPackages: {
      type: String,
      default: "Unlock packages for development",
    },
    unlockPackagesDate: {
      type: String,
      default: "18 DEC 4:54 AM",
    },
    newOrder2: String,
    newOrder2Date: String,
  },
};
</script>
