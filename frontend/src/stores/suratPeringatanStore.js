import { defineStore } from 'pinia'
import { ref } from 'vue'
import {
  fetchAllSuratPeringatan,
  createSuratPeringatan,
  updateSuratPeringatan,
  deleteSuratPeringatan
} from '@/services/studentWarningService'

export const useSuratPeringatanStore = defineStore('suratPeringatan', () => {
  const items = ref([])
  const isLoading = ref(false)

  async function fetchItems(params) {
    isLoading.value = true
    try {
      const data = await fetchAllSuratPeringatan(params)
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
    const res = await createSuratPeringatan(data)
    const newItem = res.data ?? res
    items.value.unshift(newItem)
    return newItem
  }

  async function update(id, data) {
    const res = await updateSuratPeringatan(id, data)
    const updatedItem = res.data ?? res
    const index = items.value.findIndex(item => item.id === parseInt(id))
    if (index !== -1) {
      items.value.splice(index, 1, updatedItem)
    }
    return updatedItem
  }

  async function remove(id) {
    await deleteSuratPeringatan(id)
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
