<script setup>
import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { Search, Plus, Edit, Trash2, Eye, Megaphone, FilterX, ChevronLeft, ChevronRight, ChevronsLeft, ChevronsRight, FileSpreadsheet, FileText } from 'lucide-vue-next'

import { Card, CardContent } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationFirst,
  PaginationItem,
  PaginationLast,
  PaginationNext,
  PaginationPrevious,
} from '@/components/ui/pagination'

const props = defineProps({
  items: {
    type: Array,
    required: true
  },
  readonly: {
    type: Boolean,
    default: false
  },
  isYayasan: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['delete', 'view', 'export-excel', 'export-pdf'])

const searchQuery = ref('')
const selectedCategory = ref('semua')
const selectedStatus = ref('semua')
const selectedSchool = ref('semua')
const currentPage = ref(1)
const itemsPerPage = 5

const router = useRouter()

function addAsset() {
  router.push('/lainnya/aset/tambah')
}

function editAsset(id) {
  router.push(`/lainnya/aset/edit/${id}`)
}

function resetFilters() {
  searchQuery.value = ''
  selectedCategory.value = 'semua'
  selectedStatus.value = 'semua'
  selectedSchool.value = 'semua'
  currentPage.value = 1
}

watch([searchQuery, selectedCategory, selectedStatus, selectedSchool], () => {
  currentPage.value = 1
})

const schoolList = computed(() => {
  const schools = [...new Set(props.items.map(i => i.school_name).filter(Boolean))]
  return schools.sort()
})

const filteredAsets = computed(() => {
  return props.items.filter(aset => {
    const matchesSearch = !searchQuery.value.trim() || 
      aset.name?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      aset.code?.toLowerCase().includes(searchQuery.value.toLowerCase())
      
    let matchesCategory = true
    if (selectedCategory.value && selectedCategory.value !== 'semua') {
      matchesCategory = aset.category?.toLowerCase() === selectedCategory.value.toLowerCase()
    }
    
    let matchesStatus = true
    if (selectedStatus.value && selectedStatus.value !== 'semua') {
      matchesStatus = aset.condition?.toLowerCase() === selectedStatus.value.toLowerCase()
    }

    let matchesSchool = true
    if (props.isYayasan && selectedSchool.value && selectedSchool.value !== 'semua') {
      matchesSchool = aset.school_name === selectedSchool.value
    }
    
    return matchesSearch && matchesCategory && matchesStatus && matchesSchool
  })
})

const paginatedAsets = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filteredAsets.value.slice(start, start + itemsPerPage)
})

const statusBadgeClass = (status) => {
  if (!status) return 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20 shadow-xs'
  const lowerStatus = status.toLowerCase()
  if (lowerStatus === 'baik') {
    return 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 shadow-xs'
  } else if (lowerStatus === 'rusak ringan') {
    return 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 shadow-xs'
  } else if (lowerStatus === 'rusak berat') {
    return 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20 shadow-xs'
  } else if (lowerStatus === 'perbaikan') {
    return 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 shadow-xs'
  }
  return 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20 shadow-xs'
}
</script>

<template>
  <div class="space-y-4">
    <!-- Filters Panel -->
    <div class="space-y-2">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 bg-card border border-border/80 p-3 sm:p-4 rounded-2xl shadow-xs">
        
        <!-- School filter (Yayasan only) -->
        <div v-if="isYayasan" class="col-span-1 lg:col-span-2">
          <Select v-model="selectedSchool">
            <SelectTrigger class="!h-11 w-full bg-background rounded-xl border-border px-2.5 text-xs sm:text-sm">
              <SelectValue placeholder="Pilih Sekolah" />
            </SelectTrigger>
            <SelectContent class="rounded-xl shadow-md border-border bg-card">
              <SelectItem value="semua">Semua Sekolah</SelectItem>
              <SelectItem v-for="school in schoolList" :key="school" :value="school">{{ school }}</SelectItem>
            </SelectContent>
          </Select>
        </div>

        <div :class="['col-span-1', isYayasan ? 'lg:col-span-2' : 'lg:col-span-3']">
          <Select v-model="selectedCategory">
            <SelectTrigger class="!h-11 w-full bg-background rounded-xl border-border px-2.5 text-xs sm:text-sm">
              <SelectValue placeholder="Semua Kategori" />
            </SelectTrigger>
            <SelectContent class="rounded-xl shadow-md border-border bg-card">
              <SelectItem value="semua">Semua Kategori</SelectItem>
              <SelectItem value="elektronik">Elektronik</SelectItem>
              <SelectItem value="furniture">Furniture</SelectItem>
              <SelectItem value="alat_pembelajaran">Alat Pembelajaran</SelectItem>
              <SelectItem value="alat_kebersihan">Alat Kebersihan</SelectItem>
              <SelectItem value="peralatan_kantor">Peralatan Kantor</SelectItem>
              <SelectItem value="peralatan_olahraga">Peralatan Olahraga</SelectItem>
              <SelectItem value="peralatan_laboratorium">Peralatan Laboratorium</SelectItem>
            </SelectContent>
          </Select>
        </div>

        <div :class="['col-span-1', isYayasan ? 'lg:col-span-2' : 'lg:col-span-3']">
          <Select v-model="selectedStatus">
            <SelectTrigger class="!h-11 w-full bg-background rounded-xl border-border px-2.5 text-xs sm:text-sm">
              <SelectValue placeholder="Status Kondisi" />
            </SelectTrigger>
            <SelectContent class="rounded-xl shadow-md border-border bg-card">
              <SelectItem value="semua">Semua Kondisi</SelectItem>
              <SelectItem value="baik">Baik</SelectItem>
              <SelectItem value="rusak ringan">Rusak Ringan</SelectItem>
              <SelectItem value="rusak berat">Rusak Berat</SelectItem>
            </SelectContent>
          </Select>
        </div>

        <div :class="['relative col-span-1 sm:col-span-2', isYayasan ? 'lg:col-span-3' : 'lg:col-span-3']">
          <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground pointer-events-none" />
          <Input 
            v-model="searchQuery" 
            placeholder="Cari Aset..." 
            class="pl-9 h-11 bg-background rounded-xl border-border focus-visible:ring-primary focus-visible:border-primary text-sm w-full" 
          />
        </div>

        <!-- Export buttons (Yayasan and Kepala Sekolah) -->
        <div v-if="isYayasan || readonly" class="col-span-1 sm:col-span-2 lg:col-span-3 flex items-center gap-2 justify-end">
          <Button 
            variant="outline" 
            @click="emit('export-excel')"
            class="h-11 px-3 font-semibold shadow-xs gap-1.5 rounded-xl text-xs border-emerald-500/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/10"
          >
            <FileSpreadsheet class="size-4" />
            Export Excel
          </Button>
          <Button 
            variant="outline" 
            @click="emit('export-pdf')"
            class="h-11 px-3 font-semibold shadow-xs gap-1.5 rounded-xl text-xs border-rose-500/30 text-rose-600 dark:text-rose-400 hover:bg-rose-500/10"
          >
            <FileText class="size-4" />
            Export PDF
          </Button>
        </div>

        <div v-if="!readonly && !isYayasan" class="col-span-1 lg:col-span-3 flex justify-end">
          <Button 
            size="lg" 
            @click="addAsset"
            class="h-11 w-full px-4 font-semibold shadow-xs transition-all hover:scale-[1.01] active:scale-[0.99] gap-2 rounded-xl text-xs sm:text-sm"
          >
            <Plus class="size-4 sm:size-5" />
            Tambah Aset Baru
          </Button>
        </div>
      </div>

      <div v-if="searchQuery || (selectedCategory && selectedCategory !== 'semua') || (selectedStatus && selectedStatus !== 'semua') || (isYayasan && selectedSchool && selectedSchool !== 'semua')" class="flex justify-end">
        <Button 
          variant="ghost" 
          size="sm" 
          @click="resetFilters" 
          class="text-xs text-muted-foreground hover:text-foreground gap-1.5 h-8 rounded-lg"
        >
          <FilterX class="size-3.5" />
          Hapus Semua Filter
        </Button>
      </div>
    </div>

    <!-- Data Table -->
    <Card class="rounded-2xl border-border bg-card shadow-xs overflow-hidden">
      <CardContent class="p-0">
        <div class="overflow-x-auto">
          <Table>
            <TableHeader class="bg-muted/40 border-b border-border/60">
              <TableRow>
                <TableHead v-if="isYayasan" class="font-bold text-foreground py-4 px-4 text-xs uppercase tracking-wider">CABANG SEKOLAH</TableHead>
                <TableHead class="font-bold text-foreground py-4 px-6 text-xs uppercase tracking-wider">KODE ASET</TableHead>
                <TableHead class="font-bold text-foreground py-4 px-4 text-xs uppercase tracking-wider">NAMA ASET</TableHead>
                <TableHead class="font-bold text-foreground py-4 px-4 text-xs uppercase tracking-wider w-[160px]">KATEGORI</TableHead>
                <TableHead class="font-bold text-foreground py-4 px-4 text-xs uppercase tracking-wider">LOKASI</TableHead>
                <TableHead class="font-bold text-foreground py-4 px-4 text-xs uppercase tracking-wider">STATUS</TableHead>
                <TableHead class="font-bold text-foreground py-4 px-6 text-xs uppercase tracking-wider text-center w-[120px]">AKSI</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="aset in paginatedAsets" :key="aset.id" class="group transition-all border-b border-border/50">
                <TableCell v-if="isYayasan" class="py-4 px-4 text-sm font-medium">{{ aset.school_name }}</TableCell>
                <TableCell class="py-4 px-6 font-bold text-foreground">{{ aset.code }}</TableCell>
                <TableCell class="py-4 px-4 text-sm font-medium">{{ aset.name }}</TableCell>
                <TableCell class="py-4 px-4 text-sm font-medium">{{ aset.category }}</TableCell>
                <TableCell class="py-4 px-4 text-sm text-muted-foreground">{{ aset.room || aset.building || '-' }}</TableCell>
                <TableCell class="py-4 px-4">
                  <Badge :class="statusBadgeClass(aset.condition)" class="rounded-full px-2.5 py-0.5 font-bold uppercase tracking-wider text-[10px]">
                    {{ aset.condition }}
                  </Badge>
                </TableCell>
                <TableCell class="py-3 px-6 text-center">
                  <div class="flex items-center justify-center gap-5">
                    <template v-if="!readonly">
                      <button @click="editAsset(aset.id)" class="flex flex-col items-center justify-center gap-1 group/btn text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 transition-colors focus:outline-none w-11">
                        <Edit class="size-5 transition-transform group-hover/btn:scale-110" />
                        <span class="text-[11px] font-bold tracking-wide">Edit</span>
                      </button>
                      <button @click="emit('delete', aset.id)" class="flex flex-col items-center justify-center gap-1 group/btn text-rose-600 dark:text-rose-400 hover:text-rose-700 dark:hover:text-rose-400 transition-colors focus:outline-none w-11">
                        <Trash2 class="size-5 transition-transform group-hover/btn:scale-110" />
                        <span class="text-[11px] font-bold tracking-wide">Hapus</span>
                      </button>
                    </template>
                    <template v-else>
                      <button @click="emit('view', aset.id)" class="flex flex-col items-center justify-center gap-1 group/btn text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors focus:outline-none w-11">
                        <Eye class="size-5 transition-transform group-hover/btn:scale-110" />
                        <span class="text-[11px] font-bold tracking-wide">View</span>
                      </button>
                    </template>
                  </div>
                </TableCell>
              </TableRow>
              
              <!-- Empty state row -->
              <TableRow v-if="filteredAsets.length === 0">
                <TableCell :colspan="isYayasan ? 7 : 6" class="py-16 text-center text-muted-foreground">
                  <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                    <Megaphone class="size-8 text-muted-foreground/60 animate-pulse" />
                    <p class="font-bold text-base text-foreground/80">Tidak Ada Data Aset</p>
                    <p class="text-xs text-muted-foreground/80 leading-relaxed">
                      Tidak ditemukan data aset yang sesuai dengan kata kunci atau filter aktif Anda.
                    </p>
                    <Button variant="outline" size="sm" @click="resetFilters" class="mt-2 text-xs rounded-xl">
                      Hapus Semua Filter
                    </Button>
                  </div>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>

        <!-- Pagination Section -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between p-4 bg-muted/20 border-t border-border/50">
          <div class="text-xs text-muted-foreground text-center sm:text-left font-medium">
            Menampilkan <span class="font-bold text-foreground">{{ filteredAsets.length > 0 ? (currentPage - 1) * itemsPerPage + 1 : 0 }}</span> - <span class="font-bold text-foreground">{{ Math.min(currentPage * itemsPerPage, filteredAsets.length) }}</span> dari <span class="font-bold text-foreground">{{ filteredAsets.length }}</span> Aset
          </div>

          <div class="flex justify-center">
            <Pagination v-model:page="currentPage" :total="filteredAsets.length" :items-per-page="itemsPerPage" :sibling-count="1" show-edges class="w-auto mx-0">
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
      </CardContent>
    </Card>
  </div>
</template>
