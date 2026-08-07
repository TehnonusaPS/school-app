import { ref, computed, watch, onMounted } from 'vue'
import { fetchParentDashboard } from '@/services/academicCalendarService'
import { fetchTeacherAgendas } from '@/services/teacherAgendaService'
import { toast } from 'vue-sonner'

export function useJadwalAnak() {
  const loading = ref(false)
  const children = ref([])
  const selectedChildId = ref(null)
  const selectedChild = ref(null)

  // Main Section Tabs: 'jadwal' | 'ujian' | 'tugas' | 'kegiatan' | 'libur'
  const mainSectionTab = ref('jadwal')

  // Day tabs for weekly schedule (1: Senin, 2: Selasa, 3: Rabu, 4: Kamis, 5: Jumat, 6: Sabtu)
  const daysList = [
    { num: 1, name: 'Senin', short: 'Sen' },
    { num: 2, name: 'Selasa', short: 'Sel' },
    { num: 3, name: 'Rabu', short: 'Rab' },
    { num: 4, name: 'Kamis', short: 'Kam' },
    { num: 5, name: 'Jumat', short: 'Jum' },
    { num: 6, name: 'Sabtu', short: 'Sab' }
  ]

  // Default to current day of week (if Sunday, default to Monday=1)
  const todayDayNum = new Date().getDay() // 0 = Sunday
  const defaultDayTab = todayDayNum === 0 || todayDayNum === 7 ? 1 : todayDayNum
  const selectedDayTab = ref(defaultDayTab)

  const weeklySchedule = ref({})
  const upcomingExams = ref([])
  const upcomingEvents = ref([])
  const teacherAgendas = ref([])

  // Expanded exam ID for collapsible session list
  const expandedExamId = ref(null)

  const toggleExamExpand = (id) => {
    expandedExamId.value = expandedExamId.value === id ? null : id
  }

  const loadData = async () => {
    loading.value = true
    try {
      const params = {}
      if (selectedChildId.value) {
        params.child_id = selectedChildId.value
      }

      const [res, agendaRes] = await Promise.all([
        fetchParentDashboard(params),
        fetchTeacherAgendas(params)
      ])

      if (res && res.status === 'success' && res.data) {
        children.value = res.data.children || []
        selectedChild.value = res.data.selected_child || null
        if (res.data.selected_child && !selectedChildId.value) {
          selectedChildId.value = res.data.selected_child.id
        }
        weeklySchedule.value = res.data.weekly_schedule || {}
        upcomingExams.value = res.data.upcoming_exams || []
        upcomingEvents.value = res.data.upcoming_events || []
      }

      if (agendaRes && agendaRes.status === 'success' && agendaRes.data) {
        teacherAgendas.value = agendaRes.data || []
      }
    } catch (err) {
      console.error('Gagal memuat jadwal anak:', err)
      toast.error('Gagal memuat data jadwal anak.')
    } finally {
      loading.value = false
    }
  }

  // Filter out break slots (Jam Istirahat)
  const activeDayLessons = computed(() => {
    if (!weeklySchedule.value) return []
    const raw = weeklySchedule.value[selectedDayTab.value] || []
    return raw.filter(item => !item.is_break)
  })

  // Categorize teacher agendas for tabs
  const tugasList = computed(() => {
    return teacherAgendas.value.filter(a => a.type === 'tugas')
  })

  const kegiatanList = computed(() => {
    return teacherAgendas.value.filter(a => a.type === 'kegiatan')
  })

  const ujianHarianList = computed(() => {
    return teacherAgendas.value.filter(a => a.type === 'ujian_harian')
  })

  watch(selectedChildId, (newId, oldId) => {
    if (newId && oldId && newId !== oldId) {
      loadData()
    }
  })

  onMounted(() => {
    loadData()
  })

  const formatDateIndo = (dateStr) => {
    if (!dateStr) return ''
    try {
      const d = new Date(dateStr)
      if (isNaN(d.getTime())) return dateStr
      return d.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
      })
    } catch {
      return dateStr
    }
  }

  const formatDayName = (dateStr) => {
    if (!dateStr) return ''
    try {
      const d = new Date(dateStr)
      if (isNaN(d.getTime())) return ''
      return d.toLocaleDateString('id-ID', { weekday: 'long' })
    } catch {
      return ''
    }
  }

  return {
    loading,
    children,
    selectedChildId,
    selectedChild,
    mainSectionTab,
    daysList,
    selectedDayTab,
    activeDayLessons,
    weeklySchedule,
    upcomingExams,
    upcomingEvents,
    teacherAgendas,
    tugasList,
    kegiatanList,
    ujianHarianList,
    expandedExamId,
    toggleExamExpand,
    formatDateIndo,
    formatDayName,
    loadData
  }
}
