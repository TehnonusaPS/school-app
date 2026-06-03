import { defineStore } from 'pinia'

export const useAsetStore = defineStore('aset', {
  state: () => ({
    items: [
      { id: '1', code: 'TV-01', name: 'TV Samsung 32"', category: 'elektronik', brand: 'Samsung', building: 'Gedung Utama', floor: '1', room: 'Laboratorium Multimedia', condition: 'baik', date: '2023-01-01', value: '5000000', school_name: 'SD TEHNONUSA 1' },
      { id: '2', code: 'TV-02', name: 'TV LG 43"', category: 'elektronik', brand: 'LG', building: 'Gedung Utama', floor: '1', room: 'Laboratorium Multimedia', condition: 'rusak ringan', date: '2023-02-15', value: '4500000', school_name: 'SD TEHNONUSA 1' },
      { id: '3', code: 'TV-03', name: 'TV Polytron 32"', category: 'elektronik', brand: 'Polytron', building: 'Gedung Utama', floor: '1', room: 'Laboratorium Multimedia', condition: 'rusak ringan', date: '2023-03-10', value: '3000000', school_name: 'SD TEHNONUSA 1' },
      { id: '4', code: 'TV-04', name: 'TV Sharp 40"', category: 'elektronik', brand: 'Sharp', building: 'Gedung Utama', floor: '1', room: 'Laboratorium Multimedia', condition: 'rusak berat', date: '2022-11-20', value: '4000000', school_name: 'SD TEHNONUSA 1' },
      { id: '5', code: 'TV-05', name: 'TV Sony 55"', category: 'elektronik', brand: 'Sony', building: 'Gedung Utama', floor: '1', room: 'Laboratorium Multimedia', condition: 'baik', date: '2024-01-05', value: '6000000', school_name: 'SD TEHNONUSA 1' },
      { id: '6', code: 'PC-01', name: 'PC Desktop HP', category: 'elektronik', brand: 'HP', building: 'Gedung B', floor: '2', room: 'Lab Komputer', condition: 'baik', date: '2023-06-10', value: '8000000', school_name: 'SD TEHNONUSA 2' },
      { id: '7', code: 'MJ-01', name: 'Meja Belajar Siswa', category: 'furniture', brand: 'Chitose', building: 'Gedung A', floor: '1', room: 'Kelas 7A', condition: 'baik', date: '2022-07-01', value: '500000', school_name: 'SMP TEHNONUSA 1' },
      { id: '8', code: 'PR-01', name: 'Proyektor Epson EB-X05', category: 'elektronik', brand: 'Epson', building: 'Gedung A', floor: '1', room: 'Ruang Guru', condition: 'rusak ringan', date: '2023-01-15', value: '7500000', school_name: 'SMA TEHNONUSA 1' },
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
