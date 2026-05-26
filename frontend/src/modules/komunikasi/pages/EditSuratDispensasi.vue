<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { Plus, Trash2, Calendar as CalendarIcon, Save, X } from 'lucide-vue-next'
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
import { useSuratDispensasiStore } from '@/stores/suratDispensasiStore'
import { toast } from 'vue-sonner'

const router = useRouter()
const route = useRoute()
const store = useSuratDispensasiStore()

const isLoadingSiswa = ref(false)
const siswaOptions = ref([])

// State for dynamic students
const selectedSiswa = ref([
  { id: Date.now().toString(), siswaId: null, nama: '', nisn: '', kelas: '' }
])

const form = ref({
  tanggalAwal: '',
  tanggalAkhir: '',
  perihal: ''
})

const suratId = route.params.id
const currentSurat = ref(null)

onMounted(async () => {
  isLoadingSiswa.value = true
  try {
    const data = await fetchAllSiswa()
    siswaOptions.value = data.map(user => {
      const randomKelas = ['VI A', 'VI B', 'V A', 'V B', 'IV A', 'IV B'][user.id % 6]
      return {
        ...user,
        nisn: '10' + user.id.toString().padStart(4, '0') + Math.floor(1000 + Math.random() * 9000),
        kelas: randomKelas
      }
    })

    if (suratId) {
      const existing = store.getById(suratId)
      if (existing) {
        currentSurat.value = existing
        form.value.tanggalAwal = existing.tanggalAwal
        form.value.tanggalAkhir = existing.tanggalAkhir
        form.value.perihal = existing.perihal
        
        // Map existing students
        if (existing.siswa && existing.siswa.length > 0) {
          selectedSiswa.value = existing.siswa.map((s, index) => {
            // Find matched siswaId if possible based on NISN or nama
            const matchedOption = siswaOptions.value.find(opt => opt.nisn === s.nisn || opt.name === s.nama)
            return {
              id: Date.now().toString() + index,
              siswaId: matchedOption ? matchedOption.id : null,
              nama: s.nama,
              nisn: s.nisn,
              kelas: s.kelas
            }
          })
        }
      } else {
        toast.error('Surat tidak ditemukan.')
        router.push('/komunikasi/persuratan/dispensasi')
      }
    }

  } catch (error) {
    toast.error('Gagal memuat data.')
  } finally {
    isLoadingSiswa.value = false
  }
})

function addSiswaField() {
  selectedSiswa.value.push({ 
    id: Date.now().toString() + Math.random(), 
    siswaId: null, 
    nama: '', 
    nisn: '', 
    kelas: '' 
  })
}

function removeSiswaField(index) {
  if (selectedSiswa.value.length > 1) {
    selectedSiswa.value.splice(index, 1)
  }
}

function onSiswaSelected(index, siswaId) {
  const siswa = siswaOptions.value.find(s => s.id === parseInt(siswaId))
  if (siswa) {
    selectedSiswa.value[index].siswaId = siswa.id
    selectedSiswa.value[index].nama = siswa.name
    selectedSiswa.value[index].nisn = siswa.nisn
    selectedSiswa.value[index].kelas = siswa.kelas
  }
}

function handleSave() {
  const validSiswa = selectedSiswa.value.filter(s => s.nama !== '' && s.nisn !== '')
  if (validSiswa.length === 0) {
    toast.error('Daftar siswa tidak boleh kosong. Harap isi data dengan benar.')
    return
  }
  if (!form.value.tanggalAwal || !form.value.tanggalAkhir || !form.value.perihal) {
    toast.error('Mohon lengkapi Tanggal Izin dan Perihal.')
    return
  }

  try {
    store.update(suratId, {
      tanggalAwal: form.value.tanggalAwal,
      tanggalAkhir: form.value.tanggalAkhir,
      perihal: form.value.perihal,
      siswa: validSiswa.map(s => ({
        nama: s.nama,
        nisn: s.nisn,
        kelas: s.kelas
      }))
    })
    toast.success('Surat dispensasi berhasil diperbarui!')
    router.push('/komunikasi/persuratan/dispensasi')
  } catch (error) {
    toast.error('Gagal memperbarui surat.')
  }
}

function handleCancel() {
  router.push('/komunikasi/persuratan/dispensasi')
}
</script>

<template>
  <div class="space-y-8 p-1 sm:p-4 max-w-5xl mx-auto w-full">
    <!-- Header -->
    <div class="flex flex-col gap-2 mb-2">
      <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight bg-gradient-to-r from-slate-900 to-slate-500 dark:from-white dark:to-slate-400 bg-clip-text text-transparent">
        Edit Surat Dispensasi Siswa
      </h1>
      <p class="text-sm text-muted-foreground leading-relaxed">
        Perbarui formulir di bawah ini untuk mengedit surat dispensasi siswa.
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
        
        <!-- Multiple Siswa Selector -->
        <div class="space-y-4">
          <div v-for="(item, index) in selectedSiswa" :key="item.id" class="flex flex-col sm:flex-row items-start sm:items-end gap-4 w-full">
            <div class="space-y-3 w-full sm:w-2/3">
              <label class="text-sm font-semibold text-foreground">Nama Siswa</label>
              <Select @update:modelValue="(val) => onSiswaSelected(index, val)" :disabled="isLoadingSiswa">
                <SelectTrigger class="w-full h-11 border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 shadow-sm focus:ring-primary/30">
                  <SelectValue :placeholder="item.nama ? item.nama : (isLoadingSiswa ? 'Memuat data siswa...' : 'Pilih Siswa...')" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem 
                    v-for="siswa in siswaOptions" 
                    :key="siswa.id" 
                    :value="siswa.id.toString()"
                  >
                    {{ siswa.name }} - {{ siswa.nisn }}
                  </SelectItem>
                </SelectContent>
              </Select>
            </div>
            
            <div class="flex items-center gap-2 mt-2 sm:mt-0">
              <Button 
                v-if="selectedSiswa.length > 1" 
                variant="ghost" 
                size="sm" 
                class="h-11 flex items-center gap-2 text-destructive hover:bg-destructive/10"
                @click="removeSiswaField(index)"
              >
                <Trash2 class="size-4" />
                <span class="sm:hidden">Hapus</span>
                <span class="hidden sm:inline">Hapus</span>
              </Button>
              <Button 
                v-if="index === selectedSiswa.length - 1" 
                variant="outline" 
                size="sm"
                class="h-11 flex items-center gap-2 bg-white dark:bg-slate-950 border-slate-200 dark:border-slate-800 shadow-sm hover:bg-slate-100"
                @click="addSiswaField"
              >
                <Plus class="size-4" />
                Tambah Siswa
              </Button>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 w-full">
          <!-- Tanggal Izin Awal -->
          <div class="space-y-3">
            <label class="text-sm font-semibold text-foreground">Tanggal Izin Awal</label>
            <Input 
              type="date" 
              v-model="form.tanggalAwal" 
              onclick="this.showPicker()"
              class="h-11 border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 shadow-sm focus-visible:ring-primary/30 text-muted-foreground cursor-pointer" 
            />
          </div>
          
          <!-- Tanggal Izin Akhir -->
          <div class="space-y-3">
            <label class="text-sm font-semibold text-foreground">Tanggal Izin Akhir</label>
            <Input 
              type="date" 
              v-model="form.tanggalAkhir" 
              onclick="this.showPicker()"
              class="h-11 border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 shadow-sm focus-visible:ring-primary/30 text-muted-foreground cursor-pointer" 
            />
          </div>
        </div>

        <!-- Perihal Izin -->
        <div class="space-y-3">
          <label class="text-sm font-semibold text-foreground">Perihal Izin</label>
          <Textarea 
            v-model="form.perihal"
            placeholder="Masukkan keterangan perihal izin" 
            class="min-h-[140px] resize-none border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 shadow-sm focus-visible:ring-primary/30 text-base" 
          />
        </div>

      </div>
      
      <!-- Footer / Actions -->
      <div class="p-6 border-t border-border/50 bg-muted/10 flex items-center justify-end gap-4">
        <Button @click="handleCancel" variant="outline" class="px-8 h-12 rounded-xl font-semibold border-2 hover:bg-slate-100 dark:hover:bg-slate-800">
          Batal
        </Button>
        <Button @click="handleSave" class="px-8 h-12 rounded-xl font-semibold shadow-md bg-primary text-primary-foreground hover:bg-primary/90 transition-all hover:-translate-y-0.5">
          Perbarui Data
        </Button>
      </div>
    </div>
  </div>
</template>
