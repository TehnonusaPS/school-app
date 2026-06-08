import { useAuthStore } from '@/stores/authStore'

export default [
  // 1. Rute Absensi Siswa (Eksklusif Admin Sekolah)
  {
    path: 'absensi/siswa',
    component: () => import('./pages/admin-sekolah/absensi-siswa/index.vue'),
    meta: {
      requiresAuth: true,
      roles: ['admin_sekolah'],
      title: 'Absensi Siswa',
      parent: 'Absensi',
      description: 'Rekam dan pantau kehadiran siswa.'
    }
  },
  {
    path: 'absensi/siswa/scan',
    component: () => import('./pages/admin-sekolah/presensi-siswa/index.vue'),
    meta: {
      requiresAuth: true,
      roles: ['admin_sekolah'],
      title: 'Mulai Presensi Siswa',
      parent: 'Absensi',
      description: 'Halaman pemindai absensi siswa (Wajah, Fingerprint, RFID).'
    }
  },

  // 2. Rute Redirect Absensi Guru-Staff (Bisa Admin Sekolah & Guru)
  {
    path: 'absensi/guru-staff',
    meta: {
      requiresAuth: true,
      roles: ['admin_sekolah', 'guru']
    },
    redirect: () => {
      const auth = useAuthStore()
      if (auth.user?.role === 'admin_sekolah') return '/absensi/admin-sekolah/guru-staff'
      if (auth.user?.role === 'guru') return '/absensi/guru/absensi-diri'
      return '/dashboard'
    }
  },
  {
    path: 'absensi/admin-sekolah/guru-staff',
    component: () => import('./pages/admin-sekolah/absensi-staff/index.vue'),
    meta: {
      requiresAuth: true,
      roles: ['admin_sekolah'],
      title: 'Absensi Staff',
      parent: 'Absensi',
      description: 'Rekam dan pantau kehadiran guru dan staff.'
    }
  },
  {
    path: 'absensi/guru/absensi-diri',
    component: () => import('./pages/guru/absensi-diri/index.vue'),
    meta: {
      requiresAuth: true,
      roles: ['guru'],
      title: 'Absensi Guru',
      parent: 'Absensi',
      description: 'Mandiri mencatat kehadiran menggunakan kamera.'
    }
  },

  // 3. Rute Redirect Rekap Absensi
  {
    path: 'absensi/rekap',
    meta: {
      requiresAuth: true,
      roles: ['admin_sekolah', 'siswa', 'orang_tua']
    },
    redirect: () => {
      const auth = useAuthStore()
      if (auth.user?.role === 'admin_sekolah') return '/absensi/admin-sekolah/rekap'
      if (auth.user?.role === 'siswa') return '/absensi/siswa/rekap'
      if (auth.user?.role === 'orang_tua') return '/absensi/orang-tua/rekap'
      return '/dashboard'
    }
  },
  {
    path: 'absensi/admin-sekolah/rekap',
    component: () => import('./pages/admin-sekolah/rekap/index.vue'),
    meta: {
      requiresAuth: true,
      roles: ['admin_sekolah'],
      title: 'Rekap Bulanan',
      parent: 'Absensi',
      description: 'Rekap kehadiran bulanan seluruh civitas.'
    }
  },
  {
    path: 'absensi/siswa/rekap',
    component: () => import('./pages/siswa/rekap/index.vue'),
    meta: {
      requiresAuth: true,
      roles: ['siswa'],
      title: 'Rekap Absensi',
      parent: 'Absensi',
      description: 'Rekap kehadiran Anda.'
    }
  },
  {
    path: 'absensi/orang-tua/rekap',
    component: () => import('./pages/orang-tua/rekap/index.vue'),
    meta: {
      requiresAuth: true,
      roles: ['orang_tua'],
      title: 'Rekap Absensi',
      parent: 'Absensi',
      description: 'Rekap kehadiran anak Anda.'
    }
  },

  // 4. Rute Redirect Input Kehadiran (Bisa Guru & Wali Kelas)
  {
    path: 'absensi/input',
    meta: {
      requiresAuth: true,
      roles: ['guru', 'wali_kelas']
    },
    redirect: () => {
      const auth = useAuthStore()
      if (auth.user?.role === 'guru') return '/absensi/guru/input-kehadiran'
      if (auth.user?.role === 'wali_kelas') return '/absensi/wali-kelas/input-kehadiran'
      return '/dashboard'
    }
  },
  {
    path: 'absensi/guru/input-kehadiran',
    component: () => import('./pages/guru/input-kehadiran/index.vue'),
    meta: {
      requiresAuth: true,
      roles: ['guru'],
      title: 'Kehadiran',
      parent: 'Absensi',
      description: 'Isi lembar kehadiran siswa secara harian dan periodik.'
    }
  },
  {
    path: 'absensi/wali-kelas/input-kehadiran',
    component: () => import('./pages/wali-kelas/input-kehadiran/index.vue'),
    meta: {
      requiresAuth: true,
      roles: ['wali_kelas'],
      title: 'Kehadiran',
      parent: 'Absensi',
      description: 'Isi lembar kehadiran siswa secara harian dan periodik.'
    }
  }
]
