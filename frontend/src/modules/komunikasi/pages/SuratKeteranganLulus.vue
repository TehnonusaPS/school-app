<script setup>
import { ref } from 'vue'
import { Plus, Printer, FileEdit, Trash2, Download } from 'lucide-vue-next'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow
} from '@/components/ui/table'

const mockData = ref([
  { id: 'SKL-001', nama: 'Fajar Hidayat', nisn: '0051234567', tahun: '2025/2026', status: 'Tercetak' },
  { id: 'SKL-002', nama: 'Lestari Ayu', nisn: '0057654321', tahun: '2025/2026', status: 'Draft' },
])

const getStatusColor = (status) => {
  switch (status) {
    case 'Tercetak': return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
    case 'Draft': return 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400'
    default: return 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400'
  }
}
</script>

<template>
  <div class="space-y-6 p-1">
    <Card class="border-slate-200/60 dark:border-slate-800 shadow-sm">
    <CardHeader class="flex flex-row items-center justify-between pb-4">
      <div>
        <CardTitle class="text-lg">Surat Keterangan Lulus (SKL)</CardTitle>
        <CardDescription>Penerbitan SKL sementara sebelum ijazah resmi dibagikan.</CardDescription>
      </div>
      <div class="flex items-center gap-2">
        <Button variant="outline" size="sm" class="gap-2">
          <Download class="w-4 h-4" />
          Export Data
        </Button>
        <Button class="gap-2" size="sm">
          <Plus class="w-4 h-4" />
          Terbitkan SKL
        </Button>
      </div>
    </CardHeader>
    <CardContent>
      <div class="rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
        <Table>
          <TableHeader class="bg-slate-50 dark:bg-slate-900/50">
            <TableRow>
              <TableHead class="w-[100px]">No. SKL</TableHead>
              <TableHead>Nama Siswa</TableHead>
              <TableHead>NISN</TableHead>
              <TableHead>Tahun Lulus</TableHead>
              <TableHead>Status</TableHead>
              <TableHead class="text-right">Aksi</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="item in mockData" :key="item.id">
              <TableCell class="font-medium">{{ item.id }}</TableCell>
              <TableCell>{{ item.nama }}</TableCell>
              <TableCell>{{ item.nisn }}</TableCell>
              <TableCell>{{ item.tahun }}</TableCell>
              <TableCell>
                <Badge :class="['border-transparent font-medium', getStatusColor(item.status)]">
                  {{ item.status }}
                </Badge>
              </TableCell>
              <TableCell class="text-right">
                <div class="flex items-center justify-end gap-1">
                  <Button variant="ghost" size="icon" class="h-8 w-8 text-slate-500 hover:text-primary">
                    <Printer class="w-4 h-4" />
                  </Button>
                  <Button variant="ghost" size="icon" class="h-8 w-8 text-slate-500 hover:text-amber-500">
                    <FileEdit class="w-4 h-4" />
                  </Button>
                </div>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
    </CardContent>
    </Card>
  </div>
</template>
