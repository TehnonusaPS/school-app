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
  Palette,
  ChevronsUpDown
} from 'lucide-vue-next'
import NavMain from '@/components/NavMain.vue'
import NavUser from '@/components/NavUser.vue'
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuItem } from '@/components/ui/dropdown-menu'
import { computed } from 'vue'
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

const currentUser = computed(() => ({
  name: auth.user?.name || 'Pengguna',
  email: auth.user?.email || auth.user?.role || 'guest',
  role: auth.user?.roleLabel || auth.user?.role || 'guest',
  avatar: ''
}))

// Struktur RBAC Dinamis
// Cara Penggunaan:
// Tambahkan properti `roles: ['nama_role']` pada item parent atau children untuk membatasi akses.
// Jika properti `roles` tidak ada, maka menu tersebut dapat diakses oleh SEMUA role.
// Role yang tersedia di authStore: 'superadmin', 'admin_yayasan', 'kepala_sekolah', 'admin_sekolah', 'tata_usaha', 'guru', 'wali_kelas', 'siswa', 'orang_tua'

const filteredNavMain = computed(() => {
  const currentRole = auth.user?.role

  return (
    data.navMain
      // 1. Filter parent menu
      .filter(item => {
        if (item.excludeRoles && item.excludeRoles.includes(currentRole)) return false
        if (!item.roles) return true // Terbuka untuk semua jika tidak ada pembatasan
        return item.roles.includes(currentRole)
      })
      // 2. Filter children menu (jika ada)
      .map(item => {
        if (item.items) {
          return {
            ...item,
            items: item.items.filter(sub => {
              if (sub.excludeRoles && sub.excludeRoles.includes(currentRole)) return false
              if (!sub.roles) return true
              return sub.roles.includes(currentRole)
            })
          }
        }
        return item
      })
      // 3. Sembunyikan parent jika semua childnya tersembunyi (opsional, tapi disarankan)
      .filter(item => {
        if (item.items && item.items.length === 0) return false
        return true
      })
  )
})

const data = {
  navMain: [
    {
      title: 'Dashboard',
      url: '/dashboard',
      icon: LayoutDashboard
      // roles: ['superadmin', 'admin_yayasan', 'kepala_sekolah'] <-- Contoh penggunaan
    },
    {
      title: 'Manajemen Data',
      url: '/manajemen-data',
      icon: Database,
      items: [
        {
          title: 'Data Siswa',
          url: '/manajemen-data/siswa'
          // roles: ['superadmin', 'admin_sekolah'] <-- Contoh membatasi sub-menu
        },
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
        { title: 'Mata Pelajaran', url: '/akademik/mapel' },
        { title: 'Ekstrakurikuler', url: '/akademik/ekskul' }
      ]
    },
    {
      title: 'Keuangan',
      url: '/keuangan',
      icon: Wallet,
      items: [
        { title: 'SPP Siswa', url: '/keuangan/spp', roles : ['admin_sekolah','siswa','orang_tua'] },
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
        { title: 'Pengumuman', url: '/komunikasi/pengumuman', excludeRoles: ['tata_usaha'] },
        {
          title: 'Berita Kegiatan',
          url: '/komunikasi/berita-kegiatan',
          excludeRoles: ['tata_usaha']
        },
        { title: 'Feedback Orang Tua', url: '/komunikasi/feedback', excludeRoles: ['tata_usaha'] },
        { title: 'Pesan Internal', url: '/komunikasi/pesan', excludeRoles: ['tata_usaha'] },
        { title: 'Notifikasi', url: '/komunikasi/notifikasi', excludeRoles: ['tata_usaha'] },
        { title: 'Keterangan Aktif', url: '/komunikasi/persuratan/aktif', roles: ['tata_usaha'] },
        {
          title: 'Surat Dispensasi',
          url: '/komunikasi/persuratan/dispensasi',
          roles: ['tata_usaha']
        },
        { title: 'Keterangan Lulus', url: '/komunikasi/persuratan/lulus', roles: ['tata_usaha'] },
        {
          title: 'Peringatan/Tunggakan',
          url: '/komunikasi/persuratan/peringatan',
          roles: ['tata_usaha']
        }
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
        { title: 'Backup & Restore', url: '/lainnya/backup' },
        { title: 'Manajemen Ruangan', url: '/lainnya/ruangan' },
        { title: 'Manajemen Aset', url: '/lainnya/aset' },
        { title: 'Manajemen Perpustakaan', url: '/lainnya/perpustakaan' }
      ]
    },
    {
      title: 'UI Components',
      url: '/components',
      icon: Palette,
      // Contoh pembatasan: Hanya role 'superadmin' yang bisa melihat menu ini
      roles: ['superadmin']
    }
  ]
}
</script>

<template>
  <Sidebar collapsible="icon" variant="floating">
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <DropdownMenu>
            <DropdownMenuTrigger as-child>
              <SidebarMenuButton size="lg"
                class="glass-mini text-sidebar-accent-foreground cursor-default hover:bg-transparent active:bg-transparent data-[state=open]:bg-transparent transition-colors duration-300">
                <Avatar class="h-8 w-8 rounded-lg">
                  <AvatarFallback class="rounded-lg bg-primary text-primary-foreground">
                    <School class="size-5" />
                  </AvatarFallback>
                </Avatar>
                <div class="grid flex-1 text-left text-sm leading-tight">
                  <span class="truncate font-extrabold text-sidebar-foreground tracking-tight">CerdasBangsa</span>
                  <span class="truncate text-[10px] capitalize text-sidebar-foreground/70 font-medium">
                    {{ auth.user?.roleLabel || auth.user?.role || 'guest' }}
                  </span>
                </div>
              </SidebarMenuButton>
            </DropdownMenuTrigger>
          </DropdownMenu>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>
    <SidebarContent>
      <NavMain :items="filteredNavMain" />
    </SidebarContent>
    <SidebarFooter>
      <NavUser :user="currentUser" />
    </SidebarFooter>
    <SidebarRail />
  </Sidebar>
</template>
