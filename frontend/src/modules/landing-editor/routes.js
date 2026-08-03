export default [
  {
    path: '/landing-editor',
    name: 'LandingEditor',
    redirect: '/landing-editor/general',
    meta: { requiresAuth: true, roles: ['admin_sekolah', 'admin_yayasan', 'kepala_sekolah'] }
  },
  {
    path: '/landing-editor/general',
    name: 'LandingEditorGeneral',
    component: () => import('./pages/LandingEditorGeneral.vue'),
    meta: {
      requiresAuth: true,
      roles: ['admin_sekolah', 'admin_yayasan', 'kepala_sekolah'],
      title: 'Landing Page — Pengaturan Umum'
    }
  },
  {
    path: '/landing-editor/hero',
    name: 'LandingEditorHero',
    component: () => import('./pages/LandingEditorHero.vue'),
    meta: {
      requiresAuth: true,
      roles: ['admin_sekolah', 'admin_yayasan', 'kepala_sekolah'],
      title: 'Landing Page — Hero Section'
    }
  },
  {
    path: '/landing-editor/about',
    name: 'LandingEditorAbout',
    component: () => import('./pages/LandingEditorAbout.vue'),
    meta: {
      requiresAuth: true,
      roles: ['admin_sekolah', 'admin_yayasan', 'kepala_sekolah'],
      title: 'Landing Page — Tentang'
    }
  },
  {
    path: '/landing-editor/sections',
    name: 'LandingEditorSections',
    component: () => import('./pages/LandingEditorSections.vue'),
    meta: {
      requiresAuth: true,
      roles: ['admin_sekolah', 'admin_yayasan', 'kepala_sekolah'],
      title: 'Landing Page — Kelola Section'
    }
  },
  {
    path: '/landing-editor/sections/:sectionId',
    name: 'LandingEditorSectionDetail',
    component: () => import('./pages/LandingEditorSectionDetail.vue'),
    meta: {
      requiresAuth: true,
      roles: ['admin_sekolah', 'admin_yayasan', 'kepala_sekolah'],
      title: 'Landing Page — Detail Section'
    }
  },
  {
    path: '/landing-editor/institutions',
    name: 'LandingEditorInstitutions',
    component: () => import('./pages/LandingEditorInstitutions.vue'),
    meta: {
      requiresAuth: true,
      roles: ['admin_yayasan'],
      title: 'Landing Page — Lembaga Naungan'
    }
  },
  {
    path: '/landing-editor/donasi',
    name: 'LandingEditorDonasi',
    component: () => import('./pages/LandingEditorDonasi.vue'),
    meta: {
      requiresAuth: true,
      roles: ['admin_yayasan'],
      title: 'Landing Page — Rekening Donasi'
    }
  },
  {
    path: '/landing-editor/contact',
    name: 'LandingEditorContact',
    component: () => import('./pages/LandingEditorContact.vue'),
    meta: {
      requiresAuth: true,
      roles: ['admin_sekolah', 'admin_yayasan', 'kepala_sekolah'],
      title: 'Landing Page — Kontak & Sosmed'
    }
  }
]
