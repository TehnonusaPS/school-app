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
import { sidebarSlide } from '@/config/motion'

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
      excludeRoles: ['guru', 'siswa', 'orang_tua'],
      items: [
        { 
          title: 'Siswa', 
          url: '/manajemen-data/siswa',
          roles: ['admin_sekolah', 'tata_usaha']
        },
        { 
          title: 'Yayasan', 
          url: '/manajemen-data/yayasan',
          roles: ['superadmin']
        },
        { 
          title: 'Sekolah', 
          url: '/manajemen-data/sekolah',
          roles: ['superadmin', 'admin_yayasan']
        },
        { 
          title: 'Kelola Hak Akses', 
          url: '/manajemen-data/hak-akses',
          roles: ['superadmin']
        },
        { 
          title: 'Guru dan Staff', 
          url: '/manajemen-data/guru-staff-yayasan',
          roles: ['admin_yayasan']
        },
        { 
          title: 'Monitoring Kelas', 
          url: '/manajemen-data/monitoring-kelas',
          roles: ['kepala_sekolah']
        },
        { 
          title: 'Ekstrakulikuler', 
          url: '/manajemen-data/ekskul',
          roles: ['kepala_sekolah', 'admin_sekolah']
        },
        { 
          title: 'Jadwal Pelajaran', 
          url: '/manajemen-data/jadwal',
          roles: ['kepala_sekolah']
        },
        { 
          title: 'Manajemen Kelas', 
          url: '/manajemen-data/manajemen-kelas',
          roles: ['admin_sekolah']
        },
        { title: 'Guru dan Staff', url: '/manajemen-data/guru-staff', excludeRoles: ['tata_usaha', 'superadmin', 'admin_yayasan', 'wali_kelas'] },
        { title: 'Data Kelas', url: '/manajemen-data/kelas', excludeRoles: ['tata_usaha', 'superadmin', 'admin_yayasan', 'kepala_sekolah', 'wali_kelas'] },
        { title: 'Mata Pelajaran', url: '/manajemen-data/mata-pelajaran', excludeRoles: ['tata_usaha', 'superadmin', 'admin_yayasan', 'wali_kelas'] },
        { 
          title: 'Tahun Ajaran', 
          url: '/manajemen-data/tahun-ajaran',
          roles: ['admin_sekolah']
        },
        { title: 'Tahun Ajaran', url: '/manajemen-data/tahun-ajaran', excludeRoles: ['tata_usaha', 'superadmin', 'admin_yayasan', 'kepala_sekolah', 'admin_sekolah', 'wali_kelas'] }
      ]
    },
    {
      title: 'Akademik',
      url: '/akademik',
      icon: GraduationCap,
      excludeRoles: ['superadmin', 'admin_yayasan', 'tata_usaha'],
      items: [
        { title: 'Jadwal Pelajaran', url: '/akademik/jadwal', excludeRoles: ['kepala_sekolah', 'admin_sekolah', 'wali_kelas', 'orang_tua'] },
        { title: 'Nilai & Rapor', url: '/akademik/nilai', excludeRoles: ['kepala_sekolah', 'admin_sekolah', 'guru', 'wali_kelas', 'siswa', 'orang_tua'] },
        { title: 'Ujian & Penilaian', url: '/akademik/ujian', excludeRoles: ['kepala_sekolah', 'admin_sekolah', 'guru', 'wali_kelas', 'siswa', 'orang_tua'] },
        { title: 'Kurikulum', url: '/akademik/kurikulum', excludeRoles: ['kepala_sekolah', 'admin_sekolah', 'guru', 'wali_kelas', 'siswa', 'orang_tua'] },
        { title: 'Mata Pelajaran', url: '/akademik/mapel', excludeRoles: ['kepala_sekolah', 'admin_sekolah', 'wali_kelas', 'siswa', 'orang_tua'] },
        { title: 'Ekstrakurikuler', url: '/akademik/ekskul', excludeRoles: ['kepala_sekolah', 'admin_sekolah', 'guru', 'wali_kelas', 'siswa', 'orang_tua'] },
        {
          title: 'Kalender',
          url: '/akademik/kalender',
          roles: ['kepala_sekolah', 'admin_sekolah']
        },
        {
          title: 'Kegiatan',
          url: '/akademik/kegiatan',
          roles: ['kepala_sekolah', 'admin_sekolah']
        },
        {
          title: 'Nilai & Raport',
          url: '/akademik/nilai',
          roles: ['siswa']
        },
        {
          title: 'Materi',
          url: '/akademik/materi',
          roles: ['siswa']
        },
        {
          title: 'Mata Pelajaran',
          url: '/akademik/mapel',
          roles: ['siswa']
        },
        {
          title: 'Tugas',
          url: '/akademik/tugas',
          roles: ['siswa']
        },
        {
          title: 'Ujian',
          url: '/akademik/ujian',
          roles: ['siswa']
        },
        {
          title: 'Nilai & Rapor',
          url: '/akademik/nilai',
          roles: ['orang_tua']
        },
        {
          title: 'Jadwal Pelajaran',
          url: '/akademik/jadwal',
          roles: ['orang_tua']
        }
      ]
    },
    {
      title: 'Administrasi',
      url: '/administrasi',
      icon: Mail,
      allowedRoles: ['tata_usaha'],
      items: [
        { 
          title: 'Loket', 
          url: '/administrasi/loket', 
          roles: ['tata_usaha']
        }
      ]
    },
    {
      title: 'Keuangan',
      url: '/keuangan',
      icon: Wallet,
      excludeRoles: ['kepala_sekolah', 'guru', 'wali_kelas'],
      items: [
        { title: 'SPP Siswa', url: '/keuangan/spp', excludeRoles: ['tata_usaha', 'superadmin', 'admin_yayasan', 'admin_sekolah', 'siswa', 'orang_tua'] },
        { 
          title: 'SPP Siswa', 
          url: '/keuangan/spp',
          roles: ['tata_usaha']
        },
        { title: 'Transaksi', url: '/keuangan/tagihan', excludeRoles: ['tata_usaha', 'superadmin', 'admin_yayasan', 'admin_sekolah', 'siswa', 'orang_tua'] },
        { 
          title: 'Transaksi', 
          url: '/keuangan/tagihan',
          roles: ['tata_usaha']
        },
        { title: 'Pengeluaran', url: '/keuangan/pengeluaran', excludeRoles: ['tata_usaha', 'superadmin', 'admin_yayasan', 'admin_sekolah', 'siswa', 'orang_tua'] },
        { title: 'Laporan Keuangan', url: '/keuangan/laporan', excludeRoles: ['tata_usaha', 'superadmin', 'admin_yayasan', 'admin_sekolah', 'siswa', 'orang_tua'] },
        {
          title: 'Subscribtion',
          url: '/keuangan/subscription',
          roles: ['superadmin']
        },
        {
          title: 'Keuangan Yayasan',
          url: '/keuangan/monitoring-yayasan',
          roles: ['admin_yayasan']
        },
        {
          title: 'Paket Subcription',
          url: '/keuangan/paket-subscription',
          roles: ['admin_yayasan']
        },
        {
          title: 'Dana Yayasan',
          url: '/keuangan/kelola-dana-yayasan',
          roles: ['admin_sekolah']
        },
        {
          title: 'Tarif SPP',
          url: '/keuangan/tarif-spp',
          roles: ['admin_sekolah']
        },
        {
          title: 'SPP',
          url: '/keuangan/spp',
          roles: ['siswa']
        },
        {
          title: 'SPP',
          url: '/keuangan/spp',
          roles: ['orang_tua']
        },
        {
          title: 'Transaksi',
          url: '/keuangan/tagihan',
          roles: ['orang_tua']
        }
      ]
    },
    {
      title: 'Absensi',
      url: '/absensi',
      icon: ClipboardList,
      excludeRoles: ['superadmin', 'admin_yayasan'],
      items: [
        { title: 'Absensi Siswa', url: '/absensi/siswa', roles: ['admin_sekolah'] },
        { title: 'Absensi Staff', url: '/absensi/guru-staff', roles: ['admin_sekolah', 'kepala_sekolah', 'tata_usaha'] },
        { title: 'Rekap Bulanan', url: '/absensi/rekap', roles: ['admin_sekolah'] },
        {
          title: 'Absensi Guru',
          url: '/absensi/guru-staff',
          roles: ['guru', 'wali_kelas']
        },
        {
          title: 'Kehadiran',
          url: '/absensi/input',
          roles: ['guru', 'wali_kelas']
        },
        {
          title: 'Rekap Absensi',
          url: '/absensi/rekap',
          roles: ['siswa']
        },
        {
          title: 'Rekap Absensi',
          url: '/absensi/rekap',
          roles: ['orang_tua']
        }
      ]
    },
    {
      title: 'Komunikasi',
      url: '/komunikasi',
      icon: MessageSquare,
      excludeRoles: ['superadmin', 'guru', 'wali_kelas', 'siswa'],
      items: [
        { title: 'Pengumuman', url: '/komunikasi/pengumuman', excludeRoles: ['tata_usaha', 'kepala_sekolah', 'admin_sekolah', 'orang_tua'] },
        { title: 'Berita Kegiatan', url: '/komunikasi/berita-kegiatan', excludeRoles: ['tata_usaha', 'admin_yayasan', 'orang_tua'] },
        { title: 'Feedback', url: '/komunikasi/feedback', excludeRoles: ['tata_usaha', 'admin_yayasan'] },
        { title: 'Pesan Internal', url: '/komunikasi/pesan', excludeRoles: ['tata_usaha', 'admin_yayasan', 'kepala_sekolah', 'admin_sekolah', 'orang_tua'] },
        { title: 'Notifikasi', url: '/komunikasi/notifikasi', excludeRoles: ['tata_usaha', 'admin_yayasan', 'kepala_sekolah', 'admin_sekolah', 'orang_tua'] },
        { title: 'Keterangan Aktif', url: '/komunikasi/persuratan/aktif', roles: ['tata_usaha'] },
        {
          title: 'Surat Dispensasi',
          url: '/komunikasi/persuratan/dispensasi',
          roles: ['tata_usaha']
        },
        { title: 'Keterangan Lulus', url: '/komunikasi/persuratan/lulus', roles: ['tata_usaha'] },
        {
          title: 'Peringatan',
          url: '/komunikasi/persuratan/peringatan',
          roles: ['tata_usaha']
        }
      ]
    },
    {
      title: 'Laporan',
      url: '/laporan',
      icon: FileBarChart,
      excludeRoles: ['siswa'],
      items: [
        { title: 'Akademik', url: '/laporan/akademik', excludeRoles: ['tata_usaha', 'superadmin', 'admin_yayasan', 'kepala_sekolah', 'admin_sekolah', 'guru', 'wali_kelas', 'orang_tua'] },
        { 
          title: 'Akademik', 
          url: '/laporan/akademik',
          roles: ['tata_usaha']
        },
        { 
          title: 'Perkelas', 
          url: '/laporan/akademik',
          roles: ['kepala_sekolah', 'admin_sekolah']
        },
        { title: 'Keuangan', url: '/laporan/keuangan', excludeRoles: ['tata_usaha', 'superadmin', 'admin_yayasan', 'kepala_sekolah', 'admin_sekolah', 'guru', 'wali_kelas', 'orang_tua'] },
        { 
          title: 'Keuangan', 
          url: '/laporan/keuangan',
          roles: ['tata_usaha']
        },
        { 
          title: 'Keuangan', 
          url: '/laporan/keuangan',
          roles: ['kepala_sekolah', 'admin_sekolah']
        },
        { title: 'Absensi', url: '/laporan/absensi', excludeRoles: ['tata_usaha', 'superadmin', 'admin_yayasan', 'kepala_sekolah', 'admin_sekolah', 'guru', 'wali_kelas', 'orang_tua'] },
        { 
          title: 'Absensi', 
          url: '/laporan/absensi',
          roles: ['tata_usaha']
        },
        { title: 'Ekspor Data', url: '/laporan/ekspor', excludeRoles: ['tata_usaha', 'superadmin', 'admin_yayasan', 'kepala_sekolah', 'admin_sekolah', 'guru', 'wali_kelas', 'orang_tua'] },
        {
          title: 'Laporan Nilai',
          url: '/laporan/nilai',
          roles: ['guru', 'wali_kelas']
        },
        {
          title: 'Kehadiran',
          url: '/laporan/kehadiran-siswa',
          roles: ['guru', 'wali_kelas']
        },
        {
          title: 'Raport Siswa',
          url: '/laporan/raport',
          roles: ['wali_kelas']
        },
        {
          title: 'Perkembangan',
          url: '/laporan/perkembangan',
          roles: ['orang_tua']
        },
        {
          title: 'Yayasan',
          url: '/laporan/konsolidasi',
          roles: ['superadmin']
        },
        {
          title: 'Data siswa',
          url: '/laporan/siswa-yayasan',
          roles: ['admin_yayasan']
        },
        {
          title: 'Keuangan',
          url: '/laporan/keuangan-yayasan',
          roles: ['admin_yayasan']
        },
        {
          title: 'Akademik',
          url: '/laporan/akademik-yayasan',
          roles: ['admin_yayasan']
        },
        {
          title: 'SDM',
          url: '/laporan/sdm-yayasan',
          roles: ['admin_yayasan']
        },
        {
          title: 'Infrastruktur',
          url: '/laporan/infrastruktur-yayasan',
          roles: ['admin_yayasan']
        },
        {
          title: 'Kepegawaian',
          url: '/laporan/kepegawaian',
          roles: ['kepala_sekolah', 'admin_sekolah']
        },
        {
          title: 'LPJ',
          url: '/laporan/pertanggung-jawaban',
          roles: ['kepala_sekolah']
        }
      ]
    },
    {
      title: 'Lainnya',
      url: '/lainnya',
      icon: MoreHorizontal,
      excludeRoles: ['tata_usaha', 'guru', 'wali_kelas', 'siswa', 'orang_tua'],
      items: [
        { title: 'Pengaturan Sekolah', url: '/lainnya/pengaturan', excludeRoles: ['superadmin', 'admin_yayasan', 'kepala_sekolah', 'admin_sekolah'] },
        { title: 'Manajemen Pengguna', url: '/lainnya/pengguna', excludeRoles: ['superadmin', 'admin_yayasan', 'kepala_sekolah', 'admin_sekolah'] },
        { title: 'Backup & Restore', url: '/lainnya/backup', excludeRoles: ['superadmin', 'admin_yayasan', 'kepala_sekolah', 'admin_sekolah'] },
        { title: 'Ruangan', url: '/lainnya/ruangan', excludeRoles: ['superadmin', 'admin_yayasan', 'kepala_sekolah'] },
        { title: 'Aset', url: '/lainnya/aset', excludeRoles: ['superadmin', 'admin_yayasan', 'kepala_sekolah'] },
        { title: 'Perpustakaan', url: '/lainnya/perpustakaan', excludeRoles: ['superadmin', 'admin_yayasan', 'kepala_sekolah'] },
        {
          title: 'Konfigurasi Global',
          url: '/lainnya/konfigurasi-global',
          roles: ['superadmin']
        },
        {
          title: 'Ruangan Sekolah',
          url: '/lainnya/informasi-ruangan',
          roles: ['admin_yayasan', 'kepala_sekolah']
        },
        {
          title: 'Aset Sekolah',
          url: '/lainnya/informasi-aset',
          roles: ['admin_yayasan', 'kepala_sekolah']
        },
        {
          title: 'Perpustakaan',
          url: '/lainnya/informasi-perpustakaan',
          roles: ['admin_yayasan', 'kepala_sekolah']
        },        
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
