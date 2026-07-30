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
      'Staff Sekolah': 'J005',
      'Admin Sekolah': 'J006'
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
      noHpLogin: data.noHpLogin || '',
      join_date: data.join_date || ''
    }

    if (data.foto) {
      const baseUrl = (import.meta.env.VITE_API_BASE_URL || 'http://127.0.0.1:8000/api').replace(/\/api$/, '')
      imagePreview.value = data.foto.startsWith('http') ? data.foto : `${baseUrl}/storage/${data.foto}`
    }
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
  form.value.foto = file
  imagePreview.value = URL.createObjectURL(file)
}

const formErrors = ref({})

const handleSubmit = async () => {
  const id = route.query.id
  if (!id) return

  formErrors.value = {}

  // Client-side Validation: All fields must be filled
  const errors = {}
  if (!form.value.nama_depan) {
    errors.first_name = 'Nama depan harus diisi'
  }
  if (!form.value.nik) {
    errors.nik = 'NIK harus diisi'
  }
  if (!form.value.nip_nuptk) {
    errors.nip_nuptk = 'NIP/NUPTK harus diisi'
  }
  if (!form.value.tempat_lahir) {
    errors.tempat_lahir = 'Tempat lahir harus diisi'
  }
  if (!form.value.tanggal_lahir) {
    errors.tanggal_lahir = 'Tanggal lahir harus diisi'
  }
  if (!form.value.jenis_kelamin) {
    errors.jenis_kelamin = 'Jenis kelamin harus diisi'
  }
  if (!form.value.agama) {
    errors.agama = 'Agama harus diisi'
  }
  if (!form.value.status_pernikahan) {
    errors.status_pernikahan = 'Status pernikahan harus diisi'
  }
  if (!form.value.pendidikan_terakhir) {
    errors.pendidikan_terakhir = 'Pendidikan terakhir harus diisi'
  }
  if (!form.value.email) {
    errors.email = 'E-mail sekolah harus diisi'
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email)) {
    errors.email = 'Format e-mail tidak valid'
  }
  if (!form.value.no_hp) {
    errors.phone = 'No. Telp harus diisi'
  }
  if (!form.value.alamat) {
    errors.address = 'Alamat lengkap harus diisi'
  }
  if (!form.value.unit_kerja) {
    errors.unit_kerja = 'Unit kerja harus diisi'
  }
  if (!form.value.status_aktif) {
    errors.status_aktif = 'Status aktif harus diisi'
  }
  if (!form.value.join_date) {
    errors.join_date = 'Tanggal bergabung harus diisi'
  }
  if (!form.value.status_kepegawaian) {
    errors.status_kepegawaian = 'Status kepegawaian harus diisi'
  }
  if (!form.value.jabatan) {
    errors.jabatan = 'Jabatan harus diisi'
  }
  if (!form.value.emailLogin) {
    errors.emailLogin = 'E-mail login administrator harus diisi'
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.emailLogin)) {
    errors.emailLogin = 'Format e-mail login tidak valid'
  }
  if (!form.value.noHpLogin) {
    errors.noHpLogin = 'No. HP login administrator harus diisi'
  }

  if (Object.keys(errors).length > 0) {
    formErrors.value = errors
    toast.error('Gagal Menyimpan', {
      description: 'Harap lengkapi semua data formulir sebelum menyimpan.'
    })
    return
  }

  isLoading.value = true
  try {
    let submitData = { ...form.value }
    
    if (form.value.foto instanceof File) {
      const formData = new FormData()
      Object.keys(submitData).forEach(key => {
        if (submitData[key] !== null && submitData[key] !== undefined && key !== 'foto') {
          formData.append(key, submitData[key])
        }
      })
      formData.append('foto', form.value.foto)
      submitData = formData
    }

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
    const responseData = err.response?.data

    if (responseData?.errors) {
      const backendErrors = {}

      Object.entries(responseData.errors).forEach(([field, messages]) => {
        backendErrors[field] = Array.isArray(messages)
          ? messages[0]
          : messages
      })

      formErrors.value = backendErrors

      toast.error('Gagal Menyimpan', {
        description: 'Periksa kembali data pada formulir.'
      })

      return
    }

    toast.error(responseData?.message || 'Terjadi kesalahan sistem')
    // const errorMsg = err.response?.data?.message || 'Terjadi kesalahan sistem'
    // toast.error(errorMsg)
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
      :errors="formErrors"
    />
  </div>
</template>
