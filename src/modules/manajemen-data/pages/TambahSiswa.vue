<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Separator } from '@/components/ui/separator'
import { 
  ChevronLeft, 
  Save, 
  User, 
  MapPin, 
  GraduationCap, 
  Phone, 
  Mail,
  Calendar
} from 'lucide-vue-next'

const router = useRouter()
const isLoading = ref(false)

// State Form
const form = ref({
  nisn: '',
  nama: '',
  tempat_lahir: '',
  tanggal_lahir: '',
  jenis_kelamin: '',
  alamat: '',
  email: '',
  telepon: '',
  kelas: '',
  nama_ortu: ''
})

const goBack = () => router.back()

const handleSubmit = () => {
  isLoading.value = true
  // Simulasi simpan data
  setTimeout(() => {
    isLoading.value = false
    alert('Data siswa berhasil disimpan!')
    router.push('/manajemen-data/siswa')
  }, 1500)
}
</script>

<template>
  <div class="space-y-6 p-1 pb-10">
    <!-- Header dengan Tombol Kembali -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
      <div class="flex items-center gap-4">
        <Button variant="outline" size="icon" @click="goBack" class="rounded-full shadow-sm">
          <ChevronLeft class="h-5 w-5" />
        </Button>
        <div>
          <h1 class="text-3xl font-bold tracking-tight">Pendaftaran Siswa</h1>
          <p class="text-muted-foreground">Silakan lengkapi formulir pendaftaran siswa baru di bawah ini.</p>
        </div>
      </div>
      <div class="flex gap-3">
        <Button variant="ghost" @click="goBack" :disabled="isLoading">Batal</Button>
        <Button @click="handleSubmit" :disabled="isLoading" class="shadow-md">
          <Save v-if="!isLoading" class="mr-2 h-4 w-4" />
          <span v-else class="mr-2 h-4 w-4 animate-spin border-2 border-current border-t-transparent rounded-full"></span>
          {{ isLoading ? 'Menyimpan...' : 'Simpan Data Siswa' }}
        </Button>
      </div>
    </div>

    <div class="grid gap-6 md:grid-cols-3">
      <!-- Kolom Kiri: Data Pribadi (Lebar 2 Kolom) -->
      <div class="md:col-span-2 space-y-6">
        <Card class="shadow-sm">
          <CardHeader class="pb-4">
            <div class="flex items-center gap-2 text-primary font-semibold">
              <User class="h-5 w-5" />
              <CardTitle>Identitas Siswa</CardTitle>
            </div>
            <CardDescription>Informasi dasar mengenai identitas lengkap siswa.</CardDescription>
          </CardHeader>
          <CardContent class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
              <div class="space-y-2">
                <label class="text-sm font-medium leading-none">NISN / Nomor Induk</label>
                <Input v-model="form.nisn" placeholder="Contoh: 0012345678" />
              </div>
              <div class="space-y-2">
                <label class="text-sm font-medium leading-none">Nama Lengkap Siswa</label>
                <Input v-model="form.nama" placeholder="Masukkan nama sesuai ijazah" />
              </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
              <div class="space-y-2">
                <label class="text-sm font-medium leading-none">Tempat Lahir</label>
                <Input v-model="form.tempat_lahir" placeholder="Contoh: Jakarta" />
              </div>
              <div class="space-y-2">
                <label class="text-sm font-medium leading-none">Tanggal Lahir</label>
                <div class="relative">
                  <Input type="date" v-model="form.tanggal_lahir" />
                </div>
              </div>
            </div>

            <Separator class="my-4" />

            <div class="space-y-2">
              <label class="text-sm font-medium leading-none">Alamat Tinggal Sesuai Domisili</label>
              <Input v-model="form.alamat" placeholder="Nama jalan, RT/RW, Kecamatan..." />
            </div>
          </CardContent>
        </Card>

        <Card class="shadow-sm">
          <CardHeader class="pb-4">
            <div class="flex items-center gap-2 text-primary font-semibold">
              <Phone class="h-5 w-5" />
              <CardTitle>Kontak & Orang Tua</CardTitle>
            </div>
            <CardDescription>Informasi untuk keperluan komunikasi dengan wali murid.</CardDescription>
          </CardHeader>
          <CardContent class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
              <div class="space-y-2">
                <label class="text-sm font-medium leading-none">Nama Ayah / Ibu / Wali</label>
                <Input v-model="form.nama_ortu" placeholder="Masukkan nama lengkap wali" />
              </div>
              <div class="space-y-2">
                <label class="text-sm font-medium leading-none">No. WhatsApp Aktif</label>
                <Input v-model="form.telepon" placeholder="Contoh: 081234567890" />
              </div>
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium leading-none">Alamat Email (Opsional)</label>
              <div class="relative">
                <Input v-model="form.email" type="email" placeholder="email@contoh.com" />
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Kolom Kanan: Data Akademik (Lebar 1 Kolom) -->
      <div class="space-y-6">
        <Card class="shadow-sm border-primary/20 bg-primary/5">
          <CardHeader>
            <div class="flex items-center gap-2 text-primary font-semibold">
              <GraduationCap class="h-5 w-5" />
              <CardTitle>Status Akademik</CardTitle>
            </div>
            <CardDescription>Pengaturan pendaftaran kelas dan periode.</CardDescription>
          </CardHeader>
          <CardContent class="space-y-6">
            <div class="space-y-2">
              <label class="text-sm font-medium leading-none">Pilih Kelas</label>
              <select class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                <option value="">-- Pilih Kelas --</option>
                <option value="1A">Kelas 1-A</option>
                <option value="1B">Kelas 1-B</option>
                <option value="2A">Kelas 2-A</option>
              </select>
            </div>
            
            <div class="space-y-2">
              <label class="text-sm font-medium leading-none">Tahun Ajaran</label>
              <Input value="2024 / 2025" disabled class="bg-muted" />
            </div>

            <div class="p-4 rounded-lg bg-white border border-primary/10 text-xs text-muted-foreground italic">
              * Pastikan seluruh data sudah benar sebelum menekan tombol simpan. Data yang sudah disimpan akan langsung masuk ke database aktif.
            </div>
          </CardContent>
        </Card>

        <!-- Informasi Tambahan -->
        <Card class="bg-muted/50 border-dashed">
          <CardContent class="pt-6">
             <div class="flex items-start gap-3">
               <Calendar class="h-5 w-5 text-muted-foreground shrink-0 mt-1" />
               <div class="text-sm">
                 <p class="font-semibold">Tanggal Pendaftaran</p>
                 <p class="text-muted-foreground">{{ new Date().toLocaleDateString('id-ID', { dateStyle: 'long' }) }}</p>
               </div>
             </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </div>
</template>
