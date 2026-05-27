<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { fetchAllSiswa } from '@/services/siswaService'
import { useSuratLulusStore } from '@/stores/suratLulusStore'
import { toast } from 'vue-sonner'

const router = useRouter()
const store = useSuratLulusStore()

const isLoadingSiswa = ref(false)
const siswaList = ref([])

const form = ref({
  nama: '',
  nisn: '',
  nis: '',
  tempatLahir: '',
  tanggalLahir: '',
  namaOrangTua: ''
})

onMounted(async () => {
  isLoadingSiswa.value = true
  try {
    const data = await fetchAllSiswa()
    siswaList.value = data.map(user => {
      // Generate realistic fake birthplaces, birthdays, and NISN/NIS since placeholder placeholder/json lacks them
      const randomCities = ['Jakarta', 'Bandung', 'Surabaya', 'Medan', 'Semarang', 'Yogyakarta', 'Makassar', 'Denpasar']
      const city = randomCities[user.id % randomCities.length]
      
      const year = 2013 + (user.id % 3)
      const month = String(1 + (user.id % 12)).padStart(2, '0')
      const day = String(10 + (user.id % 18)).padStart(2, '0')
      
      return {
        ...user,
        nisn: '00' + user.id.toString().padStart(3, '0') + Math.floor(10000 + Math.random() * 90000),
        nis: '20230' + user.id.toString().padStart(3, '0'),
        tempatLahir: city,
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
    form.value.nis = siswa.nis
    form.value.tempatLahir = siswa.tempatLahir
    form.value.tanggalLahir = siswa.tanggalLahir
  }
}

function handleSave() {
  if (!form.value.nama || !form.value.namaOrangTua || !form.value.tempatLahir || !form.value.tanggalLahir) {
    toast.error('Mohon lengkapi seluruh kolom formulir.')
    return
  }

  try {
    store.add({ ...form.value })
    toast.success('Surat Keterangan Lulus berhasil dibuat!')
    router.push('/komunikasi/persuratan/lulus')
  } catch (error) {
    toast.error('Gagal menyimpan data surat.')
  }
}

function handleCancel() {
  router.push('/komunikasi/persuratan/lulus')
}
</script>

<template>
  <div class="space-y-8 p-1 sm:p-4 max-w-5xl mx-auto w-full">
    <!-- Header -->
    <div class="flex flex-col gap-2 mb-2">
      <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight bg-gradient-to-r from-slate-900 to-slate-500 dark:from-white dark:to-slate-400 bg-clip-text text-transparent">
        Buat Surat Keterangan Lulus
      </h1>
      <p class="text-sm text-muted-foreground leading-relaxed">
        Kelola Surat Keterangan Lulus
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
            <h2 class="text-lg font-bold text-foreground">Informasi Formulir</h2>
          </div>
        </div>
      </div>
      
      <!-- Form Fields -->
      <div class="p-6 sm:p-8 space-y-8 bg-slate-50/50 dark:bg-slate-900/10">
        
        <div class="space-y-6 max-w-2xl">
          <!-- Nama Siswa -->
          <div class="space-y-3">
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

          <!-- Nama Orang Tua -->
          <div class="space-y-3">
            <label class="text-sm font-semibold text-foreground">Nama Orang Tua</label>
            <Input 
              type="text"
              v-model="form.namaOrangTua"
              placeholder="Masukkan nama orang tua / wali siswa" 
              class="h-11 border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 shadow-sm focus-visible:ring-primary/30" 
            />
          </div>

          <!-- Tempat Lahir Siswa -->
          <div class="space-y-3">
            <label class="text-sm font-semibold text-foreground">Tempat Lahir Siswa</label>
            <Input 
              type="text"
              v-model="form.tempatLahir"
              placeholder="Masukkan tempat lahir siswa" 
              class="h-11 border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 shadow-sm focus-visible:ring-primary/30" 
            />
          </div>

          <!-- Tanggal Lahir Siswa -->
          <div class="space-y-3">
            <label class="text-sm font-semibold text-foreground">Tanggal Lahir Siswa</label>
            <Input 
              type="date" 
              v-model="form.tanggalLahir" 
              onclick="this.showPicker()"
              class="h-11 border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 shadow-sm focus-visible:ring-primary/30 text-muted-foreground cursor-pointer" 
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
          Simpan
        </Button>
      </div>
    </div>
  </div>
</template>
