<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { fetchAllSiswa } from '@/services/siswaService'
import { useSuratAktifStore } from '@/stores/suratAktifStore'
import { toast } from 'vue-sonner'

const router = useRouter()
const store = useSuratAktifStore()

const isLoadingSiswa = ref(false)
const siswaList = ref([])

const form = ref({
  nama: '',
  nisn: '',
  tahunAkademik: '2025/2026',
  semester: 'Genap',
  kelas: '',
  tanggalLahir: '',
  alamat: ''
})

onMounted(async () => {
  isLoadingSiswa.value = true
  try {
    const data = await fetchAllSiswa()
    // jsonplaceholder doesn't have NISN, Kelas, and Tgl Lahir, so we generate fake ones
    siswaList.value = data.map(user => {
      const randomKelas = ['VI A', 'VI B', 'V A', 'V B', 'IV A', 'IV B'][user.id % 6]
      const year = 2012 + (user.id % 4)
      const month = String(1 + (user.id % 12)).padStart(2, '0')
      const day = String(10 + (user.id % 18)).padStart(2, '0')
      
      return {
        ...user,
        nisn: '10' + user.id.toString().padStart(4, '0') + Math.floor(1000 + Math.random() * 9000),
        kelas: randomKelas,
        tanggalLahir: `${year}-${month}-${day}`
      }
    })
  } catch (error) {
    toast.error('Gagal memuat data siswa.')
  } finally {
    isLoadingSiswa.value = false
  }
})

function onSiswaSelected(siswaId) {
  const siswa = siswaList.value.find(s => s.id === parseInt(siswaId))
  if (siswa) {
    form.value.nama = siswa.name
    form.value.nisn = siswa.nisn
    form.value.kelas = siswa.kelas
    form.value.tanggalLahir = siswa.tanggalLahir
    // Simulate auto-filling other fields if possible
    form.value.alamat = `${siswa.address?.street}, ${siswa.address?.city}`
  }
}

function handleSave() {
  if (!form.value.nama || !form.value.tahunAkademik || !form.value.semester || !form.value.kelas) {
    toast.error('Mohon lengkapi semua kolom yang wajib diisi.')
    return
  }

  try {
    store.add({ ...form.value })
    toast.success('Surat keterangan aktif berhasil dibuat!')
    router.push('/komunikasi/persuratan/aktif')
  } catch (error) {
    toast.error('Gagal menyimpan surat.')
  }
}

function handleCancel() {
  router.push('/komunikasi/persuratan/aktif')
}
</script>

<template>
  <div class="space-y-8 p-1 sm:p-4 max-w-5xl mx-auto w-full">
    <!-- Header -->
    <div class="flex flex-col gap-2 mb-2">
      <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight bg-gradient-to-r from-slate-900 to-slate-500 dark:from-white dark:to-slate-400 bg-clip-text text-transparent">
        Buat Surat Keterangan Aktif
      </h1>
      <p class="text-sm text-muted-foreground leading-relaxed">
        Lengkapi formulir di bawah ini untuk menerbitkan surat keterangan siswa aktif yang baru.
      </p>
    </div>

    <!-- Form Container -->
    <div class="bg-card border border-border/60 rounded-2xl shadow-sm overflow-hidden flex flex-col">
      <!-- Title Bar -->
      <div class="bg-primary/5 p-6 border-b border-border/50">
        <div class="flex items-center gap-3">
          <div class="p-2 bg-primary/10 rounded-lg text-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><line x1="10" x2="8" y1="9" y2="9"/></svg>
          </div>
          <div>
            <h2 class="text-lg font-bold text-foreground">Informasi Surat</h2>
            <p class="text-xs text-muted-foreground">Pastikan data yang dimasukkan sudah benar.</p>
          </div>
        </div>
      </div>
      
      <!-- Form Fields -->
      <div class="p-6 sm:p-8 space-y-8 bg-slate-50/50 dark:bg-slate-900/10">
        
        <!-- Grid 2 Column Layout -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 w-full">
          <!-- Nama Siswa (Spans full width) -->
          <div class="space-y-3 sm:col-span-2">
            <label class="text-sm font-semibold text-foreground">Nama Siswa</label>
            <Select @update:modelValue="onSiswaSelected" :disabled="isLoadingSiswa">
              <SelectTrigger class="w-full h-11 border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 shadow-sm focus:ring-primary/30">
                <SelectValue :placeholder="isLoadingSiswa ? 'Memuat data siswa...' : 'Pilih Siswa...'" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem 
                  v-for="siswa in siswaList" 
                  :key="siswa.id" 
                  :value="siswa.id.toString()"
                >
                  {{ siswa.name }} - {{ siswa.nisn }}
                </SelectItem>
              </SelectContent>
            </Select>
          </div>

          <!-- Tahun Akademik -->
          <div class="space-y-3">
            <label class="text-sm font-semibold text-foreground">Tahun Akademik</label>
            <Select v-model="form.tahunAkademik">
              <SelectTrigger class="w-full h-11 border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 shadow-sm focus:ring-primary/30">
                <SelectValue placeholder="Pilih Tahun Akademik" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="2024/2025">2024/2025</SelectItem>
                <SelectItem value="2025/2026">2025/2026</SelectItem>
                <SelectItem value="2026/2027">2026/2027</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <!-- Semester -->
          <div class="space-y-3">
            <label class="text-sm font-semibold text-foreground">Semester</label>
            <Select v-model="form.semester">
              <SelectTrigger class="w-full h-11 border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 shadow-sm focus:ring-primary/30">
                <SelectValue placeholder="Pilih Semester" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="Ganjil">Ganjil</SelectItem>
                <SelectItem value="Genap">Genap</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <!-- Kelas -->
          <div class="space-y-3">
            <label class="text-sm font-semibold text-foreground">Kelas</label>
            <Input v-model="form.kelas" placeholder="Misal: VI A" class="h-11 border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 shadow-sm focus-visible:ring-primary/30" />
          </div>

          <!-- Tanggal Lahir -->
          <div class="space-y-3">
            <label class="text-sm font-semibold text-foreground">Tanggal Lahir</label>
            <Input type="date" v-model="form.tanggalLahir" class="h-11 border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 shadow-sm focus-visible:ring-primary/30 text-muted-foreground" />
          </div>

          <!-- Alamat (Spans full width) -->
          <div class="space-y-3 sm:col-span-2">
            <label class="text-sm font-semibold text-foreground">Alamat</label>
            <Textarea 
              v-model="form.alamat"
              placeholder="Masukkan alamat lengkap siswa" 
              class="min-h-[140px] resize-none border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 shadow-sm focus-visible:ring-primary/30 text-base" 
            />
          </div>
        </div>
      </div>
      
      <!-- Footer / Actions -->
      <div class="p-6 border-t border-border/50 bg-muted/10 flex items-center justify-end gap-4">
        <Button @click="handleCancel" variant="outline" class="px-8 h-12 rounded-xl font-semibold border-2 hover:bg-slate-100 dark:hover:bg-slate-800">
          Batal
        </Button>
        <Button @click="handleSave" class="px-8 h-12 rounded-xl font-semibold shadow-md bg-primary text-primary-foreground hover:bg-primary/90 transition-all hover:-translate-y-0.5">
          Simpan Data
        </Button>
      </div>
    </div>
  </div>
</template>
