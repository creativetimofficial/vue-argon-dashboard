<script setup>
import { useTheme } from 'vuetify'
import { hexToRgb } from '@core/utils/colorConverter'

const vuetifyTheme = useTheme()

const series = [{
  data: [
    23,
    81,
    70,
    31,
    99,
    46,
    73,
  ],
}]

const chartOptions = computed(() => {
  const currentTheme = vuetifyTheme.current.value.colors
  const variableTheme = vuetifyTheme.current.value.variables
  const disabledText = `rgba(${ hexToRgb(String(currentTheme['on-surface'])) },${ variableTheme['disabled-opacity'] })`
  
  return {
    chart: {
      parentHeightOffset: 0,
      toolbar: { show: false },

      // offsetY: -30,
    },
    plotOptions: {
      bar: {
        borderRadius: 2,
        distributed: true,
        columnWidth: '65%',
        endingShape: 'rounded',
        startingShape: 'rounded',
      },

      // offsetY: -30,
    },
    legend: { show: false },
    tooltip: { enabled: false },
    dataLabels: { enabled: false },
    colors: [
      `rgba(${ hexToRgb(String(currentTheme.primary)) }, 0.16)`,
      `rgba(${ hexToRgb(String(currentTheme.primary)) }, 0.16)`,
      `rgba(${ hexToRgb(String(currentTheme.primary)) }, 0.16)`,
      `rgba(${ hexToRgb(String(currentTheme.primary)) }, 0.16)`,
      `rgba(${ hexToRgb(String(currentTheme.primary)) }, 1)`,
      `rgba(${ hexToRgb(String(currentTheme.primary)) }, 0.16)`,
      `rgba(${ hexToRgb(String(currentTheme.primary)) }, 0.16)`,
    ],
    states: {
      hover: { filter: { type: 'none' } },
      active: { filter: { type: 'none' } },

      // offsetY: -30,
    },
    xaxis: {
      categories: [
        'M',
        'T',
        'W',
        'T',
        'F',
        'S',
        'S',
      ],
      axisTicks: { show: false },
      axisBorder: { show: false },
      tickPlacement: 'on',
      offsetY: -10,
      labels: {
        style: {
          fontSize: '11px',
          colors: disabledText,
          fontFamily: 'Public Sans',
        },
      },

      // offsetY: -30,
    },
    yaxis: { show: false },
    grid: {
      show: false,
      padding: {
        left: 0,
        top: -10,
        right: 7,
        bottom: -3,
      },
    },
  }
})
</script>

<template>
  <VCard>
    <VCardText class="pb-0">
      <div class="text-base">
        Revenue
      </div>
      <h4 class="text-h4 font-weight-medium">
        425k
      </h4>
    </VCardText>

    <VueApexCharts
      type="bar"
      :height="110"
      :options="chartOptions"
      :series="series"
    />
  </VCard>
</template>
