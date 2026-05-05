import { createRouter, createWebHistory } from 'vue-router'
import authRoutes from '../modules/auth/routes'
import AdminLayout from '../layouts/AdminLayout.vue'
import { useAuthStore } from '../stores/authStore'

const routes = [
  ...authRoutes,

  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAuth: true, role: 'admin' },
    children: [
      {
        path: 'dashboard',
        component: () => import('../modules/dashboard/pages/Dashboard.vue'),
      }
    ]
  },

  {
    path: '/guru',
    component: AdminLayout,
    meta: { requiresAuth: true, role: 'guru' },
    children: [
      {
        path: 'dashboard',
        component: { template: '<div>Dashboard Guru</div>' }
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

/* 🔥 INI DIA PROTEKSI ROLE */
router.beforeEach((to, from, next) => {
  const auth = useAuthStore()

  // ❌ belum login
  if (to.meta.requiresAuth && !auth.user) {
    return next('/login')
  }

  // ❌ role tidak sesuai
  if (to.meta.role && auth.user?.role !== to.meta.role) {
    return next('/login')
  }

  next()
})

export default router