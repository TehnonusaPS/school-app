<script setup>
import { 
  Phone, 
  Image,
  Building2,
  UserCog
} from 'lucide-vue-next'
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Separator } from '@/components/ui/separator'
import PageHeader from '@/components/page-header/PageHeader.vue'
import ImageUpload from '@/components/forms/ImageUpload.vue'
import FormInput from '@/components/forms/FormInput.vue'
import FormTextArea from '@/components/forms/FormTextArea.vue'
import FormDate from '@/components/forms/FormDate.vue'
import FormSection from '@/components/forms/FormSection.vue'
import FormSelect from '@/components/forms/FormSelect.vue'
import SuccessAccountDialog from '@/components/dialogs/SuccessAccountDialog.vue'

const router = useRouter()
const isLoading = ref(false)

// State Form
const form = ref({
  kode: '',
  nama: '',
  tanggal_berdiri: '',
  status: '',
  alamat: '',
  email: '',
  no_hp: '',
  website: '',
  no_akta: '',
  tanggal_akta: '',
  no_sk: '',
  tanggal_sk: '',
  emailLogin: '',
  noHpLogin: ''
})

const statusOptions = [
  {
    label: 'Aktif',
    value: '1'
  },
  {
    label: 'Tidak Aktif',
    value: '0'
  }
]

const imagePreview = ref('')

const handleImage = (file) => {
  imagePreview.value = URL.createObjectURL(file)
}

const goBack = () => router.back()

const showSuccessModal = ref(false)

const generatedAccount = ref({
  email: 'admin@foundation.id',
  phone: '081234567890',
  password: 'Adm#2026!'
})

const handleSubmit = () => {
  isLoading.value = true

  setTimeout(() => {
    isLoading.value = false
    showSuccessModal.value = true
  }, 1000)
}

const goToList = () => {
  router.push('/manajemen-data/yayasan')
}
</script>

<template>
  <div class="space-y-6 p-1 pb-10">
    <!-- Header dengan Tombol Kembali -->
    <PageHeader
    variant="form"
    title="Tambah Yayasan"
    description="Lengkapi formulir berikut untuk menambahkan data yayasan baru"
    :loading="isLoading"
    save-text="Simpan Data"
    @back="goBack"
    @cancel="goBack"
    @save="handleSubmit"
    />

    <div class="grid gap-6 md:grid-cols-3">
      <!-- Kolom Kiri: Data Logo & Kontak (Lebar 1 Kolom) -->
      <div class="space-y-6">
        <!-- Logo Yayasan -->
        <FormSection title="Logo Yayasan" description="Upload logo yayasan disini" :icon="Image">
            <ImageUpload :preview="imagePreview" @change="handleImage" note="Format: JPG atau PNG. Maksimal 2MB. Dimensi rasio 1:1"/>
        </FormSection>
        <!-- Informasi Kontak -->
        <FormSection title="Kontak Yayasan" description="Informasi kontak dari yayasan" :icon="Phone">
            <FormInput v-model="form.email" label="E-mail" placeholder="Contoh: yayasan@mail.com"/>
            <FormInput v-model="form.no_hp" label="No. Telp" placeholder="Contoh: 081289170180"/>
            <FormInput v-model="form.website" label="Website" placeholder="Contoh: www.yayasan.com"/>
        </FormSection>
      </div>
      <!-- Kolom Kanan: Informasi Umum Yayasan (Lebar 2 Kolom) -->
      <div class="md:col-span-2 space-y-6">
        <FormSection title="Informasi Umum" description="Informasi dasar mengenai yayasan" :icon="Building2">
            <div class="grid gap-4 md:grid-cols-2">
                <FormInput v-model="form.nama" label="Nama Yayasan" placeholder="Contoh: Yayasan Cerdas Bangsa"/>
                <FormInput v-model="form.kode" label="Kode Yayasan" placeholder="Contoh: YCB001"/>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <FormDate v-model="form.tanggal_berdiri" label="Tanggal Berdiri"/>
                <FormSelect v-model="form.status" label="Status" placeholder="Pilih status yayasan" :options="statusOptions"/>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <FormInput v-model="form.no_akta" label="No. Akta Pendirian" placeholder="Contoh: 1234567890"/>
                <FormDate v-model="form.tanggal_akta" label="Tanggal Akta Pendirian"/>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <FormInput v-model="form.no_sk" label="No. SK Kemenkumham" placeholder="Contoh: 1234567890"/>
                <FormDate v-model="form.tanggal_sk" label="Tanggal SK Kemenkumham"/>
            </div>
            <Separator class="my-4" />
            <FormTextArea v-model="form.alamat" label="Alamat Lengkap" placeholder="Contoh: Nama Jalan, RT/RW, Kelurahan, Kecamatan, Kota"/>
        </FormSection>

        <!-- Informasi Login -->
        <FormSection title="Akun Administrator" description="Data akun yang digunakan oleh administrator yayasan untuk mengakses sistem." :icon="UserCog">
          <div class="grid gap-4 md:grid-cols-2">
            <FormInput v-model="form.emailLogin" label="E-mail Login" placeholder="Contoh: admin@yayasan.id"/>
            <FormInput v-model="form.noHpLogin" label="No. HP Login" placeholder="Contoh: 081234567890"/>
          </div>
        </FormSection>
      </div>
    </div>
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
