<script setup>
import { ref, computed } from 'vue'
import { Save, Phone, Image, SquareUserRound, Building2, Key } from 'lucide-vue-next'
import { toast } from 'vue-sonner'
import PageHeader from '@/components/page-header/PageHeader.vue'
import FormSection from '@/components/forms/FormSection.vue'
import FormInput from '@/components/forms/FormInput.vue'
import FormTextArea from '@/components/forms/FormTextArea.vue'
import ImageUpload from '@/components/forms/ImageUpload.vue'
import { defaultForm } from './data/defaultForm'
import { useAuthStore } from '@/stores/authStore'

const auth = useAuthStore()
const isLoading = ref(false)

const form = ref({
  ...defaultForm,
  namaDepan: auth.user?.name?.split(' ')[0] || defaultForm.namaDepan,
  namaBelakang: auth.user?.name?.split(' ').slice(1).join(' ') || defaultForm.namaBelakang,
  email: auth.user?.email || 'adminyayasan@mail.com'
})

const imagePreview = ref('')
const handleImageChange = (file) => {
  imagePreview.value = URL.createObjectURL(file)
}

const handleSave = () => {
  if (form.value.passwordBaru && form.value.passwordBaru !== form.value.confirmPasswordBaru) {
    toast.error('Gagal menyimpan', {
      description: 'Konfirmasi password baru tidak cocok!'
    })
    return
  }
  
  isLoading.value = true
  setTimeout(() => {
    isLoading.value = false
    toast.success('Pengaturan Akun Berhasil Diperbarui', {
      description: 'Perubahan data profil Anda telah disimpan dengan sukses.'
    })
  }, 1000)
}

const actions = computed(() => [
  {
    label: isLoading.value ? 'Menyimpan...' : 'Simpan Perubahan',
    icon: Save,
    loading: isLoading.value,
    click: handleSave
  }
])
</script>

<template>
  <div class="space-y-6 pb-12 text-left">
    <PageHeader
      back
      title="Pengaturan Akun"
      description="Kelola informasi profil pribadi dan pengaturan keamanan akun Admin Yayasan Anda"
      :actions="actions"
    />

    <div class="grid gap-6 md:grid-cols-2">
      <!-- Row 1, Col 1: Foto Profil -->
      <div class="h-full">
        <FormSection title="Foto Profil" description="Ubah foto profil akun Anda" :icon="Image" class="h-full">
          <div class="flex-1 flex flex-col justify-center py-4">
            <ImageUpload :preview="imagePreview" @change="handleImageChange" />
          </div>
          <div class="p-4 rounded-lg bg-white/50 border border-primary/10 text-xs text-muted-foreground italic">
            Format: JPG/PNG. Maks 2MB. Rasio 1:1.
          </div>
        </FormSection>
      </div>

      <!-- Row 1, Col 2: Identitas & Yayasan -->
      <div class="space-y-6">
        <!-- Identitas -->
        <FormSection title="Informasi Identitas" description="Data nama lengkap dan nomor identitas kependudukan" :icon="SquareUserRound">
          <div class="grid gap-4 md:grid-cols-2">
            <FormInput v-model="form.namaDepan" label="Nama Depan" />
            <FormInput v-model="form.namaBelakang" label="Nama Belakang" />
            <FormInput v-model="form.nik" label="NIK" class="md:col-span-2" />
          </div>
        </FormSection>

        <!-- Afiliasi Yayasan -->
        <FormSection title="Informasi Yayasan" description="Afiliasi yayasan yang menaungi akun Anda" :icon="Building2">
          <FormInput v-model="form.namaYayasan" label="Nama Yayasan" disabled />
        </FormSection>
      </div>

      <!-- Row 2, Col 1: Keamanan -->
      <div class="h-full">
        <FormSection title="Keamanan Akun" description="Ganti password Anda secara berkala untuk menjaga keamanan akun" :icon="Key" class="h-full">
          <FormInput v-model="form.passwordLama" type="password" label="Password Lama" placeholder="Masukkan password saat ini" />
          <FormInput v-model="form.passwordBaru" type="password" label="Password Baru" placeholder="Masukkan password baru" />
          <FormInput v-model="form.confirmPasswordBaru" type="password" label="Konfirmasi Password Baru" placeholder="Ulangi password baru" />
        </FormSection>
      </div>

      <!-- Row 2, Col 2: Kontak -->
      <div class="h-full">
        <FormSection title="Kontak & Alamat" description="Informasi nomor telepon dan alamat tinggal Anda" :icon="Phone" class="h-full">
          <div class="grid gap-4 md:grid-cols-2">
            <FormInput v-model="form.email" label="E-mail" placeholder="E-mail" disabled />
            <FormInput v-model="form.noHp" label="No. Handphone" placeholder="Contoh: 0812xxxxxxxx" />
          </div>
          <FormTextArea v-model="form.alamat" label="Alamat Lengkap" :rows="3" placeholder="Alamat tinggal saat ini" />
        </FormSection>
      </div>
    </div>
  </div>
</template>
