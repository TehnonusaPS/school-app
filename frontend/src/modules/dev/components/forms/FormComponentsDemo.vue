<script setup>
import { ref } from 'vue'
import FormSection from '@/components/forms/FormSection.vue'
import FormInput from '@/components/forms/FormInput.vue'
import FormSelect from '@/components/forms/FormSelect.vue'
import FormDate from '@/components/forms/FormDate.vue'
import FormTextArea from '@/components/forms/FormTextArea.vue'
import ImageUpload from '@/components/forms/ImageUpload.vue'
import { Button } from '@/components/ui/button'
import { User, School, MapPin, CheckCircle } from 'lucide-vue-next'

// Form state
const form = ref({
  nama: '',
  nisn: '',
  email: '',
  gender: '',
  kelas: '',
  tanggalLahir: '',
  alamat: '',
  foto: null,
})

const previewUrl = ref(null)

// Error state
const errors = ref({})
const isSubmitted = ref(false)

const genderOptions = [
  { value: 'L', label: 'Laki-laki' },
  { value: 'P', label: 'Perempuan' },
]

const kelasOptions = [
  { value: '10-A', label: 'Kelas X - A' },
  { value: '10-B', label: 'Kelas X - B' },
  { value: '11-A', label: 'Kelas XI - A' },
  { value: '11-B', label: 'Kelas XI - B' },
]

const handleFotoChange = (file) => {
  form.value.foto = file
  previewUrl.value = URL.createObjectURL(file)
  errors.value.foto = ''
}

const validateForm = () => {
  const newErrors = {}
  if (!form.value.nama) newErrors.nama = 'Nama lengkap wajib diisi.'
  if (!form.value.nisn) {
    newErrors.nisn = 'NISN wajib diisi.'
  } else if (!/^\d{10}$/.test(form.value.nisn)) {
    newErrors.nisn = 'NISN harus berupa 10 digit angka.'
  }
  
  if (!form.value.email) {
    newErrors.email = 'Email wajib diisi.'
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email)) {
    newErrors.email = 'Format email tidak valid.'
  }

  if (!form.value.gender) newErrors.gender = 'Pilih jenis kelamin.'
  if (!form.value.kelas) newErrors.kelas = 'Pilih kelas.'
  if (!form.value.tanggalLahir) newErrors.tanggalLahir = 'Tanggal lahir wajib diisi.'
  if (!form.value.alamat) newErrors.alamat = 'Alamat lengkap wajib diisi.'
  if (!form.value.foto) newErrors.foto = 'Foto profil wajib diunggah.'

  errors.value = newErrors
  return Object.keys(newErrors).length === 0
}

const handleSubmit = () => {
  isSubmitted.value = false
  if (validateForm()) {
    isSubmitted.value = true
    // Reset errors
    errors.value = {}
  }
}

const resetForm = () => {
  form.value = {
    nama: '',
    nisn: '',
    email: '',
    gender: '',
    kelas: '',
    tanggalLahir: '',
    alamat: '',
    foto: null,
  }
  previewUrl.value = null
  errors.value = {}
  isSubmitted.value = false
}

// Trigger error state demo
const triggerErrorsDemo = () => {
  errors.value = {
    nama: 'Nama lengkap wajib diisi.',
    nisn: 'NISN harus berupa 10 digit angka.',
    email: 'Format email tidak valid.',
    gender: 'Pilih jenis kelamin.',
    kelas: 'Pilih kelas.',
    tanggalLahir: 'Tanggal lahir wajib diisi.',
    alamat: 'Alamat lengkap wajib diisi.',
    foto: 'Foto profil wajib diunggah.',
  }
  isSubmitted.value = false
}
</script>

<template>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-4">
    <!-- Form Area -->
    <div class="lg:col-span-2 space-y-6">
      <form @submit.prevent="handleSubmit" class="space-y-6">
        <!-- Section 1: Identitas Diri -->
        <FormSection
          title="Identitas Siswa"
          description="Masukkan informasi pribadi siswa sesuai dengan kartu identitas resmi."
          :icon="User"
        >
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <FormInput
              v-model="form.nama"
              label="Nama Lengkap"
              placeholder="Masukkan nama lengkap"
              :error="errors.nama"
              required
            />
            
            <FormInput
              v-model="form.nisn"
              label="NISN"
              placeholder="Contoh: 0012345678"
              :error="errors.nisn"
              required
            />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <FormInput
              v-model="form.email"
              label="Alamat Email"
              type="email"
              placeholder="nama@sekolah.sch.id"
              :error="errors.email"
              required
            />

            <FormSelect
              v-model="form.gender"
              label="Jenis Kelamin"
              placeholder="Pilih jenis kelamin"
              :options="genderOptions"
              :error="errors.gender"
              required
            />
          </div>
        </FormSection>

        <!-- Section 2: Akademik -->
        <FormSection
          title="Informasi Akademik & Kelahiran"
          description="Tentukan penempatan kelas dan lengkapi tanggal lahir siswa."
          :icon="School"
        >
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <FormSelect
              v-model="form.kelas"
              label="Kelas"
              placeholder="Pilih kelas tujuan"
              :options="kelasOptions"
              :error="errors.kelas"
              required
            />

            <FormDate
              v-model="form.tanggalLahir"
              label="Tanggal Lahir"
              :error="errors.tanggalLahir"
              required
            />
          </div>
        </FormSection>

        <!-- Section 3: Tempat Tinggal -->
        <FormSection
          title="Alamat Domisili"
          description="Masukkan alamat domisili lengkap wali/orang tua siswa saat ini."
          :icon="MapPin"
        >
          <FormTextArea
            v-model="form.alamat"
            label="Alamat Lengkap"
            placeholder="Jalan, RT/RW, Kecamatan, Kabupaten/Kota, Provinsi"
            :error="errors.alamat"
            :rows="3"
            required
          />
        </FormSection>

        <!-- Buttons -->
        <div class="flex items-center gap-4">
          <Button type="submit" class="bg-primary hover:bg-primary/90">
            Submit Registrasi
          </Button>
          <Button type="button" variant="outline" @click="resetForm">
            Reset Form
          </Button>
          <Button type="button" variant="ghost" class="text-destructive hover:bg-destructive/10" @click="triggerErrorsDemo">
            Demo Error State
          </Button>
        </div>
      </form>
    </div>

    <!-- Sidebar Upload & State Preview -->
    <div class="space-y-6">
      <!-- Foto Upload -->
      <FormSection
        title="Foto Profil"
        description="Unggah pas foto terbaru siswa (format JPG/PNG, maks. 2MB)."
      >
        <ImageUpload
          label="Foto Siswa"
          note="Gunakan foto berlatar merah atau biru untuk keperluan administrasi resmi sekolah."
          :preview="previewUrl"
          :error="errors.foto"
          @change="handleFotoChange"
        />
      </FormSection>

      <!-- Live State Preview -->
      <FormSection
        title="Live Data Preview"
        description="Nilai saat ini yang ditangkap oleh form model (reactive binding)."
      >
        <div class="space-y-3 text-xs">
          <div>
            <span class="font-semibold text-muted-foreground block">Nama Lengkap:</span>
            <span class="text-foreground font-mono bg-muted/50 px-1 py-0.5 rounded break-all">{{ form.nama || '-' }}</span>
          </div>
          <div>
            <span class="font-semibold text-muted-foreground block">NISN:</span>
            <span class="text-foreground font-mono bg-muted/50 px-1 py-0.5 rounded">{{ form.nisn || '-' }}</span>
          </div>
          <div>
            <span class="font-semibold text-muted-foreground block">Email:</span>
            <span class="text-foreground font-mono bg-muted/50 px-1 py-0.5 rounded break-all">{{ form.email || '-' }}</span>
          </div>
          <div>
            <span class="font-semibold text-muted-foreground block">Jenis Kelamin:</span>
            <span class="text-foreground font-mono bg-muted/50 px-1 py-0.5 rounded">{{ form.gender || '-' }}</span>
          </div>
          <div>
            <span class="font-semibold text-muted-foreground block">Kelas:</span>
            <span class="text-foreground font-mono bg-muted/50 px-1 py-0.5 rounded">{{ form.kelas || '-' }}</span>
          </div>
          <div>
            <span class="font-semibold text-muted-foreground block">Tanggal Lahir:</span>
            <span class="text-foreground font-mono bg-muted/50 px-1 py-0.5 rounded">{{ form.tanggalLahir || '-' }}</span>
          </div>
          <div>
            <span class="font-semibold text-muted-foreground block">Alamat:</span>
            <span class="text-foreground font-mono bg-muted/50 px-1 py-0.5 rounded block whitespace-pre-wrap break-all">{{ form.alamat || '-' }}</span>
          </div>
          <div>
            <span class="font-semibold text-muted-foreground block">Foto Profil:</span>
            <span class="text-foreground font-mono bg-muted/50 px-1 py-0.5 rounded break-all">{{ form.foto ? form.foto.name : '-' }}</span>
          </div>
        </div>
      </FormSection>

      <!-- Success Notification -->
      <div
        v-if="isSubmitted"
        v-motion
        :initial="{ opacity: 0, scale: 0.95 }"
        :enter="{ opacity: 1, scale: 1 }"
        class="p-4 rounded-xl border border-green-500/20 bg-green-500/10 text-green-700 dark:text-green-400 flex items-start gap-3"
      >
        <CheckCircle class="h-5 w-5 mt-0.5 shrink-0" />
        <div>
          <h4 class="font-bold text-sm">Registrasi Berhasil!</h4>
          <p class="text-xs mt-1">Seluruh data form telah tervalidasi dan siap untuk dikirim ke API server.</p>
        </div>
      </div>
    </div>
  </div>
</template>
