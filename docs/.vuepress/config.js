const markdownParser = require('./markdownParser')
const path = require('path');

module.exports = {
  base: process.env.NODE_ENV ? '/vue-argon-dashboard/documentation/' : '',
  markdown: {
    config: markdownParser
  },
  configureWebpack: (config, isServer) => {
    config.resolve.alias['@'] = path.resolve(__dirname, '../../src')
    config.resolve.alias['assets'] = path.resolve(__dirname, '../../src/assets')
  },
  chainWebpack: config => {
    config.module
      .rule('vue')
      .use('vue-loader')
      .tap(options => {
          options.optimizeSSR = false
          return options;
        }
      )
  },
  head: [
    ['link', { rel: 'stylesheet', href: "https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" }],
    ['script', { href: "https://maps.googleapis.com/maps/api/js?key=AIzaSyDTTfWur0PDbZWPr7Pmq8K3jiDp0_xUziI" }],
  ],
  themeConfig: {
    sidebarDepth: 1,

    sidebar: [{
      title: 'Getting Started',
      collapsable: false,
      children: [
        '/',
        '/quick-start',
        '/licence',
        '/contents',
        '/build-tools'
      ]
    },
    {
      title: 'Foundation',
      collapsable: false,
      children: [
        '/foundation/colors',
        '/foundation/grid',
        '/foundation/typography',
        '/foundation/icons',
      ]
    },
    {
      title: 'Components',
      collapsable: false,
      children: [
        '/components/alerts',
        '/components/badges',
        '/components/buttons',
        '/components/dropdowns',
        '/components/forms',
        '/components/modal',
        '/components/tabs',
        '/components/navbar',
        '/components/pagination',
        '/components/popovers',
        '/components/progress',
        '/components/tooltips',
      ]
    },
    {
      title: 'Plugins',
      collapsable: false,
      children: [
        '/components/plugin_datepicker',
        '/components/plugin_charts',
        '/components/plugin_slider',
      ]
    }
    ],
    nav: [
      { text: 'Home', link: '/' },
    ]
  }
};
