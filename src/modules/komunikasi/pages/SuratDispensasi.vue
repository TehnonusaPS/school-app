<script setup>
import { computed, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { Plus, Mail, Eye, Edit, Trash2, Search, Calendar as CalendarIcon, ChevronLeft, ChevronRight, ChevronsLeft, ChevronsRight } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow
} from '@/components/ui/table'
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationFirst,
  PaginationItem,
  PaginationLast,
  PaginationNext,
  PaginationPrevious
} from '@/components/ui/pagination'
import { useSuratDispensasiStore } from '@/stores/suratDispensasiStore'
import { toast } from 'vue-sonner'
import SuratDispensasiPrintModal from '../components/SuratDispensasiPrintModal.vue'

const router = useRouter()
const store = useSuratDispensasiStore()
const searchQuery = ref('')

const isPrintModalOpen = ref(false)
const selectedSurat = ref(null)

const currentPage = ref(1)
const itemsPerPage = 5

watch(searchQuery, () => {
  currentPage.value = 1
})

const suratList = computed(() => {
  if (!searchQuery.value) return store.items
  const query = searchQuery.value.toLowerCase()
  return store.items.filter(item => {
    // Check if any student name in the surat matches the query
    return item.siswa.some(s => s.nama.toLowerCase().includes(query))
  })
})

const paginatedSuratList = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return suratList.value.slice(start, end)
})

function openCreateForm() {
  router.push('/komunikasi/persuratan/dispensasi/buat')
}

function viewSurat(item) {
  selectedSurat.value = item
  isPrintModalOpen.value = true
}

function editSurat(item) {
  router.push('/komunikasi/persuratan/dispensasi/edit/' + item.id)
}

function deleteSurat(item) {
  if (confirm(`Yakin ingin menghapus surat dispensasi ini?`)) {
    store.remove(item.id)
    toast.success('Surat berhasil dihapus!')
  }
}

function formatNamaSiswa(siswaList) {
  if (!siswaList || siswaList.length === 0) return '-'
  const first = siswaList[0].nama
  if (siswaList.length > 1) {
    return `${first} & ${siswaList.length - 1} lainnya`
  }
  return first
}

function formatNisnSiswa(siswaList) {
  if (!siswaList || siswaList.length === 0) return '-'
  const first = siswaList[0].nisn
  if (siswaList.length > 1) {
    return `${first} (+${siswaList.length - 1})`
  }
  return first
}
</script>

<template>
  <div class="space-y-6 p-1">
    <!-- Header & Stats Card -->
    <div>
      <div class="flex flex-col gap-1 mb-6">
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight bg-gradient-to-r from-foreground to-foreground/80 bg-clip-text">
          Daftar Surat Dispensasi Siswa
        </h1>
        <p class="text-xs sm:text-sm text-muted-foreground leading-relaxed">
          Kelola Surat Dispensasi
        </p>
      </div>

      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between bg-card border border-border/80 p-4 sm:p-5 rounded-2xl shadow-xs">
        <div class="flex items-center gap-3 sm:gap-4 border-2 border-primary/20 p-3 rounded-xl bg-primary/5">
          <div class="shrink-0">
            <Mail class="size-6 sm:size-8 text-primary" />
          </div>
          <div>
            <p class="text-[10px] sm:text-xs font-semibold text-muted-foreground uppercase tracking-wider leading-none">Total Surat Dispensasi Siswa</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-foreground mt-1 sm:mt-1.5 tabular-nums leading-none">
              {{ store.items.length }}
            </p>
          </div>
        </div>

        <Button 
          @click="openCreateForm" 
          size="lg" 
          class="h-11 sm:h-12 w-full sm:w-auto px-6 font-semibold shadow-xs transition-all hover:scale-[1.01] active:scale-[0.99] gap-2 rounded-xl text-sm border-2 border-primary/20 bg-primary/10 text-primary hover:bg-primary/20 hover:text-primary"
          variant="outline"
        >
          <Plus class="size-4 sm:size-5" />
          Buat Surat Dispensasi Siswa
        </Button>
      </div>
    </div>

    <!-- Filters & Table -->
    <div class="bg-card border border-border/80 rounded-2xl shadow-xs overflow-hidden">
      <!-- Search & Date Filter -->
      <div class="p-4 border-b border-border/50 flex flex-col sm:flex-row gap-4">
        <div class="relative w-full sm:w-72">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
          <Input 
            v-model="searchQuery"
            placeholder="Cari Nama Siswa ......" 
            class="pl-9 bg-muted/50 border-muted-foreground/20 focus-visible:ring-primary/30"
          />
        </div>
        <div class="relative w-full sm:w-48">
          <CalendarIcon class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
          <Input 
            type="date"
            class="pl-9 bg-muted/50 border-muted-foreground/20 focus-visible:ring-primary/30 text-muted-foreground"
          />
        </div>
      </div>

      <!-- Table -->
      <Table>
        <TableHeader class="bg-muted/30">
          <TableRow class="hover:bg-transparent">
            <TableHead class="font-semibold text-foreground">Nama Siswa</TableHead>
            <TableHead class="font-semibold text-foreground">Tanggal Dibuat</TableHead>
            <TableHead class="font-semibold text-foreground text-center">AKSI</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="item in paginatedSuratList" :key="item.id" class="hover:bg-muted/30 transition-colors">
            <TableCell>
              <div class="flex flex-col">
                <span class="font-medium text-foreground">{{ formatNamaSiswa(item.siswa) }}</span>
                <span class="text-[10px] text-muted-foreground">{{ formatNisnSiswa(item.siswa) }}</span>
              </div>
            </TableCell>
            <TableCell class="text-muted-foreground">{{ item.tanggalDibuat }}</TableCell>
            <TableCell>
              <div class="flex items-center justify-center gap-5">
                <button 
                  @click="viewSurat(item)" 
                  class="flex flex-col items-center justify-center gap-1 group/btn text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors focus:outline-none w-11"
                >
                  <Eye class="size-5 transition-transform group-hover/btn:scale-110" />
                  <span class="text-[11px] font-bold tracking-wide">Lihat</span>
                </button>

                <button 
                  @click="editSurat(item)" 
                  class="flex flex-col items-center justify-center gap-1 group/btn text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 transition-colors focus:outline-none w-11"
                >
                  <Edit class="size-5 transition-transform group-hover/btn:scale-110" />
                  <span class="text-[11px] font-bold tracking-wide">Edit</span>
                </button>

                <button 
                  @click="deleteSurat(item)" 
                  class="flex flex-col items-center justify-center gap-1 group/btn text-rose-600 dark:text-rose-400 hover:text-rose-700 dark:hover:text-rose-400 transition-colors focus:outline-none w-11"
                >
                  <Trash2 class="size-5 transition-transform group-hover/btn:scale-110" />
                  <span class="text-[11px] font-bold tracking-wide">Hapus</span>
                </button>
              </div>
            </TableCell>
          </TableRow>
          <TableRow v-if="suratList.length === 0">
            <TableCell colspan="3" class="h-24 text-center text-muted-foreground">
              Tidak ada data surat yang ditemukan.
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
      
      <!-- Pagination Section matching administrative roles -->
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between p-4 bg-muted/20 border-t border-border/50">
        <div class="text-xs text-muted-foreground text-center sm:text-left font-medium">
          Menampilkan <span class="font-bold text-foreground">{{ suratList.length > 0 ? (currentPage - 1) * itemsPerPage + 1 : 0 }}</span> - <span class="font-bold text-foreground">{{ Math.min(currentPage * itemsPerPage, suratList.length) }}</span> dari <span class="font-bold text-foreground">{{ suratList.length }}</span> Data
        </div>

        <div class="flex justify-center">
          <Pagination 
            v-model:page="currentPage" 
            :total="suratList.length" 
            :items-per-page="itemsPerPage" 
            :sibling-count="1" 
            show-edges
            class="w-auto mx-0"
          >
            <PaginationContent v-slot="{ items }">
              <PaginationFirst class="hidden sm:inline-flex size-8 p-0 rounded-lg hover:bg-muted border border-border/50">
                <ChevronsLeft class="size-4" />
              </PaginationFirst>
              <PaginationPrevious class="size-8 p-0 rounded-lg hover:bg-muted border border-border/50">
                <ChevronLeft class="size-4" />
              </PaginationPrevious>
              <template v-for="(item, index) in items">
                <PaginationItem 
                  v-if="item.type === 'page'" 
                  :key="index" 
                  :value="item.value" 
                  :is-active="item.value === currentPage"
                  class="size-8 rounded-lg border border-border/50 text-xs font-bold font-mono transition-all duration-200"
                  :class="item.value === currentPage ? 'bg-primary text-primary-foreground font-extrabold shadow-xs hover:bg-primary/90' : 'text-muted-foreground hover:bg-muted'"
                >
                  {{ item.value }}
                </PaginationItem>
                <PaginationEllipsis v-else :key="item.type" :index="index" class="size-8 text-muted-foreground" />
              </template>
              <PaginationNext class="size-8 p-0 rounded-lg hover:bg-muted border border-border/50">
                <ChevronRight class="size-4" />
              </PaginationNext>
              <PaginationLast class="hidden sm:inline-flex size-8 p-0 rounded-lg hover:bg-muted border border-border/50">
                <ChevronsRight class="size-4" />
              </PaginationLast>
            </PaginationContent>
          </Pagination>
        </div>
      </div>
    </div>
    
    <!-- Print Preview Modal -->
    <SuratDispensasiPrintModal v-model:open="isPrintModalOpen" :data="selectedSurat" />
  </div>
</template>
