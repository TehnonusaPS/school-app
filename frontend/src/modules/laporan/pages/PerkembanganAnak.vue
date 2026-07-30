<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import {
  FileBarChart,
  Download,
  Printer,
  TrendingUp,
  UserCircle2,
  Calendar,
  AlertCircle,
  GraduationCap,
  Medal,
  Activity,
  HeartPulse,
  Star,
} from 'lucide-vue-next'
import { Skeleton } from '@/components/ui/skeleton'
import StatCardGrid from '@/components/stat-card/StatCardGrid.vue'
import StatCard from '@/components/stat-card/StatCard.vue'
import { Button } from '@/components/ui/button'
import { Card, CardHeader, CardContent, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Label } from '@/components/ui/label'
import { Separator } from '@/components/ui/separator'
import { getSchoolStudentDevelopment } from '@/services/api/reports'

const isLoading = ref(false)
const selectedSiswa = ref('1')
const dataDetail = ref(null)
const siswaList = ref([])
const allData = ref({})

onMounted(() => {
  loadData()
})

async function loadData() {
  isLoading.value = true
  try {
    const res = await getSchoolStudentDevelopment()
    siswaList.value = res.siswaList || []
    allData.value = res.data || {}
    if (siswaList.value.length > 0) {
      selectedSiswa.value = siswaList.value[0].id
      dataDetail.value = allData.value[selectedSiswa.value]
    }
  } catch (error) {
    console.error('Failed to fetch student development data:', error)
  } finally {
    isLoading.value = false
  }
}

watch(selectedSiswa, (newVal) => {
  if (allData.value && allData.value[newVal]) {
    dataDetail.value = allData.value[newVal]
  }
})

const currentSiswa = computed(() => siswaList.value.find(s => s.id === selectedSiswa.value))

const avgNilai = computed(() => {
  if (!dataDetail.value) return 0
  const arr = dataDetail.value.akademik
  return Math.round(arr.reduce((s,k)=>s+k.nilai,0)/arr.length)
})

const getTrend = computed(() => {
  if (!dataDetail.value) return 0
  const arr = dataDetail.value.akademik
  return arr[arr.length-1].nilai - arr[0].nilai
})
</script>

<template>
  <div class="space-y-6 animate-in fade-in duration-300">
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between border-b pb-4">
      <div>
        <h1 class="text-2xl font-bold tracking-tight flex items-center gap-2">
          <Activity class="size-6 text-primary" />
          Grafik Perkembangan Anak
        </h1>
        <p class="text-muted-foreground mt-1 text-sm">Pantau tren akademik, kehadiran, dan kedisiplinan siswa secara berkala.</p>
      </div>
      <div class="flex gap-2 shrink-0">
        <Button variant="outline" size="sm" class="gap-2"><Printer class="size-4" />Cetak Laporan</Button>
      </div>
    </div>

    <!-- Pilih Anak -->
    <Card class="bg-muted/30 border-dashed">
      <CardContent class="p-4 flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div class="flex items-center gap-3 w-full sm:w-auto">
          <div class="p-2 bg-primary/10 rounded-full shrink-0"><UserCircle2 class="size-6 text-primary" /></div>
          <div class="flex-1 min-w-[200px]">
            <Label class="text-xs text-muted-foreground mb-1 block">Pilih Siswa</Label>
            <Select v-model="selectedSiswa">
              <SelectTrigger class="w-[240px]"><SelectValue /></SelectTrigger>
              <SelectContent>
                <SelectItem v-for="s in siswaList" :key="s.id" :value="s.id">
                  {{ s.nama }} - {{ s.kelas }}
                </SelectItem>
              </SelectContent>
            </Select>
          </div>
        </div>
      </CardContent>
    </Card>

    <div v-if="isLoading" class="space-y-4">
      <div class="grid gap-4 grid-cols-2 lg:grid-cols-4"><Skeleton class="h-28" v-for="i in 4" :key="i" /></div>
      <Skeleton class="h-[300px] w-full" />
    </div>

    <template v-else-if="dataDetail">
      <!-- Identitas Singkat -->
      <div class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center p-4 bg-primary/5 rounded-xl border border-primary/10">
        <div>
          <h2 class="text-lg font-bold text-primary">{{ currentSiswa.nama }}</h2>
          <p class="text-sm text-muted-foreground">NISN: {{ currentSiswa.nisn }} | Kelas: {{ currentSiswa.kelas }}</p>
        </div>
        <Badge :class="getTrend >= 0 ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400'" class="text-sm py-1 px-3">
          Tren Akademik: {{ getTrend >= 0 ? '+' : '' }}{{ getTrend }} Poin Semester Ini
        </Badge>
      </div>

      <!-- Stats Kunci -->
      <StatCardGrid cols="4">
        <StatCard
          label="Rata-rata Nilai"
          :value="avgNilai"
          sub="Semester 1"
          :icon="GraduationCap"
          variant="primary"
          :delay="100"
        />
        <StatCard
          label="Kehadiran"
          :value="dataDetail.kehadiran.hadir + '%'"
          sub="Total kehadiran"
          :icon="Calendar"
          variant="emerald"
          :delay="200"
        />
        <StatCard
          label="Sikap Sosial"
          :value="dataDetail.sikap.sosial"
          :icon="HeartPulse"
          variant="blue"
          :delay="300"
        />
        <StatCard
          label="Prestasi"
          :value="dataDetail.prestasi.length"
          sub="Sertifikat/Penghargaan"
          :icon="Medal"
          variant="amber"
          :delay="400"
        />
      </StatCardGrid>

      <div class="grid gap-6 lg:grid-cols-3">
        <!-- Grafik Nilai Dummy -->
        <Card class="lg:col-span-2">
          <CardHeader class="pb-2">
            <CardTitle class="text-base flex items-center gap-2"><TrendingUp class="size-4 text-primary" /> Grafik Nilai Akademik (6 Bulan Terakhir)</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="h-[250px] flex items-end justify-between gap-2 pt-6 pb-2 border-b border-l px-2">
              <div v-for="(item, idx) in dataDetail.akademik" :key="idx" class="flex flex-col items-center flex-1 group">
                <div class="relative w-full max-w-[40px] bg-primary/20 hover:bg-primary/40 transition-all rounded-t-sm" :style="`height: ${item.nilai}%`">
                  <div class="absolute -top-7 left-1/2 -translate-x-1/2 text-xs font-bold text-primary opacity-0 group-hover:opacity-100 transition-opacity bg-background border shadow-sm px-2 py-0.5 rounded">{{ item.nilai }}</div>
                </div>
                <span class="text-xs text-muted-foreground mt-2 font-medium">{{ item.bulan }}</span>
              </div>
            </div>
          </CardContent>
        </Card>

        <div class="space-y-6">
          <!-- Kedisiplinan -->
          <Card>
            <CardHeader class="pb-2">
              <CardTitle class="text-base flex items-center gap-2"><AlertCircle class="size-4 text-primary" /> Kedisiplinan & Kehadiran</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
              <div class="grid grid-cols-2 gap-3 text-center">
                <div class="p-3 bg-red-50 dark:bg-red-950/20 rounded-xl border border-red-100 dark:border-red-900/40">
                  <div class="text-xl font-bold text-red-600 dark:text-red-400">{{ dataDetail.kehadiran.alpa }}</div>
                  <p class="text-xs text-muted-foreground">Alpa</p>
                </div>
                <div class="p-3 bg-yellow-50 dark:bg-yellow-950/20 rounded-xl border border-yellow-100 dark:border-yellow-900/40">
                  <div class="text-xl font-bold text-yellow-600 dark:text-yellow-400">{{ dataDetail.kehadiran.sakit + dataDetail.kehadiran.izin }}</div>
                  <p class="text-xs text-muted-foreground">Sakit/Izin</p>
                </div>
              </div>
              <div>
                <Label class="text-xs text-muted-foreground">Catatan Wali Kelas:</Label>
                <p class="text-sm italic text-foreground mt-1 border-l-2 border-primary pl-3">"{{ dataDetail.sikap.catatan }}"</p>
              </div>
            </CardContent>
          </Card>

          <!-- Prestasi -->
          <Card v-if="dataDetail.prestasi.length">
            <CardHeader class="pb-2">
              <CardTitle class="text-base flex items-center gap-2"><Medal class="size-4 text-primary" /> Daftar Prestasi</CardTitle>
            </CardHeader>
            <CardContent>
              <ul class="space-y-2">
                <li v-for="(p, i) in dataDetail.prestasi" :key="i" class="flex items-start gap-2 text-sm bg-muted/40 p-2 rounded-lg">
                  <Star class="size-4 text-yellow-500 shrink-0 mt-0.5" />
                  <span>{{ p }}</span>
                </li>
              </ul>
            </CardContent>
          </Card>
        </div>
      </div>
    </template>
  </div>
</template>
