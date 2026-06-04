<script setup>
import { 
  Phone, 
  Image,
  SquareUserRound,
  BriefcaseBusiness
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
import { agamaOptions, jabatan, kelaminOptions, pendidikanOptions, statusKepegawaian, statusOptions, statusPernikahanOptions, unitKerja } from './data/guruStaffYayasan'
// import { agamaOptions, kelaminOptions, pendidikanOptions, statusPernikahanOptions } from './data/guru-staff-yayasan'

const router = useRouter()
const isLoading = ref(false)

// State Form
const form = ref({
  nama_depan: '',
  nama_belakang: '',
  nik: '',
  nip_nuptk: '',
  tempat_lahir: '',
  tanggal_lahir: '',
  jenis_kelamin: '',
  agama: '',
  status_pernikahan: '',
  pendidikan_terakhir: '',
  gelar_depan: '',
  gelar_belakang: '',
  email: '',
  no_hp: '',
  alamat: '',
  jabatan: '',
  status_kepegawaian: '',
  unit_kerja: '',
  status_aktif: ''
})

const imagePreview = ref('')

const handleImage = (file) => {
  imagePreview.value = URL.createObjectURL(file)
}

const goBack = () => router.back()

const handleSubmit = () => {
  isLoading.value = true
  // Simulasi simpan data
  setTimeout(() => {
    isLoading.value = false
    alert('Data guru/staff berhasil disimpan!')
    router.push('/manajemen-data/guru-staff-yayasan')
  }, 1500)
}
</script>

<template>
  <div class="space-y-6 p-1 pb-10">
    <!-- Header dengan Tombol Kembali -->
    <PageHeader
    variant="form"
    title="Tambah Guru/Staff"
    description="Lengkapi formulir berikut untuk menambahkan data guru atau staff baru"
    :loading="isLoading"
    save-text="Simpan Data Guru/Staff"
    @back="goBack"
    @cancel="goBack"
    @save="handleSubmit"
    />

    <div class="grid gap-6 md:grid-cols-3">
      <!-- Kolom Kiri: Data Logo & Kontak (Lebar 1 Kolom) -->
      <div class="space-y-6">
        <!-- Foto Profil -->
        <FormSection title="Foto Profil" description="Upload foto profil dari guru/staff disini" :icon="Image">
            <ImageUpload :preview="imagePreview" @change="handleImage" note="Format: JPG atau PNG. Maksimal 2MB. Dimensi rasio 1:1"/>
        </FormSection>

        <!-- Informasi Kontak -->
        <FormSection title="Kontak Guru/Staff" description="Informasi kontak dari guru/staff" :icon="Phone">
            <FormInput v-model="form.email" label="E-mail" placeholder="Contoh: nama@sekolah.com"/>
            <FormInput v-model="form.no_hp" label="No. Telp" placeholder="Contoh: 081289170180"/>
        </FormSection>
      </div>
      <!-- Kolom Kanan: Informasi Pribadi (Lebar 2 Kolom) -->
      <div class="md:col-span-2 space-y-6">
        <FormSection title="Informasi Pribadi" description="Informasi dasar mengenai guru/staff" :icon="SquareUserRound">
          <div class="grid gap-4 md:grid-cols-2">
              <FormInput v-model="form.nama_depan" label="Nama Depan" placeholder="Contoh: John"/>
              <FormInput v-model="form.nama_belakang" label="Nama Belakang" placeholder="Contoh: Doe"/>
          </div>
          <div class="grid gap-4 md:grid-cols-2">
              <FormInput v-model="form.nik" label="NIK" placeholder="Contoh: 1234567890"/>
              <FormInput v-model="form.nip_nuptk" label="NIP/NUPTK" placeholder="Contoh: 1234567890"/>
          </div>
          <div class="grid gap-4 md:grid-cols-2">
              <FormInput v-model="form.tempat_lahir" label="Tempat Lahir" placeholder="Contoh: Jakarta"/>
              <FormDate v-model="form.tanggal_lahir" label="Tanggal Lahir"/>
          </div>
          <div class="grid gap-4 md:grid-cols-2">
              <FormSelect v-model="form.jenis_kelamin" label="Jenis Kelamin" placeholder="Pilih jenis kelamin" :options="kelaminOptions"/>
              <FormSelect v-model="form.agama" label="Agama" placeholder="Pilih agama" :options="agamaOptions"/>
          </div>
          <div class="grid gap-4 md:grid-cols-2">
              <FormSelect v-model="form.status_pernikahan" label="Status Pernikahan" placeholder="Pilih status pernikahan" :options="statusPernikahanOptions"/>
              <FormSelect v-model="form.pendidikan_terakhir" label="Pendidikan Terakhir" placeholder="Pilih pendidikan terakhir" :options="pendidikanOptions"/>
          </div>
          <div class="grid gap-4 md:grid-cols-2">
              <FormInput v-model="form.gelar_depan" label="Gelar Depan" placeholder="Contoh: Drs."/>
              <FormInput v-model="form.gelar_belakang" label="Gelar Belakang" placeholder="Contoh: S.Pd., M.Pd."/>
          </div>
          <Separator class="my-4" />
          <FormTextArea v-model="form.alamat" label="Alamat Lengkap" placeholder="Contoh: Nama Jalan, RT/RW, Kelurahan, Kecamatan, Kota"/>
        </FormSection>

        <FormSection title="Informasi Kepegawaian" description="Informasi kepegawaian mengenai guru/staff" :icon="BriefcaseBusiness">
          <div class="grid gap-4 md:grid-cols-2">
            <FormSelect v-model="form.jabatan" label="Jabatan" placeholder="Pilih jabatan" :options="jabatan"/>
            <FormSelect v-model="form.status_kepegawaian" label="Status Kepegawaian" placeholder="Pilih status kepegawaian" :options="statusKepegawaian"/>
          </div>
          <div class="grid gap-4 md:grid-cols-2">
            <FormSelect v-model="form.unit_kerja" label="Unit Kerja" placeholder="Pilih unit kerja" :options="unitKerja"/>
            <FormSelect v-model="form.status_aktif" label="Status Aktif" placeholder="Pilih status" :options="statusOptions"/>
          </div>
        </FormSection>
      </div>
    </div>
  </div>
</template>
