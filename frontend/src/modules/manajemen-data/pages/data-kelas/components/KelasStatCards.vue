<script setup>
import { computed } from 'vue'
import { School, Users, UserCheck, PieChart } from 'lucide-vue-next'
import StatCard from '@/components/stat-card/StatCard.vue'
import StatCardGrid from '@/components/stat-card/StatCardGrid.vue'

const props = defineProps({
  items: {
    type: Array,
    required: true
  }
})

const totalKelas = computed(() => props.items.length)
const totalSiswa = computed(() => props.items.reduce((sum, item) => sum + item.students_count, 0))
const totalKapasitas = computed(() => props.items.reduce((sum, item) => sum + item.capacity, 0))

const persenTerisi = computed(() => {
  if (totalKapasitas.value === 0) return 0
  return Math.round((totalSiswa.value / totalKapasitas.value) * 100)
})

const waliKelasTerisi = computed(() => props.items.filter(i => i.homeroom_teacher).length)
const waliKelasBelum = computed(() => totalKelas.value - waliKelasTerisi.value)

const stats = computed(() => [
  { 
    label: 'Total Kelas', 
    value: totalKelas.value.toLocaleString('id-ID'), 
    sub: 'kelas aktif', 
    icon: School, 
    variant: 'blue' 
  },
  { 
    label: 'Total Siswa', 
    value: totalSiswa.value.toLocaleString('id-ID'), 
    sub: `dari kapasitas ${totalKapasitas.value}`, 
    icon: Users, 
    variant: 'emerald' 
  },
  { 
    label: 'Kursi Terisi', 
    value: `${persenTerisi.value}%`, 
    sub: `${totalKapasitas.value - totalSiswa.value} kursi kosong`, 
    icon: PieChart, 
    variant: persenTerisi.value > 95 ? 'amber' : 'violet' 
  },
  { 
    label: 'Wali Kelas', 
    value: `${waliKelasTerisi.value}/${totalKelas.value}`, 
    sub: waliKelasBelum.value > 0 ? `${waliKelasBelum.value} belum ada` : 'Semua terisi', 
    icon: UserCheck, 
    variant: waliKelasBelum.value > 0 ? 'rose' : 'emerald' 
  },
])
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
