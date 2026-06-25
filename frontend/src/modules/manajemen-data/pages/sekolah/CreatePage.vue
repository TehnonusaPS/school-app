<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import PageHeader from '@/components/page-header/PageHeader.vue'
import { akreditasi, jenjangOptions, statusOptions } from './data/sekolah'
import { useAuthStore } from '@/stores/authStore'
import { computed, onMounted } from 'vue'
import SuccessAccountDialog from '@/components/dialogs/SuccessAccountDialog.vue'
import SekolahForm from './components/SekolahForm.vue'
import { defaultForm } from './data/defaultForm'
import { Save } from 'lucide-vue-next'
import { toast } from 'vue-sonner'
import { getFoundations, createSchool, getRoles, createUser } from '@/services/managementService'

const auth = useAuthStore()
const isSuperAdmin = computed(() => auth.user?.role === 'superadmin')

const foundationOptions = ref([])

onMounted(async () => {
  try {
    const res = await getFoundations()
    foundationOptions.value = res.data.data.map(y => ({
      label: y.name,
      value: y.id
    }))
  } catch (err) {
    toast.error('Gagal mengambil daftar yayasan')
  }
})

const form = ref({ ...defaultForm})

const router = useRouter()
const isLoading = ref(false)

const imagePreview = ref('')
const logoFile = ref(null)

const handleImage = (file) => {
  logoFile.value = file
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
  isLoading.value = true
  formErrors.value = {}

  let foundationId = null
  if (isSuperAdmin.value) {
    foundationId = form.value.yayasan
  } else {
    foundationId = auth.user?.foundation_id
  }

  if (!foundationId) {
    toast.error('Gagal', { description: 'Yayasan tidak boleh kosong.' })
    isLoading.value = false
    return
  }

  let newSchoolId = null

  // 1. Create the school using FormData
  try {
    const formData = new FormData()
    formData.append('foundation_id', foundationId)
    formData.append('name', form.value.nama || '')
    if (form.value.npsn) formData.append('npsn', form.value.npsn)
    if (form.value.jenjang) formData.append('level', form.value.jenjang)
    if (form.value.tanggal_berdiri) formData.append('established_date', form.value.tanggal_berdiri)
    formData.append('status', form.value.status ? form.value.status.toLowerCase() : 'active')
    if (form.value.alamat) formData.append('address', form.value.alamat)
    if (form.value.email) formData.append('email', form.value.email)
    if (form.value.no_hp) formData.append('phone', form.value.no_hp)
    if (form.value.website) formData.append('website', form.value.website)
    if (form.value.instagram) formData.append('instagram', form.value.instagram)
    if (form.value.facebook) formData.append('facebook', form.value.facebook)
    if (form.value.no_sk) formData.append('decree_number', form.value.no_sk)
    if (form.value.tanggal_sk) formData.append('decree_date', form.value.tanggal_sk)
    if (form.value.no_izin) formData.append('permit_number', form.value.no_izin)
    if (form.value.tanggal_izin) formData.append('permit_date', form.value.tanggal_izin)
    if (form.value.akreditasi) formData.append('accreditation', form.value.akreditasi)
    if (form.value.tanggal_akreditasi) formData.append('accreditation_date', form.value.tanggal_akreditasi)
    if (form.value.no_akreditasi) formData.append('accreditation_number', form.value.no_akreditasi)
    if (logoFile.value) {
      formData.append('logo', logoFile.value)
    }

    const resSchool = await createSchool(formData)
    newSchoolId = resSchool.data.id
  } catch (err) {
    if (err.response?.status === 422 && err.response?.data?.errors) {
      const serverErrors = err.response.data.errors
      const localErrors = {}
      Object.keys(serverErrors).forEach(key => {
        localErrors[key] = serverErrors[key][0]
      })
      formErrors.value = localErrors
      toast.error('Gagal', { description: 'Terdapat kesalahan validasi pada data sekolah.' })
    } else {
      const errorMsg = err.response?.data?.message || 'Gagal menyimpan data sekolah.'
      toast.error('Gagal', { description: errorMsg })
    }
    isLoading.value = false
    return
  }

  // 2. Create school admin user
  let adminSekolahRole = null
  try {
    const resRoles = await getRoles()
    adminSekolahRole = resRoles.data.find(r => r.name === 'admin_sekolah')
  } catch (err) {
    toast.error('Gagal mengambil data peran.')
  }

  const generatedPassword = Math.random().toString(36).substring(2, 10) + 'A1!'
  const userData = {
    name: 'Admin ' + form.value.nama,
    email: form.value.emailLogin,
    phone: form.value.noHpLogin,
    password: generatedPassword,
    role_id: adminSekolahRole ? adminSekolahRole.id : 4,
    foundation_id: foundationId,
    school_id: newSchoolId,
    is_active: true
  }

  try {
    await createUser(userData)

    generatedAccount.value = {
      email: form.value.emailLogin,
      phone: form.value.noHpLogin,
      password: generatedPassword
    }

    showSuccessModal.value = true
  } catch (err) {
    if (err.response?.status === 422 && err.response?.data?.errors) {
      const serverErrors = err.response.data.errors
      const localErrors = {}
      if (serverErrors.email) localErrors.emailLogin = serverErrors.email[0]
      if (serverErrors.phone) localErrors.noHpLogin = serverErrors.phone[0]
      formErrors.value = localErrors
      toast.error('Gagal', { description: 'Terdapat kesalahan validasi pada akun administrator.' })
    } else {
      const errorMsg = err.response?.data?.message || 'Gagal membuat akun administrator.'
      toast.error('Gagal', { description: errorMsg })
    }
  } finally {
    isLoading.value = false
  }
}

const goToList = () => {
  router.push('/manajemen-data/sekolah')
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
      title="Tambah Sekolah"
      description="Lengkapi formulir berikut untuk menambahkan data sekolah baru"
      :actions="customActions"
    /> 

    <!-- Form Sekolah -->
    <SekolahForm
      v-model:form="form"
      :image-preview="imagePreview"
      :foundation-options="foundationOptions"
      :jenjang-options="jenjangOptions"
      :status-options="statusOptions"
      :akreditasi="akreditasi"
      :errors="formErrors"
      @image-change="handleImage"
    />
  </div>

  <SuccessAccountDialog
    v-model:open="showSuccessModal"
    title="Sekolah Berhasil Ditambahkan"
    description="Data sekolah berhasil disimpan dan akun administrator sekolah telah dibuat."
    :email="generatedAccount.email"
    :phone="generatedAccount.phone"
    :password="generatedAccount.password"
    @close="goToList"
  />
</template>
