<script setup>
import DataTableToolbar from './DataTableToolbar.vue'
import DataTablePagination from './DataTablePagination.vue'
import BaseDataTable from './BaseDataTable.vue'
import { Card } from '@/components/ui/card'

const emit = defineEmits(['update:page'])

defineProps({
  columns: Array,
  items: Array,
  filters: Array,
  actions: Array,

  from: Number,
  to: Number,
  total: Number,
  page: Number
})
</script>

<template>
  <Card>
    <DataTableToolbar
      :filters="filters"
      :actions="actions"
    />

    <BaseDataTable
      :columns="columns"
      :items="items"
    >
      <template
        v-for="column in columns"
        #[`cell-${column.key}`]="slotProps"
      >
        <slot
          :name="`cell-${column.key}`"
          v-bind="slotProps"
        />
      </template>
    </BaseDataTable>

    <DataTablePagination
    :from="from"
    :to="to"
    :total="total"
    :page="page"
    @update:page="emit('update:page', $event)"
    />
  </Card>
</template>