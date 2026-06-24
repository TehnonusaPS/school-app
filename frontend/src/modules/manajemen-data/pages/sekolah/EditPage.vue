<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import PageHeader from '@/components/page-header/PageHeader.vue'
import { akreditasi, jenjangOptions, statusOptions, yayasanOptions } from './data/sekolah'
import { useAuthStore } from '@/stores/authStore'
import { computed } from 'vue'
import SekolahForm from './components/SekolahForm.vue'
import { defaultForm } from './data/defaultForm'
import { toast } from 'vue-sonner'
import { Save } from 'lucide-vue-next'

const auth = useAuthStore()
const isSuperAdmin = computed(() => auth.user?.role === 'superadmin')

const foundationOptions = computed(() => {
  if (isSuperAdmin.value) {
    return yayasanOptions.value
  }

  return yayasanOptions.filter(
    item => item.value === auth.user?.yayasanId
  )
})

const form = ref({ ...defaultForm})

const router = useRouter()
const isLoading = ref(false)

const imagePreview = ref('')

const handleImage = (file) => {
  imagePreview.value = URL.createObjectURL(file)
}

const handleSubmit = () => {
  isLoading.value = true

  setTimeout(() => {
    isLoading.value = false

    toast('Data sekolah berhasil diperbarui', {
      description: 'Perubahan data sekolah telah berhasil disimpan.'
    })

    router.push('/manajemen-data/sekolah')

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
    <PageHeader
      back
      title="Edit Sekolah"
      description="Lengkapi formulir berikut untuk mengedit data sekolah"
      :actions="customActions"
    /> 

    <!-- Form Sekolah -->
    <SekolahForm
      v-model:form="form"
      :image-preview="imagePreview"
      :foundation-options="foundationOptions"
      :jenjang-options="jenjangOptions"
      :status-options="statusOptions"
      :akreditasi="akreditasi"
      @image-change="handleImage"
    />
  </div>
</template>
