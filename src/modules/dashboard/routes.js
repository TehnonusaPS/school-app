export default [
  {
    path: 'dashboard',
    component: () => import('./pages/Dashboard.vue'),
    meta: {
      title: 'Dashboard',
      description: 'Ringkasan aktivitas dan statistik sekolah.'
    }
  }
]
