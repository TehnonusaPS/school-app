<script setup>
import { computed, ref, watch } from 'vue'
import DataTableCard from '@/components/data-table/DataTableCard.vue'
import { usePagination } from '@/composables/usePagination'

const props = defineProps({
  items: {
    type: Array,
    required: true
  }
})

const emit = defineEmits(['view', 'edit', 'delete'])

// --- Filtering State ---
const filterValues = ref({
  search: '',
  kategori: 'all',
  tanggal: ''
})

const filters = computed(() => [
  { type: 'search', key: 'search', placeholder: 'Cari Judul Berita Kegiatan...' },
  {
    type: 'select',
    key: 'kategori',
    label: 'Kategori:',
    placeholder: 'Semua Kategori',
    options: [
      { label: 'Akademik', value: 'AKADEMIK' },
      { label: 'Keuangan', value: 'KEUANGAN' },
      { label: 'Umum', value: 'UMUM' }
    ]
  },
  { type: 'date', key: 'tanggal', label: 'Tanggal:' }
])

const columns = computed(() => [
  { key: 'judul', label: 'Judul Berita' },
  {
    key: 'kategori',
    label: 'Kategori',
    badge: true,
    badgeVariant: {
      AKADEMIK: 'blue',
      KEUANGAN: 'green',
      UMUM: 'amber'
    }
  },
  { key: 'tanggal', label: 'Tanggal', type: 'date' },
  { key: 'actions', label: 'Aksi' }
])

// --- Computed Filters ---
const filteredItems = computed(() => {
  return props.items.filter(item => {
    const searchVal = filterValues.value.search?.trim().toLowerCase() || ''
    const matchesSearch = !searchVal || 
      item.judul.toLowerCase().includes(searchVal) ||
      item.isi.toLowerCase().includes(searchVal)

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
</script>

<template>
  <DataTableCard
    :columns="columns"
    :items="paginatedItems"
    :filters="filters"
    v-model:filterValues="filterValues"
    illustration="open_book"
    v-model:perPage="perPage"
    :from="from"
    :to="to"
    :total="total"
    :page="currentPage"
    @update:page="currentPage = $event"
    @view="(id, item) => $emit('view', item)"
    @edit="(item) => $emit('edit', item)"
    @delete="(id, item) => $emit('delete', item)"
    delete-label="judul"
  >
    <template #cell-judul="{ item }">
      <div 
        class="font-bold text-foreground leading-snug text-sm sm:text-base hover:text-primary transition-colors cursor-pointer text-left" 
        @click="$emit('view', item)"
      >
        {{ item.judul }}
      </div>
      <div class="text-xs text-muted-foreground mt-1 line-clamp-2 max-w-[550px] leading-relaxed text-left">
        {{ item.isi }}
      </div>
    </template>

    <template #cell-tanggal="{ value }">
      <span class="font-mono">{{ formatDate(value) }}</span>
    </template>
  </DataTableCard>
</template>
