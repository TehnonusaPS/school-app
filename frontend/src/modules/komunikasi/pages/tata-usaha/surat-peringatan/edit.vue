<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
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
import { useSuratPeringatanStore } from '@/stores/suratPeringatanStore'
import { toast } from 'vue-sonner'
import PageHeader from '@/components/page-header/PageHeader.vue'
import { glassFade } from '@/config/motion'

const router = useRouter()
const route = useRoute()
const store = useSuratPeringatanStore()

const isLoadingSiswa = ref(false)
const siswaList = ref([])

const form = ref({
  jenisSurat: 'Surat Pelanggaran',
  siswaId: null,
  namaSiswa: '',
  nisn: '',
  kelas: '',
  tanggal: '',
  perihalPelanggaran: '',
  jumlahTunggakan: ''
})

const suratId = route.params.id

onMounted(async () => {
  isLoadingSiswa.value = true
  try {
    const data = await fetchAllSiswa()
    siswaList.value = data.map(user => {
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
        form.value = { ...existing }
      } else {
        toast.error('Surat tidak ditemukan.')
        router.push('/komunikasi/persuratan/peringatan')
      }
    }
  } catch (error) {
    toast.error('Gagal memuat data.')
  } finally {
    isLoadingSiswa.value = false
  }
})

function onSiswaSelected(siswaId) {
  const siswa = siswaList.value.find(s => s.id === parseInt(siswaId))
  if (siswa) {
    form.value.siswaId = siswa.id
    form.value.namaSiswa = siswa.name
    form.value.nisn = siswa.nisn
    form.value.kelas = siswa.kelas
  }
}

function formatRupiah(value) {
  if (!value) return ''
  const numericString = String(value).replace(/[^0-9]/g, '')
  return numericString.replace(/\B(?=(\d{3})+(?!\d))/g, '.')
}

function handleJumlahTunggakanInput(e) {
  const formatted = formatRupiah(e.target.value)
  form.value.jumlahTunggakan = formatted
}

function handleSave() {
  if (!form.value.namaSiswa || !form.value.jenisSurat) {
    toast.error('Mohon lengkapi Jenis Surat dan Nama Siswa.')
    return
  }

  if (form.value.jenisSurat === 'Surat Pelanggaran') {
    if (!form.value.perihalPelanggaran) {
      toast.error('Mohon lengkapi Perihal Pelanggaran.')
      return
    }
  }

  if (form.value.jenisSurat === 'Surat Tunggakan') {
    if (!form.value.jumlahTunggakan) {
      toast.error('Mohon lengkapi Jumlah Tunggakan.')
      return
    }
  }

  try {
    store.update(suratId, { ...form.value })
    toast.success('Surat peringatan berhasil diperbarui!')
    router.push('/komunikasi/persuratan/peringatan')
  } catch (error) {
    toast.error('Gagal memperbarui surat.')
  }
}

function handleCancel() {
  router.push('/komunikasi/persuratan/peringatan')
}
</script>

<template>
  <div
    v-motion
    :initial="glassFade.initial"
    :visible-once="glassFade.visible"
    class="space-y-8 p-1 sm:p-4 max-w-5xl mx-auto w-full"
  >
    <!-- Header -->
    <PageHeader
      title="Edit Surat Peringatan"
      description="Perbarui formulir di bawah ini untuk mengedit surat."
      back
      @back="handleCancel"
    />

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
          <!-- Jenis Surat -->
          <div class="space-y-3">
            <label class="text-sm font-semibold text-foreground">Jenis Surat</label>
            <Select v-model="form.jenisSurat">
              <SelectTrigger class="w-full h-11 border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 shadow-sm focus:ring-primary/30">
                <SelectValue placeholder="Pilih Jenis Surat" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="Surat Pelanggaran">Surat Pelanggaran</SelectItem>
                <SelectItem value="Surat Tunggakan">Surat Tunggakan</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <!-- Nama Siswa -->
          <div class="space-y-3">
            <label class="text-sm font-semibold text-foreground">Nama Siswa</label>
            <Select @update:modelValue="onSiswaSelected" :disabled="isLoadingSiswa">
              <SelectTrigger class="w-full h-11 border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 shadow-sm focus:ring-primary/30">
                <SelectValue :placeholder="form.namaSiswa ? form.namaSiswa : (isLoadingSiswa ? 'Memuat data siswa...' : 'Pilih Siswa...') " />
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

          <!-- Kelas -->
          <div class="space-y-3">
            <label class="text-sm font-semibold text-foreground">Kelas</label>
            <Input v-model="form.kelas" placeholder="Terisi otomatis" readonly class="h-11 border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-900 shadow-sm text-muted-foreground" />
          </div>

          <!-- CONDITIONAL: Surat Pelanggaran -->
          <template v-if="form.jenisSurat === 'Surat Pelanggaran'">
            <!-- Perihal Pelanggaran -->
            <div class="space-y-3">
              <label class="text-sm font-semibold text-foreground">Perihal Pelanggaran</label>
              <Textarea 
                v-model="form.perihalPelanggaran"
                placeholder="Masukkan keterangan perihal pelanggaran" 
                class="min-h-[140px] resize-none border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 shadow-sm focus-visible:ring-primary/30 text-base" 
              />
            </div>
          </template>

          <!-- CONDITIONAL: Surat Tunggakan -->
          <template v-if="form.jenisSurat === 'Surat Tunggakan'">
            <!-- Jumlah Tunggakan -->
            <div class="space-y-3">
              <label class="text-sm font-semibold text-foreground">Jumlah Tunggakan</label>
              <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground font-medium select-none">Rp</span>
                <Input 
                  type="text" 
                  v-model="form.jumlahTunggakan"
                  @input="handleJumlahTunggakanInput"
                  placeholder="1.500.000" 
                  style="padding-left: 3rem;"
                  class="h-11 border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 shadow-sm focus-visible:ring-primary/30" 
                />
              </div>
            </div>
          </template>

        </div>
      </div>
      
      <!-- Footer / Actions -->
      <div class="p-6 border-t border-border/50 bg-muted/10 flex items-center justify-end gap-4">
        <Button @click="handleCancel" variant="outline" class="px-8 h-12 rounded-xl font-semibold border-2 hover:bg-slate-100 dark:hover:bg-slate-800">
          Batal
        </Button>
        <Button @click="handleSave" class="px-8 h-12 rounded-xl font-semibold shadow-md bg-primary text-primary-foreground hover:bg-primary/90 transition-all hover:-translate-y-0.5">
          Perbarui
        </Button>
      </div>
    </div>
  </div>
</template>
