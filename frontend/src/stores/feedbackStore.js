import { defineStore } from 'pinia'
import * as feedbackService from '@/services/feedbackService'

export const useFeedbackStore = defineStore('feedbackStore', {
  state: () => ({
    items: [],
    loading: false
  }),
  actions: {
    async fetchFeedbacks() {
      this.loading = true
      try {
        const data = await feedbackService.getFeedbacks()
        this.items = data
      } catch (error) {
        console.error('Failed to fetch feedbacks:', error)
      } finally {
        this.loading = false
      }
    },
    async add(item) {
      try {
        const newItem = await feedbackService.createFeedback(item)
        this.items.unshift(newItem)
        return newItem
      } catch (error) {
        console.error('Failed to create feedback:', error)
        throw error
      }
    },
    appendFeedback(feedback) {
      // Avoid duplicates
      if (!this.items.some(i => i.id === feedback.id)) {
        this.items.unshift(feedback)
      }
    },
    // No-op fallback for unused mock actions to prevent runtime errors
    update(id, updatedItem) {
      const index = this.items.findIndex(item => item.id === id)
      if (index !== -1) {
        this.items[index] = { ...this.items[index], ...updatedItem }
      }
    },
    delete(id) {
      this.items = this.items.filter(item => item.id !== id)
    }
  }
})
