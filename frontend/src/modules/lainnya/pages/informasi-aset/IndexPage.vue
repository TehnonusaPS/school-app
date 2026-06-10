<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { toast } from 'vue-sonner'
import { useAuthStore } from '@/stores/authStore'

import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
  DialogFooter
} from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Package, MapPin, Calendar, Tag, Info, School } from 'lucide-vue-next'

import AsetStatCards from '../../components/AsetStatCards.vue'
import AsetTable from '../../components/AsetTable.vue'
import { useAsetStore } from '@/stores/asetStore'
import PageHeader from '@/components/page-header/PageHeader.vue'


const router = useRouter()
const auth = useAuthStore()
const store = useAsetStore()

const isYayasan = computed(() => auth.user?.role === 'admin_yayasan')

const asets = computed(() => store.items)


const isViewModalOpen = ref(false)
const selectedAset = ref(null)

const formatCurrency = (value) => {
  if (!value) return '-'
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(value)
}

const statusBadgeClass = (status) => {
  if (!status) return 'bg-slate-500/10 text-slate-600 border border-slate-500/20'
  const lowerStatus = status.toLowerCase()
  if (lowerStatus === 'baik') return 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 shadow-xs'
  if (lowerStatus === 'rusak ringan') return 'bg-amber-500/10 text-amber-600 border border-amber-500/20 shadow-xs'
  if (lowerStatus === 'rusak berat') return 'bg-rose-500/10 text-rose-600 border border-rose-500/20 shadow-xs'
  if (lowerStatus === 'perbaikan') return 'bg-blue-500/10 text-blue-600 border border-blue-500/20 shadow-xs'
  return 'bg-slate-500/10 text-slate-600 border border-slate-500/20'
}

function handleView(id) {
  const aset = store.items.find(i => i.id === id)
  if (aset) {
    selectedAset.value = aset
    isViewModalOpen.value = true
  }
}

function handleExportExcel() {
  toast.info('Mengunduh data aset dalam format Excel...')
}

function handleExportPdf() {
  toast.info('Mengunduh data aset dalam format PDF...')
}
</script>

<template>
  <div class="space-y-6">
    <PageHeader 
      title="Informasi Aset Sekolah" 
      :description="isYayasan ? 'Lihat informasi aset barang di seluruh cabang sekolah yang terdaftar' : 'Lihat informasi aset barang dan kondisinya yang ada di sekolah'" 
    />

    <!-- Stats Cards -->
    <AsetStatCards :is-yayasan="isYayasan" />


    <!-- Data Table & Filters -->
    <AsetTable 
      :items="asets" 
      readonly 
      :is-yayasan="isYayasan"
      @view="handleView" 
      @export-excel="handleExportExcel"
      @export-pdf="handleExportPdf"
    />

    <!-- View Aset Modal -->
    <Dialog v-model:open="isViewModalOpen">
      <DialogContent :show-close-button="false" class="sm:max-w-[500px] p-0 overflow-hidden border-border/80 rounded-2xl">
        <DialogHeader class="px-6 pt-6 pb-4 border-b border-border/50 bg-muted/20">
          <div class="flex items-start justify-between gap-4">
            <div class="space-y-1">
              <DialogTitle class="text-xl font-bold flex items-center gap-2">
                <Package class="size-5 text-primary" />
                Detail Aset Sekolah
              </DialogTitle>
              <DialogDescription class="text-xs">
                Rincian lengkap dari inventaris yang dipilih.
              </DialogDescription>
            </div>
            <Badge v-if="selectedAset" :class="statusBadgeClass(selectedAset.condition)" class="rounded-full px-3 py-1 font-bold uppercase tracking-wider text-[10px] whitespace-nowrap">
              {{ selectedAset.condition }}
            </Badge>
          </div>
        </DialogHeader>

        <div v-if="selectedAset" class="p-6 space-y-6">
          <div class="flex items-center gap-4 p-4 rounded-xl bg-primary/5 border border-primary/10 shadow-xs">
            <!-- Foto Aset / Placeholder -->
            <div class="size-16 sm:size-20 rounded-lg overflow-hidden bg-muted border border-border/50 shrink-0 shadow-inner relative">
              <img 
                v-if="selectedAset.image" 
                :src="selectedAset.image" 
                :alt="selectedAset.name"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex flex-col items-center justify-center text-muted-foreground bg-slate-100 dark:bg-slate-800">
                <Tag class="size-6 mb-1 opacity-50" />
                <span class="text-[8px] font-semibold uppercase tracking-widest opacity-60">No Photo</span>
              </div>
            </div>
            
            <div>
              <p class="text-sm font-medium text-muted-foreground">Nama Aset / Barang</p>
              <p class="text-lg font-bold text-foreground leading-tight">{{ selectedAset.name }}</p>
              <p class="text-xs font-mono text-muted-foreground mt-0.5 font-semibold bg-muted/50 inline-block px-1.5 py-0.5 rounded">{{ selectedAset.code }}</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <!-- Cabang Sekolah (Yayasan only) -->
            <div v-if="isYayasan" class="space-y-1.5 col-span-2">
              <div class="flex items-center gap-1.5 text-[10px] font-bold text-muted-foreground uppercase tracking-wider">
                <School class="size-3.5" />
                Cabang Sekolah
              </div>
              <p class="text-sm font-semibold">{{ selectedAset.school_name || '-' }}</p>
            </div>

            <div class="space-y-1.5">
              <div class="flex items-center gap-1.5 text-[10px] font-bold text-muted-foreground uppercase tracking-wider">
                <Info class="size-3.5" />
                Spesifikasi
              </div>
              <p class="text-sm font-semibold">{{ selectedAset.brand || '-' }} <span class="text-muted-foreground font-medium text-xs">({{ selectedAset.category }})</span></p>
            </div>
            
            <div class="space-y-1.5">
              <div class="flex items-center gap-1.5 text-[10px] font-bold text-muted-foreground uppercase tracking-wider">
                <Calendar class="size-3.5" />
                Tgl Pengadaan
              </div>
              <p class="text-sm font-semibold">{{ selectedAset.date || '-' }}</p>
            </div>
            
            <div class="space-y-1.5 col-span-2">
              <div class="flex items-center gap-1.5 text-[10px] font-bold text-muted-foreground uppercase tracking-wider">
                <MapPin class="size-3.5" />
                Lokasi Terkini
              </div>
              <p class="text-sm font-medium bg-muted/30 p-2.5 rounded-xl border border-border/60">
                <span class="font-bold block">{{ selectedAset.room || '-' }}</span>
                <span class="text-muted-foreground text-xs">{{ selectedAset.building || '-' }}, Lantai {{ selectedAset.floor || '-' }}</span>
              </p>
            </div>
            
            <div class="space-y-1.5 col-span-2">
              <div class="flex items-center gap-1.5 text-[10px] font-bold text-muted-foreground uppercase tracking-wider">
                <Package class="size-3.5" />
                Nilai / Harga
              </div>
              <p class="text-sm font-semibold">{{ formatCurrency(selectedAset.value) }}</p>
            </div>
          </div>
        </div>

        <DialogFooter class="px-6 pt-4 pb-6 border-t border-border/50 bg-muted/20">
          <Button variant="outline" @click="isViewModalOpen = false" class="w-full sm:w-auto font-semibold rounded-xl">Tutup</Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>
