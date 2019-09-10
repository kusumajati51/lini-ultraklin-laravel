import Vue from 'vue'
import VueRouter from 'vue-router'

import Dashboard from './pages/Dashboard'

import Order from './pages/order/Index'
import OrderShow from './pages/order/Show'

import Agent from './pages/agent/Index'
import AgentCreate from './pages/agent/Create'

import Sales from './pages/sales/Index'
import SalesCreate from './pages/sales/Create'
import SalesShow from './pages/sales/Show'

import Store from './pages/store/Index'

import AgentLevel from './pages/agent-level/Index'
import AgentLevelCreate from './pages/agent-level/Create'
import AgentLevelShow from './pages/agent-level/Show'

import SalesLevel from './pages/sales-level/Index'
import SalesLevelCreate from './pages/sales-level/Create'
import SalesLevelShow from './pages/sales-level/Show'

import OrderByUpline from './pages/order/IndexByUpline'

import UserByUpline from './pages/user/IndexByUpline'

Vue.use(VueRouter)

const routes = [
  {
    path: '/dashboard',
    name: 'dashboard',
    component: Dashboard
  },
  {
    path: '/orders',
    name: 'order',
    component: Order
  },
  {
    path: '/orders/:code',
    name: 'order-show',
    component: OrderShow
  },
  {
    path: '/agents',
    name: 'agent',
    component: Agent
  },
  {
    path: '/agents/create',
    name: 'agent-create',
    component: AgentCreate
  },
  {
    path: '/agents/:id/edit',
    name: 'agent-edit',
    component: AgentCreate
  },
  {
    path: '/sales',
    name: 'sales',
    component: Sales
  },
  {
    path: '/sales/create',
    name: 'sales-create',
    component: SalesCreate
  },
  {
    path: '/sales/:code',
    name: 'sales-show',
    component: SalesShow
  },
  {
    path: '/sales/:code/downline',
    name: 'sales-downline',
    component: UserByUpline
  },
  {
    path: '/sales/:code/downline/orders',
    name: 'sales-downline-order',
    component: OrderByUpline
  },
  {
    path: '/sales/:code/downline/orders/:orderCode',
    name: 'sales-downline-order-show',
    component: OrderShow
  },
  {
    path: '/sales/:id/edit',
    name: 'sales-edit',
    component: SalesCreate
  },
  {
    path: '/partners',
    name: 'partner',
    component: Store
  },
  {
    path: '/agent-levels',
    name: 'agent-level',
    component: AgentLevel
  },
  {
    path: '/agent-levels/create',
    name: 'agent-level-create',
    component: AgentLevelCreate
  },
  {
    path: '/agent-levels/:id',
    name: 'agent-level-show',
    component: AgentLevelShow
  },
  {
    path: '/agent-levels/:id/edit',
    name: 'agent-level-edit',
    component: AgentLevelCreate
  },
  {
    path: '/sales-levels',
    name: 'sales-level',
    component: SalesLevel
  },
  {
    path: '/sales-levels/create',
    name: 'sales-level-create',
    component: SalesLevelCreate
  },
  {
    path: '/sales-levels/:id',
    name: 'sales-level-show',
    component: SalesLevelShow
  },
  {
    path: '/sales-levels/:id/edit',
    name: 'sales-level-edit',
    component: SalesLevelCreate
  }
]

const router = new VueRouter({
  routes
})

export default router
