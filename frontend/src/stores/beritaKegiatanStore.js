import { defineStore } from 'pinia'
import { ref } from 'vue'
import {
  fetchActivityNews,
  createActivityNews,
  updateActivityNews,
  deleteActivityNews
} from '@/services/activityNewsService'

export const useBeritaKegiatanStore = defineStore('beritaKegiatan', () => {
  const items = ref([])
  const isLoading = ref(false)

  async function fetchItems(params) {
    isLoading.value = true
    try {
      const data = await fetchActivityNews(params)
      items.value = data
      return data
    } finally {
      isLoading.value = false
    }
  }

  function getById(id) {
    // Both string and int IDs should be supported
    return items.value.find(item => String(item.id) === String(id))
  }

  async function add(item) {
    const res = await createActivityNews(item)
    const newItem = res.data ?? res
    items.value.unshift(newItem)
    return newItem
  }

  async function update(id, updatedItem) {
    const res = await updateActivityNews(id, updatedItem)
    const newItem = res.data ?? res
    const index = items.value.findIndex(item => String(item.id) === String(id))
    if (index !== -1) {
      items.value.splice(index, 1, newItem)
    }
    return newItem
  }

  async function remove(id) {
    await deleteActivityNews(id)
    items.value = items.value.filter(item => String(item.id) !== String(id))
  }

  return {
    items,
    isLoading,
    fetchItems,
    getById,
    add,
    update,
    delete: remove
  }
})
