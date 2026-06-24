<script setup>
import { computed } from 'vue'
import { Book, BookOpen, Library, BookMarked, School } from 'lucide-vue-next'
import { usePerpustakaanStore } from '@/stores/perpustakaanStore'
import StatCard from '@/components/stat-card/StatCard.vue'
import StatCardGrid from '@/components/stat-card/StatCardGrid.vue'

const props = defineProps({
  isYayasan: {
    type: Boolean,
    default: false
  }
})

const store = usePerpustakaanStore()

const schoolCount = computed(() => new Set(store.items.map(i => i.school_name).filter(Boolean)).size)

const stats = computed(() => {
  if (props.isYayasan) {
    return [
      { label: 'Total Cabang', value: schoolCount.value.toLocaleString('id-ID'), sub: 'unit terdaftar', icon: School, variant: 'blue', illustration: 'globe' },
      { label: 'Total Buku', value: store.items.reduce((acc, curr) => acc + (parseInt(curr.jumlahStok) || 0), 0).toLocaleString('id-ID'), sub: 'total eksemplar', icon: Book, variant: 'emerald', illustration: 'textbook' },
      { label: 'Total Judul', value: store.items.length.toLocaleString('id-ID'), sub: 'judul unik', icon: BookOpen, variant: 'violet', illustration: 'open_book' },
      { 
        label: 'Buku Terbaru', 
        value: store.items.filter(i => {
          const year = parseInt(i.tahunTerbit)
          const currentYear = new Date().getFullYear()
          return year >= currentYear - 3
        }).length.toLocaleString('id-ID'), 
        sub: '3 tahun terakhir',
        icon: BookMarked,
        variant: 'amber',
        illustration: 'closed_book'
      },
    ]
  }
  return [
    { 
      label: 'Total Buku', 
      value: store.items.reduce((acc, curr) => acc + (parseInt(curr.jumlahStok) || 0), 0).toLocaleString('id-ID'), 
      sub: 'total eksemplar',
      icon: Book,
      variant: 'blue',
      illustration: 'textbook'
    },
    { label: 'Total Judul', value: store.items.length.toLocaleString('id-ID'), sub: 'judul unik', icon: BookOpen, variant: 'emerald', illustration: 'open_book' },
    { label: 'Total Kategori', value: new Set(store.items.map(i => i.kategori)).size.toLocaleString('id-ID'), sub: 'kategori', icon: Library, variant: 'violet', illustration: 'abc_board' },
    { 
      label: 'Buku Terbaru', 
      value: store.items.filter(i => {
        const year = parseInt(i.tahunTerbit)
        const currentYear = new Date().getFullYear()
        return year >= currentYear - 3
      }).length.toLocaleString('id-ID'), 
      sub: '3 tahun terakhir',
      icon: BookMarked,
      variant: 'amber',
      illustration: 'closed_book'
    },
  ]
})
</script>

<template>
  <StatCardGrid cols="4" :delay="300">
    <StatCard 
      v-for="(stat, index) in stats" 
      :key="index"
      :label="stat.label"
      :value="stat.value"
      :sub="stat.sub"
      :icon="stat.icon"
      :variant="stat.variant"
      :illustration="stat.illustration"
    />
  </StatCardGrid>
</template>
