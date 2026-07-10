<script setup>
import { 
  Save
} from 'lucide-vue-next'
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import PageHeader from '@/components/page-header/PageHeader.vue'
import { defaultForm } from './data/defaultForm'
import { useAuthStore } from '@/stores/authStore'
import GuruStaffForm from './components/GuruStaffForm.vue'
import { agamaOptions, jabatanOptions, kelaminOptions, pendidikanOptions, statusKepegawaianOptions, statusOptions, statusPernikahanOptions } from './data/guruStaff'
import SuccessAccountDialog from '@/components/dialogs/SuccessAccountDialog.vue'
import { createTeacher, getSchools, getFoundation } from '@/services/managementService'
import { toast } from 'vue-sonner'

const auth = useAuthStore()
const router = useRouter()
const isLoading = ref(false)
const unitOptions = ref([])

const loadUnitOptions = async () => {
  try {
    const resSchools = await getSchools()
    const options = resSchools.data.data.map(s => ({
      label: s.name,
      value: 'S' + String(s.id).padStart(4, '0')
    }))

    if (auth.user?.foundation_id) {
      try {
        const resFd = await getFoundation(auth.user.foundation_id)
        options.unshift({
          label: resFd.data.name,
          value: 'Y' + String(auth.user.foundation_id).padStart(4, '0')
        })
      } catch (err) {
        options.unshift({
          label: 'Yayasan',
          value: 'Y' + String(auth.user.foundation_id).padStart(4, '0')
        })
      }
    }
    unitOptions.value = options
  } catch (err) {
    console.error('Failed to load schools', err)
  }
}

onMounted(() => {
  loadUnitOptions()
})

const form = ref({ ...defaultForm})

const imagePreview = ref('')

const handleImage = (file) => {
  imagePreview.value = URL.createObjectURL(file)
}

const showSuccessModal = ref(false)

const generatedAccount = ref({
  email: '',
  phone: '',
  password: ''
})

const handleSubmit = async () => {
  isLoading.value = true
  try {
    const submitData = { ...form.value }
    const res = await createTeacher(submitData)
    if (res.status === 'success') {
      generatedAccount.value = {
        email: res.data.email,
        phone: res.data.phone || '-',
        password: res.data.password
      }
      showSuccessModal.value = true
    } else {
      toast.error(res.message || 'Gagal menyimpan data')
    }
  } catch (err) {
    const errorMsg = err.response?.data?.message || 'Terjadi kesalahan sistem'
    toast.error(errorMsg)
  } finally {
    isLoading.value = false
  }
}
const goToList = () => {
  router.push('/manajemen-data/guru-staff')
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
      title="Tambah Guru/Staff"
      description="Lengkapi formulir berikut untuk menambahkan data guru atau staff baru"
      :actions="customActions"
    /> 

    <GuruStaffForm
      v-model:form="form"
      :image-preview="imagePreview"
      :agama-options="agamaOptions"
      :kelamin-options="kelaminOptions"
      :pendidikan-options="pendidikanOptions"
      :status-pernikahan-options="statusPernikahanOptions"
      :jabatan-options="jabatanOptions"
      :status-kepegawaian-options="statusKepegawaianOptions"
      :unit-kerja-options="unitOptions"
      :status-options="statusOptions"
      @image-change="handleImage"
    />

  </div>

  <SuccessAccountDialog
    v-model:open="showSuccessModal"
    title="Guru/Staff Berhasil Ditambahkan"
    description="Data guru/staff berhasil disimpan dan akun administrator telah dibuat."
    :email="generatedAccount.email"
    :phone="generatedAccount.phone"
    :password="generatedAccount.password"
    @close="goToList"
  />
</template>
