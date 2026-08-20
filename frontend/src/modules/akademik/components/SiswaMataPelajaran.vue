<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { 
  BookOpen, 
  ClipboardList, 
  FileText, 
  Sparkles 
} from 'lucide-vue-next'
import StatCard from '@/components/stat-card/StatCard.vue'

import SiswaSubjectFilterBar from './SiswaSubjectFilterBar.vue'
import SiswaSubjectScoreCards from './SiswaSubjectScoreCards.vue'
import SiswaMaterialListCard from './SiswaMaterialListCard.vue'
import SiswaSubjectActivityCard from './SiswaSubjectActivityCard.vue'
import SiswaSubjectGradeSection from './SiswaSubjectGradeSection.vue'
import SiswaSubjectActivitySheet from './SiswaSubjectActivitySheet.vue'

import { useSiswaAkademik } from '../composables/useSiswaAkademik'
import { fetchTeacherAgendas } from '@/services/teacherAgendaService'
import { formatNumber } from '@/utils/formatNumber'

const {
  classrooms,
  subjects,
  overview,
  globalStats,
  selectedClassroomId,
  selectedSubjectId,
  selectedMaterialId,
  fetchClassrooms,
  fetchSubjects,
  fetchOverview,
  fetchStats,
  handleDownload
} = useSiswaAkademik()

const teacherAgendasList = ref([])
const showAllActivitiesSheet = ref(false)

const loadTeacherAgendas = async () => {
  try {
    const res = await fetchTeacherAgendas()
    if (res && res.status === 'success' && res.data) {
      teacherAgendasList.value = res.data
    }
  } catch (err) {
    console.error('Gagal memuat agenda guru:', err)
  }
}

onMounted(async () => {
  await fetchClassrooms()
  await loadTeacherAgendas()
})

// Watch classroom selection changes
watch(selectedClassroomId, async (newVal) => {
  if (newVal) {
    await fetchSubjects(newVal)
    await fetchStats(newVal)
    await loadTeacherAgendas()
  }
})

// Watch subject selection changes
watch(selectedSubjectId, async (newVal) => {
  if (newVal && selectedClassroomId.value) {
    await fetchOverview(newVal, selectedClassroomId.value)
  }
})

const currentClassroomLabel = computed(() => {
  const current = classrooms.value.find(c => String(c.classroom_id) === String(selectedClassroomId.value))
  return current ? `${current.classroom_name} (${current.academic_year_name} ${current.semester_label})` : 'Pilih Kelas & Semester'
})

const currentSubjectLabel = computed(() => {
  const current = subjects.value.find(s => String(s.id) === String(selectedSubjectId.value))
  return current ? current.name : 'Pilih Mata Pelajaran'
})

const currentSemesterLabel = computed(() => {
  return currentClassroomLabel.value.includes('Ganjil') ? 'Ganjil' : 'Genap'
})

// Auto-select first material when overview materials change
watch(() => overview.value.materials, (newMats) => {
  if (newMats && newMats.length > 0) {
    selectedMaterialId.value = String(newMats[0].id)
  } else {
    selectedMaterialId.value = ''
  }
})

// Dynamic Subject Activities list
const allSubjectActivities = computed(() => {
  const list = []
  const subId = String(selectedSubjectId.value || '')
  const subLabel = currentSubjectLabel.value.toLowerCase()

  // 1. Agendas Guru for selected subject
  if (teacherAgendasList.value && teacherAgendasList.value.length > 0) {
    teacherAgendasList.value.forEach(agenda => {
      const matchId = agenda.subject_id && String(agenda.subject_id) === subId
      const matchName = agenda.subject_name && agenda.subject_name.toLowerCase() === subLabel
      if (matchId || matchName || (!agenda.subject_id && !agenda.subject_name)) {
        const d = agenda.date ? new Date(agenda.date) : new Date()
        const dateNum = isNaN(d.getDate()) ? 18 : d.getDate()
        const monthShort = isNaN(d.getTime()) ? 'Aug' : d.toLocaleDateString('id-ID', { month: 'short' })

        let variant = 'indigo'
        let icon = ClipboardList
        let typeLabel = 'Agenda Guru'

        if (agenda.type === 'tugas') {
          variant = 'amber'
          icon = BookOpen
          typeLabel = 'Tugas Kelas'
        } else if (agenda.type === 'kegiatan') {
          variant = 'amber'
          icon = Sparkles
          typeLabel = 'Kegiatan Kelas'
        } else if (agenda.type === 'ujian_harian' || agenda.type === 'ujian') {
          variant = 'purple'
          icon = ClipboardList
          typeLabel = 'Ujian Harian'
        }

        list.push({
          id: `agenda-${agenda.id}`,
          date: dateNum,
          month: monthShort,
          rawDate: agenda.date || new Date().toISOString().substring(0, 10),
          title: agenda.title,
          description: agenda.description || `${typeLabel} • ${agenda.classroom_name || 'Kelas'}`,
          typeLabel,
          icon,
          variant
        })
      }
    })
  }

  // 2. Materials from overview
  if (overview.value.materials && overview.value.materials.length > 0) {
    overview.value.materials.forEach(mat => {
      const d = mat.created_at ? new Date(mat.created_at) : new Date()
      const dateNum = isNaN(d.getDate()) ? 15 : d.getDate()
      const monthShort = isNaN(d.getTime()) ? 'Aug' : d.toLocaleDateString('id-ID', { month: 'short' })

      list.push({
        id: `mat-${mat.id}`,
        date: dateNum,
        month: monthShort,
        rawDate: mat.created_at || new Date().toISOString().substring(0, 10),
        title: mat.title,
        description: `Materi Pelajaran ${mat.file_name ? '• ' + mat.file_name : ''}`,
        typeLabel: 'Materi Pelajaran',
        icon: FileText,
        variant: 'green'
      })
    })
  }

  // 3. Assessments from overview
  if (overview.value.assessments && overview.value.assessments.length > 0) {
    overview.value.assessments.forEach(ass => {
      list.push({
        id: `ass-${ass.id}`,
        date: 20,
        month: 'Aug',
        rawDate: '2026-08-20',
        title: ass.title,
        description: `Penilaian ${ass.category === 'tugas' ? 'Tugas' : 'Ujian'} • Nilai: ${ass.score !== null ? formatNumber(ass.score) : 'Belum dinilai'}`,
        typeLabel: ass.category === 'tugas' ? 'Nilai Tugas' : 'Nilai Ujian',
        icon: ClipboardList,
        variant: ass.category === 'tugas' ? 'amber' : 'purple'
      })
    })
  }

  // Sort by nearest date first (ascending)
  list.sort((a, b) => {
    const timeA = new Date(a.rawDate).getTime()
    const timeB = new Date(b.rawDate).getTime()
    return timeA - timeB
  })
  return list
})

// Format Category List for GradeDetailCard: limit to 2 items + '...'
const formattedCategories = computed(() => {
  const stats = overview.value.stats || {}
  const cats = [
    {
      label: 'Tugas',
      weight: 40,
      score: stats.avg_tugas || 0,
      maxScore: 100,
      items: (stats.tugas_details || []).map(t => ({ label: t.label, value: t.value }))
    },
    {
      label: 'Ujian',
      weight: 60,
      score: stats.avg_ujian || 0,
      maxScore: 100,
      items: (stats.ujian_details || []).map(u => ({ label: u.label, value: u.value }))
    }
  ]

  return cats.map(cat => {
    let slicedItems = cat.items.slice(0, 2)
    if (cat.items.length > 2) {
      slicedItems.push({ label: '...', value: '' })
    }
    return {
      ...cat,
      items: slicedItems
    }
  })
})

const averageScoreData = computed(() => {
  const score = overview.value.stats?.avg_keseluruhan || 0
  let label = 'Cukup Baik !'
  if (score >= 90) label = 'Sangat Baik !'
  else if (score >= 75) label = 'Baik !'
  else if (score < 60) label = 'Perlu Ditingkatkan !'

  return {
    title: `Rata-rata Nilai ${currentSubjectLabel.value}`,
    score: String(score),
    label,
    description: `Nilai Akhir Semester (Tugas 40% & Ujian 60%)`,
    percentage: score,
    variant: 'purple'
  }
})

const attendanceData = {
  title: 'Persentase Kehadiran',
  score: '98%',
  label: 'Sangat Baik !',
  description: 'Persentase absensi kehadiran kelas semester ini',
  percentage: 98,
  variant: 'emerald'
}
</script>

<template>
  <div class="space-y-6">
    <!-- Top Stats Overview -->
    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
      <StatCard 
        label="Nilai Rata-Rata (Seluruh Pelajaran)" 
        :value="formatNumber(globalStats.avg_all_subjects)"
        sub="Rata-rata raport seluruh pelajaran" 
        :icon="BookOpen" 
        illustration="apple" 
        variant="blue" 
      />

      <StatCard 
        label="Jumlah Tugas Terselesaikan" 
        :value="formatNumber(globalStats.total_tugas_completed)"
        sub="Total tugas terinput & dinilai" 
        :icon="ClipboardList" 
        illustration="open_book" 
        variant="violet" 
      />

      <StatCard 
        label="Nilai Ujian Rata-Rata" 
        :value="formatNumber(globalStats.avg_ujian_all)"
        sub="Rata-rata ujian harian, UTS & UAS" 
        :icon="BookOpen" 
        illustration="protractor" 
        variant="emerald" 
      />

      <StatCard 
        label="Nilai Tugas Rata-Rata" 
        :value="formatNumber(globalStats.avg_tugas_all)"
        sub="Rata-rata seluruh nilai tugas" 
        :icon="ClipboardList" 
        illustration="graded_paper" 
        variant="amber" 
      />
    </div>

    <!-- Header Filter Bar -->
    <SiswaSubjectFilterBar
      v-model:selected-classroom-id="selectedClassroomId"
      v-model:selected-subject-id="selectedSubjectId"
      :classrooms="classrooms"
      :subjects="subjects"
    />

    <!-- Main Subject Content -->
    <div v-if="selectedClassroomId && selectedSubjectId" class="space-y-6">
      <!-- UTS & UAS Score Cards -->
      <SiswaSubjectScoreCards
        :uts-score="overview.uts"
        :uas-score="overview.uas"
        :subject-name="currentSubjectLabel"
        :semester-label="currentSemesterLabel"
      />

      <!-- Two Column Layout: Materials (Left) & Activities (Right) -->
      <div class="grid gap-6 lg:grid-cols-2">
        <SiswaMaterialListCard
          v-model:selected-material-id="selectedMaterialId"
          :subject-name="currentSubjectLabel"
          :materials="overview.materials"
          :assessments="overview.assessments"
          @download="handleDownload"
        />

        <SiswaSubjectActivityCard
          :subject-name="currentSubjectLabel"
          :activities="allSubjectActivities"
          @open-sheet="showAllActivitiesSheet = true"
        />
      </div>

      <!-- Grade Detail & Progress Circles Section -->
      <SiswaSubjectGradeSection
        :subject-name="currentSubjectLabel"
        :formatted-categories="formattedCategories"
        :average-score-data="averageScoreData"
        :attendance-data="attendanceData"
      />
    </div>

    <!-- Empty State when filters are not selected -->
    <div v-else class="flex flex-col items-center justify-center py-20 text-center text-muted-foreground border border-dashed rounded-2xl min-h-[300px] glass-ui">
      <BookOpen class="size-12 mb-3 opacity-40 text-muted-foreground" />
      <h3 class="text-lg font-bold">Pilih Kelas, Semester dan Mata Pelajaran</h3>
      <p class="text-sm max-w-md">Silakan gunakan filter di atas untuk melihat materi pelajaran, aktivitas harian, serta pencapaian nilai UTS & UAS Anda.</p>
    </div>

    <!-- Slide-Over Sheet for All Activities -->
    <SiswaSubjectActivitySheet
      v-model:open="showAllActivitiesSheet"
      :subject-name="currentSubjectLabel"
      :activities="allSubjectActivities"
    />
  </div>
</template>
