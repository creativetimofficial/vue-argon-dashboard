<script setup>
const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  image: {
    type: String,
    required: true,
  },
  stats: {
    type: String,
    required: true,
  },
  change: {
    type: Number,
    required: true,
  },
})

const isPositive = controlledComputed(() => props.change, () => Math.sign(props.change) === 1)

const moreList = [
  {
    title: 'Yesterday',
    value: 'Yesterday',
  },
  {
    title: 'Last Week',
    value: 'Last Week',
  },
  {
    title: 'Last Month',
    value: 'Last Month',
  },
]
</script>

<template>
  <VCard>
    <VCardText class="d-flex align-center pb-4">
      <img
        width="42"
        :src="props.image"
        alt="image"
      >

      <VSpacer />

      <MoreBtn
        class="me-n3 mt-n4"
        :menu-list="moreList"
      />
    </VCardText>

    <VCardText>
      <p class="mb-1">
        {{ props.title }}
      </p>
      <h5 class="text-h5 text-no-wrap mb-3">
        {{ props.stats }}
      </h5>
      <span
        :class="isPositive ? 'text-success' : 'text-error'"
        class="d-flex align-center gap-1 text-sm"
      >
        <VIcon
          size="18"
          :icon="isPositive ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt'"
        />
        {{ isPositive ? Math.abs(props.change) : props.change }}%
      </span>
    </VCardText>
  </VCard>
</template>
