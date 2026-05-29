<script setup>
import { ref, computed } from 'vue'
import {
  Download,
  FileSpreadsheet,
  FileText,
  FileJson,
  CheckCircle2,
  Clock,
  Settings,
  ChevronRight,
} from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
  CardDescription,
} from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Label } from '@/components/ui/label'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { Checkbox } from '@/components/ui/checkbox'
import { Separator } from '@/components/ui/separator'
import { Switch } from '@/components/ui/switch'
import { toast } from 'vue-sonner'

const selectedFormat = ref('excel')
const selectedPeriode = ref('bulan_ini')
const selectedData = ref([])
const isExporting = ref(false)
const includeChart = ref(false)
const includeHeader = ref(true)

const formatList = [
  { id: 'excel', label: 'Excel (.xlsx)', desc: 'Format spreadsheet Microsoft Excel', icon: FileSpreadsheet, color: 'text-green-600', bg: 'bg-green-50 dark:bg-green-950/20 border-green-200 dark:border-green-900/40' },
  { id: 'pdf', label: 'PDF (.pdf)', desc: 'Format dokumen siap cetak', icon: FileText, color: 'text-red-600', bg: 'bg-red-50 dark:bg-red-950/20 border-red-200 dark:border-red-900/40' },
  { id: 'csv', label: 'CSV (.csv)', desc: 'Format data teks terpisah koma', icon: FileJson, color: 'text-blue-600', bg: 'bg-blue-50 dark:bg-blue-950/20 border-blue-200 dark:border-blue-900/40' },
]

const dataOptions = [
  { id: 'absensi', label: 'Data Absensi Siswa', desc: 'Rekap kehadiran per periode' },
  { id: 'nilai', label: 'Data Nilai Siswa', desc: 'Nilai per mata pelajaran' },
  { id: 'kehadiran', label: 'Laporan Kehadiran', desc: 'Persentase kehadiran per siswa' },
  { id: 'keuangan', label: 'Data Keuangan', desc: 'Pemasukan dan pengeluaran' },
  { id: 'kepegawaian', label: 'Data Kepegawaian', desc: 'Data absensi staf & guru' },
  { id: 'raport', label: 'Raport Siswa', desc: 'Nilai akhir semester' },
]

const periodeList = [
  { v: 'bulan_ini', l: 'Bulan Ini' },
  { v: 'bulan_lalu', l: 'Bulan Lalu' },
  { v: 'semester_1', l: 'Semester 1' },
  { v: 'semester_2', l: 'Semester 2' },
  { v: 'tahun_ini', l: 'Tahun Ini (2026)' },
  { v: 'kustom', l: 'Rentang Kustom' },
]

const riwayatEkspor = ref([
  { id: 1, nama: 'Absensi_Mei2026.xlsx', ukuran: '124 KB', tgl: '27 Mei 2026, 14:32', status: 'selesai', format: 'excel' },
  { id: 2, nama: 'LaporanNilai_Sem1.pdf', ukuran: '892 KB', tgl: '20 Mei 2026, 09:15', status: 'selesai', format: 'pdf' },
  { id: 3, nama: 'Keuangan_Apr2026.xlsx', ukuran: '256 KB', tgl: '1 Mei 2026, 08:00', status: 'selesai', format: 'excel' },
])

function toggleData(id) {
  const idx = selectedData.value.indexOf(id)
  if (idx >= 0) selectedData.value.splice(idx, 1)
  else selectedData.value.push(id)
}

function isSelected(id) {
  return selectedData.value.includes(id)
}

async function handleEkspor() {
  if (!selectedData.value.length) {
    toast.error('Pilih minimal satu jenis data untuk diekspor.')
    return
  }
  isExporting.value = true
  await new Promise(r => setTimeout(r, 2000))
  isExporting.value = false
  const fmt = formatList.find(f => f.id === selectedFormat.value)
  const namaFile = `Ekspor_${selectedData.value.join('_')}_${new Date().toLocaleDateString('id-ID').replace(/\//g, '')}.${selectedFormat.value === 'excel' ? 'xlsx' : selectedFormat.value}`
  riwayatEkspor.value.unshift({ id: Date.now(), nama: namaFile, ukuran: '~', tgl: 'Baru saja', status: 'selesai', format: selectedFormat.value })
  toast.success(`Ekspor berhasil: ${namaFile}`)
}
</script>

<template>
  <div class="space-y-6 animate-in fade-in duration-300">
    <!-- Header -->
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between border-b pb-4">
      <div>
        <h1 class="text-2xl font-bold tracking-tight flex items-center gap-2">
          <Download class="size-6 text-primary" />
          Ekspor Data
        </h1>
        <p class="text-muted-foreground mt-1 text-sm">Ekspor data laporan ke format Excel, PDF, atau CSV.</p>
      </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
      <!-- Left: Config -->
      <div class="lg:col-span-2 space-y-5">
        <!-- Pilih Format -->
        <Card>
          <CardHeader class="pb-3">
            <CardTitle class="text-base flex items-center gap-2">
              <Settings class="size-4 text-primary" />
              Format Ekspor
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div class="grid gap-3 grid-cols-1 sm:grid-cols-3">
              <button
                v-for="fmt in formatList"
                :key="fmt.id"
                @click="selectedFormat = fmt.id"
                :class="[
                  'flex items-start gap-3 p-4 rounded-xl border-2 text-left transition-all',
                  selectedFormat === fmt.id ? 'border-primary bg-primary/5' : 'border-muted hover:border-muted-foreground/30',
                ]"
              >
                <div :class="['p-2 rounded-lg', fmt.bg]">
                  <component :is="fmt.icon" :class="['size-5', fmt.color]" />
                </div>
                <div>
                  <p class="font-semibold text-sm">{{ fmt.label }}</p>
                  <p class="text-xs text-muted-foreground mt-0.5">{{ fmt.desc }}</p>
                </div>
              </button>
            </div>
          </CardContent>
        </Card>

        <!-- Pilih Data -->
        <Card>
          <CardHeader class="pb-3">
            <CardTitle class="text-base">Pilih Data yang Diekspor</CardTitle>
            <CardDescription class="text-xs">Centang satu atau lebih jenis data yang ingin diekspor.</CardDescription>
          </CardHeader>
          <CardContent>
            <div class="grid gap-3 grid-cols-1 sm:grid-cols-2">
              <button
                v-for="opt in dataOptions"
                :key="opt.id"
                @click="toggleData(opt.id)"
                :class="[
                  'flex items-center gap-3 p-3 rounded-xl border text-left transition-all',
                  isSelected(opt.id) ? 'border-primary bg-primary/5' : 'border-muted hover:border-muted-foreground/30',
                ]"
              >
                <Checkbox :checked="isSelected(opt.id)" @click.prevent />
                <div>
                  <p class="font-medium text-sm">{{ opt.label }}</p>
                  <p class="text-xs text-muted-foreground">{{ opt.desc }}</p>
                </div>
              </button>
            </div>
          </CardContent>
        </Card>

        <!-- Opsi Tambahan -->
        <Card>
          <CardHeader class="pb-3">
            <CardTitle class="text-base">Opsi Lanjutan</CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <div class="flex flex-col gap-3">
              <div class="flex flex-col gap-1.5">
                <Label class="text-xs text-muted-foreground">Periode Data</Label>
                <Select v-model="selectedPeriode">
                  <SelectTrigger class="w-[220px]"><SelectValue /></SelectTrigger>
                  <SelectContent>
                    <SelectItem v-for="p in periodeList" :key="p.v" :value="p.v">{{ p.l }}</SelectItem>
                  </SelectContent>
                </Select>
              </div>
              <Separator />
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium">Sertakan Header Sekolah</p>
                  <p class="text-xs text-muted-foreground">Tambahkan logo dan identitas sekolah pada dokumen</p>
                </div>
                <Switch v-model:checked="includeHeader" />
              </div>
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium">Sertakan Grafik</p>
                  <p class="text-xs text-muted-foreground">Tambahkan visualisasi data (hanya untuk Excel & PDF)</p>
                </div>
                <Switch v-model:checked="includeChart" :disabled="selectedFormat === 'csv'" />
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Button Ekspor -->
        <Button
          id="btn-do-ekspor"
          @click="handleEkspor"
          :disabled="isExporting"
          class="w-full gap-2 h-11 text-base"
        >
          <Download v-if="!isExporting" class="size-5" />
          <div v-else class="size-5 animate-spin rounded-full border-2 border-background border-t-transparent" />
          {{ isExporting ? 'Sedang Mengekspor...' : 'Ekspor Sekarang' }}
        </Button>
      </div>

      <!-- Right: Riwayat -->
      <div>
        <Card>
          <CardHeader class="pb-3">
            <CardTitle class="text-base flex items-center gap-2">
              <Clock class="size-4 text-primary" />
              Riwayat Ekspor
            </CardTitle>
          </CardHeader>
          <CardContent class="space-y-3">
            <div
              v-for="r in riwayatEkspor"
              :key="r.id"
              class="flex items-start gap-3 p-3 rounded-xl border bg-muted/20 hover:bg-muted/40 transition-colors"
            >
              <div :class="[
                'p-1.5 rounded-lg shrink-0',
                r.format === 'excel' ? 'bg-green-100 dark:bg-green-950/40' :
                r.format === 'pdf' ? 'bg-red-100 dark:bg-red-950/40' : 'bg-blue-100 dark:bg-blue-950/40'
              ]">
                <component :is="r.format === 'excel' ? FileSpreadsheet : r.format === 'pdf' ? FileText : FileJson"
                  :class="['size-4', r.format === 'excel' ? 'text-green-600' : r.format === 'pdf' ? 'text-red-600' : 'text-blue-600']" />
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold truncate">{{ r.nama }}</p>
                <p class="text-xs text-muted-foreground">{{ r.ukuran }} · {{ r.tgl }}</p>
              </div>
              <Button variant="ghost" size="icon" class="h-7 w-7 shrink-0">
                <Download class="size-3.5" />
              </Button>
            </div>
            <p v-if="!riwayatEkspor.length" class="text-sm text-muted-foreground text-center py-4">Belum ada riwayat ekspor.</p>
          </CardContent>
        </Card>
      </div>
    </div>
  </div>
</template>
