import { defineStore } from 'pinia'

export const useRuanganStore = defineStore('ruangan', {
  state: () => ({
    items: [
      { id: '1', code: 'LAB-01', name: 'LAB BAHASA', category: 'lab', building: 'Gedung B', capacity: '30', area: '60', facilities: ['ac', 'proyektor'] },
      { id: '2', code: 'R-01', name: 'KELAS 8A', category: 'kelas', building: 'Gedung A', capacity: '30', area: '50', facilities: ['ac', 'proyektor'] },
      { id: '3', code: 'LAB-02', name: 'LAB KOMPUTER', category: 'lab', building: 'Gedung B', capacity: '30', area: '70', facilities: ['ac', 'proyektor', 'wifi'] },
      { id: '4', code: 'R-02', name: 'RUANG MUSIK', category: 'fasilitas', building: 'Gedung C', capacity: '25', area: '45', facilities: ['ac', 'sound_system'] },
      { id: '5', code: 'R-03', name: 'RUANG OSIS', category: 'fasilitas', building: 'Gedung C', capacity: '25', area: '40', facilities: ['ac', 'meja', 'wifi'] },
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
