<script setup>
import { ref } from 'vue'
import FormSheet from '@/components/data-sheet/FormSheet.vue'
import { Button } from '@/components/ui/button'
import { Plus, Pencil } from 'lucide-vue-next'
import { toast } from 'vue-sonner'
import { rawGuruItem } from './data/dataSheetDemos'
import { guruSheetSections } from './data/dataSheetSchema'

const isOpen = ref(false)
const selectedItem = ref(null)
const isSaving = ref(false)

const openCreate = () => {
  selectedItem.value = null
  isOpen.value = true
}

const openEdit = () => {
  selectedItem.value = { ...rawGuruItem }
  isOpen.value = true
}

const handleSubmit = (formData) => {
  isSaving.value = true
  setTimeout(() => {
    isSaving.value = false
    isOpen.value = false
    toast.success(formData.id ? 'Guru Berhasil Diperbarui' : 'Guru Berhasil Ditambahkan', {
      description: `Nama: ${formData.nama}, Jabatan: ${formData.jabatan}`
    })
  }, 1000)
}
</script>

<template>
  <div class="flex items-center gap-3">
    <Button
      variant="outline"
      @click="openCreate"
    >
      <Plus class="mr-2 h-4 w-4" />
      Buka Form Tambah
    </Button>

    <Button
      variant="outline"
      @click="openEdit"
    >
      <Pencil class="mr-2 h-4 w-4" />
      Buka Form Edit
    </Button>

    <FormSheet
      v-model:open="isOpen"
      :item="selectedItem"
      :title="selectedItem ? 'Edit Profil Guru' : 'Tambah Guru Baru'"
      :description="selectedItem ? 'Ubah informasi data akademik dan kontak guru' : 'Lengkapi data formulir di bawah ini untuk menambahkan guru baru'"
      :sections="guruSheetSections"
      :loading="isSaving"
      @submit="handleSubmit"
    />
  </div>
</template>
