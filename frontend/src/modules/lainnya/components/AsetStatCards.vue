<script setup>
import { computed } from 'vue'
import { Package, CheckCircle, Wrench, TriangleAlert, School } from 'lucide-vue-next'
import { useAsetStore } from '@/stores/asetStore'
import StatCard from '@/components/stat-card/StatCard.vue'
import StatCardGrid from '@/components/stat-card/StatCardGrid.vue'

const props = defineProps({
  isYayasan: {
    type: Boolean,
    default: false
  }
})

const store = useAsetStore()

const schoolCount = computed(() => new Set(store.items.map(i => i.school_name).filter(Boolean)).size)
const layakPakai = computed(() => store.items.filter(i => i.condition === 'Baik').length)
const butuhPerbaikan = computed(() => store.items.filter(i => i.condition !== 'Baik').length)

const stats = computed(() => {
  if (props.isYayasan) {
    return [
      { label: 'Total Cabang', value: schoolCount.value.toLocaleString('id-ID'), sub: 'unit terdaftar', icon: School, variant: 'blue', illustration: 'globe' },
      { label: 'Total Aset', value: store.items.length.toLocaleString('id-ID'), sub: 'seluruh cabang', icon: Package, variant: 'emerald', illustration: 'bag' },
      { label: 'Aset Layak Pakai', value: layakPakai.value.toLocaleString('id-ID'), sub: 'kondisi baik', icon: CheckCircle, variant: 'violet', illustration: 'star' },
      { label: 'Butuh Perbaikan', value: butuhPerbaikan.value.toLocaleString('id-ID'), sub: 'perlu tindakan', icon: Wrench, variant: 'amber', illustration: 'crayon' },
    ]
  }
  return [
    { label: 'Total Aset', value: store.items.length.toLocaleString('id-ID'), sub: 'semua aset', icon: Package, variant: 'blue', illustration: 'globe' },
    { label: 'Kondisi Baik', value: store.items.filter(i => i.condition === 'Baik').length.toLocaleString('id-ID'), sub: 'layak pakai', icon: CheckCircle, variant: 'emerald', illustration: 'star' },
    { label: 'Rusak Ringan', value: store.items.filter(i => i.condition === 'Rusak Ringan' || i.condition === 'Perbaikan').length.toLocaleString('id-ID'), sub: 'perlu perbaikan', icon: Wrench, variant: 'amber', illustration: 'crayon' },
    { label: 'Rusak Berat', value: store.items.filter(i => i.condition === 'Rusak Berat').length.toLocaleString('id-ID'), sub: 'tidak layak', icon: TriangleAlert, variant: 'rose', illustration: 'ruler' },
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
