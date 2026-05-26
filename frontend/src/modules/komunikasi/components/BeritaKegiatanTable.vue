<script setup>
import { computed, ref, watch } from 'vue'
import { Card, CardContent } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Badge } from '@/components/ui/badge'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue
} from '@/components/ui/select'
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
import {
  Search,
  Calendar,
  Eye,
  Edit,
  Trash2,
  Megaphone,
  FilterX,
  ChevronLeft,
  ChevronRight,
  ChevronsLeft,
  ChevronsRight
} from 'lucide-vue-next'

const props = defineProps({
  items: {
    type: Array,
    required: true
  }
})

const emit = defineEmits(['view', 'edit', 'delete'])

// --- Filtering State ---
const searchQuery = ref('')
const selectedCategory = ref('all')
const selectedDate = ref('')
const currentPage = ref(1)
const itemsPerPage = 5

function resetFilters() {
  searchQuery.value = ''
  selectedCategory.value = 'all'
  selectedDate.value = ''
  currentPage.value = 1
}

watch([searchQuery, selectedCategory, selectedDate], () => {
  currentPage.value = 1
})

// --- Computed Filters & Slices ---
const filteredItems = computed(() => {
  return props.items.filter(item => {
    const matchesSearch = !searchQuery.value.trim() || 
      item.judul.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      item.isi.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesCategory = selectedCategory.value === 'all' || item.kategori === selectedCategory.value
    const matchesDate = !selectedDate.value || item.tanggal === selectedDate.value
    return matchesSearch && matchesCategory && matchesDate
  })
})

const paginatedItems = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filteredItems.value.slice(start, start + itemsPerPage)
})

// --- Formatting & Styles ---
const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  const [year, month, day] = dateStr.split('-')
  return `${day}/${month}/${year}`
}

const categoryBadgeClass = (kategori) => {
  switch (kategori) {
    case 'AKADEMIK':
      return 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 shadow-xs'
    case 'UMUM':
      return 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 shadow-xs'
    case 'KEUANGAN':
      return 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 shadow-xs'
    default:
      return 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20 shadow-xs'
  }
}
</script>

<template>
  <div class="space-y-6">
    <!-- Filters Panel -->
    <div class="space-y-2">
      <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-12 gap-3 bg-card border border-border/80 p-3 sm:p-4 rounded-2xl shadow-xs">
        <div class="relative col-span-2 lg:col-span-8">
          <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground pointer-events-none" />
          <Input 
            v-model="searchQuery" 
            placeholder="Cari Judul Berita Kegiatan..." 
            class="pl-9 h-11 bg-background rounded-xl border-border focus-visible:ring-primary focus-visible:border-primary text-sm" 
          />
        </div>

        <div class="col-span-1 lg:col-span-2">
          <Select v-model="selectedCategory">
            <SelectTrigger class="!h-11 w-full bg-background rounded-xl border-border px-2.5 text-xs sm:text-sm">
              <SelectValue placeholder="Pilih Kategori" />
            </SelectTrigger>
            <SelectContent class="rounded-xl shadow-md border-border bg-card">
              <SelectItem value="all">Semua Kategori</SelectItem>
              <SelectItem value="AKADEMIK">Akademik</SelectItem>
              <SelectItem value="KEUANGAN">Keuangan</SelectItem>
              <SelectItem value="UMUM">Umum</SelectItem>
            </SelectContent>
          </Select>
        </div>

        <div class="relative col-span-1 lg:col-span-2">
          <Input 
            type="date" 
            v-model="selectedDate" 
            @click="$event.target.showPicker()"
            class="h-11 bg-background rounded-xl border-border focus-visible:ring-primary w-full cursor-pointer px-2 text-xs sm:text-sm font-medium" 
          />
        </div>
      </div>

      <div v-if="searchQuery || selectedCategory !== 'all' || selectedDate" class="flex justify-end">
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

    <!-- Desktop Table Card Container -->
    <Card class="rounded-2xl border-border bg-card shadow-xs overflow-hidden hidden md:block">
      <CardContent class="p-0">
        <div class="overflow-x-auto">
          <Table>
            <TableHeader class="bg-muted/40 border-b border-border/60">
              <TableRow>
                <TableHead class="font-bold text-foreground py-4 px-6 text-xs uppercase tracking-wider">JUDUL BERITA</TableHead>
                <TableHead class="font-bold text-foreground py-4 px-4 text-xs uppercase tracking-wider w-[140px]">KATEGORI</TableHead>
                <TableHead class="font-bold text-foreground py-4 px-4 text-xs uppercase tracking-wider w-[130px]">TANGGAL</TableHead>
                <TableHead class="font-bold text-foreground py-4 px-6 text-xs uppercase tracking-wider text-center w-[160px]">AKSI</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow 
                v-for="item in paginatedItems" 
                :key="item.id" 
                class="group transition-all border-b border-border/50"
              >
                <!-- Title & Content snippet -->
                <TableCell class="py-4 px-6">
                  <div 
                    class="font-bold text-foreground leading-snug text-sm sm:text-base group-hover:text-primary transition-colors cursor-pointer text-left" 
                    @click="$emit('view', item)"
                  >
                    {{ item.judul }}
                  </div>
                  <div class="text-xs text-muted-foreground mt-1 line-clamp-2 max-w-[550px] leading-relaxed text-left">
                    {{ item.isi }}
                  </div>
                </TableCell>
                
                <!-- Category Badge -->
                <TableCell class="py-4 px-4 text-left">
                  <Badge :class="categoryBadgeClass(item.kategori)" class="rounded-full px-2.5 py-0.5 font-bold uppercase tracking-wider text-[9px]">
                    {{ item.kategori }}
                  </Badge>
                </TableCell>
                
                <!-- Date -->
                <TableCell class="py-4 px-4 text-xs font-mono text-muted-foreground font-medium text-left">
                  {{ formatDate(item.tanggal) }}
                </TableCell>
                
                <!-- Actions Column -->
                <TableCell class="py-3 px-6 text-center">
                  <div class="flex items-center justify-center gap-5">
                    <button 
                      @click="$emit('view', item)" 
                      class="flex flex-col items-center justify-center gap-1 group/btn text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors focus:outline-none w-11"
                    >
                      <Eye class="size-5 transition-transform group-hover/btn:scale-110" />
                      <span class="text-[11px] font-bold tracking-wide">Lihat</span>
                    </button>

                    <button 
                      @click="$emit('edit', item)" 
                      class="flex flex-col items-center justify-center gap-1 group/btn text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 transition-colors focus:outline-none w-11"
                    >
                      <Edit class="size-5 transition-transform group-hover/btn:scale-110" />
                      <span class="text-[11px] font-bold tracking-wide">Edit</span>
                    </button>

                    <button 
                      @click="$emit('delete', item)" 
                      class="flex flex-col items-center justify-center gap-1 group/btn text-rose-600 dark:text-rose-400 hover:text-rose-700 dark:hover:text-rose-400 transition-colors focus:outline-none w-11"
                    >
                      <Trash2 class="size-5 transition-transform group-hover/btn:scale-110" />
                      <span class="text-[11px] font-bold tracking-wide">Hapus</span>
                    </button>
                  </div>
                </TableCell>
              </TableRow>
              
              <!-- Empty state row -->
              <TableRow v-if="filteredItems.length === 0">
                <TableCell colspan="4" class="py-16 text-center text-muted-foreground">
                  <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                    <Megaphone class="size-8 text-muted-foreground/60 animate-pulse" />
                    <p class="font-bold text-base text-foreground/80">Tidak Ada Berita Kegiatan</p>
                    <p class="text-xs text-muted-foreground/80 leading-relaxed">
                      Tidak ditemukan data berita kegiatan yang sesuai kata kunci atau filter aktif Anda.
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

        <!-- Desktop Pagination Section -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between p-4 bg-muted/20 border-t border-border/50">
          <div class="text-xs text-muted-foreground text-center sm:text-left font-medium">
            Menampilkan <span class="font-bold text-foreground">{{ filteredItems.length > 0 ? (currentPage - 1) * itemsPerPage + 1 : 0 }}</span> - <span class="font-bold text-foreground">{{ Math.min(currentPage * itemsPerPage, filteredItems.length) }}</span> dari <span class="font-bold text-foreground">{{ filteredItems.length }}</span> Berita
          </div>

          <div class="flex justify-center">
            <Pagination 
              v-model:page="currentPage" 
              :total="filteredItems.length" 
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
      </CardContent>
    </Card>

    <!-- Mobile Stack Layout -->
    <div class="block md:hidden space-y-4 animate-fade-in">
      <div 
        v-for="item in paginatedItems" 
        :key="item.id" 
        class="bg-card border border-border/80 p-5 rounded-2xl shadow-xs space-y-4 transition-all duration-200 hover:scale-[1.01] hover:shadow-sm"
      >
        <!-- Card Header: Category Badge & Date -->
        <div class="flex items-center justify-between gap-2">
          <Badge :class="categoryBadgeClass(item.kategori)" class="rounded-full px-2.5 py-0.5 font-bold uppercase tracking-wider text-[9px]">
            {{ item.kategori }}
          </Badge>
        </div>

        <!-- Card Body: Title & Content Snippet -->
        <div class="space-y-1.5">
          <h3 
            @click="$emit('view', item)"
            class="font-bold text-foreground leading-snug text-sm sm:text-base hover:text-primary transition-colors cursor-pointer text-left"
          >
            {{ item.judul }}
          </h3>
          <p class="text-xs text-muted-foreground/90 line-clamp-3 leading-relaxed text-left">
            {{ item.isi }}
          </p>
        </div>

        <!-- Card Divider -->
        <div class="border-t border-border/60"></div>

        <!-- Card Footer: Date & Actions -->
        <div class="flex items-center justify-between pt-1">
          <div class="text-[11px] font-mono text-muted-foreground font-medium">
            {{ formatDate(item.tanggal) }}
          </div>
          
          <div class="flex items-center gap-4">
            <button 
              @click="$emit('view', item)" 
              class="flex flex-col items-center justify-center gap-0.5 group/btn text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors focus:outline-none w-10"
            >
              <Eye class="size-5 transition-transform group-hover/btn:scale-110" />
              <span class="text-[10px] font-bold">Lihat</span>
            </button>

            <button 
              @click="$emit('edit', item)" 
              class="flex flex-col items-center justify-center gap-0.5 group/btn text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 transition-colors focus:outline-none w-10"
            >
              <Edit class="size-5 transition-transform group-hover/btn:scale-110" />
              <span class="text-[10px] font-bold">Edit</span>
            </button>

            <button 
              @click="$emit('delete', item)" 
              class="flex flex-col items-center justify-center gap-0.5 group/btn text-rose-600 dark:text-rose-400 hover:text-rose-700 dark:hover:text-rose-400 transition-colors focus:outline-none w-10"
            >
              <Trash2 class="size-5 transition-transform group-hover/btn:scale-110" />
              <span class="text-[10px] font-bold">Hapus</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Empty State for Mobile -->
      <div v-if="filteredItems.length === 0" class="bg-card border border-border/80 rounded-2xl py-12 px-4 text-center text-muted-foreground shadow-xs">
        <div class="flex flex-col items-center justify-center gap-2 max-w-xs mx-auto">
          <Megaphone class="size-8 text-muted-foreground/60 animate-pulse" />
          <p class="font-bold text-sm text-foreground/80">Tidak Ada Berita Kegiatan</p>
          <p class="text-[11px] text-muted-foreground/80 leading-relaxed">
            Tidak ditemukan data berita kegiatan yang sesuai kata kunci atau filter aktif Anda.
          </p>
          <Button variant="outline" size="sm" @click="resetFilters" class="mt-2 text-xs rounded-xl h-8">
            Hapus Semua Filter
          </Button>
        </div>
      </div>

      <!-- Mobile Pagination Card -->
      <div v-if="filteredItems.length > 0" class="bg-card border border-border/80 rounded-2xl p-4 shadow-xs space-y-3.5">
        <div class="text-xs text-muted-foreground text-center font-medium">
          Menampilkan <span class="font-bold text-foreground">{{ filteredItems.length > 0 ? (currentPage - 1) * itemsPerPage + 1 : 0 }}</span> - <span class="font-bold text-foreground">{{ Math.min(currentPage * itemsPerPage, filteredItems.length) }}</span> dari <span class="font-bold text-foreground">{{ filteredItems.length }}</span> Berita
        </div>

        <div class="flex justify-center">
          <Pagination 
            v-model:page="currentPage" 
            :total="filteredItems.length" 
            :items-per-page="itemsPerPage" 
            :sibling-count="0" 
            show-edges
            class="w-auto mx-0"
          >
            <PaginationContent v-slot="{ items }">
              <PaginationPrevious class="size-9 p-0 rounded-xl hover:bg-muted border border-border/50">
                <ChevronLeft class="size-4" />
              </PaginationPrevious>
              <template v-for="(item, index) in items">
                <PaginationItem 
                  v-if="item.type === 'page'" 
                  :key="index" 
                  :value="item.value" 
                  :is-active="item.value === currentPage"
                  class="size-9 rounded-xl border border-border/50 text-xs font-bold font-mono transition-all duration-200"
                  :class="item.value === currentPage ? 'bg-primary text-primary-foreground font-extrabold shadow-xs hover:bg-primary/90' : 'text-muted-foreground hover:bg-muted'"
                >
                  {{ item.value }}
                </PaginationItem>
                <PaginationEllipsis v-else :key="item.type" :index="index" class="size-9 text-muted-foreground" />
              </template>
              <PaginationNext class="size-9 p-0 rounded-xl hover:bg-muted border border-border/50">
                <ChevronRight class="size-4" />
              </PaginationNext>
            </PaginationContent>
          </Pagination>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.25s ease-out forwards;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(4px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
