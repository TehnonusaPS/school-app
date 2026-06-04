<script setup>
import {
  Pencil,
  Ban,
  Eye,
} from 'lucide-vue-next'
import DataTableCard from '@/components/data-table/DataTableCard.vue'
import PageHeader from '@/components/page-header/PageHeader.vue'
import { Badge } from '@/components/ui/badge'
import { statusConfig } from '@/constants/statusConfig'
import { usePagination } from '@/composables/usePagination'
import { stats, columns, filters, actions, allItems } from './data/sekolah.js'
import StatCard from '@/components/stat-card/StatCard.vue'
import { useAuthStore } from '@/stores/authStore'
import { computed } from 'vue'

const auth = useAuthStore()
const isSuperAdmin = computed(() => auth.user?.role === 'superadmin')

const items = computed(() => {
  if (isSuperAdmin.value) {
    return allItems.value
  }

  return allItems.value.filter(
    item => item.yayasanId === auth.user?.yayasanId
  )
})
const { currentPage, total, from, to, paginatedItems } = usePagination(items)

</script>

<template>
  <div class="space-y-6 w-full mx-auto px-0">
    <PageHeader
      title="Data Sekolah"
      description="Kelola informasi dan profil sekolah secara lengkap dan terstruktur di sini"
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
      @update:page="currentPage = $event"
    >
      <template #cell-no="{ index }">
        {{ from + index }}
      </template>

      <template #cell-namaSekolah="{ item }">
        
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded bg-primary text-white flex items-center justify-center text-xs font-bold">
            {{ item.initial }}
          </div>

          <div>
            <div class="font-bold">{{ item.namaSekolah }}</div>
            <div class="text-xs text-muted-foreground">{{ item.code }}</div>
          </div>
        </div>
      </template>

      <template #cell-alamatSekolah="{ value }">
        <div class="max-w-sm whitespace-normal break-words">{{ value }}</div>
      </template>

      <template #cell-status="{ value }">
        <Badge :variant="statusConfig[value]" showDot> {{ value }}</Badge>
      </template>

      <template #cell-actions>
        <div class="flex items-center gap-3 text-muted-foreground">
          <button class="hover:text-foreground">
            <Eye class="w-4 h-4" />
          </button>

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
