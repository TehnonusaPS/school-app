<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import PageHeader from '@/components/page-header/PageHeader.vue'
import { statusOptions, agamaOptions, kelaminOptions, pekerjaanOptions, hubunganOptions } from './data/siswa'
import { useAuthStore } from '@/stores/authStore'
import SiswaForm from './components/SiswaForm.vue'
import { defaultForm } from './data/defaultForm'
import { Save } from 'lucide-vue-next'
import { toast } from 'vue-sonner'
import { getClassrooms } from '@/services/managementService'
import { getSiswaDetail, updateSiswa } from '@/services/siswaService'

const auth = useAuthStore()
const isWaliKelas = computed(() => auth.user?.role === 'wali_kelas')

const route = useRoute()
const studentId = route.query.id

const activeClassrooms = ref([])
const form = ref({ ...defaultForm })

onMounted(async () => {
  try {
    const resClass = await getClassrooms()
    activeClassrooms.value = resClass.data
  } catch (err) {
    console.error('Gagal mengambil data kelas', err)
  }

  if (studentId) {
    try {
      const res = await getSiswaDetail(studentId)
      form.value = {
        ...res.data,
        tanggal_lahir: res.data.tanggal_lahir || '',
        tahun_masuk: res.data.tahun_masuk || ''
      }
      if (res.data.foto) {
        const photo = res.data.foto
        imagePreview.value = photo.startsWith('http')
          ? photo
          : `http://127.0.0.1:8000/${photo.startsWith('/') ? photo.slice(1) : photo}`
      }
    } catch (err) {
      toast.error('Gagal memuat data detail siswa')
    }
  }
})

const classes = computed(() => {
  let list = activeClassrooms.value.map(c => ({ label: c.name, value: String(c.id) }))
  if (isWaliKelas.value) {
    return list.filter(item => item.value === String(auth.user?.kelasId))
  }
  return list
})

const router = useRouter()
const isLoading = ref(false)

const imagePreview = ref('')

const handleImage = (file) => {
  imagePreview.value = URL.createObjectURL(file)
  form.value.foto = file
}

const handleSubmit = async () => {
  isLoading.value = true

  const payload = {
    ...form.value,
    tanggal_lahir: form.value.tanggal_lahir || null,
    tahun_masuk: form.value.tahun_masuk || null
  }

  try {
    await updateSiswa(studentId, payload)
    toast.success('Data siswa berhasil diperbarui', {
      description: 'Perubahan data siswa telah berhasil disimpan.'
    })
    router.push('/manajemen-data/siswa')
  } catch (err) {
    const errorMsg = err.response?.data?.message || 'Gagal memperbarui data siswa.'
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
