<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import PageHeader from '@/components/page-header/PageHeader.vue'
import { statusOptions, agamaOptions, kelaminOptions, pekerjaanOptions, hubunganOptions } from './data/siswa'
import { useAuthStore } from '@/stores/authStore'
import SuccessAccountDialog from '@/components/dialogs/SuccessAccountDialog.vue'
import SiswaForm from './components/SiswaForm.vue'
import { defaultForm } from './data/defaultForm'
import { Save } from 'lucide-vue-next'
import { toast } from 'vue-sonner'
import { getClassrooms } from '@/services/managementService'
import { createSiswa } from '@/services/siswaService'

const auth = useAuthStore()
const isWaliKelas = computed(() => auth.user?.role === 'wali_kelas')

const activeClassrooms = ref([])
onMounted(async () => {
  try {
    const resClass = await getClassrooms()
    activeClassrooms.value = resClass.data
  } catch (err) {
    console.error('Gagal mengambil data kelas', err)
  }
})

const classes = computed(() => {
  let list = activeClassrooms.value.map(c => ({ label: c.name, value: String(c.id) }))
  if (isWaliKelas.value) {
    return list.filter(item => item.value === String(auth.user?.kelasId))
  }
  return list
})

const form = ref({ ...defaultForm })

const router = useRouter()
const isLoading = ref(false)

const imagePreview = ref('')

const handleImage = (file) => {
  imagePreview.value = URL.createObjectURL(file)
  form.value.foto = file
}

const showSuccessModal = ref(false)

const generatedAccount = ref({
  email: '',
  phone: '',
  password: ''
})

const handleSubmit = async () => {
  isLoading.value = true

  const payload = {
    ...form.value,
    tanggal_lahir: form.value.tanggal_lahir || null,
    tahun_masuk: form.value.tahun_masuk || null
  }

  try {
    const res = await createSiswa(payload)
    generatedAccount.value = {
      email: res.data.email,
      phone: res.data.phone || '-',
      password: res.data.password
    }
    showSuccessModal.value = true
  } catch (err) {
    const errorMsg = err.response?.data?.message || 'Gagal menambahkan siswa baru.'
    toast.error('Gagal Menyimpan', { description: errorMsg })
  } finally {
    isLoading.value = false
  }
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
