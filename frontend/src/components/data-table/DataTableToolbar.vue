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
  <div
    class="p-4 flex flex-col lg:flex-row justify-between items-stretch lg:items-center border-b gap-4"
  >
    <div
      class="flex flex-col lg:flex-row lg:items-center gap-3 w-full lg:w-auto order-2 lg:order-1"
    >
      <!-- Search Filter -->
      <template
        v-for="filter in filters"
        :key="filter.key"
      >
        <div
          v-if="filter.type === 'search'"
          class="relative w-full md:w-[288px]"
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
      <div class="flex flex-row flex-wrap items-center gap-3 w-full lg:w-auto">
        <template
          v-for="filter in filters"
          :key="filter.key"
        >
          <!-- Select Filter -->
          <div
            v-if="filter.type === 'select'"
            class="flex items-center gap-1.5 w-auto"
          >
            <span class="text-sm font-medium text-muted-foreground whitespace-nowrap">
              {{ filter.label }}
            </span>

            <Select
              :model-value="filterValues[filter.key]"
              @update:model-value="updateFilter(filter.key, $event)"
            >
              <SelectTrigger class="min-w-[85px] max-w-[160px] h-8 bg-background">
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
            class="flex items-center gap-1.5 w-auto"
          >
            <span
              v-if="filter.label"
              class="text-sm font-medium text-muted-foreground whitespace-nowrap"
            >
              {{ filter.label }}
            </span>
            <Input
              type="date"
              :model-value="filterValues[filter.key]"
              @update:model-value="updateFilter(filter.key, $event)"
              @click="$event.target.showPicker()"
              class="h-8 w-[140px] px-2 bg-background cursor-pointer text-xs"
            />
          </div>
        </template>
      </div>
    </div>

    <div
      v-if="actions && actions.length"
      class="flex flex-row-reverse flex-wrap gap-2 items-center justify-start w-full lg:w-auto shrink-0 order-1 lg:order-2"
    >
      <Button
        v-for="(action, index) in actions"
        :key="action.label || index"
        :variant="action.variant || 'default'"
        size="sm"
        class="h-8 justify-center shrink-0 w-auto [direction:ltr]"
        :disabled="action.disabled || action.loading"
        @click="handleAction(action)"
      >
        <span
          v-if="action.loading"
          class="mr-2 h-4 w-4 animate-spin border-2 border-current border-t-transparent rounded-full"
        />
        <component
          :is="action.icon"
          v-else-if="action.icon"
          class="w-4 h-4 mr-1"
        />
        {{ action.label }}
      </Button>
    </div>
  </div>
</template>
