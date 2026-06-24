<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import PageHeader from '@/components/page-header/PageHeader.vue'
import { statusOptions, agamaOptions, kelaminOptions, pekerjaanOptions, kelasOptions, hubunganOptions } from './data/siswa'
import { useAuthStore } from '@/stores/authStore'
import { computed } from 'vue'
import SiswaForm from './components/SiswaForm.vue'
import { defaultForm } from './data/defaultForm'
import { Save } from 'lucide-vue-next'
import { toast } from 'vue-sonner'

const auth = useAuthStore()
const isWaliKelas = computed(() => auth.user?.role === 'wali_kelas')

const classes = computed(() => {
  if (isWaliKelas.value) {
      return kelasOptions.filter(
        item => item.value === auth.user?.kelasId
    )
  }
  return kelasOptions
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

    toast('Data siswa berhasil diperbarui', {
      description: 'Perubahan data siswa telah berhasil disimpan.'
    })

    router.push('/manajemen-data/siswa')

  }, 1000)
}

const goToList = () => {
  router.push('/manajemen-data/siswa')
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
      title="Edit Siswa"
      description="Lengkapi formulir berikut untuk mengedit data siswa"
      :actions="customActions"
    /> 

    <!-- Form Siswa -->
    <SiswaForm
      v-model:form="form"
      :image-preview="imagePreview"
      :status-options="statusOptions"
      :kelas-options="classes"
      :pekerjaan-options="pekerjaanOptions"
      :agama-options="agamaOptions"
      :kelamin-options="kelaminOptions"
      :hubungan-options="hubunganOptions"
      @image-change="handleImage"
    />
  </div>
</template>
