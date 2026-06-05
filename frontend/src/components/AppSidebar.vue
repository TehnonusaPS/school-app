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
          title: 'Kelola Hak Akses Pengguna', 
          url: '/manajemen-data/hak-akses',
          roles: ['superadmin']
        },
        { 
          title: 'Guru dan Staff Yayasan', 
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
        { title: 'Data Guru dan Staff', url: '/manajemen-data/guru-staff', excludeRoles: ['tata_usaha', 'superadmin', 'admin_yayasan', 'wali_kelas'] },
        { title: 'Data Kelas', url: '/manajemen-data/kelas', excludeRoles: ['tata_usaha', 'superadmin', 'admin_yayasan', 'kepala_sekolah', 'wali_kelas'] },
        { title: 'Mata Pelajaran', url: '/manajemen-data/mata-pelajaran', excludeRoles: ['tata_usaha', 'superadmin', 'admin_yayasan', 'wali_kelas'] },
        { 
          title: 'Tahun Ajaran Bedasarkan Siswa/Kelas', 
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
        { title: 'Mata Pelajaran', url: '/akademik/mapel', excludeRoles: ['kepala_sekolah', 'admin_sekolah', 'guru', 'wali_kelas', 'siswa', 'orang_tua'] },
        { title: 'Ekstrakurikuler', url: '/akademik/ekskul', excludeRoles: ['kepala_sekolah', 'admin_sekolah', 'guru', 'wali_kelas', 'siswa', 'orang_tua'] },
        {
          title: 'Kalender Akademik',
          url: '/akademik/kalender',
          roles: ['kepala_sekolah', 'admin_sekolah']
        },
        {
          title: 'Kegiatan akademik sekolah',
          url: '/akademik/kegiatan',
          roles: ['kepala_sekolah', 'admin_sekolah']
        },
        {
          title: 'Kelola Materi',
          url: '/akademik/materi',
          roles: ['guru', 'wali_kelas']
        },
        {
          title: 'Kelola Tugas',
          url: '/akademik/tugas',
          roles: ['guru', 'wali_kelas']
        },
        {
          title: 'Input Nilai',
          url: '/akademik/input-nilai',
          roles: ['guru', 'wali_kelas']
        },
        {
          title: 'Nilai dan Raport',
          url: '/akademik/nilai',
          roles: ['siswa']
        },
        {
          title: 'Materi Pembelajaran',
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
          title: 'Nilai dan Raport Anak',
          url: '/akademik/nilai',
          roles: ['orang_tua']
        },
        {
          title: 'Jadwal Pelajaran Anak',
          url: '/akademik/jadwal',
          roles: ['orang_tua']
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
          title: 'Daftar Siswa yang Blom Bayar SPP', 
          url: '/keuangan/spp',
          roles: ['tata_usaha']
        },
        { title: 'Tagihan & Pembayaran', url: '/keuangan/tagihan', excludeRoles: ['tata_usaha', 'superadmin', 'admin_yayasan', 'admin_sekolah', 'siswa', 'orang_tua'] },
        { 
          title: 'History Transaksi Keuangan', 
          url: '/keuangan/tagihan',
          roles: ['tata_usaha']
        },
        { title: 'Pengeluaran', url: '/keuangan/pengeluaran', excludeRoles: ['tata_usaha', 'superadmin', 'admin_yayasan', 'admin_sekolah', 'siswa', 'orang_tua'] },
        { title: 'Laporan Keuangan', url: '/keuangan/laporan', excludeRoles: ['tata_usaha', 'superadmin', 'admin_yayasan', 'admin_sekolah', 'siswa', 'orang_tua'] },
        {
          title: 'Monitoring Subscribtion',
          url: '/keuangan/subscription',
          roles: ['superadmin']
        },
        {
          title: 'Halaman Monitoring Keuangan Yayasan',
          url: '/keuangan/monitoring-yayasan',
          roles: ['admin_yayasan']
        },
        {
          title: 'Halaman Menu Paket Subcription',
          url: '/keuangan/paket-subscription',
          roles: ['admin_yayasan']
        },
        {
          title: 'Mengelola Dana Yayasan',
          url: '/keuangan/kelola-dana-yayasan',
          roles: ['admin_sekolah']
        },
        {
          title: 'Pembayaran SPP',
          url: '/keuangan/spp',
          roles: ['siswa']
        },
        {
          title: 'Pembayaran SPP',
          url: '/keuangan/spp',
          roles: ['orang_tua']
        },
        {
          title: 'Riwayat Pembayaran SPP',
          url: '/keuangan/tagihan',
          roles: ['orang_tua']
        }
      ]
    },
    {
      title: 'Absensi',
      url: '/absensi',
      icon: ClipboardList,
      excludeRoles: ['superadmin', 'admin_yayasan', 'kepala_sekolah', 'admin_sekolah', 'tata_usaha', 'orang_tua'],
      items: [
        { title: 'Absensi Siswa', url: '/absensi/siswa', excludeRoles: ['guru', 'wali_kelas', 'siswa'] },
        { title: 'Absensi Guru & Staff', url: '/absensi/guru-staff', excludeRoles: ['guru', 'wali_kelas', 'siswa'] },
        { title: 'Rekap Bulanan', url: '/absensi/rekap', excludeRoles: ['guru', 'wali_kelas', 'siswa'] },
        {
          title: 'Absensi',
          url: '/absensi/siswa',
          roles: ['guru', 'wali_kelas']
        },
        {
          title: 'Input Kehadiran Siswa',
          url: '/absensi/input',
          roles: ['guru', 'wali_kelas']
        },
        {
          title: 'Rekap Absensi',
          url: '/absensi/rekap',
          roles: ['siswa']
        }
      ]
    },
    {
      title: 'Komunikasi',
      url: '/komunikasi',
      icon: MessageSquare,
      excludeRoles: ['superadmin', 'guru', 'wali_kelas', 'siswa', 'orang_tua'],
      items: [
        { title: 'Pengumuman', url: '/komunikasi/pengumuman', excludeRoles: ['tata_usaha', 'kepala_sekolah', 'admin_sekolah'] },
        { title: 'Berita Kegiatan', url: '/komunikasi/berita-kegiatan', excludeRoles: ['tata_usaha', 'admin_yayasan'] },
        { title: 'Feedback Orang Tua', url: '/komunikasi/feedback', excludeRoles: ['tata_usaha', 'admin_yayasan'] },
        { title: 'Pesan Internal', url: '/komunikasi/pesan', excludeRoles: ['tata_usaha', 'admin_yayasan', 'kepala_sekolah', 'admin_sekolah'] },
        { title: 'Notifikasi', url: '/komunikasi/notifikasi', excludeRoles: ['tata_usaha', 'admin_yayasan', 'kepala_sekolah', 'admin_sekolah'] },
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
      excludeRoles: ['siswa'],
      items: [
        { title: 'Laporan Akademik', url: '/laporan/akademik', excludeRoles: ['tata_usaha', 'superadmin', 'admin_yayasan', 'kepala_sekolah', 'admin_sekolah', 'guru', 'wali_kelas', 'orang_tua'] },
        { 
          title: 'Laporan Akademik Perkelas', 
          url: '/laporan/akademik',
          roles: ['tata_usaha']
        },
        { 
          title: 'Akademik Perkelas', 
          url: '/laporan/akademik',
          roles: ['kepala_sekolah', 'admin_sekolah']
        },
        { title: 'Laporan Keuangan', url: '/laporan/keuangan', excludeRoles: ['tata_usaha', 'superadmin', 'admin_yayasan', 'kepala_sekolah', 'admin_sekolah', 'guru', 'wali_kelas', 'orang_tua'] },
        { 
          title: 'Laporan Keuangan SPP dan Pendapatan Sekolah', 
          url: '/laporan/keuangan',
          roles: ['tata_usaha']
        },
        { 
          title: 'Keuangan SPP dan Pendapatan Sekolah', 
          url: '/laporan/keuangan',
          roles: ['kepala_sekolah', 'admin_sekolah']
        },
        { title: 'Laporan Absensi', url: '/laporan/absensi', excludeRoles: ['tata_usaha', 'superadmin', 'admin_yayasan', 'kepala_sekolah', 'admin_sekolah', 'guru', 'wali_kelas', 'orang_tua'] },
        { 
          title: 'Laporan Absensi Kepegawaian', 
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
          title: 'Laporan Kehadiran Siswa',
          url: '/laporan/kehadiran-siswa',
          roles: ['guru', 'wali_kelas']
        },
        {
          title: 'Laporan Raport Siswa',
          url: '/laporan/raport',
          roles: ['wali_kelas']
        },
        {
          title: 'Perkembangan Anak',
          url: '/laporan/perkembangan',
          roles: ['orang_tua']
        },
        {
          title: 'Mengakses laporan konsolidasi seluruh yayasan',
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
          title: 'Laporan pertanggung Jawaban',
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
        { title: 'Manajemen Ruangan', url: '/lainnya/ruangan', excludeRoles: ['superadmin', 'admin_yayasan', 'kepala_sekolah'] },
        { title: 'Manajemen Aset', url: '/lainnya/aset', excludeRoles: ['superadmin', 'admin_yayasan', 'kepala_sekolah'] },
        { title: 'Manajemen Perpustakaan', url: '/lainnya/perpustakaan', excludeRoles: ['superadmin', 'admin_yayasan', 'kepala_sekolah'] },
        {
          title: 'Konfigurasi sistem global',
          url: '/lainnya/konfigurasi-global',
          roles: ['superadmin']
        },
        {
          title: 'Informasi Ruangan Sekolah',
          url: '/lainnya/informasi-ruangan',
          roles: ['admin_yayasan', 'kepala_sekolah']
        },
        {
          title: 'Informasi Aset Sekolah',
          url: '/lainnya/informasi-aset',
          roles: ['admin_yayasan', 'kepala_sekolah']
        },
        {
          title: 'Informasi Perpustakaan Sekolah',
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
