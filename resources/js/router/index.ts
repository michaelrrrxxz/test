import { createRouter, createWebHistory } from 'vue-router'
import Customers from '../Pages/Customers.vue'
import Users from '../Pages/Users.vue'
import QuotationsByCustomer from '../Pages/QuotationsByCustomer.vue'


const routes = [
  {
    path: '/customers',
    name: 'Customers',
    component: Customers,
  },
  {
    path: '/users',
    name: 'Users',  
    component: Users,
  },
  {
    path: '/quotations/:customerId',
    name: 'QuotationsByCustomer',
    component: QuotationsByCustomer,
    props: true,
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
