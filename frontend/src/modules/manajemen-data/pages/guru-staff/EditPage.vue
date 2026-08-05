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
import { getTeacher, updateTeacher } from '@/services/managementService'

const auth = useAuthStore()
const router = useRouter()
const route = useRoute()
const isLoading = ref(false)
const teacherId = route.query.id

const form = ref({ ...defaultForm })
const imagePreview = ref('')
const formErrors = ref({})

const mapAgamaToValue = (label) => {
  const map = {
    'Islam': 'A01',
    'Kristen': 'A02',
    'Katolik': 'A03',
    'Buddha': 'A04',
    'Hindu': 'A05',
    'Konghucu': 'A06'
  }
  return map[label] || ''
}

const mapPernikahanToValue = (label) => {
  const map = {
    'Belum Menikah': 'SP01',
    'Menikah': 'SP02',
    'Janda': 'SP03',
    'Duda': 'SP04'
  }
  return map[label] || ''
}

const form = ref({ ...defaultForm})
const imagePreview = ref('')

const loadTeacher = async () => {
  const teacherId = route.query.id
  if (!teacherId) return

  isLoading.value = true
  try {
    const res = await getTeacher(teacherId)
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
  }
  return map[label] || ''
}

const mapPendidikanToValue = (label) => {
  const map = {
    'Sekolah Dasar (SD)': 'P01',
    'SD': 'P01',
    'Sekolah Menengah Pertama (SMP)': 'P02',
    'SMP': 'P02',
    'Sekolah Menengah Awal/Kejuruan (SMA/SMK)': 'P03',
    'SMA/SMK': 'P03',
    'Diploma I (D1)': 'P04',
    'D1': 'P04',
    'Diploma III (D3)': 'P05',
    'D3': 'P05',
    'Diploma IV (D4)': 'P06',
    'D4': 'P06',
    'Sarjana (S1)': 'P07',
    'S1': 'P07',
    'Magister (S2)': 'P08',
    'S2': 'P08',
    'Doktoral (S3)': 'P09',
    'S3': 'P09'
  }
  return map[label] || ''
}

const mapJabatanToValue = (label) => {
  const map = {
    'Kepala Yayasan': 'J001',
    'Staff Yayasan': 'J002',
    'Kepala Sekolah': 'J003',
    'Guru': 'J004',
    'Staff Sekolah': 'J005'
  }
  return map[label] || ''
}

const mapStatusKepegawaianToValue = (label) => {
  const map = {
    'Tetap': 'SK01',
    'Kontrak': 'SK02',
    'Honorer': 'SK03'
  }
  return map[label] || ''
}

const mapValueToAgama = (value) => {
  const map = {
    'A01': 'Islam',
    'A02': 'Kristen',
    'A03': 'Katolik',
    'A04': 'Buddha',
    'A05': 'Hindu',
    'A06': 'Konghucu'
  }
  return map[value] || value
}

const mapValueToPernikahan = (value) => {
  const map = {
    'SP01': 'Belum Menikah',
    'SP02': 'Menikah',
    'SP03': 'Janda',
    'SP04': 'Duda'
  }
  return map[value] || value
}

const mapValueToPendidikan = (value) => {
  const map = {
    'P01': 'Sekolah Dasar (SD)',
    'P02': 'Sekolah Menengah Pertama (SMP)',
    'P03': 'Sekolah Menengah Awal/Kejuruan (SMA/SMK)',
    'P04': 'Diploma I (D1)',
    'P05': 'Diploma III (D3)',
    'P06': 'Diploma IV (D4)',
    'P07': 'Sarjana (S1)',
    'P08': 'Magister (S2)',
    'P09': 'Doktoral (S3)'
  }
  return map[value] || value
}

const mapValueToJabatan = (value) => {
  const map = {
    'J001': 'Kepala Yayasan',
    'J002': 'Staff Yayasan',
    'J003': 'Kepala Sekolah',
    'J004': 'Guru',
    'J005': 'Staff Sekolah'
  }
  return map[value] || value
}

const mapValueToStatusKepegawaian = (value) => {
  const map = {
    'SK01': 'Tetap',
    'SK02': 'Kontrak',
    'SK03': 'Honorer'
  }
  return map[value] || value
}

const mapValueToKelamin = (value) => {
  const map = {
    'JK01': 'Laki-laki',
    'JK02': 'Perempuan'
  }
  return map[value] || value
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
  const teacherId = route.query.id
  if (!teacherId) return

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

    const res = await updateTeacher(teacherId, submitData)
    if (res.status === 'success') {
      toast.success('Berhasil diperbarui', {
        description: 'Data guru/staff telah berhasil disimpan.'
      })
      router.push('/manajemen-data/guru-staff')
    } else {
      toast.error(res.message || 'Gagal memperbarui data')
    }
    imagePreview.value = t.foto || ''
  } catch (err) {
    toast.error('Gagal mengambil data guru/staff')
    router.push('/manajemen-data/guru-staff')
  } finally {
    isLoading.value = false
  }
})

const handleSubmit = async () => {
  isLoading.value = true
  formErrors.value = {}

  const postData = {
    nama_depan: form.value.nama_depan,
    nama_belakang: form.value.nama_belakang,
    emailLogin: form.value.emailLogin,
    noHpLogin: form.value.noHpLogin,
    nik: form.value.nik,
    nip_nuptk: form.value.nip_nuptk,
    tempat_lahir: form.value.tempat_lahir,
    tanggal_lahir: form.value.tanggal_lahir,
    jenis_kelamin: mapValueToKelamin(form.value.jenis_kelamin),
    agama: mapValueToAgama(form.value.agama),
    status_pernikahan: mapValueToPernikahan(form.value.status_pernikahan),
    pendidikan_terakhir: mapValueToPendidikan(form.value.pendidikan_terakhir),
    gelar_depan: form.value.gelar_depan,
    gelar_belakang: form.value.gelar_belakang,
    email: form.value.email,
    no_hp: form.value.no_hp,
    alamat: form.value.alamat,
    jabatan: mapValueToJabatan(form.value.jabatan),
    status_kepegawaian: mapValueToStatusKepegawaian(form.value.status_kepegawaian),
    unit_kerja: form.value.unit_kerja,
    status_aktif: form.value.status_aktif === 'Aktif' ? 'aktif' : 'nonaktif'
  }

  if (form.value.password) {
    postData.password = form.value.password
  }

  try {
    // If there's a new photo, send as FormData
    if (form.value.foto instanceof File) {
      const fd = new FormData()
      Object.keys(postData).forEach(k => fd.append(k, postData[k]))
      fd.append('foto', form.value.foto)
      await updateTeacher(teacherId, fd)
    } else {
      await updateTeacher(teacherId, postData)
    }
    toast.success('Data guru/staff berhasil diperbarui', {
      description: 'Perubahan data guru/staff telah berhasil disimpan.'
    })
    router.push('/manajemen-data/guru-staff')
  } catch (err) {
    if (err.response?.status === 422 && err.response?.data?.errors) {
      const serverErrors = err.response.data.errors
      const localErrors = {}
      Object.keys(serverErrors).forEach(key => {
        localErrors[key] = serverErrors[key][0]
      })
      formErrors.value = localErrors
      toast.error('Gagal', { description: 'Terdapat kesalahan validasi pada data guru/staff.' })
    } else {
      const errorMsg = err.response?.data?.message || 'Gagal memperbarui data guru/staff.'
      toast.error('Gagal', { description: errorMsg })
    }
  } finally {
    isLoading.value = false
  }
}
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
      :errors="formErrors"
      @image-change="handleImage"
      :errors="formErrors"
    />
  </div>
</template>
