import { defineStore } from 'pinia'

export const usePerpustakaanStore = defineStore('perpustakaan', {
  state: () => ({
    items: [
      { id: '1', name: 'FISIKA', isbn: '999-1', kategori: 'sains', jumlahStok: '12', lokasiRak: 'A-01', tahunTerbit: '2020', penulis: 'Newton', penerbit: 'Erlangga', deskripsi: 'Buku Fisika Dasar' },
      { id: '2', name: 'BIOLOGI', isbn: '999-2', kategori: 'sains', jumlahStok: '15', lokasiRak: 'A-01', tahunTerbit: '2021', penulis: 'Darwin', penerbit: 'Erlangga', deskripsi: 'Buku Biologi Lanjut' },
      { id: '3', name: 'MATEMATIKA', isbn: '999-3', kategori: 'sains', jumlahStok: '20', lokasiRak: 'A-01', tahunTerbit: '2019', penulis: 'Pythagoras', penerbit: 'Erlangga', deskripsi: 'Buku Matematika Diskrit' },
      { id: '4', name: 'ALGORITMA & PEMROGRAMAN', isbn: '999-4', kategori: 'sains', jumlahStok: '25', lokasiRak: 'A-01', tahunTerbit: '2022', penulis: 'Knuth', penerbit: 'Informatika', deskripsi: 'Dasar Pemrograman' },
      { id: '5', name: 'BASIS DATA', isbn: '999-5', kategori: 'sains', jumlahStok: '100', lokasiRak: 'A-01', tahunTerbit: '2023', penulis: 'Codd', penerbit: 'Informatika', deskripsi: 'Sistem Basis Data' }
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
