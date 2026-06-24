<script setup lang="ts">
import { ref, computed, nextTick, watch } from 'vue'
import { ChevronDown } from 'lucide-vue-next'

const props = defineProps<{
  modelValue: string
  options: Array<{ value: string; label: string }>
}>()

const emit = defineEmits<{
  'update:modelValue': [value: string]
}>()

const open = ref(false)
const listRef = ref<HTMLElement | null>(null)

const selectedLabel = computed(() =>
  props.options.find(o => o.value === props.modelValue)?.label ?? props.modelValue
)

function select(value: string) {
  emit('update:modelValue', value)
  open.value = false
}

function scrollToSelected() {
  const list = listRef.value
  if (!list) return
  const sel = list.querySelector('[data-selected="true"]') as HTMLElement | null
  if (sel) {
    list.scrollTop = sel.offsetTop - list.clientHeight / 2 + sel.offsetHeight / 2
  }
}

watch(open, (val) => {
  if (val) nextTick(() => scrollToSelected())
})
</script>

<template>
  <div class="relative">
    <button
      type="button"
      class="flex items-center gap-1 px-2.5 h-8 rounded-md text-sm font-semibold text-foreground hover:bg-muted/50 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
      @click.stop="open = !open"
    >
      {{ selectedLabel }}
      <ChevronDown
        class="size-3.5 text-muted-foreground transition-transform duration-150"
        :class="open ? 'rotate-180' : ''"
      />
    </button>

    <!-- Inline dropdown — no portal, stays inside calendar DOM -->
    <Transition
      enter-active-class="transition-all duration-150 ease-out"
      enter-from-class="opacity-0 scale-95 -translate-y-1"
      enter-to-class="opacity-100 scale-100 translate-y-0"
      leave-active-class="transition-all duration-100 ease-in"
      leave-from-class="opacity-100 scale-100 translate-y-0"
      leave-to-class="opacity-0 scale-95 -translate-y-1"
    >
      <div
        v-if="open"
        ref="listRef"
        class="absolute z-[60] top-full mt-1 left-1/2 -translate-x-1/2 min-w-fit max-h-52 overflow-y-auto rounded-lg border border-border/80 bg-popover shadow-lg py-1"
        @click.stop
      >
        <button
          v-for="opt in options"
          :key="opt.value"
          type="button"
          :data-selected="opt.value === modelValue"
          class="w-full whitespace-nowrap px-4 py-1.5 text-sm text-center transition-colors hover:bg-muted/60 focus-visible:outline-none"
          :class="opt.value === modelValue
            ? 'font-semibold text-primary bg-primary/10 hover:bg-primary/15'
            : 'text-foreground'"
          @click="select(opt.value)"
        >
          {{ opt.label }}
        </button>
      </div>
    </Transition>

    <!-- Overlay to close when clicking outside -->
    <div v-if="open" class="fixed inset-0 z-[59]" @click="open = false" />
  </div>
</template>
