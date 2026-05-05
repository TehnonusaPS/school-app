export default [
  {
    path: '/',
    redirect: '/login' // ⬅️ INI PENYEBAB UTAMA BLANK
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('./pages/Login.vue')
  }
]
