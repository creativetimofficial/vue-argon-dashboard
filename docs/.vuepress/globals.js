import Vue from 'vue'
import camelCase from 'lodash/camelCase'
import upperFirst from 'lodash/upperFirst'
import LineChart from '@/components/Charts/LineChart'
import BarChart from '@/components/Charts/BarChart'
import PieChart from '@/components/Charts/PieChart'
import DoughnutChart from '@/components/Charts/DoughnutChart'
// https://webpack.js.org/guides/dependency-management/#require-context
export default function getComponents() {
  let components = {}
  let baseComponents = loadComponents()
  let chartComponents = {
    LineChart,
    BarChart,
    PieChart,
    DoughnutChart
  }

  components = {
    ...baseComponents,
    ...chartComponents
  }

  return components
}

function loadComponents() {
  let components = {}
  const requireComponent = require.context(
    // Look for files in the current directory
    '../../src/components',
    // Do not look in subdirectories
    false)

// For each matching file name...
  requireComponent.keys().forEach((fileName) => {
    // Get the component config
    console.log(fileName)
    let componentConfig = requireComponent(fileName)
    componentConfig = componentConfig.default || componentConfig
    const componentName = upperFirst(
      camelCase(
        fileName
        // Remove the "./_" from the beginning
          .replace(/^\.\/_/, '')
          // Remove the file extension from the end
          .replace(/\.\w+$/, '')
      )
    )

    components[componentName] = componentConfig

    Vue.component(componentName, componentConfig)
  })
  return components
}
