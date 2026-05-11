import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/authStore'
import SidebarLayout from '../layouts/SidebarLayout.vue'

// Import rute dari setiap module
import authRoutes from '../modules/auth/routes'
import dashboardRoutes from '../modules/dashboard/routes'
import manajemenDataRoutes from '../modules/manajemen-data/routes'
import akademikRoutes from '../modules/akademik/routes'
import keuanganRoutes from '../modules/keuangan/routes'
import absensiRoutes from '../modules/absensi/routes'
import komunikasiRoutes from '../modules/komunikasi/routes'
import laporanRoutes from '../modules/laporan/routes'
import lainnyaRoutes from '../modules/lainnya/routes'

const routes = [
  // Rute Auth (Login, dll) biasanya tidak pakai SidebarLayout
  ...authRoutes,

  // Semua rute aplikasi yang pakai Sidebar
  {
    path: '/',
    component: SidebarLayout,
    meta: { requiresAuth: true },
    children: [
      { path: '', redirect: '/dashboard' },
      ...dashboardRoutes,
      ...manajemenDataRoutes,
      ...akademikRoutes,
      ...keuanganRoutes,
      ...absensiRoutes,
      ...komunikasiRoutes,
      ...laporanRoutes,
      ...lainnyaRoutes
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
