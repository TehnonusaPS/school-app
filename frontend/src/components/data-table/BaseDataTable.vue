<script setup>
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { tableRowFade } from '@/config/motion'

defineProps({
  columns: {
    type: Array,
    default: () => []
  },

  items: {
    type: Array,
    default: () => []
  }
})
</script>

<template>
  <Table>
    <TableHeader class="bg-muted/50">
      <TableRow>
        <TableHead
          v-for="column in columns"
          :key="column.key"
          :class="[
            'font-semibold text-xs uppercase text-muted-foreground',
            column.headerClass
          ]"
        >
          {{ column.label }}
        </TableHead>
      </TableRow>
    </TableHeader>

    <TableBody>
      <TableRow
        v-for="(item, rowIndex) in items"
        :key="rowIndex"
        v-motion
        :initial="tableRowFade.initial"
        :visible-once="{ ...tableRowFade.visible, transition: { ...tableRowFade.visible.transition, delay: rowIndex * 50 } }"
      >
        <TableCell
          v-for="column in columns"
          :key="column.key"
          :class="column.cellClass"
        >
          <slot
            :name="`cell-${column.key}`"
            :item="item"
            :value="item[column.key]"
          >
            {{ item[column.key] }}
          </slot>
        </TableCell>
      </TableRow>
    </TableBody>
  </Table>
</template>