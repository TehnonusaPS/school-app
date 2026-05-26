<script setup lang="ts">
import { BookOpen, ChevronRight } from 'lucide-vue-next'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'

const tugas = [
  { nama: 'Tugas Matematika',    guru: 'Ibu Maria',   deadline: '19 Okt 2024', status: 'Belum Selesai', tipe: 'upload' },
  { nama: 'Ujian Aljabar Linear',guru: 'Ibu Maria',   deadline: '20 Okt 2024', status: 'Selesai',       tipe: 'nilai'  },
  { nama: 'Essay B. Inggris',    guru: 'Bpk Supri',   deadline: '21 Okt 2024', status: 'Belum Selesai', tipe: 'upload' },
  { nama: 'Laporan Fisika',      guru: 'Bpk Udin',    deadline: '22 Okt 2024', status: 'Belum Selesai', tipe: 'upload' },
  { nama: 'Ujian Tengah Semester',guru: 'Ibu Sari',   deadline: '25 Okt 2024', status: 'Belum Selesai', tipe: 'upload' }
]

const statusVariant = (s: string) => s === 'Selesai' ? 'default' : 'secondary'
</script>

<template>
  <Card>
    <CardHeader class="pb-3">
      <div class="flex items-center justify-between">
        <div>
          <div class="flex items-center gap-2">
            <BookOpen class="size-4 text-primary" />
            <CardTitle class="text-base font-semibold">Tugas & Ujian Mendatang</CardTitle>
          </div>
          <CardDescription class="text-xs mt-1">Daftar tugas dan ujian yang perlu diselesaikan</CardDescription>
        </div>
        <Button variant="ghost" size="sm" class="h-8 text-xs gap-1 text-muted-foreground hover:text-foreground">
          Lihat Semua Tugas
          <ChevronRight class="size-3.5" />
        </Button>
      </div>
    </CardHeader>

    <CardContent class="p-0">
      <!-- Fixed header -->
      <table class="w-full text-sm border-b">
        <colgroup>
          <col style="width: 30%" />
          <col style="width: 20%" />
          <col style="width: 20%" />
          <col style="width: 15%" />
          <col style="width: 15%" />
        </colgroup>
        <thead>
          <tr class="border-b bg-muted/50">
            <th class="pl-6 py-3 text-left text-xs font-medium text-muted-foreground">Nama Tugas</th>
            <th class="py-3 text-left text-xs font-medium text-muted-foreground">Guru</th>
            <th class="py-3 text-center text-xs font-medium text-muted-foreground">Deadline</th>
            <th class="py-3 text-center text-xs font-medium text-muted-foreground">Status</th>
            <th class="pr-6 py-3 text-right text-xs font-medium text-muted-foreground">Action</th>
          </tr>
        </thead>
      </table>

      <!-- Body -->
      <table class="w-full text-sm">
        <colgroup>
          <col style="width: 30%" />
          <col style="width: 20%" />
          <col style="width: 20%" />
          <col style="width: 15%" />
          <col style="width: 15%" />
        </colgroup>
        <tbody>
          <tr
            v-for="(t, i) in tugas"
            :key="i"
            class="border-b transition-colors hover:bg-muted/50 cursor-default"
          >
            <td class="pl-6 py-3 font-semibold text-sm">{{ t.nama }}</td>
            <td class="py-3 text-muted-foreground text-sm">{{ t.guru }}</td>
            <td class="py-3 text-center text-sm text-muted-foreground">{{ t.deadline }}</td>
            <td class="py-3 text-center">
              <Badge :variant="statusVariant(t.status)" class="text-xs">
                {{ t.status }}
              </Badge>
            </td>
            <td class="pr-6 py-3 text-right">
              <Button size="sm" class="h-7 text-xs">
                {{ t.tipe === 'upload' ? 'Upload Tugas' : 'Buka Penilaian' }}
              </Button>
            </td>
          </tr>
        </tbody>
      </table>
    </CardContent>
  </Card>
</template>
