import { createRouter, createWebHistory } from 'vue-router'
import Dashboard from '../views/Dashboard.vue'
import Rules from '../views/Rules.vue'
import Groups from '../views/Groups.vue'
import GroupDetail from '../views/GroupDetail.vue'

const routes = [
  {
    path: '/',
    name: 'Dashboard',
    component: Dashboard
  },
  {
    path: '/rules',
    name: 'Rules',
    component: Rules
  },
  {
    path: '/groups',
    name: 'Groups',
    component: Groups
  },
  {
    path: '/groups/:id',
    name: 'GroupDetail',
    component: GroupDetail,
    props: true
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router