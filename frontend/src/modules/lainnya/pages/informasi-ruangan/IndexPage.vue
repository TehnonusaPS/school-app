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
import { School, MapPin, Users, Info, DoorOpen } from 'lucide-vue-next'

import RuanganStatCards from '../../components/RuanganStatCards.vue'
import RuanganTable from '../../components/RuanganTable.vue'
import { useRuanganStore } from '@/stores/ruanganStore'
import PageHeader from '@/components/page-header/PageHeader.vue'


const router = useRouter()
const auth = useAuthStore()
const store = useRuanganStore()

const isYayasan = computed(() => auth.user?.role === 'admin_yayasan')

const rooms = computed(() => store.items)


const isViewModalOpen = ref(false)
const selectedRoom = ref(null)

const categoryBadgeClass = (category) => {
  if (!category) return 'bg-slate-500/10 text-slate-600 border border-slate-500/20'
  const lowerCat = category.toLowerCase()
  if (lowerCat === 'kelas') return 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 shadow-xs'
  if (lowerCat === 'lab') return 'bg-violet-500/10 text-violet-600 border border-violet-500/20 shadow-xs'
  if (lowerCat === 'fasilitas') return 'bg-amber-500/10 text-amber-600 border border-amber-500/20 shadow-xs'
  return 'bg-slate-500/10 text-slate-600 border border-slate-500/20'
}

function handleView(id) {
  const room = store.getById(id)
  if (room) {
    selectedRoom.value = room
    isViewModalOpen.value = true
  }
}

function handleExportExcel() {
  toast.info('Mengunduh data ruangan dalam format Excel...')
}

function handleExportPdf() {
  toast.info('Mengunduh data ruangan dalam format PDF...')
}
</script>

<template>
  <div class="space-y-6">
    <PageHeader 
      title="Informasi Ruangan Sekolah" 
      :description="isYayasan ? 'Lihat data ruangan di seluruh cabang sekolah yang terdaftar' : 'Lihat data ruangan kelas, laboratorium, dan fasilitas lainnya'" 
    />

    <!-- Stats Cards -->
    <RuanganStatCards :is-yayasan="isYayasan" />


    <!-- Data Table & Filters -->
    <RuanganTable 
      :items="rooms" 
      readonly 
      :is-yayasan="isYayasan"
      @view="handleView" 
      @export-excel="handleExportExcel"
      @export-pdf="handleExportPdf"
    />

    <!-- View Modal -->
    <Dialog v-model:open="isViewModalOpen">
      <DialogContent :show-close-button="false" class="sm:max-w-[500px] p-0 overflow-hidden border-border/80 rounded-2xl">
        <DialogHeader class="px-6 pt-6 pb-4 border-b border-border/50 bg-muted/20">
          <div class="flex items-start justify-between gap-4">
            <div class="space-y-1">
              <DialogTitle class="text-xl font-bold flex items-center gap-2">
                <School class="size-5 text-primary" />
                Detail Ruangan Sekolah
              </DialogTitle>
              <DialogDescription class="text-xs">
                Rincian lengkap dari ruangan yang dipilih.
              </DialogDescription>
            </div>
            <Badge v-if="selectedRoom" :class="categoryBadgeClass(selectedRoom.category)" class="rounded-full px-3 py-1 font-bold uppercase tracking-wider text-[10px] whitespace-nowrap">
              {{ selectedRoom.category }}
            </Badge>
          </div>
        </DialogHeader>

        <div v-if="selectedRoom" class="p-6 space-y-6">
          <div class="flex items-center gap-4 p-4 rounded-xl bg-primary/5 border border-primary/10 shadow-xs">
            <div class="size-16 sm:size-20 rounded-lg overflow-hidden bg-muted border border-border/50 shrink-0 shadow-inner relative">
              <img 
                v-if="selectedRoom.image" 
                :src="selectedRoom.image" 
                :alt="selectedRoom.name"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex flex-col items-center justify-center text-muted-foreground bg-slate-100 dark:bg-slate-800">
                <DoorOpen class="size-6 mb-1 opacity-50" />
                <span class="text-[8px] font-semibold uppercase tracking-widest opacity-60">No Photo</span>
              </div>
            </div>
            <div>
              <p class="text-sm font-medium text-muted-foreground">Nama Ruangan</p>
              <p class="text-lg font-bold text-foreground leading-tight">{{ selectedRoom.name }}</p>
              <p class="text-xs font-mono text-muted-foreground mt-0.5 font-semibold bg-muted/50 inline-block px-1.5 py-0.5 rounded">{{ selectedRoom.code }}</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <!-- Cabang Sekolah (Yayasan only) -->
            <div v-if="isYayasan" class="space-y-1.5 col-span-2">
              <div class="flex items-center gap-1.5 text-[10px] font-bold text-muted-foreground uppercase tracking-wider">
                <School class="size-3.5" />
                Cabang Sekolah
              </div>
              <p class="text-sm font-semibold">{{ selectedRoom.school_name || '-' }}</p>
            </div>

            <div class="space-y-1.5">
              <div class="flex items-center gap-1.5 text-[10px] font-bold text-muted-foreground uppercase tracking-wider">
                <Users class="size-3.5" />
                Kapasitas
              </div>
              <p class="text-sm font-semibold">{{ selectedRoom.capacity || '-' }} <span class="text-muted-foreground font-medium text-xs">Orang</span></p>
            </div>
            <div class="space-y-1.5">
              <div class="flex items-center gap-1.5 text-[10px] font-bold text-muted-foreground uppercase tracking-wider">
                <MapPin class="size-3.5" />
                Luas Area
              </div>
              <p class="text-sm font-semibold">{{ selectedRoom.area || '-' }} <span class="text-muted-foreground font-medium text-xs">m²</span></p>
            </div>
            <div class="space-y-1.5 col-span-2">
              <div class="flex items-center gap-1.5 text-[10px] font-bold text-muted-foreground uppercase tracking-wider">
                <School class="size-3.5" />
                Lokasi Gedung
              </div>
              <p class="text-sm font-semibold">{{ selectedRoom.building || '-' }}</p>
            </div>
            <div class="space-y-1.5 col-span-2">
              <div class="flex items-center gap-1.5 text-[10px] font-bold text-muted-foreground uppercase tracking-wider">
                <Info class="size-3.5" />
                Fasilitas
              </div>
              <div class="flex flex-wrap gap-1.5 mt-1">
                <Badge v-for="fac in (selectedRoom.facilities || [])" :key="fac" variant="secondary" class="text-[10px] rounded-md font-medium px-2 py-0">
                  {{ fac }}
                </Badge>
                <span v-if="!selectedRoom.facilities?.length" class="text-sm text-muted-foreground">-</span>
              </div>
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
