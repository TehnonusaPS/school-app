import { ref, reactive } from 'vue'
import { toast } from 'vue-sonner'
import {
  fetchMyClassrooms,
  createTeacherAgenda,
  updateTeacherAgenda,
  deleteTeacherAgenda
} from '@/services/teacherAgendaService'

export function useTeacherAgenda(onSuccessCallback) {
  const isSheetOpen = ref(false)
  const dialogMode = ref('add') // 'add' | 'edit'
  const isSaving = ref(false)
  const isFromCalendarCell = ref(false)

  const classroomOptions = ref([])
  const subjectOptions = ref([])
  const loadingOptions = ref(false)

  const initialForm = {
    id: null,
    title: '',
    type: 'tugas',
    date: '',
    classroom_id: 'all',
    subject_id: 'all',
    description: ''
  }

  const form = reactive({ ...initialForm })
  const formErrors = reactive({})

  const resetForm = () => {
    Object.assign(form, initialForm)
    Object.keys(formErrors).forEach(key => delete formErrors[key])
  }

  const loadOptions = async () => {
    loadingOptions.value = true
    try {
      const res = await fetchMyClassrooms()
      if (res && res.status === 'success' && res.data) {
        classroomOptions.value = [
          { value: 'all', label: 'Semua Kelas Saya' },
          ...(res.data.classrooms || []).map(c => ({ value: String(c.id), label: c.name }))
        ]
        subjectOptions.value = [
          { value: 'all', label: 'Umum / Tidak Spesifik' },
          ...(res.data.subjects || []).map(s => ({ value: String(s.id), label: `${s.name} ${s.code ? `(${s.code})` : ''}` }))
        ]
      }
    } catch (e) {
      console.error('Failed to load teacher classroom options', e)
    } finally {
      loadingOptions.value = false
    }
  }

  const openAddDialog = (prefillDate = null, fromCalendarCell = false) => {
    resetForm()
    dialogMode.value = 'add'
    isFromCalendarCell.value = fromCalendarCell

    if (prefillDate) {
      const d = prefillDate instanceof Date ? prefillDate : new Date(prefillDate)
      const y = d.getFullYear()
      const m = String(d.getMonth() + 1).padStart(2, '0')
      const day = String(d.getDate()).padStart(2, '0')
      form.date = `${y}-${m}-${day}`
    } else {
      const today = new Date()
      const y = today.getFullYear()
      const m = String(today.getMonth() + 1).padStart(2, '0')
      const day = String(today.getDate()).padStart(2, '0')
      form.date = `${y}-${m}-${day}`
    }
    loadOptions()
    isSheetOpen.value = true
  }

  const openEditDialog = (agenda) => {
    resetForm()
    dialogMode.value = 'edit'
    isFromCalendarCell.value = false
    form.id = agenda.id
    form.title = agenda.title || ''
    form.type = agenda.type || 'tugas'
    form.date = agenda.date || ''
    form.classroom_id = agenda.classroom_id ? String(agenda.classroom_id) : 'all'
    form.subject_id = agenda.subject_id ? String(agenda.subject_id) : 'all'
    form.description = agenda.description || ''
    loadOptions()
    isSheetOpen.value = true
  }

  const saveAgenda = async () => {
    Object.keys(formErrors).forEach(key => delete formErrors[key])

    if (!form.title.trim()) {
      formErrors.title = 'Judul agenda wajib diisi.'
      return
    }
    if (!form.date) {
      formErrors.date = 'Tanggal agenda wajib diisi.'
      return
    }

    isSaving.value = true
    try {
      const payload = {
        title: form.title,
        type: form.type,
        date: form.date,
        classroom_id: (form.classroom_id && form.classroom_id !== 'all') ? form.classroom_id : null,
        subject_id: (form.subject_id && form.subject_id !== 'all') ? form.subject_id : null,
        description: form.description || null
      }

      if (dialogMode.value === 'add') {
        const res = await createTeacherAgenda(payload)
        if (res && res.status === 'success') {
          toast.success('Agenda berhasil ditambahkan!')
          isSheetOpen.value = false
          if (onSuccessCallback) onSuccessCallback()
        }
      } else {
        const res = await updateTeacherAgenda(form.id, payload)
        if (res && res.status === 'success') {
          toast.success('Agenda berhasil diperbarui!')
          isSheetOpen.value = false
          if (onSuccessCallback) onSuccessCallback()
        }
      }
    } catch (e) {
      console.error('Failed to save agenda', e)
      if (e.response && e.response.data && e.response.data.errors) {
        Object.assign(formErrors, e.response.data.errors)
      } else {
        toast.error(e.response?.data?.message || 'Gagal menyimpan agenda.')
      }
    } finally {
      isSaving.value = false
    }
  }

  const handleDelete = async (id) => {
    if (!confirm('Apakah Anda yakin ingin menghapus agenda ini?')) return
    try {
      const res = await deleteTeacherAgenda(id)
      if (res && res.status === 'success') {
        toast.success('Agenda berhasil dihapus.')
        if (onSuccessCallback) onSuccessCallback()
      }
    } catch (e) {
      console.error('Failed to delete agenda', e)
      toast.error('Gagal menghapus agenda.')
    }
  }

  return {
    isSheetOpen,
    dialogMode,
    isSaving,
    isFromCalendarCell,
    form,
    formErrors,
    classroomOptions,
    subjectOptions,
    loadingOptions,
    openAddDialog,
    openEditDialog,
    saveAgenda,
    handleDelete
  }
}
