<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import PageHeader from '@/components/page-header/PageHeader.vue'
import YayasanForm from './components/YayasanForm.vue'
import { defaultForm } from './data/defaultForm'
import { statusOptions } from './data/yayasan.js'
import { toast } from 'vue-sonner'
import { Save } from 'lucide-vue-next'
import { getFoundation, updateFoundation } from '@/services/managementService'

const router = useRouter()
const route = useRoute()
const isLoading = ref(false)
const foundationId = route.query.id

const form = ref({ ...defaultForm})

const imagePreview = ref('')
const logoFile = ref(null)

const handleImage = (file) => {
  logoFile.value = file
  imagePreview.value = URL.createObjectURL(file)
}

onMounted(async () => {
  if (!foundationId) {
    toast.error('ID Yayasan tidak ditemukan')
    router.push('/manajemen-data/yayasan')
    return
  }

  isLoading.value = true
  try {
    const res = await getFoundation(foundationId)
    const foundation = res.data
    form.value = {
      kode: foundation.code,
      nama: foundation.name,
      tanggal_berdiri: foundation.established_date ? foundation.established_date.split('T')[0] : '',
      status: foundation.status.charAt(0).toUpperCase() + foundation.status.slice(1),
      alamat: foundation.address,
      email: foundation.email,
      no_hp: foundation.phone,
      website: foundation.website,
      no_akta: foundation.deed_number,
      tanggal_akta: foundation.deed_date ? foundation.deed_date.split('T')[0] : '',
      no_sk: foundation.decree_number,
      tanggal_sk: foundation.decree_date ? foundation.decree_date.split('T')[0] : '',
      emailLogin: foundation.users && foundation.users[0] ? foundation.users[0].email : '',
      noHpLogin: foundation.users && foundation.users[0] ? foundation.users[0].phone : ''
    }
    imagePreview.value = foundation.logo || ''
  } catch (err) {
    toast.error('Gagal mengambil data yayasan')
    router.push('/manajemen-data/yayasan')
  } finally {
    isLoading.value = false
  }
})

const formErrors = ref({})

const handleSubmit = async () => {
  formErrors.value = {}

  // Client-side Validation: All fields must be filled
  const errors = {}
  if (!form.value.email) {
    errors.email = 'E-mail yayasan harus diisi'
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email)) {
    errors.email = 'Format e-mail tidak valid'
  }
  if (!form.value.no_hp) {
    errors.phone = 'No. Telp harus diisi'
  }
  if (!form.value.website) {
    errors.website = 'Website harus diisi'
  }
  if (!form.value.nama) {
    errors.name = 'Nama yayasan harus diisi'
  }
  if (!form.value.kode) {
    errors.code = 'Kode yayasan harus diisi'
  }
  if (!form.value.tanggal_berdiri) {
    errors.established_date = 'Tanggal berdiri harus diisi'
  }
  if (!form.value.status) {
    errors.status = 'Status harus dipilih'
  }
  if (!form.value.no_akta) {
    errors.deed_number = 'No. Akta Pendirian harus diisi'
  }
  if (!form.value.tanggal_akta) {
    errors.deed_date = 'Tanggal akta pendirian harus diisi'
  }
  if (!form.value.no_sk) {
    errors.decree_number = 'No. SK Kemenkumham harus diisi'
  }
  if (!form.value.tanggal_sk) {
    errors.decree_date = 'Tanggal SK Kemenkumham harus diisi'
  }
  if (!form.value.alamat) {
    errors.address = 'Alamat lengkap harus diisi'
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
    if (form.value.emailLogin) formData.append('emailLogin', form.value.emailLogin)
    if (form.value.noHpLogin) formData.append('noHpLogin', form.value.noHpLogin)
    if (logoFile.value) {
      formData.append('logo', logoFile.value)
    }

    await updateFoundation(foundationId, formData)
    toast.success('Data yayasan berhasil diperbarui', {
      description: 'Perubahan data yayasan telah berhasil disimpan.'
    })
    router.push('/manajemen-data/yayasan')
  } catch (err) {
    if (err.response?.status === 422 && err.response?.data?.errors) {
      const serverErrors = err.response.data.errors
      const localErrors = {}
      const isUserValidationError = err.response.data.message?.toLowerCase().includes('administrator')
      Object.keys(serverErrors).forEach(key => {
        if (isUserValidationError) {
          if (key === 'email') localErrors.emailLogin = serverErrors[key][0]
          else if (key === 'phone') localErrors.noHpLogin = serverErrors[key][0]
          else localErrors[key] = serverErrors[key][0]
        } else {
          if (key === 'email') localErrors.email = serverErrors[key][0]
          else if (key === 'phone') localErrors.phone = serverErrors[key][0]
          else localErrors[key] = serverErrors[key][0]
        }
      })
      formErrors.value = localErrors
      toast.error('Gagal', { description: 'Terdapat kesalahan validasi pada data yayasan.' })
    } else {
      const errorMsg = err.response?.data?.message || 'Gagal menyimpan perubahan yayasan.'
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
      title="Edit Yayasan"
      description="Lengkapi formulir berikut untuk mengedit data yayasan"
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
</template>
