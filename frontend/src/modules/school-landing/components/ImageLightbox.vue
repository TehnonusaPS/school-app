<script setup>
import { ref, watch } from 'vue'
import { X, ChevronLeft, ChevronRight } from 'lucide-vue-next'

const props = defineProps({
  images: { type: Array, required: true },
  modelValue: { type: Boolean, default: false },
  startIndex: { type: Number, default: 0 }
})
const emit = defineEmits(['update:modelValue'])

const currentIndex = ref(props.startIndex)

watch(
  () => props.startIndex,
  val => {
    currentIndex.value = val
  }
)
watch(
  () => props.modelValue,
  val => {
    if (val) {
      document.body.style.overflow = 'hidden'
    } else {
      document.body.style.overflow = ''
    }
  }
)

function close() {
  emit('update:modelValue', false)
}

function prev() {
  currentIndex.value = (currentIndex.value - 1 + props.images.length) % props.images.length
}

function next() {
  currentIndex.value = (currentIndex.value + 1) % props.images.length
}

function onKeydown(e) {
  if (e.key === 'Escape') close()
  if (e.key === 'ArrowLeft') prev()
  if (e.key === 'ArrowRight') next()
}
</script>

<template>
  <Teleport to="body">
    <Transition name="lightbox">
      <div
        v-if="modelValue"
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/90 backdrop-blur-sm"
        @click.self="close"
        @keydown="onKeydown"
        tabindex="0"
      >
        <!-- Close Button -->
        <button
          @click="close"
          class="absolute top-6 right-6 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all z-10"
        >
          <X class="w-6 h-6" />
        </button>

        <!-- Nav Prev -->
        <button
          v-if="images.length > 1"
          @click="prev"
          class="absolute left-6 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all z-10"
        >
          <ChevronLeft class="w-6 h-6" />
        </button>

        <!-- Image -->
        <div class="max-w-5xl max-h-[85vh] px-16">
          <img
            :src="images[currentIndex]?.url || images[currentIndex]"
            :alt="images[currentIndex]?.title || `Image ${currentIndex + 1}`"
            class="max-w-full max-h-[85vh] object-contain rounded-lg shadow-2xl"
          />
          <!-- Caption -->
          <p
            v-if="images[currentIndex]?.title"
            class="text-center text-white/80 text-sm mt-4"
          >
            {{ images[currentIndex].title }}
          </p>
        </div>

        <!-- Nav Next -->
        <button
          v-if="images.length > 1"
          @click="next"
          class="absolute right-6 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all z-10"
        >
          <ChevronRight class="w-6 h-6" />
        </button>

        <!-- Dots -->
        <div
          v-if="images.length > 1"
          class="absolute bottom-8 flex gap-2"
        >
          <button
            v-for="(_, i) in images"
            :key="i"
            @click="currentIndex = i"
            :class="[
              'w-2.5 h-2.5 rounded-full transition-all',
              i === currentIndex ? 'bg-white scale-125' : 'bg-white/40 hover:bg-white/60'
            ]"
          />
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.lightbox-enter-active,
.lightbox-leave-active {
  transition: opacity 0.3s ease;
}
.lightbox-enter-from,
.lightbox-leave-to {
  opacity: 0;
}
</style>
