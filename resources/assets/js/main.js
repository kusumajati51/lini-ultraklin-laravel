/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import 'babel-polyfill'
import Vue from 'vue'
import PaymentChannel from './components/payment'
import Vuetify from 'vuetify'
import VueTheMask from 'vue-the-mask'
import 'vuetify/dist/vuetify.min.css'

Vue.use(Vuetify)
Vue.use(VueTheMask)
Vue.use(require('vue-moment'))
require('./bootstrap')
// require('./firebase')
// window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

new Vue({
  el: '#app',
  components: {
    PaymentChannel
  }
})
