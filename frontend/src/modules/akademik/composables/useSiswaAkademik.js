import { ref, computed } from 'vue'
import { toast } from 'vue-sonner'
import * as siswaAkademikService from '@/services/siswaAkademikService'

export function useSiswaAkademik() {
  const classrooms = ref([])
  const subjects = ref([])
  const overview = ref({
    materials: [],
    assessments: [],
    uts: null,
    uas: null,
    stats: {
      avg_tugas: 0,
      avg_ujian: 0,
      avg_keseluruhan: 0,
      tugas_count: 0,
      ujian_count: 0,
      tugas_details: [],
      ujian_details: []
    }
  })
  
  const globalStats = ref({
    avg_all_subjects: 0,
    total_tugas_completed: 0,
    avg_ujian_all: 0,
    avg_tugas_all: 0
  })

  const isLoading = ref(false)
  const selectedClassroomId = ref('')
  const selectedSubjectId = ref('')
  const selectedMaterialId = ref('all') // 'all' or material ID

  const fetchClassrooms = async () => {
    isLoading.value = true
    try {
      const res = await siswaAkademikService.getMyClassrooms()
      classrooms.value = res.data
      if (classrooms.value.length > 0) {
        const current = classrooms.value.find(c => c.is_current) || classrooms.value[0]
        selectedClassroomId.value = String(current.classroom_id)
      }
    } catch (err) {
      console.error(err)
      toast.error('Gagal memuat daftar kelas.')
    } finally {
      isLoading.value = false
    }
  }

  const fetchSubjects = async (classroomId) => {
    if (!classroomId) return
    isLoading.value = true
    try {
      const res = await siswaAkademikService.getSubjects(classroomId)
      subjects.value = res.data
      if (subjects.value.length > 0) {
        selectedSubjectId.value = String(subjects.value[0].id)
      } else {
        selectedSubjectId.value = ''
      }
    } catch (err) {
      console.error(err)
      toast.error('Gagal memuat mata pelajaran.')
    } finally {
      isLoading.value = false
    }
  }

  const fetchOverview = async (subjectId, classroomId) => {
    if (!subjectId || !classroomId) return
    isLoading.value = true
    try {
      const res = await siswaAkademikService.getSubjectOverview(subjectId, classroomId)
      overview.value = res.data
      selectedMaterialId.value = 'all'
    } catch (err) {
      console.error(err)
      toast.error('Gagal memuat detail pelajaran.')
    } finally {
      isLoading.value = false
    }
  }

  const fetchStats = async (classroomId) => {
    if (!classroomId) return
    try {
      const res = await siswaAkademikService.getGlobalStats(classroomId)
      globalStats.value = res.data
    } catch (err) {
      console.error(err)
    }
  }

  const handleDownload = async (materialId, fileName) => {
    try {
      toast.info('Memulai unduhan berkas...')
      const response = await siswaAkademikService.downloadMaterial(materialId)
      const blob = new Blob([response.data], { type: response.headers['content-type'] })
      const link = document.createElement('a')
      link.href = window.URL.createObjectURL(blob)
      link.download = fileName || 'Materi.pdf'
      link.click()
      window.URL.revokeObjectURL(link.href)
      toast.success('Unduhan berhasil!')
    } catch (err) {
      console.error(err)
      toast.error('Gagal mengunduh materi.')
    }
  }

  return {
    classrooms,
    subjects,
    overview,
    globalStats,
    isLoading,
    selectedClassroomId,
    selectedSubjectId,
    selectedMaterialId,
    fetchClassrooms,
    fetchSubjects,
    fetchOverview,
    fetchStats,
    handleDownload
  }
}
