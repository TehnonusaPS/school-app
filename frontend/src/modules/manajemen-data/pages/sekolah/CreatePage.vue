<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import PageHeader from '@/components/page-header/PageHeader.vue'
import { akreditasi, jenjangOptions, statusOptions, yayasanOptions } from './data/sekolah'
import { useAuthStore } from '@/stores/authStore'
import { computed } from 'vue'
import SuccessAccountDialog from '@/components/dialogs/SuccessAccountDialog.vue'
import SekolahForm from './components/SekolahForm.vue'
import { defaultForm } from './data/defaultForm'
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
  router.push('/manajemen-data/sekolah')
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
      title="Tambah Sekolah"
      description="Lengkapi formulir berikut untuk menambahkan data sekolah baru"
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

  <SuccessAccountDialog
    v-model:open="showSuccessModal"
    title="Sekolah Berhasil Ditambahkan"
    description="Data sekolah berhasil disimpan dan akun administrator sekolah telah dibuat."
    :email="generatedAccount.email"
    :phone="generatedAccount.phone"
    :password="generatedAccount.password"
    @close="goToList"
  />
</template>
