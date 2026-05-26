<script setup lang="ts">
import { BellRing, ChevronRight } from 'lucide-vue-next'
import { ref, computed } from 'vue'
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Progress } from '@/components/ui/progress'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue
} from '@/components/ui/select'

const semester = ref('ganjil-2026')

const dataPerSemester: Record<string, { nama: string; nilai: number }[]> = {
  'ganjil-2026': [
    { nama: 'Matematika',       nilai: 85 },
    { nama: 'Bahasa Indonesia', nilai: 88 },
    { nama: 'Fisika',           nilai: 78 },
    { nama: 'Kimia',            nilai: 82 },
    { nama: 'Biologi',          nilai: 90 },
    { nama: 'Bahasa Inggris',   nilai: 84 },
    { nama: 'PKn',              nilai: 91 }
  ],
  'genap-2025': [
    { nama: 'Matematika',       nilai: 80 },
    { nama: 'Bahasa Indonesia', nilai: 84 },
    { nama: 'Fisika',           nilai: 75 },
    { nama: 'Kimia',            nilai: 79 },
    { nama: 'Biologi',          nilai: 87 },
    { nama: 'Bahasa Inggris',   nilai: 81 },
    { nama: 'PKn',              nilai: 88 }
  ],
  'ganjil-2025': [
    { nama: 'Matematika',       nilai: 76 },
    { nama: 'Bahasa Indonesia', nilai: 81 },
    { nama: 'Fisika',           nilai: 70 },
    { nama: 'Kimia',            nilai: 74 },
    { nama: 'Biologi',          nilai: 83 },
    { nama: 'Bahasa Inggris',   nilai: 77 },
    { nama: 'PKn',              nilai: 85 }
  ]
}

const mapel = computed(() => dataPerSemester[semester.value] ?? [])

const catatanGuruWali = 'Aditya menunjukkan perkembangan yang baik. Perlu ditingkatkan konsistensi belajar Matematika dan Fisika agar lebih stabil.'

const nilaiColor = (v: number) =>
  v >= 85 ? 'text-emerald-500' : v >= 75 ? 'text-amber-500' : 'text-rose-500'
</script>

<template>
  <Card>
    <CardHeader class="pb-3">
      <div class="flex items-center justify-between gap-3">
        <CardTitle class="text-base font-semibold">Performa Akademik</CardTitle>
        <Select v-model="semester">
          <SelectTrigger class="h-8 text-xs w-auto min-w-[160px]">
            <SelectValue />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="ganjil-2026" class="text-xs">Semester Ganjil 2026</SelectItem>
            <SelectItem value="genap-2025"  class="text-xs">Semester Genap 2025</SelectItem>
            <SelectItem value="ganjil-2025" class="text-xs">Semester Ganjil 2025</SelectItem>
          </SelectContent>
        </Select>
      </div>
      <CardDescription class="text-xs">Nilai per mata pelajaran semester ini</CardDescription>
    </CardHeader>

    <CardContent class="space-y-4">
      <div
        v-for="m in mapel"
        :key="m.nama"
        class="space-y-1.5 transition-all duration-300"
      >
        <div class="flex items-center justify-between text-sm">
          <span class="font-medium">{{ m.nama }}</span>
          <div class="flex items-center gap-2">
            <span :class="['font-bold tabular-nums text-xs', nilaiColor(m.nilai)]">{{ m.nilai }}%</span>
            <ChevronRight class="size-3.5 text-muted-foreground" />
          </div>
        </div>
        <Progress :model-value="m.nilai" class="h-2" />
      </div>


    </CardContent>

    <CardFooter class="border-t pt-4">
      <div class="flex items-start gap-3 rounded-lg bg-muted/50 p-3 w-full">
        <BellRing class="size-4 text-amber-500 shrink-0 mt-0.5" />
        <div>
          <p class="text-xs font-semibold text-foreground">Catatan Guru Wali</p>
          <p class="text-xs text-muted-foreground mt-0.5 leading-relaxed">{{ catatanGuruWali }}</p>
        </div>
      </div>
    </CardFooter>
  </Card>
</template>
