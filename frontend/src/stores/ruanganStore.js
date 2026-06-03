import { defineStore } from 'pinia'

export const useRuanganStore = defineStore('ruangan', {
  state: () => ({
    items: [
      { id: '1', code: 'LAB-01', name: 'LAB BAHASA', category: 'lab', building: 'Gedung B', capacity: '30', area: '60', facilities: ['ac', 'proyektor'], school_name: 'SD TEHNONUSA 1' },
      { id: '2', code: 'R-01', name: 'KELAS 8A', category: 'kelas', building: 'Gedung A', capacity: '30', area: '50', facilities: ['ac', 'proyektor'], school_name: 'SD TEHNONUSA 1' },
      { id: '3', code: 'LAB-02', name: 'LAB KOMPUTER', category: 'lab', building: 'Gedung B', capacity: '30', area: '70', facilities: ['ac', 'proyektor', 'wifi'], school_name: 'SD TEHNONUSA 1' },
      { id: '4', code: 'R-02', name: 'RUANG MUSIK', category: 'fasilitas', building: 'Gedung C', capacity: '25', area: '45', facilities: ['ac', 'sound_system'], school_name: 'SD TEHNONUSA 1' },
      { id: '5', code: 'R-03', name: 'RUANG OSIS', category: 'fasilitas', building: 'Gedung C', capacity: '25', area: '40', facilities: ['ac', 'meja', 'wifi'], school_name: 'SD TEHNONUSA 1' },
      { id: '6', code: 'R-04', name: 'KELAS 7A', category: 'kelas', building: 'Gedung A', capacity: '35', area: '55', facilities: ['ac', 'proyektor'], school_name: 'SD TEHNONUSA 2' },
      { id: '7', code: 'LAB-03', name: 'LAB IPA', category: 'lab', building: 'Gedung B', capacity: '28', area: '65', facilities: ['ac', 'proyektor', 'wastafel'], school_name: 'SD TEHNONUSA 2' },
      { id: '8', code: 'R-05', name: 'KELAS 9A', category: 'kelas', building: 'Gedung A', capacity: '32', area: '52', facilities: ['ac', 'proyektor'], school_name: 'SMP TEHNONUSA 1' },
      { id: '9', code: 'R-06', name: 'RUANG BK', category: 'fasilitas', building: 'Gedung C', capacity: '10', area: '30', facilities: ['ac', 'meja'], school_name: 'SMP TEHNONUSA 1' },
      { id: '10', code: 'LAB-04', name: 'LAB MULTIMEDIA', category: 'lab', building: 'Gedung B', capacity: '40', area: '80', facilities: ['ac', 'proyektor', 'wifi', 'sound_system'], school_name: 'SMA TEHNONUSA 1' },
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
