import { defineStore } from 'pinia'
import { mockRuangan } from '@/modules/lainnya/data/mockRuangan'

export const useRuanganStore = defineStore('ruangan', {
  state: () => ({
    items: [...mockRuangan]
  }),
  actions: {
    add(item) {
      const newItem = {
        ...item,
        id: Date.now().toString()
      }
      this.items.unshift(newItem)
      return newItem
    },
    update(id, updatedItem) {
      const index = this.items.findIndex(item => item.id === id)
      if (index !== -1) {
        this.items[index] = { ...this.items[index], ...updatedItem }
        return this.items[index]
      }
      return null
    },
    delete(id) {
      this.items = this.items.filter(item => item.id !== id)
    },
    getById(id) {
      return this.items.find(item => item.id === id)
    }
  }
})
