<script setup>
import { useTheme } from 'vuetify'

const vuetifyTheme = useTheme()

const series = [{
  data: [
    30,
    70,
    35,
    55,
    45,
    70,
  ],
}]

const chartOptions = computed(() => {
  const currentTheme = vuetifyTheme.current.value.colors
  
  return {
    chart: {
      parentHeightOffset: 0,
      toolbar: { show: false },
    },
    tooltip: { enabled: false },
    dataLabels: { enabled: false },
    stroke: {
      width: 2,
      curve: 'smooth',
      lineCap: 'round',
    },
    grid: {
      show: false,
      padding: {
        left: 0,
        top: -32,
        right: 17,
      },
    },
    fill: {
      type: 'gradient',
      gradient: {
        opacityTo: 0.7,
        opacityFrom: 0.5,
        shadeIntensity: 1,
        stops: [
          0,
          90,
          100,
        ],
        colorStops: [[
          {
            offset: 0,
            opacity: 0.6,
            color: currentTheme.success,
          },
          {
            offset: 100,
            opacity: 0.1,
            color: currentTheme.surface,
          },
        ]],
      },
    },
    theme: {
      monochrome: {
        enabled: true,
        shadeTo: 'light',
        shadeIntensity: 1,
        color: currentTheme.success,
      },
    },
    xaxis: {
      labels: { show: false },
      axisTicks: { show: false },
      axisBorder: { show: false },
    },
    yaxis: { show: false },
    markers: {
      size: 1,
      offsetY: 2,
      offsetX: -5,
      strokeWidth: 3,
      strokeOpacity: 1,
      colors: ['transparent'],
      strokeColors: 'transparent',
      discrete: [{
        size: 6,
        seriesIndex: 0,
        fillColor: '#fff',
        strokeColor: currentTheme.success,
        dataPointIndex: series[0].data.length - 1,
      }],
    },
  }
})
</script>

<template>
  <VCard>
    <VCardText class="pb-4">
      <div class="text-base">
        Order
      </div>
      <h4 class="text-h4">
        276k
      </h4>
    </VCardText>

    <VueApexCharts
      type="area"
      :height="104"
      :options="chartOptions"
      :series="series"
    />
  </VCard>
</template>
