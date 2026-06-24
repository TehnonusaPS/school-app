<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import PageHeader from '@/components/page-header/PageHeader.vue'
import SuccessAccountDialog from '@/components/dialogs/SuccessAccountDialog.vue'
import YayasanForm from './components/YayasanForm.vue'
import { defaultForm } from './data/defaultForm'
import { statusOptions } from './data/yayasan.js'
import { Save } from 'lucide-vue-next'

const router = useRouter()
const isLoading = ref(false)

const form = ref({ ...defaultForm})

const imagePreview = ref('')

const handleImage = (file) => {
  imagePreview.value = URL.createObjectURL(file)
}

const showSuccessModal = ref(false)

const generatedAccount = ref({
  email: 'admin@foundation.id',
  phone: '081234567890',
  password: 'Adm#2026!'
})

const handleSubmit = () => {
  isLoading.value = true

  setTimeout(() => {
    isLoading.value = false
    showSuccessModal.value = true
  }, 1000)
}

const goToList = () => {
  router.push('/manajemen-data/yayasan')
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
      title="Tambah Yayasan"
      description="Lengkapi formulir berikut untuk menambahkan data yayasan baru"
      :actions="customActions"
    />

    <YayasanForm
      v-model:form="form"
      :image-preview="imagePreview"
      :status-options="statusOptions"
      @image-change="handleImage"
    />
  </div>

  <SuccessAccountDialog
    v-model:open="showSuccessModal"
    title="Yayasan Berhasil Ditambahkan"
    description="Data yayasan berhasil disimpan dan akun administrator yayasan telah dibuat."
    :email="generatedAccount.email"
    :phone="generatedAccount.phone"
    :password="generatedAccount.password"
    @close="goToList"
  />
</template>
