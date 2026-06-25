<script setup>
import { 
  Phone, 
  Image,
  School,
  Scale,
  UserCog
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
  foundationOptions: { type: Array, default: () => [] },
  jenjangOptions: { type: Array, default: () => [] },
  statusOptions: { type: Array, default: () => [] },
  akreditasi: { type: Array, default: () => [] },
  errors: { type: Object, default: () => ({}) }
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
        <!-- Logo Sekolah -->
        <FormSection title="Logo Sekolah" description="Upload logo sekolah disini" :icon="Image">
            <ImageUpload :preview="imagePreview" @change="handleImageChange" note="Format: JPG atau PNG. Maksimal 2MB. Dimensi rasio 1:1" :error="errors.logo"/>
        </FormSection>

        <!-- Informasi Kontak -->
        <FormSection title="Kontak Sekolah" description="Informasi kontak dari sekolah" :icon="Phone">
            <FormInput v-model="form.email" label="E-mail" placeholder="Contoh: sekolah@yayasan.com" :error="errors.email"/>
            <FormInput v-model="form.no_hp" label="No. Telp" placeholder="Contoh: 081289170180" :error="errors.phone"/>
            <FormInput v-model="form.website" label="Website Sekolah" placeholder="Contoh: www.sekolah-yayasan.com" :error="errors.website"/>
            <FormInput v-model="form.instagram" label="Instagram" placeholder="Contoh: @sekolah_yayasan" :error="errors.instagram"/>
            <FormInput v-model="form.facebook" label="Facebook" placeholder="Contoh: @sekolah_yayasan" :error="errors.facebook"/>
        </FormSection>
      </div>

      <!-- Kolom Kanan: Informasi Umum Sekolah (Lebar 2 Kolom) -->
      <div class="md:col-span-2 space-y-6">
        <FormSection title="Informasi Umum" description="Informasi dasar mengenai sekolah" :icon="School">
            <div class="grid gap-4 md:grid-cols-2">
                <FormInput v-model="form.nama" label="Nama Sekolah" placeholder="Contoh: Sekolah Cerdas Bangsa 1 Tebet" :error="errors.name"/>
                <FormInput v-model="form.npsn" label="NPSN" placeholder="Contoh: YCB001" :error="errors.npsn"/>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <FormSelect v-model="form.yayasan" label="Nama Yayasan" placeholder="Pilih yayasan" :options="foundationOptions" :error="errors.foundation_id"/>
                <FormSelect v-model="form.jenjang" label="Jenjang Pendidikan" placeholder="Pilih jenjang" :options="jenjangOptions" :error="errors.level"/>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <FormDate v-model="form.tanggal_berdiri" label="Tanggal Berdiri" :error="errors.established_date"/>
                <FormSelect v-model="form.status" label="Status" placeholder="Pilih status sekolah" :options="statusOptions" :error="errors.status"/>
            </div>
            <Separator class="my-4" />
            <FormTextArea v-model="form.alamat" label="Alamat Lengkap" placeholder="Contoh: Nama Jalan, RT/RW, Kelurahan, Kecamatan, Kota" :error="errors.address"/>
        </FormSection>

        <!-- Informasi Legalitas Sekolah -->
        <FormSection title="Data Legalitas" description="Informasi mengenai legalitas sekolah" :icon="Scale">
            <div class="grid gap-4 md:grid-cols-2">
                <FormInput v-model="form.no_sk" label="No. SK Pendirian" placeholder="Contoh: 1234567890" :error="errors.decree_number"/>
                <FormDate v-model="form.tanggal_sk" label="Tanggal SK Pendirian" :error="errors.decree_date"/>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <FormInput v-model="form.no_izin" label="No. Izin Operasional" placeholder="Contoh: 1234567890" :error="errors.permit_number"/>
                <FormDate v-model="form.tanggal_izin" label="Tanggal Izin Operasional" :error="errors.permit_date"/>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <FormSelect v-model="form.akreditasi" label="Akreditasi" placeholder="Pilih akreditasi" :options="akreditasi" :error="errors.accreditation"/>
                <FormDate v-model="form.tanggal_akreditasi" label="Tanggal Akreditasi" :error="errors.accreditation_date"/>
            </div>
            <FormInput v-model="form.no_akreditasi" label="No. SK Akreditasi" placeholder="Contoh: 1234567890" :error="errors.accreditation_number"/>
        </FormSection>

        <!-- Informasi Login -->
        <FormSection title="Akun Administrator" description="Data akun yang digunakan oleh administrator sekolah untuk mengakses sistem." :icon="UserCog">
          <div class="grid gap-4 md:grid-cols-2">
            <FormInput v-model="form.emailLogin" label="E-mail Login" placeholder="Contoh: admin@sekolah.sch.id" :error="errors.emailLogin"/>
            <FormInput v-model="form.noHpLogin" label="No. HP Login" placeholder="Contoh: 081234567890" :error="errors.noHpLogin"/>
          </div>
        </FormSection>
      </div>
    </div>
</template>
