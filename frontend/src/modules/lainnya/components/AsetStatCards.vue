<script setup>
import { computed } from 'vue'
import { Package, CheckCircle, Wrench, TriangleAlert, School } from 'lucide-vue-next'
import { useAsetStore } from '@/stores/asetStore'
import StatCard from '@/components/stat-card/StatCard.vue'

const props = defineProps({
  isYayasan: {
    type: Boolean,
    default: false
  }
})

const store = useAsetStore()

const schoolCount = computed(() => new Set(store.items.map(i => i.school_name).filter(Boolean)).size)
const layakPakai = computed(() => store.items.filter(i => i.condition === 'baik').length)
const butuhPerbaikan = computed(() => store.items.filter(i => i.condition !== 'baik').length)

const stats = computed(() => {
  if (props.isYayasan) {
    return [
      { label: 'Total Cabang', value: schoolCount.value.toLocaleString('id-ID'), sub: 'unit terdaftar', icon: School, variant: 'blue' },
      { label: 'Total Aset', value: store.items.length.toLocaleString('id-ID'), sub: 'seluruh cabang', icon: Package, variant: 'emerald' },
      { label: 'Aset Layak Pakai', value: layakPakai.value.toLocaleString('id-ID'), sub: 'kondisi baik', icon: CheckCircle, variant: 'violet' },
      { label: 'Butuh Perbaikan', value: butuhPerbaikan.value.toLocaleString('id-ID'), sub: 'perlu tindakan', icon: Wrench, variant: 'amber' },
    ]
  }
  return [
    { label: 'Total Aset', value: store.items.length.toLocaleString('id-ID'), sub: 'semua aset', icon: Package, variant: 'blue' },
    { label: 'Kondisi Baik', value: store.items.filter(i => i.condition === 'baik').length.toLocaleString('id-ID'), sub: 'layak pakai', icon: CheckCircle, variant: 'emerald' },
    { label: 'Rusak Ringan', value: store.items.filter(i => i.condition === 'rusak ringan' || i.condition === 'perbaikan').length.toLocaleString('id-ID'), sub: 'perlu perbaikan', icon: Wrench, variant: 'amber' },
    { label: 'Rusak Berat', value: store.items.filter(i => i.condition === 'rusak berat').length.toLocaleString('id-ID'), sub: 'tidak layak', icon: TriangleAlert, variant: 'rose' },
  ]
})
</script>

<template>
  <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
    <StatCard 
      v-for="(stat, index) in stats" 
      :key="index"
      :label="stat.label"
      :value="stat.value"
      :sub="stat.sub"
      :icon="stat.icon"
      :variant="stat.variant"
    />
  </div>
</template>
