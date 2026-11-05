import { createRouter, createWebHistory } from 'vue-router'
import PublicHome from '../views/PublicHome.vue'
import AdminLogin from '../views/AdminLogin.vue'
import AdminDashboard from '../views/AdminDashboard.vue'
import { getAdminToken } from '../services/api'

const routes = [
  {
    path: '/',
    name: 'PublicHome',
    component: PublicHome,
    meta: { title: 'The Kit Room | Team Jersey Tracker 2026' }
  },
  {
    path: '/admin/login',
    name: 'AdminLogin',
    component: AdminLogin,
    meta: { title: 'Admin Login | Team Jersey Tracker' }
  },
  {
    path: '/admin',
    name: 'AdminDashboard',
    component: AdminDashboard,
    meta: { requiresAuth: true, title: 'Admin Dashboard | Team Jersey Tracker' }
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/'
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Authentication route guard
router.beforeEach((to, from, next) => {
  if (to.meta.title) {
    document.title = to.meta.title
  }

  const token = getAdminToken()

  if (to.meta.requiresAuth && !token) {
    next({ name: 'AdminLogin', query: { redirect: to.fullPath } })
  } else if (to.name === 'AdminLogin' && token) {
    next({ name: 'AdminDashboard' })
  } else {
    next()
  }
})

export default router
