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
import { agamaOptions, jabatanOptions, kelaminOptions, pendidikanOptions, statusKepegawaianOptions, statusOptions, statusPernikahanOptions, unitKerjaOptions } from './data/guruStaff'
import { toast } from 'vue-sonner'
import { getTeacher, updateTeacher } from '@/services/managementService'

const auth = useAuthStore()
const isAdminYayasan = computed(() => auth.user?.role === 'admin_yayasan')

const unitOptions = computed(() => {
  if (isAdminYayasan.value) {
    return unitKerjaOptions
  }

  return unitKerjaOptions.filter(
    item => item.value === auth.user?.unitId
  )
})

const router = useRouter()
const route = useRoute()
const isLoading = ref(false)
const teacherId = route.query.id

const form = ref({ ...defaultForm })
const imagePreview = ref('')
const formErrors = ref({})

const handleImage = (file) => {
  imagePreview.value = URL.createObjectURL(file)
}

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
  if (!teacherId) {
    toast.error('ID Guru/Staff tidak ditemukan')
    router.push('/manajemen-data/guru-staff')
    return
  }

  isLoading.value = true
  try {
    const res = await getTeacher(teacherId)
    const t = res.data
    form.value = {
      nama_depan: t.nama_depan || '',
      nama_belakang: t.nama_belakang || '',
      nik: t.nik || '',
      nip_nuptk: t.nip_nuptk || '',
      tempat_lahir: t.tempat_lahir || '',
      tanggal_lahir: t.tanggal_lahir || '',
      jenis_kelamin: t.jenis_kelamin === 'Laki-laki' ? 'JK01' : (t.jenis_kelamin === 'Perempuan' ? 'JK02' : ''),
      agama: mapAgamaToValue(t.agama),
      status_pernikahan: mapPernikahanToValue(t.status_pernikahan),
      pendidikan_terakhir: mapPendidikanToValue(t.pendidikan_terakhir),
      gelar_depan: t.gelar_depan || '',
      gelar_belakang: t.gelar_belakang || '',
      email: t.email || '',
      no_hp: t.no_hp || '',
      alamat: t.alamat || '',
      jabatan: mapJabatanToValue(t.jabatan),
      status_kepegawaian: mapStatusKepegawaianToValue(t.status_kepegawaian),
      unit_kerja: t.unit_id || '',
      status_aktif: t.status_aktif || 'Aktif',
      emailLogin: t.emailLogin || '',
      noHpLogin: t.noHpLogin || '',
      password: ''
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
    await updateTeacher(teacherId, postData)
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
    />
  </div>
</template>
