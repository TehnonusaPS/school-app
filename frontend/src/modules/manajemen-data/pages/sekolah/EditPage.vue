<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import PageHeader from '@/components/page-header/PageHeader.vue'
import { akreditasi, jenjangOptions, statusOptions } from './data/sekolah'
import { useAuthStore } from '@/stores/authStore'
import SekolahForm from './components/SekolahForm.vue'
import { defaultForm } from './data/defaultForm'
import { toast } from 'vue-sonner'
import { Save } from 'lucide-vue-next'
import { getSchool, updateSchool, getFoundations } from '@/services/managementService'

const auth = useAuthStore()
const isSuperAdmin = computed(() => auth.user?.role === 'superadmin')
const router = useRouter()
const route = useRoute()
const isLoading = ref(false)
const schoolId = route.query.id

const foundationOptions = ref([])
const form = ref({ ...defaultForm })
const imagePreview = ref('')
const logoFile = ref(null)

const handleImage = (file) => {
  logoFile.value = file
  imagePreview.value = URL.createObjectURL(file)
}

onMounted(async () => {
  if (!schoolId) {
    toast.error('ID Sekolah tidak ditemukan')
    router.push('/manajemen-data/sekolah')
    return
  }

  isLoading.value = true
  try {
    const resFoundations = await getFoundations()
    foundationOptions.value = resFoundations.data.data.map(y => ({
      label: y.name,
      value: y.id
    }))

    const resSchool = await getSchool(schoolId)
    const school = resSchool.data
    form.value = {
      nama: school.name,
      npsn: school.npsn,
      yayasan: school.foundation_id,
      jenjang: school.level,
      tanggal_berdiri: school.established_date ? school.established_date.split('T')[0] : '',
      status: school.status.charAt(0).toUpperCase() + school.status.slice(1),
      alamat: school.address,
      email: school.email,
      no_hp: school.phone,
      website: school.website,
      instagram: school.instagram,
      facebook: school.facebook,
      no_sk: school.decree_number,
      tanggal_sk: school.decree_date ? school.decree_date.split('T')[0] : '',
      no_izin: school.permit_number,
      tanggal_izin: school.permit_date ? school.permit_date.split('T')[0] : '',
      akreditasi: school.accreditation,
      tanggal_akreditasi: school.accreditation_date ? school.accreditation_date.split('T')[0] : '',
      no_akreditasi: school.accreditation_number,
      emailLogin: '-',
      noHpLogin: '-'
    }
    imagePreview.value = school.logo || ''
  } catch (err) {
    toast.error('Gagal mengambil data sekolah')
    router.push('/manajemen-data/sekolah')
  } finally {
    isLoading.value = false
  }
})

const formErrors = ref({})

const handleSubmit = async () => {
  isLoading.value = true
  formErrors.value = {}
  try {
    const formData = new FormData()
    formData.append('foundation_id', form.value.yayasan)
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

    await updateSchool(schoolId, formData)
    toast.success('Data sekolah berhasil diperbarui', {
      description: 'Perubahan data sekolah telah berhasil disimpan.'
    })
    router.push('/manajemen-data/sekolah')
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
      const errorMsg = err.response?.data?.message || 'Gagal menyimpan perubahan sekolah.'
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
    <PageHeader
      back
      title="Edit Sekolah"
      description="Lengkapi formulir berikut untuk mengedit data sekolah"
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
</template>
