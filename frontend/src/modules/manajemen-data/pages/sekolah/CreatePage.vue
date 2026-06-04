<script setup>
import { 
  Phone, 
  Image,
  School,
  Scale,
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
import { akreditasi, jenjangOptions, statusOptions, yayasanOptions } from './data/sekolah'
import { useAuthStore } from '@/stores/authStore'
import { computed } from 'vue'
import SuccessAccountDialog from '@/components/dialogs/SuccessAccountDialog.vue'

const auth = useAuthStore()
const isSuperAdmin = computed(() => auth.user?.role === 'superadmin')

const foundationOptions = computed(() => {
  if (isSuperAdmin.value) {
    return yayasanOptions.value
  }

  return yayasanOptions.filter(
    item => item.value === auth.user?.yayasanId
  )
})

const router = useRouter()
const isLoading = ref(false)

// State Form
const form = ref({
  nama: '',
  npsn: '',
  yayasan: '',
  jenjang: '',
  tanggal_berdiri: '',
  status: '',
  alamat: '',
  email: '',
  no_hp: '',
  website: '',
  instagram: '',
  facebook: '',
  no_sk: '',
  tanggal_sk: '',
  no_izin: '',
  tanggal_izin: '',
  akreditasi: '',
  tanggal_akreditasi: '',
  no_akreditasi: '',
  emailLogin: '',
  noHpLogin: ''
})

const imagePreview = ref('')

const handleImage = (file) => {
  imagePreview.value = URL.createObjectURL(file)
}

const goBack = () => router.back()

const showSuccessModal = ref(false)

const generatedAccount = ref({
  email: 'admin@sdnusantara.sch.id',
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
    title="Tambah Sekolah"
    description="Lengkapi formulir berikut untuk menambahkan data sekolah baru"
    :loading="isLoading"
    save-text="Simpan Data"
    @back="goBack"
    @cancel="goBack"
    @save="handleSubmit"
    />

    <div class="grid gap-6 md:grid-cols-3">
      <!-- Kolom Kiri: Data Logo & Kontak (Lebar 1 Kolom) -->
      <div class="space-y-6">
        <!-- Logo Sekolah -->
        <FormSection title="Logo Sekolah" description="Upload logo sekolah disini" :icon="Image">
            <ImageUpload :preview="imagePreview" @change="handleImage" note="Format: JPG atau PNG. Maksimal 2MB. Dimensi rasio 1:1"/>
        </FormSection>

        <!-- Informasi Kontak -->
        <FormSection title="Kontak Sekolah" description="Informasi kontak dari sekolah" :icon="Phone">
            <FormInput v-model="form.email" label="E-mail" placeholder="Contoh: sekolah@yayasan.com"/>
            <FormInput v-model="form.no_hp" label="No. Telp" placeholder="Contoh: 081289170180"/>
            <FormInput v-model="form.website" label="Website Sekolah" placeholder="Contoh: www.sekolah-yayasan.com"/>
            <FormInput v-model="form.instagram" label="Instagram" placeholder="Contoh: @sekolah_yayasan"/>
            <FormInput v-model="form.facebook" label="Facebook" placeholder="Contoh: @sekolah_yayasan"/>
        </FormSection>
      </div>
      <!-- Kolom Kanan: Informasi Umum Sekolah (Lebar 2 Kolom) -->
      <div class="md:col-span-2 space-y-6">
        <FormSection title="Informasi Umum" description="Informasi dasar mengenai sekolah" :icon="School">
            <div class="grid gap-4 md:grid-cols-2">
                <FormInput v-model="form.nama" label="Nama Sekolah" placeholder="Contoh: Sekolah Cerdas Bangsa 1 Tebet"/>
                <FormInput v-model="form.kode" label="NPSN" placeholder="Contoh: YCB001"/>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <FormSelect v-model="form.yayasan" label="Nama Yayasan" placeholder="Pilih yayasan" :options="foundationOptions"/>
                <FormSelect v-model="form.jenjang" label="Jenjang Pendidikan" placeholder="Pilih jenjang" :options="jenjangOptions"/>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <FormDate v-model="form.tanggal_berdiri" label="Tanggal Berdiri"/>
                <FormSelect v-model="form.status" label="Status" placeholder="Pilih status sekolah" :options="statusOptions"/>
            </div>
            <Separator class="my-4" />
            <FormTextArea v-model="form.alamat" label="Alamat Lengkap" placeholder="Contoh: Nama Jalan, RT/RW, Kelurahan, Kecamatan, Kota"/>
        </FormSection>

        <!-- Informasi Legalitas Sekolah -->
        <FormSection title="Data Legalitas" description="Informasi mengenai legalitas sekolah" :icon="Scale">
            <div class="grid gap-4 md:grid-cols-2">
                <FormInput v-model="form.no_sk" label="No. SK Pendirian" placeholder="Contoh: 1234567890"/>
                <FormDate v-model="form.tanggal_sk" label="Tanggal SK Pendirian"/>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <FormInput v-model="form.no_izin" label="No. Izin Operasional" placeholder="Contoh: 1234567890"/>
                <FormDate v-model="form.tanggal_izin" label="Tanggal Izin Operasional"/>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <FormSelect v-model="form.akreditasi" label="Akreditasi" placeholder="Pilih akreditasi" :options="akreditasi"/>
                <FormDate v-model="form.tanggal_akreditasi" label="Tanggal Akreditasi"/>
            </div>
            <FormInput v-model="form.no_akreditasi" label="No. SK Akreditasi" placeholder="Contoh: 1234567890"/>
        </FormSection>

        <!-- Informasi Login -->
        <FormSection title="Akun Administrator" description="Data akun yang digunakan oleh administrator sekolah untuk mengakses sistem." :icon="UserCog">
          <div class="grid gap-4 md:grid-cols-2">
            <FormInput v-model="form.emailLogin" label="E-mail Login" placeholder="Contoh: admin@sekolah.sch.id"/>
            <FormInput v-model="form.noHpLogin" label="No. HP Login" placeholder="Contoh: 081234567890"/>
          </div>
        </FormSection>
      </div>
    </div>
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
