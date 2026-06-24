<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import PageHeader from '@/components/page-header/PageHeader.vue'
import { statusOptions, agamaOptions, kelaminOptions, pekerjaanOptions, kelasOptions, hubunganOptions } from './data/siswa'
import { useAuthStore } from '@/stores/authStore'
import { computed } from 'vue'
import SuccessAccountDialog from '@/components/dialogs/SuccessAccountDialog.vue'
import SiswaForm from './components/SiswaForm.vue'
import { defaultForm } from './data/defaultForm'
import { Save } from 'lucide-vue-next'

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

const showSuccessModal = ref(false)

const generatedAccount = ref({
  email: 'admin@sdnusantara.sch.id',
  phone: '081234567890',
  password: 'Adm#2026!'
})

const handleSubmit = () => {
  isLoading.value = true

  setTimeout(() => {
    isLoading.value = false
    showSuccessModal.value = true
  }, 1500)
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
      title="Tambah Siswa"
      description="Lengkapi formulir berikut untuk menambahkan data siswa baru"
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

  <SuccessAccountDialog
    v-model:open="showSuccessModal"
    title="Siswa Berhasil Ditambahkan"
    description="Data siswa berhasil disimpan dan akun administrator siswa telah dibuat."
    :email="generatedAccount.email"
    :phone="generatedAccount.phone"
    :password="generatedAccount.password"
    @close="goToList"
  />
</template>
