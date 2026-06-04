<script setup>
import { Button } from '@/components/ui/button'
import { computed } from 'vue'

const props = defineProps({
  total: Number,
  from: Number,
  to: Number,
  page: Number,
  perPage: {
    type: Number,
    default: 10
  }
})

const emit = defineEmits(['update:page'])

const totalPages = computed(() =>
  Math.ceil(props.total / props.perPage)
)

const nextPage = () => {
  if (props.page < totalPages.value) {
    emit('update:page', props.page + 1)
  }
}

const prevPage = () => {
  if (props.page > 1) {
    emit('update:page', props.page - 1)
  }
}

const visiblePages = computed(() => {
  const pages = []

  let start = Math.max(1, props.page - 1)
  let end = Math.min(totalPages.value, start + 2)

  if (end - start < 2) {
    start = Math.max(1, end - 2)
  }

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }

  return pages
})

const goToPage = (page) => {emit('update:page', page)}

</script>

<template>
  <div class="p-4 border-t flex justify-between items-center text-sm text-muted-foreground">
    <div>
      Menampilkan {{ from }}-{{ to }} dari {{ total }} Data
    </div>

    <div class="flex items-center gap-1">
      <Button variant="outline" class="w-8 h-8 p-0" :disabled="page <= 1" @click="prevPage">
        &lt;
      </Button>

      <Button
        v-for="pageNumber in visiblePages"
        :key="pageNumber"
        :variant="pageNumber === page ? 'default' : 'outline'"
        class="w-8 h-8 p-0"
        @click="goToPage(pageNumber)"
      >
        {{ pageNumber }}
      </Button>

      <Button variant="outline" class="w-8 h-8 p-0" :disabled="page >= totalPages.value" @click="nextPage">
        &gt;
      </Button>
    </div>
  </div>
</template>