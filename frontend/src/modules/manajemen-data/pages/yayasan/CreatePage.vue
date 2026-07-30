<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import PageHeader from '@/components/page-header/PageHeader.vue'
import SuccessAccountDialog from '@/components/dialogs/SuccessAccountDialog.vue'
import YayasanForm from './components/YayasanForm.vue'
import { defaultForm } from './data/defaultForm'
import { statusOptions } from './data/yayasan.js'
import { Save, ArrowLeft, CheckCircle2, HelpCircle, Building2 } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
  DialogFooter
} from '@/components/ui/dialog'
import { toast } from 'vue-sonner'
import { createFoundation, getRoles, createUser } from '@/services/managementService'
import { glassFade, glassSlide } from '@/config/motion'

const router = useRouter()
const isLoading = ref(false)
const isConfirmOpen = ref(false)

const form = ref({ ...defaultForm })

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

// Trigger confirmation dialog before actual save
function onClickSave() {
  formErrors.value = {}
  if (!form.value.nama?.trim()) formErrors.value.name = 'Nama yayasan wajib diisi.'
  if (!form.value.kode?.trim()) formErrors.value.code = 'Kode yayasan wajib diisi.'
  
  const loginEmail = form.value.emailLogin?.trim() || form.value.email?.trim()
  const loginPhone = form.value.noHpLogin?.trim() || form.value.no_hp?.trim()

  if (!loginEmail) formErrors.value.emailLogin = 'Email login administrator wajib diisi.'
  if (!loginPhone) formErrors.value.noHpLogin = 'No. HP login administrator wajib diisi.'

  if (Object.keys(formErrors.value).length > 0) {
    toast.error('Gagal Menyimpan', { description: 'Harap lengkapi semua isian wajib terlebih dahulu.' })
    return
  }

  isConfirmOpen.value = true
}

const handleSubmit = async () => {
  isConfirmOpen.value = false
  isLoading.value = true
  formErrors.value = {}

  let newFoundationId = null
  const loginEmail = form.value.emailLogin?.trim() || form.value.email?.trim()
  const loginPhone = form.value.noHpLogin?.trim() || form.value.no_hp?.trim()
  const yayasanEmail = form.value.email?.trim() || loginEmail
  const yayasanPhone = form.value.no_hp?.trim() || loginPhone

  // 1. Create the foundation using FormData
  try {
    const formData = new FormData()
    formData.append('code', form.value.kode || '')
    formData.append('name', form.value.nama || '')
    if (form.value.tanggal_berdiri) formData.append('established_date', form.value.tanggal_berdiri)
    formData.append('status', form.value.status ? form.value.status.toLowerCase() : 'active')
    if (form.value.alamat) formData.append('address', form.value.alamat)
    if (yayasanEmail) formData.append('email', yayasanEmail)
    if (yayasanPhone) formData.append('phone', yayasanPhone)
    if (form.value.website) formData.append('website', form.value.website)
    if (form.value.no_akta) formData.append('deed_number', form.value.no_akta)
    if (form.value.tanggal_akta) formData.append('deed_date', form.value.tanggal_akta)
    if (form.value.no_sk) formData.append('decree_number', form.value.no_sk)
    if (form.value.tanggal_sk) formData.append('decree_date', form.value.tanggal_sk)
    if (form.value.curriculum_id) formData.append('curriculum_id', form.value.curriculum_id)
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

  // 3. Create administrator user for this foundation using emailLogin & noHpLogin
  const generatedPassword = Math.random().toString(36).substring(2, 10) + 'A1!'
  const userData = {
    name: 'Admin ' + form.value.nama,
    email: loginEmail,
    phone: loginPhone,
    password: generatedPassword,
    role_id: adminYayasanRole ? adminYayasanRole.id : 2,
    foundation_id: newFoundationId,
    is_active: true
  }

  try {
    await createUser(userData)

    generatedAccount.value = {
      email: loginEmail,
      phone: loginPhone,
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
</script>

<template>
  <div
    v-motion
    :initial="glassFade.initial"
    :visible-once="glassFade.visible"
    class="space-y-6 p-1 pb-24 text-left"
  >
    <!-- Header dengan Tombol Kembali -->
    <PageHeader
      back
      title="Tambah Yayasan Baru"
      description="Lengkapi formulir berikut untuk menambahkan data yayasan pendidikan baru."
    />

    <!-- Form Utama -->
    <div
      v-motion
      :initial="glassSlide.initial"
      :visible-once="{ ...glassSlide.visible, transition: { ...glassSlide.visible.transition, delay: 100 } }"
    >
      <YayasanForm
        v-model:form="form"
        :image-preview="imagePreview"
        :status-options="statusOptions"
        :errors="formErrors"
        @image-change="handleImage"
      />
    </div>

    <!-- Bottom Action Bar (Tombol Batal & Simpan di Bawah) -->
    <div class="fixed bottom-0 left-0 right-0 z-20 backdrop-blur-md bg-background/80 dark:bg-zinc-900/80 border-t border-border dark:border-zinc-800 p-4 transition-all">
      <div class="max-w-7xl mx-auto flex items-center justify-between gap-4 px-4 sm:px-6">
        <div class="hidden sm:flex items-center gap-2 text-xs text-muted-foreground dark:text-zinc-400">
          <Building2 class="h-4 w-4 text-primary" />
          <span>Pastikan seluruh data yayasan yang dimasukkan sudah benar.</span>
        </div>

        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
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
            class="gap-1.5 px-6 shadow-sm"
          >
            <Save class="h-4 w-4" />
            {{ isLoading ? 'Menyimpan...' : 'Simpan Data Yayasan' }}
          </Button>
        </div>
      </div>
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
              Konfirmasi Simpan Data Yayasan
            </DialogTitle>
            <DialogDescription class="text-xs text-muted-foreground dark:text-zinc-400 mt-0.5">
              Mohon periksa kembali isian formulir sebelum melanjutkan.
            </DialogDescription>
          </div>
        </div>
      </DialogHeader>

      <div class="py-3 text-sm text-foreground dark:text-zinc-300 space-y-2">
        <p>Apakah Anda yakin data <strong>{{ form.nama }}</strong> sudah sesuai?</p>
        <div class="p-3 rounded-lg bg-accent/40 dark:bg-zinc-800/50 border border-border/50 dark:border-zinc-800 text-xs space-y-1">
          <div><span class="text-muted-foreground">Email Administrator:</span> <span class="font-semibold text-foreground dark:text-zinc-100">{{ form.email }}</span></div>
          <div><span class="text-muted-foreground">No. HP Administrator:</span> <span class="font-semibold text-foreground dark:text-zinc-100">{{ form.no_hp }}</span></div>
        </div>
      </div>

      <DialogFooter class="gap-2 sm:gap-0">
        <Button variant="outline" type="button" @click="isConfirmOpen = false" :disabled="isLoading">
          Kembali Periksa
        </Button>
        <Button type="button" :disabled="isLoading" @click="handleSubmit" class="gap-1.5">
          <CheckCircle2 class="h-4 w-4" />
          {{ isLoading ? 'Memproses...' : 'Ya, Simpan Sekarang' }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>

  <!-- Success Account Dialog -->
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
