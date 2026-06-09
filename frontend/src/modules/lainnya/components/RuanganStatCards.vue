<script setup>
import { computed } from 'vue'
import { School, Presentation, FlaskConical, DoorOpen } from 'lucide-vue-next'
import { useRuanganStore } from '@/stores/ruanganStore'
import StatCard from '@/components/stat-card/StatCard.vue'
import StatCardGrid from '@/components/stat-card/StatCardGrid.vue'

const props = defineProps({
  isYayasan: {
    type: Boolean,
    default: false
  }
})

const store = useRuanganStore()

const schoolCount = computed(() => new Set(store.items.map(i => i.school_name).filter(Boolean)).size)

const stats = computed(() => {
  if (props.isYayasan) {
    return [
      { label: 'Cabang Sekolah', value: schoolCount.value.toString(), sub: 'unit terdaftar', icon: School, variant: 'blue' },
      { label: 'Total Ruangan', value: store.items.length.toString(), sub: 'seluruh cabang', icon: DoorOpen, variant: 'emerald' },
      { label: 'Total Ruang Kelas', value: store.items.filter(i => i.category === 'kelas').length.toString(), sub: 'ruang belajar', icon: Presentation, variant: 'violet' },
      { label: 'Total Laboratorium', value: store.items.filter(i => i.category === 'lab').length.toString(), sub: 'fasilitas praktik', icon: FlaskConical, variant: 'amber' },
    ]
  }
  return [
    { label: 'Total Ruangan', value: store.items.length.toString(), sub: 'seluruh ruangan', icon: School, variant: 'blue' },
    { label: 'Ruang Kelas', value: store.items.filter(i => i.category === 'kelas').length.toString(), sub: 'ruang belajar', icon: Presentation, variant: 'emerald' },
    { label: 'Laboratorium', value: store.items.filter(i => i.category === 'lab').length.toString(), sub: 'fasilitas praktik', icon: FlaskConical, variant: 'violet' },
    { label: 'Ruangan Lainnya', value: store.items.filter(i => i.category !== 'kelas' && i.category !== 'lab').length.toString(), sub: 'fasilitas pendukung', icon: DoorOpen, variant: 'amber' },
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
    />
  </StatCardGrid>
</template>
