import { defineStore } from 'pinia'
import { ref } from 'vue'
import { fetchAllSuratAktif, createSuratAktif, updateSuratAktif, deleteSuratAktif } from '@/services/suratAktifService'

export const useSuratAktifStore = defineStore('suratAktif', () => {
  const items = ref([])
  const isLoading = ref(false)

  async function fetchItems(params) {
    isLoading.value = true
    try {
      const data = await fetchAllSuratAktif(params)
      items.value = data
      return data
    } finally {
      isLoading.value = false
    }
  }

  function getById(id) {
    return items.value.find(item => item.id === parseInt(id))
  }

  async function add(data) {
    // Expected database format: snake_case for parameters
    const payload = {
      student_id: parseInt(data.studentId),
      academic_year_id: parseInt(data.academicYearId),
      semester: data.semester,
      kelas: data.kelas,
      tanggal_lahir: data.tanggalLahir,
      alamat: data.alamat,
      status: data.status || 'Selesai'
    }

    // service returns response.data = { message, data: {...} }
    const res = await createSuratAktif(payload)
    const newItem = res.data ?? res
    items.value.unshift(newItem)
    return newItem
  }

  async function update(id, data) {
    const payload = {
      academic_year_id: parseInt(data.academicYearId),
      semester: data.semester,
      kelas: data.kelas,
      tanggal_lahir: data.tanggalLahir,
      alamat: data.alamat,
      status: data.status ?? undefined,
    }

    // Sertakan student_id jika user mengganti siswa (selectedSiswaId ada dan bukan -1)
    if (data.studentId && parseInt(data.studentId) > 0) {
      payload.student_id = parseInt(data.studentId)
    }

    // service returns response.data = { message, data: {...} }
    const res = await updateSuratAktif(id, payload)
    const updatedItem = res.data ?? res
    const index = items.value.findIndex(item => item.id === parseInt(id))
    if (index !== -1) {
      // splice memastikan Vue reactivity ter-trigger dengan benar
      items.value.splice(index, 1, updatedItem)
    }
    return updatedItem
  }

  async function remove(id) {
    await deleteSuratAktif(id)
    const index = items.value.findIndex(item => item.id === parseInt(id))
    if (index !== -1) {
      items.value.splice(index, 1)
      return true
    }
    return false
  }

  return {
    items,
    isLoading,
    fetchItems,
    getById,
    add,
    update,
    remove
  }
})
