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
  akreditasi: { type: Array, default: () => [] }
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
            <ImageUpload :preview="imagePreview" @change="handleImageChange" note="Format: JPG atau PNG. Maksimal 2MB. Dimensi rasio 1:1"/>
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

</template>
