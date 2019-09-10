import Vue from 'vue'
import VueRouter from 'vue-router'

import Invoice from './pages/Invoice'
import Logger from './pages/Logger'

Vue.use(VueRouter)

const routes = [
  {
    path: '/invoice',
    name: 'invoice',
    component: Invoice
  },
  {
    path: '/logs/:name',
    name: 'logger',
    component: Logger
  }
]

const router = new VueRouter({
  routes
})

export default router
