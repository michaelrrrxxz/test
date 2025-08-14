import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'
import Customers from '../Pages/Customers.vue'
import QuotationsByCustomer from '../Pages/QuotationsByCustomer.vue'
import LandingPage from '@/Pages/LandingPage.vue'


declare module 'vue-router' {
  interface RouteMeta {
    title?: string
  }
}

const appname = " | AND I QUOTE"

const routes: RouteRecordRaw[] = [
    {
    path:'/',
    name:'LandingPage',
    component: LandingPage,
     meta: { title: `${appname}` }
    },
  {
    path: '/customers',
    name: 'Customers',
    component: Customers,
     meta: { title: `Customers: ${appname}` }
  },
  {
    path: '/quotations/:customerId',
    name: 'QuotationsByCustomer',
    component: QuotationsByCustomer,
    meta: { title: `Quotations: ${appname}` },
    props: true,
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
