<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { mockStudents, academicMonths } from '../../../data/mockAbsensi'

const route = useRoute()

// Query params
const selectedKelas = computed(() => route.query.kelas || '2 D')
const selectedTahun = computed(() => route.query.tahun || '2026/2027')
const activeMonthIdx = computed(() => parseInt(route.query.monthIdx || '11'))

// --- Data Siswa ---
const students = ref([])

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
  const key = getAttendanceKey(
    selectedKelas.value,
    selectedTahun.value,
    activeMonthIdx.value,
    studentId,
    dayNum
  )
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

onMounted(() => {
  const savedMap = localStorage.getItem('print_attendance_map')
  if (savedMap) {
    attendanceMap.value = JSON.parse(savedMap)
  }

  const savedStudents = localStorage.getItem('print_students_list')
  if (savedStudents) {
    students.value = JSON.parse(savedStudents)
  }

  // Auto-trigger window.print() once rendered
  setTimeout(() => {
    window.focus()
    window.print()
  }, 500)
})
</script>

<template>
  <div class="p-6 max-w-full print-page">
    <!-- Print-only Header Box (Exact Wireframe match) -->
    <div class="print-header-box text-left mb-6">
      <div class="print-header-line">Kelas : {{ selectedKelas }}</div>
      <div class="print-header-line">
        Tahun Pelajaran : {{ selectedTahun }} - Semester {{ selectedSemester }}
      </div>
      <div class="print-header-line">
        Bulan : {{ activeMonthName }} {{ currentCalendarYear }} 
        (1 {{ activeMonthName }} {{ currentCalendarYear }} - {{ daysInMonth.length }} {{ activeMonthName }} {{ currentCalendarYear }})
      </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="border-collapse w-full print-table">
        <thead>
          <tr>
            <th
              rowspan="2"
              class="w-[50px]"
            >
              No
            </th>
            <th
              rowspan="2"
              class="w-[200px] text-left pl-2"
            >
              Nama Siswa
            </th>
            <th
              rowspan="2"
              class="w-[110px]"
            >
              NIS
            </th>
            <th
              rowspan="2"
              class="w-[60px]"
            >
              L/P
            </th>
            <th
              :colspan="daysInMonth.length"
              class="py-2"
            >
              Tanggal
            </th>
            <th
              colspan="5"
              class="py-2"
            >
              Jumlah
            </th>
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
            <th class="w-[30px]">T</th>
            <th class="w-[30px]">I</th>
            <th class="w-[30px]">S</th>
            <th class="w-[30px]">A</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(student, index) in students"
            :key="student.id"
          >
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
            <td class="font-extrabold text-xs">{{ getStudentTotal(student.id, 'T') }}</td>
            <td class="font-extrabold text-xs">{{ getStudentTotal(student.id, 'I') }}</td>
            <td class="font-extrabold text-xs">{{ getStudentTotal(student.id, 'S') }}</td>
            <td class="font-extrabold text-xs">{{ getStudentTotal(student.id, 'A') }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Keterangan Absensi -->
    <div class="mt-8 border-2 border-black rounded-lg p-4 bg-gray-50/50 print:bg-white text-left break-inside-avoid">
      <h3 class="font-bold text-sm uppercase mb-4 text-black tracking-wider">Keterangan Absensi</h3>
      <div class="grid grid-cols-2 gap-4">
        <div class="flex items-start gap-3">
          <span class="w-7 h-7 shrink-0 rounded border border-black font-extrabold flex items-center justify-center text-xs mt-0.5 bg-gray-200 print:bg-gray-200 print:color-black">H</span>
          <div class="text-xs">
            <p class="font-bold text-black">Hadir</p>
            <p class="text-gray-700 font-medium">Siswa menghadiri proses belajar-mengajar.</p>
          </div>
        </div>
        <div class="flex items-start gap-3">
          <span class="w-7 h-7 shrink-0 rounded border border-black font-extrabold flex items-center justify-center text-xs mt-0.5 bg-gray-200 print:bg-gray-200 print:color-black">T</span>
          <div class="text-xs">
            <p class="font-bold text-black">Terlambat</p>
            <p class="text-gray-700 font-medium">Siswa hadir melewati batas toleransi keterlambatan sekolah.</p>
          </div>
        </div>
        <div class="flex items-start gap-3">
          <span class="w-7 h-7 shrink-0 rounded border border-black font-extrabold flex items-center justify-center text-xs mt-0.5 bg-gray-200 print:bg-gray-200 print:color-black">S</span>
          <div class="text-xs">
            <p class="font-bold text-black">Sakit</p>
            <p class="text-gray-700 font-medium">Siswa sakit dengan surat keterangan wali/dokter.</p>
          </div>
        </div>
        <div class="flex items-start gap-3">
          <span class="w-7 h-7 shrink-0 rounded border border-black font-extrabold flex items-center justify-center text-xs mt-0.5 bg-gray-200 print:bg-gray-200 print:color-black">I</span>
          <div class="text-xs">
            <p class="font-bold text-black">Izin</p>
            <p class="text-gray-700 font-medium">Siswa meminta izin untuk kepentingan mendesak.</p>
          </div>
        </div>
        <div class="flex items-start gap-3">
          <span class="w-7 h-7 shrink-0 rounded border border-black font-extrabold flex items-center justify-center text-xs mt-0.5 bg-gray-200 print:bg-gray-200 print:color-black">A</span>
          <div class="text-xs">
            <p class="font-bold text-black">Alpha</p>
            <p class="text-gray-700 font-medium">Tanpa keterangan atau tidak memberi kabar.</p>
          </div>
        </div>
        <div class="flex items-start gap-3">
          <span class="w-7 h-7 shrink-0 rounded border border-black font-extrabold flex items-center justify-center text-xs mt-0.5 bg-gray-200 print:bg-gray-200 print:color-black">?</span>
          <div class="text-xs">
            <p class="font-bold text-black">Belum Diisi</p>
            <p class="text-gray-700 font-medium">Data presensi tanggal tersebut belum dimasukkan.</p>
          </div>
        </div>
      </div>
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
  body,
  html,
  .print-page {
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
