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
import { createTeacher } from '@/services/managementService'
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

const formErrors = ref({})

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

const handleSubmit = async () => {
  isLoading.value = true
  formErrors.value = {}

  const plainPassword = form.value.password || Math.random().toString(36).substring(2, 10) + 'A1!'

  const postData = {
    nama_depan: form.value.nama_depan,
    nama_belakang: form.value.nama_belakang,
    emailLogin: form.value.emailLogin,
    noHpLogin: form.value.noHpLogin,
    password: plainPassword,
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

  try {
    await createTeacher(postData)
    generatedAccount.value = {
      email: postData.emailLogin,
      phone: postData.noHpLogin || '-',
      password: plainPassword
    }
    showSuccessModal.value = true
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
      const errorMsg = err.response?.data?.message || 'Gagal menyimpan data guru/staff.'
      toast.error('Gagal', { description: errorMsg })
    }
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
      :errors="formErrors"
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
