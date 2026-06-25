<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import PageHeader from '@/components/page-header/PageHeader.vue'
import SuccessAccountDialog from '@/components/dialogs/SuccessAccountDialog.vue'
import YayasanForm from './components/YayasanForm.vue'
import { defaultForm } from './data/defaultForm'
import { statusOptions } from './data/yayasan.js'
import { Save } from 'lucide-vue-next'
import { toast } from 'vue-sonner'
import { createFoundation, getRoles, createUser } from '@/services/managementService'

const router = useRouter()
const isLoading = ref(false)

const form = ref({ ...defaultForm})

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

  let newFoundationId = null

  // 1. Create the foundation using FormData
  try {
    const formData = new FormData()
    formData.append('code', form.value.kode || '')
    formData.append('name', form.value.nama || '')
    if (form.value.tanggal_berdiri) formData.append('established_date', form.value.tanggal_berdiri)
    formData.append('status', form.value.status ? form.value.status.toLowerCase() : 'active')
    if (form.value.alamat) formData.append('address', form.value.alamat)
    if (form.value.email) formData.append('email', form.value.email)
    if (form.value.no_hp) formData.append('phone', form.value.no_hp)
    if (form.value.website) formData.append('website', form.value.website)
    if (form.value.no_akta) formData.append('deed_number', form.value.no_akta)
    if (form.value.tanggal_akta) formData.append('deed_date', form.value.tanggal_akta)
    if (form.value.no_sk) formData.append('decree_number', form.value.no_sk)
    if (form.value.tanggal_sk) formData.append('decree_date', form.value.tanggal_sk)
    if (logoFile.value) {
      formData.append('logo', logoFile.value)
    }

    const resFoundation = await createFoundation(formData)
    newFoundationId = resFoundation.data.id
  } catch (err) {
    if (err.response?.status === 422 && err.response?.data?.errors) {
      const serverErrors = err.response.data.errors
      const localErrors = {}
      Object.keys(serverErrors).forEach(key => {
        localErrors[key] = serverErrors[key][0]
      })
      formErrors.value = localErrors
      toast.error('Gagal', { description: 'Terdapat kesalahan validasi pada data yayasan.' })
    } else {
      const errorMsg = err.response?.data?.message || 'Gagal menyimpan data yayasan.'
      toast.error('Gagal', { description: errorMsg })
    }
    isLoading.value = false
    return
  }

  // 2. Retrieve Roles to find admin_yayasan role
  let adminYayasanRole = null
  try {
    const resRoles = await getRoles()
    adminYayasanRole = resRoles.data.find(r => r.name === 'admin_yayasan')
  } catch (err) {
    toast.error('Gagal mengambil data peran.')
  }

  // 3. Create the administrator user for this foundation
  const generatedPassword = Math.random().toString(36).substring(2, 10) + 'A1!'
  const userData = {
    name: 'Admin ' + form.value.nama,
    email: form.value.emailLogin,
    phone: form.value.noHpLogin,
    password: generatedPassword,
    role_id: adminYayasanRole ? adminYayasanRole.id : 2, // default fallback
    foundation_id: newFoundationId,
    is_active: true
  }

  try {
    await createUser(userData)

    // Save details to display in success modal
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
  router.push('/manajemen-data/yayasan')
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
      title="Tambah Yayasan"
      description="Lengkapi formulir berikut untuk menambahkan data yayasan baru"
      :actions="customActions"
    />

    <YayasanForm
      v-model:form="form"
      :image-preview="imagePreview"
      :status-options="statusOptions"
      :errors="formErrors"
      @image-change="handleImage"
    />
  </div>

  <SuccessAccountDialog
    v-model:open="showSuccessModal"
    title="Yayasan Berhasil Ditambahkan"
    description="Data yayasan berhasil disimpan dan akun administrator yayasan telah dibuat."
    :email="generatedAccount.email"
    :phone="generatedAccount.phone"
    :password="generatedAccount.password"
    @close="goToList"
  />
</template>
