<script setup>
import { 
  Phone, 
  Image,
  UserCog,
  SquareUserRound,
  GraduationCap,
  ShieldUser
} from 'lucide-vue-next'
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
  statusOptions: { type: Array, default: () => [] },
  kelasOptions: { type: Array, default: () => [] },
  pekerjaanOptions: { type: Array, default: () => [] },
  agamaOptions: { type: Array, default: () => [] },
  kelaminOptions: { type: Array, default: () => [] },
  hubunganOptions: { type: Array, default: () => [] },
})

const emit = defineEmits(['image-change'])

const handleImageChange = (file) => {
  emit('image-change', file)
}
</script>

<template>
    <div class="grid gap-6 md:grid-cols-3">
      <!-- Kolom Kiri: Data Logo & Kontak (Lebar 1 Kolom) -->
      <div class="space-y-6">
        <!-- Foto Siswa -->
        <FormSection title="Foto Profil" description="Upload foto profil siswa disini" :icon="Image">
            <ImageUpload :preview="imagePreview" @change="handleImageChange" note="Format: JPG atau PNG. Maksimal 2MB. Dimensi rasio 1:1"/>
        </FormSection>

        <!-- Informasi Kontak -->
        <FormSection title="Kontak Siswa" description="Informasi kontak dari siswa" :icon="Phone">
            <FormInput v-model="form.email" label="E-mail" placeholder="Contoh: nama@sekolah.com"/>
            <FormInput v-model="form.no_hp" label="No. Telp" placeholder="Contoh: 081289170180"/>
            <!-- <Separator class="my-4" />
            <FormTextArea v-model="form.alamat" :rows="6" label="Alamat Lengkap" placeholder="Contoh: Nama Jalan, RT/RW, Kelurahan, Kecamatan, Kota"/> -->
        </FormSection>

        <!-- Informasi Akademik -->
        <FormSection title="Informasi Akademik" description="Informasi akademik siswa" :icon="GraduationCap">
            <FormDate v-model="form.tahun_masuk" label="Tahun Masuk"/>
            <FormSelect v-model="form.kelas" label="Kelas" placeholder="Pilih kelas" :options="kelasOptions"/>
            <FormSelect v-model="form.status" label="Status" placeholder="Pilih status" :options="statusOptions"/>
        </FormSection>
      </div>

      <!-- Kolom Kanan: Informasi Pribadi & Akun Pengguna (Lebar 2 Kolom) -->
      <div class="md:col-span-2 space-y-6">
        <FormSection title="Informasi Siswa" description="Informasi dasar mengenai siswa" :icon="SquareUserRound">
          <div class="grid gap-4 md:grid-cols-2">
              <FormInput v-model="form.nama_depan" label="Nama Depan" placeholder="Contoh: John"/>
              <FormInput v-model="form.nama_belakang" label="Nama Belakang" placeholder="Contoh: Doe"/>
          </div>
          <div class="grid gap-4 md:grid-cols-2">
              <FormInput v-model="form.nik" label="NIK" placeholder="Contoh: 1234567890"/>
              <FormInput v-model="form.nisn" label="NISN" placeholder="Contoh: 1234567890"/>
          </div>
          <div class="grid gap-4 md:grid-cols-2">
              <FormInput v-model="form.tempat_lahir" label="Tempat Lahir" placeholder="Contoh: Jakarta"/>
              <FormDate v-model="form.tanggal_lahir" label="Tanggal Lahir"/>
          </div>
          <div class="grid gap-4 md:grid-cols-2">
              <FormSelect v-model="form.jenis_kelamin" label="Jenis Kelamin" placeholder="Pilih jenis kelamin" :options="kelaminOptions"/>
              <FormSelect v-model="form.agama" label="Agama" placeholder="Pilih agama" :options="agamaOptions"/>
          </div>  
          <Separator class="my-4" />
          <FormTextArea v-model="form.alamat" :rows="6" label="Alamat Lengkap" placeholder="Contoh: Nama Jalan, RT/RW, Kelurahan, Kecamatan, Kota"/>
        </FormSection>

        <FormSection title="Informasi Orang Tua / Wali" description="Informasi dasar mengenai orang tua / wali siswa" :icon="ShieldUser">
          <div class="grid gap-4 md:grid-cols-2">
              <FormInput v-model="form.nama_wali" label="Nama Wali" placeholder="Contoh: John Doe"/>
              <FormSelect v-model="form.hubungan_siswa" label="Hubungan dengan Siswa" placeholder="Pilih hubungan" :options="hubunganOptions"/>
          </div> 
          <div class="grid gap-4 md:grid-cols-2">
              <FormSelect v-model="form.kelamin_wali" label="Jenis Kelamin Wali" placeholder="Pilih jenis kelamin" :options="kelaminOptions"/>
              <FormSelect v-model="form.pekerjaan_wali" label="Pekerjaan Wali" placeholder="Pilih pekerjaan" :options="pekerjaanOptions"/>
          </div> 
          <Separator class="my-4" />
          <div class="grid gap-4 md:grid-cols-2">
              <FormInput v-model="form.email_wali" label="E-mail Wali" placeholder="Contoh: john.doe@sekolah.com"/>
              <FormInput v-model="form.no_hp_wali" label="No. HP Wali" placeholder="Contoh: 081234567890"/>
          </div> 
        </FormSection>

        <!-- Informasi Login -->
        <FormSection title="Akun Pengguna Orang Tua/Wali" description="Informasi akun yang digunakan orang tua/wali siswa untuk mengakses sistem." :icon="UserCog">
          <div class="grid gap-4 md:grid-cols-2">
            <FormInput v-model="form.emailLogin" label="E-mail Login" placeholder="Contoh: nama_ortu@gmail.com"/>
            <FormInput v-model="form.noHpLogin" label="No. HP Login" placeholder="Contoh: 081234567890"/>
          </div>
        </FormSection>
      </div>
    </div>

</template>
