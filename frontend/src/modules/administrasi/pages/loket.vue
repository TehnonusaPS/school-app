<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { toast } from 'vue-sonner'
import { Plus, UserPlus, CheckCircle, Eye, Pencil, Trash2 } from 'lucide-vue-next'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'

import StatCardGrid from '@/components/stat-card/StatCardGrid.vue'
import StatCard from '@/components/stat-card/StatCard.vue'
import DataTableCard from '@/components/data-table/DataTableCard.vue'

const router = useRouter()
const filterValues = ref({ search: '', tanggal: '' })

const dataList = ref([
  { id: 1, nama: 'Andi Pratama',  nisn: '0123456789', nik: '3174012345678901', noKk: '3174098765432101', tanggal: '2024-01-15', status: 'Diterima' },
  { id: 2, nama: 'Siti Aminah',   nisn: '0123456790', nik: '3174012345678902', noKk: '3174098765432102', tanggal: '2024-01-16', status: 'Ditolak' },
  { id: 3, nama: 'Budi Santoso',  nisn: '0123456791', nik: '3174012345678903', noKk: '3174098765432103', tanggal: '2024-01-17', status: 'Cadangan' },
])

const filteredDataList = computed(() => {
  return dataList.value.filter(siswa => {
    const matchName = siswa.nama.toLowerCase().includes(filterValues.value.search?.toLowerCase() || '')
    const matchTanggal = filterValues.value.tanggal ? siswa.tanggal === filterValues.value.tanggal : true
    return matchName && matchTanggal
  })
})

const columns = [
  { key: 'nama', label: 'Nama Calon Siswa' },
  { key: 'tanggal', label: 'Tanggal Daftar', class: 'hidden md:table-cell' },
  { key: 'status', label: 'Status' },
  { key: 'actions', label: 'Aksi', align: 'center' }
]

const filters = [
  { key: 'search', type: 'search', placeholder: 'Cari Nama Calon Siswa...' },
  { key: 'tanggal', type: 'date', label: 'Tanggal Daftar' }
]

const statusClass = (status) => {
  switch (status) {
    case 'Diterima':   return 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30'
    case 'Ditolak':    return 'bg-red-500/20 text-red-400 border border-red-500/30'
    case 'Cadangan':   return 'bg-amber-500/20 text-amber-400 border border-amber-500/30'
    default:           return 'bg-surface-variant text-on-surface-variant'
  }
}

const daftarkanSiswa = () => {
  router.push('/administrasi/tambah-siswa')
}

const lihatSiswa = (item) => {
  const id = typeof item === 'object' ? item.id : item;
  router.push(`/administrasi/detail-pendaftaran?id=${id}`)
}

const editSiswa = (item) => {
  const id = typeof item === 'object' ? item.id : item;
  router.push(`/administrasi/edit-pendaftaran?id=${id}`)
}

const hapusSiswa = (id) => {
  dataList.value = dataList.value.filter(s => s.id !== id)
  toast.success('Data siswa berhasil dihapus')
}

const totalPendaftar = computed(() => dataList.value.length)
const totalDiterima = computed(() => dataList.value.filter(s => s.status === 'Diterima').length)
</script>

<template>
  <div class="flex flex-col gap-6 relative z-10 w-full p-2 sm:p-4">
    <!-- Header Section with Stats -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
      <StatCardGrid :cols="2" class="w-full md:w-auto">
        <StatCard 
          label="TOTAL PENDAFTAR BARU" 
          :value="totalPendaftar" 
          color="primary" 
          :icon="UserPlus" 
          trend="+15%"
          trendDirection="up"
          sub="dibanding tahun lalu"
          class="glass-panel rounded-[20px] loket-stat-card"
        />
        <StatCard 
          label="TOTAL SISWA DITERIMA" 
          :value="totalDiterima" 
          color="emerald" 
          :icon="CheckCircle" 
          trend="+8%"
          trendDirection="up"
          sub="dibanding tahun lalu"
          class="glass-panel rounded-[20px] loket-stat-card"
        />
      </StatCardGrid>
      <Button @click="daftarkanSiswa" class="shrink-0 h-9">
        <Plus class="w-4 h-4 mr-1" />
        Daftarkan Siswa Baru
      </Button>
    </div>

    <!-- DataTableCard Section (Seragam dengan module lain) -->
    <DataTableCard
      :columns="columns"
      :items="filteredDataList"
      :filters="filters"
      v-model:filterValues="filterValues"
      :total="filteredDataList.length"
      deleteLabel="nama"
      @view="lihatSiswa"
      @edit="editSiswa"
      @delete="hapusSiswa"
      class="glass-panel glass-panel-glow border-none shadow-none text-on-surface [&_.bg-muted\/50]:bg-transparent [&_.bg-background]:bg-transparent [&_th]:text-on-surface [&_th]:border-b [&_th]:border-white/10 [&_th]:py-5 [&_th]:px-6 [&_th]:font-bold [&_th]:tracking-wide [&_td]:border-b [&_td]:border-white/5 [&_td]:py-4 [&_td]:px-6 [&_tr:hover]:bg-white/5 [&_table]:w-full [&_.border-b]:border-white/10"
    >
      <template #cell-nama="{ item }">
        <div class="font-bold text-on-surface text-[15px]">{{ item.nama }}</div>
        <div class="text-xs text-on-surface-variant mt-0.5 flex flex-wrap gap-x-2">
          <span>NISN: {{ item.nisn }}</span>
          <span class="md:hidden opacity-70">• {{ item.tanggal }}</span>
        </div>
      </template>

      <template #cell-status="{ item }">
        <span
          class="px-3 py-1.5 rounded-full text-xs font-bold"
          :class="statusClass(item.status)"
        >
          {{ item.status }}
        </span>
      </template>
    </DataTableCard>
  </div>
</template>

<style scoped>
.glass-panel {
  background: rgba(45, 52, 73, 0.4) !important;
  backdrop-filter: blur(12px) !important;
  -webkit-backdrop-filter: blur(12px) !important;
  border: 1px solid rgba(255, 255, 255, 0.1) !important;
}

.glass-panel-glow {
  position: relative;
  overflow: hidden;
}

.glass-panel-glow::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -50%;
  width: 100%;
  height: 100%;
  background: radial-gradient(circle, rgba(37, 99, 235, 0.15) 0%, rgba(37, 99, 235, 0) 70%);
  z-index: 0;
  pointer-events: none;
}

.glass-panel-glow > * {
  position: relative;
  z-index: 1;
}

/* Custom scrollbar for table */
:deep(::-webkit-scrollbar) {
  width: 6px;
  height: 6px;
}
:deep(::-webkit-scrollbar-track) {
  background: rgba(255,255,255,0.05);
  border-radius: 4px;
}
:deep(::-webkit-scrollbar-thumb) {
  background: rgba(255,255,255,0.2);
  border-radius: 4px;
}
:deep(::-webkit-scrollbar-thumb:hover) {
  background: rgba(255,255,255,0.3);
}

/* Style tambahan agar DataTableCard tampak Glassmorphism */
:deep(.bg-card) {
  background-color: transparent !important;
}

/* Mengatur khusus StatCard di halaman loket agar text bisa wrap (tidak terpotong truncate) */
:deep(.loket-stat-card .truncate) {
  white-space: normal !important;
  display: -webkit-box !important;
  -webkit-line-clamp: 2 !important;
  -webkit-box-orient: vertical !important;
  overflow: hidden !important;
  line-height: 1.25 !important;
}
</style>
