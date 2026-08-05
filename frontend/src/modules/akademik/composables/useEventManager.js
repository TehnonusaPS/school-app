import { ref, computed } from 'vue'
import { toast } from 'vue-sonner'
import { eventTypes } from '../data/calendarConstants'

export function useEventManager(events = ref([]), classrooms = ref([])) {
  const isDialogOpen = ref(false)
  const dialogMode = ref('add') // 'add' | 'edit'

  const form = ref({
    id: '',
    startDate: '',
    endDate: '',
    title: '',
    type: 'kegiatan',
    classroom_id: 'all',
    description: ''
  })

  const formErrors = ref({
    startDate: '',
    endDate: '',
    title: '',
    type: ''
  })

  const selectedCategoryFilter = ref('all')

  const filteredEventsList = computed(() => {
    const list = events.value || []
    if (selectedCategoryFilter.value === 'all') return list
    const categoryTypes = eventTypes.filter(t => t.category === selectedCategoryFilter.value).map(t => t.value)
    return list.filter(e => categoryTypes.includes(e.type))
  })

  function openAddDialog(defaultDateStr = '') {
    dialogMode.value = 'add'
    form.value = {
      id: '',
      startDate: defaultDateStr || '2026-07-05',
      endDate: defaultDateStr || '2026-07-05',
      title: '',
      type: 'kegiatan',
      classroom_id: 'all',
      description: ''
    }
    formErrors.value = { startDate: '', endDate: '', title: '', type: '' }
    isDialogOpen.value = true
  }

  function openEditDialog(ev) {
    dialogMode.value = 'edit'
    form.value = {
      id: ev.id,
      startDate: ev.startDate,
      endDate: ev.endDate,
      title: ev.title,
      type: ev.type || 'kegiatan',
      classroom_id: ev.classroom_id ? String(ev.classroom_id) : 'all',
      description: ev.description || ''
    }
    formErrors.value = { startDate: '', endDate: '', title: '', type: '' }
    isDialogOpen.value = true
  }

  function handleSaveEvent() {
    formErrors.value = { startDate: '', endDate: '', title: '', type: '' }

    if (!form.value.title.trim()) formErrors.value.title = 'Judul agenda wajib diisi'
    if (!form.value.startDate) formErrors.value.startDate = 'Tanggal mulai wajib diisi'
    if (!form.value.endDate) formErrors.value.endDate = 'Tanggal selesai wajib diisi'

    if (formErrors.value.title || formErrors.value.startDate || formErrors.value.endDate) return

    const classList = classrooms.value || []
    const selectedClass = classList.find(c => String(c.id) === String(form.value.classroom_id))

    if (dialogMode.value === 'add') {
      const newEv = {
        id: 'draft-' + Date.now() + '-' + Math.random().toString(36).substring(2, 6),
        title: form.value.title,
        startDate: form.value.startDate,
        endDate: form.value.endDate,
        type: form.value.type,
        classroom_id: form.value.classroom_id === 'all' ? null : parseInt(form.value.classroom_id),
        classroom_name: form.value.classroom_id === 'all' ? 'Semua Kelas' : (selectedClass?.name || ''),
        description: form.value.description
      }
      events.value.push(newEv)
      toast.success('Agenda ditambahkan ke draf kalender.')
    } else {
      const idx = events.value.findIndex(e => e.id === form.value.id)
      if (idx !== -1) {
        events.value[idx] = {
          ...events.value[idx],
          title: form.value.title,
          startDate: form.value.startDate,
          endDate: form.value.endDate,
          type: form.value.type,
          classroom_id: form.value.classroom_id === 'all' ? null : parseInt(form.value.classroom_id),
          classroom_name: form.value.classroom_id === 'all' ? 'Semua Kelas' : (selectedClass?.name || ''),
          description: form.value.description
        }
        toast.success('Agenda draf diperbarui.')
      }
    }

    isDialogOpen.value = false
  }

  function handleDeleteEvent(id) {
    events.value = events.value.filter(e => e.id !== id)
    toast.success('Agenda dihapus dari draf.')
    isDialogOpen.value = false
  }

  return {
    isDialogOpen,
    dialogMode,
    form,
    formErrors,
    selectedCategoryFilter,
    filteredEventsList,
    openAddDialog,
    openEditDialog,
    handleSaveEvent,
    handleDeleteEvent
  }
}
