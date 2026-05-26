import { defineStore } from 'pinia'
import { ref } from 'vue'
import { mockSuratDispensasi } from '../modules/komunikasi/data/mockSuratDispensasi'

export const useSuratDispensasiStore = defineStore('suratDispensasi', () => {
  const items = ref([...mockSuratDispensasi])

  function getAll() {
    return items.value
  }

  function getById(id) {
    return items.value.find(item => item.id === parseInt(id))
  }

  function add(data) {
    const newId = items.value.length > 0 ? Math.max(...items.value.map(i => i.id)) + 1 : 1
    const hariIni = new Date().toISOString().split('T')[0]
    
    const newItem = {
      id: newId,
      tanggalDibuat: hariIni,
      ...data
    }
    
    // Add to the beginning of the list
    items.value.unshift(newItem)
    return newItem
  }

  function update(id, data) {
    const index = items.value.findIndex(item => item.id === parseInt(id))
    if (index !== -1) {
      items.value[index] = { ...items.value[index], ...data }
      return items.value[index]
    }
    return null
  }

  function remove(id) {
    const index = items.value.findIndex(item => item.id === parseInt(id))
    if (index !== -1) {
      items.value.splice(index, 1)
      return true
    }
    return false
  }

  return {
    items,
    getAll,
    getById,
    add,
    update,
    remove
  }
})
