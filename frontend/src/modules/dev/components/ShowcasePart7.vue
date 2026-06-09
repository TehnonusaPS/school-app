<script setup>
import { ref } from 'vue'
import {
  Wallet, Users, LayoutGrid, School, UserCheck, CalendarDays,
  Activity, MessageCircle, Plus, Download, BookOpen, Bell,
  Pencil, ClipboardList, Image, AlignLeft, ListFilter, Table2
} from 'lucide-vue-next'

// Stat Card
import StatCard from '@/components/stat-card/StatCard.vue'

// Dashboard Widgets
import WidgetCard from '@/components/dashboard-widget/WidgetCard.vue'
import WidgetList from '@/components/dashboard-widget/WidgetList.vue'
import WidgetProgressList from '@/components/dashboard-widget/WidgetProgressList.vue'

// Data Table
import DataTableCard from '@/components/data-table/DataTableCard.vue'

// Forms
import FormSection from '@/components/forms/FormSection.vue'
import FormInput from '@/components/forms/FormInput.vue'
import FormSelect from '@/components/forms/FormSelect.vue'
import FormTextArea from '@/components/forms/FormTextArea.vue'
import FormDate from '@/components/forms/FormDate.vue'
import ImageUpload from '@/components/forms/ImageUpload.vue'



// Activity Card
import ActivityCard from '@/components/activity-card/ActivityCard.vue'

import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { usePagination } from '@/composables/usePagination'
import { computed } from 'vue'

const props = defineProps({
  searchQuery: { type: String, default: '' }
})

const match = keywords => {
  if (!props.searchQuery) return true
  return keywords.toLowerCase().includes(props.searchQuery.toLowerCase())
}

// ── Stat Card data ─────────────────────────────────────────────
const dummyListItems = [
  { id: 1, name: 'Bapak Udin', info: 'Masuk tepat waktu', time: '07:05' },
  { id: 2, name: 'Ibu Sari', info: 'Masuk tepat waktu', time: '07:12' },
  { id: 3, name: 'Bapak Supri', info: 'Terlambat 15 menit', time: '07:45' }
]

const dummyProgressItems = [
  { id: 1, label: 'Kelas 10-A', value: 95 },
  { id: 2, label: 'Kelas 11-B', value: 88 },
  { id: 3, label: 'Kelas 12-A', value: 92 }
]

// ── Data Table data ─────────────────────────────────────────────
const tableColumns = [
  { key: 'nama', label: 'Nama Siswa' },
  { key: 'kelas', label: 'Kelas' },
  { key: 'nisn', label: 'NISN' },
  { key: 'status', label: 'Status' },
  { key: 'actions', label: 'Aksi' }
]

const tableFilters = [
  { type: 'search', key: 'search', placeholder: 'Cari nama siswa...' },
  {
    type: 'select', key: 'kelas', label: 'Kelas:',
    placeholder: 'Semua Kelas',
    options: [
      { label: 'Kelas 10', value: '10' },
      { label: 'Kelas 11', value: '11' },
      { label: 'Kelas 12', value: '12' }
    ]
  }
]

const tableActions = [
  { label: 'Tambah Siswa', icon: Plus },
  { label: 'Export', icon: Download, variant: 'outline' }
]

const tableRawItems = computed(() => [
  { id: 1, nama: 'Ahmad Fauzi', kelas: '10-A', nisn: '0012345678', status: 'Aktif' },
  { id: 2, nama: 'Siti Rahayu', kelas: '11-B', nisn: '0023456789', status: 'Aktif' },
  { id: 3, nama: 'Budi Santoso', kelas: '12-A', nisn: '0034567890', status: 'Nonaktif' },
  { id: 4, nama: 'Dewi Lestari', kelas: '10-C', nisn: '0045678901', status: 'Aktif' },
  { id: 5, nama: 'Riko Prasetyo', kelas: '11-A', nisn: '0056789012', status: 'Aktif' },
])

const { currentPage, total, from, to, paginatedItems } = usePagination(tableRawItems)

// ── Form data ──────────────────────────────────────────────────
const formName = ref('')
const formKelas = ref('')
const formTanggal = ref('')
const formCatatan = ref('')
const formImagePreview = ref('')
const formErrors = ref({ name: '', kelas: '' })

const kelasOptions = [
  { label: 'Kelas 10-A', value: '10a' },
  { label: 'Kelas 10-B', value: '10b' },
  { label: 'Kelas 11-A', value: '11a' },
  { label: 'Kelas 11-B', value: '11b' },
]

const handleImageChange = (file) => {
  formImagePreview.value = URL.createObjectURL(file)
}
</script>

<template>
  <div class="space-y-12">

    <!-- 61. STAT CARD -->
    <section v-show="match('stat card statistik dashboard progress trend')" class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">61. Stat Card</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Kartu statistik responsif dengan desain Glassmorphism. Tersedia berbagai varian warna, indikator trend, dan progress bar.
        </p>
      </div>
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 p-6 border rounded-xl bg-card">
        <StatCard label="Total Siswa" value="402" sub="siswa aktif" trend="+12 bulan ini" trendDirection="up" :icon="Users" illustration="graduation_cap" variant="blue" />
        <StatCard label="Total Kelas" value="18" sub="rombel aktif" trend="Semester Ganjil" trendDirection="neutral" :icon="LayoutGrid" illustration="abc_board" variant="violet" />
        <StatCard label="Absensi Hari Ini" value="378" sub="94% hadir" trend="+2% dari kemarin" trendDirection="up" :icon="UserCheck" illustration="school_bell" variant="emerald" />
        <StatCard label="SPP Terkumpul" value="82.2%" sub="Target Rp 2,4M" trend="+5.1% bulan ini" trendDirection="up" :icon="Wallet" illustration="bag" variant="amber" />
        <StatCard label="Alpa Hari Ini" value="0" sub="tidak hadir tanpa ket." trendDirection="neutral" illustration="star" variant="primary" />
        <StatCard label="Total Anggaran" value="Rp 1,2M" sub="Realisasi Anggaran" :progress="78.5" :icon="Wallet" illustration="open_book" variant="emerald" />
      </div>
    </section>

    <!-- 62. WIDGET CARD -->
    <section v-show="match('widget card container dashboard wrapper')" class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">62. Widget Card</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Container generik untuk widget dashboard. Memiliki title, description, icon, dan slot untuk header action, konten, dan footer.
        </p>
      </div>
      <div class="grid gap-4 sm:grid-cols-2 p-6 border rounded-xl bg-card">
        <WidgetCard title="Overview Akademik" description="Ringkasan aktivitas hari ini" :icon="Activity">
          <template #header-action>
            <Badge variant="outline">Hari Ini</Badge>
          </template>
          <div class="h-[120px] flex items-center justify-center text-sm text-muted-foreground bg-muted/20 rounded-lg border border-dashed">
            Konten Custom Widget
          </div>
          <template #footer>
            <Button variant="ghost" class="w-full text-xs h-8">Lihat Detail Lengkap</Button>
          </template>
        </WidgetCard>

        <WidgetCard title="Notifikasi Sekolah" description="Pesan dan pengumuman terbaru" :icon="Bell">
          <template #header-action>
            <Badge>3 Baru</Badge>
          </template>
          <div class="h-[120px] flex items-center justify-center text-sm text-muted-foreground bg-muted/20 rounded-lg border border-dashed">
            Slot Konten Fleksibel
          </div>
        </WidgetCard>
      </div>
    </section>

    <!-- 63. WIDGET LIST -->
    <section v-show="match('widget list daftar scrollable agenda pesan aktivitas')" class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">63. Widget List</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Widget daftar item dengan area scroll. Menggunakan scoped slot <code class="bg-muted px-1 rounded text-xs">#item</code> untuk custom layout setiap baris.
        </p>
      </div>
      <div class="grid gap-4 sm:grid-cols-2 p-6 border rounded-xl bg-card">
        <WidgetList title="Log Absensi" description="Rekap kehadiran hari ini" :icon="MessageCircle" :items="dummyListItems" listClass="h-[180px] px-4">
          <template #item="{ item }">
            <div class="size-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
              <UserCheck class="size-4 text-primary" />
            </div>
            <div class="min-w-0 flex-1 ml-2">
              <p class="text-sm font-semibold truncate">{{ item.name }}</p>
              <p class="text-xs text-muted-foreground mt-0.5">{{ item.info }}</p>
            </div>
            <div class="text-xs font-medium tabular-nums">{{ item.time }}</div>
          </template>
        </WidgetList>
      </div>
    </section>

    <!-- 64. WIDGET PROGRESS LIST -->
    <section v-show="match('widget progress list bar ranking indikator persentase')" class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">64. Widget Progress List</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Widget daftar dengan progress bar per item. Cocok untuk ranking, tingkat kehadiran, atau penggunaan kapasitas.
        </p>
      </div>
      <div class="grid gap-4 sm:grid-cols-2 p-6 border rounded-xl bg-card">
        <WidgetProgressList title="Kehadiran Per Kelas" description="Persentase bulan ini" :icon="Users" :items="dummyProgressItems" listClass="h-[180px] px-4">
          <template #value="{ item }">
            <span class="text-xs font-bold tabular-nums">{{ item.value }}%</span>
          </template>
        </WidgetProgressList>
      </div>
    </section>

    <!-- 65. DATA TABLE CARD -->
    <section v-show="match('data table tabel kartu toolbar pagination filter aksi')" class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">65. Data Table Card</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Komponen tabel data lengkap dengan toolbar (search & filter), konten tabel, dan pagination. Terdiri dari <code class="bg-muted px-1 rounded text-xs">DataTableCard</code>, <code class="bg-muted px-1 rounded text-xs">DataTableToolbar</code>, <code class="bg-muted px-1 rounded text-xs">BaseDataTable</code>, dan <code class="bg-muted px-1 rounded text-xs">DataTablePagination</code>.
        </p>
      </div>

      <DataTableCard
        :columns="tableColumns"
        :items="paginatedItems"
        :filters="tableFilters"
        :actions="tableActions"
        :from="from"
        :to="to"
        :total="total"
        :page="currentPage"
      >
        <template #cell-status="{ value }">
          <Badge :variant="value === 'Aktif' ? 'default' : 'secondary'">
            {{ value }}
          </Badge>
        </template>
        <template #cell-actions>
          <div class="flex items-center gap-2 text-muted-foreground">
            <button class="hover:text-foreground"><Pencil class="w-4 h-4" /></button>
          </div>
        </template>
      </DataTableCard>
    </section>



    <!-- 67. FORM COMPONENTS -->
    <section v-show="match('form input select textarea tanggal date upload gambar section komponen')" class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">67. Form Components</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Kumpulan komponen form standar yang reusable: <code class="bg-muted px-1 rounded text-xs">FormInput</code>, <code class="bg-muted px-1 rounded text-xs">FormSelect</code>, <code class="bg-muted px-1 rounded text-xs">FormTextArea</code>, <code class="bg-muted px-1 rounded text-xs">FormDate</code>, <code class="bg-muted px-1 rounded text-xs">FormSection</code>, dan <code class="bg-muted px-1 rounded text-xs">ImageUpload</code>.
        </p>
      </div>

      <div class="grid gap-6 lg:grid-cols-2">
        <!-- FormInput -->
        <FormSection title="FormInput" description="Field teks tunggal dengan label, placeholder, dan validasi error." :icon="Pencil">
          <FormInput label="Nama Lengkap" placeholder="Masukkan nama lengkap" v-model="formName" required />
          <FormInput label="Email" placeholder="email@sekolah.ac.id" type="email" />
          <FormInput label="Field dengan Error" placeholder="Isi field ini" error="Nama wajib diisi." />
        </FormSection>

        <!-- FormSelect -->
        <FormSection title="FormSelect" description="Dropdown pilihan dengan label dan opsi dinamis." :icon="ListFilter">
          <FormSelect label="Kelas" placeholder="Pilih kelas" v-model="formKelas" :options="kelasOptions" required />
          <FormSelect label="Status" placeholder="Pilih status" :options="[{label: 'Aktif', value: 'aktif'}, {label: 'Nonaktif', value: 'nonaktif'}]" />
          <FormSelect label="Select dengan Error" placeholder="Pilih opsi" :options="kelasOptions" error="Kelas wajib dipilih." />
        </FormSection>

        <!-- FormTextArea -->
        <FormSection title="FormTextArea" description="Area teks multi-baris untuk keterangan atau catatan." :icon="AlignLeft">
          <FormTextArea label="Catatan Siswa" placeholder="Tulis catatan di sini..." v-model="formCatatan" />
          <FormTextArea label="Catatan dengan Error" placeholder="Tulis catatan..." error="Catatan tidak boleh kosong." />
        </FormSection>

        <!-- FormDate -->
        <FormSection title="FormDate" description="Input tanggal dengan format standar." :icon="CalendarDays">
          <FormDate label="Tanggal Lahir" v-model="formTanggal" required />
          <FormDate label="Tanggal Masuk" />
          <FormDate label="Tanggal dengan Error" error="Tanggal wajib diisi." />
        </FormSection>

        <!-- ImageUpload -->
        <FormSection title="ImageUpload" description="Komponen upload gambar dengan preview, validasi error, dan catatan." :icon="Image">
          <ImageUpload
            label="Foto Profil Siswa"
            note="Format yang didukung: JPG, PNG, WEBP. Ukuran maksimal 2MB."
            :preview="formImagePreview"
            @change="handleImageChange"
          />
        </FormSection>

        <!-- FormSection standalone -->
        <FormSection title="FormSection" description="Container section form dengan icon, judul, dan deskripsi. Dapat menampung field apapun." :icon="ClipboardList">
          <div class="text-sm text-muted-foreground bg-muted/30 rounded-lg border border-dashed p-8 text-center">
            Slot konten FormSection — bisa diisi komponen form apapun
          </div>
        </FormSection>
      </div>

      <!-- Contoh Form Lengkap -->
      <div class="space-y-2">
        <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Contoh Form Lengkap</p>
        <div class="p-6 border rounded-xl bg-card space-y-6">
          <PageHeader variant="form" title="Tambah Data Siswa" description="Lengkapi semua informasi siswa di bawah ini." />
          <div class="grid gap-6 lg:grid-cols-2">
            <FormSection title="Informasi Pribadi" :icon="Users">
              <FormInput label="Nama Lengkap" placeholder="Masukkan nama lengkap" required />
              <FormInput label="NISN" placeholder="0012345678" />
              <FormDate label="Tanggal Lahir" required />
            </FormSection>
            <FormSection title="Informasi Akademik" :icon="BookOpen">
              <FormSelect label="Kelas" placeholder="Pilih kelas" :options="kelasOptions" required />
              <FormSelect label="Status" placeholder="Pilih status" :options="[{label: 'Aktif', value: 'aktif'}, {label: 'Nonaktif', value: 'nonaktif'}]" />
              <FormTextArea label="Catatan Tambahan" placeholder="Keterangan tambahan..." />
            </FormSection>
          </div>
        </div>
      </div>
    </section>

    <!-- 68. ACTIVITY CARD -->
    <section v-show="match('activity card aktivitas agenda jadwal icon tanggal notifikasi')" class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">68. Activity Card</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Kartu item aktivitas/agenda dengan dua mode: <strong>mode tanggal</strong> (lingkaran dengan tanggal & bulan) dan <strong>mode icon</strong>. Tersedia 4 variant warna: <code class="bg-muted px-1 rounded text-xs">green</code>, <code class="bg-muted px-1 rounded text-xs">blue</code>, <code class="bg-muted px-1 rounded text-xs">purple</code>, <code class="bg-muted px-1 rounded text-xs">yellow</code>.
        </p>
      </div>

      <div class="space-y-6 p-6 border rounded-xl bg-card">
        <!-- Mode Tanggal -->
        <div class="space-y-2">
          <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Mode Tanggal</p>
          <div class="space-y-2">
            <ActivityCard date="14" month="Jun" title="Rapat Komite Sekolah" description="Aula Utama, pukul 09.00 WIB" variant="green" :trailingIcon="CalendarDays" />
            <ActivityCard date="18" month="Jun" title="Ujian Tengah Semester" description="Seluruh kelas, pukul 07.30 WIB" variant="blue" :trailingIcon="CalendarDays" />
            <ActivityCard date="25" month="Jun" title="Pengumuman Hasil UTS" description="Publikasi melalui aplikasi" variant="purple" :trailingIcon="Bell" />
            <ActivityCard date="30" month="Jun" title="Pembagian Raport" description="Pertemuan orang tua wajib hadir" variant="yellow" :trailingIcon="School" />
          </div>
        </div>

        <!-- Mode Icon -->
        <div class="space-y-2">
          <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Mode Icon</p>
          <div class="space-y-2">
            <ActivityCard :leadingIcon="UserCheck" title="Ahmad Fauzi hadir tepat waktu" description="07:05 WIB — Kelas 10-A" variant="green" />
            <ActivityCard :leadingIcon="BookOpen" title="Materi Bab 5 diunggah" description="Matematika — Bpk. Rudi Hartono" variant="blue" />
            <ActivityCard :leadingIcon="Wallet" title="SPP bulan Juni lunas" description="Siti Rahayu — Rp 350.000" variant="purple" />
            <ActivityCard :leadingIcon="Bell" title="Peringatan: 3 siswa terlambat" description="Kelas 11-B — hari ini" variant="yellow" />
          </div>
        </div>
      </div>
    </section>

  </div>
</template>
