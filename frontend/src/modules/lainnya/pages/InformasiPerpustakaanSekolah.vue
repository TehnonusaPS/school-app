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
import { Book, Hash, Calendar, BookOpen, Layers, School } from 'lucide-vue-next'

import PerpustakaanStatCards from '../components/PerpustakaanStatCards.vue'
import PerpustakaanTable from '../components/PerpustakaanTable.vue'
import { usePerpustakaanStore } from '@/stores/perpustakaanStore'

import { VisAxis, VisGroupedBar, VisXYContainer } from '@unovis/vue'
import {
  ChartContainer,
  ChartCrosshair,
  ChartTooltip,
  ChartTooltipContent,
  componentToString
} from '@/components/ui/chart'

const router = useRouter()
const auth = useAuthStore()
const store = usePerpustakaanStore()

const isYayasan = computed(() => auth.user?.role === 'admin_yayasan')

const books = computed(() => store.items)

const chartData = computed(() => {
  const sains = store.items.filter(i => i.kategori?.toLowerCase() === 'sains').length
  const tik = store.items.filter(i => i.kategori?.toLowerCase() === 'tik').length
  const bahasa = store.items.filter(i => i.kategori?.toLowerCase() === 'bahasa').length
  const sejarah = store.items.filter(i => i.kategori?.toLowerCase() === 'sejarah').length
  return [
    { type: 'Sains', total: sains },
    { type: 'TIK', total: tik },
    { type: 'Bahasa', total: bahasa },
    { type: 'Sejarah', total: sejarah }
  ]
})

const chartConfig = {
  total: {
    label: 'Jumlah Buku',
    color: 'var(--primary)'
  }
}

const isViewModalOpen = ref(false)
const selectedBook = ref(null)

const categoryBadgeClass = (category) => {
  if (!category) return 'bg-slate-500/10 text-slate-600 border border-slate-500/20'
  const lowerCat = category.toLowerCase()
  if (lowerCat === 'sains') return 'bg-blue-500/10 text-blue-600 border border-blue-500/20 shadow-xs'
  if (lowerCat === 'tik') return 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 shadow-xs'
  if (lowerCat === 'bahasa') return 'bg-amber-500/10 text-amber-600 border border-amber-500/20 shadow-xs'
  if (lowerCat === 'sejarah') return 'bg-orange-500/10 text-orange-600 border border-orange-500/20 shadow-xs'
  return 'bg-violet-500/10 text-violet-600 border border-violet-500/20 shadow-xs'
}

function handleView(id) {
  const book = store.items.find(i => i.id === id)
  if (book) {
    selectedBook.value = book
    isViewModalOpen.value = true
  }
}

function handleExportExcel() {
  toast.info('Mengunduh data perpustakaan dalam format Excel...')
}

function handleExportPdf() {
  toast.info('Mengunduh data perpustakaan dalam format PDF...')
}
</script>

<template>
  <div class="space-y-6">
    <div class="flex flex-col gap-1">
      <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight bg-gradient-to-r from-foreground to-foreground/80 bg-clip-text">
        Informasi Buku Perpustakaan
      </h1>
      <p class="text-xs sm:text-sm text-muted-foreground leading-relaxed">
        {{ isYayasan ? 'Lihat katalog dan informasi buku di seluruh perpustakaan cabang sekolah' : 'Lihat katalog dan informasi buku di perpustakaan sekolah' }}
      </p>
    </div>

    <!-- Stats Cards -->
    <PerpustakaanStatCards :is-yayasan="isYayasan" />

    <!-- Grafik Perpustakaan (hanya untuk Kepala Sekolah) -->
    <div v-if="!isYayasan" class="bg-card border border-border rounded-2xl p-6 shadow-xs">
      <div class="mb-6">
        <h2 class="text-lg font-bold">Grafik Buku Perpustakaan</h2>
        <p class="text-sm text-muted-foreground">Distribusi jumlah buku berdasarkan kategorinya</p>
      </div>
      <ChartContainer :config="chartConfig" class="h-[300px] w-full">
        <VisXYContainer
          :data="chartData"
          :x-domain="[-0.5, chartData.length - 0.5]"
          :margin="{ left: -24 }"
        >
          <VisGroupedBar
            :x="(d, i) => i"
            :y="[(d) => d.total]"
            :color="['var(--primary)']"
            :rounded-corners="4"
          />
          <VisAxis
            type="x"
            :tick-format="(i) => chartData[i]?.type"
            :tick-values="chartData.map((_, i) => i)"
            :grid-line="false"
            :tick-line="false"
            :domain-line="false"
          />
          <VisAxis
            type="y"
            :num-ticks="4"
            :tick-line="false"
            :domain-line="false"
            :grid-line="true"
          />
          <ChartTooltip />
          <ChartCrosshair
            :template="componentToString(chartConfig, ChartTooltipContent, {
              labelFormatter: (x) => chartData[Math.round(x)]?.type || ''
            })"
            :color="['var(--primary)']"
            :hide-when-far-from-pointer="false"
            :skip-range-check="true"
          />
        </VisXYContainer>
      </ChartContainer>
    </div>

    <!-- Data Table & Filters -->
    <PerpustakaanTable 
      :items="books" 
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
                <Book class="size-5 text-primary" />
                Detail Buku Perpustakaan
              </DialogTitle>
              <DialogDescription class="text-xs">
                Rincian lengkap dari koleksi buku yang dipilih.
              </DialogDescription>
            </div>
            <Badge v-if="selectedBook" :class="categoryBadgeClass(selectedBook.kategori)" class="rounded-full px-3 py-1 font-bold uppercase tracking-wider text-[10px] whitespace-nowrap">
              {{ selectedBook.kategori }}
            </Badge>
          </div>
        </DialogHeader>

        <div v-if="selectedBook" class="p-6 space-y-6">
          <div class="flex items-center gap-4 p-4 rounded-xl bg-primary/5 border border-primary/10 shadow-xs">
            <div class="size-16 sm:size-20 rounded-lg overflow-hidden bg-muted border border-border/50 shrink-0 shadow-inner relative">
              <img 
                v-if="selectedBook.image" 
                :src="selectedBook.image" 
                :alt="selectedBook.name"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex flex-col items-center justify-center text-muted-foreground bg-slate-100 dark:bg-slate-800">
                <Book class="size-6 mb-1 opacity-50" />
                <span class="text-[8px] font-semibold uppercase tracking-widest opacity-60">No Cover</span>
              </div>
            </div>
            <div>
              <p class="text-sm font-medium text-muted-foreground">Judul Buku</p>
              <p class="text-lg font-bold text-foreground leading-tight">{{ selectedBook.name }}</p>
              <p class="text-xs font-mono text-muted-foreground mt-0.5 font-semibold bg-muted/50 inline-block px-1.5 py-0.5 rounded">ISBN: {{ selectedBook.isbn }}</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <!-- Cabang Sekolah (Yayasan only) -->
            <div v-if="isYayasan" class="space-y-1.5 col-span-2">
              <div class="flex items-center gap-1.5 text-[10px] font-bold text-muted-foreground uppercase tracking-wider">
                <School class="size-3.5" />
                Cabang Sekolah
              </div>
              <p class="text-sm font-semibold">{{ selectedBook.school_name || '-' }}</p>
            </div>

            <div class="space-y-1.5">
              <div class="flex items-center gap-1.5 text-[10px] font-bold text-muted-foreground uppercase tracking-wider">
                <BookOpen class="size-3.5" />
                Penulis
              </div>
              <p class="text-sm font-semibold">{{ selectedBook.penulis || '-' }}</p>
            </div>
            <div class="space-y-1.5">
              <div class="flex items-center gap-1.5 text-[10px] font-bold text-muted-foreground uppercase tracking-wider">
                <Hash class="size-3.5" />
                Penerbit
              </div>
              <p class="text-sm font-semibold">{{ selectedBook.penerbit || '-' }}</p>
            </div>
            <div class="space-y-1.5">
              <div class="flex items-center gap-1.5 text-[10px] font-bold text-muted-foreground uppercase tracking-wider">
                <Calendar class="size-3.5" />
                Tahun Terbit
              </div>
              <p class="text-sm font-semibold">{{ selectedBook.tahunTerbit || '-' }}</p>
            </div>
            <div class="space-y-1.5">
              <div class="flex items-center gap-1.5 text-[10px] font-bold text-muted-foreground uppercase tracking-wider">
                <Layers class="size-3.5" />
                Lokasi Rak
              </div>
              <p class="text-sm font-semibold">{{ selectedBook.lokasiRak || '-' }}</p>
            </div>
            <div class="space-y-1.5 col-span-2">
              <div class="flex items-center justify-between text-[10px] font-bold text-muted-foreground uppercase tracking-wider w-full">
                <span class="flex items-center gap-1.5"><Layers class="size-3.5" /> Jumlah Stok</span>
                <Badge variant="secondary" class="rounded text-[10px]">{{ selectedBook.jumlahStok }} Eksemplar</Badge>
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
