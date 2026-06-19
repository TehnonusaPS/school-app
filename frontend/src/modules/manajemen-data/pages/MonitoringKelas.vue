<script setup>
import { ref, computed } from 'vue'
import PageHeader from '@/components/page-header/PageHeader.vue'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue
} from '@/components/ui/select'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow
} from '@/components/ui/table'
import { Card, CardContent } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Input } from '@/components/ui/input'
import {
  KELAS_OPTIONS,
  classMetadata,
  initialSiswaTahunAjaran,
  getTahunAjaranList
} from './tahun-ajaran/data/mockTahunAjaran'
import StatCard from '@/components/stat-card/StatCard.vue'
import { glassSlide, glassFade } from '@/config/motion'
import { Calendar, GraduationCap, User, Users } from 'lucide-vue-next'

// Deteksi tahun ajaran aktif secara dinamis dari Local Storage
const tahunAjaranList = getTahunAjaranList()
const activeTahunItem = tahunAjaranList.find(t => t.status === 'aktif') || { tahun: '2025/2026' }
const activeTahun = ref(activeTahunItem.tahun)
const activeTahunText = ref('Tahun Ajaran ' + activeTahun.value + ' (Aktif)')

const selectedKelas = ref('V A')

const classInfo = computed(() => {
  const years = classMetadata[activeTahun.value]
  if (years && years[selectedKelas.value]) {
    return years[selectedKelas.value]
  }
  return { waliKelas: '-', siswaCount: 0 }
})

const filteredStudents = computed(() => {
  return initialSiswaTahunAjaran.filter(
    s => s.tahunAjaran === activeTahun.value && s.kelas === selectedKelas.value
  )
})
</script>

<template>
  <div
    v-motion
    :initial="glassFade.initial"
    :visible-once="glassFade.visible"
    class="space-y-6 w-full mx-auto px-0 text-left"
  >
    <!-- Header -->
    <PageHeader
      title="Monitoring Kelas"
      description="Pantau roster dan pembagian siswa pada kelas berjalan di tahun ajaran aktif"
    />

    <!-- Selector Card (Tahun Ajaran Aktif & Pilihan Kelas) -->
    <Card
      v-motion
      :initial="glassSlide.initial"
      :visible-once="{ ...glassSlide.visible, transition: { ...glassSlide.visible.transition, delay: 100 } }"
      class="border border-border bg-card/60 backdrop-blur-md rounded-2xl shadow-sm overflow-hidden"
    >
      <CardContent class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-2">
            <label class="text-sm font-bold text-muted-foreground flex items-center gap-1.5">
              <Calendar class="size-4 text-primary" />
              Tahun Ajaran Aktif
            </label>
            <Input
              v-model="activeTahunText"
              disabled
              class="h-11 rounded-xl bg-muted/50 border-border font-semibold text-foreground cursor-not-allowed select-none"
            />
          </div>

          <div class="space-y-2">
            <label class="text-sm font-bold text-muted-foreground flex items-center gap-1.5">
              <GraduationCap class="size-4 text-primary" />
              Pilih Kelas
            </label>
            <Select v-model="selectedKelas">
              <SelectTrigger class="h-11 rounded-xl bg-background/50 border-border">
                <SelectValue placeholder="Pilih Kelas..." />
              </SelectTrigger>
              <SelectContent class="rounded-xl">
                <SelectItem
                  v-for="opt in KELAS_OPTIONS"
                  :key="opt.value"
                  :value="opt.value"
                >
                  Kelas {{ opt.label }}
                </SelectItem>
              </SelectContent>
            </Select>
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Info Cards (Grid of StatCards) -->
    <div
      v-motion
      :initial="glassSlide.initial"
      :visible-once="{ ...glassSlide.visible, transition: { ...glassSlide.visible.transition, delay: 200 } }"
      class="grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-4"
    >
      <StatCard
        label="TAHUN AJARAN"
        :value="activeTahun"
        :icon="Calendar"
        illustration="globe"
        variant="primary"
      />
      <StatCard
        label="KELAS"
        :value="selectedKelas"
        :icon="GraduationCap"
        illustration="school_bell"
        variant="emerald"
      />
      <StatCard
        label="WALI KELAS"
        :value="classInfo.waliKelas"
        :icon="User"
        illustration="open_book"
        variant="amber"
      />
      <StatCard
        label="JUMLAH SISWA"
        :value="filteredStudents.length"
        sub="Siswa Aktif"
        :icon="Users"
        illustration="abc_board"
        variant="violet"
      />
    </div>

    <!-- Table Card -->
    <Card
      v-motion
      :initial="glassSlide.initial"
      :visible-once="{ ...glassSlide.visible, transition: { ...glassSlide.visible.transition, delay: 300 } }"
      class="border border-border bg-card/60 backdrop-blur-md rounded-2xl shadow-sm overflow-hidden"
    >
      <CardContent class="p-0">
        <div class="overflow-x-auto">
          <Table>
            <TableHeader class="bg-muted/40">
              <TableRow>
                <TableHead class="w-[80px] font-bold text-foreground text-center">No</TableHead>
                <TableHead class="font-bold text-foreground">Nama Siswa</TableHead>
                <TableHead class="font-bold text-foreground text-center">NIS</TableHead>
                <TableHead class="font-bold text-foreground text-center">NISN</TableHead>
                <TableHead class="w-[120px] font-bold text-foreground text-center">L/P</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow
                v-for="(student, idx) in filteredStudents"
                :key="student.id"
                class="hover:bg-muted/30 transition-colors"
              >
                <TableCell class="text-center font-semibold text-muted-foreground">
                  {{ idx + 1 }}
                </TableCell>
                <TableCell class="font-bold text-foreground">
                  {{ student.nama }}
                </TableCell>
                <TableCell class="text-center font-mono text-xs text-muted-foreground">
                  {{ student.nis }}
                </TableCell>
                <TableCell class="text-center font-mono text-xs text-muted-foreground">
                  {{ student.nisn }}
                </TableCell>
                <TableCell class="text-center">
                  <Badge
                    :variant="student.gender === 'L' ? 'primary' : 'outline'"
                    class="font-semibold text-xs px-2.5 py-0.5 rounded-full"
                    :class="student.gender === 'L' ? 'bg-blue-500/10 text-blue-500 hover:bg-blue-500/20 border-none' : 'bg-pink-500/10 text-pink-500 hover:bg-pink-500/20 border-none'"
                  >
                    {{ student.gender }}
                  </Badge>
                </TableCell>
              </TableRow>
              <TableRow v-if="filteredStudents.length === 0">
                <TableCell colspan="5" class="h-24 text-center text-muted-foreground font-semibold">
                  Tidak ada data siswa untuk tahun ajaran dan kelas yang dipilih.
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
      </CardContent>
    </Card>
  </div>
</template>
