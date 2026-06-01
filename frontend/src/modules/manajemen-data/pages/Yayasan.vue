<script setup>
import {
  Building2,
  ShieldCheck,
  ShieldAlert,
  ShieldX,
  Plus,
  Download,
  Pencil,
  Ban,
  TrendingUp,
  PlusCircle,
  TrendingDown
} from 'lucide-vue-next'
import StatCard from '@/components/stat-card/StatCard.vue'
import DataTableCard from '@/components/data-table/DataTableCard.vue'
import PageHeader from '@/components/page-header/PageHeader.vue'
import { Badge } from '@/components/ui/badge'
import { statusConfig } from '@/constants/statusConfig'
import { computed } from 'vue'
import { usePagination } from '@/composables/usePagination'

// Data for StatCard
const yayasanData = [
  {
    label: 'TOTAL YAYASAN',
    value: '123',
    trend: '+8.4% bln ini',
    trendDirection: 'up',
    icon: Building2,
    variant: 'emerald'
  },
  {
    label: 'YAYASAN AKTIF',
    value: '112',
    trend: '+12 Baru',
    trendDirection: 'up',
    icon: ShieldCheck,
    variant: 'emerald'
  },
  {
    label: 'YAYASAN SEDANG TRIAL',
    value: '8',
    trend: '-2 Yayasan',
    trendDirection: 'down',
    icon: ShieldAlert,
    variant: 'amber'
  },
  {
    label: 'YAYASAN TIDAK AKTIF',
    value: '3',
    trend: '-1 Yayasan',
    trendDirection: 'up',
    icon: ShieldX,
    variant: 'primary'
  }
]

const columns = [
  { key: 'yayasan', label: 'Nama Yayasan' },
  { key: 'jmlSekolah', label: 'Jumlah Sekolah' },
  { key: 'alamat', label: 'Alamat Yayasan' },
  { key: 'jmlSiswa', label: 'Jumlah Siswa' },
  { key: 'status', label: 'Status' },
  { key: 'actions', label: 'Aksi' }
]

const filters = [
  {
    type: 'search',
    key: 'search',
    placeholder: 'Cari nama yayasan disini...'
  },
  {
    type: 'select',
    key: 'status',
    label: 'Filter Status:',
    placeholder: 'Semua Status',
    options: [
      {
        label: 'Aktif',
        value: 'active'
      },
      {
        label: 'Non Aktif',
        value: 'inactive'
      },
      {
        label: 'Trial',
        value: 'trial'
      }
    ]
  }
]

const actions = [
  {
    label: 'Tambah Yayasan Baru',
    icon: Plus,
    to: '/manajemen-data/yayasan/tambah'
  },
  {
    label: 'Export',
    icon: Download,
    variant: 'outline'
  }
]

const items = computed(() => [
  {
    id: 1,
    initial: 'NP',
    yayasan: 'Yayasan Nusantara Pintar',
    jmlSekolah: '2',
    alamat: 'Jl. Lorem Ipsum Dolor Sit No. 123, Kel. Lorem, Kec. Lorem Ipsum, Lorem Ipsum 123456',
    jmlSiswa: '612',
    status: 'Aktif',
  },
  {
    id: 2,
    initial: 'HB',
    yayasan: 'Yayasan Harapan Bangsa',
    jmlSekolah: '1',
    alamat: 'Jl. Lorem Ipsum Dolor Sit No. 123, Kel. Lorem, Kec. Lorem Ipsum, Lorem Ipsum 123456',
    jmlSiswa: '248',
    status: 'Non Aktif',
  },
  {
    id: 3,
    initial: 'SI',
    yayasan: 'Yayasan Sinar Ilmu',
    jmlSekolah: '3',
    alamat: 'Jl. Lorem Ipsum Dolor Sit No. 123, Kel. Lorem, Kec. Lorem Ipsum, Lorem Ipsum 123456',
    jmlSiswa: '937',
    status: 'Trial',
  },
])

const { currentPage, total, from, to, paginatedItems } = usePagination(items)

</script>

<template>
  <div class="space-y-6 w-full mx-auto px-0">
    <PageHeader
      title="Data Yayasan"
      description="Kelola informasi dan profil yayasan secara lengkap dan terstruktur di sini"
    />
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <StatCard
        v-for="(stat, index) in yayasanData"
        :key="index"
        :label="stat.label"
        :value="stat.value"
        :trend="stat.trend"
        :trendDirection="stat.trendDirection"
        :icon="stat.icon"
        :variant="stat.variant"
      />
    </div>

    <DataTableCard
      :columns="columns"
      :items="paginatedItems"
      :filters="filters"
      :actions="actions"

      :from="from"
      :to="to"
      :total="total"
      :page="currentPage"
    >
      <template #cell-yayasan="{ item }">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded bg-primary text-white flex items-center justify-center text-xs font-bold">
            {{ item.initial }}
          </div>

          <div>
            <div class="font-bold">
              {{ item.yayasan }}
            </div>

            <!-- <div class="text-xs text-muted-foreground">
              ID: {{ item.code }}
            </div> -->
          </div>
        </div>
      </template>

      <template #cell-status="{ value }">
        <Badge
          :variant="statusConfig[value]"
          showDot
        >
          {{ value }}
        </Badge>
      </template>

      <template #cell-actions>
        <div class="flex items-center gap-3 text-muted-foreground">
          <button class="hover:text-foreground">
            <Pencil class="w-4 h-4" />
          </button>

          <button class="hover:text-foreground">
            <Ban class="w-4 h-4" />
          </button>
        </div>
      </template>
    </DataTableCard>

  </div>
</template>

