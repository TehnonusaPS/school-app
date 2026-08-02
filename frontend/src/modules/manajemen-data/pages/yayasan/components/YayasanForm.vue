<script setup>
import { 
  Phone, 
  Image,
  Building2,
  FileText,
  Globe,
  MapPin,
  Sparkles,
  UserCog
} from 'lucide-vue-next'
import { Separator } from '@/components/ui/separator'
import ImageUpload from '@/components/forms/ImageUpload.vue'
import FormInput from '@/components/forms/FormInput.vue'
import FormTextArea from '@/components/forms/FormTextArea.vue'
import FormDate from '@/components/forms/FormDate.vue'
import FormSection from '@/components/forms/FormSection.vue'
import FormSelect from '@/components/forms/FormSelect.vue'
import CurriculumSelect from '@/components/curriculum/CurriculumSelect.vue'

const props = defineProps({
  form: { type: Object, required: true },
  statusOptions: { type: Array, default: () => [] },
  imagePreview: { type: String, default: '' },
  errors: { type: Object, default: () => ({}) }
})

const emit = defineEmits(['image-change'])

const handleImageChange = (file) => {
  emit('image-change', file)
}
</script>

<template>
  <div class="grid gap-6 md:grid-cols-3 text-left">
    <!-- Kolom Kiri: Logo, Kontak & Akun Login (Lebar 1 Kolom) -->
    <div class="space-y-6">
      <!-- Logo Yayasan -->
      <FormSection
        title="Logo Yayasan"
        description="Unggah identitas visual logo yayasan"
        :icon="Image"
      >
        <ImageUpload
          :preview="imagePreview"
          @change="handleImageChange"
          note="Format: JPG atau PNG. Maksimal 2MB. Dimensi rekomendasi 1:1"
          :error="errors.logo"
        />
      </FormSection>

      <!-- Informasi Kontak Resmi -->
      <FormSection
        title="Kontak Resmi Yayasan"
        description="Informasi email, telepon kantor, dan website resmi yayasan"
        :icon="Phone"
      >
        <FormInput
          v-model="form.email"
          label="E-mail Resmi Yayasan"
          placeholder="Contoh: info@yayasan.or.id"
          :error="errors.email"
        />

        <FormInput
          v-model="form.no_hp"
          label="No. Telepon Kantor"
          placeholder="Contoh: (021) 1234567"
          :error="errors.phone"
        />

        <FormInput
          v-model="form.website"
          label="Website Resmi"
          placeholder="Contoh: https://yayasan.or.id"
          :error="errors.website"
        />
      </FormSection>

      <!-- Akun Administrator (Login) -->
      <FormSection
        title="Akun Administrator Login"
        description="Data E-mail dan No. HP yang digunakan administrator yayasan untuk login ke sistem"
        :icon="UserCog"
      >
        <FormInput
          v-model="form.emailLogin"
          label="E-mail Login Administrator"
          placeholder="Contoh: admin@yayasan.or.id"
          :error="errors.emailLogin || errors.email"
          required
        />

        <FormInput
          v-model="form.noHpLogin"
          label="No. HP Login Administrator"
          placeholder="Contoh: 081234567890"
          :error="errors.noHpLogin || errors.phone"
          required
        />
      </FormSection>
    </div>

    <!-- Kolom Kanan: Informasi Umum & Legalitas Yayasan (Lebar 2 Kolom) -->
    <div class="md:col-span-2 space-y-6">
      <!-- Informasi Dasar -->
      <FormSection
        title="Informasi Dasar Yayasan"
        description="Data utama profil dan kurikulum acuan yayasan pendidikan"
        :icon="Building2"
      >
        <div class="grid gap-4 md:grid-cols-2">
          <FormInput
            v-model="form.nama"
            label="Nama Lengkap Yayasan"
            placeholder="Contoh: Yayasan Pendidikan Nusantara"
            :error="errors.name"
            required
          />
          <FormInput
            v-model="form.kode"
            label="Kode Yayasan"
            placeholder="Contoh: YPN001"
            :error="errors.code"
            required
          />
        </div>

        <div class="grid gap-4 md:grid-cols-2">
          <FormDate
            v-model="form.tanggal_berdiri"
            label="Tanggal Berdiri"
            :error="errors.established_date"
          />
          <FormSelect
            v-model="form.status"
            label="Status Operasional"
            placeholder="Pilih status yayasan"
            :options="statusOptions"
            :error="errors.status"
          />
        </div>

        <div class="pt-1">
          <CurriculumSelect
            v-model="form.curriculum_id"
            mode="foundation"
            label="Kurikulum Utama Yayasan"
            hint="Fokus Kurikulum Utama Yayasan: Kurikulum Merdeka atau Kurikulum 2013 (K13)."
            :error="errors.curriculum_id"
          />
        </div>
      </FormSection>

      <!-- Legalitas & Alamat -->
      <FormSection
        title="Dokumen Legalitas & Alamat"
        description="Rincian Nomor Akta Pendirian, SK Kemenkumham, dan alamat domisili yayasan"
        :icon="FileText"
      >
        <div class="grid gap-4 md:grid-cols-2">
          <FormInput
            v-model="form.no_akta"
            label="No. Akta Pendirian"
            placeholder="Contoh: AHU-0012345.AH.01.04"
            :error="errors.deed_number"
          />
          <FormDate
            v-model="form.tanggal_akta"
            label="Tanggal Akta Pendirian"
            :error="errors.deed_date"
          />
        </div>

        <div class="grid gap-4 md:grid-cols-2">
          <FormInput
            v-model="form.no_sk"
            label="No. SK Kemenkumham"
            placeholder="Contoh: SK-001/YPN/2010"
            :error="errors.decree_number"
          />
          <FormDate
            v-model="form.tanggal_sk"
            label="Tanggal SK Kemenkumham"
            :error="errors.decree_date"
          />
        </div>

        <Separator class="my-2 bg-border/60 dark:bg-zinc-800" />

        <FormTextArea
          v-model="form.alamat"
          label="Alamat Lengkap Yayasan"
          placeholder="Contoh: Jl. Pendidikan No. 123, RT 01/RW 05, Kel. Kayu Manis, Kec. Matraman, Kota Jakarta Timur"
          :rows="3"
          :error="errors.address"
        />
      </FormSection>
    </div>
  </div>
</template>
