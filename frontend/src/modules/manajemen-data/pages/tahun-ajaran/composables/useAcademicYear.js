import { ref, computed, watch } from 'vue'
import { toast } from 'vue-sonner'
import { usePagination } from '@/composables/usePagination'
import {
  fetchAllAcademicYears,
  updateAcademicYear,
  deleteAcademicYear
} from '@/services/academicYearService'

export function useAcademicYear() {
  const dbItems = ref([])
  const perPage = ref(5)
  const filterValues = ref({
    search: '',
    status: 'all'
  })
  const isLoading = ref(false)

  async function fetchData() {
    isLoading.value = true
    try {
      const res = await fetchAllAcademicYears()
      const rawList = res.data || []

      const map = {}
      rawList.forEach(item => {
        const yearName = item.name
        if (!map[yearName]) {
          map[yearName] = {
            id: yearName,
            tahun: yearName,
            odd: null,
            even: null,
            ids: [],
            status: 'nonaktif'
          }
        }
        map[yearName].ids.push(item.id)

        const start = item.start_date ? item.start_date.substring(0, 10) : ''
        const end = item.end_date ? item.end_date.substring(0, 10) : ''
        const active = Boolean(item.is_active)

        if (item.semester === 'odd') {
          map[yearName].odd = { id: item.id, tanggalMulai: start, tanggalSelesai: end, isActive: active }
        } else if (item.semester === 'even') {
          map[yearName].even = { id: item.id, tanggalMulai: start, tanggalSelesai: end, isActive: active }
        }

        if (active) {
          map[yearName].status = 'aktif'
        }
      })

      dbItems.value = Object.values(map).map(group => ({
        id: group.tahun,
        tahun: group.tahun,
        ids: group.ids,
        odd: group.odd,
        even: group.even,
        tanggalMulai: group.odd?.tanggalMulai || group.even?.tanggalMulai || '',
        tanggalSelesai: group.even?.tanggalSelesai || group.odd?.tanggalSelesai || '',
        status: group.status
      }))
    } catch (err) {
      toast.error('Gagal mengambil data tahun ajaran')
    } finally {
      isLoading.value = false
    }
  }

  // Stats
  const totalCount = computed(() => dbItems.value.length)
  const aktifCount = computed(() => dbItems.value.filter(t => t.status === 'aktif').length)
  const nonaktifCount = computed(() => dbItems.value.filter(t => t.status === 'nonaktif').length)

  // Filters & Search
  const filteredItems = computed(() => {
    return dbItems.value.filter(item => {
      const searchVal = filterValues.value.search?.trim().toLowerCase() || ''
      const searchMatch = !searchVal || item.tahun.toLowerCase().includes(searchVal)

      const statusVal = filterValues.value.status
      const statusMatch = !statusVal || statusVal === 'all' || item.status === statusVal

      return searchMatch && statusMatch
    })
  })

  const { currentPage, total, from, to, paginatedItems } = usePagination(filteredItems, perPage)

  watch(filteredItems, () => {
    currentPage.value = 1
  })

  // Table config
  const columns = [
    { key: 'tahun', label: 'Tahun Ajaran' },
    { key: 'tanggalMulai', label: 'Tanggal Mulai (Ganjil)' },
    { key: 'tanggalSelesai', label: 'Tanggal Selesai (Genap)' },
    {
      key: 'status',
      label: 'Status',
      badge: true,
      badgeVariant: {
        aktif: 'green',
        nonaktif: 'gray'
      }
    },
    { key: 'actions', label: 'Aksi' }
  ]

  const filters = [
    {
      type: 'search',
      key: 'search',
      placeholder: 'Cari tahun ajaran...'
    },
    {
      type: 'select',
      key: 'status',
      label: 'Status:',
      placeholder: 'Semua Status',
      options: [
        { label: 'Aktif', value: 'aktif' },
        { label: 'Nonaktif', value: 'nonaktif' }
      ]
    }
  ]

  // Helpers
  function getStatusLabel(status) {
    return status === 'aktif' ? 'Aktif' : 'Nonaktif'
  }

  function getStatusBadgeVariant(status) {
    return status === 'aktif' ? 'green' : 'gray'
  }

  function formatDate(dateStr) {
    if (!dateStr) return '-'
    const d = new Date(dateStr)
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
  }

  // Toggle status (activation)
  async function handleToggleStatus(item) {
    if (item.status === 'aktif') {
      toast.warning('Aksi Dibatalkan', { description: 'Harus ada minimal satu tahun ajaran yang aktif pada sistem.' })
      return
    }

    try {
      const targetId = item.odd?.id || item.even?.id || (item.ids && item.ids[0]) || item.id
      await updateAcademicYear(targetId, { is_active: true })
      toast.success('Tahun Ajaran Diaktifkan', {
        description: `Tahun ajaran "${item.tahun}" (Ganjil & Genap) kini aktif. Tahun ajaran lainnya telah dinonaktifkan.`
      })
      await fetchData()
    } catch (err) {
      toast.error('Gagal mengaktifkan tahun ajaran')
    }
  }

  // Delete
  async function executeDelete(item) {
    if (!item) return
    try {
      const ids = item.ids || [item.id]
      for (const id of ids) {
        await deleteAcademicYear(id)
      }
      toast.success('Berhasil Dihapus', { description: `Tahun ajaran "${item.tahun}" telah dihapus.` })
      await fetchData()
    } catch (err) {
      toast.error('Gagal menghapus tahun ajaran')
    }
  }

  return {
    dbItems,
    isLoading,
    perPage,
    filterValues,
    currentPage,
    total,
    from,
    to,
    paginatedItems,
    totalCount,
    aktifCount,
    nonaktifCount,
    columns,
    filters,
    fetchData,
    getStatusLabel,
    getStatusBadgeVariant,
    formatDate,
    handleToggleStatus,
    executeDelete
  }
}
