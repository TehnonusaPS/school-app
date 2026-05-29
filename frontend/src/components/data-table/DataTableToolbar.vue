<script setup>
import { Search } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { InputGroup, InputGroupButton, InputGroupInput } from '@/components/ui/input-group'
import { useRouter } from 'vue-router'

const router = useRouter()

const handleAction = (action) => {
  if (action.to) {
    router.push(action.to)
  }
}

defineProps({
  filters: {
    type: Array,
    default: () => []
  },

  actions: {
    type: Array,
    default: () => []
  }
})
</script>

<template>
  <div class="p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center border-b gap-4">
    <div class="flex flex-wrap items-center gap-4">
      <template
        v-for="filter in filters"
        :key="filter.key"
        class="flex items-center gap-2"
      >
        <!-- SEARCH -->
         <div v-if="filter.type === 'search'" class="flex items-center gap-2">
            <InputGroup class="w-[300px]">
            <InputGroupInput :placeholder="filter.placeholder" class="h-8"/>
            <InputGroupButton variant="secondary">
                <Search class="w-4 h-4" />
            </InputGroupButton>
            </InputGroup>
        </div>

        <!-- SELECT -->
        <div v-else-if="filter.type === 'select'" class="flex items-center gap-2">
            <span class="text-sm font-medium text-muted-foreground">
            {{ filter.label }}
            </span>

            <Select>
            <SelectTrigger class="w-[150px] h-8">
                <SelectValue :placeholder="filter.placeholder" />
            </SelectTrigger>

            <SelectContent>
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
    </template>
    </div>

    <div class="flex items-center gap-2">
      <Button
        v-for="action in actions"
        :key="action.label"
        :variant="action.variant || 'default'"
        class="h-8"
        @click="handleAction(action)"
      >
        <component :is="action.icon" class="w-4 h-4 mr-1" />
        {{ action.label }}
      </Button>
    </div>
  </div>
</template>