import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'
import Customers from '../Pages/Customers.vue'
import QuotationsByCustomer from '../Pages/QuotationsByCustomer.vue'
import LandingPage from '@/Pages/LandingPage.vue'
import NotFound from '@/Pages/NotFound.vue'
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
  },

    {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: NotFound,
    meta: { title: `404 Not Found: ${appname}` }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
