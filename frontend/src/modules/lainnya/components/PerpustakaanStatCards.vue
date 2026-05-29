<script setup>
import { computed } from 'vue'
import { Book, BookOpen, Library, BookMarked } from 'lucide-vue-next'
import { usePerpustakaanStore } from '@/stores/perpustakaanStore'

const store = usePerpustakaanStore()

const stats = computed(() => [
  { 
    title: 'Total Buku', 
    value: store.items.reduce((acc, curr) => acc + (parseInt(curr.jumlahStok) || 0), 0).toLocaleString('id-ID'), 
    icon: Book 
  },
  { title: 'Total Judul', value: store.items.length.toLocaleString('id-ID'), icon: BookOpen },
  { title: 'Total Kategori', value: new Set(store.items.map(i => i.kategori)).size.toLocaleString('id-ID'), icon: Library },
  { 
    title: 'Buku Terbaru', 
    value: store.items.filter(i => {
      const year = parseInt(i.tahunTerbit)
      const currentYear = new Date().getFullYear()
      return year >= currentYear - 3
    }).length.toLocaleString('id-ID'), 
    icon: BookMarked 
  },
])
</script>

<template>
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
</template>
