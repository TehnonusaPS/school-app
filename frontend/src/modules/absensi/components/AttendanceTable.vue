<script setup>
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow
} from '@/components/ui/table'

defineProps({
  students: {
    type: Array,
    required: true
  },
  daysInMonth: {
    type: Array,
    required: true
  },
  isReadOnly: {
    type: Boolean,
    default: false
  },
  getStatus: {
    type: Function,
    required: true
  },
  getStudentTotal: {
    type: Function,
    required: true
  }
})

const emit = defineEmits(['toggle-status'])

const handleCellClick = (studentId, dateNum) => {
  emit('toggle-status', studentId, dateNum)
}
</script>

<template>
  <div class="overflow-x-auto relative">
    <Table class="border-collapse w-full min-w-[1200px]">
      <TableHeader class="bg-muted/40 border-b border-border/80">
        <!-- Row 1: Header Titles -->
        <TableRow>
          <TableHead rowspan="2" class="font-bold text-foreground text-center border-r border-border/60 w-[50px] text-xs uppercase tracking-wider">No</TableHead>
          <TableHead rowspan="2" class="font-bold text-foreground text-left border-r border-border/60 w-[200px] text-xs uppercase tracking-wider">Nama Siswa</TableHead>
          <TableHead rowspan="2" class="font-bold text-foreground text-center border-r border-border/60 w-[110px] text-xs uppercase tracking-wider">NIS</TableHead>
          <TableHead rowspan="2" class="font-bold text-foreground text-center border-r border-border/60 w-[60px] text-xs uppercase tracking-wider">L/P</TableHead>
          <TableHead :colspan="daysInMonth.length" class="font-bold text-foreground text-center border-b border-r border-border/60 py-2 text-xs uppercase tracking-wider">Tanggal</TableHead>
          <TableHead colspan="4" class="font-bold text-foreground text-center border-b border-border/60 py-2 text-xs uppercase tracking-wider">Jumlah</TableHead>
        </TableRow>
        <!-- Row 2: Date list & Rekap labels -->
        <TableRow class="bg-muted/20">
          <!-- Dynamic Date Columns -->
          <TableHead
            v-for="d in daysInMonth"
            :key="d.dateNum"
            class="text-center font-bold text-foreground/90 p-0 text-[10px] w-[30px] border-r border-border/60"
            :class="d.isWeekend ? 'bg-rose-500/10 text-rose-500 dark:bg-rose-950/20' : ''"
          >
            <div class="flex flex-col items-center justify-center py-1">
              <span class="font-mono">{{ d.dateNum.toString().padStart(2, '0') }}</span>
              <span class="text-[8px] text-muted-foreground uppercase font-bold">{{ d.dayName }}</span>
            </div>
          </TableHead>
          <!-- Rekap Labels (H, I, S, A) -->
          <TableHead class="text-center font-extrabold text-emerald-600 dark:text-emerald-400 bg-emerald-500/5 text-[10px] w-[30px] border-r border-border/60">H</TableHead>
          <TableHead class="text-center font-extrabold text-amber-600 dark:text-amber-400 bg-amber-500/5 text-[10px] w-[30px] border-r border-border/60">I</TableHead>
          <TableHead class="text-center font-extrabold text-blue-600 dark:text-blue-400 bg-blue-500/5 text-[10px] w-[30px] border-r border-border/60">S</TableHead>
          <TableHead class="text-center font-extrabold text-red-600 dark:text-red-400 bg-red-500/5 text-[10px] w-[30px]">A</TableHead>
        </TableRow>
      </TableHeader>

      <TableBody>
        <TableRow
          v-for="(student, index) in students"
          :key="student.id"
          class="hover:bg-muted/10 transition-colors border-b border-border/60"
        >
          <!-- No -->
          <TableCell class="text-center font-medium text-xs border-r border-border/60 py-3">{{ index + 1 }}</TableCell>
          
          <!-- Nama Siswa -->
          <TableCell class="text-left font-bold text-foreground text-xs border-r border-border/60 py-3">{{ student.nama }}</TableCell>
          
          <!-- NIS -->
          <TableCell class="text-center font-mono text-xs text-muted-foreground border-r border-border/60 py-3">{{ student.nis }}</TableCell>
          
          <!-- L/P -->
          <TableCell class="text-center font-semibold text-xs border-r border-border/60 py-3">{{ student.gender }}</TableCell>

          <!-- Dynamic Attendance Cells -->
          <TableCell
            v-for="d in daysInMonth"
            :key="d.dateNum"
            class="text-center p-0.5 border-r border-border/60 select-none"
            :class="d.isWeekend ? 'bg-rose-500/5 dark:bg-rose-950/10' : ''"
          >
            <!-- Clickable attendance block -->
            <button
              type="button"
              @click="handleCellClick(student.id, d.dateNum)"
              :disabled="isReadOnly || d.isWeekend"
              class="w-full aspect-square max-w-[28px] max-h-[28px] mx-auto rounded flex items-center justify-center font-extrabold text-[10px] transition-all focus:outline-none"
              :class="[
                d.isWeekend ? 'cursor-not-allowed opacity-40' : (isReadOnly ? 'cursor-not-allowed' : 'cursor-pointer hover:scale-105 active:scale-95'),
                getStatus(student.id, d.dateNum) === 'H' ? 'bg-emerald-500 text-white shadow-xs' : '',
                getStatus(student.id, d.dateNum) === 'S' ? 'bg-blue-500 text-white shadow-xs' : '',
                getStatus(student.id, d.dateNum) === 'I' ? 'bg-amber-500 text-white shadow-xs' : '',
                getStatus(student.id, d.dateNum) === 'A' ? 'bg-red-500 text-white shadow-xs' : '',
                !getStatus(student.id, d.dateNum) ? 'text-muted-foreground/40 hover:bg-muted/30' : ''
              ]"
            >
              {{ getStatus(student.id, d.dateNum) || '?' }}
            </button>
          </TableCell>

          <!-- Realtime totals (H, I, S, A) -->
          <TableCell class="text-center font-extrabold text-xs text-emerald-600 dark:text-emerald-400 bg-emerald-500/5 border-r border-border/60 py-3">{{ getStudentTotal(student.id, 'H') }}</TableCell>
          <TableCell class="text-center font-extrabold text-xs text-amber-600 dark:text-amber-400 bg-amber-500/5 border-r border-border/60 py-3">{{ getStudentTotal(student.id, 'I') }}</TableCell>
          <TableCell class="text-center font-extrabold text-xs text-blue-600 dark:text-blue-400 bg-blue-500/5 border-r border-border/60 py-3">{{ getStudentTotal(student.id, 'S') }}</TableCell>
          <TableCell class="text-center font-extrabold text-xs text-red-600 dark:text-red-400 bg-red-500/5 py-3">{{ getStudentTotal(student.id, 'A') }}</TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </div>
</template>

<style scoped>
button:disabled {
  opacity: 0.85;
}
</style>
