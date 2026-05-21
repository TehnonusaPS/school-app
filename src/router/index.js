import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/authStore'
import SidebarLayout from '../layouts/SidebarLayout.vue'

// Import rute dari setiap module
import landingRoutes from '../modules/landing/routes'
import authRoutes from '../modules/auth/routes'
import dashboardRoutes from '../modules/dashboard/routes'
import manajemenDataRoutes from '../modules/manajemen-data/routes'
import akademikRoutes from '../modules/akademik/routes'
import keuanganRoutes from '../modules/keuangan/routes'
import absensiRoutes from '../modules/absensi/routes'
import komunikasiRoutes from '../modules/komunikasi/routes'
import laporanRoutes from '../modules/laporan/routes'
import lainnyaRoutes from '../modules/lainnya/routes'
import devRoutes from '../modules/dev/routes'

const routes = [
  // Landing page
  ...landingRoutes,

  // Rute Auth (Login, dll) biasanya tidak pakai SidebarLayout
  ...authRoutes,

  // Landing page publik
  {
    path: '/',
    name: 'Landing',
    component: () => import('../modules/public/pages/LandingPage.vue'),
    meta: { requiresAuth: false }
  },

  // Semua rute aplikasi yang pakai Sidebar
  {
    path: '/',
    component: SidebarLayout,
    meta: { requiresAuth: true },
    children: [
      ...dashboardRoutes,
      ...manajemenDataRoutes,
      ...akademikRoutes,
      ...keuanganRoutes,
      ...absensiRoutes,
      ...komunikasiRoutes,
      ...laporanRoutes,
      ...lainnyaRoutes,
      ...devRoutes
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

  // ❌ role tidak termasuk daftar yang diizinkan
  if (to.meta.roles && !to.meta.roles.includes(auth.user?.role)) {
    return next('/login')
  }

  next()
})

export default router
