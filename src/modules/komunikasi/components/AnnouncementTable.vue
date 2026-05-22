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
  },
  isSchoolRole: {
    type: Boolean,
    default: false
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
      item.deskripsi.toLowerCase().includes(searchQuery.value.toLowerCase())
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
      return 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 hover:bg-blue-500/20 shadow-xs'
    case 'KEUANGAN':
      return 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20 shadow-xs'
    case 'UMUM':
      return 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 hover:bg-amber-500/20 shadow-xs'
    default:
      return 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20 hover:bg-slate-500/20 shadow-xs'
  }
}
</script>

<template>
  <div class="space-y-6">
    <!-- Filters Panel -->
    <div class="space-y-2">
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 bg-card border border-border/80 p-4 rounded-2xl shadow-xs">
        <div class="relative col-span-2">
          <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground pointer-events-none" />
          <Input 
            v-model="searchQuery" 
            placeholder="Cari Pengumuman ...." 
            class="pl-9 h-11 bg-background rounded-xl border-border focus-visible:ring-primary focus-visible:border-primary" 
          />
        </div>

        <div class="col-span-1">
          <Select v-model="selectedCategory">
            <SelectTrigger class="!h-11 w-full bg-background rounded-xl border-border">
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

        <div class="relative col-span-1">
          <Input 
            type="date" 
            v-model="selectedDate" 
            @click="$event.target.showPicker()"
            class="h-11 bg-background rounded-xl border-border focus-visible:ring-primary w-full cursor-pointer" 
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

    <!-- Data Table -->
    <Card class="rounded-2xl border-border bg-card shadow-xs overflow-hidden">
      <CardContent class="p-0">
        <!-- Desktop Table Layout (Visible on md and up, hidden on mobile) -->
        <div class="hidden md:block overflow-x-auto">
          <Table>
            <TableHeader class="bg-muted/40 border-b border-border/60">
              <TableRow>
                <TableHead class="font-bold text-foreground py-4 px-6 text-xs uppercase tracking-wider">JUDUL PENGUMUMAN</TableHead>
                <TableHead class="font-bold text-foreground py-4 px-4 text-xs uppercase tracking-wider w-[130px]">KATEGORI</TableHead>
                <TableHead v-if="!isSchoolRole" class="font-bold text-foreground py-4 px-4 text-xs uppercase tracking-wider w-[150px]">SEKOLAH</TableHead>
                <TableHead class="font-bold text-foreground py-4 px-4 text-xs uppercase tracking-wider w-[120px]">TANGGAL</TableHead>
                <TableHead class="font-bold text-foreground py-4 px-6 text-xs uppercase tracking-wider text-center w-[180px]">AKSI</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow 
                v-for="item in paginatedItems" 
                :key="item.id" 
                class="group transition-all border-b border-border/50"
              >
                <TableCell class="py-4 px-6">
                  <div 
                    class="font-bold text-foreground leading-snug text-sm sm:text-base group-hover:text-primary transition-colors cursor-pointer" 
                    @click="$emit('view', item)"
                  >
                    {{ item.judul }}
                  </div>
                  <div class="text-xs text-muted-foreground mt-1 line-clamp-2 max-w-[550px] leading-relaxed">
                    {{ item.deskripsi }}
                  </div>
                </TableCell>
                
                <TableCell class="py-4 px-4">
                  <Badge :class="categoryBadgeClass(item.kategori)" class="rounded-full px-2.5 py-0.5 font-bold uppercase tracking-wider text-[9px]">
                    {{ item.kategori }}
                  </Badge>
                </TableCell>
                
                <TableCell v-if="!isSchoolRole" class="py-4 px-4">
                  <div class="inline-flex items-center gap-1.5 text-xs text-foreground/80 font-semibold bg-muted/60 border border-border/40 px-2 py-1 rounded-lg">
                    <span class="size-1.5 rounded-full bg-primary/60"></span>
                    {{ item.sekolah }}
                  </div>
                </TableCell>
                
                <TableCell class="py-4 px-4 text-xs font-mono text-muted-foreground font-medium">
                  {{ formatDate(item.tanggal) }}
                </TableCell>
                
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
                      v-if="!isSchoolRole"
                      @click="$emit('edit', item)" 
                      class="flex flex-col items-center justify-center gap-1 group/btn text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 transition-colors focus:outline-none w-11"
                    >
                      <Edit class="size-5 transition-transform group-hover/btn:scale-110" />
                      <span class="text-[11px] font-bold tracking-wide">Edit</span>
                    </button>

                    <button 
                      v-if="!isSchoolRole"
                      @click="$emit('delete', item)" 
                      class="flex flex-col items-center justify-center gap-1 group/btn text-rose-600 dark:text-rose-400 hover:text-rose-700 dark:hover:text-rose-400 transition-colors focus:outline-none w-11"
                    >
                      <Trash2 class="size-5 transition-transform group-hover/btn:scale-110" />
                      <span class="text-[11px] font-bold tracking-wide">Hapus</span>
                    </button>
                  </div>
                </TableCell>
              </TableRow>
              
              <TableRow v-if="filteredItems.length === 0">
                <TableCell :colspan="isSchoolRole ? 4 : 5" class="py-16 text-center text-muted-foreground">
                  <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                    <Megaphone class="size-8 text-muted-foreground/60 animate-pulse" />
                    <p class="font-bold text-base text-foreground/80">Tidak Ada Pengumuman</p>
                    <p class="text-xs text-muted-foreground/80 leading-relaxed">
                      Tidak ditemukan data pengumuman yang sesuai kata kunci atau filter aktif Anda.
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

        <!-- Mobile Card Layout (Visible on small screens, hidden on md and up) -->
        <div class="block md:hidden divide-y divide-border/50">
          <div 
            v-for="item in paginatedItems" 
            :key="item.id" 
            class="p-4 space-y-4 hover:bg-muted/10 transition-colors animate-fade-in"
          >
            <!-- Badge & Target School Row -->
            <div class="flex items-center justify-between gap-2">
              <Badge :class="categoryBadgeClass(item.kategori)" class="rounded-full px-2.5 py-0.5 font-bold uppercase tracking-wider text-[9px]">
                {{ item.kategori }}
              </Badge>
              <div v-if="!isSchoolRole" class="inline-flex items-center gap-1.5 text-xs text-foreground/80 font-semibold bg-muted/60 border border-border/40 px-2 py-1 rounded-lg">
                <span class="size-1.5 bg-primary/60 rounded-full"></span>
                {{ item.sekolah }}
              </div>
            </div>

            <!-- Title & Description -->
            <div class="space-y-1.5">
              <h3 
                @click="$emit('view', item)"
                class="font-bold text-foreground leading-snug text-sm sm:text-base hover:text-primary transition-colors cursor-pointer"
              >
                {{ item.judul }}
              </h3>
              <p class="text-xs text-muted-foreground line-clamp-2 leading-relaxed">
                {{ item.deskripsi }}
              </p>
            </div>

            <!-- Date & Actions Row -->
            <div class="flex items-center justify-between pt-3 border-t border-border/40">
              <span class="text-xs font-mono text-muted-foreground font-medium">
                {{ formatDate(item.tanggal) }}
              </span>
              
              <!-- Actions -->
              <div class="flex items-center gap-4">
                <button 
                  @click="$emit('view', item)" 
                  class="flex flex-col items-center justify-center gap-0.5 group/btn text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors focus:outline-none w-10"
                >
                  <Eye class="size-5 transition-transform group-hover/btn:scale-110" />
                  <span class="text-[10px] font-bold">Lihat</span>
                </button>

                <button 
                  v-if="!isSchoolRole"
                  @click="$emit('edit', item)" 
                  class="flex flex-col items-center justify-center gap-0.5 group/btn text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 transition-colors focus:outline-none w-10"
                >
                  <Edit class="size-5 transition-transform group-hover/btn:scale-110" />
                  <span class="text-[10px] font-bold">Edit</span>
                </button>

                <button 
                  v-if="!isSchoolRole"
                  @click="$emit('delete', item)" 
                  class="flex flex-col items-center justify-center gap-0.5 group/btn text-rose-600 dark:text-rose-400 hover:text-rose-700 dark:hover:text-rose-400 transition-colors focus:outline-none w-10"
                >
                  <Trash2 class="size-5 transition-transform group-hover/btn:scale-110" />
                  <span class="text-[10px] font-bold">Hapus</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Empty State (Mobile) -->
          <div v-if="filteredItems.length === 0" class="py-12 px-4 text-center text-muted-foreground">
            <div class="flex flex-col items-center justify-center gap-2 max-w-xs mx-auto">
              <Megaphone class="size-8 text-muted-foreground/60 animate-pulse" />
              <p class="font-bold text-sm text-foreground/80">Tidak Ada Pengumuman</p>
              <p class="text-[11px] text-muted-foreground/80 leading-relaxed">
                Tidak ditemukan data pengumuman yang sesuai kata kunci atau filter aktif Anda.
              </p>
              <Button variant="outline" size="sm" @click="resetFilters" class="mt-2 text-xs rounded-xl h-8">
                Hapus Semua Filter
              </Button>
            </div>
          </div>
        </div>

        <!-- Reusable Shadcn Pagination Components -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between p-4 bg-muted/20 border-t border-border/50">
          <div class="text-xs text-muted-foreground text-center sm:text-left font-medium">
            Menampilkan <span class="font-bold text-foreground">{{ filteredItems.length > 0 ? (currentPage - 1) * itemsPerPage + 1 : 0 }}</span> - <span class="font-bold text-foreground">{{ Math.min(currentPage * itemsPerPage, filteredItems.length) }}</span> dari <span class="font-bold text-foreground">{{ filteredItems.length }}</span> Pengumuman
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
  </div>
</template>
