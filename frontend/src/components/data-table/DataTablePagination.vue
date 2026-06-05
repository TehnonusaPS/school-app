<script setup>
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationItem,
  PaginationNext,
  PaginationPrevious
} from '@/components/ui/pagination'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'

const props = defineProps({
  total: Number,
  from: Number,
  to: Number,
  page: Number,
  perPage: {
    type: Number,
    default: 10
  },
  perPageOptions: {
    type: Array,
    default: () => [5, 10, 15, 20, 30, 50]
  }
})

const emit = defineEmits(['update:page', 'update:perPage'])
</script>

<template>
  <div
    class="p-4 border-t flex flex-col lg:flex-row justify-between items-center gap-4 text-sm text-muted-foreground w-full"
  >
    <div class="flex items-center justify-between w-full lg:w-auto gap-4">
      <span class="whitespace-nowrap">{{ from }}-{{ to }} dari {{ total }}</span>

      <div class="flex items-center gap-1.5">
        <span class="text-xs whitespace-nowrap">Baris:</span>
        <Select
          :model-value="String(perPage)"
          @update:model-value="emit('update:perPage', Number($event))"
        >
          <SelectTrigger class="h-7 w-[70px] text-xs">
            <SelectValue />
          </SelectTrigger>
          <SelectContent>
            <SelectItem
              v-for="opt in perPageOptions"
              :key="opt"
              :value="String(opt)"
              class="text-xs"
            >
              {{ opt }}
            </SelectItem>
          </SelectContent>
        </Select>
      </div>
    </div>

    <!-- Kanan: pagination -->
    <Pagination
      v-slot="{ page: rootPage }"
      :total="total"
      :page="page"
      :items-per-page="perPage"
      :sibling-count="0"
      show-edges
      class="w-full lg:w-auto flex justify-center lg:justify-end mx-0"
      @update:page="emit('update:page', $event)"
    >
      <PaginationContent v-slot="{ items }">
        <PaginationPrevious />

        <template v-for="(item, index) in items" :key="index">
          <PaginationItem
            v-if="item.type === 'page'"
            :value="item.value"
            :is-active="item.value === rootPage"
            :class="item.value === rootPage ? 'bg-primary! text-primary-foreground! hover:bg-primary/90!' : ''"
          >
            {{ item.value }}
          </PaginationItem>
          <PaginationEllipsis
            v-else
            :index="index"
          />
        </template>

        <PaginationNext />
      </PaginationContent>
    </Pagination>
  </div>
</template>