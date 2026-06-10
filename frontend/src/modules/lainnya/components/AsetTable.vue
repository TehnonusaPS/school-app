<script setup>
import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { Plus, FileSpreadsheet, FileText } from 'lucide-vue-next'

import DataTableCard from '@/components/data-table/DataTableCard.vue'
import { Badge } from '@/components/ui/badge'

const props = defineProps({
  items: {
    type: Array,
    required: true
  },
  readonly: {
    type: Boolean,
    default: false
  },
  isYayasan: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['delete', 'view', 'export-excel', 'export-pdf'])
const router = useRouter()

// DataTable state
const filterValues = ref({})
const page = ref(1)
const perPage = ref(5)

// Reset page to 1 whenever filters change
watch(filterValues, () => {
  page.value = 1
}, { deep: true })

const schoolList = computed(() => {
  const schools = [...new Set(props.items.map(i => i.school_name).filter(Boolean))]
  return schools.sort()
})

const filters = computed(() => {
  const list = []

  if (props.isYayasan) {
    list.push({
      key: 'school',
      type: 'select',
      placeholder: 'Semua Sekolah',
      options: [
        ...schoolList.value.map(s => ({ label: s, value: s }))
      ]
    })
  }

  list.push({
    key: 'category',
    type: 'select',
    placeholder: 'Semua Kategori',
    options: [
      { label: 'Elektronik', value: 'elektronik' },
      { label: 'Furniture', value: 'furniture' },
      { label: 'Alat Pembelajaran', value: 'alat_pembelajaran' },
      { label: 'Alat Kebersihan', value: 'alat_kebersihan' },
      { label: 'Peralatan Kantor', value: 'peralatan_kantor' },
      { label: 'Peralatan Olahraga', value: 'peralatan_olahraga' },
      { label: 'Peralatan Laboratorium', value: 'peralatan_laboratorium' }
    ]
  })

  list.push({
    key: 'status',
    type: 'select',
    placeholder: 'Status Kondisi',
    options: [
      { label: 'Baik', value: 'baik' },
      { label: 'Rusak Ringan', value: 'rusak ringan' },
      { label: 'Rusak Berat', value: 'rusak berat' }
    ]
  })

  list.push({
    key: 'search',
    type: 'search',
    placeholder: 'Cari Aset...'
  })

  return list
})

const actions = computed(() => {
  const list = []
  
  if (props.isYayasan || props.readonly) {
    list.push({
      label: 'Export Excel',
      icon: FileSpreadsheet,
      variant: 'outline',
      class: 'border-emerald-500/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/10',
      click: () => emit('export-excel')
    })
    list.push({
      label: 'Export PDF',
      icon: FileText,
      variant: 'outline',
      class: 'border-rose-500/30 text-rose-600 dark:text-rose-400 hover:bg-rose-500/10',
      click: () => emit('export-pdf')
    })
  }

  if (!props.readonly && !props.isYayasan) {
    list.push({
      label: 'Tambah Aset Baru',
      icon: Plus,
      variant: 'default',
      click: () => router.push('/lainnya/aset/tambah')
    })
  }

  return list
})

const columns = computed(() => {
  const cols = []
  if (props.isYayasan) {
    cols.push({ key: 'school_name', label: 'CABANG SEKOLAH' })
  }
  cols.push(
    { key: 'code', label: 'KODE ASET', class: 'font-bold text-foreground' },
    { key: 'name', label: 'NAMA ASET' },
    { key: 'category', label: 'KATEGORI' },
    { key: 'location', label: 'LOKASI' },
    { key: 'condition', label: 'STATUS' },
    { key: 'actions', label: 'AKSI' }
  )
  return cols
})

const filteredItems = computed(() => {
  return props.items.filter(aset => {
    const fSearch = filterValues.value.search?.toLowerCase() || ''
    const fCategory = filterValues.value.category || 'all'
    const fStatus = filterValues.value.status || 'all'
    const fSchool = filterValues.value.school || 'all'

    const matchesSearch = !fSearch || 
      aset.name?.toLowerCase().includes(fSearch) ||
      aset.code?.toLowerCase().includes(fSearch)
      
    const matchesCategory = fCategory === 'all' || aset.category?.toLowerCase() === fCategory.toLowerCase()
    const matchesStatus = fStatus === 'all' || aset.condition?.toLowerCase() === fStatus.toLowerCase()
    
    let matchesSchool = true
    if (props.isYayasan && fSchool !== 'all') {
      matchesSchool = aset.school_name === fSchool
    }
    
    return matchesSearch && matchesCategory && matchesStatus && matchesSchool
  })
})

const paginatedItems = computed(() => {
  const start = (page.value - 1) * perPage.value
  return filteredItems.value.slice(start, start + perPage.value)
})

const statusBadgeClass = (status) => {
  if (!status) return 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20 shadow-xs'
  const lowerStatus = status.toLowerCase()
  if (lowerStatus === 'baik') {
    return 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 shadow-xs'
  } else if (lowerStatus === 'rusak ringan') {
    return 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 shadow-xs'
  } else if (lowerStatus === 'rusak berat') {
    return 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20 shadow-xs'
  } else if (lowerStatus === 'perbaikan') {
    return 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 shadow-xs'
  }
  return 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20 shadow-xs'
}

// Handlers for Row Actions
const handleEdit = (item) => {
  router.push(`/lainnya/aset/edit/${item.id}`)
}

const handleDelete = (id) => {
  emit('delete', id)
}

const handleView = (id) => {
  emit('view', id)
}
</script>

<template>
  <DataTableCard
    :columns="columns"
    :items="paginatedItems"
    :filters="filters"
    :actions="actions"
    illustration="ruler"
    v-model:filterValues="filterValues"
    :page="page"
    :per-page="perPage"
    :total="filteredItems.length"
    :from="(page - 1) * perPage + 1"
    :to="Math.min(page * perPage, filteredItems.length)"
    @update:page="page = $event"
    @update:perPage="perPage = $event"
    :on-edit="!readonly ? handleEdit : undefined"
    :on-delete="!readonly ? handleDelete : undefined"
    :on-view="readonly ? handleView : undefined"
  >
    <!-- Custom Render untuk Kolom Lokasi -->
    <template #cell-location="{ item }">
      <span class="text-muted-foreground">{{ item.room || item.building || '-' }}</span>
    </template>

    <!-- Custom Render untuk Badge Status -->
    <template #cell-condition="{ item }">
      <Badge :class="statusBadgeClass(item.condition)" class="rounded-full px-2.5 py-0.5 font-bold uppercase tracking-wider text-[10px]">
        {{ item.condition }}
      </Badge>
    </template>
  </DataTableCard>
</template>
