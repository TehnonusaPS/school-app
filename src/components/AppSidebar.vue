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
  Settings,
  Palette
} from 'lucide-vue-next'
import NavMain from '@/components/NavMain.vue'
import NavUser from '@/components/NavUser.vue'
import { useAuthStore } from '@/stores/authStore'
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

const data = {
  user: {
    name: auth.user?.name || 'Pengguna',
    email: auth.user?.role || 'guest',
    avatar: ''
  },
  navMain: [
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
    },
    {
      title: 'UI Components',
      url: '/components',
      icon: Palette
    }
  ]
}
</script>

<template>
  <Sidebar collapsible="icon">
    <SidebarHeader class="sidebar-brand-header">
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg" as-child class="sidebar-brand-btn">
            <a href="/dashboard">
              <div class="sidebar-brand-icon">
                <School class="size-5" />
              </div>
              <div class="grid flex-1 text-left text-sm leading-tight">
                <span class="truncate font-extrabold text-sidebar-foreground tracking-tight">CerdasBangsa</span>
                <span class="truncate text-[10px] capitalize text-sidebar-foreground/70 font-medium">
                  {{ auth.user?.role || 'guest' }}
                </span>
              </div>
            </a>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>
    <SidebarContent>
      <NavMain :items="data.navMain" />
    </SidebarContent>
    <SidebarFooter>
      <NavUser :user="data.user" />
    </SidebarFooter>
    <SidebarRail />
  </Sidebar>
</template>
