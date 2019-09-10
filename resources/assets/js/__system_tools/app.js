import Vue from 'vue'

import App from './App.vue'

import router from './router'

import 'uikit'
import './lib/http'
import './lib/element-ui'

Vue.config.devtools = true

new Vue({
  el: '#app',

  router,

  render: h => h(App)
})
