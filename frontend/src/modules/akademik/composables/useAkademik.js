import { ref } from 'vue'
import { toast } from 'vue-sonner'
import * as akademikService from '@/services/akademikService'

export function useAkademik() {
  const subjects = ref([])
  const classrooms = ref([])
  const students = ref([])
  const activeAcademicYear = ref(null)

  const materiList = ref([])
  const tugasList = ref([])
  const ujianList = ref([])

  const isLoading = ref(false)
  const isSubmitting = ref(false)

  // Draft form states for preservation
  const draftForm = ref({
    materi: { classroom_id: '', title: '', file: null },
    tugas: { classroom_id: '', type: '', title: '', scores: {} },
    ujian: { classroom_id: '', type: '', title: '', scores: {} },
  })

  // ─── Data Lookup ────────────────────────────────────

  const fetchActiveAcademicYear = async () => {
    try {
      const res = await akademikService.getActiveAcademicYear()
      activeAcademicYear.value = res.data
    } catch (err) {
      console.error(err)
    }
  }

  const fetchSubjects = async () => {
    isLoading.value = true
    try {
      const res = await akademikService.getMySubjects()
      subjects.value = res.data
    } catch (err) {
      handleApiError(err, 'memuat mata pelajaran')
    } finally {
      isLoading.value = false
    }
  }

  const fetchClassrooms = async (subjectId) => {
    if (!subjectId) return
    try {
      const res = await akademikService.getMyClassrooms(subjectId)
      classrooms.value = res.data
    } catch (err) {
      handleApiError(err, 'memuat kelas')
    }
  }

  const fetchStudents = async (classroomId) => {
    if (!classroomId) {
      students.value = []
      return
    }
    try {
      const res = await akademikService.getStudentsByClassroom(classroomId)
      students.value = res.data
    } catch (err) {
      handleApiError(err, 'memuat daftar siswa')
    }
  }

  // ─── List Fetching ──────────────────────────────────

  const fetchMaterials = async (subjectId, filters = {}) => {
    if (!subjectId) return
    isLoading.value = true
    try {
      const res = await akademikService.getMaterials({
        subject_id: subjectId,
        ...filters
      })
      materiList.value = res.data
    } catch (err) {
      handleApiError(err, 'memuat daftar materi')
    } finally {
      isLoading.value = false
    }
  }

  const fetchAssessments = async (subjectId, category, filters = {}) => {
    if (!subjectId) return
    isLoading.value = true
    try {
      const res = await akademikService.getAssessments({
        subject_id: subjectId,
        category,
        ...filters
      })
      if (category === 'tugas') {
        tugasList.value = res.data
      } else {
        ujianList.value = res.data
      }
    } catch (err) {
      handleApiError(err, `memuat daftar ${category}`)
    } finally {
      isLoading.value = false
    }
  }

  // ─── CRUD Actions ───────────────────────────────────

  const saveMaterial = async (formData) => {
    isSubmitting.value = true
    try {
      await akademikService.createMaterial(formData)
      toast.success('Materi baru berhasil diupload!')
      clearDraft('materi')
      return true
    } catch (err) {
      handleApiError(err, 'mengunggah materi')
      return false
    } finally {
      isSubmitting.value = false
    }
  }

  const updateMaterial = async (id, formData) => {
    isSubmitting.value = true
    try {
      await akademikService.updateMaterial(id, formData)
      toast.success('Materi berhasil diubah!')
      return true
    } catch (err) {
      handleApiError(err, 'mengubah materi')
      return false
    } finally {
      isSubmitting.value = false
    }
  }

  const saveAssessment = async (data) => {
    const label = data.category === 'tugas' ? 'Tugas' : 'Ujian'
    isSubmitting.value = true
    try {
      await akademikService.createAssessment(data)
      toast.success(`${label} baru berhasil disimpan!`)
      clearDraft(data.category)
      return true
    } catch (err) {
      handleApiError(err, `menyimpan ${label.toLowerCase()}`)
      return false
    } finally {
      isSubmitting.value = false
    }
  }

  const updateAssessment = async (id, data) => {
    const label = data.category === 'tugas' ? 'Tugas' : 'Ujian'
    isSubmitting.value = true
    try {
      await akademikService.updateAssessment(id, data)
      toast.success(`${label} berhasil diupdate!`)
      return true
    } catch (err) {
      handleApiError(err, `mengupdate ${label.toLowerCase()}`)
      return false
    } finally {
      isSubmitting.value = false
    }
  }

  const deleteItem = async (type, id) => {
    try {
      if (type === 'materi') {
        await akademikService.deleteMaterial(id)
      } else {
        await akademikService.deleteAssessment(id)
      }
      toast.success('Data berhasil dihapus!')
      return true
    } catch (err) {
      handleApiError(err, 'menghapus data')
      return false
    }
  }

  const toggleStatus = async (type, id) => {
    try {
      if (type === 'materi') {
        await akademikService.toggleMaterialStatus(id)
      } else {
        await akademikService.toggleAssessmentStatus(id)
      }
      toast.success('Status berhasil diubah!')
      return true
    } catch (err) {
      handleApiError(err, 'mengubah status')
      return false
    }
  }

  const handleDownload = async (id) => {
    try {
      const res = await akademikService.downloadMaterial(id)
      const blob = new Blob([res.data], { type: res.headers['content-type'] })
      const url = window.URL.createObjectURL(blob)
      const link = document.createElement('a')
      link.href = url

      // Try parsing filename from Content-Disposition header
      let filename = 'materi.pdf'
      const contentDisposition = res.headers['content-disposition']
      if (contentDisposition) {
        const matches = /filename="?([^"]+)"?/g.exec(contentDisposition)
        if (matches && matches[1]) {
          filename = matches[1]
        }
      }
      link.setAttribute('download', filename)
      document.body.appendChild(link)
      link.click()
      link.remove()
      window.URL.revokeObjectURL(url)
      toast.success('File berhasil diunduh.')
    } catch (err) {
      handleApiError(err, 'mengunduh materi')
    }
  }

  // ─── Error Helper ───────────────────────────────────

  const handleApiError = (err, context) => {
    console.error(err)
    if (err.response?.status === 422) {
      const errors = err.response.data.errors
      const firstField = Object.keys(errors)[0]
      const firstMsg = errors[firstField][0]
      toast.error(`Gagal ${context}`, {
        description: `${firstField}: ${firstMsg}`
      })
    } else if (err.response?.status === 403) {
      toast.error('Akses ditolak', {
        description: err.response.data.message || 'Anda tidak memiliki hak akses.'
      })
    } else if (err.response?.status === 413) {
      toast.error('File terlalu besar', {
        description: 'Ukuran berkas melebihi batas maksimal 15MB.'
      })
    } else {
      toast.error(`Gagal ${context}`, {
        description: err.response?.data?.message || 'Terjadi kesalahan sistem.'
      })
    }
  }

  // ─── Draft Management ───────────────────────────────

  const clearDraft = (tab) => {
    if (tab === 'materi') {
      draftForm.value.materi = { classroom_id: '', title: '', file: null }
    } else if (tab === 'tugas') {
      draftForm.value.tugas = { classroom_id: '', type: '', title: '', scores: {} }
    } else if (tab === 'ujian') {
      draftForm.value.ujian = { classroom_id: '', type: '', title: '', scores: {} }
    }
  }

  const saveDraft = (tab, data) => {
    draftForm.value[tab] = { ...data }
  }

  return {
    subjects,
    classrooms,
    students,
    activeAcademicYear,
    materiList,
    tugasList,
    ujianList,
    isLoading,
    isSubmitting,
    draftForm,
    fetchActiveAcademicYear,
    fetchSubjects,
    fetchClassrooms,
    fetchStudents,
    fetchMaterials,
    fetchAssessments,
    saveMaterial,
    updateMaterial,
    saveAssessment,
    updateAssessment,
    deleteItem,
    toggleStatus,
    handleDownload,
    clearDraft,
    saveDraft,
  }
}
