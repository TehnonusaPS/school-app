<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { mockStudents, academicMonths } from '../data/mockAbsensi'

const route = useRoute()

// Query params
const selectedKelas = computed(() => route.query.kelas || '2 D')
const selectedTahun = computed(() => route.query.tahun || '2026/2027')
const activeMonthIdx = computed(() => parseInt(route.query.monthIdx || '11'))

// --- Data Siswa ---
const students = ref(mockStudents)

const currentCalendarYear = computed(() => {
  const startYear = parseInt(selectedTahun.value.split('/')[0])
  const monthInfo = academicMonths[activeMonthIdx.value]
  return startYear + monthInfo.yearOffset
})

const selectedMonthVal = computed(() => {
  return academicMonths[activeMonthIdx.value].val
})

const selectedSemester = computed(() => {
  return academicMonths[activeMonthIdx.value].semester
})

const activeMonthName = computed(() => {
  return academicMonths[activeMonthIdx.value].name
})

const daysInMonth = computed(() => {
  const year = currentCalendarYear.value
  const month = selectedMonthVal.value
  const totalDays = new Date(year, month + 1, 0).getDate()
  
  const days = []
  for (let d = 1; d <= totalDays; d++) {
    days.push({ dateNum: d })
  }
  return days
})

const attendanceMap = ref({})

const getAttendanceKey = (kelas, tahun, monthIdx, studentId, day) => {
  return `${kelas}_${tahun}_${monthIdx}_${studentId}_${day}`
}

const getStatus = (studentId, dayNum) => {
  const key = getAttendanceKey(selectedKelas.value, selectedTahun.value, activeMonthIdx.value, studentId, dayNum)
  return attendanceMap.value[key] || null
}

const getStudentTotal = (studentId, statusType) => {
  let count = 0
  const daysCount = daysInMonth.value.length
  const currentKelas = selectedKelas.value
  const currentTahun = selectedTahun.value
  const currentMonthIdx = activeMonthIdx.value
  
  for (let d = 1; d <= daysCount; d++) {
    const key = getAttendanceKey(currentKelas, currentTahun, currentMonthIdx, studentId, d)
    if (attendanceMap.value[key] === statusType) {
      count++
    }
  }
  return count > 0 ? count : ''
}

import { glassFade } from '@/config/motion'

onMounted(() => {
  const saved = localStorage.getItem('print_attendance_map')
  if (saved) {
    attendanceMap.value = JSON.parse(saved)
  }
  
  // Auto-trigger window.print() once rendered
  setTimeout(() => {
    window.focus()
    window.print()
  }, 500)
})
</script>

<template>
  <div
    v-motion
    :initial="glassFade.initial"
    :visible-once="glassFade.visible"
    class="p-6 max-w-full print-page"
  >
    <!-- Print-only Header Box (Exact Wireframe match) -->
    <div class="print-header-box text-left mb-6">
      <div class="print-header-line">Kelas : {{ selectedKelas }}</div>
      <div class="print-header-line">Tahun Pelajaran : {{ selectedTahun }} - Semester {{ selectedSemester }}</div>
      <div class="print-header-line">Bulan : {{ activeMonthName }} {{ currentCalendarYear }}</div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="border-collapse w-full print-table">
        <thead>
          <tr>
            <th rowspan="2" class="w-[50px]">No</th>
            <th rowspan="2" class="w-[200px] text-left pl-2">Nama Siswa</th>
            <th rowspan="2" class="w-[110px]">NIS</th>
            <th rowspan="2" class="w-[60px]">L/P</th>
            <th :colspan="daysInMonth.length" class="py-2">Tanggal</th>
            <th colspan="4" class="py-2">Jumlah</th>
          </tr>
          <tr>
            <th
              v-for="d in daysInMonth"
              :key="d.dateNum"
              class="w-[30px] text-[10px] font-mono"
            >
              {{ d.dateNum.toString().padStart(2, '0') }}
            </th>
            <th class="w-[30px]">H</th>
            <th class="w-[30px]">I</th>
            <th class="w-[30px]">S</th>
            <th class="w-[30px]">A</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(student, index) in students" :key="student.id">
            <td>{{ index + 1 }}</td>
            <td class="text-left font-bold pl-2">{{ student.nama }}</td>
            <td class="font-mono text-xs">{{ student.nis }}</td>
            <td>{{ student.gender }}</td>
            <td
              v-for="d in daysInMonth"
              :key="d.dateNum"
              class="font-extrabold text-[10px]"
            >
              {{ getStatus(student.id, d.dateNum) || '' }}
            </td>
            <td class="font-extrabold text-xs">{{ getStudentTotal(student.id, 'H') }}</td>
            <td class="font-extrabold text-xs">{{ getStudentTotal(student.id, 'I') }}</td>
            <td class="font-extrabold text-xs">{{ getStudentTotal(student.id, 'S') }}</td>
            <td class="font-extrabold text-xs">{{ getStudentTotal(student.id, 'A') }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<style scoped>
.print-page {
  background: white !important;
  color: black !important;
  font-family: sans-serif;
}

.print-header-box {
  border: 1.5px solid #0f172a !important;
  border-radius: 8px !important;
  padding: 14px 18px !important;
  background-color: #f1f5f9 !important;
  color: #0f172a !important;
  font-size: 13px !important;
  line-height: 1.5 !important;
  font-weight: bold !important;
}

.print-header-line {
  margin-bottom: 6px !important;
}
.print-header-line:last-child {
  margin-bottom: 0 !important;
}

.print-table {
  width: 100% !important;
  border-collapse: collapse !important;
  table-layout: auto !important;
  border: 1.5px solid #000000 !important;
}

.print-table th, 
.print-table td {
  border: 1.5px solid #000000 !important;
  color: #000000 !important;
  padding: 6px 4px !important;
  font-size: 10px !important;
  text-align: center !important;
}

.print-table th {
  font-weight: bold !important;
  background-color: #f1f5f9 !important;
}

.print-table td.text-left {
  text-align: left !important;
  font-weight: bold !important;
  padding-left: 8px !important;
}

@media print {
  body, html, .print-page {
    margin: 0 !important;
    padding: 0 !important;
    width: 100% !important;
    background: white !important;
    color: black !important;
  }
  @page {
    size: landscape !important;
    margin: 1cm !important;
  }
}
</style>
