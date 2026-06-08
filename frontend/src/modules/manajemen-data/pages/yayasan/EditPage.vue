<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import PageHeader from '@/components/page-header/PageHeader.vue'
import YayasanForm from './components/YayasanForm.vue'
import { defaultForm } from './data/defaultForm'
import { statusOptions } from './data/yayasan.js'
import { toast } from 'vue-sonner'
import { Save } from 'lucide-vue-next'

const router = useRouter()
const isLoading = ref(false)

const form = ref({ ...defaultForm})

const imagePreview = ref('')

const handleImage = (file) => {
  imagePreview.value = URL.createObjectURL(file)
}

const handleSubmit = () => {
  isLoading.value = true

  setTimeout(() => {
    isLoading.value = false

    toast('Data yayasan berhasil diperbarui', {
      description: 'Perubahan data yayasan telah berhasil disimpan.'
    })

    router.push('/manajemen-data/yayasan')

  }, 1000)
}

const customActions = computed(() => [
  {
    label: isLoading.value ? 'Menyimpan...' : 'Simpan',
    icon: Save,
    loading: isLoading.value,
    click: handleSubmit
  },
])

</script>

<template>
  <div class="space-y-6 p-1 pb-10">
    <!-- Header dengan Tombol Kembali -->
    <PageHeader
      back
      title="Edit Yayasan"
      description="Lengkapi formulir berikut untuk mengedit data yayasan"
      :actions="customActions"
    />

    <YayasanForm
      v-model:form="form"
      :image-preview="imagePreview"
      :status-options="statusOptions"
      @image-change="handleImage"
    />
  </div>
</template>
