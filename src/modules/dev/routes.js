export default [
  {
    path: '/components',
    name: 'ComponentShowcase',
    component: () => import('./pages/ComponentShowcase.vue'),
    meta: {
      title: 'UI Components',
      description: 'Halaman referensi untuk komponen antarmuka aplikasi.'
    }
  }
]
