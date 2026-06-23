<script setup>
import { useRouter } from 'vue-router'
import { useRuanganStore } from '@/stores/ruanganStore'
import { toast } from 'vue-sonner'
import RuanganForm from '@/modules/lainnya/components/RuanganForm.vue'
import PageHeader from '@/components/page-header/PageHeader.vue'

const router = useRouter()
const store = useRuanganStore()

function handleSubmit(formData) {
  store.add(formData)
  toast.success('Ruangan berhasil ditambahkan!')
  router.push('/lainnya/ruangan')
}

function handleCancel() {
  router.push('/lainnya/ruangan')
}
</script>

<template>
  <div class="space-y-6">
    <PageHeader 
      title="Tambah Ruangan Sekolah" 
      description="Lengkapi formulir dibawah ini untuk menambahkan data ruangan baru"
      back
    />
    <RuanganForm @submit="handleSubmit" @cancel="handleCancel" />
  </div>
</template>
