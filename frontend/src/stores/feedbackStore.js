import { defineStore } from 'pinia'
import { mockFeedbacks } from '../modules/komunikasi/data/mockFeedbacks'

export const useFeedbackStore = defineStore('feedbackStore', {
  state: () => ({
    items: [...mockFeedbacks]
  }),
  actions: {
    add(item) {
      const maxId = this.items.reduce((max, i) => (typeof i.id === 'number' && i.id > max ? i.id : max), 0)
      const newItem = {
        ...item,
        id: maxId + 1,
        tanggal: new Date().toISOString().split('T')[0]
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
