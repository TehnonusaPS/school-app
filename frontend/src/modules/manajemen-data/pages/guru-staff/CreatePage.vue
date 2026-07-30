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
  form.value.foto = file
  imagePreview.value = URL.createObjectURL(file)
}

const showSuccessModal = ref(false)

const generatedAccount = ref({
  email: '',
  phone: '',
  password: ''
})

const formErrors = ref({})

const handleSubmit = async () => {
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

  console.log(errors);

  if (Object.keys(errors).length > 0) {
    formErrors.value = errors
    toast.error('Gagal Menyimpan', {
      description: 'Harap lengkapi semua data formulir sebelum menyimpan.'
    })
    return
  }

  isLoading.value = true
  try {
    const formData = new FormData()
    const rawForm = form.value
    Object.keys(rawForm).forEach(key => {
      if (key === 'foto') return // handle separately
      if (rawForm[key] !== null && rawForm[key] !== undefined && rawForm[key] !== '') {
        formData.append(key, rawForm[key])
      }
    })
    if (rawForm.foto instanceof File) {
      formData.append('foto', rawForm.foto)
    }
    const res = await createTeacher(formData)
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
      :errors="formErrors"
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
