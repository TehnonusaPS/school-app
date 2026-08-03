<script setup>
import { computed, reactive, watch, ref, onMounted } from 'vue'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle
} from '@/components/ui/dialog'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue
} from '@/components/ui/select'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Button } from '@/components/ui/button'
import { toast } from 'vue-sonner'
import { useAuthStore } from '@/stores/authStore'
import api from '@/services/api'

const authStore = useAuthStore()
const currentUser = authStore.user

const props = defineProps({
  open: {
    type: Boolean,
    required: true
  },
  mode: {
    type: String,
    default: 'create' // 'create' | 'edit'
  },
  announcement: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['update:open', 'save'])

const isOpen = computed({
  get: () => props.open,
  set: (val) => emit('update:open', val)
})

const schools = ref([])

const loadSchools = async () => {
  if (!authStore.token) return
  try {
    const response = await api.get('/management/schools')
    schools.value = response.data.data.data || []
  } catch (error) {
    console.error('Gagal memuat sekolah sasaran:', error)
  }
}

onMounted(() => {
  loadSchools()
})

const form = reactive({
  id: null,
  judul: '',
  deskripsi: '',
  kategori: 'UMUM',
  sekolah: 'ALL',
  tanggal: ''
})

watch(
  () => props.open,
  (newOpen) => {
    if (newOpen) {
      if (props.mode === 'create') {
        form.id = null
        form.judul = ''
        form.deskripsi = ''
        form.kategori = 'UMUM'
        form.sekolah = 'ALL'
        form.tanggal = new Date().toISOString().split('T')[0]
      } else if (props.announcement) {
        form.id = props.announcement.id
        form.judul = props.announcement.judul
        form.deskripsi = props.announcement.deskripsi
        form.kategori = props.announcement.kategori
        form.sekolah = props.announcement.target_school_id ? props.announcement.target_school_id.toString() : 'ALL'
        form.tanggal = props.announcement.tanggal
      }
    }
  },
  { immediate: true }
)

function handleSubmit() {
  if (!form.judul.trim() || !form.deskripsi.trim() || !form.tanggal) {
    toast.error('Semua kolom wajib diisi!')
    return
  }

  emit('save', {
    id: form.id,
    judul: form.judul.trim(),
    deskripsi: form.deskripsi.trim(),
    kategori: form.kategori,
    target_school_id: form.sekolah === 'ALL' ? null : parseInt(form.sekolah),
    sekolah: form.sekolah === 'ALL' ? 'SEMUA SEKOLAH' : (schools.value.find(s => s.id === parseInt(form.sekolah))?.name || 'SEKOLAH'),
    tanggal: form.tanggal
  })
}
</script>

<template>
  <Dialog v-model:open="isOpen">
    <DialogContent class="sm:max-w-[600px] rounded-2xl border border-border bg-card shadow-2xl  p-6">
      <DialogHeader>
        <DialogTitle class="text-lg font-extrabold text-foreground leading-none">
          {{ mode === 'create' ? 'Buat Pengumuman Baru' : 'Edit Pengumuman' }}
        </DialogTitle>
        <DialogDescription class="text-xs text-muted-foreground mt-1.5">
          Lengkapi detail formulir pengumuman di bawah ini secara lengkap.
        </DialogDescription>
      </DialogHeader>
      
      <form @submit.prevent="handleSubmit" class="space-y-4 pt-3">
        <div class="space-y-1.5">
          <label for="judul" class="text-xs font-bold text-foreground">Judul Pengumuman</label>
          <Input 
            id="judul" 
            v-model="form.judul" 
            placeholder="Masukkan judul pengumuman..." 
            class="h-11 rounded-xl border-border bg-background focus-visible:ring-primary focus-visible:border-primary" 
            required 
          />
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="space-y-1.5">
            <label for="kategori" class="text-xs font-bold text-foreground">Kategori</label>
            <Select v-model="form.kategori">
              <SelectTrigger id="kategori" class="h-11 rounded-xl border-border">
                <SelectValue placeholder="Pilih Kategori" />
              </SelectTrigger>
              <SelectContent class="rounded-xl shadow-lg border-border bg-card">
                <SelectItem value="AKADEMIK">Akademik</SelectItem>
                <SelectItem value="KEUANGAN">Keuangan</SelectItem>
                <SelectItem value="UMUM">Umum</SelectItem>
              </SelectContent>
            </Select>
          </div>
          
          <div class="space-y-1.5">
            <label for="sekolah" class="text-xs font-bold text-foreground">Sekolah Sasaran</label>
            <Select v-model="form.sekolah">
              <SelectTrigger id="sekolah" class="h-11 rounded-xl border-border">
                <SelectValue placeholder="Pilih Sekolah" />
              </SelectTrigger>
              <SelectContent class="rounded-xl shadow-lg border-border bg-card">
                <SelectItem value="ALL">Semua Sekolah</SelectItem>
                <SelectItem v-for="school in schools" :key="school.id" :value="school.id.toString()">
                  {{ school.name }}
                </SelectItem>
              </SelectContent>
            </Select>
          </div>
        </div>
        
        <div class="space-y-1.5">
          <label for="tanggal" class="text-xs font-bold text-foreground">Tanggal Publikasi</label>
          <Input 
            id="tanggal" 
            type="date" 
            v-model="form.tanggal" 
            @click="$event.target.showPicker()"
            class="h-11 rounded-xl border-border bg-background focus-visible:ring-primary cursor-pointer w-full" 
            required 
          />
        </div>
        
        <div class="space-y-1.5">
          <label for="deskripsi" class="text-xs font-bold text-foreground">Detail Pengumuman</label>
          <Textarea 
            id="deskripsi" 
            v-model="form.deskripsi" 
            rows="5" 
            placeholder="Tuliskan isi pengumuman secara rinci di sini..." 
            class="rounded-xl border-border bg-background focus-visible:ring-primary resize-none py-3" 
            required 
          />
        </div>
        
        <DialogFooter class="flex justify-end gap-2 border-t border-border pt-4 mt-6">
          <Button 
            type="button" 
            variant="outline" 
            @click="isOpen = false" 
            class="rounded-xl h-11 px-4 font-semibold"
          >
            Batal
          </Button>
          <Button 
            type="submit" 
            class="rounded-xl h-11 px-6 font-semibold shadow-xs bg-primary hover:bg-primary/95 text-primary-foreground active:scale-[0.98]"
          >
            {{ mode === 'create' ? 'Publikasikan' : 'Simpan Perubahan' }}
          </Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>
