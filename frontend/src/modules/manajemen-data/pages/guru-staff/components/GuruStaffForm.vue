<script setup>
import { Phone, Image, SquareUserRound, BriefcaseBusiness, UserCog } from 'lucide-vue-next'
import { Separator } from '@/components/ui/separator'
import ImageUpload from '@/components/forms/ImageUpload.vue'
import FormInput from '@/components/forms/FormInput.vue'
import FormTextArea from '@/components/forms/FormTextArea.vue'
import FormDate from '@/components/forms/FormDate.vue'
import FormSection from '@/components/forms/FormSection.vue'
import FormSelect from '@/components/forms/FormSelect.vue'
const props = defineProps({
  form: { type: Object, required: true },
  imagePreview: { type: String, default: '' },
  kelaminOptions: { type: Array, default: () => [] },
  agamaOptions: { type: Array, default: () => [] },
  statusPernikahanOptions: { type: Array, default: () => [] },
  pendidikanOptions: { type: Array, default: () => [] },
  jabatanOptions: { type: Array, default: () => [] },
  statusKepegawaianOptions: { type: Array, default: () => [] },
  unitKerjaOptions: { type: Array, default: () => [] },
  statusOptions: { type: Array, default: () => [] },
  errors: { type: Object, default: () => ({}) }
})
const emit = defineEmits(['image-change'])

const handleImageChange = file => {
  emit('image-change', file)
}
</script>

<template>
    <div class="grid gap-6 md:grid-cols-3">
      <!-- Kolom Kiri: Data Logo & Kontak (Lebar 1 Kolom) -->
      <div class="space-y-6">
        <!-- Foto Profil -->
        <FormSection title="Foto Profil" description="Upload foto profil dari guru/staff disini" :icon="Image">
            <ImageUpload :preview="imagePreview" @change="handleImageChange" note="Format: JPG atau PNG. Maksimal 2MB. Dimensi rasio 1:1" :error="errors.foto"/>
        </FormSection>

        <!-- Informasi Kontak -->
        <FormSection title="Kontak & Alamat Guru/Staff" description="Informasi kontak dari guru/staff" :icon="Phone">
            <FormInput v-model="form.email" label="E-mail" placeholder="Contoh: nama@sekolah.com" :error="errors.email"/>
            <FormInput v-model="form.no_hp" label="No. Telp" placeholder="Contoh: 081289170180" :error="errors.no_hp"/>
            <Separator class="my-4" />
            <FormTextArea v-model="form.alamat" :rows="6" label="Alamat Lengkap" placeholder="Contoh: Nama Jalan, RT/RW, Kelurahan, Kecamatan, Kota" :error="errors.alamat"/>
        </FormSection>
      </div>
      <!-- Kolom Kanan: Informasi Pribadi (Lebar 2 Kolom) -->
      <div class="md:col-span-2 space-y-6">
        <FormSection title="Informasi Pribadi" description="Informasi dasar mengenai guru/staff" :icon="SquareUserRound">
          <div class="grid gap-4 md:grid-cols-2">
              <FormInput v-model="form.nama_depan" label="Nama Depan" placeholder="Contoh: John" :error="errors.nama_depan"/>
              <FormInput v-model="form.nama_belakang" label="Nama Belakang" placeholder="Contoh: Doe" :error="errors.nama_belakang"/>
          </div>
          <div class="grid gap-4 md:grid-cols-2">
              <FormInput v-model="form.nik" label="NIK" placeholder="Contoh: 1234567890" :error="errors.nik"/>
              <FormInput v-model="form.nip_nuptk" label="NIP/NUPTK" placeholder="Contoh: 1234567890" :error="errors.nip_nuptk"/>
          </div>
          <div class="grid gap-4 md:grid-cols-2">
              <FormInput v-model="form.tempat_lahir" label="Tempat Lahir" placeholder="Contoh: Jakarta" :error="errors.tempat_lahir"/>
              <FormDate v-model="form.tanggal_lahir" label="Tanggal Lahir" :error="errors.tanggal_lahir"/>
          </div>
          <div class="grid gap-4 md:grid-cols-2">
              <FormSelect v-model="form.jenis_kelamin" label="Jenis Kelamin" placeholder="Pilih jenis kelamin" :options="kelaminOptions" :error="errors.jenis_kelamin"/>
              <FormSelect v-model="form.agama" label="Agama" placeholder="Pilih agama" :options="agamaOptions" :error="errors.agama"/>
          </div>
          <div class="grid gap-4 md:grid-cols-2">
              <FormSelect v-model="form.status_pernikahan" label="Status Pernikahan" placeholder="Pilih status pernikahan" :options="statusPernikahanOptions" :error="errors.status_pernikahan"/>
              <FormSelect v-model="form.pendidikan_terakhir" label="Pendidikan Terakhir" placeholder="Pilih pendidikan terakhir" :options="pendidikanOptions" :error="errors.pendidikan_terakhir"/>
          </div>
          <div class="grid gap-4 md:grid-cols-2">
              <FormInput v-model="form.gelar_depan" label="Gelar Depan" placeholder="Contoh: Drs." :error="errors.gelar_depan"/>
              <FormInput v-model="form.gelar_belakang" label="Gelar Belakang" placeholder="Contoh: S.Pd., M.Pd." :error="errors.gelar_belakang"/>
          </div>
        </FormSection>

        <FormSection title="Informasi Kepegawaian" description="Informasi kepegawaian mengenai guru/staff" :icon="BriefcaseBusiness">
          <div class="grid gap-4 md:grid-cols-2">
            <FormSelect v-model="form.jabatan" label="Jabatan" placeholder="Pilih jabatan" :options="jabatanOptions" :error="errors.jabatan"/>
            <FormSelect v-model="form.status_kepegawaian" label="Status Kepegawaian" placeholder="Pilih status kepegawaian" :options="statusKepegawaianOptions" :error="errors.status_kepegawaian"/>
          </div>
          <div class="grid gap-4 md:grid-cols-2">
            <FormSelect v-model="form.unit_kerja" label="Unit Kerja" placeholder="Pilih unit kerja" :options="unitKerjaOptions" :error="errors.unit_kerja"/>
            <FormSelect v-model="form.status_aktif" label="Status Aktif" placeholder="Pilih status" :options="statusOptions" :error="errors.status_aktif"/>
          </div>
        </FormSection>

        <!-- Informasi Login -->
        <FormSection title="Akun Pengguna" description="Informasi akun yang digunakan guru atau staff untuk mengakses sistem." :icon="UserCog">
          <div class="grid gap-4 md:grid-cols-2">
            <FormInput v-model="form.emailLogin" label="E-mail Login" placeholder="Contoh: admin@sekolah.sch.id" :error="errors.emailLogin"/>
            <FormInput v-model="form.noHpLogin" label="No. HP Login" placeholder="Contoh: 081234567890" :error="errors.noHpLogin"/>
          </div>
        </FormSection>
      </div>
    </div>
</template>
