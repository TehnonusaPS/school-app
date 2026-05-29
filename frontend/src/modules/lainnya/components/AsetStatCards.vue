<script setup>
import { computed } from 'vue'
import { Package, CheckCircle, Wrench, TriangleAlert } from 'lucide-vue-next'
import { useAsetStore } from '@/stores/asetStore'

const store = useAsetStore()

const stats = computed(() => [
  { title: 'Total Aset', value: store.items.length.toLocaleString('id-ID'), icon: Package },
  { title: 'Kondisi Baik', value: store.items.filter(i => i.condition === 'baik').length.toLocaleString('id-ID'), icon: CheckCircle },
  { title: 'Rusak Ringan', value: store.items.filter(i => i.condition === 'rusak ringan' || i.condition === 'perbaikan').length.toLocaleString('id-ID'), icon: Wrench },
  { title: 'Rusak Berat', value: store.items.filter(i => i.condition === 'rusak berat').length.toLocaleString('id-ID'), icon: TriangleAlert },
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
