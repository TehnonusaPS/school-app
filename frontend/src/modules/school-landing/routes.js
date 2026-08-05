export default [
  {
    path: '/s/:slug',
    name: 'SchoolLanding',
    component: () => import('./pages/SchoolLandingPage.vue'),
    meta: { requiresAuth: false }
  }
]
