import { defineStore } from 'pinia'
import { mockBeritaKegiatan } from '../modules/komunikasi/data/mockBeritaKegiatan'

export const useBeritaKegiatanStore = defineStore('beritaKegiatan', {
  state: () => ({
    items: [...mockBeritaKegiatan]
  }),
  actions: {
    add(item) {
      const newItem = {
        ...item,
        id: `BK-${String(this.items.length + 1).padStart(3, '0')}`
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
