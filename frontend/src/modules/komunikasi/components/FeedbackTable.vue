<script setup>
import { computed, ref, watch } from 'vue'
import DataTableCard from '@/components/data-table/DataTableCard.vue'
import { usePagination } from '@/composables/usePagination'
import { Lock, EyeOff, FileSpreadsheet, FileDown } from 'lucide-vue-next'

const props = defineProps({
  items: {
    type: Array,
    required: true
  }
})

const emit = defineEmits(['view', 'exportExcel', 'exportPdf'])

// --- Filters State ---
const filterValues = ref({
  search: '',
  kategori: 'all',
  tanggal: ''
})

const filters = computed(() => [
  { type: 'search', key: 'search', placeholder: 'Cari berdasarkan Judul, Isi, atau Kelas...' },
  {
    type: 'select',
    key: 'kategori',
    label: 'Kategori:',
    placeholder: 'Semua Kategori',
    options: [
      { label: 'Akademik', value: 'AKADEMIK' },
      { label: 'Fasilitas', value: 'FASILITAS' },
      { label: 'Pelayanan', value: 'PELAYANAN' },
      { label: 'Keuangan', value: 'KEUANGAN' }
    ]
  },
  { type: 'date', key: 'tanggal', label: 'Tanggal:' }
])

const columns = computed(() => [
  { key: 'judul', label: 'Judul Keluhan / Masukan' },
  {
    key: 'kategori',
    label: 'Kategori',
    badge: true,
    badgeVariant: {
      AKADEMIK: 'blue',
      FASILITAS: 'green',
      PELAYANAN: 'amber',
      KEUANGAN: 'purple'
    }
  },
  { key: 'tanggal', label: 'Tanggal', type: 'date' },
  { key: 'actions', label: 'Aksi' }
])

// --- Computed Filters & Pagination ---
const filteredItems = computed(() => {
  return props.items.filter(item => {
    const searchVal = filterValues.value.search?.trim().toLowerCase() || ''
    const matchesSearch = !searchVal || 
      item.judul.toLowerCase().includes(searchVal) ||
      item.pesan.toLowerCase().includes(searchVal) ||
      item.kelas.toLowerCase().includes(searchVal) ||
      item.kategori.toLowerCase().includes(searchVal)
    
    const catVal = filterValues.value.kategori
    const matchesCategory = !catVal || catVal === 'all' || item.kategori === catVal

    const dateVal = filterValues.value.tanggal
    const matchesDate = !dateVal || item.tanggal === dateVal
    
    return matchesSearch && matchesCategory && matchesDate
  })
})

const perPage = ref(5)
const { currentPage, total, from, to, paginatedItems } = usePagination(filteredItems, perPage)

watch(filteredItems, () => {
  currentPage.value = 1
})

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  const [year, month, day] = dateStr.split('-')
  return `${day}/${month}/${year}`
}

const customTableActions = computed(() => [
  {
    label: 'EXPORT EXCEL',
    icon: FileSpreadsheet,
    variant: 'outline',
    click: () => emit('exportExcel')
  },
  {
    label: 'EXPORT PDF',
    icon: FileDown,
    variant: 'outline',
    click: () => emit('exportPdf')
  }
])
</script>

<template>
  <DataTableCard
    :columns="columns"
    :items="paginatedItems"
    :filters="filters"
    :actions="customTableActions"
    v-model:filterValues="filterValues"
    illustration="paper_sheet"
    v-model:perPage="perPage"
    :from="from"
    :to="to"
    :total="total"
    :page="currentPage"
    @update:page="currentPage = $event"
    @view="(id, item) => $emit('view', item)"
  >
    <template #cell-judul="{ item }">
      <div 
        class="font-bold text-foreground leading-snug text-sm sm:text-base hover:text-primary transition-colors cursor-pointer text-left" 
        @click="$emit('view', item)"
      >
        {{ item.judul }}
      </div>
      <div class="text-xs text-muted-foreground mt-1 line-clamp-2 max-w-[550px] leading-relaxed text-left">
        {{ item.pesan }}
      </div>
      
      <!-- Anonymous Sender Tagging -->
      <div class="flex items-center gap-2 mt-2.5 text-[10px] text-muted-foreground/90 font-medium text-left flex-wrap">
        <span class="flex items-center gap-1 bg-muted px-2 py-0.5 rounded border border-border/40">
          <Lock class="size-3 text-emerald-500" />
          Pengirim: <span class="font-semibold text-foreground/80">Wali Murid (Anonim - #{{ item.id }})</span>
        </span>
        <span class="flex items-center gap-1 bg-muted px-2 py-0.5 rounded border border-border/40">
          <EyeOff class="size-3 text-amber-500" />
          Siswa: <span class="font-semibold text-foreground/80">Kelas {{ item.kelas }} (Dirahasiakan)</span>
        </span>
        <span class="flex items-center gap-1 bg-emerald-500/5 text-emerald-600 dark:text-emerald-400 px-2 py-0.5 rounded border border-emerald-500/10 font-bold uppercase tracking-wider text-[8px]">
          RAHASIA / EVALUASI
        </span>
      </div>
    </template>

    <template #cell-tanggal="{ value }">
      <span class="font-mono">{{ formatDate(value) }}</span>
    </template>
  </DataTableCard>
</template>
