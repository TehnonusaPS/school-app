import { defineStore } from 'pinia'

export const useAsetStore = defineStore('aset', {
  state: () => ({
    items: [
      { id: '1', code: 'TV-01', name: 'TV Samsung 32"', category: 'elektronik', brand: 'Samsung', building: 'Gedung Utama', floor: '1', room: 'Laboratorium Multimedia', condition: 'baik', date: '2023-01-01', value: '5000000' },
      { id: '2', code: 'TV-02', name: 'TV LG 43"', category: 'elektronik', brand: 'LG', building: 'Gedung Utama', floor: '1', room: 'Laboratorium Multimedia', condition: 'rusak ringan', date: '2023-02-15', value: '4500000' },
      { id: '3', code: 'TV-03', name: 'TV Polytron 32"', category: 'elektronik', brand: 'Polytron', building: 'Gedung Utama', floor: '1', room: 'Laboratorium Multimedia', condition: 'rusak ringan', date: '2023-03-10', value: '3000000' },
      { id: '4', code: 'TV-04', name: 'TV Sharp 40"', category: 'elektronik', brand: 'Sharp', building: 'Gedung Utama', floor: '1', room: 'Laboratorium Multimedia', condition: 'rusak berat', date: '2022-11-20', value: '4000000' },
      { id: '5', code: 'TV-05', name: 'TV Sony 55"', category: 'elektronik', brand: 'Sony', building: 'Gedung Utama', floor: '1', room: 'Laboratorium Multimedia', condition: 'baik', date: '2024-01-05', value: '6000000' }
    ]
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
