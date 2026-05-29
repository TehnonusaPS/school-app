<script setup>
import { Button } from '@/components/ui/button'

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

const totalPages = Math.ceil(props.total / props.perPage)

const nextPage = () => {
  if (props.page < totalPages) {
    emit('update:page', props.page + 1)
  }
}

const prevPage = () => {
  if (props.page > 1) {
    emit('update:page', props.page - 1)
  }
}
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

      <Button variant="default" class="w-8 h-8 p-0"> {{ page }}</Button>

      <Button variant="outline" class="w-8 h-8 p-0" :disabled="page >= totalPages" @click="nextPage">
        &gt;
      </Button>
    </div>
  </div>
</template>