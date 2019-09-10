import Vue from 'vue'
import Vue2Filters from 'vue2-filters'
import UIkit from 'uikit'

import App from './App.vue'
import mixin from './mixin'
import router from './router'
import store from './store'

import './lib/http'
import './lib/service'
import './lib/date'
import './lib/element-ui'

Vue.use(Vue2Filters)

Object.defineProperties(Vue.prototype, {
  $uikit: UIkit
})

new Vue({
  el: '#app',
  mixins: [mixin],
  router,
  store,
  render: h => h(App),
  beforeCreate() {
    this.$store.dispatch('initApp')
  }
})
