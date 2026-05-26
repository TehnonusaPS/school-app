<script setup>
import { ref, computed, watch } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import { useRouter } from 'vue-router'
import { Search, Plus, Edit, Trash2, ChevronLeft, ChevronRight, ChevronsLeft, ChevronsRight, Megaphone, FilterX, Book, BookOpen, Library, BookMarked, Lock } from 'lucide-vue-next'

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

const auth = useAuthStore()
const router = useRouter()

// Data Dummy
const books = ref([
  { id: 1, title: 'FISIKA', isbn: '999-1', category: 'SAINS', stock: 12, location: 'A-01' },
  { id: 2, title: 'BIOLOGI', isbn: '999-2', category: 'SAINS', stock: 15, location: 'A-01' },
  { id: 3, title: 'MATEMATIKA', isbn: '999-3', category: 'SAINS', stock: 20, location: 'A-01' },
  { id: 4, title: 'ALGORITMA & PEMROGRAMAN', isbn: '999-4', category: 'TIK', stock: 25, location: 'A-01' },
  { id: 5, title: 'BASIS DATA', isbn: '999-5', category: 'TIK', stock: 100, location: 'A-01' },
])

const searchQuery = ref('')
const selectedCategory = ref('semua')
const currentPage = ref(1)
const itemsPerPage = 5

function resetFilters() {
  searchQuery.value = ''
  selectedCategory.value = 'semua'
  currentPage.value = 1
}

watch([searchQuery, selectedCategory], () => {
  currentPage.value = 1
})

const filteredBooks = computed(() => {
  return books.value.filter(book => {
    const matchesSearch = !searchQuery.value.trim() || 
      book.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      book.isbn.toLowerCase().includes(searchQuery.value.toLowerCase())
      
    let matchesCategory = true
    if (selectedCategory.value && selectedCategory.value !== 'semua') {
      matchesCategory = book.category.toLowerCase() === selectedCategory.value.toLowerCase()
    }
    
    return matchesSearch && matchesCategory
  })
})

const paginatedBooks = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filteredBooks.value.slice(start, start + itemsPerPage)
})

const stats = [
  { title: 'Total Buku', value: '1.284', icon: Book },
  { title: 'Total Judul', value: '120', icon: BookOpen },
  { title: 'Total Kategori', value: '120', icon: Library },
  { title: 'Buku Terbaru', value: '120', icon: BookMarked },
]
</script>

<template>
  <div v-if="auth.user?.role === 'admin_sekolah'" class="space-y-6">
    <div class="flex flex-col gap-1">
      <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight bg-gradient-to-r from-foreground to-foreground/80 bg-clip-text">
        Manajemen Buku Perpustakaan
      </h1>
      <p class="text-xs sm:text-sm text-muted-foreground leading-relaxed">
        Kelola koleksi buku yang ada di perpustakaan sekolah
      </p>
    </div>

    <!-- Stats Cards -->
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
      <div v-for="(stat, index) in stats" :key="index" class="flex flex-col bg-card border border-border/80 p-4 sm:p-5 rounded-2xl shadow-xs transition-all hover:scale-[1.02] hover:shadow-sm cursor-default">
        <div class="flex items-center gap-3 sm:gap-4">
          <div class="p-2.5 sm:p-3 bg-primary/10 text-primary rounded-xl border border-primary/20 shrink-0">
            <component :is="stat.icon" class="size-5 sm:size-6 text-primary" />
          </div>
          <div>
            <p class="text-[10px] sm:text-xs font-semibold text-muted-foreground uppercase tracking-wider leading-none">{{ stat.title }}</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-foreground mt-1 sm:mt-1.5 tabular-nums leading-none">
              {{ stat.value }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters Panel -->
    <div class="space-y-2">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 bg-card border border-border/80 p-3 sm:p-4 rounded-2xl shadow-xs">
        
        <div class="col-span-1 lg:col-span-3">
          <Select v-model="selectedCategory">
            <SelectTrigger class="!h-11 w-full bg-background rounded-xl border-border px-2.5 text-xs sm:text-sm">
              <SelectValue placeholder="Semua Kategori" />
            </SelectTrigger>
            <SelectContent class="rounded-xl shadow-md border-border bg-card">
              <SelectItem value="semua">Semua Kategori</SelectItem>
              <SelectItem value="sains">Sains</SelectItem>
              <SelectItem value="tik">TIK</SelectItem>
              <SelectItem value="bahasa">Bahasa</SelectItem>
              <SelectItem value="sejarah">Sejarah</SelectItem>
            </SelectContent>
          </Select>
        </div>

        <div class="relative col-span-1 sm:col-span-2 lg:col-span-6">
          <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground pointer-events-none" />
          <Input 
            v-model="searchQuery" 
            placeholder="Cari Buku..." 
            class="pl-9 h-11 bg-background rounded-xl border-border focus-visible:ring-primary focus-visible:border-primary text-sm w-full" 
          />
        </div>

        <div class="col-span-1 lg:col-span-3 flex justify-end">
          <Button 
            size="lg" 
            class="h-11 w-full px-4 font-semibold shadow-xs transition-all hover:scale-[1.01] active:scale-[0.99] gap-2 rounded-xl text-xs sm:text-sm"
          >
            <Plus class="size-4 sm:size-5" />
            Tambah Data
          </Button>
        </div>
      </div>

      <div v-if="searchQuery || (selectedCategory && selectedCategory !== 'semua')" class="flex justify-end">
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
                <TableHead class="font-bold text-foreground py-4 px-6 text-xs uppercase tracking-wider">JUDUL BUKU</TableHead>
                <TableHead class="font-bold text-foreground py-4 px-4 text-xs uppercase tracking-wider">ISBN</TableHead>
                <TableHead class="font-bold text-foreground py-4 px-4 text-xs uppercase tracking-wider w-[160px]">KATEGORI</TableHead>
                <TableHead class="font-bold text-foreground py-4 px-4 text-xs uppercase tracking-wider text-center">STOK</TableHead>
                <TableHead class="font-bold text-foreground py-4 px-4 text-xs uppercase tracking-wider text-center">LOKASI RAK</TableHead>
                <TableHead class="font-bold text-foreground py-4 px-6 text-xs uppercase tracking-wider text-center w-[160px]">AKSI</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="book in paginatedBooks" :key="book.id" class="group transition-all border-b border-border/50">
                <TableCell class="py-4 px-6 font-bold text-foreground text-xs">{{ book.title }}</TableCell>
                <TableCell class="py-4 px-4 text-sm font-medium">{{ book.isbn }}</TableCell>
                <TableCell class="py-4 px-4 text-sm font-medium">{{ book.category }}</TableCell>
                <TableCell class="py-4 px-4 text-sm font-medium text-center">{{ book.stock }}</TableCell>
                <TableCell class="py-4 px-4 text-sm text-muted-foreground text-center">{{ book.location }}</TableCell>
                <TableCell class="py-3 px-6 text-center">
                  <div class="flex items-center justify-center gap-5">
                    <button class="flex flex-col items-center justify-center gap-1 group/btn text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 transition-colors focus:outline-none w-11">
                      <Edit class="size-5 transition-transform group-hover/btn:scale-110" />
                      <span class="text-[11px] font-bold tracking-wide">Edit</span>
                    </button>
                    <button class="flex flex-col items-center justify-center gap-1 group/btn text-rose-600 dark:text-rose-400 hover:text-rose-700 dark:hover:text-rose-400 transition-colors focus:outline-none w-11">
                      <Trash2 class="size-5 transition-transform group-hover/btn:scale-110" />
                      <span class="text-[11px] font-bold tracking-wide">Hapus</span>
                    </button>
                  </div>
                </TableCell>
              </TableRow>
              
              <!-- Empty state row -->
              <TableRow v-if="filteredBooks.length === 0">
                <TableCell colspan="6" class="py-16 text-center text-muted-foreground">
                  <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                    <Megaphone class="size-8 text-muted-foreground/60 animate-pulse" />
                    <p class="font-bold text-base text-foreground/80">Tidak Ada Data Buku</p>
                    <p class="text-xs text-muted-foreground/80 leading-relaxed">
                      Tidak ditemukan buku yang sesuai dengan kata kunci atau filter aktif Anda.
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
            Menampilkan <span class="font-bold text-foreground">{{ filteredBooks.length > 0 ? (currentPage - 1) * itemsPerPage + 1 : 0 }}</span> - <span class="font-bold text-foreground">{{ Math.min(currentPage * itemsPerPage, filteredBooks.length) }}</span> dari <span class="font-bold text-foreground">{{ filteredBooks.length }}</span> Buku
          </div>

          <div class="flex justify-center">
            <Pagination v-model:page="currentPage" :total="filteredBooks.length" :items-per-page="itemsPerPage" :sibling-count="1" show-edges class="w-auto mx-0">
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
  
  <div v-else class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
    <div class="bg-card border border-border rounded-2xl p-8 max-w-md w-full shadow-sm flex flex-col items-center">
      <div class="size-16 bg-primary/10 text-primary rounded-2xl flex items-center justify-center mb-6 border border-primary/20">
        <Lock class="size-8" />
      </div>
      <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-foreground mb-2">Akses Dibatasi</h2>
      <p class="text-sm text-muted-foreground leading-relaxed mb-8">
        Halaman Manajemen Perpustakaan untuk role Anda (Kepala Sekolah / Admin Yayasan) belum tersedia dan sedang dalam tahap pengembangan.
      </p>
      <Button @click="router.push('/dashboard')" size="lg" class="w-full font-semibold rounded-xl transition-all active:scale-[0.98]">
        Kembali ke Dashboard
      </Button>
    </div>
  </div>
</template>
