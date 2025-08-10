import { createRouter, createWebHistory } from 'vue-router';
import type { RouteRecordRaw } from 'vue-router';
import Customers from '../Pages/Customers.vue';

const routes: Array<RouteRecordRaw> = [
  {
    path: '/customers',
    name: 'customers',
    component: Customers
  },
  { path: '/', redirect: '/customers' }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

export default router;
