<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import PageHeader from '@/components/page-header/PageHeader.vue'
import YayasanForm from './components/YayasanForm.vue'
import { defaultForm } from './data/defaultForm'
import { statusOptions } from './data/yayasan.js'
import { toast } from 'vue-sonner'
import { Save, ArrowLeft, CheckCircle2, HelpCircle } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
  DialogFooter
} from '@/components/ui/dialog'
import { getFoundation, updateFoundation } from '@/services/managementService'

const router = useRouter()
const route = useRoute()
const isLoading = ref(false)
const isConfirmOpen = ref(false)
const foundationId = route.query.id

const form = ref({ ...defaultForm })

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
      status: foundation.status ? foundation.status.charAt(0).toUpperCase() + foundation.status.slice(1) : '',
      alamat: foundation.address,
      email: foundation.email,
      no_hp: foundation.phone,
      emailLogin: foundation.email,
      noHpLogin: foundation.phone,
      website: foundation.website,
      no_akta: foundation.deed_number,
      tanggal_akta: foundation.deed_date ? foundation.deed_date.split('T')[0] : '',
      no_sk: foundation.decree_number,
      tanggal_sk: foundation.decree_date ? foundation.decree_date.split('T')[0] : '',
      curriculum_id: foundation.curriculum_id ? String(foundation.curriculum_id) : ''
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

function onClickSave() {
  formErrors.value = {}
  if (!form.value.nama?.trim()) formErrors.value.name = 'Nama yayasan wajib diisi.'
  if (!form.value.email?.trim()) formErrors.value.email = 'Email yayasan wajib diisi.'

  if (Object.keys(formErrors.value).length > 0) {
    toast.error('Gagal', { description: 'Terdapat isian wajib yang belum dilengkapi.' })
    return
  }

  isConfirmOpen.value = true
}

const handleSubmit = async () => {
  isConfirmOpen.value = false
  isLoading.value = true
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
    if (form.value.curriculum_id) formData.append('curriculum_id', form.value.curriculum_id)
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

const goToList = () => {
  router.push('/manajemen-data/yayasan')
}
</script>

<template>
  <div class="space-y-6 p-1 pb-16">
    <!-- Header dengan Tombol Kembali -->
    <PageHeader
      back
      title="Edit Data Yayasan"
      description="Lengkapi formulir berikut untuk memperbarui data yayasan."
    />

    <YayasanForm
      v-model:form="form"
      :image-preview="imagePreview"
      :status-options="statusOptions"
      :errors="formErrors"
      @image-change="handleImage"
    />

    <!-- Bottom Footer Actions (Tombol Batal & Simpan di Bawah) -->
    <div class="flex items-center justify-end gap-3 pt-6 border-t border-border dark:border-zinc-800">
      <Button
        type="button"
        variant="outline"
        @click="goToList"
        :disabled="isLoading"
        class="gap-1.5"
      >
        <ArrowLeft class="h-4 w-4" />
        Batal
      </Button>

      <Button
        type="button"
        @click="onClickSave"
        :disabled="isLoading"
        class="gap-1.5 px-6"
      >
        <Save class="h-4 w-4" />
        {{ isLoading ? 'Menyimpan...' : 'Simpan Perubahan' }}
      </Button>
    </div>
  </div>

  <!-- Confirmation Dialog (Konfirmasi Sebelum Simpan) -->
  <Dialog :open="isConfirmOpen" @update:open="isConfirmOpen = false">
    <DialogContent class="sm:max-w-md bg-card dark:bg-zinc-900 border border-border dark:border-zinc-800">
      <DialogHeader>
        <div class="flex items-center gap-3">
          <div class="h-10 w-10 rounded-full bg-primary/10 text-primary flex items-center justify-center shrink-0">
            <HelpCircle class="h-5 w-5" />
          </div>
          <div>
            <DialogTitle class="text-base font-semibold text-foreground dark:text-zinc-100">
              Konfirmasi Perubahan Data Yayasan
            </DialogTitle>
            <DialogDescription class="text-xs text-muted-foreground dark:text-zinc-400 mt-0.5">
              Apakah Anda yakin ingin menyimpan perubahan data pada {{ form.nama }}?
            </DialogDescription>
          </div>
        </div>
      </DialogHeader>

      <DialogFooter class="gap-2 sm:gap-0">
        <Button variant="outline" type="button" @click="isConfirmOpen = false" :disabled="isLoading">
          Batal
        </Button>
        <Button type="button" :disabled="isLoading" @click="handleSubmit" class="gap-1.5">
          <CheckCircle2 class="h-4 w-4" />
          {{ isLoading ? 'Memproses...' : 'Ya, Simpan Perubahan' }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
