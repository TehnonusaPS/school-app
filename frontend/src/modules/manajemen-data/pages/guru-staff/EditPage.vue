<script setup>
import { 
  Save
} from 'lucide-vue-next'
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import PageHeader from '@/components/page-header/PageHeader.vue'
import { defaultForm } from './data/defaultForm'
import { useAuthStore } from '@/stores/authStore'
import GuruStaffForm from './components/GuruStaffForm.vue'
import { agamaOptions, jabatanOptions, kelaminOptions, pendidikanOptions, statusKepegawaianOptions, statusOptions, statusPernikahanOptions } from './data/guruStaff'
import { toast } from 'vue-sonner'
import { getTeacher, updateTeacher, getSchools, getFoundation } from '@/services/managementService'

const auth = useAuthStore()
const router = useRouter()
const route = useRoute()
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

const form = ref({ ...defaultForm})
const imagePreview = ref('')

const loadTeacher = async () => {
  const id = route.query.id
  if (!id) return

  isLoading.value = true
  try {
    const res = await getTeacher(id)
    const data = res.data

    const jabMap = {
      'Kepala Yayasan': 'J001',
      'Staff Yayasan': 'J002',
      'Kepala Sekolah': 'J003',
      'Guru': 'J004',
      'Staff Sekolah': 'J005'
    }
    const kepMap = {
      'Tetap': 'SK01',
      'Kontrak': 'SK02',
      'Honorer': 'SK03'
    }

    form.value = {
      ...defaultForm,
      nama_depan: data.nama_depan || '',
      nama_belakang: data.nama_belakang || '',
      nik: data.nik || '',
      nip_nuptk: data.nip_nuptk || '',
      tempat_lahir: data.tempat_lahir || '',
      tanggal_lahir: data.tanggal_lahir || '',
      jenis_kelamin: data.jenis_kelamin || '',
      agama: data.agama || '',
      status_pernikahan: data.status_pernikahan || '',
      pendidikan_terakhir: data.pendidikan_terakhir || '',
      gelar_depan: data.gelar_depan || '',
      gelar_belakang: data.gelar_belakang || '',
      email: data.email || '',
      no_hp: data.no_hp || '',
      alamat: data.alamat || '',
      jabatan: jabMap[data.jabatan] || data.jabatan || '',
      status_kepegawaian: kepMap[data.status_kepegawaian] || data.status_kepegawaian || '',
      unit_kerja: data.unit_id || '',
      status_aktif: data.status_aktif === 'Aktif' ? 'Aktif' : 'Nonaktif',
      emailLogin: data.emailLogin || '',
      noHpLogin: data.noHpLogin || ''
    }

    imagePreview.value = data.foto || ''
  } catch (err) {
    toast.error('Gagal memuat data guru/staff')
  } finally {
    isLoading.value = false
  }
}

onMounted(async () => {
  await loadUnitOptions()
  await loadTeacher()
})

const handleImage = (file) => {
  imagePreview.value = URL.createObjectURL(file)
}

const handleSubmit = async () => {
  const id = route.query.id
  if (!id) return

  isLoading.value = true
  try {
    const submitData = { ...form.value }
    const res = await updateTeacher(id, submitData)
    if (res.status === 'success') {
      toast.success('Berhasil diperbarui', {
        description: 'Data guru/staff telah berhasil disimpan.'
      })
      router.push('/manajemen-data/guru-staff')
    } else {
      toast.error(res.message || 'Gagal memperbarui data')
    }
  } catch (err) {
    const errorMsg = err.response?.data?.message || 'Terjadi kesalahan sistem'
    toast.error(errorMsg)
  } finally {
    isLoading.value = false
  }
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
      title="Edit Guru/Staff"
      description="Lengkapi formulir berikut untuk mengedit data guru atau staff"
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
</template>
