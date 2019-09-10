import Vue from 'vue'
import VueRouter from 'vue-router'

import Logger from './pages/Logger'

Vue.use(VueRouter)

const routes = [
  {
    path: '/logger',
    name: 'logger',
    component: Logger
  }
]

const router = new VueRouter({
  routes
})

export default router
