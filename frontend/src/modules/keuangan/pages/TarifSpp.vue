<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import { toast } from 'vue-sonner'
import {
  CalendarDays,
  Banknote,
  UserCheck,
  SlidersHorizontal,
  Download,
  Pencil,
  Trash2,
  Save,
  PlusCircle,
  Search,
  X,
  Plus,
  RefreshCw
} from 'lucide-vue-next'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Badge } from '@/components/ui/badge'
import { Input } from '@/components/ui/input'
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue
} from '@/components/ui/select'

const auth = useAuthStore()

// State
const searchQuery = ref('')
const selectedProgramFilter = ref('semua')
const showFilters = ref(false)

const tariffs = ref([
  {
    id: 1,
    angkatan: '2024',
    tipe: 'SPP Bulanan',
    lingkup: 'Kelas X (Semua)',
    kategori: 'Bulanan',
    nominal: 1250000,
    kategoriProgram: 'Reguler',
    grade: 'X (Sepuluh)',
    lingkupOnly: 'Semua'
  },
  {
    id: 2,
    angkatan: '2024',
    tipe: 'Uang Pangkal',
    lingkup: 'Kelas X (Baru)',
    kategori: 'Satu Kali',
    nominal: 2500000,
    kategoriProgram: 'Reguler',
    grade: 'X (Sepuluh)',
    lingkupOnly: 'Baru'
  },
  {
    id: 3,
    angkatan: '2023',
    tipe: 'Uang Kegiatan',
    lingkup: 'Kelas XI (Semua)',
    kategori: 'Semester',
    nominal: 1350000,
    kategoriProgram: 'Reguler',
    grade: 'XI (Sebelas)',
    lingkupOnly: 'Semua'
  },
  {
    id: 4,
    angkatan: '2024',
    tipe: 'Biaya Asrama',
    lingkup: 'Kelas X (Asrama)',
    kategori: 'Bulanan',
    nominal: 3000000,
    kategoriProgram: 'Reguler',
    grade: 'X (Sepuluh)',
    lingkupOnly: 'Asrama'
  }
])

const activities = ref([
  {
    id: 1,
    type: 'add',
    title: 'Tarif baru ditambahkan: Kategori Reguler Kelas X',
    subtitle: '2 jam yang lalu oleh Admin (Andi)',
    time: '2 jam yang lalu'
  },
  {
    id: 2,
    type: 'edit',
    title: 'Perubahan nominal tarif: Kategori Internasional Kelas XI',
    subtitle: 'Kemarin, 14:20 oleh Admin (Sarah)',
    time: 'Kemarin, 14:20'
  }
])

// Form state
const initialForm = {
  id: null,
  tipe: 'SPP Bulanan',
  kategoriProgram: 'Reguler',
  grade: 'X (Sepuluh)',
  kategori: 'Bulanan',
  angkatan: '2024',
  lingkupOnly: 'Semua',
  nominal: ''
}
const form = ref({ ...initialForm })
const isEditing = ref(false)

// Helper function to format IDR currency
const formatCurrency = (value) => {
  if (value === undefined || value === null) return '0'
  return new Intl.NumberFormat('id-ID', {
    minimumFractionDigits: 0
  }).format(value)
}

// Log action to Activity Trail
const logActivity = (type, message) => {
  const currentAdmin = auth.user?.name || 'Admin (Andi)'
  activities.value.unshift({
    id: Date.now(),
    type,
    title: message,
    subtitle: `Baru saja oleh ${currentAdmin}`,
    time: 'Baru saja'
  })
}

// Handle Form Submit
const handleSave = () => {
  if (!form.value.nominal || Number(form.value.nominal) <= 0) {
    toast.error('Silakan isi nominal tarif dengan benar!')
    return
  }

  const calculatedLingkup = `Kelas ${form.value.grade.split(' ')[0]} (${form.value.lingkupOnly})`

  if (isEditing.value) {
    // Edit Mode
    const index = tariffs.value.findIndex((t) => t.id === form.value.id)
    if (index !== -1) {
      tariffs.value[index] = {
        ...tariffs.value[index],
        tipe: form.value.tipe,
        kategoriProgram: form.value.kategoriProgram,
        grade: form.value.grade,
        kategori: form.value.kategori,
        angkatan: form.value.angkatan,
        lingkupOnly: form.value.lingkupOnly,
        lingkup: calculatedLingkup,
        nominal: Number(form.value.nominal)
      }
      logActivity(
        'edit',
        `Perubahan nominal tarif: Kategori ${form.value.kategoriProgram} Kelas ${form.value.grade.split(' ')[0]}`
      )
      toast.success('Tarif SPP berhasil diperbarui!')
      cancelEdit()
    }
  } else {
    // Create Mode
    const newTarif = {
      id: Date.now(),
      angkatan: form.value.angkatan,
      tipe: form.value.tipe,
      lingkup: calculatedLingkup,
      kategori: form.value.kategori,
      nominal: Number(form.value.nominal),
      kategoriProgram: form.value.kategoriProgram,
      grade: form.value.grade,
      lingkupOnly: form.value.lingkupOnly
    }
    tariffs.value.unshift(newTarif)
    logActivity(
      'add',
      `Tarif baru ditambahkan: Kategori ${form.value.kategoriProgram} Kelas ${form.value.grade.split(' ')[0]}`
    )
    toast.success('Tarif SPP baru berhasil disimpan!')
    resetForm()
  }
}

// Trigger Edit Mode
const editTarif = (item) => {
  form.value = {
    id: item.id,
    tipe: item.tipe,
    kategoriProgram: item.kategoriProgram,
    grade: item.grade,
    kategori: item.kategori,
    angkatan: item.angkatan,
    lingkupOnly: item.lingkupOnly,
    nominal: item.nominal
  }
  isEditing.value = true
}

// Cancel Edit Mode
const cancelEdit = () => {
  resetForm()
}

// Reset Form
const resetForm = () => {
  form.value = { ...initialForm }
  isEditing.value = false
}

// Delete Action
const deleteTarif = (item) => {
  tariffs.value = tariffs.value.filter((t) => t.id !== item.id)
  logActivity(
    'delete',
    `Tarif dihapus: Kategori ${item.kategoriProgram} Kelas ${item.grade.split(' ')[0]}`
  )
  toast.success('Tarif SPP berhasil dihapus!')
}

// Export Trigger
const handleExport = () => {
  toast.success('Data tarif SPP berhasil diekspor ke Excel!')
}

// Computed Filtered Tariffs
const filteredTariffs = computed(() => {
  return tariffs.value.filter((t) => {
    // Search filter
    const matchesSearch =
      t.tipe.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      t.angkatan.includes(searchQuery.value) ||
      t.kategori.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      t.lingkup.toLowerCase().includes(searchQuery.value.toLowerCase())

    // Program Category filter
    const matchesProgram =
      selectedProgramFilter.value === 'semua' ||
      t.kategoriProgram.toLowerCase() === selectedProgramFilter.value.toLowerCase()

    return matchesSearch && matchesProgram
  })
})
</script>

<template>
  <div class="space-y-6 w-full mx-auto px-0 text-left">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h1 class="text-3xl font-bold tracking-tight text-foreground my-0">Pengaturan Tarif SPP</h1>
        <p class="text-sm text-muted-foreground mt-1">Unit Sekolah: SMA Nusantara</p>
      </div>
    </div>

    <!-- Top Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Tahun Ajaran Aktif -->
      <Card class="shadow-sm border-border p-5 flex flex-col justify-between">
        <div class="flex justify-between items-start mb-4">
          <p class="text-[11px] font-bold text-muted-foreground uppercase tracking-widest">Tahun Ajaran Aktif</p>
          <div class="w-10 h-10 rounded-lg bg-secondary flex items-center justify-center">
            <CalendarDays class="w-5 h-5 text-foreground" />
          </div>
        </div>
        <div>
          <p class="text-2xl lg:text-3xl font-extrabold tracking-tight text-foreground">2023/2024</p>
        </div>
      </Card>

      <!-- Dana Teralokasi -->
      <Card class="shadow-sm border-border p-5 flex flex-col justify-between">
        <div class="flex justify-between items-start mb-4">
          <p class="text-[11px] font-bold text-muted-foreground uppercase tracking-widest">Dana Teralokasi</p>
          <div class="w-10 h-10 rounded-lg bg-emerald-500/10 dark:bg-emerald-500/20 flex items-center justify-center">
            <Banknote class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
          </div>
        </div>
        <div>
          <p class="text-2xl lg:text-3xl font-extrabold tracking-tight text-emerald-600 dark:text-emerald-400">
            Rp 2.450.000.000
          </p>
        </div>
      </Card>

      <!-- Sisa Kuota Beasiswa -->
      <Card class="shadow-sm border-border p-5 flex flex-col justify-between">
        <div class="flex justify-between items-start mb-4">
          <p class="text-[11px] font-bold text-muted-foreground uppercase tracking-widest">Sisa Kuota Beasiswa</p>
          <div class="w-10 h-10 rounded-lg bg-rose-500/10 dark:bg-rose-500/20 flex items-center justify-center">
            <UserCheck class="w-5 h-5 text-rose-600 dark:text-rose-400" />
          </div>
        </div>
        <div>
          <p class="text-2xl lg:text-3xl font-extrabold tracking-tight text-rose-600 dark:text-rose-400">
            42 Siswa
          </p>
        </div>
      </Card>
    </div>

    <!-- Main Content Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left Column (Daftar Tarif Aktif) -->
      <Card class="lg:col-span-2 shadow-sm border-border flex flex-col h-full">
        <div class="p-5 flex flex-col sm:flex-row justify-between sm:items-center gap-4 border-b border-border">
          <h3 class="font-bold text-lg text-foreground">Daftar Tarif Aktif</h3>
          <div class="flex items-center gap-2">
            <!-- Search Toggle / Input -->
            <div class="relative w-44 sm:w-56">
              <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
              <Input
                type="text"
                v-model="searchQuery"
                placeholder="Cari tarif..."
                class="pl-8 h-9 rounded-md bg-muted/40"
              />
            </div>
            <!-- Filter Button -->
            <Button
              variant="outline"
              size="icon"
              class="h-9 w-9"
              @click="showFilters = !showFilters"
              :class="{ 'bg-secondary': showFilters }"
            >
              <SlidersHorizontal class="w-4 h-4 text-foreground" />
            </Button>
            <!-- Export Button -->
            <Button variant="outline" size="icon" class="h-9 w-9" @click="handleExport">
              <Download class="w-4 h-4 text-foreground" />
            </Button>
          </div>
        </div>

        <!-- Filter Dropdown Drawer/Row -->
        <div v-if="showFilters" class="px-5 py-3 bg-muted/20 border-b border-border flex items-center gap-4">
          <span class="text-xs font-semibold text-muted-foreground">Kategori Program:</span>
          <div class="w-40">
            <Select v-model="selectedProgramFilter">
              <SelectTrigger class="h-8">
                <SelectValue placeholder="Pilih Program" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="semua">Semua Program</SelectItem>
                <SelectItem value="Reguler">Reguler</SelectItem>
                <SelectItem value="Internasional">Internasional</SelectItem>
                <SelectItem value="Bilingual">Bilingual</SelectItem>
                <SelectItem value="Keagamaan">Keagamaan</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </div>

        <!-- Table -->
        <div class="flex-grow overflow-x-auto">
          <Table>
            <TableHeader class="bg-muted/30">
              <TableRow class="hover:bg-transparent">
                <TableHead class="font-bold text-xs uppercase tracking-wider text-muted-foreground py-3">Angkatan</TableHead>
                <TableHead class="font-bold text-xs uppercase tracking-wider text-muted-foreground py-3">Tipe & Lingkup</TableHead>
                <TableHead class="font-bold text-xs uppercase tracking-wider text-muted-foreground py-3">Kategori</TableHead>
                <TableHead class="font-bold text-xs uppercase tracking-wider text-muted-foreground py-3">Nominal</TableHead>
                <TableHead class="font-bold text-xs uppercase tracking-wider text-muted-foreground py-3 text-center">Aksi</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-if="filteredTariffs.length === 0">
                <TableCell colspan="5" class="text-center py-8 text-muted-foreground text-sm">
                  Tidak ada data tarif SPP ditemukan.
                </TableCell>
              </TableRow>
              <TableRow v-for="item in filteredTariffs" :key="item.id" class="group hover:bg-muted/10">
                <!-- Angkatan -->
                <TableCell class="py-4 font-semibold text-sm text-foreground">
                  {{ item.angkatan }}
                </TableCell>
                <!-- Tipe & Lingkup -->
                <TableCell class="py-4">
                  <p class="font-bold text-foreground text-sm">{{ item.tipe }}</p>
                  <p class="text-xs text-muted-foreground mt-0.5">{{ item.lingkup }}</p>
                </TableCell>
                <!-- Kategori -->
                <TableCell class="py-4 text-sm text-muted-foreground">
                  {{ item.kategori }}
                </TableCell>
                <!-- Nominal -->
                <TableCell class="py-4">
                  <span class="text-xs text-muted-foreground font-semibold mr-1">Rp</span>
                  <span class="font-extrabold text-foreground">{{ formatCurrency(item.nominal) }}</span>
                </TableCell>
                <!-- Aksi -->
                <TableCell class="py-4">
                  <div class="flex items-center justify-center gap-1">
                    <Button
                      variant="ghost"
                      size="icon"
                      class="h-8 w-8 hover:bg-secondary"
                      @click="editTarif(item)"
                    >
                      <Pencil class="w-4 h-4 text-foreground" />
                    </Button>
                    <Button
                      variant="ghost"
                      size="icon"
                      class="h-8 w-8 text-destructive hover:bg-destructive/10"
                      @click="deleteTarif(item)"
                    >
                      <Trash2 class="w-4 h-4" />
                    </Button>
                  </div>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
      </Card>

      <!-- Right Column (Atur Tarif Baru) -->
      <Card class="shadow-sm border-border h-fit">
        <CardHeader class="pb-4 border-b border-border">
          <CardTitle class="text-lg font-bold flex items-center justify-between">
            <span>{{ isEditing ? 'Edit Tarif' : 'Atur Tarif Baru' }}</span>
            <Button
              v-if="isEditing"
              variant="ghost"
              size="icon"
              class="h-7 w-7 text-muted-foreground"
              @click="cancelEdit"
            >
              <X class="w-4 h-4" />
            </Button>
          </CardTitle>
        </CardHeader>
        <CardContent class="p-5 space-y-4">
          <!-- Kategori Program -->
          <div class="space-y-1.5">
            <label class="text-xs font-bold text-muted-foreground uppercase tracking-wider">Kategori Program</label>
            <Select v-model="form.kategoriProgram">
              <SelectTrigger class="w-full bg-muted/30">
                <SelectValue placeholder="Pilih Kategori Program" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="Reguler">Reguler</SelectItem>
                <SelectItem value="Internasional">Internasional</SelectItem>
                <SelectItem value="Bilingual">Bilingual</SelectItem>
                <SelectItem value="Keagamaan">Keagamaan</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <!-- Grade / Jenjang -->
          <div class="space-y-1.5">
            <label class="text-xs font-bold text-muted-foreground uppercase tracking-wider">Grade / Jenjang</label>
            <Select v-model="form.grade">
              <SelectTrigger class="w-full bg-muted/30">
                <SelectValue placeholder="Pilih Jenjang" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="X (Sepuluh)">X (Sepuluh)</SelectItem>
                <SelectItem value="XI (Sebelas)">XI (Sebelas)</SelectItem>
                <SelectItem value="XII (Dua Belas)">XII (Dua Belas)</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <!-- Tipe Tarif -->
          <div class="space-y-1.5">
            <label class="text-xs font-bold text-muted-foreground uppercase tracking-wider">Tipe Tarif / Nama Biaya</label>
            <Select v-model="form.tipe">
              <SelectTrigger class="w-full bg-muted/30">
                <SelectValue placeholder="Pilih Tipe Tarif" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="SPP Bulanan">SPP Bulanan</SelectItem>
                <SelectItem value="Uang Pangkal">Uang Pangkal</SelectItem>
                <SelectItem value="Uang Kegiatan">Uang Kegiatan</SelectItem>
                <SelectItem value="Biaya Asrama">Biaya Asrama</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <!-- Kategori / Frekuensi & Angkatan -->
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1.5">
              <label class="text-xs font-bold text-muted-foreground uppercase tracking-wider">Kategori</label>
              <Select v-model="form.kategori">
                <SelectTrigger class="w-full bg-muted/30">
                  <SelectValue placeholder="Pilih Kategori" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="Bulanan">Bulanan</SelectItem>
                  <SelectItem value="Semester">Semester</SelectItem>
                  <SelectItem value="Satu Kali">Satu Kali</SelectItem>
                </SelectContent>
              </Select>
            </div>
            <div class="space-y-1.5">
              <label class="text-xs font-bold text-muted-foreground uppercase tracking-wider">Angkatan</label>
              <Select v-model="form.angkatan">
                <SelectTrigger class="w-full bg-muted/30">
                  <SelectValue placeholder="Angkatan" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="2023">2023</SelectItem>
                  <SelectItem value="2024">2024</SelectItem>
                  <SelectItem value="2025">2025</SelectItem>
                  <SelectItem value="2026">2026</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>

          <!-- Lingkup & Nominal -->
          <div class="space-y-4">
            <div class="space-y-1.5">
              <label class="text-xs font-bold text-muted-foreground uppercase tracking-wider">Lingkup</label>
              <Select v-model="form.lingkupOnly">
                <SelectTrigger class="w-full bg-muted/30">
                  <SelectValue placeholder="Pilih Lingkup" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="Semua">Semua Siswa</SelectItem>
                  <SelectItem value="Baru">Siswa Baru</SelectItem>
                  <SelectItem value="Asrama">Siswa Asrama</SelectItem>
                </SelectContent>
              </Select>
            </div>

            <div class="space-y-1.5">
              <label class="text-xs font-bold text-muted-foreground uppercase tracking-wider">Nominal Tarif (Rp)</label>
              <Input
                type="number"
                v-model="form.nominal"
                placeholder="Contoh: 1500000"
                class="w-full h-10 bg-muted/30 border-border rounded-md px-3"
              />
            </div>
          </div>

          <!-- Submit Button -->
          <div class="pt-2 flex flex-col gap-2">
            <Button
              class="w-full bg-foreground text-background hover:bg-foreground/90 font-bold h-11"
              @click="handleSave"
            >
              <Save class="w-4 h-4 mr-2" /> {{ isEditing ? 'Perbarui Tarif' : 'Simpan Tarif' }}
            </Button>
            <Button
              v-if="isEditing"
              variant="outline"
              class="w-full font-bold h-11"
              @click="cancelEdit"
            >
              Batal
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Aktivitas Terakhir (Bottom Card) -->
    <Card class="shadow-sm border-border">
      <CardHeader class="pb-2">
        <CardTitle class="text-base font-bold text-foreground">Aktivitas Terakhir</CardTitle>
      </CardHeader>
      <CardContent class="p-6">
        <div class="space-y-5">
          <div v-for="act in activities" :key="act.id" class="flex items-start gap-4">
            <!-- Icon Indicator -->
            <div
              class="w-9 h-9 rounded-full flex items-center justify-center shrink-0"
              :class="{
                'bg-blue-500/10 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400': act.type === 'add' || act.type === 'edit',
                'bg-rose-500/10 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400': act.type === 'delete'
              }"
            >
              <Plus class="w-5 h-5 font-bold" v-if="act.type === 'add'" />
              <Pencil class="w-4 h-4" v-else-if="act.type === 'edit'" />
              <Trash2 class="w-4 h-4" v-else-if="act.type === 'delete'" />
            </div>
            <!-- Log Text -->
            <div class="space-y-0.5">
              <p class="text-sm font-semibold text-foreground">
                {{ act.title }}
              </p>
              <p class="text-xs text-muted-foreground">
                {{ act.subtitle }}
              </p>
            </div>
          </div>
        </div>
      </CardContent>
    </Card>
  </div>
</template>
