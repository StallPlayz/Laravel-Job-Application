import { createRouter, createWebHistory } from 'vue-router'
import Register from '../views/Register.vue'
import Login from '../views/Login.vue'
import Dashboard from '../views/Dashboard.vue'
import ApplyJob from '../views/ApplyJob.vue'
import EditApplication from '../views/EditApplication.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      redirect: '/dashboard',
    },
    {
      path: '/register',
      name: 'Register',
      component: Register,
      meta: { guest: true },
    },
    {
      path: '/login',
      name: 'Login',
      component: Login,
      meta: { guest: true },
    },
    {
      path: '/dashboard',
      name: 'Dashboard',
      component: Dashboard,
      meta: { requiresAuth: true },
    },
    {
      path: '/apply/:jobId',
      name: 'ApplyJob',
      component: ApplyJob,
      meta: { requiresAuth: true },
    },
    {
      path: '/edit-application/:id',
      name: 'EditApplication',
      component: EditApplication,
      meta: { requiresAuth: true },
    },
    {
      path: '/test-axios',
      name: 'TestAxios',
      component: () => import('../views/TestAxios.vue'),
    },
  ],
})

// Navigation guard
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')

  if (to.meta.requiresAuth && !token) {
    next('/login')
  } else if (to.meta.guest && token) {
    next('/dashboard')
  } else {
    next()
  }
})

export default router
