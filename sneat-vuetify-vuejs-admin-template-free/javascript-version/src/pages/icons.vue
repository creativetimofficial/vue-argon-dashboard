<script setup>
const iconsList = [
  'bx-abacus',
  'bx-accessibility',
  'bx-add-to-queue',
  'bx-adjust',
  'bx-alarm',
  'bx-alarm-add',
  'bx-alarm-exclamation',
  'bx-alarm-off',
  'bx-alarm-snooze',
  'bx-album',
  'bx-align-justify',
  'bx-align-left',
  'bx-align-middle',
  'bx-align-right',
  'bx-analyse',
  'bx-anchor',
  'bx-angry',
  'bx-aperture',
  'bx-arch',
  'bx-archive',
  'bx-archive-in',
  'bx-archive-out',
  'bx-area',
  'bx-arrow-back',
  'bx-arrow-from-bottom',
  'bx-arrow-from-left',
  'bx-arrow-from-right',
  'bx-arrow-from-top',
  'bx-arrow-to-bottom',
  'bx-arrow-to-left',
  'bx-arrow-to-right',
  'bx-arrow-to-top',
  'bx-at',
  'bx-atom',
  'bx-award',
  'bx-badge',
  'bx-badge-check',
  'bx-baguette',
  'bx-ball',
  'bx-band-aid',
  'bx-bar-chart',
  'bx-bar-chart-alt',
  'bx-bar-chart-alt-2',
  'bx-bar-chart-square',
  'bx-barcode',
  'bx-barcode-reader',
  'bx-baseball',
  'bx-basket',
]
</script>

<template>
  <div>
    <div class="d-flex align-center flex-wrap">
      <VCard
        v-for="icon in iconsList"
        :key="icon"
        class="mb-6 me-6"
      >
        <VCardText class="py-3 px-4">
          <VIcon
            size="30"
            :icon="icon"
          />
        </VCardText>

        <!-- tooltips -->
        <VTooltip
          location="top"
          activator="parent"
        >
          {{ icon }}
        </VTooltip>
      </VCard>
    </div>

    <!-- more icons -->
    <div class="text-center">
      <VBtn
        href="https://boxicons.com/"
        rel="noopener noreferrer"
        color="primary"
        target="_blank"
      >
        View All Box Icons
      </VBtn>
    </div>
  </div>
</template>
