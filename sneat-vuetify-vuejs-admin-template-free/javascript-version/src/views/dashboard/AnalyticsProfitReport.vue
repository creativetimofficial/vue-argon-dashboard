<script setup>
import {
  useDisplay,
  useTheme,
} from 'vuetify'
import { hexToRgb } from '@core/utils/colorConverter'

const vuetifyTheme = useTheme()
const display = useDisplay()

const series = [{
  data: [
    30,
    58,
    35,
    53,
    50,
    68,
  ],
}]

const chartOptions = computed(() => {
  const currentTheme = vuetifyTheme.current.value.colors
  
  return {
    chart: {
      parentHeightOffset: 0,
      toolbar: { show: false },
      dropShadow: {
        top: 12,
        blur: 4,
        left: 0,
        enabled: true,
        opacity: 0.12,
        color: currentTheme.warning,
      },
    },
    tooltip: { enabled: false },
    colors: [`rgba(${ hexToRgb(String(currentTheme.warning)) }, 1)`],
    stroke: {
      width: 4,
      curve: 'smooth',
      lineCap: 'round',
    },
    grid: {
      show: false,
      padding: {
        top: -21,
        left: -5,
        bottom: -8,
      },
    },
    xaxis: {
      labels: { show: false },
      axisTicks: { show: false },
      axisBorder: { show: false },
    },
    yaxis: { labels: { show: false } },
    responsive: [
      {
        breakpoint: display.thresholds.value.lg,
        options: {
          chart: {
            height: 151,
            width: '100%',
          },
        },
      },
      {
        breakpoint: display.thresholds.value.md,
        options: {
          chart: {
            height: 131,
            width: '100%',
          },
        },
      },
    ],
  }
})
</script>

<template>
  <VCard>
    <VCardText class="d-flex justify-space-between h-100">
      <div class="d-flex flex-column justify-space-between gap-y-4">
        <div>
          <h5 class="text-h5 mb-1">
            Profile Report
          </h5>
          <VChip
            color="warning"
            size="small"
          >
            Year 2022
          </VChip>
        </div>

        <div>
          <div class="d-flex gap-1 align-center text-success">
            <VIcon
              icon="bx-up-arrow-alt"
              size="20"
            />
            <span class="text-base d-inline-block">68.2%</span>
          </div>

          <h4 class="text-h4">
            $84,686k
          </h4>
        </div>
      </div>

      <div class="h-100 d-flex align-center">
        <VueApexCharts
          type="line"
          :height="131"
          width="80%"
          :options="chartOptions"
          :series="series"
        />
      </div>
    </VCardText>
  </VCard>
</template>
