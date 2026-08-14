<script setup>
import { Search } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue
} from '@/components/ui/select'
import { Input } from '@/components/ui/input'
import DatePicker from '@/components/date-picker/DatePicker.vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const props = defineProps({
  filters: {
    type: Array,
    default: () => []
  },
  actions: {
    type: Array,
    default: () => []
  },
  filterValues: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['update:filterValues'])

// Update satu key filter tanpa mengganggu key lainnya
const updateFilter = (key, value) => {
  emit('update:filterValues', { ...props.filterValues, [key]: value })
}

const handleAction = action => {
  if (action.click) {
    action.click()
  } else if (action.to) {
    router.push(action.to)
  }
}
</script>

<template>
  <div class="p-4 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border-b">
    <div class="flex flex-col sm:flex-row sm:flex-wrap items-start sm:items-center gap-3 w-full md:w-auto flex-1">
      <!-- Search Filter -->
      <template
        v-for="filter in filters"
        :key="filter.key"
      >
        <div
          v-if="filter.type === 'search'"
          class="relative w-full sm:w-[288px] shrink-0"
        >
          <Search
            class="absolute left-2.5 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground pointer-events-none"
          />
          <Input
            :model-value="filterValues[filter.key]"
            @update:model-value="updateFilter(filter.key, $event)"
            type="text"
            :placeholder="filter.placeholder"
            class="pl-9 h-8 w-full bg-muted/50 focus-visible:bg-background transition-colors"
          />
        </div>
      </template>

      <!-- Select & Date Filters -->
      <div class="flex flex-col sm:flex-row sm:flex-wrap items-start sm:items-center gap-3 w-full sm:w-auto">
        <template
          v-for="filter in filters"
          :key="filter.key"
        >
          <!-- Select Filter -->
          <div
            v-if="filter.type === 'select'"
            class="flex flex-col sm:flex-row sm:items-center gap-1.5 sm:gap-2 w-full sm:w-auto"
          >
            <span class="text-xs font-semibold text-muted-foreground whitespace-nowrap">
              {{ filter.label }}
            </span>

            <Select
              :model-value="filterValues[filter.key]"
              @update:model-value="updateFilter(filter.key, $event)"
            >
              <SelectTrigger class="h-8 w-full sm:min-w-[120px] sm:max-w-[200px] bg-background text-xs">
                <SelectValue :placeholder="filter.placeholder" />
              </SelectTrigger>

              <SelectContent>
                <SelectItem value="all">Semua</SelectItem>
                <SelectItem
                  v-for="option in filter.options"
                  :key="option.value"
                  :value="option.value"
                >
                  {{ option.label }}
                </SelectItem>
              </SelectContent>
            </Select>
          </div>

          <!-- Date Filter -->
          <div
            v-else-if="filter.type === 'date'"
            class="flex flex-col sm:flex-row sm:items-center gap-1.5 sm:gap-2 w-full sm:w-auto"
          >
            <span
              v-if="filter.label"
              class="text-xs font-semibold text-muted-foreground whitespace-nowrap"
            >
              {{ filter.label }}
            </span>
            <DatePicker
              :model-value="filterValues[filter.key]"
              @update:model-value="updateFilter(filter.key, $event)"
              placeholder="Pilih tanggal"
            />
          </div>
        </template>
      </div>
    </div>

    <!-- Actions -->
    <div
      v-if="actions && actions.length"
      class="flex flex-wrap items-center gap-2 shrink-0 self-end sm:self-auto w-full sm:w-auto justify-end"
    >
      <Button
        v-for="(action, index) in actions"
        :key="action.label || index"
        :variant="action.variant || 'default'"
        size="sm"
        class="h-8 gap-1.5"
        :disabled="action.disabled || action.loading"
        @click="handleAction(action)"
      >
        <span
          v-if="action.loading"
          class="size-3.5 animate-spin border-2 border-current border-t-transparent rounded-full shrink-0"
        />
        <component
          :is="action.icon"
          v-else-if="action.icon"
          class="size-3.5 shrink-0"
        />
        {{ action.label }}
      </Button>
    </div>
  </div>
</template>
