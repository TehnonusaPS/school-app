<script setup>
import { 
  Phone, 
  Image,
  Building2,
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
    <div class="grid gap-6 md:grid-cols-3">
      <!-- Kolom Kiri: Data Logo & Kontak (Lebar 1 Kolom) -->
      <div class="space-y-6">
        <!-- Logo Yayasan -->
        <FormSection title="Logo Yayasan" description="Upload logo yayasan disini" :icon="Image">
            <ImageUpload :preview="imagePreview" @change="handleImageChange" note="Format: JPG atau PNG. Maksimal 2MB. Dimensi rasio 1:1" :error="errors.logo"/>
        </FormSection>
        <!-- Informasi Kontak -->
        <FormSection title="Kontak Yayasan" description="Informasi kontak dari yayasan" :icon="Phone">
            <FormInput v-model="form.email" label="E-mail" placeholder="Contoh: yayasan@mail.com" :error="errors.email"/>
            <FormInput v-model="form.no_hp" label="No. Telp" placeholder="Contoh: 081289170180" :error="errors.phone"/>
            <FormInput v-model="form.website" label="Website" placeholder="Contoh: www.yayasan.com" :error="errors.website"/>
        </FormSection>
      </div>
      <!-- Kolom Kanan: Informasi Umum Yayasan (Lebar 2 Kolom) -->
      <div class="md:col-span-2 space-y-6">
        <FormSection title="Informasi Umum" description="Informasi dasar mengenai yayasan" :icon="Building2">
            <div class="grid gap-4 md:grid-cols-2">
                <FormInput v-model="form.nama" label="Nama Yayasan" placeholder="Contoh: Yayasan Cerdas Bangsa" :error="errors.name"/>
                <FormInput v-model="form.kode" label="Kode Yayasan" placeholder="Contoh: YCB001" :error="errors.code"/>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <FormDate v-model="form.tanggal_berdiri" label="Tanggal Berdiri" :error="errors.established_date"/>
                <FormSelect v-model="form.status" label="Status" placeholder="Pilih status yayasan" :options="statusOptions" :error="errors.status"/>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <FormInput v-model="form.no_akta" label="No. Akta Pendirian" placeholder="Contoh: 1234567890" :error="errors.deed_number"/>
                <FormDate v-model="form.tanggal_akta" label="Tanggal Akta Pendirian" :error="errors.deed_date"/>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <FormInput v-model="form.no_sk" label="No. SK Kemenkumham" placeholder="Contoh: 1234567890" :error="errors.decree_number"/>
                <FormDate v-model="form.tanggal_sk" label="Tanggal SK Kemenkumham" :error="errors.decree_date"/>
            </div>
            <Separator class="my-4" />
            <FormTextArea v-model="form.alamat" label="Alamat Lengkap" placeholder="Contoh: Nama Jalan, RT/RW, Kelurahan, Kecamatan, Kota" :error="errors.address"/>
        </FormSection>

        <!-- Informasi Login -->
        <FormSection title="Akun Administrator" description="Data akun yang digunakan oleh administrator yayasan untuk mengakses sistem." :icon="UserCog">
          <div class="grid gap-4 md:grid-cols-2">
            <FormInput v-model="form.emailLogin" label="E-mail Login" placeholder="Contoh: admin@yayasan.id" :error="errors.emailLogin"/>
            <FormInput v-model="form.noHpLogin" label="No. HP Login" placeholder="Contoh: 081234567890" :error="errors.noHpLogin"/>
          </div>
        </FormSection>
      </div>
    </div>
</template>
