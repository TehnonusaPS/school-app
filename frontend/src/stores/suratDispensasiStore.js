import { defineStore } from 'pinia'
import { ref } from 'vue'
import {
  fetchAllSuratDispensasi,
  createSuratDispensasi,
  updateSuratDispensasi,
  deleteSuratDispensasi
} from '@/services/studentDispensationService'

export const useSuratDispensasiStore = defineStore('suratDispensasi', () => {
  const items = ref([])
  const isLoading = ref(false)

  async function fetchItems(params) {
    isLoading.value = true
    try {
      const data = await fetchAllSuratDispensasi(params)
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
    // Expected database format: camelCase to snake_case is handled in controller, 
    // but the payload format is:
    // {
    //   tanggalAwal: "YYYY-MM-DD",
    //   tanggalAkhir: "YYYY-MM-DD",
    //   perihal: "text",
    //   siswa: [ { student_id, nama, nisn, kelas }, ... ]
    // }
    const res = await createSuratDispensasi(data)
    const newItem = res.data ?? res
    items.value.unshift(newItem)
    return newItem
  }

  async function update(id, data) {
    const res = await updateSuratDispensasi(id, data)
    const updatedItem = res.data ?? res
    const index = items.value.findIndex(item => item.id === parseInt(id))
    if (index !== -1) {
      items.value.splice(index, 1, updatedItem)
    }
    return updatedItem
  }

  async function remove(id) {
    await deleteSuratDispensasi(id)
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
