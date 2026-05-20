<script setup>
import {
  LayoutDashboard,
  Database,
  GraduationCap,
  Wallet,
  ClipboardList,
  MessageSquare,
  FileBarChart,
  MoreHorizontal,
  Users,
  BookOpen,
  CalendarDays,
  DollarSign,
  Receipt,
  PiggyBank,
  UserCheck,
  Calendar,
  Bell,
  Mail,
  FileText,
  BarChart3,
  School,
  UserCog,
  Settings
} from 'lucide-vue-next'
import NavMain from '@/components/NavMain.vue'
import NavUser from '@/components/NavUser.vue'
import { useAuthStore } from '@/stores/authStore'
import { computed } from 'vue'
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarRail,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem
} from '@/components/ui/sidebar'

const auth = useAuthStore()

const roleLabels = {
  'admin-yayasan': 'Admin Yayasan',
  'kepala-sekolah': 'Kepala Sekolah',
  'admin-sekolah': 'Admin Sekolah',
  'tata-usaha': 'Tata Usaha',
  admin: 'Admin',
  guru: 'Guru',
  siswa: 'Siswa'
}

const role = computed(() => auth.user?.role || 'guest')
const roleLabel = computed(() => roleLabels[role.value] || role.value)

const fullNav = [
  {
    title: 'Dashboard',
    url: '/dashboard',
    icon: LayoutDashboard
  },
  {
    title: 'Manajemen Data',
    url: '/manajemen-data',
    icon: Database,
    items: [
      { title: 'Data Siswa', url: '/manajemen-data/siswa' },
      { title: 'Data Guru & Staff', url: '/manajemen-data/guru-staff' },
      { title: 'Data Kelas', url: '/manajemen-data/kelas' },
      { title: 'Data Mata Pelajaran', url: '/manajemen-data/mata-pelajaran' },
      { title: 'Tahun Ajaran', url: '/manajemen-data/tahun-ajaran' }
    ]
  },
  {
    title: 'Akademik',
    url: '/akademik',
    icon: GraduationCap,
    items: [
      { title: 'Jadwal Pelajaran', url: '/akademik/jadwal' },
      { title: 'Nilai & Rapor', url: '/akademik/nilai' },
      { title: 'Ujian & Penilaian', url: '/akademik/ujian' },
      { title: 'Kurikulum', url: '/akademik/kurikulum' },
      { title: 'Ekstrakurikuler', url: '/akademik/ekskul' }
    ]
  },
  {
    title: 'Keuangan',
    url: '/keuangan',
    icon: Wallet,
    items: [
      { title: 'SPP Siswa', url: '/keuangan/spp' },
      { title: 'Tagihan & Pembayaran', url: '/keuangan/tagihan' },
      { title: 'Pengeluaran', url: '/keuangan/pengeluaran' },
      { title: 'Laporan Keuangan', url: '/keuangan/laporan' }
    ]
  },
  {
    title: 'Absensi',
    url: '/absensi',
    icon: ClipboardList,
    items: [
      { title: 'Absensi Siswa', url: '/absensi/siswa' },
      { title: 'Absensi Guru & Staff', url: '/absensi/guru-staff' },
      { title: 'Rekap Bulanan', url: '/absensi/rekap' }
    ]
  },
  {
    title: 'Komunikasi',
    url: '/komunikasi',
    icon: MessageSquare,
    items: [
      { title: 'Pengumuman', url: '/komunikasi/pengumuman' },
      { title: 'Pesan Internal', url: '/komunikasi/pesan' },
      { title: 'Notifikasi', url: '/komunikasi/notifikasi' }
    ]
  },
  {
    title: 'Laporan',
    url: '/laporan',
    icon: FileBarChart,
    items: [
      { title: 'Laporan Akademik', url: '/laporan/akademik' },
      { title: 'Laporan Keuangan', url: '/laporan/keuangan' },
      { title: 'Laporan Absensi', url: '/laporan/absensi' },
      { title: 'Ekspor Data', url: '/laporan/ekspor' }
    ]
  },
  {
    title: 'Lainnya',
    url: '/lainnya',
    icon: MoreHorizontal,
    items: [
      { title: 'Pengaturan Sekolah', url: '/lainnya/pengaturan' },
      { title: 'Manajemen Pengguna', url: '/lainnya/pengguna' },
      { title: 'Backup & Restore', url: '/lainnya/backup' }
    ]
  }
]

const navByTitle = Object.fromEntries(fullNav.map(item => [item.title, item]))

const adminYayasanNav = [
  navByTitle['Dashboard'],
  navByTitle['Manajemen Data'],
  navByTitle['Keuangan'],
  navByTitle['Laporan'],
  navByTitle['Komunikasi'],
  navByTitle['Lainnya']
].filter(Boolean)

const kepalaSekolahNav = [
  navByTitle['Dashboard'],
  navByTitle['Manajemen Data'],
  navByTitle['Akademik'],
  navByTitle['Laporan'],
  navByTitle['Komunikasi'],
  navByTitle['Lainnya']
].filter(Boolean)

const adminSekolahNav = [
  navByTitle['Dashboard'],
  navByTitle['Manajemen Data'],
  navByTitle['Absensi'],
  navByTitle['Akademik'],
  navByTitle['Keuangan'],
  navByTitle['Laporan'],
  navByTitle['Komunikasi'],
  navByTitle['Lainnya']
].filter(Boolean)

const tataUsahaNav = [
  navByTitle['Dashboard'],
  navByTitle['Manajemen Data'],
  navByTitle['Keuangan'],
  navByTitle['Laporan'],
  navByTitle['Komunikasi']
].filter(Boolean)

const dashboardOnlyNav = [
  {
    title: 'Dashboard',
    url: '/dashboard',
    icon: LayoutDashboard
  }
]

const dashboardOnlyRoles = new Set([])

const navMain = computed(() => {
  if (role.value === 'admin-yayasan') return adminYayasanNav
  if (role.value === 'kepala-sekolah') return kepalaSekolahNav
  if (role.value === 'admin-sekolah') return adminSekolahNav
  if (role.value === 'tata-usaha') return tataUsahaNav
  if (dashboardOnlyRoles.has(role.value)) return dashboardOnlyNav
  return fullNav
})

const user = computed(() => ({
  name: auth.user?.name || 'Pengguna',
  email: roleLabel.value,
  role: roleLabel.value,
  avatar: ''
}))
</script>

<template>
  <Sidebar collapsible="icon">
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg" as-child>
            <a href="/dashboard">
              <div
                class="flex aspect-square size-8 items-center justify-center rounded-lg bg-sidebar-primary text-sidebar-primary-foreground"
              >
                <School class="size-4" />
              </div>
              <div class="grid flex-1 text-left text-sm leading-tight">
                <span class="truncate font-semibold">Sekolah App</span>
                <span class="truncate text-xs capitalize">{{ roleLabel }}</span>
              </div>
            </a>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>
    <SidebarContent>
      <NavMain :items="navMain" />
    </SidebarContent>
    <SidebarFooter>
      <NavUser :user="user" />
    </SidebarFooter>
    <SidebarRail />
  </Sidebar>
</template>
