import { createRouter, createWebHistory } from 'vue-router'
import Customers from '../Pages/Customers.vue'

const routes = [
  {
    path: '/customers',
    name: 'Customers',
    component: Customers,
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
