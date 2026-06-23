<script setup>
import { useRouter } from 'vue-router'
import { usePerpustakaanStore } from '@/stores/perpustakaanStore'
import { toast } from 'vue-sonner'
import PerpustakaanForm from '@/modules/lainnya/components/PerpustakaanForm.vue'
import PageHeader from '@/components/page-header/PageHeader.vue'

const router = useRouter()
const store = usePerpustakaanStore()

function handleSubmit(formData) {
  store.add(formData)
  toast.success('Buku berhasil ditambahkan!')
  router.push('/lainnya/perpustakaan')
}

function handleCancel() {
  router.push('/lainnya/perpustakaan')
}
</script>

<template>
  <div class="space-y-6">
    <PageHeader 
      title="Tambah Buku Perpustakaan" 
      description="Lengkapi formulir dibawah ini untuk menambahkan data buku baru"
      back
    />
    <PerpustakaanForm @submit="handleSubmit" @cancel="handleCancel" />
  </div>
</template>
