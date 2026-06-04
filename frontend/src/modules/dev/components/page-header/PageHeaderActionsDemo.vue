<script setup>
import { ref, computed } from 'vue'
import PageHeader from '@/components/page-header/PageHeader.vue'
import { ListFilter, Download, Save, Plus } from 'lucide-vue-next'

const handleFilter = () => alert('Filter di-klik (Aksi 1)!')
const handleExport = () => alert('Export di-klik (Aksi 2)!')
const handleAdd = () => alert('Tambah Data di-klik (Aksi 3)!')

const isSaving = ref(false)
const handleSave = () => {
  isSaving.value = true
  setTimeout(() => {
    isSaving.value = false
    alert('Data berhasil disimpan!')
  }, 2000)
}

const customActions = computed(() => [
  // Contoh tombol aksi primary biasa (misal: Tambah Data):
  // { label: 'Tambah Data', icon: Plus, click: handleAdd },
  {
    label: isSaving.value ? 'Menyimpan...' : 'Simpan',
    icon: Save,
    loading: isSaving.value,
    click: handleSave
  },
  { label: 'Export', icon: Download, variant: 'secondary', click: handleExport },
  { label: 'Filter', icon: ListFilter, variant: 'outline', click: handleFilter }
])
</script>

<template>
  <PageHeader
    back
    title="Data Transaksi SPP"
    description="Lacak status transaksi pembayaran SPP siswa di sini."
    :actions="customActions"
  />
</template>
