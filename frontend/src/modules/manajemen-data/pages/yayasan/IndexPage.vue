<script setup>
import {
  Pencil,
  Ban,
} from 'lucide-vue-next'
// import StatsCard from '@/components/stats-card/StatsCard.vue'
import DataTableCard from '@/components/data-table/DataTableCard.vue'
import PageHeader from '@/components/page-header/PageHeader.vue'
import { Badge } from '@/components/ui/badge'
import { statusConfig } from '@/constants/statusConfig'
import { usePagination } from '@/composables/usePagination'
import { stats, columns, filters, actions, items } from './data/yayasan.js'
import StatCard from '@/components/stat-card/StatCard.vue'

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
      v-for="(stat, index) in stats"
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
            <div class="font-bold">{{ item.yayasan }}</div>
          </div>
        </div>
      </template>

      <template #cell-status="{ value }">
        <Badge :variant="statusConfig[value]" showDot>{{ value }}</Badge>
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
